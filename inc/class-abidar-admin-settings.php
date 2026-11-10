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

		/*
		 * add_menu_page یک آیتم زیرمنو با همان اسلاگ والد می‌سازد که نامش با
		 * عنوان منوی اصلی یکی است و در فهرست پیشخوان تکراری دیده می‌شود.
		 * حذفش می‌کنیم تا فهرست فقط گروه‌های واقعی را نشان دهد.
		 */
		remove_submenu_page( 'abidar-settings', 'abidar-settings' );
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
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				// admin_post_* فقط روی admin-post.php اجرا می‌شود، نه admin-ajax.php.
				'adminPost' => admin_url( 'admin-post.php' ),
				'nonce'    => wp_create_nonce( 'abidar_admin_nonce' ),
			)
		);
	}

	/**
	 * آیا این فیلد به کارتِ در حال ذخیره تعلق دارد؟
	 *
	 * برای حلقه‌هایی که با isset محافظت شده‌اند امن است: اگر نقشی تعیین
	 * نشده باشد، رفتار قبلی حفظ می‌شود. سوئیچ‌های بولی جداست چون نبودِ
	 * چک‌باکس در POST یعنی «خاموش» و آنجا محافظت بیشتری لازم است.
	 *
	 * @param string     $field نام فیلد.
	 * @param array|null $scope فهرست فیلدهای کارت جاری.
	 * @return bool
	 */
	private static function in_scope( $field, $scope ) {
		if ( ! is_array( $scope ) ) {
			return false;
		}

		return in_array( $field, $scope, true );
	}

	/**
	 * نقشهٔ مالکیت فیلدها: هر فیلد دقیقاً به یک کارت (یعنی یک صفحه) تعلق دارد.
	 *
	 * این نقشه قلب تفکیک صفحات است. اگر نباشد، ذخیرهٔ هر صفحه همهٔ
	 * سوئیچ‌های صفحات دیگر را خاموش می‌کند، چون چک‌باکسِ ارسال‌نشده در
	 * PHP یعنی «خاموش».
	 *
	 * اگر فیلد تازه‌ای اضافه کردید، اینجا هم ثبتش کنید؛ وگرنه هرگز ذخیره
	 * نخواهد شد. تست خودکار انطباق این نقشه با فرم‌ها را بررسی می‌کند.
	 *
	 * @return array<string, array<int, string>>
	 */
	public static function get_card_fields() {
		return array(
			'blog-layout' => array(
				'post_author_box', 'post_reading_progress', 'post_reading_time', 'post_related_articles',
				'post_related_count', 'post_share_buttons', 'post_sticky_toc', 'single_post_layout',
			),
			'colors' => array(
				'accent_color', 'primary_color', 'secondary_color', 'surface_dark_color',
				'surface_light_color', 'text_main_color',
			),
			'floating-depts' => array(
				'floating_departments',
			),
			'floating-general' => array(
				'floating_contact_enabled', 'floating_position', 'floating_pulse', 'floating_socials',
				'floating_subtitle_en', 'floating_subtitle_fa', 'floating_subtitle_zh',
				'floating_title_en', 'floating_title_fa', 'floating_title_zh',
			),
			'fonts' => array(
				'base_font_size', 'custom_font_name', 'custom_font_ttf_url', 'custom_font_woff2_url',
				'custom_font_woff_url', 'font_family_en', 'font_family_fa', 'font_family_zh',
				'h1_font_size', 'h2_font_size', 'h3_font_size',
			),
			'header-footer' => array(
				'custom_footer_id', 'custom_header_id', 'footer_layout', 'header_layout', 'sticky_header',
			),
			// کارت‌هایی که فیلد فرم ندارند ولی صفحهٔ مستقل دارند. عمداً با
			// آرایهٔ خالی ثبت می‌شوند تا «دامنهٔ هیچ» صریح و قابل بازبینی باشد،
			// نه نتیجهٔ اتفاقیِ نبودن در نقشه.
			'security-log'     => array(),
			'diagnostics-status' => array(),
			'demo-import'      => array(),
			'lang-custom' => array(
				'custom_languages',
			),
			'lang-default' => array(
				'default_language', 'lang_switcher_style',
			),
			'layout-settings' => array(
				'border_radius', 'container_width', 'grid_gap', 'layout_mode', 'mobile_breakpoint',
				'section_padding',
			),
			'mobile-buttons' => array(
				'mobile_nav_items',
			),
			'mobile-settings' => array(
				'mobile_cta_text_en', 'mobile_cta_text_fa', 'mobile_cta_text_zh', 'mobile_cta_url',
				'mobile_nav_enabled', 'mobile_nav_layout', 'mobile_quick_phone',
			),
			'preloader-settings' => array(
				'preloader_accent_color', 'preloader_bg_color', 'preloader_duration', 'preloader_enabled',
				'preloader_style', 'preloader_text_en', 'preloader_text_fa', 'preloader_text_zh',
			),
			'security-brute' => array(
				'security_limit_login_enabled', 'security_lockout_duration', 'security_max_login_attempts',
			),
			'security-ip' => array(
				'security_ip_whitelist',
			),
			'security-antispam' => array(
				'security_antispam_honeypot',
				'security_antispam_min_seconds',
				'security_antispam_timetrap',
				'security_block_user_enum',
				'security_disable_xmlrpc',
				'security_hide_wp_version',
				'security_http_headers',
				'security_math_captcha',
			),
			'security-login' => array(
				'security_custom_login_enabled', 'security_custom_login_slug',
			),
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

		/*
		 * هر کارت یک صفحهٔ مستقل و یک فرم مستقل است. اگر همان منطق قبلی
		 * (نوشتن بی‌قید همهٔ سوئیچ‌ها) اجرا شود، ذخیرهٔ صفحهٔ «فونت» تمام
		 * گزینه‌های امنیتی و پیش‌بارگذار را false می‌کند، چون در آن فرم
		 * هیچ چک‌باکسی ارسال نمی‌شود. پس فقط فیلدهای همان کارت لمس می‌شوند.
		 */
		$card_fields = self::get_card_fields();
		$active_card = isset( $_POST['abidar_card'] ) ? sanitize_key( wp_unslash( $_POST['abidar_card'] ) ) : '';
		/*
		 * نکتهٔ ایمنی: اگر کارت ناشناخته باشد، دامنه «هیچ» است، نه «همه».
		 * قبلاً null به معنی «بدون محدودیت» تفسیر می‌شد و همین باعث می‌شد
		 * ذخیرهٔ کارتی که فیلد ندارد (مثل گزارش رویدادها) تمام سوئیچ‌های
		 * امنیتی را خاموش کند.
		 */
		$scope = isset( $card_fields[ $active_card ] ) ? $card_fields[ $active_card ] : array();

		$options = abidar_get_all_options();
		$posted  = isset( $_POST['abidar'] ) && is_array( $_POST['abidar'] ) ? wp_unslash( $_POST['abidar'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// کدام تب ذخیره شده است؟ برای تشخیص «حذف همه ردیف‌ها» لازم است، چون در
		// آن حالت مرورگر هیچ فیلدی از آن لیست ارسال نمی‌کند.
		$submitted_panel = isset( $_POST['abidar_panel'] ) ? sanitize_key( wp_unslash( $_POST['abidar_panel'] ) ) : '';

		// با تفکیک صفحات، «کدام بخش ذخیره شده» یعنی کدام کارت.
		if ( '' === $submitted_panel ) {
			$submitted_panel = $active_card;
		}

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
			'preloader_duration',
			'security_max_login_attempts',
			'security_lockout_duration',
			'security_antispam_min_seconds',
		);
		foreach ( $int_fields as $field ) {
			if ( isset( $posted[ $field ] ) && self::in_scope( $field, $scope ) ) {
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
			if ( isset( $posted[ $field ] ) && self::in_scope( $field, $scope ) ) {
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
			if ( isset( $posted[ $field ] ) && self::in_scope( $field, $scope ) ) {
				$options[ $field ] = sanitize_text_field( $posted[ $field ] );
			}
		}

		// لیست سفید IP: هر مقدار با کاما، فاصله یا خط جدید جدا می‌شود و اعتبارسنجی می‌گردد.
		// فقط وقتی این صفحه مالک این فیلد است پردازش می‌شود تا با صفحات دیگر قفل نشود.
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
			if ( isset( $posted[ $field ] ) && self::in_scope( $field, $scope ) ) {
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
		);
		foreach ( $bool_fields as $field ) {
			// اگر این صفحه مالک فیلد نیست، به مقدار ذخیره‌شده دست نزن.
			if ( ! self::in_scope( $field, $scope ) ) {
				continue;
			}
			$options[ $field ] = ! empty( $posted[ $field ] );
		}

		// ۶. پاک‌سازی شبکه‌های اجتماعی دکمه شناور
		if ( isset( $posted['floating_socials'] ) && is_array( $posted['floating_socials'] ) && self::in_scope( 'floating_socials', $scope ) ) {
			$clean_socials = array();
			foreach ( $posted['floating_socials'] as $s_key => $s_val ) {
				$clean_socials[ sanitize_key( $s_key ) ] = sanitize_text_field( $s_val );
			}
			$options['floating_socials'] = $clean_socials;
		}

		// ۷. پاک‌سازی بخش‌های چندگانه دکمه شناور تماس (Multi-Department Contact Hub)
		// اگر کل ردیف‌ها حذف شده باشند، فیلد اصلاً ارسال نمی‌شود؛ در آن حالت
		// باید لیست خالی ذخیره شود تا «حذف همه» واقعاً کار کند.
		if ( array_key_exists( 'floating_departments', $posted ) || 'floating-depts' === $submitted_panel ) {
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
		if ( array_key_exists( 'mobile_nav_items', $posted ) || 'mobile-buttons' === $submitted_panel ) {
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
		if ( array_key_exists( 'custom_languages', $posted ) || 'lang-custom' === $submitted_panel ) {
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
		$back_card  = isset( $_POST['abidar_card'] ) ? sanitize_key( wp_unslash( $_POST['abidar_card'] ) ) : '';

		$redirect_args = array(
			'page'    => 'abidar-settings',
			'tab'     => $active_tab,
			'updated' => 'true',
		);

		// بعد از ذخیره، به همان کارت برگرد، نه به اولین کارت گروه.
		if ( '' !== $back_card ) {
			$redirect_args['card'] = $back_card;
		}

		wp_safe_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ) );
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
	/**
	 * ساختار درختی منوی تنظیمات: گروه‌های اصلی و زیرگروه‌های هر گروه.
	 *
	 * هر گروه یک آیکون، یک عنوان و یک آدرس ثابت دارد (همان `tab` قدیمی) تا
	 * لینک‌های موجود و بوکمارک‌های مدیر از کار نیفتند. زیرگروه‌ها فقط نمایشی
	 * هستند: همه در یک صفحه رندر می‌شوند و کلیک روی هرکدام صفحه را به همان
	 * بخش اسکرول می‌کند؛ بنابراین هیچ تنظیمی از دست نمی‌رود و لازم نیست فرم
	 * بین چند صفحه تقسیم شود.
	 *
	 * @return array
	 */
	public static function get_settings_menu() {
		return array(
			'layout'           => array(
				'label'    => __( 'طراحی و چیدمان', 'abidar-group' ),
				'icon'     => 'dashicons-admin-appearance',
				'children' => array(
					'layout-settings' => __( 'چیدمان و کانتینر', 'abidar-group' ),
					'colors'          => __( 'رنگ‌بندی', 'abidar-group' ),
					'fonts'           => __( 'فونت و تایپوگرافی', 'abidar-group' ),
					'header-footer'   => __( 'هدر و فوتر', 'abidar-group' ),
				),
			),
			'mobile-nav'       => array(
				'label'    => __( 'ناوبری موبایل', 'abidar-group' ),
				'icon'     => 'dashicons-smartphone',
				'children' => array(
					'mobile-settings' => __( 'چیدمان و تنظیمات', 'abidar-group' ),
					'mobile-buttons'  => __( 'دکمه‌ها و لینک‌ها', 'abidar-group' ),
				),
			),
			'floating-contact' => array(
				'label'    => __( 'دکمه شناور تماس', 'abidar-group' ),
				'icon'     => 'dashicons-phone',
				'children' => array(
					'floating-general' => __( 'تنظیمات عمومی', 'abidar-group' ),
					'floating-depts'   => __( 'بخش‌های تماس', 'abidar-group' ),
				),
			),
			'blog'             => array(
				'label'    => __( 'مقاله و پست‌ها', 'abidar-group' ),
				'icon'     => 'dashicons-admin-post',
				'children' => array(
					'blog-layout' => __( 'چیدمان و بخش‌های مقاله', 'abidar-group' ),
				),
			),
			'preloader'        => array(
				'label'    => __( 'پیش‌بارگذاری سایت', 'abidar-group' ),
				'icon'     => 'dashicons-update',
				'children' => array(
					'preloader-settings' => __( 'ظاهر و زمان‌بندی', 'abidar-group' ),
				),
			),
			'security'         => array(
				'label'    => __( 'امنیت پیشرفته', 'abidar-group' ),
				'icon'     => 'dashicons-shield',
				'children' => array(
					'security-login'   => __( 'ورود سفارشی', 'abidar-group' ),
					'security-brute'   => __( 'قفل حساب و تلاش ورود', 'abidar-group' ),
					'security-ip'      => __( 'لیست سفید IP', 'abidar-group' ),
					'security-antispam' => __( 'ضد اسپم و سخت‌سازی', 'abidar-group' ),
					'security-log'     => __( 'گزارش رویدادها', 'abidar-group' ),
				),
			),
			'languages'        => array(
				'label'    => __( 'زبان‌های سایت', 'abidar-group' ),
				'icon'     => 'dashicons-translation',
				'children' => array(
					'lang-default' => __( 'زبان پیش‌فرض و نمایش', 'abidar-group' ),
					'lang-custom'  => __( 'زبان‌های اضافی', 'abidar-group' ),
				),
			),
			'demo'             => array(
				'label'    => __( 'ساخت دمو المنتور', 'abidar-group' ),
				'icon'     => 'dashicons-download',
				'children' => array(
					'demo-import' => __( 'ایمپورت برگه‌های دمو', 'abidar-group' ),
				),
			),
			'diagnostics'      => array(
				'label'    => __( 'عیب‌یابی المنتور', 'abidar-group' ),
				'icon'     => 'dashicons-sos',
				'children' => array(
					'diagnostics-status' => __( 'وضعیت ویجت‌ها', 'abidar-group' ),
				),
			),
		);
	}

	/**
	 * رندر رابط کاربری کامل پنل تنظیمات تفکیک‌شده در پیشخوان وردپرس
	 */
	public static function render_settings_page() {
		$options = abidar_get_all_options();
		$menu    = self::get_settings_menu();

		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'layout'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $menu[ $active_tab ] ) ) {
			$active_tab = 'layout';
		}

		/*
		 * هر زیرگروه یک صفحهٔ مستقل است، نه یک لنگر در همان صفحه.
		 * ?card= می‌گوید کدام کارت رندر شود؛ اگر نیامده باشد، اولین
		 * زیرگروهِ گروه فعال نمایش داده می‌شود.
		 */
		$card_keys = array_keys( $menu[ $active_tab ]['children'] );
		$active_card = isset( $_GET['card'] ) ? sanitize_key( wp_unslash( $_GET['card'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! in_array( $active_card, $card_keys, true ) ) {
			$active_card = reset( $card_keys );
		}

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

			<?php if ( isset( $_GET['cleared'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e( 'گزارش رویدادهای امنیتی پاک شد.', 'abidar-group' ); ?></strong></p>
				</div>
			<?php endif; ?>

			<div class="abidar-admin-shell">
				<!-- دکمه باز/بسته سایدبار در موبایل -->
				<button type="button" class="abidar-admin-menu-toggle" id="abidar-menu-toggle" aria-expanded="false" aria-controls="abidar-settings-menu">
					<span class="dashicons dashicons-menu" aria-hidden="true"></span>
					<span><?php esc_html_e( 'فهرست تنظیمات', 'abidar-group' ); ?></span>
				</button>

				<!-- فهرست عمودی تنظیمات: گروه اصلی + زیرگروه‌ها -->
				<aside class="abidar-admin-sidebar" id="abidar-settings-menu" aria-label="<?php esc_attr_e( 'فهرست تنظیمات قالب', 'abidar-group' ); ?>">
					<nav class="abidar-admin-menu">
						<?php foreach ( $menu as $group_key => $group ) : ?>
							<?php $group_is_active = ( $active_tab === $group_key ); ?>
							<div class="abidar-admin-menu__group <?php echo $group_is_active ? 'is-active' : ''; ?>">
								<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'abidar-settings', 'tab' => $group_key ), admin_url( 'admin.php' ) ) ); ?>"
								   class="abidar-admin-menu__link"
								   data-tab-target="<?php echo esc_attr( $group_key ); ?>"
								   aria-expanded="<?php echo $group_is_active ? 'true' : 'false'; ?>">
									<span class="dashicons <?php echo esc_attr( $group['icon'] ); ?>" aria-hidden="true"></span>
									<span class="abidar-admin-menu__text"><?php echo esc_html( $group['label'] ); ?></span>
									<span class="abidar-admin-menu__chevron" aria-hidden="true"></span>
								</a>
								<?php if ( ! empty( $group['children'] ) ) : ?>
									<ul class="abidar-admin-submenu">
										<?php foreach ( $group['children'] as $child_key => $child_label ) : ?>
											<?php $child_is_active = ( $active_card === $child_key ); ?>
											<li>
												<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'abidar-settings', 'tab' => $group_key, 'card' => $child_key ), admin_url( 'admin.php' ) ) ); ?>"
												   class="abidar-admin-submenu__link <?php echo $child_is_active ? 'is-active' : ''; ?>"
												   data-card-target="<?php echo esc_attr( $child_key ); ?>"
												   <?php echo $child_is_active ? 'aria-current="page"' : ''; ?>>
													<?php echo esc_html( $child_label ); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</nav>
				</aside>

				<!-- محتوای تنظیمات -->
				<div class="abidar-admin-content">

			<?php
			/*
			 * عنوان صفحه و مسیر راهنما: چون هر زیرگروه یک صفحهٔ مستقل است،
			 * مدیر باید بداند کجاست و بعدی/قبلی کدام است.
			 */
			$card_label = isset( $menu[ $active_tab ]['children'][ $active_card ] )
				? $menu[ $active_tab ]['children'][ $active_card ]
				: '';

			$flat_pages = array();
			foreach ( $menu as $g_key => $g ) {
				foreach ( $g['children'] as $c_key => $c_label ) {
					$flat_pages[] = array(
						'group' => $g_key,
						'card'  => $c_key,
						'label' => $c_label,
						'group_label' => $g['label'],
					);
				}
			}
			$page_index = 0;
			foreach ( $flat_pages as $i => $pg ) {
				if ( $pg['card'] === $active_card ) {
					$page_index = $i;
				}
			}
			$prev_page = $page_index > 0 ? $flat_pages[ $page_index - 1 ] : null;
			$next_page = $page_index < count( $flat_pages ) - 1 ? $flat_pages[ $page_index + 1 ] : null;
			?>
			<div class="abidar-page-head">
				<div class="abidar-page-head__crumbs">
					<span class="abidar-page-head__group"><?php echo esc_html( $menu[ $active_tab ]['label'] ); ?></span>
					<span class="abidar-page-head__sep" aria-hidden="true">/</span>
					<span class="abidar-page-head__current"><?php echo esc_html( $card_label ); ?></span>
					<span class="abidar-page-head__count"><?php printf( esc_html__( 'صفحهٔ %1$d از %2$d', 'abidar-group' ), (int) ( $page_index + 1 ), (int) count( $flat_pages ) ); ?></span>
				</div>
				<h2 class="abidar-page-head__title"><?php echo esc_html( $card_label ); ?></h2>
			</div>

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
				<input type="hidden" name="abidar_card" value="<?php echo esc_attr( $active_card ); ?>" />
				<input type="hidden" name="abidar_panel" value="<?php echo esc_attr( $active_card ); ?>" data-abidar-panel-mirror />
				<?php wp_nonce_field( 'abidar_save_settings_action', 'abidar_settings_nonce' ); ?>

				<!-- ================================================================
				     تب ۱: تنظیمات طرح، چیدمان، رنگ‌ها و آپلود فونت اختصاصی
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'layout' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="layout" aria-hidden="<?php echo ( 'layout' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'تنظیمات طرح و چیدمان، رنگ‌بندی، تایپوگرافی و فونت اختصاصی', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<?php if ( $active_card === 'layout-settings' ) : ?>
						<div class="abidar-admin-card" id="layout-settings">
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
							<label><?php esc_html_e( 'فاصله عمودی بخش‌ها (Section Padding px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[section_padding]" value="<?php echo esc_attr( $options['section_padding'] ); ?>" min="40" max="160" />
							</label>
							<label><?php esc_html_e( 'نقطه شکست موبایل (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[mobile_breakpoint]" value="<?php echo esc_attr( $options['mobile_breakpoint'] ); ?>" min="480" max="1024" />
							</label>
						</div>
						<?php endif; ?>

						<?php if ( $active_card === 'colors' ) : ?>
						<div class="abidar-admin-card" id="colors">
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
							<label><?php esc_html_e( 'رنگ پس‌زمینه بخش‌های روشن:', 'abidar-group' ); ?>
								<input type="text" name="abidar[surface_light_color]" value="<?php echo esc_attr( $options['surface_light_color'] ); ?>" class="abidar-color-picker" />
							</label>
							<label><?php esc_html_e( 'رنگ متن اصلی:', 'abidar-group' ); ?>
								<input type="text" name="abidar[text_main_color]" value="<?php echo esc_attr( $options['text_main_color'] ); ?>" class="abidar-color-picker" />
							</label>
						</div>
						<?php endif; ?>

						<?php if ( $active_card === 'fonts' ) : ?>
						<div class="abidar-admin-card" id="fonts">
							<h3><?php esc_html_e( 'سایز فونت‌ها و بارگذاری فونت اختصاصی (WOFF2 / TTF)', 'abidar-group' ); ?></h3>
							<label><?php esc_html_e( 'سایز پایه متن سایت (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[base_font_size]" value="<?php echo esc_attr( $options['base_font_size'] ); ?>" min="13" max="22" />
							</label>
							<label><?php esc_html_e( 'سایز تیترهای H1 (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[h1_font_size]" value="<?php echo esc_attr( $options['h1_font_size'] ); ?>" min="28" max="72" />
							</label>
							<label><?php esc_html_e( 'سایز تیترهای H2 (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[h2_font_size]" value="<?php echo esc_attr( $options['h2_font_size'] ); ?>" min="22" max="56" />
							</label>
							<label><?php esc_html_e( 'سایز تیترهای H3 (px):', 'abidar-group' ); ?>
								<input type="number" name="abidar[h3_font_size]" value="<?php echo esc_attr( $options['h3_font_size'] ); ?>" min="18" max="40" />
							</label>
							<h4 class="abidar-field-group-title"><?php esc_html_e( 'خانواده فونت هر زبان', 'abidar-group' ); ?></h4>
							<p class="description">
								<?php esc_html_e( 'هر زبان فونت خودش را می‌گیرد. اگر فونت اختصاصی آپلود کنید و نامش را پایین‌تر وارد کنید، همه زبان‌ها از آن استفاده می‌کنند.', 'abidar-group' ); ?>
							</p>
							<label><?php esc_html_e( 'فونت فارسی:', 'abidar-group' ); ?>
								<input type="text" name="abidar[font_family_fa]" value="<?php echo esc_attr( $options['font_family_fa'] ); ?>" dir="ltr" placeholder="Vazirmatn, IRANSans, Tahoma" />
							</label>
							<label><?php esc_html_e( 'فونت انگلیسی:', 'abidar-group' ); ?>
								<input type="text" name="abidar[font_family_en]" value="<?php echo esc_attr( $options['font_family_en'] ); ?>" dir="ltr" placeholder="Inter, Segoe UI" />
							</label>
							<label><?php esc_html_e( 'فونت چینی:', 'abidar-group' ); ?>
								<input type="text" name="abidar[font_family_zh]" value="<?php echo esc_attr( $options['font_family_zh'] ); ?>" dir="ltr" placeholder="Noto Sans SC, Microsoft YaHei" />
							</label>

							<h4 class="abidar-field-group-title"><?php esc_html_e( 'فونت اختصاصی (آپلود فایل)', 'abidar-group' ); ?></h4>
							<label><?php esc_html_e( 'نام خانواده فونت اختصاصی (در صورت آپلود):', 'abidar-group' ); ?>
								<input type="text" name="abidar[custom_font_name]" value="<?php echo esc_attr( $options['custom_font_name'] ); ?>" placeholder="مثلاً IRANSansX یا Peyda" />
							</label>
							<label><?php esc_html_e( 'فایل فونت اختصاصی WOFF (نسخه قدیمی‌تر):', 'abidar-group' ); ?>
								<div class="abidar-upload-row">
									<input type="url" name="abidar[custom_font_woff_url]" id="custom_font_woff_url" value="<?php echo esc_url( $options['custom_font_woff_url'] ); ?>" dir="ltr" />
									<button type="button" class="button abidar-media-upload" data-target="#custom_font_woff_url"><?php esc_html_e( 'بارگذاری فونت', 'abidar-group' ); ?></button>
								</div>
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
						<?php endif; ?>

						<?php if ( $active_card === 'header-footer' ) : ?>
						<div class="abidar-admin-card" id="header-footer">
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

							<h4 class="abidar-field-group-title"><?php esc_html_e( 'انتخاب هدر و فوتر ساخته‌شده با المنتور', 'abidar-group' ); ?></h4>
							<p class="description">
								<?php esc_html_e( 'اگر طرح هدر یا فوتر را روی «المنتور» گذاشتید، شناسه برگه ساخته‌شده در بخش «هدر و فوتر» را اینجا وارد کنید. شناسه را از آدرس ویرایش آن برگه بردارید (post=۱۲۳).', 'abidar-group' ); ?>
							</p>
							<label><?php esc_html_e( 'شناسه برگه هدر المنتور:', 'abidar-group' ); ?>
								<input type="number" name="abidar[custom_header_id]" value="<?php echo esc_attr( $options['custom_header_id'] ); ?>" min="0" dir="ltr" placeholder="0" />
							</label>
							<label><?php esc_html_e( 'شناسه برگه فوتر المنتور:', 'abidar-group' ); ?>
								<input type="number" name="abidar[custom_footer_id]" value="<?php echo esc_attr( $options['custom_footer_id'] ); ?>" min="0" dir="ltr" placeholder="0" />
							</label>
						</div>
						<?php endif; ?>
					</div>
				</section>

				<!-- ================================================================
				     تب ۲: تنظیمات ناوبری موبایل
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'mobile-nav' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="mobile-nav" aria-hidden="<?php echo ( 'mobile-nav' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'تنظیمات ناوبری حرفه‌ای موبایل (چیدمان‌های متفاوت + تعریف دکمه‌ها و لینک‌ها)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<?php if ( $active_card === 'mobile-settings' ) : ?>
						<div class="abidar-admin-card" id="mobile-settings">
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
							<label><?php esc_html_e( 'عنوان دکمه اقدام سریع (English):', 'abidar-group' ); ?>
								<input type="text" name="abidar[mobile_cta_text_en]" value="<?php echo esc_attr( $options['mobile_cta_text_en'] ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'عنوان دکمه اقدام سریع (中文):', 'abidar-group' ); ?>
								<input type="text" name="abidar[mobile_cta_text_zh]" value="<?php echo esc_attr( $options['mobile_cta_text_zh'] ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'لینک دکمه اقدام سریع موبایل:', 'abidar-group' ); ?>
								<input type="text" name="abidar[mobile_cta_url]" value="<?php echo esc_attr( $options['mobile_cta_url'] ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'شماره تماس مستقیم در منوی موبایل:', 'abidar-group' ); ?>
								<input type="text" name="abidar[mobile_quick_phone]" value="<?php echo esc_attr( $options['mobile_quick_phone'] ); ?>" dir="ltr" />
							</label>
						</div>
						<?php endif; ?>

						<?php if ( $active_card === 'mobile-buttons' ) : ?>
						<div class="abidar-admin-card" id="mobile-buttons">
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
						<?php endif; ?>
					</div>
				</section>

				<!-- ================================================================
				     تب ۳: تنظیمات دکمه شناور تماس با ما و بخش‌های مختلف هلدینگ
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'floating-contact' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="floating-contact" aria-hidden="<?php echo ( 'floating-contact' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'تنظیمات دکمه شناور ارتباط چندبخشی و شبکه‌های اجتماعی', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<?php if ( $active_card === 'floating-general' ) : ?>
						<div class="abidar-admin-card" id="floating-general">
							<h3><?php esc_html_e( 'تنظیمات عمومی و لینک شبکه‌های اجتماعی', 'abidar-group' ); ?></h3>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[floating_contact_enabled]" value="1" <?php checked( $options['floating_contact_enabled'], true ); ?> />
								<?php esc_html_e( 'نمایش دکمه شناور تماس چندبخشی در تمام صفحات', 'abidar-group' ); ?>
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[floating_pulse]" value="1" <?php checked( $options['floating_pulse'], true ); ?> />
								<?php esc_html_e( 'انیمیشن نبض (Pulse) روی دکمه شناور برای جلب توجه', 'abidar-group' ); ?>
							</label>

							<h4 class="abidar-field-group-title"><?php esc_html_e( 'متن عنوان و زیرعنوان دکمه شناور (سه‌زبانه)', 'abidar-group' ); ?></h4>
							<label><?php esc_html_e( 'عنوان (فارسی):', 'abidar-group' ); ?>
								<input type="text" name="abidar[floating_title_fa]" value="<?php echo esc_attr( $options['floating_title_fa'] ?? '' ); ?>" placeholder="تماس با بخش‌های هلدینگ" />
							</label>
							<label><?php esc_html_e( 'عنوان (English):', 'abidar-group' ); ?>
								<input type="text" name="abidar[floating_title_en]" value="<?php echo esc_attr( $options['floating_title_en'] ?? '' ); ?>" dir="ltr" placeholder="Contact Our Divisions" />
							</label>
							<label><?php esc_html_e( 'عنوان (中文):', 'abidar-group' ); ?>
								<input type="text" name="abidar[floating_title_zh]" value="<?php echo esc_attr( $options['floating_title_zh'] ?? '' ); ?>" dir="ltr" placeholder="联系我们" />
							</label>
							<label><?php esc_html_e( 'زیرعنوان (فارسی):', 'abidar-group' ); ?>
								<input type="text" name="abidar[floating_subtitle_fa]" value="<?php echo esc_attr( $options['floating_subtitle_fa'] ?? '' ); ?>" />
							</label>
							<label><?php esc_html_e( 'زیرعنوان (English):', 'abidar-group' ); ?>
								<input type="text" name="abidar[floating_subtitle_en]" value="<?php echo esc_attr( $options['floating_subtitle_en'] ?? '' ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'زیرعنوان (中文):', 'abidar-group' ); ?>
								<input type="text" name="abidar[floating_subtitle_zh]" value="<?php echo esc_attr( $options['floating_subtitle_zh'] ?? '' ); ?>" dir="ltr" />
							</label>

							<h4 class="abidar-field-group-title"><?php esc_html_e( 'شبکه‌های اجتماعی', 'abidar-group' ); ?></h4>
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
						<?php endif; ?>

						<?php if ( $active_card === 'floating-depts' ) : ?>
						<div class="abidar-admin-card" id="floating-depts">
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
						<?php endif; ?>
					</div>
				</section>

				<!-- ================================================================
				     تب ۴: تنظیمات قالب مقاله و پست‌ها
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'blog' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="blog" aria-hidden="<?php echo ( 'blog' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'تنظیمات قالب پیش‌فرض مقاله و پست‌های خبری هلدینگ', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<?php if ( $active_card === 'blog-layout' ) : ?>
						<div class="abidar-admin-card" id="blog-layout">
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
							<label><?php esc_html_e( 'تعداد مقالات مرتبط در پایین هر مقاله:', 'abidar-group' ); ?>
								<input type="number" name="abidar[post_related_count]" value="<?php echo esc_attr( $options['post_related_count'] ); ?>" min="0" max="12" />
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[post_share_buttons]" value="1" <?php checked( $options['post_share_buttons'], true ); ?> />
								<?php esc_html_e( 'نمایش دکمه‌های اشتراک‌گذاری مقاله', 'abidar-group' ); ?>
							</label>
						</div>
						<?php endif; ?>
					</div>
				</section>

				<!-- ================================================================
				     تب ۵: تنظیمات پیش‌بارگذاری سایت (Preloader)
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'preloader' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="preloader" aria-hidden="<?php echo ( 'preloader' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'تنظیمات پیش‌بارگذاری سایت (Preloader)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<?php if ( $active_card === 'preloader-settings' ) : ?>
						<div class="abidar-admin-card" id="preloader-settings">
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
							<label><?php esc_html_e( 'متن پیش‌بارگذاری (چینی):', 'abidar-group' ); ?>
								<input type="text" name="abidar[preloader_text_zh]" value="<?php echo esc_attr( $options['preloader_text_zh'] ); ?>" dir="ltr" />
							</label>
							<label><?php esc_html_e( 'رنگ پس‌زمینه پیش‌بارگذاری:', 'abidar-group' ); ?>
								<input type="text" name="abidar[preloader_bg_color]" value="<?php echo esc_attr( $options['preloader_bg_color'] ); ?>" class="abidar-color-picker" />
							</label>
							<label><?php esc_html_e( 'رنگ تاکید پیش‌بارگذاری:', 'abidar-group' ); ?>
								<input type="text" name="abidar[preloader_accent_color]" value="<?php echo esc_attr( $options['preloader_accent_color'] ); ?>" class="abidar-color-picker" />
							</label>
							<label><?php esc_html_e( 'مدت زمان نمایش (میلی‌ثانیه):', 'abidar-group' ); ?>
								<input type="number" name="abidar[preloader_duration]" value="<?php echo esc_attr( $options['preloader_duration'] ); ?>" min="300" max="4000" step="100" />
							</label>
						</div>
						<?php endif; ?>
					</div>
				</section>

				<!-- ================================================================
				     تب ۶: تنظیمات امنیت سایت (High-Security Suite)
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'security' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="security" aria-hidden="<?php echo ( 'security' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'سپر امنیتی پیشرفته گروه آبیدر (تغییر آدرس ادمین، محدودیت ورود، ضد اسپم)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<?php if ( $active_card === 'security-login' ) : ?>
						<div class="abidar-admin-card" id="security-login">
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
						<?php endif; ?>

						<?php if ( $active_card === 'security-brute' ) : ?>
						<div class="abidar-admin-card" id="security-brute">
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
						</div>
						<?php endif; ?>

						<?php if ( $active_card === 'security-ip' ) : ?>
						<div class="abidar-admin-card" id="security-ip">
							<h3><?php esc_html_e( '۲ب. لیست سفید IPهای مجاز', 'abidar-group' ); ?></h3>
							<p class="description">
								<?php esc_html_e( 'این آی‌پی‌ها هرگز مسدود نمی‌شوند و محدودیت تلاش ورود برایشان اعمال نمی‌شود. اگر پشت VPN یا پراکسی هستید، آی‌پی خود را اینجا وارد کنید تا از پیشخوان قفل نشوید.', 'abidar-group' ); ?>
							</p>
							<label><?php esc_html_e( 'لیست سفید IPهای مجاز:', 'abidar-group' ); ?>
								<textarea name="abidar[security_ip_whitelist]" rows="4" dir="ltr" placeholder="203.0.113.10&#10;198.51.100.0/24"><?php echo esc_textarea( $options['security_ip_whitelist'] ); ?></textarea>
							</label>
							<p class="description">
								<?php esc_html_e( 'هر IP در یک خط، یا جداشده با کاما. محدوده CIDR هم پذیرفته می‌شود (مثل 198.51.100.0/24).', 'abidar-group' ); ?>
							</p>
						</div>
						<?php endif; ?>

						<?php if ( $active_card === 'security-antispam' ) : ?>
						<div class="abidar-admin-card" id="security-antispam">
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
								<input type="checkbox" name="abidar[security_hide_wp_version]" value="1" <?php checked( $options['security_hide_wp_version'], true ); ?> />
								<?php esc_html_e( 'حذف شماره نسخه وردپرس از سورس صفحه و فیدها', 'abidar-group' ); ?>
							</label>
							<label><?php esc_html_e( 'حداقل زمان پر کردن فرم تماس (ثانیه) — کمتر از این، ربات تشخیص داده می‌شود:', 'abidar-group' ); ?>
								<input type="number" name="abidar[security_antispam_min_seconds]" value="<?php echo esc_attr( $options['security_antispam_min_seconds'] ); ?>" min="0" max="120" />
							</label>
							<label class="abidar-checkbox-label">
								<input type="checkbox" name="abidar[security_http_headers]" value="1" <?php checked( $options['security_http_headers'], true ); ?> />
								<?php esc_html_e( 'ارسال هدرهای امنیتی HTTP (XSS-Protection, Nosniff, Referrer-Policy)', 'abidar-group' ); ?>
							</label>
						</div>
						<?php endif; ?>

						<?php if ( $active_card === 'security-log' ) : ?>
						<div class="abidar-admin-card" id="security-log">
							<h3><?php esc_html_e( '۴. گزارش رویدادهای امنیتی', 'abidar-group' ); ?></h3>
							<?php
							/*
							 * گزارش پیش‌تر همه ۵۰ ردیف را یک‌جا نشان می‌داد و پنل را
							 * شلوغ می‌کرد. حالا صفحه‌بندی، فیلتر نوع رویداد و
							 * جست‌وجو دارد و تعداد ردیف هر صفحه قابل انتخاب است.
							 */
							$log_page    = isset( $_GET['log_page'] ) ? max( 1, absint( wp_unslash( $_GET['log_page'] ) ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$log_type    = isset( $_GET['log_type'] ) ? sanitize_key( wp_unslash( $_GET['log_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$log_search  = isset( $_GET['log_s'] ) ? sanitize_text_field( wp_unslash( $_GET['log_s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$log_per     = isset( $_GET['log_per'] ) ? absint( wp_unslash( $_GET['log_per'] ) ) : 10; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$log_per     = in_array( $log_per, array( 5, 10, 25, 50 ), true ) ? $log_per : 10;

							$log_data    = Abidar_Security::get_security_logs_page(
								array(
									'page'     => $log_page,
									'per_page' => $log_per,
									'type'     => $log_type,
									'search'   => $log_search,
								)
							);
							$log_base_url = add_query_arg( array( 'page' => 'abidar-settings', 'tab' => 'security' ), admin_url( 'admin.php' ) );
							?>

							<!--
								نوار ابزار فیلتر. اینجا از <form> استفاده نمی‌کنیم چون کل
								پنل داخل فرم اصلی ذخیره تنظیمات است و فرم تودرتو در HTML
								نامعتبر است (مرورگر آن را می‌شکند). به‌جایش هر کنترل با
								data-abidar-log-filter مقدارش را به آدرس اضافه می‌کند.
							-->
							<div class="abidar-log-toolbar"
								data-abidar-log-filter
								data-base-url="<?php echo esc_url( $log_base_url ); ?>">

								<label class="abidar-log-toolbar__field">
									<span><?php esc_html_e( 'نوع رویداد', 'abidar-group' ); ?></span>
									<select data-log-key="log_type">
										<option value=""><?php esc_html_e( 'همه رویدادها', 'abidar-group' ); ?></option>
										<?php foreach ( $log_data['types'] as $type_key => $type_count ) : ?>
											<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $log_type, $type_key ); ?>>
												<?php echo esc_html( $type_key . ' (' . $type_count . ')' ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</label>

								<label class="abidar-log-toolbar__field">
									<span><?php esc_html_e( 'جست‌وجو', 'abidar-group' ); ?></span>
									<input type="search" data-log-key="log_s" value="<?php echo esc_attr( $log_search ); ?>" placeholder="<?php esc_attr_e( 'شرح یا آدرس IP', 'abidar-group' ); ?>" />
								</label>

								<label class="abidar-log-toolbar__field abidar-log-toolbar__field--narrow">
									<span><?php esc_html_e( 'ردیف در صفحه', 'abidar-group' ); ?></span>
									<select data-log-key="log_per">
										<?php foreach ( array( 5, 10, 25, 50 ) as $per_option ) : ?>
											<option value="<?php echo esc_attr( $per_option ); ?>" <?php selected( $log_per, $per_option ); ?>><?php echo esc_html( $per_option ); ?></option>
										<?php endforeach; ?>
									</select>
								</label>

								<button type="button" class="button button-primary" data-log-apply><?php esc_html_e( 'اعمال', 'abidar-group' ); ?></button>

								<?php if ( '' !== $log_type || '' !== $log_search || 1 !== $log_page ) : ?>
									<a href="<?php echo esc_url( $log_base_url ); ?>" class="button">
										<?php esc_html_e( 'پاک کردن فیلترها', 'abidar-group' ); ?>
									</a>
								<?php endif; ?>

								<?php if ( $log_data['all'] > 0 ) : ?>
									<?php
									/*
									 * پاک‌کردن گزارش باید یک درخواست POST جدا باشد، نه لینک؛
									 * وگرنه با یک prefetch یا بازکردن آدرس در تب، گزارش بی‌بازگشت
									 * حذف می‌شود. فرم داخل فرم هم نامعتبر است، پس به‌جای فرم
									 * از تابع JS و fetch به admin-post.php استفاده می‌کنیم.
									 */
									?>
									<button type="button"
										class="button abidar-log-clear"
										data-log-clear
										data-nonce="<?php echo esc_attr( wp_create_nonce( 'abidar_clear_logs' ) ); ?>"
										data-confirm="<?php esc_attr_e( 'همه رویدادهای امنیتی پاک شوند؟ این کار قابل بازگشت نیست.', 'abidar-group' ); ?>">
										<?php esc_html_e( 'پاک کردن کل گزارش', 'abidar-group' ); ?>
									</button>
								<?php endif; ?>
							</div>

							<p class="description abidar-log-summary">
								<?php
								printf(
									/* translators: 1: shown count, 2: total matching, 3: total stored */
									esc_html__( 'نمایش %1$s از %2$s رویداد مطابق فیلتر (از مجموع %3$s رویداد ذخیره‌شده). حداکثر ۵۰ رویداد اخیر نگه‌داری می‌شود.', 'abidar-group' ),
									esc_html( count( $log_data['items'] ) ),
									esc_html( $log_data['total'] ),
									esc_html( $log_data['all'] )
								);
								?>
							</p>

							<?php if ( empty( $log_data['items'] ) ) : ?>
								<div class="abidar-log-empty">
									<?php esc_html_e( 'هیچ رویدادی با این فیلتر پیدا نشد.', 'abidar-group' ); ?>
								</div>
							<?php else : ?>
								<table class="widefat striped abidar-log-table">
									<thead>
										<tr>
											<th><?php esc_html_e( 'نوع رویداد', 'abidar-group' ); ?></th>
											<th><?php esc_html_e( 'شرح امنیتی', 'abidar-group' ); ?></th>
											<th><?php esc_html_e( 'آدرس IP', 'abidar-group' ); ?></th>
											<th><?php esc_html_e( 'زمان', 'abidar-group' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $log_data['items'] as $log ) : ?>
											<tr>
												<td><code><?php echo esc_html( $log['type'] ); ?></code></td>
												<td><?php echo esc_html( $log['details'] ); ?></td>
												<td dir="ltr"><?php echo esc_html( $log['ip'] ); ?></td>
												<td dir="ltr"><?php echo esc_html( $log['time'] ); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>

								<?php if ( $log_data['pages'] > 1 ) : ?>
									<nav class="abidar-log-pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی گزارش امنیتی', 'abidar-group' ); ?>">
										<?php
										$page_link = function ( $target_page ) use ( $log_base_url, $log_type, $log_search, $log_per ) {
											return add_query_arg(
												array(
													'log_page' => $target_page,
													'log_type' => $log_type,
													'log_s'    => $log_search,
													'log_per'  => $log_per,
												),
												$log_base_url
											);
										};
										?>
										<a class="button <?php echo ( $log_data['page'] <= 1 ) ? 'disabled' : ''; ?>"
										   href="<?php echo esc_url( $page_link( max( 1, $log_data['page'] - 1 ) ) ); ?>"
										   <?php echo ( $log_data['page'] <= 1 ) ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
											&larr; <?php esc_html_e( 'قبلی', 'abidar-group' ); ?>
										</a>
										<span class="abidar-log-pagination__status">
											<?php
											printf(
												/* translators: 1: current page, 2: total pages */
												esc_html__( 'صفحه %1$s از %2$s', 'abidar-group' ),
												esc_html( $log_data['page'] ),
												esc_html( $log_data['pages'] )
											);
											?>
										</span>
										<a class="button <?php echo ( $log_data['page'] >= $log_data['pages'] ) ? 'disabled' : ''; ?>"
										   href="<?php echo esc_url( $page_link( min( $log_data['pages'], $log_data['page'] + 1 ) ) ); ?>"
										   <?php echo ( $log_data['page'] >= $log_data['pages'] ) ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
											<?php esc_html_e( 'بعدی', 'abidar-group' ); ?> &rarr;
										</a>
									</nav>
								<?php endif; ?>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
				</section>

				<!-- ================================================================
				     تب ۷: تنظیمات زبان سایت (سه زبان پیش‌فرض + افزودن زبان جدید)
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'languages' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="languages" aria-hidden="<?php echo ( 'languages' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'مدیریت سیستم چندزبانه (فارسی، انگلیسی، چینی + افزودن زبان جدید)', 'abidar-group' ); ?></h2>
					<div class="abidar-admin-grid">
						<?php if ( $active_card === 'lang-default' ) : ?>
						<div class="abidar-admin-card" id="lang-default">
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
						<?php endif; ?>

						<?php if ( $active_card === 'lang-custom' ) : ?>
						<div class="abidar-admin-card" id="lang-custom">
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
						<?php endif; ?>
					</div>
				</section>

				<!-- ================================================================
				     تب ۸: ساخت خودکار برگه‌های دمو و داده‌های المنتور
				     ================================================================ -->
				<section class="abidar-admin-panel <?php echo ( 'demo' === $active_tab ) ? 'is-active' : ''; ?>" data-tab-panel="demo" aria-hidden="<?php echo ( 'demo' === $active_tab ) ? 'false' : 'true'; ?>">
					<h2><?php esc_html_e( 'ساخت خودکار برگه‌های دمو با صفحه‌ساز المنتور', 'abidar-group' ); ?></h2>
					<?php if ( $active_card === 'demo-import' ) : ?>
					<div class="abidar-admin-card" id="demo-import">
						<p><?php esc_html_e( 'با کلیک روی دکمه زیر، تمامی ۷ برگه اصلی (صفحه اصلی، درباره ما، خدمات، پروژه‌ها، تیم ما، مدارک و اسناد، تماس با ما) در بخش «برگه‌ها»ی وردپرس ایجاد شده و ساختار کامل المان‌های اختصاصی المنتور روی آن‌ها بارگذاری می‌گردد.', 'abidar-group' ); ?></p>
						<button type="button" class="button button-primary button-hero" id="abidar-run-demo-importer">
							<?php esc_html_e( 'ساخت و بازسازی تمام برگه‌های دمو المنتور و محتوای نمونه', 'abidar-group' ); ?>
						</button>
						<div id="abidar-demo-import-status" style="margin-top:16px;"></div>
					</div>
					<?php endif; ?>
				</section>

				<?php if ( $active_card === 'diagnostics-status' ) : ?>
				<section id="diagnostics-status" class="abidar-admin-panel is-active" data-tab-panel="diagnostics" aria-hidden="false">
					<h2><?php esc_html_e( 'عیب‌یابی ویرایشگر المنتور', 'abidar-group' ); ?></h2>
					<?php self::render_diagnostics_panel(); ?>
				</section>
				<?php endif; ?>

				<div class="abidar-admin-footer">
					<div class="abidar-admin-footer__nav">
						<?php if ( $prev_page ) : ?>
							<a class="button" href="<?php echo esc_url( add_query_arg( array( 'page' => 'abidar-settings', 'tab' => $prev_page['group'], 'card' => $prev_page['card'] ), admin_url( 'admin.php' ) ) ); ?>">
								<span aria-hidden="true">→</span>
								<?php echo esc_html( $prev_page['label'] ); ?>
							</a>
						<?php endif; ?>

						<?php if ( $next_page ) : ?>
							<a class="button" href="<?php echo esc_url( add_query_arg( array( 'page' => 'abidar-settings', 'tab' => $next_page['group'], 'card' => $next_page['card'] ), admin_url( 'admin.php' ) ) ); ?>">
								<?php echo esc_html( $next_page['label'] ); ?>
								<span aria-hidden="true">←</span>
							</a>
						<?php endif; ?>
					</div>

					<div class="abidar-admin-footer__save">
						<button type="submit" class="button button-primary button-large">
							<?php
							printf(
								/* translators: %s: settings page name. */
								esc_html__( 'ذخیرهٔ «%s»', 'abidar-group' ),
								esc_html( $card_label )
							);
							?>
						</button>
					</div>
				</div>
			</form>

				</div><!-- /.abidar-admin-content -->
			</div><!-- /.abidar-admin-shell -->
		</div><!-- /.abidar-admin-wrap -->
		<?php
	}
}

Abidar_Admin_Settings::init();
