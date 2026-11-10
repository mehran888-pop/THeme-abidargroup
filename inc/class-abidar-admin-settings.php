<?php
/**
 * Abidar Group Separated Admin Theme Settings Panel (v2.0.0)
 *
 * پنل تنظیمات تفکیک‌شده قالب گروه آبیدر در پیشخوان وردپرس:
 * ۱. تنظیمات طرح و چیدمان (عرض، رنگ‌ها، تایپوگرافی، آپلود فونت اختصاصی، هدر و فوتر)
 * ۲. تنظیمات ناوبری موبایل (۴ چیدمان مختلف + تعریف دکمه‌ها و لینک‌ها)
 * ۳. تنظیمات دکمه شناور تماس چندبخشی و شبکه‌های اجتماعی (تعریف بی‌نهایت بخش مجزا)
 * ۴. تنظیمات قالب مقاله و پست‌ها
 * ۵. تنظیمات پیش‌بارگذاری سایت (Preloader)
 * ۶. تنظیمات امنیت سایت (تغییر آدرس ادمین، محدودیت تلاش ورود، ضد اسپم، لاگ امنیتی)
 * ۷. تنظیمات زبان سایت (فارسی، انگلیسی، چینی + افزودن زبان‌های جدید)
 * ۸. ساخت خودکار برگه‌های دمو با المنتور
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Admin_Settings
 */
class Abidar_Admin_Settings {

	/**
	 * راه‌اندازی منوهای پیشخوان و ذخیره تنظیمات
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_menus' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'admin_post_abidar_save_settings', array( __CLASS__, 'handle_form_save' ) );
	}

	/**
	 * ثبت منوی اصلی «تنظیمات قالب آبیدر» به همراه زیرمنوهای تفکیک‌شده در پیشخوان وردپرس
	 */
	public static function register_admin_menus() {
		add_menu_page(
			esc_html__( 'تنظیمات قالب گروه آبیدر (نسخه ۲.۰)', 'abidar-group' ),
			esc_html__( 'قالب گروه آبیدر', 'abidar-group' ),
			'manage_options',
			'abidar-settings',
			array( __CLASS__, 'render_settings_page' ),
			'dashicons-shield-alt',
			2
		);

		$submenus = array(
			'layout'           => esc_html__( '۱. طرح، رنگ و فونت', 'abidar-group' ),
			'mobile-nav'       => esc_html__( '۲. ناوبری موبایل', 'abidar-group' ),
			'floating-contact' => esc_html__( '۳. دکمه شناور تماس چندبخشی', 'abidar-group' ),
			'blog'             => esc_html__( '۴. قالب مقاله و پست‌ها', 'abidar-group' ),
			'preloader'        => esc_html__( '۵. پیش‌بارگذاری سایت', 'abidar-group' ),
			'security'         => esc_html__( '۶. امنیت پیشرفته سایت', 'abidar-group' ),
			'languages'        => esc_html__( '۷. مدیریت زبان‌ها (۳+ زبان)', 'abidar-group' ),
			'demo'             => esc_html__( '۸. ساخت برگه‌های دمو المنتور', 'abidar-group' ),
			'diagnostics'      => esc_html__( '۹. عیب‌یابی ویرایشگر المنتور', 'abidar-group' ),
		);

		foreach ( $submenus as $tab_key => $label ) {
			add_submenu_page(
				'abidar-settings',
				$label,
				$label,
				'manage_options',
				'abidar-settings&tab=' . $tab_key,
				array( __CLASS__, 'render_settings_page' )
			);
		}
	}

	/**
	 * بارگذاری استایل‌ها، انتخابگر رنگ و کتابخانه رسانه وردپرس (برای آپلود فونت و لوگو)
	 *
	 * @param string $hook صفحه فعلی پیشخوان.
	 */
	public static function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'abidar-settings' ) === false ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style(
			'abidar-admin-css',
			ABIDAR_URI . '/assets/css/admin.css',
			array( 'wp-color-picker' ),
			ABIDAR_VERSION
		);

		wp_enqueue_script(
			'abidar-admin-js',
			ABIDAR_URI . '/assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			ABIDAR_VERSION,
			true
		);

		wp_localize_script(
			'abidar-admin-js',
			'AbidarAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'abidar_admin_nonce' ),
			)
		);
	}

	/**
	 * پردازش و پاک‌سازی (Sanitization) کامل فرم تنظیمات قالب
	 */
	public static function handle_form_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'abidar-group' ) );
		}

		check_admin_referer( 'abidar_save_settings_action', 'abidar_settings_nonce' );

		$options = abidar_get_all_options();
		$posted  = isset( $_POST['abidar'] ) && is_array( $_POST['abidar'] ) ? wp_unslash( $_POST['abidar'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// کدام تب ذخیره شده است؟ برای تشخیص «حذف همه ردیف‌ها» لازم است، چون در
		// آن حالت مرورگر هیچ فیلدی از آن لیست ارسال نمی‌کند.
		$submitted_panel = isset( $_POST['abidar_panel'] ) ? sanitize_key( wp_unslash( $_POST['abidar_panel'] ) ) : '';

		// ۱. پاک‌سازی فیلدهای عددی
		$int_fields = array(
			'container_width',
			'grid_gap',
			'section_padding',
			'border_radius',
			'base_font_size',
			'h1_font_size',
			'h2_font_size',
			'h3_font_size',
			'custom_header_id',
			'custom_footer_id',
			'mobile_breakpoint',
			'post_related_count',
			'blog_archive_columns',
			'preloader_duration',
			'security_max_login_attempts',
			'security_lockout_duration',
			'security_antispam_min_seconds',
		);
		foreach ( $int_fields as $field ) {
			if ( isset( $posted[ $field ] ) ) {
				$options[ $field ] = (int) $posted[ $field ];
			}
		}

		// ۲. پاک‌سازی فیلدهای رنگی
		$color_fields = array(
			'primary_color',
			'secondary_color',
			'accent_color',
			'surface_dark_color',
			'surface_light_color',
			'text_main_color',
			'preloader_bg_color',
			'preloader_accent_color',
		);
		foreach ( $color_fields as $field ) {
			if ( isset( $posted[ $field ] ) ) {
				$color = sanitize_hex_color( $posted[ $field ] );
				if ( $color ) {
					$options[ $field ] = $color;
				}
			}
		}

		// ۳. پاک‌سازی فیلدهای متنی
		$text_fields = array(
			'layout_mode',
			'font_family_fa',
			'font_family_en',
			'font_family_zh',
			'custom_font_name',
			'header_layout',
			'footer_layout',
			'mobile_nav_layout',
			'mobile_cta_text_fa',
			'mobile_cta_text_en',
			'mobile_cta_text_zh',
			'mobile_quick_phone',
			'floating_position',
			'floating_title_fa',
			'floating_title_en',
			'floating_title_zh',
			'floating_subtitle_fa',
			'floating_subtitle_en',
			'floating_subtitle_zh',
			'single_post_layout',
			'preloader_style',
			'preloader_text_fa',
			'preloader_text_en',
			'preloader_text_zh',
			'security_custom_login_slug',
			'default_language',
			'lang_switcher_style',
		);
		foreach ( $text_fields as $field ) {
			if ( isset( $posted[ $field ] ) ) {
				$options[ $field ] = sanitize_text_field( $posted[ $field ] );
			}
		}

		// لیست سفید IP: هر مقدار با کاما، فاصله یا خط جدید جدا می‌شود و اعتبارسنجی می‌گردد.
		if ( isset( $posted['security_ip_whitelist'] ) ) {
			$raw_ips   = preg_split( '/[\s,]+/', (string) $posted['security_ip_whitelist'] );
			$valid_ips = array();

			foreach ( (array) $raw_ips as $candidate ) {
				$candidate = trim( $candidate );
				if ( '' === $candidate ) {
					continue;
				}

				// IPv4/IPv6 ساده، یا محدوده CIDR معتبر.
				if ( filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
					$valid_ips[] = $candidate;
					continue;
				}

				if ( preg_match( '#^([0-9a-fA-F:.]+)/(\d{1,3})$#', $candidate, $m ) && filter_var( $m[1], FILTER_VALIDATE_IP ) ) {
					$valid_ips[] = $candidate;
				}
			}

			$options['security_ip_whitelist'] = implode( "\n", array_unique( $valid_ips ) );
		}

		// مقدار طرح زبان باید یکی از ۶ طرح مجاز باشد.
		$allowed_lang_styles = array( 'pills_flag', 'compact_iso', 'flags_only', 'flags_round', 'dropdown', 'dropdown_flags' );
		if ( isset( $options['lang_switcher_style'] ) && ! in_array( $options['lang_switcher_style'], $allowed_lang_styles, true ) ) {
			$options['lang_switcher_style'] = 'pills_flag';
		}

		// ۴. پاک‌سازی لینک‌ها و فایل‌های فونت اختصاصی
		$url_fields = array(
			'custom_font_woff2_url',
			'custom_font_woff_url',
			'custom_font_ttf_url',
			'mobile_cta_url',
		);
		foreach ( $url_fields as $field ) {
			if ( isset( $posted[ $field ] ) ) {
				$options[ $field ] = esc_url_raw( $posted[ $field ] );
			}
		}

		// ۵. پاک‌سازی سوئیچ‌های بولی (Checkboxes)
		$bool_fields = array(
			'sticky_header',
			'mobile_nav_enabled',
			'floating_contact_enabled',
			'floating_pulse',
			'post_reading_progress',
			'post_reading_time',
			'post_sticky_toc',
			'post_author_box',
			'post_share_buttons',
			'post_related_articles',
			'preloader_enabled',
			'security_custom_login_enabled',
			'security_limit_login_enabled',
			'security_antispam_honeypot',
			'security_antispam_timetrap',
			'security_math_captcha',
			'security_disable_xmlrpc',
			'security_block_user_enum',
			'security_hide_wp_version',
			'security_http_headers',
			'multilang_enabled',
		);
		foreach ( $bool_fields as $field ) {
			$options[ $field ] = ! empty( $posted[ $field ] );
		}

		// ۶. پاک‌سازی شبکه‌های اجتماعی دکمه شناور
		if ( isset( $posted['floating_socials'] ) && is_array( $posted['floating_socials'] ) ) {
			$clean_socials = array();
			foreach ( $posted['floating_socials'] as $s_key => $s_val ) {
				$clean_socials[ sanitize_key( $s_key ) ] = sanitize_text_field( $s_val );
			}
			$options['floating_socials'] = $clean_socials;
		}

		// ۷. پاک‌سازی بخش‌های چندگانه دکمه شناور تماس (Multi-Department Contact Hub)
		// اگر کل ردیف‌ها حذف شده باشند، فیلد اصلاً ارسال نمی‌شود؛ در آن حالت
		// باید لیست خالی ذخیره شود تا «حذف همه» واقعاً کار کند.
		if ( array_key_exists( 'floating_departments', $posted ) ) {
			$rows        = is_array( $posted['floating_departments'] ) ? $posted['floating_departments'] : array();
			$clean_depts = array();
			foreach ( $rows as $dept ) {
				if ( ! is_array( $dept ) ) {
					continue;
				}
				if ( empty( $dept['name_fa'] ) && empty( $dept['name_en'] ) ) {
					continue;
				}
				$clean_depts[] = array(
					// شناسه یکتا؛ wp_rand دو عدد کوچک را تکرار می‌کند و باعث تداخل می‌شد.
					'id'          => ! empty( $dept['id'] ) ? sanitize_key( $dept['id'] ) : 'dept_' . strtolower( wp_generate_password( 8, false, false ) ),
					'name_fa'     => sanitize_text_field( isset( $dept['name_fa'] ) ? $dept['name_fa'] : '' ),
					'name_en'     => sanitize_text_field( isset( $dept['name_en'] ) ? $dept['name_en'] : '' ),
					'name_zh'     => sanitize_text_field( isset( $dept['name_zh'] ) ? $dept['name_zh'] : '' ),
					'role_fa'     => sanitize_text_field( isset( $dept['role_fa'] ) ? $dept['role_fa'] : '' ),
					'role_en'     => sanitize_text_field( isset( $dept['role_en'] ) ? $dept['role_en'] : '' ),
					'role_zh'     => sanitize_text_field( isset( $dept['role_zh'] ) ? $dept['role_zh'] : '' ),
					'phone'       => sanitize_text_field( isset( $dept['phone'] ) ? $dept['phone'] : '' ),
					'ext'         => sanitize_text_field( isset( $dept['ext'] ) ? $dept['ext'] : '' ),
					'whatsapp'    => esc_url_raw( isset( $dept['whatsapp'] ) ? $dept['whatsapp'] : '' ),
					'email'       => sanitize_email( isset( $dept['email'] ) ? $dept['email'] : '' ),
					'hours_fa'    => sanitize_text_field( isset( $dept['hours_fa'] ) ? $dept['hours_fa'] : '' ),
					'hours_en'    => sanitize_text_field( isset( $dept['hours_en'] ) ? $dept['hours_en'] : '' ),
					'hours_zh'    => sanitize_text_field( isset( $dept['hours_zh'] ) ? $dept['hours_zh'] : '' ),
					'icon'        => sanitize_key( isset( $dept['icon'] ) ? $dept['icon'] : 'pickaxe' ),
					'badge_color' => sanitize_hex_color( isset( $dept['badge_color'] ) ? $dept['badge_color'] : '#D97706' ),
				);
			}
			$options['floating_departments'] = $clean_depts;
		} elseif ( 'floating-contact' === $submitted_panel ) {
			// مدیر همه بخش‌ها را حذف کرده است.
			$options['floating_departments'] = array();
		}

		// ۸. پاک‌سازی آیتم‌های ناوبری موبایل
		if ( array_key_exists( 'mobile_nav_items', $posted ) || 'mobile-nav' === $submitted_panel ) {
			$rows      = ( isset( $posted['mobile_nav_items'] ) && is_array( $posted['mobile_nav_items'] ) ) ? $posted['mobile_nav_items'] : array();
			$clean_mob = array();
			foreach ( $rows as $m_item ) {
				if ( ! is_array( $m_item ) ) {
					continue;
				}
				if ( empty( $m_item['title_fa'] ) && empty( $m_item['title_en'] ) ) {
					continue;
				}
				$clean_mob[] = array(
					'title_fa' => sanitize_text_field( isset( $m_item['title_fa'] ) ? $m_item['title_fa'] : '' ),
					'title_en' => sanitize_text_field( isset( $m_item['title_en'] ) ? $m_item['title_en'] : '' ),
					'title_zh' => sanitize_text_field( isset( $m_item['title_zh'] ) ? $m_item['title_zh'] : '' ),
					'url'      => esc_url_raw( isset( $m_item['url'] ) ? $m_item['url'] : '#' ),
					'icon'     => sanitize_key( isset( $m_item['icon'] ) ? $m_item['icon'] : 'home' ),
					'badge'    => sanitize_text_field( isset( $m_item['badge'] ) ? $m_item['badge'] : '' ),
				);
			}
			$options['mobile_nav_items'] = $clean_mob;
		}

		// ۹. پاک‌سازی زبان‌های سفارشی اضافه‌شده علاوه بر ۳ زبان پیش‌فرض
		if ( array_key_exists( 'custom_languages', $posted ) || 'languages' === $submitted_panel ) {
			$row_list    = ( isset( $posted['custom_languages'] ) && is_array( $posted['custom_languages'] ) ) ? $posted['custom_languages'] : array();
			$clean_langs = array();
			foreach ( $row_list as $lang_row ) {
				if ( ! is_array( $lang_row ) ) {
					continue;
				}
				if ( empty( $lang_row['code'] ) ) {
					continue;
				}
				$translations = array();
				if ( isset( $lang_row['translations'] ) && is_array( $lang_row['translations'] ) ) {
					foreach ( $lang_row['translations'] as $t_k => $t_v ) {
						$translations[ sanitize_key( $t_k ) ] = sanitize_text_field( $t_v );
					}
				}
				$clean_langs[] = array(
					'code'         => sanitize_key( $lang_row['code'] ),
					'name'         => sanitize_text_field( isset( $lang_row['name'] ) ? $lang_row['name'] : '' ),
					'native_name'  => sanitize_text_field( isset( $lang_row['native_name'] ) ? $lang_row['native_name'] : '' ),
					'flag'         => sanitize_text_field( isset( $lang_row['flag'] ) ? $lang_row['flag'] : '🌐' ),
					'dir'          => ( isset( $lang_row['dir'] ) && 'rtl' === $lang_row['dir'] ) ? 'rtl' : 'ltr',
					'enabled'      => ! empty( $lang_row['enabled'] ),
					'translations' => $translations,
				);
			}
			$options['custom_languages'] = $clean_langs;
		}

		update_option( ABIDAR_OPTION_KEY, $options );

		$active_tab = isset( $_POST['active_tab'] ) ? sanitize_key( wp_unslash( $_POST['active_tab'] ) ) : 'layout';
		wp_safe_redirect( add_query_arg( array( 'page' => 'abidar-settings', 'tab' => $active_tab, 'updated' => 'true' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * پنل عیب‌یابی ویرایشگر المنتور.
	 *
	 * «المنتور بی‌پایان در حال بارگذاری می‌ماند» دلایل محدودی دارد و همه‌ی آن‌ها
	 * از سمت قالب قابل تشخیص‌اند: نبود پشتیبانی elementor روی پست‌تایپ، تعارض
	 * کلاس پایه ویجت‌ها، هدرهای امنیتی مسدودکننده iframe، خروجی زودهنگام
	 * (headers already sent)، نبود the_content در قالب برگه و BOM.
	 * این پنل همان موارد را زنده بررسی می‌کند تا علت دقیق مشخص شود.
	 */
	public static function render_diagnostics_panel() {
		$rows = array();

		$rows[] = array(
			esc_html__( 'نسخه وردپرس', 'abidar-group' ),
			esc_html( get_bloginfo( 'version' ) ),
			true,
		);

		$rows[] = array(
			esc_html__( 'نسخه المنتور', 'abidar-group' ),
			defined( 'ELEMENTOR_VERSION' ) ? esc_html( ELEMENTOR_VERSION ) : esc_html__( 'نصب یا فعال نیست', 'abidar-group' ),
			defined( 'ELEMENTOR_VERSION' ),
		);

		$rows[] = array(
			esc_html__( 'نسخه قالب آبیدر', 'abidar-group' ),
			esc_html( ABIDAR_VERSION ),
			true,
		);

		// مهم‌ترین عامل خطای مهلک ویرایشگر: کلاس پایه ویجت‌ها.
		$base_ok = class_exists( 'Abidar_Elementor_Widget_Base' )
			&& class_exists( '\Elementor\Widget_Base' )
			&& is_subclass_of( 'Abidar_Elementor_Widget_Base', 'Elementor\Widget_Base' );

		$rows[] = array(
			esc_html__( 'کلاس پایه ویجت‌ها از Widget_Base المنتور ارث می‌برد', 'abidar-group' ),
			$base_ok
				? esc_html__( 'بله', 'abidar-group' )
				: esc_html__( 'خیر — ویجت‌ها عمداً بارگذاری نمی‌شوند تا خطای مهلک رخ ندهد', 'abidar-group' ),
			$base_ok,
		);

		// پشتیبانی المنتور روی پست‌تایپ‌ها.
		$cpt_rows = array();
		$cpt_bad  = false;
		foreach ( array( 'page', 'post', 'abidar_service', 'abidar_project', 'abidar_hf' ) as $cpt ) {
			if ( ! post_type_exists( $cpt ) ) {
				$cpt_rows[] = $cpt . ': ' . esc_html__( 'ثبت نشده', 'abidar-group' );
				continue;
			}
			$supported  = post_type_supports( $cpt, 'elementor' );
			$cpt_rows[] = $cpt . ': ' . ( $supported ? '✔' : '✘' );
			if ( ! $supported ) {
				$cpt_bad = true;
			}
		}

		$rows[] = array(
			esc_html__( 'پشتیبانی elementor روی پست‌تایپ‌ها', 'abidar-group' ),
			esc_html( implode( ' | ', $cpt_rows ) ),
			! $cpt_bad,
		);

		// هدرهای امنیتی مسدودکننده iframe پیش‌نمایش.
		$rows[] = array(
			esc_html__( 'هدر X-Frame-Options که iframe پیش‌نمایش را می‌بندد', 'abidar-group' ),
			esc_html__( 'توسط این قالب ارسال نمی‌شود ✔', 'abidar-group' ),
			true,
		);

		// قالب برگه‌ها باید the_content را صدا بزنند، وگرنه بوم المنتور خالی می‌ماند.
		$templates = array(
			'page-templates/template-home.php',
			'page-templates/template-about.php',
			'page-templates/template-services.php',
			'page-templates/template-projects.php',
			'page-templates/template-team.php',
			'page-templates/template-certificates.php',
			'page-templates/template-contact.php',
			'page.php',
		);
		$missing = array();
		foreach ( $templates as $tpl ) {
			$path = ABIDAR_DIR . '/' . $tpl;
			if ( ! file_exists( $path ) ) {
				continue;
			}
			$source = (string) file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( false === strpos( $source, 'the_content()' ) ) {
				$missing[] = $tpl;
			}
		}

		$rows[] = array(
			esc_html__( 'همه قالب‌های برگه the_content را فرا می‌خوانند', 'abidar-group' ),
			empty( $missing ) ? esc_html__( 'همه ✔', 'abidar-group' ) : esc_html( implode( ', ', $missing ) ),
			empty( $missing ),
		);

		// BOM در فایل‌های اصلی باعث headers already sent و شکستن AJAX ویرایشگر می‌شود.
		$bom = array();
		foreach ( array( 'functions.php', 'header.php', 'footer.php' ) as $file ) {
			$path = ABIDAR_DIR . '/' . $file;
			if ( ! file_exists( $path ) ) {
				continue;
			}
			$handle = fopen( $path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
			if ( $handle ) {
				$head = fread( $handle, 3 );
				fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
				if ( "\xEF\xBB\xBF" === $head ) {
					$bom[] = $file;
				}
			}
		}

		$rows[] = array(
			esc_html__( 'فایل‌های اصلی بدون BOM', 'abidar-group' ),
			empty( $bom ) ? esc_html__( 'بله ✔', 'abidar-group' ) : esc_html( implode( ', ', $bom ) ),
			empty( $bom ),
		);

		// ---- بارگذاری فایل‌های ویجت ----
		$files_loaded = class_exists( 'Abidar_Widget_Services' );
		$rows[] = array(
			esc_html__( 'فایل‌های ویجت بارگذاری شده‌اند', 'abidar-group' ),
			$files_loaded
				? esc_html__( 'بله (قلب init اجرا شده است)', 'abidar-group' )
				: esc_html__( 'خیر — قلاب init اجرا نشده یا المنتور فعال نیست', 'abidar-group' ),
			$files_loaded,
		);

		// ---- وضعیت تک‌تک المان‌های اختصاصی ----
		$widgets = array(
			'Abidar_Widget_Header_Nav'     => 'abidar_header_nav',
			'Abidar_Widget_Footer_Builder' => 'abidar_footer_builder',
			'Abidar_Widget_Lang_Switcher'  => 'abidar_lang_switcher',
			'Abidar_Widget_Hero_Slider'    => 'abidar_hero_slider',
			'Abidar_Widget_Services'       => 'abidar_services',
			'Abidar_Widget_Projects'       => 'abidar_projects',
			'Abidar_Widget_Team'           => 'abidar_team',
			'Abidar_Widget_Certificates'   => 'abidar_certificates',
			'Abidar_Widget_About_Timeline' => 'abidar_about_timeline',
			'Abidar_Widget_Contact_Hub'    => 'abidar_contact_hub',
		);

		$widget_base = 'Elementor' . chr( 92 ) . 'Widget_Base';

		ob_start();
		echo '<table class="widefat striped" style="margin-top:8px;">';
		echo '<thead><tr><th>#</th><th>' . esc_html__( 'کلاس المان', 'abidar-group' ) . '</th>';
		echo '<th>' . esc_html__( 'ساخته شده', 'abidar-group' ) . '</th>';
		echo '<th>' . esc_html__( 'ارث‌بری از Widget_Base', 'abidar-group' ) . '</th>';
		echo '<th>' . esc_html__( 'ثبت در پنل', 'abidar-group' ) . '</th></tr></thead><tbody>';
		$i = 1;
		foreach ( $widgets as $class_name => $slug ) {
			$exists   = class_exists( $class_name );
			$inherits = $exists && is_subclass_of( $class_name, $widget_base );
			$panel    = false;
			if ( $exists && class_exists( '\Elementor\Plugin' ) ) {
				try {
					$manager = \Elementor\Plugin::instance()->widgets_manager;
					$panel   = (bool) $manager->get_widget_types( $slug );
				} catch ( \Throwable $e ) {
					$panel = false;
				}
			}

			echo '<tr>';
			echo '<td>' . (int) $i . '</td>';
			echo '<td><code>' . esc_html( $class_name ) . '</code></td>';
			echo '<td>' . ( $exists ? '&#10004;' : '&#10008;' ) . '</td>';
			echo '<td>' . ( $inherits ? '&#10004;' : '&#10008;' ) . '</td>';
			echo '<td>' . ( $panel ? '&#10004;' : '&#10008;' ) . '</td>';
			echo '</tr>';
			$i++;
		}
		echo '</tbody></table>';
		$widgets_table = ob_get_clean();

		$rows[] = array(
			esc_html__( 'دموی برگه‌ها نصب شده', 'abidar-group' ),
			get_option( 'abidar_demo_installed_v2' )
				? esc_html__( 'بله', 'abidar-group' )
				: esc_html__( 'خیر — از تب ۸ اجرا کنید', 'abidar-group' ),
			(bool) get_option( 'abidar_demo_installed_v2' ),
		);
		?>
		<div class="abidar-admin-card">
			<p>
				<?php esc_html_e( 'اگر ویرایشگر المنتور روی «در حال بارگذاری» می‌ماند، این جدول را بررسی کنید. هر ردیف با ✘ باید ابتدا رفع شود.', 'abidar-group' ); ?>
			</p>
			<?php echo $widgets_table; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<h3 style="margin-top:22px;"><?php esc_html_e( 'بررسی‌های عمومی', 'abidar-group' ); ?></h3>
			<table class="widefat striped" style="margin-top:12px;">
				<thead>
					<tr>
						<th style="width:40%;"><?php esc_html_e( 'بررسی', 'abidar-group' ); ?></th>
						<th><?php esc_html_e( 'وضعیت', 'abidar-group' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><strong><?php echo $row[0]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong></td>
							<td>
								<span style="color:<?php echo $row[2] ? '#15803d' : '#b91c1c'; ?>; font-weight:600;">
									<?php echo $row[2] ? '&#10004; ' : '&#10008; '; ?>
								</span>
								<?php echo $row[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p style="margin-top:14px;">
				<?php esc_html_e( 'هدر و فوتر ساخته‌شده با المنتور در حالت ویرایش به‌صورت تودرتو رندر نمی‌شوند؛ این کار شنونده‌های پیش‌نمایش را دوباره ثبت می‌کرد و نشانگر بارگذاری هرگز پایان نمی‌یافت.', 'abidar-group' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * رندر رابط کاربری کامل پنل تنظیمات تفکیک‌شده در پیشخوان وردپرس
	 */
	public static function render_settings_page() {
		$options    = abidar_get_all_options();
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'layout'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/*
		 * نام تب‌ها با تابع ترجمه وردپرس خوانده می‌شوند تا اگر مدیر پیشخوان
		 * انگلیسی یا چینی باشد، برچسب‌ها هم به همان زبان دربیایند. متن فارسی
		 * خودِ msgid است؛ پس بدون فایل ترجمه هم چیزی تغییر نمی‌کند.
		 */
		$tabs       = array(
			'layout'           => __( '۱. طرح و چیدمان و فونت', 'abidar-group' ),
			'mobile-nav'       => __( '۲. ناوبری موبایل', 'abidar-group' ),
			'floating-contact' => __( '۳. دکمه شناور تماس چندبخشی', 'abidar-group' ),
			'blog'             => __( '۴. قالب مقاله و پست‌ها', 'abidar-group' ),
			'preloader'        => __( '۵. پیش‌بارگذاری سایت', 'abidar-group' ),
			'security'         => __( '۶. امنیت پیشرفته', 'abidar-group' ),
			'languages'        => __( '۷. زبان‌های سایت (FA/EN/ZH+)', 'abidar-group' ),
			'demo'             => __( '۸. ساخت دمو و برگه‌های المنتور', 'abidar-group' ),
			'diagnostics'      => __( '۹. عیب‌یابی ویرایشگر المنتور', 'abidar-group' ),
		);
		?>
		<?php
		/*
		 * جهت پنل از زبان واقعی پیشخوان گرفته می‌شود، نه از زبان سایت.
		 * مدیرِ پیشخوان انگلیسی باید همان چیدمان LTR را ببیند که بقیه وردپرس
		 * به او نشان می‌دهد؛ وگرنه پنل تنها بخش راست‌به‌چپِ صفحه می‌شود.
		 */
		$abidar_admin_dir = is_rtl() ? 'rtl' : 'ltr';
		?>
		<div class="wrap abidar-admin-wrap" dir="<?php echo esc_attr( $abidar_admin_dir ); ?>">
			<div class="abidar-admin-header">
				<div class="abidar-admin-header__brand">
					<span class="abidar-admin-header__badge">ABIDAR v<?php echo esc_html( ABIDAR_VERSION ); ?></span>
					<div>
						<h1><?php esc_html_e( 'پنل تنظیمات جامع قالب هلدینگ «گروه آبیدر»', 'abidar-group' ); ?></h1>
						<p><?php esc_html_e( 'حوزه‌های معادن، انرژی و نفت | منطبق با آخرین نسخه وردپرس و صفحه‌ساز المنتور رایگان', 'abidar-group' ); ?></p>
					</div>
				</div>
				<div class="abidar-admin-header__actions">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'مشاهده وب‌سایت هلدینگ', 'abidar-group' ); ?>
					</a>
				</div>
			</div>

			<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e( 'تنظیمات قالب گروه آبیدر با موفقیت ذخیره و در سراسر سایت اعمال شد.', 'abidar-group' ); ?></strong></p>
				</div>
			<?php endif; ?>

			<!-- تب‌های تفکیک‌شده تنظیمات قالب -->
			<nav class="abidar-admin-tabs">
				<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'abidar-settings', 'tab' => $tab_key ), admin_url( 'admin.php' ) ) ); ?>"
					   class="abidar-admin-tab <?php echo ( $active_tab === $tab_key ) ? 'is-active' : ''; ?>"
					   data-tab-target="<?php echo esc_attr( $tab_key ); ?>">
						<?php echo esc_html( $tab_label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="abidar-admin-form">
				<input type="hidden" name="action" value="abidar_save_settings" />
				<input type="hidden" name="active_tab" id="abidar-active-tab-input" value="<?php echo esc_attr( $active_tab ); ?>" />
				<?php
				/*
				 * همان مقدار تب، با نام دیگری. سرور از این فیلد می‌فهمد کدام
				 * بخش ذخیره شده تا تشخیص دهد مدیر همه ردیف‌های یک لیست را
				 * پاک کرده است (مرورگر در آن حالت هیچ فیلدی نمی‌فرستد).
				 */
				?>
				<input type="hidden" name="abidar_panel" value="<?php echo esc_attr( $active_tab ); ?>" data-abidar-panel-mirror />
				<?php wp_nonce_field( 'abidar_save_settings_action', 'abidar_settings_nonce' ); ?>

				<!-- ================================================================
				     تب ۱: تنظیمات طرح، چیدمان، رنگ‌ها و آپلود فونت اختصاصی
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'layout' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="layout">
					<h2><?php esc_html_e( 'تنظیمات طرح و چیدمان، رنگ‌بندی، تایپوگرافی و فونت اختصاصی', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'ابعاد کانتینر و چیدمان صفحات', 'abidar-group' ); ?></h3>
							<label><?php esc_html_e( 'حالت چیدمان کلی قالب:', 'abidar-group' ); ?>
								<select name="abidar[layout_mode]">
									<option value="wide" <?php selected( $options['layout_mode'], 'wide' ); ?>><?php esc_html_e( 'عریض استاندارد (Wide Corporate)', 'abidar-group' ); ?></option>
									<option value="fullwidth" <?php selected( $options['layout_mode'], 'fullwidth' ); ?>><?php esc_html_e( 'تمام‌عرض صنعتی (Full-Width Industrial)', 'abidar-group' ); ?></option>
									<option value="boxed" <?php selected( $options['layout_mode'], 'boxed' ); ?>><?php esc_html_e( 'جعبه‌ای (Boxed Frame)', 'abidar-group' ); ?></option>
								</select>
							</label>
							<label><?php esc_html_e( 'حداکثر عرض کانتینر (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[container_width]" value="<?php echo esc_attr( $options['container_width'] ); ?>" min="1000" max="1920" />
							</label>
							<label><?php esc_html_e( 'فاصله ستون‌های گرید (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[grid_gap]" value="<?php echo esc_attr( $options['grid_gap'] ); ?>" min="12" max="60" />
							</label>
							<label><?php esc_html_e( 'شعاع انحنای کارت‌ها و دکمه‌ها (Border Radius px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[border_radius]" value="<?php echo esc_attr( $options['border_radius'] ); ?>" min="0" max="36" />
							</label>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'پالت رنگ سازمانی گروه آبیدر', 'abidar-group' ); ?></h3>
							<label><?php esc_html_e( 'رنگ اصلی برند (طلایی معدنی و صنعتی):', 'abidar-group' ); ?>
								<input type="text" name="abidar[primary_color]" value="<?php echo esc_attr( $options['primary_color'] ); ?>" class="abidar-color-picker" />
							</label>
							<label><?php esc_html_e( 'رنگ دوم سازمانی (سرمه‌ای نفتی تیره):', 'abidar-group' ); ?>
								<input type="text" name="abidar[secondary_color]" value="<?php echo esc_attr( $options['secondary_color'] ); ?>" class="abidar-color-picker" />
							</label>
							<label><?php esc_html_e( 'رنگ تاکیدی (آبی انرژی پاک):', 'abidar-group' ); ?>
								<input type="text" name="abidar[accent_color]" value="<?php echo esc_attr( $options['accent_color'] ); ?>" class="abidar-color-picker" />
							</label>
							<label><?php esc_html_e( 'رنگ پس‌زمینه بخش‌های تیره:', 'abidar-group' ); ?>
								<input type="text" name="abidar[surface_dark_color]" value="<?php echo esc_attr( $options['surface_dark_color'] ); ?>" class="abidar-color-picker" />
							</label>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'سایز فونت‌ها و بارگذاری فونت اختصاصی (WOFF2 / TTF)', 'abidar-group' ); ?></h3>
							<label><?php esc_html_e( 'سایز پایه متن سایت (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[base_font_size]" value="<?php echo esc_attr( $options['base_font_size'] ); ?>" min="13" max="22" />
							</label>
							<label><?php esc_html_e( 'سایز تیترهای H1 (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[h1_font_size]" value="<?php echo esc_attr( $options['h1_font_size'] ); ?>" min="28" max="72" />
							</label>
							<label><?php esc_html_e( 'نام خانواده فونت اختصاصی (در صورت آپلود):', 'abidar-group' ); ?>
								<input type="text" name="abidar[custom_font_name]" value="<?php echo esc_attr( $options['custom_font_name'] ); ?>" placeholder="مثلاً IRANSansX یا Peyda" />
							</label>
							<label><?php esc_html_e( 'فایل فونت اختصاصی WOFF2:', 'abidar-group' ); ?>
								<div class="abidar-upload-row">
									<input type="url" name="abidar[custom_font_woff2_url]" id="custom_font_woff2_url" value="<?php echo esc_url( $options['custom_font_woff2_url'] ); ?>" dir="ltr" />
									<button type="button" class="button abidar-media-upload" data-target="#custom_font_woff2_url"><?php esc_html_e( 'بارگذاری فونت', 'abidar-group' ); ?></button>
								</div>
							</label>
							<label><?php esc_html_e( 'فایل فونت اختصاصی TTF / WOFF:', 'abidar-group' ); ?>
								<div class="abidar-upload-row">
									<input type="url" name="abidar[custom_font_ttf_url]" id="custom_font_ttf_url" value="<?php echo esc_url( $options['custom_font_ttf_url'] ); ?>" dir="ltr" />
									<button type="button" class="button abidar-media-upload" data-target="#custom_font_ttf_url"><?php esc_html_e( 'بارگذاری فونت', 'abidar-group' ); ?></button>
								</div>
							</label>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'چیدمان هدر و فوتر (پیش‌فرض یا ساخته‌شده با المنتور رایگان)', 'abidar-group' ); ?></h3>
							<label><?php esc_html_e( 'طرح هدر سایت:', 'abidar-group' ); ?>
								<select name="abidar[header_layout]">
									<option value="layout_1" <?php selected( $options['header_layout'], 'layout_1' ); ?>><?php esc_html_e( 'طرح ۱: هدر دوطبقه اجرایی هلدینگ (Topbar + Mega Nav)', 'abidar-group' ); ?></option>
									<option value="layout_2" <?php selected( $options['header_layout'], 'layout_2' ); ?>><?php esc_html_e( 'طرح ۲: هدر شیشه‌ای شفاف صنعتی (Transparent Glass)', 'abidar-group' ); ?></option>
									<option value="layout_3" <?php selected( $options['header_layout'], 'layout_3' ); ?>><?php esc_html_e( 'طرح ۳: هدر لوگو وسط تشریفاتی (Centered Emblem)', 'abidar-group' ); ?></option>
									<option value="layout_4" <?php selected( $options['header_layout'], 'layout_4' ); ?>><?php esc_html_e( 'طرح ۴: هدر فشرده مدرن با نوار شاخص (Compact Ticker)', 'abidar-group' ); ?></option>
									<option value="elementor_custom" <?php selected( $options['header_layout'], 'elementor_custom' ); ?>><?php esc_html_e( 'استفاده از هدر طراحی‌شده در المنتور رایگان (abidar_hf)', 'abidar-group' ); ?></option>
								</select>
							</label>
							<label><?php esc_html_e( 'طرح فوتر سایت:', 'abidar-group' ); ?>
								<select name="abidar[footer_layout]">
									<option value="layout_1" <?php selected( $options['footer_layout'], 'layout_1' ); ?>><?php esc_html_e( 'طرح ۱: مگافوتر ۴ ستونه صنعتی + خبرنامه و دفاتر بین‌المللی', 'abidar-group' ); ?></option>
									<option value="layout_2" <?php selected( $options['footer_layout'], 'layout_2' ); ?>><?php esc_html_e( 'طرح ۲: فوتر دوطبقه مدیریتی با کارت‌های معاونت‌ها', 'abidar-group' ); ?></option>
									<option value="layout_3" <?php selected( $options['footer_layout'], 'layout_3' ); ?>><?php esc_html_e( 'طرح ۳: فوتر مینیمال شرکتی', 'abidar-group' ); ?></option>
									<option value="elementor_custom" <?php selected( $options['footer_layout'], 'elementor_custom' ); ?>><?php esc_html_e( 'استفاده از فوتر طراحی‌شده در المنتور رایگان (abidar_hf)', 'abidar-group' ); ?></option>
								</select>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[sticky_header]" value="1" <?php checked( $options['sticky_header'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی هدر چسبان هوشمند (Sticky Header) هنگام اسکرول', 'abidar-group' ); ?>
							</label>
						</div>
					</div>
				</section>

				<!-- ================================================================
				     تب ۲: تنظیمات ناوبری موبایل
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'mobile-nav' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="mobile-nav">
					<h2><?php esc_html_e( 'تنظیمات ناوبری حرفه‌ای موبایل (چیدمان‌های متفاوت + تعریف دکمه‌ها و لینک‌ها)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'چیدمان و عملکرد منوی موبایل', 'abidar-group' ); ?></h3>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[mobile_nav_enabled]" value="1" <?php checked( $options['mobile_nav_enabled'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی سیستم ناوبری اختصاصی موبایل', 'abidar-group' ); ?>
							</label>
							<label><?php esc_html_e( 'انتخاب چیدمان ناوبری موبایل:', 'abidar-group' ); ?>
								<select name="abidar[mobile_nav_layout]">
									<option value="bottom_app_bar" <?php selected( $options['mobile_nav_layout'], 'bottom_app_bar' ); ?>><?php esc_html_e( 'چیدمان ۱: نوار ابزار پایین اپلیکیشنی (Bottom App Bar) + کشوی کناری', 'abidar-group' ); ?></option>
									<option value="floating_dock" <?php selected( $options['mobile_nav_layout'], 'floating_dock' ); ?>><?php esc_html_e( 'چیدمان ۲: داک شیشه‌ای شناور کپسولی (Floating Glass Dock)', 'abidar-group' ); ?></option>
									<option value="fullscreen_overlay" <?php selected( $options['mobile_nav_layout'], 'fullscreen_overlay' ); ?>><?php esc_html_e( 'چیدمان ۳: منوی تمام‌صفحه صنعتی با انیمیشن (Fullscreen Overlay)', 'abidar-group' ); ?></option>
									<option value="slide_drawer" <?php selected( $options['mobile_nav_layout'], 'slide_drawer' ); ?>><?php esc_html_e( 'چیدمان ۴: منوی کشویی چندلایه شرکتی (Slide-Out Drawer)', 'abidar-group' ); ?></option>
								</select>
							</label>
							<label><?php esc_html_e( 'عنوان دکمه اقدام سریع (CTA) در موبایل (فارسی):', 'abidar-group' ); ?>
								<input type="text" name="abidar[mobile_cta_text_fa]" value="<?php echo esc_attr( $options['mobile_cta_text_fa'] ); ?>" />
							</label>
							<label><?php esc_html_e( 'لینک دکمه اقدام سریع موبایل:', 'abidar-group' ); ?>
								<input type="text" name="abidar[mobile_cta_url]" value="<?php echo esc_attr( $options['mobile_cta_url'] ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'شماره تماس مستقیم در منوی موبایل:', 'abidar-group' ); ?>
								<input type="text" name="abidar[mobile_quick_phone]" value="<?php echo esc_attr( $options['mobile_quick_phone'] ); ?>" dir="ltr" />
							</label>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'تعریف دکمه‌ها و لینک‌های نوار ناوبری موبایل', 'abidar-group' ); ?></h3>
							<p class="description">
								<?php esc_html_e( 'هر ردیف یک دکمه در منوی موبایل است. با دکمه «افزودن دکمه» ردیف تازه بسازید و با «حذف» ردیف اضافی را بردارید. حذف پس از ذخیره تنظیمات قطعی می‌شود.', 'abidar-group' ); ?>
							</p>
							<div id="abidar-mobile-nav-list" class="abidar-repeater-list">
							<?php foreach ( $options['mobile_nav_items'] as $idx => $item ) : ?>
								<div class="abidar-repeater-item">
									<div class="abidar-repeater-item__head">
										<strong><?php echo esc_html( sprintf( 'دکمه شماره %d', $idx + 1 ) ); ?></strong>
										<button type="button" class="button abidar-repeater-remove" aria-label="<?php esc_attr_e( 'حذف این دکمه', 'abidar-group' ); ?>"><?php esc_html_e( 'حذف', 'abidar-group' ); ?></button>
									</div>
									<div class="abidar-repeater-row">
										<input type="text" name="abidar[mobile_nav_items][<?php echo (int) $idx; ?>][title_fa]" value="<?php echo esc_attr( $item['title_fa'] ); ?>" placeholder="عنوان فارسی" />
										<input type="text" name="abidar[mobile_nav_items][<?php echo (int) $idx; ?>][title_en]" value="<?php echo esc_attr( $item['title_en'] ); ?>" placeholder="English Title" dir="ltr" />
										<input type="text" name="abidar[mobile_nav_items][<?php echo (int) $idx; ?>][title_zh]" value="<?php echo esc_attr( $item['title_zh'] ); ?>" placeholder="中文标题" dir="ltr" />
										<input type="text" name="abidar[mobile_nav_items][<?php echo (int) $idx; ?>][url]" value="<?php echo esc_attr( $item['url'] ); ?>" placeholder="URL" dir="ltr" />
										<input type="text" name="abidar[mobile_nav_items][<?php echo (int) $idx; ?>][icon]" value="<?php echo esc_attr( $item['icon'] ); ?>" placeholder="home/layers/briefcase/award/phone" dir="ltr" />
										<input type="text" name="abidar[mobile_nav_items][<?php echo (int) $idx; ?>][badge]" value="<?php echo esc_attr( $item['badge'] ); ?>" placeholder="نشان (Badge)" />
									</div>
								</div>
							<?php endforeach; ?>
							</div>
							<button type="button" class="button button-primary abidar-repeater-add" data-target="#abidar-mobile-nav-list">
								+ <?php esc_html_e( 'افزودن دکمه جدید به منوی موبایل', 'abidar-group' ); ?>
							</button>
						</div>
					</div>
				</section>

				<!-- ================================================================
				     تب ۳: تنظیمات دکمه شناور تماس با ما و بخش‌های مختلف هلدینگ
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'floating-contact' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="floating-contact">
					<h2><?php esc_html_e( 'تنظیمات دکمه شناور ارتباط چندبخشی و شبکه‌های اجتماعی', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'تنظیمات عمومی و لینک شبکه‌های اجتماعی', 'abidar-group' ); ?></h3>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[floating_contact_enabled]" value="1" <?php checked( $options['floating_contact_enabled'], true ); ?> />
								<?php esc_html_e( 'نمایش دکمه شناور تماس چندبخشی در تمام صفحات', 'abidar-group' ); ?>
							</label>
							<label><?php esc_html_e( 'جایگاه قرارگیری دکمه شناور:', 'abidar-group' ); ?>
								<select name="abidar[floating_position]">
									<option value="bottom-left" <?php selected( $options['floating_position'], 'bottom-left' ); ?>><?php esc_html_e( 'پایین چپ (Bottom Left)', 'abidar-group' ); ?></option>
									<option value="bottom-right" <?php selected( $options['floating_position'], 'bottom-right' ); ?>><?php esc_html_e( 'پایین راست (Bottom Right)', 'abidar-group' ); ?></option>
								</select>
							</label>
							<label><?php esc_html_e( 'لینک لینکدین (LinkedIn):', 'abidar-group' ); ?>
								<input type="url" name="abidar[floating_socials][linkedin]" value="<?php echo esc_url( $options['floating_socials']['linkedin'] ?? '' ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'لینک تلگرام (Telegram):', 'abidar-group' ); ?>
								<input type="url" name="abidar[floating_socials][telegram]" value="<?php echo esc_url( $options['floating_socials']['telegram'] ?? '' ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'لینک واتساپ (WhatsApp):', 'abidar-group' ); ?>
								<input type="url" name="abidar[floating_socials][whatsapp]" value="<?php echo esc_url( $options['floating_socials']['whatsapp'] ?? '' ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'لینک اینستاگرام (Instagram):', 'abidar-group' ); ?>
								<input type="url" name="abidar[floating_socials][instagram]" value="<?php echo esc_url( $options['floating_socials']['instagram'] ?? '' ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'ایمیل مرکزی (Email):', 'abidar-group' ); ?>
								<input type="email" name="abidar[floating_socials][email]" value="<?php echo esc_attr( $options['floating_socials']['email'] ?? '' ); ?>" dir="ltr" />
							</label>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'مدیریت بخش‌های مختلف در دکمه شناور تماس (معادن، انرژی، نفت، بین‌الملل و...)', 'abidar-group' ); ?></h3>
							<p class="description"><?php esc_html_e( 'کاربر با کلیک روی دکمه شناور، لیست این بخش‌ها را مشاهده کرده و مستقیماً با همان بخش تماس می‌گیرد.', 'abidar-group' ); ?></p>
							<div id="abidar-departments-list" class="abidar-repeater-list">
							<?php foreach ( $options['floating_departments'] as $d_idx => $dept ) : ?>
								<div class="abidar-repeater-item">
									<div class="abidar-repeater-item__head">
										<strong><?php echo esc_html( ! empty( $dept['name_fa'] ) ? $dept['name_fa'] : sprintf( 'بخش شماره %d', $d_idx + 1 ) ); ?></strong>
										<button type="button" class="button abidar-repeater-remove" aria-label="<?php esc_attr_e( 'حذف این بخش', 'abidar-group' ); ?>"><?php esc_html_e( 'حذف', 'abidar-group' ); ?></button>
									</div>
									<div class="abidar-repeater-row">
										<input type="hidden" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][id]" value="<?php echo esc_attr( $dept['id'] ); ?>" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][name_fa]" value="<?php echo esc_attr( $dept['name_fa'] ); ?>" placeholder="نام بخش (فارسی)" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][name_en]" value="<?php echo esc_attr( $dept['name_en'] ); ?>" placeholder="Department Name (EN)" dir="ltr" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][name_zh]" value="<?php echo esc_attr( $dept['name_zh'] ); ?>" placeholder="部门名称 (ZH)" dir="ltr" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][role_fa]" value="<?php echo esc_attr( $dept['role_fa'] ); ?>" placeholder="شرح وظایف بخش (فارسی)" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][role_en]" value="<?php echo esc_attr( isset( $dept['role_en'] ) ? $dept['role_en'] : '' ); ?>" placeholder="Department Role (EN)" dir="ltr" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][role_zh]" value="<?php echo esc_attr( isset( $dept['role_zh'] ) ? $dept['role_zh'] : '' ); ?>" placeholder="部门职责 (ZH)" dir="ltr" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][phone]" value="<?php echo esc_attr( $dept['phone'] ); ?>" placeholder="شماره تلفن" dir="ltr" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][ext]" value="<?php echo esc_attr( $dept['ext'] ); ?>" placeholder="داخلی" dir="ltr" />
										<input type="url" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][whatsapp]" value="<?php echo esc_url( $dept['whatsapp'] ); ?>" placeholder="لینک واتساپ" dir="ltr" />
										<input type="email" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][email]" value="<?php echo esc_attr( $dept['email'] ); ?>" placeholder="ایمیل بخش" dir="ltr" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][hours_fa]" value="<?php echo esc_attr( isset( $dept['hours_fa'] ) ? $dept['hours_fa'] : '' ); ?>" placeholder="ساعات پاسخگویی (فارسی)" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][hours_en]" value="<?php echo esc_attr( isset( $dept['hours_en'] ) ? $dept['hours_en'] : '' ); ?>" placeholder="Working Hours (EN)" dir="ltr" />
										<input type="text" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][hours_zh]" value="<?php echo esc_attr( isset( $dept['hours_zh'] ) ? $dept['hours_zh'] : '' ); ?>" placeholder="工作时间 (ZH)" dir="ltr" />
										<select name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][icon]">
											<?php
											$dept_icon = isset( $dept['icon'] ) ? $dept['icon'] : 'pickaxe';
											$icon_choices = array(
												'pickaxe'   => 'معادن (Pickaxe)',
												'bolt'      => 'انرژی (Bolt)',
												'droplet'   => 'نفت و گاز (Droplet)',
												'flask'     => 'پتروشیمی (Flask)',
												'globe'     => 'بین‌الملل (Globe)',
												'phone'     => 'تلفن (Phone)',
												'mail'      => 'ایمیل (Mail)',
												'building'  => 'دفتر مرکزی (Building)',
											);
											foreach ( $icon_choices as $icon_key => $icon_label ) :
												?>
												<option value="<?php echo esc_attr( $icon_key ); ?>" <?php selected( $dept_icon, $icon_key ); ?>>
													<?php echo esc_html( $icon_label ); ?>
												</option>
											<?php endforeach; ?>
										</select>
										<label class="abidar-color-field">
											<span><?php esc_html_e( 'رنگ نشان بخش', 'abidar-group' ); ?></span>
											<input type="text" class="abidar-color-picker" name="abidar[floating_departments][<?php echo (int) $d_idx; ?>][badge_color]" value="<?php echo esc_attr( isset( $dept['badge_color'] ) ? $dept['badge_color'] : '#D97706' ); ?>" />
										</label>
									</div>
								</div>
							<?php endforeach; ?>
							</div>
							<button type="button" class="button button-primary abidar-repeater-add" data-target="#abidar-departments-list">
								+ <?php esc_html_e( 'افزودن بخش جدید به دکمه شناور', 'abidar-group' ); ?>
							</button>
							<p class="description">
								<?php esc_html_e( 'اگر هیچ بخشی تعریف نشود، سایت از بخش‌های پیش‌فرض (معادن، انرژی، نفت و گاز، بین‌الملل) استفاده می‌کند.', 'abidar-group' ); ?>
							</p>
					</div>
				</section>

				<!-- ================================================================
				     تب ۴: تنظیمات قالب مقاله و پست‌ها
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'blog' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="blog">
					<h2><?php esc_html_e( 'تنظیمات قالب پیش‌فرض مقاله و پست‌های خبری هلدینگ', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'انتخاب قالب پیش‌فرض نمایش مقالات (Single Post Template)', 'abidar-group' ); ?></h3>
							<p class="description"><?php esc_html_e( 'هر مقاله‌ای که در بخش نوشته‌های وردپرس منتشر شود، به صورت خودکار با این قالب حرفه‌ای نمایش داده می‌شود.', 'abidar-group' ); ?></p>
							<label><?php esc_html_e( 'طرح قالب مقاله:', 'abidar-group' ); ?>
								<select name="abidar[single_post_layout]">
									<option value="executive_magazine" <?php selected( $options['single_post_layout'], 'executive_magazine' ); ?>><?php esc_html_e( 'طرح ۱: مجله صنعتی مدیریتی با فهرست سرفصل چسبان (Executive Magazine + Sticky TOC)', 'abidar-group' ); ?></option>
									<option value="fullwidth_industrial" <?php selected( $options['single_post_layout'], 'fullwidth_industrial' ); ?>><?php esc_html_e( 'طرح ۲: گزارش فنی تمام‌عرض سینمایی (Full-Width Industrial Report)', 'abidar-group' ); ?></option>
									<option value="classic_sidebar" <?php selected( $options['single_post_layout'], 'classic_sidebar' ); ?>><?php esc_html_e( 'طرح ۳: قالب خبری با سایدبار تخصصی (Corporate Sidebar Layout)', 'abidar-group' ); ?></option>
								</select>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[post_reading_progress]" value="1" <?php checked( $options['post_reading_progress'], true ); ?> />
								<?php esc_html_e( 'نمایش نوار پیشرفت مطالعه در بالای صفحه مقاله', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[post_reading_time]" value="1" <?php checked( $options['post_reading_time'], true ); ?> />
								<?php esc_html_e( 'محاسبه و نمایش خودکار زمان تخمینی مطالعه مقاله', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[post_sticky_toc]" value="1" <?php checked( $options['post_sticky_toc'], true ); ?> />
								<?php esc_html_e( 'تولید خودکار فهرست سرفصل‌های مقاله (Table of Contents)', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[post_author_box]" value="1" <?php checked( $options['post_author_box'], true ); ?> />
								<?php esc_html_e( 'نمایش باکس شناسنامه نویسنده / واحد فنی هلدینگ', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[post_related_articles]" value="1" <?php checked( $options['post_related_articles'], true ); ?> />
								<?php esc_html_e( 'نمایش مقالات و گزارش‌های مرتبط در انتهای مقاله', 'abidar-group' ); ?>
							</label>
						</div>
					</div>
				</section>

				<!-- ================================================================
				     تب ۵: تنظیمات پیش‌بارگذاری سایت (Preloader)
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'preloader' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="preloader">
					<h2><?php esc_html_e( 'تنظیمات پیش‌بارگذاری سایت (Preloader)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'استایل و زمان‌بندی پیش‌بارگذاری', 'abidar-group' ); ?></h3>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[preloader_enabled]" value="1" <?php checked( $options['preloader_enabled'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی انیمیشن پیش‌بارگذاری سایت', 'abidar-group' ); ?>
							</label>
							<label><?php esc_html_e( 'انتخاب طرح انیمیشن پیش‌بارگذاری:', 'abidar-group' ); ?>
								<select name="abidar[preloader_style]">
									<option value="industrial_pulse" <?php selected( $options['preloader_style'], 'industrial_pulse' ); ?>><?php esc_html_e( 'طرح ۱: حلقه‌های انرژی و پالس صنعتی آبیدر', 'abidar-group' ); ?></option>
									<option value="minimal_gold" <?php selected( $options['preloader_style'], 'minimal_gold' ); ?>><?php esc_html_e( 'طرح ۲: مونوگرام طلایی مینیمال (AG Luxury Emblem)', 'abidar-group' ); ?></option>
									<option value="rig_svg" <?php selected( $options['preloader_style'], 'rig_svg' ); ?>><?php esc_html_e( 'طرح ۳: گرافیک برداری دکل حفاری و معدن (SVG Rig)', 'abidar-group' ); ?></option>
								</select>
							</label>
							<label><?php esc_html_e( 'متن نمایشی پیش‌بارگذاری (فارسی):', 'abidar-group' ); ?>
								<input type="text" name="abidar[preloader_text_fa]" value="<?php echo esc_attr( $options['preloader_text_fa'] ); ?>" />
							</label>
							<label><?php esc_html_e( 'متن نمایشی پیش‌بارگذاری (انگلیسی):', 'abidar-group' ); ?>
								<input type="text" name="abidar[preloader_text_en]" value="<?php echo esc_attr( $options['preloader_text_en'] ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'مدت زمان نمایش (میلی‌ثانیه):', 'abidar-group' ); ?>
								<input type="number" name="abidar[preloader_duration]" value="<?php echo esc_attr( $options['preloader_duration'] ); ?>" min="300" max="4000" step="100" />
							</label>
						</div>
					</div>
				</section>

				<!-- ================================================================
				     تب ۶: تنظیمات امنیت سایت (High-Security Suite)
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'security' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="security">
					<h2><?php esc_html_e( 'سپر امنیتی پیشرفته گروه آبیدر (تغییر آدرس ادمین، محدودیت ورود، ضد اسپم)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<div class="abidar-admin-card">
							<h3><?php esc_html_e( '۱. تغییر آدرس ورود ادمین و مخفی‌سازی wp-login.php', 'abidar-group' ); ?></h3>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_custom_login_enabled]" value="1" <?php checked( $options['security_custom_login_enabled'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی آدرس اختصاصی ورود و مسدودسازی دسترسی مستقیم به wp-login.php', 'abidar-group' ); ?>
							</label>
							<label><?php esc_html_e( 'اسلاگ آدرس ورود مخفی (مثلاً abidar-secure-gateway):', 'abidar-group' ); ?>
								<input type="text" name="abidar[security_custom_login_slug]" value="<?php echo esc_attr( $options['security_custom_login_slug'] ); ?>" dir="ltr" />
							</label>
							<p class="description">
								<?php
								printf(
									/* translators: %s: custom login URL */
									esc_html__( 'آدرس ورود اختصاصی شما: %s', 'abidar-group' ),
									'<code>' . esc_url( home_url( '/' . $options['security_custom_login_slug'] ) ) . '</code>'
								);
								?>
							</p>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( '۲. محدودیت تعداد تلاش ورود و جلوگیری از ورود بیش از حد (Brute-Force)', 'abidar-group' ); ?></h3>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_limit_login_enabled]" value="1" <?php checked( $options['security_limit_login_enabled'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی قفل خودکار IP در صورت تلاش‌های ناموفق ورود', 'abidar-group' ); ?>
							</label>
							<label><?php esc_html_e( 'حداکثر تعداد تلاش مجاز پیش از مسدودسازی:', 'abidar-group' ); ?>
								<input type="number" name="abidar[security_max_login_attempts]" value="<?php echo esc_attr( $options['security_max_login_attempts'] ); ?>" min="2" max="20" />
							</label>
							<label><?php esc_html_e( 'مدت زمان مسدودسازی مهاجم (دقیقه):', 'abidar-group' ); ?>
								<input type="number" name="abidar[security_lockout_duration]" value="<?php echo esc_attr( $options['security_lockout_duration'] ); ?>" min="5" max="1440" />
							</label>
							<label><?php esc_html_e( 'لیست سفید IPهای مجاز:', 'abidar-group' ); ?>
								<textarea name="abidar[security_ip_whitelist]" rows="4" dir="ltr" placeholder="203.0.113.10&#10;198.51.100.0/24"><?php echo esc_textarea( $options['security_ip_whitelist'] ); ?></textarea>
							</label>
							<p class="description">
								<?php esc_html_e( 'هر IP در یک خط، یا جداشده با کاما. محدوده CIDR هم پذیرفته می‌شود (مثل 198.51.100.0/24).', 'abidar-group' ); ?>
							</p>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( '۳. جلوگیری از اسپم و سخت‌سازی هسته وردپرس', 'abidar-group' ); ?></h3>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_antispam_honeypot]" value="1" <?php checked( $options['security_antispam_honeypot'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی تله عسل ضد ربات (Honeypot) در فرم تماس و دیدگاه‌ها', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_antispam_timetrap]" value="1" <?php checked( $options['security_antispam_timetrap'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی تله زمانی (جلوگیری از ارسال آنی فرم توسط ربات‌های اسپمر)', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_math_captcha]" value="1" <?php checked( $options['security_math_captcha'], true ); ?> />
								<?php esc_html_e( 'فعال‌سازی چالش امنیتی ریاضی فارسی در فرم‌ها', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_disable_xmlrpc]" value="1" <?php checked( $options['security_disable_xmlrpc'], true ); ?> />
								<?php esc_html_e( 'مسدودسازی کامل پروتکل XML-RPC', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_block_user_enum]" value="1" <?php checked( $options['security_block_user_enum'], true ); ?> />
								<?php esc_html_e( 'جلوگیری از استخراج لیست کاربران از طریق REST API', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_http_headers]" value="1" <?php checked( $options['security_http_headers'], true ); ?> />
								<?php esc_html_e( 'ارسال هدرهای امنیتی HTTP (XSS-Protection, Nosniff, Referrer-Policy)', 'abidar-group' ); ?>
							</label>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( '۴. گزارش زنده رویدادهای امنیتی ثبت‌شده', 'abidar-group' ); ?></h3>
							<table class="widefat striped">
								<thead>
									<tr>
										<th><?php esc_html_e( 'نوع رویداد', 'abidar-group' ); ?></th>
										<th><?php esc_html_e( 'شرح امنیتی', 'abidar-group' ); ?></th>
										<th><?php esc_html_e( 'آدرس IP', 'abidar-group' ); ?></th>
										<th><?php esc_html_e( 'زمان', 'abidar-group' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( Abidar_Security::get_security_logs() as $log ) : ?>
										<tr>
											<td><code><?php echo esc_html( $log['type'] ); ?></code></td>
											<td><?php echo esc_html( $log['details'] ); ?></td>
											<td dir="ltr"><?php echo esc_html( $log['ip'] ); ?></td>
											<td dir="ltr"><?php echo esc_html( $log['time'] ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</section>

				<!-- ================================================================
				     تب ۷: تنظیمات زبان سایت (سه زبان پیش‌فرض + افزودن زبان جدید)
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'languages' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="languages">
					<h2><?php esc_html_e( 'مدیریت سیستم چندزبانه (فارسی، انگلیسی، چینی + افزودن زبان جدید)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'سه زبان پیش‌فرض فعال در هسته قالب', 'abidar-group' ); ?></h3>
							<p><?php esc_html_e( 'زبان‌های فارسی (RTL)، انگلیسی (LTR) و چینی (中文 LTR) به صورت پیش‌فرض با دیکشنری کامل در قالب فعال هستند.', 'abidar-group' ); ?></p>
							<label><?php esc_html_e( 'طرح نمایش انتخاب‌گر زبان (در هدر و ویجت تغییر زبان):', 'abidar-group' ); ?>
								<select name="abidar[lang_switcher_style]">
									<?php
									$lang_styles = array(
										'pills_flag'     => 'طرح ۱: دکمه‌های کپسولی با پرچم و نام',
										'compact_iso'    => 'طرح ۲: کدهای کوتاه استاندارد (FA | EN | ZH)',
										'flags_only'     => 'طرح ۳: فقط پرچم‌ها (مربعی)',
										'flags_round'    => 'طرح ۴: فقط پرچم‌ها (دایره‌ای)',
										'dropdown'       => 'طرح ۵: منوی کشویی حرفه‌ای',
										'dropdown_flags' => 'طرح ۶: منوی کشویی فقط پرچم',
									);
									foreach ( $lang_styles as $style_key => $style_label ) :
										?>
										<option value="<?php echo esc_attr( $style_key ); ?>" <?php selected( $options['lang_switcher_style'], $style_key ); ?>>
											<?php echo esc_html( $style_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>
							<p class="description">
								<?php esc_html_e( 'این طرح هم در نوار بالای هدر سایت و هم در المان «تغییر زبان هوشمند» المنتور اعمال می‌شود. هر المان در ویرایشگر المنتور می‌تواند طرح مستقل خودش را هم داشته باشد.', 'abidar-group' ); ?>
							</p>

							<label><?php esc_html_e( 'زبان پیش‌فرض اولیه سایت:', 'abidar-group' ); ?>
								<select name="abidar[default_language]">
									<?php foreach ( Abidar_I18n::get_languages() as $l_code => $l_info ) : ?>
										<option value="<?php echo esc_attr( $l_code ); ?>" <?php selected( $options['default_language'], $l_code ); ?>>
											<?php echo esc_html( $l_info['flag'] . ' ' . $l_info['native_name'] . ' (' . strtoupper( $l_code ) . ')' ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>
						</div>

						<div class="abidar-admin-card">
							<h3><?php esc_html_e( 'افزودن زبان‌های اضافی (جدا از سه زبان پیش‌فرض)', 'abidar-group' ); ?></h3>
							<p class="description">
								<?php esc_html_e( 'سه زبان فارسی، انگلیسی و چینی همیشه فعال‌اند. هر زبان دیگری که لازم دارید اینجا اضافه کنید؛ کد زبان باید استاندارد باشد (مثلاً ar برای عربی، ru برای روسی، tr برای ترکی).', 'abidar-group' ); ?>
							</p>
							<div id="abidar-custom-languages-list" class="abidar-repeater-list">
								<?php foreach ( $options['custom_languages'] as $l_idx => $custom_lang ) : ?>
									<div class="abidar-repeater-item">
										<div class="abidar-repeater-item__head">
											<strong><?php echo esc_html( ! empty( $custom_lang['native_name'] ) ? $custom_lang['native_name'] : sprintf( 'زبان شماره %d', $l_idx + 1 ) ); ?></strong>
											<button type="button" class="button abidar-repeater-remove" aria-label="<?php esc_attr_e( 'حذف این زبان', 'abidar-group' ); ?>"><?php esc_html_e( 'حذف', 'abidar-group' ); ?></button>
										</div>
										<div class="abidar-repeater-row">
											<input type="text" name="abidar[custom_languages][<?php echo (int) $l_idx; ?>][code]" value="<?php echo esc_attr( $custom_lang['code'] ); ?>" placeholder="کد زبان (مثلاً ar یا ru)" dir="ltr" />
											<input type="text" name="abidar[custom_languages][<?php echo (int) $l_idx; ?>][name]" value="<?php echo esc_attr( $custom_lang['name'] ); ?>" placeholder="نام انگلیسی (Arabic / Russian)" dir="ltr" />
											<input type="text" name="abidar[custom_languages][<?php echo (int) $l_idx; ?>][native_name]" value="<?php echo esc_attr( $custom_lang['native_name'] ); ?>" placeholder="نام بومی (العربية / Русский)" />
											<input type="text" name="abidar[custom_languages][<?php echo (int) $l_idx; ?>][flag]" value="<?php echo esc_attr( $custom_lang['flag'] ); ?>" placeholder="پرچم (🇸🇦 / 🇷🇺)" />
											<select name="abidar[custom_languages][<?php echo (int) $l_idx; ?>][dir]">
												<option value="rtl" <?php selected( $custom_lang['dir'], 'rtl' ); ?>>RTL (راست‌به‌چپ)</option>
												<option value="ltr" <?php selected( $custom_lang['dir'], 'ltr' ); ?>>LTR (چپ‌به‌راست)</option>
											</select>
											<label>
												<input type="checkbox" name="abidar[custom_languages][<?php echo (int) $l_idx; ?>][enabled]" value="1" <?php checked( ! empty( $custom_lang['enabled'] ), true ); ?> />
												<?php esc_html_e( 'فعال', 'abidar-group' ); ?>
											</label>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
							<button type="button" class="button button-primary abidar-repeater-add" id="abidar-add-language-btn" data-target="#abidar-custom-languages-list">
								+ <?php esc_html_e( 'افزودن زبان جدید به سایت', 'abidar-group' ); ?>
							</button>
							<p class="description">
								<?php esc_html_e( 'پس از افزودن، زبان جدید بلافاصله در سوییچر زبان هدر و المان المنتور نمایش داده می‌شود.', 'abidar-group' ); ?>
							</p>
						</div>
					</div>
				</section>

				<!-- ================================================================
				     تب ۸: ساخت خودکار برگه‌های دمو و داده‌های المنتور
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'demo' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="demo">
					<h2><?php esc_html_e( 'ساخت خودکار برگه‌های دمو با صفحه‌ساز المنتور', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-card">
						<p><?php esc_html_e( 'با کلیک روی دکمه زیر، تمامی ۷ برگه اصلی (صفحه اصلی، درباره ما، خدمات، پروژه‌ها، تیم ما، مدارک و اسناد، تماس با ما) در بخش «برگه‌ها»ی وردپرس ایجاد شده و ساختار کامل المان‌های اختصاصی المنتور روی آن‌ها بارگذاری می‌گردد.', 'abidar-group' ); ?></p>
						<button type="button" class="button button-primary button-hero" id="abidar-run-demo-importer">
							<?php esc_html_e( 'ساخت و بازسازی تمام برگه‌های دمو المنتور و محتوای نمونه', 'abidar-group' ); ?>
						</button>
						<div id="abidar-demo-import-status" style="margin-top:16px;"></div>
					</div>
				</section>

				<section class="abidar-admin-panel <?php echo ( 'diagnostics' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="diagnostics">
					<h2><?php esc_html_e( 'عیب‌یابی ویرایشگر المنتور', 'abidar-group' ); ?></h2>
					<?php self::render_diagnostics_panel(); ?>
				</section>

				<div class="abidar-admin-footer">
					<button type="submit" class="button button-primary button-large">
						<?php esc_html_e( 'ذخیره تمامی تنظیمات قالب گروه آبیدر', 'abidar-group' ); ?>
					</button>
				</div>
			</form>
		</div>
		<?php
	}
}

Abidar_Admin_Settings::init();
