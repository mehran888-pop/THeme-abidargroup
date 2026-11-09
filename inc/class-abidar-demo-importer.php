<?php
/**
 * Abidar Group Demo Pages & Elementor Builder Importer (v2.0.0)
 *
 * ساخت خودکار ۷ برگه اصلی وردپرس (صفحه اصلی، درباره ما، خدمات، پروژه‌ها، تیم ما، مدارک و اسناد، تماس با ما)
 * به همراه تزریق ساختار کامل JSON المنتور (_elementor_data) و محتوای دمو برای پست‌تایپ‌ها و مقالات.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Demo_Importer
 */
class Abidar_Demo_Importer {

	/**
	 * راه‌اندازی قلاب ساخت دمو هنگام فعال‌سازی قالب یا درخواست ادمین
	 */
	public static function init() {
		add_action( 'after_switch_theme', array( __CLASS__, 'maybe_install_demo_content' ) );
		add_action( 'wp_ajax_abidar_install_demo', array( __CLASS__, 'ajax_install_demo' ) );
	}

	/**
	 * بررسی وجود یک نوشته با عنوان دقیق (جایگزین امن و به‌روز get_page_by_title منسوخ‌شده)
	 *
	 * @param string $title     عنوان نوشته.
	 * @param string $post_type نوع پست.
	 * @return WP_Post|null
	 */
	public static function find_post_by_title( $title, $post_type = 'post' ) {
		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'title'                  => $title,
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		if ( $query->have_posts() ) {
			return $query->posts[0];
		}

		return null;
	}

	/**
	 * لیست ۷ برگه اصلی مورد نیاز کارفرما که باید از بخش برگه‌های وردپرس با المنتور ساخته شوند
	 *
	 * @return array
	 */
	public static function get_required_pages_blueprint() {
		return array(
			'home'         => array(
				'title_fa'    => 'صفحه اصلی',
				'title_en'    => 'Home - Abidar Group Holding',
				'title_zh'    => '首页 - 阿比达尔控股集团',
				'slug'        => 'home',
				'template'    => 'page-templates/template-home.php',
				'is_front'    => true,
				'widgets'     => array( 'abidar_hero_slider', 'abidar_about_timeline', 'abidar_services', 'abidar_projects', 'abidar_team', 'abidar_certificates', 'abidar_contact_hub' ),
			),
			'about-us'     => array(
				'title_fa'    => 'درباره ما',
				'title_en'    => 'About Abidar Group',
				'title_zh'    => '关于阿比达尔集团',
				'slug'        => 'about-us',
				'template'    => 'page-templates/template-about.php',
				'is_front'    => false,
				'widgets'     => array( 'abidar_about_timeline', 'abidar_team', 'abidar_certificates' ),
			),
			'services'     => array(
				'title_fa'    => 'خدمات',
				'title_en'    => 'Industrial Services',
				'title_zh'    => '核心工业服务',
				'slug'        => 'services',
				'template'    => 'page-templates/template-services.php',
				'is_front'    => false,
				'widgets'     => array( 'abidar_services', 'abidar_contact_hub' ),
			),
			'projects'     => array(
				'title_fa'    => 'پروژه‌ها',
				'title_en'    => 'Mega Projects Portfolio',
				'title_zh'    => '重点工程项目',
				'slug'        => 'projects',
				'template'    => 'page-templates/template-projects.php',
				'is_front'    => false,
				'widgets'     => array( 'abidar_projects', 'abidar_contact_hub' ),
			),
			'our-team'     => array(
				'title_fa'    => 'تیم ما',
				'title_en'    => 'Board & Executive Team',
				'title_zh'    => '董事会与管理团队',
				'slug'        => 'our-team',
				'template'    => 'page-templates/template-team.php',
				'is_front'    => false,
				'widgets'     => array( 'abidar_team' ),
			),
			'certificates' => array(
				'title_fa'    => 'مدارک و اسناد',
				'title_en'    => 'Certificates, ISO & Awards',
				'title_zh'    => '资质证书与国际认证',
				'slug'        => 'certificates',
				'template'    => 'page-templates/template-certificates.php',
				'is_front'    => false,
				'widgets'     => array( 'abidar_certificates' ),
			),
			'contact-us'   => array(
				'title_fa'    => 'تماس با ما',
				'title_en'    => 'Contact Divisions',
				'title_zh'    => '联系各事业部',
				'slug'        => 'contact-us',
				'template'    => 'page-templates/template-contact.php',
				'is_front'    => false,
				'widgets'     => array( 'abidar_contact_hub' ),
			),
		);
	}

	/**
	 * ساخت ساختار استاندارد JSON المنتور (_elementor_data) برای هر برگه
	 *
	 * خروجی دقیقاً منطبق با اسکیمای رسمی Elementor است تا ویرایشگر بدون خطا باز شود:
	 * - هر بخش (section) دارای column با کلید isInner
	 * - هر ویجت با widgetType معتبر و settings قابل‌فهم برای همان ویجت
	 * - شناسه‌ها ۷ کاراکتری هگز و یکتا هستند
	 *
	 * @param string $page_key کلید برگه.
	 * @param array  $widgets  لیست ویجت‌های اختصاصی صفحه.
	 * @return string رشته JSON معتبر المنتور
	 */
	public static function build_elementor_json_for_page( $page_key, $widgets ) {
		$elements = array();
		$index    = 1;

		foreach ( $widgets as $widget_name ) {
			$settings = self::get_elementor_widget_defaults( $page_key, $widget_name );

			$elements[] = array(
				'id'       => substr( md5( $page_key . '_sec_' . $index . wp_rand( 1000, 9999 ) ), 0, 7 ),
				'elType'   => 'section',
				'settings' => array(
					'layout'          => 'full_width',
					'content_width'   => array( 'unit' => 'px', 'size' => 1520 ),
					'gap'             => 'no',
					'stretch_section' => 'section-stretched',
					'background_background' => 'classic',
				),
				'elements' => array(
					array(
						'id'       => substr( md5( $page_key . '_col_' . $index . wp_rand( 1000, 9999 ) ), 0, 7 ),
						'elType'   => 'column',
						'settings' => array(
							'_column_size' => 100,
							'_inline_size' => null,
						),
						'elements' => array(
							array(
								'id'         => substr( md5( $page_key . '_wid_' . $index . wp_rand( 1000, 9999 ) ), 0, 7 ),
								'elType'     => 'widget',
								'widgetType' => $widget_name,
								'settings'   => $settings,
								'elements'   => array(),
							),
						),
						'isInner'  => false,
					),
				),
			);
			$index++;
		}

		return wp_json_encode( $elements );
	}

	/**
	 * مقادیر پیش‌فرض معتبر برای هر ویجت اختصاصی.
	 *
	 * فقط کلیدهایی که واقعاً در register_controls() همان ویجت تعریف شده‌اند
	 * استفاده می‌شوند تا ویرایشگر المنتور بدون هشدار باز شود.
	 *
	 * @param string $page_key    کلید برگه.
	 * @param string $widget_name نام ویجت.
	 * @return array
	 */
	public static function get_elementor_widget_defaults( $page_key, $widget_name ) {
		$layouts = self::get_widget_layout_map();

		$settings = array(
			'layout_style' => isset( $layouts[ $widget_name ][0] ) ? $layouts[ $widget_name ][0] : 'layout_1',
		);

		switch ( $widget_name ) {
			case 'abidar_header_nav':
				$settings = array_merge(
					$settings,
					array(
						'show_topbar'        => 'yes',
						'show_lang_switcher' => 'yes',
						'show_cta_btn'       => 'yes',
					)
				);
				break;

			case 'abidar_services':
				$settings = array_merge(
					$settings,
					array(
						'layout_style'         => 'layout_2',
						'sector_filter'        => 'all',
						'show_layout_switcher' => 'no',
					)
				);
				break;

			case 'abidar_team':
				$settings = array_merge(
					$settings,
					array( 'layout_style' => 'layout_1' )
				);
				break;

			case 'abidar_contact_hub':
				$settings = array_merge(
					$settings,
					array(
						'show_form'    => 'yes',
						'show_offices' => 'yes',
					)
				);
				break;

			case 'abidar_projects':
				$settings = array_merge(
					$settings,
					array( 'show_progress_bar' => 'yes' )
				);
				break;
		}

		return $settings;
	}

	/**
	 * نقشه چیدمان‌های پشتیبانی‌شده هر ویجت (اولین مورد = پیش‌فرض).
	 *
	 * @return array
	 */
	public static function get_widget_layout_map() {
		return array(
			'abidar_header_nav'     => array( 'layout_1', 'layout_2', 'layout_3', 'layout_4' ),
			'abidar_footer_builder' => array( 'layout_1', 'layout_2', 'layout_3' ),
			'abidar_lang_switcher'  => array( 'pills_flag', 'compact_iso', 'dropdown' ),
			'abidar_hero_slider'    => array( 'layout_1', 'layout_2', 'layout_3' ),
			'abidar_about_timeline' => array( 'layout_1', 'layout_2', 'layout_3', 'layout_4' ),
			'abidar_services'       => array( 'layout_1', 'layout_2', 'layout_3', 'layout_4' ),
			'abidar_projects'       => array( 'layout_1', 'layout_2', 'layout_3', 'layout_4' ),
			'abidar_team'           => array( 'layout_1', 'layout_2', 'layout_3', 'layout_4' ),
			'abidar_certificates'   => array( 'layout_1', 'layout_2', 'layout_3' ),
			'abidar_contact_hub'    => array( 'layout_1', 'layout_2', 'layout_3' ),
		);
	}

	/**
	 * نصب خودکار دمو در زمان فعال‌سازی قالب در صورتی که قبلاً نصب نشده باشد
	 */
	public static function maybe_install_demo_content() {
		if ( get_option( 'abidar_demo_installed_v2' ) ) {
			return;
		}
		self::install_full_demo();
	}

	/**
	 * ایجاد کامل برگه‌ها، پست‌تایپ‌ها، مقالات، هدر و فوتر المنتور و تنظیم صفحه اصلی
	 *
	 * @return array گزارش آیتم‌های ایجاد شده
	 */
	public static function install_full_demo() {
		$created_pages = array();
		$blueprints    = self::get_required_pages_blueprint();

		foreach ( $blueprints as $key => $info ) {
			$existing = get_page_by_path( $info['slug'], OBJECT, 'page' );
			if ( $existing ) {
				$page_id = $existing->ID;
			} else {
				$page_id = wp_insert_post(
					array(
						'post_title'   => $info['title_fa'],
						'post_name'    => $info['slug'],
						'post_status'  => 'publish',
						'post_type'    => 'page',
						'post_content' => sprintf( '<!-- Built with Elementor & Abidar Enterprise Widgets (%s) -->', esc_html( $info['title_en'] ) ),
					)
				);
			}

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_wp_page_template', $info['template'] );
				update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
				update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
				update_post_meta( $page_id, '_elementor_version', '3.25.0' );
				update_post_meta( $page_id, '_elementor_data', wp_slash( self::build_elementor_json_for_page( $key, $info['widgets'] ) ) );
				update_post_meta( $page_id, '_abidar_title_en', $info['title_en'] );
				update_post_meta( $page_id, '_abidar_title_zh', $info['title_zh'] );

				if ( ! empty( $info['is_front'] ) ) {
					update_option( 'show_on_front', 'page' );
					update_option( 'page_on_front', $page_id );
				}

				$created_pages[ $key ] = $page_id;
			}
		}

		// ایجاد خدمات نمونه در دیتابیس
		foreach ( Abidar_CPT::get_default_dataset( 'services' ) as $srv ) {
			$exists = self::find_post_by_title( $srv['title_fa'], 'abidar_service' );
			if ( ! $exists ) {
				$post_id = wp_insert_post(
					array(
						'post_title'   => $srv['title_fa'],
						'post_content' => $srv['desc_fa'],
						'post_excerpt' => $srv['desc_fa'],
						'post_status'  => 'publish',
						'post_type'    => 'abidar_service',
					)
				);
				if ( $post_id && ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_abidar_sector', $srv['sector'] );
					update_post_meta( $post_id, '_abidar_icon', $srv['icon'] );
					update_post_meta( $post_id, '_abidar_metric', $srv['metric'] );
					update_post_meta( $post_id, '_abidar_title_en', $srv['title_en'] );
					update_post_meta( $post_id, '_abidar_title_zh', $srv['title_zh'] );
					update_post_meta( $post_id, '_abidar_desc_en', $srv['desc_en'] );
					update_post_meta( $post_id, '_abidar_desc_zh', $srv['desc_zh'] );

					// فیلدهای پنل حرفه‌ای: خلاصه، نکات کلیدی، ویژگی‌ها و مشخصات.
					update_post_meta( $post_id, '_abidar_summary', $srv['desc_fa'] );
					update_post_meta(
						$post_id,
						'_abidar_highlights',
						implode( "\n", isset( $srv['features'] ) ? (array) $srv['features'] : array() )
					);
					update_post_meta( $post_id, '_abidar_features', self::features_to_rows( $srv, 'features' ) );
					update_post_meta( $post_id, '_abidar_specs', self::features_to_rows( $srv, 'features', 'مشخصه' ) );
					update_post_meta( $post_id, '_abidar_show_on_home', '1' );
					update_post_meta( $post_id, '_abidar_gallery', self::sector_gallery( $post_id, $srv ) );
				}
			}
		}

		// ایجاد پروژه‌های نمونه در دیتابیس
		foreach ( Abidar_CPT::get_default_dataset( 'projects' ) as $prj ) {
			$exists = self::find_post_by_title( $prj['title_fa'], 'abidar_project' );
			if ( ! $exists ) {
				$post_id = wp_insert_post(
					array(
						'post_title'   => $prj['title_fa'],
						'post_content' => $prj['desc_fa'],
						'post_excerpt' => $prj['desc_fa'],
						'post_status'  => 'publish',
						'post_type'    => 'abidar_project',
					)
				);
				if ( $post_id && ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_abidar_sector', $prj['sector'] );
					update_post_meta( $post_id, '_abidar_client', $prj['client_fa'] );
					update_post_meta( $post_id, '_abidar_location', $prj['location_fa'] );
					update_post_meta( $post_id, '_abidar_capacity', $prj['capacity'] );
					update_post_meta( $post_id, '_abidar_progress', $prj['progress'] );
					update_post_meta( $post_id, '_abidar_year', $prj['year'] );
					update_post_meta( $post_id, '_abidar_title_en', $prj['title_en'] );
					update_post_meta( $post_id, '_abidar_title_zh', $prj['title_zh'] );

					// فیلدهای پنل حرفه‌ای: خلاصه، دستاوردها، مشخصات فنی و گالری.
					update_post_meta( $post_id, '_abidar_summary', $prj['desc_fa'] );
					update_post_meta( $post_id, '_abidar_badge_text', 'پروژه شاخص هلدینگ' );
					update_post_meta(
						$post_id,
						'_abidar_specs',
						array(
							array( 'label' => 'کارفرما', 'value' => $prj['client_fa'] ),
							array( 'label' => 'موقعیت', 'value' => $prj['location_fa'] ),
							array( 'label' => 'ظرفیت', 'value' => $prj['capacity'] ),
							array( 'label' => 'بازه اجرا', 'value' => (string) $prj['year'] ),
						)
					);
					update_post_meta(
						$post_id,
						'_abidar_achievements',
						array(
							array( 'label' => 'پیشرفت فیزیکی', 'value' => $prj['progress'] . '٪ تا پایان دوره گزارش' ),
							array( 'label' => 'ایمنی کارگاه', 'value' => 'بدون حادثه منجر به توقف کار' ),
							array( 'label' => 'کیفیت اجرا', 'value' => 'مطابق استانداردهای ISO 9001 / 45001' ),
						)
					);
					update_post_meta( $post_id, '_abidar_gallery', self::sector_gallery( $post_id, $prj ) );
				}
			}
		}

		// ایجاد اعضای تیم در دیتابیس
		foreach ( Abidar_CPT::get_default_dataset( 'team' ) as $member ) {
			$exists = self::find_post_by_title( $member['name_fa'], 'abidar_team' );
			if ( ! $exists ) {
				$post_id = wp_insert_post(
					array(
						'post_title'   => $member['name_fa'],
						'post_content' => $member['bio_fa'],
						'post_excerpt' => $member['bio_fa'],
						'post_status'  => 'publish',
						'post_type'    => 'abidar_team',
					)
				);
				if ( $post_id && ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_abidar_role_fa', $member['role_fa'] );
					update_post_meta( $post_id, '_abidar_dept_fa', $member['dept_fa'] );
					update_post_meta( $post_id, '_abidar_dept_en', $member['dept_en'] );
					update_post_meta( $post_id, '_abidar_dept_zh', $member['dept_zh'] );
					update_post_meta( $post_id, '_abidar_role_en', $member['role_en'] );
					update_post_meta( $post_id, '_abidar_role_zh', $member['role_zh'] );
					update_post_meta( $post_id, '_abidar_education', $member['education'] );
					update_post_meta( $post_id, '_abidar_experience', $member['experience'] );
					update_post_meta( $post_id, '_abidar_email', $member['email'] );
					update_post_meta( $post_id, '_abidar_linkedin', $member['linkedin'] );
					update_post_meta( $post_id, '_abidar_title_en', $member['name_en'] );
					update_post_meta( $post_id, '_abidar_title_zh', $member['name_zh'] );

					// فیلدهای پنل حرفه‌ای: خلاصه، حوزه‌های تخصصی و نشان سمت.
					update_post_meta( $post_id, '_abidar_summary', $member['bio_fa'] );
					update_post_meta( $post_id, '_abidar_badge', $member['role_fa'] );
					update_post_meta(
						$post_id,
						'_abidar_expertise',
						array(
							array( 'label' => 'حوزه تخصصی', 'value' => $member['dept_fa'] ),
							array( 'label' => 'تحصیلات', 'value' => $member['education'] ),
							array( 'label' => 'سابقه اجرایی', 'value' => $member['experience'] ),
						)
					);
					self::sector_gallery( $post_id, array( 'sector' => 'energy' ) );
				}
			}
		}

		// ایجاد مدارک و گواهینامه‌ها در دیتابیس
		foreach ( Abidar_CPT::get_default_dataset( 'certificates' ) as $cert ) {
			$exists = self::find_post_by_title( $cert['title_fa'], 'abidar_certificate' );
			if ( ! $exists ) {
				$post_id = wp_insert_post(
					array(
						'post_title'   => $cert['title_fa'],
						'post_content' => $cert['title_fa'],
						'post_status'  => 'publish',
						'post_type'    => 'abidar_certificate',
					)
				);
				if ( $post_id && ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_abidar_cert_issuer', $cert['issuer_fa'] );
					update_post_meta( $post_id, '_abidar_cert_code', $cert['code'] );
					update_post_meta( $post_id, '_abidar_cert_year', $cert['year'] );
					update_post_meta( $post_id, '_abidar_title_en', $cert['title_en'] );
					update_post_meta( $post_id, '_abidar_title_zh', $cert['title_zh'] );

					// فیلدهای پنل حرفه‌ای: خلاصه، نشان و دامنه اعتبار.
					update_post_meta( $post_id, '_abidar_summary', sprintf( '%s — صادرشده توسط %s', $cert['title_fa'], $cert['issuer_fa'] ) );
					update_post_meta( $post_id, '_abidar_badge', $cert['code'] );
					update_post_meta(
						$post_id,
						'_abidar_scope',
						array(
							array( 'label' => 'مرجع صدور', 'value' => $cert['issuer_fa'] ),
							array( 'label' => 'بازه اعتبار', 'value' => (string) $cert['year'] ),
							array( 'label' => 'کد استعلام', 'value' => $cert['code'] ),
						)
					);
					update_post_meta( $post_id, '_abidar_gallery', self::sector_gallery( $post_id, array( 'sector' => 'mining' ) ) );
				}
			}
		}

		// ایجاد مقالات نمونه در بخش نوشته‌های وردپرس
		foreach ( Abidar_CPT::get_default_dataset( 'posts' ) as $article ) {
			$exists = self::find_post_by_title( $article['title_fa'], 'post' );
			if ( ! $exists ) {
				wp_insert_post(
					array(
						'post_title'   => $article['title_fa'],
						'post_excerpt' => $article['excerpt_fa'],
						'post_content' => '<h2>۱. مقدمه و اهمیت راهبردی در هلدینگ گروه آبیدر</h2><p>' . $article['excerpt_fa'] . '</p><h2>۲. فناوری‌های نوین مهندسی در معادن، انرژی و نفت</h2><p>گروه هلدینگ آبیدر با بهره‌گیری از استانداردهای بین‌المللی ISO 9001، ISO 14001 و ISO 29001 توانسته است زنجیره ارزش یکپارچه‌ای از اکتشاف و استخراج تا تولید برق پاک و پالایش میعانات گازی ایجاد نماید.</p><h2>۳. دستاوردها و چشم‌انداز توسعه پایدار ۱۴۰۵-۱۴۱۰</h2><p>توسعه پایانه‌های صادراتی، کاهش ردپای کربن و هوشمندسازی خطوط فرآوری در دستور کار مستمر معاونت‌های تخصصی هلدینگ قرار دارد.</p>',
						'post_status'  => 'publish',
						'post_type'    => 'post',
					)
				);
			}
		}

		update_option( 'abidar_demo_installed_v2', current_time( 'mysql' ) );

		return $created_pages;
	}

	/**
	 * تبدیل فهرست رشته‌ای داده دمو به ردیف‌های ریپیتر (label/value).
	 *
	 * @param array  $item       یک ردیف از مجموعه داده دمو.
	 * @param string $key        کلید فهرست در مجموعه داده.
	 * @param string $label_base پیشوند برچسب (برای ریپیتر مشخصات).
	 * @return array
	 */
	private static function features_to_rows( $item, $key = 'features', $label_base = '' ) {
		if ( empty( $item[ $key ] ) || ! is_array( $item[ $key ] ) ) {
			return array();
		}

		$rows  = array();
		$index = 1;

		foreach ( $item[ $key ] as $feature ) {
			$feature = trim( (string) $feature );

			if ( '' === $feature ) {
				continue;
			}

			if ( '' === $label_base ) {
				$rows[] = array(
					'label' => $feature,
					'value' => '',
				);
			} else {
				/* translators: 1: label prefix, 2: row number */
				$label = sprintf( '%1$s %2$s', $label_base, number_format_i18n( $index ) );

				$rows[] = array(
					'label' => $label,
					'value' => $feature,
				);
			}

			$index++;
		}

		return $rows;
	}

	/**
	 * وارد کردن تصویر قالب به کتابخانه رسانه و بازگرداندن شناسه پیوست.
	 *
	 * تصاویر دمو داخل پوشه assets/images قالب قرار دارند؛ این متد آن‌ها را یک بار
	 * به کتابخانه رسانه وردپرس منتقل می‌کند تا گالری‌ها و تصاویر شاخص واقعی باشند.
	 *
	 * @param string $relative_path مسیر نسبی تصویر، مثل /assets/images/sector-mining.jpg
	 * @param string $title         عنوان پیوست.
	 * @return int شناسه پیوست یا صفر.
	 */
	private static function import_theme_image( $relative_path, $title = '' ) {
		if ( empty( $relative_path ) ) {
			return 0;
		}

		$cache = get_option( 'abidar_demo_attachments', array() );
		$cache = is_array( $cache ) ? $cache : array();

		if ( ! empty( $cache[ $relative_path ] ) && get_post( (int) $cache[ $relative_path ] ) ) {
			return (int) $cache[ $relative_path ];
		}

		$source = ABIDAR_DIR . $relative_path;

		if ( ! file_exists( $source ) ) {
			return 0;
		}

		$upload = wp_upload_bits( basename( $relative_path ), null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( ! empty( $upload['error'] ) ) {
			return 0;
		}

		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => $title ? $title : preg_replace( '/\.[a-z]+$/i', '', basename( $relative_path ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return 0;
		}

		// توابع پردازش تصویر فقط در محیط مدیریتی وردپرس در دسترس هستند.
		if ( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		wp_update_attachment_metadata(
			$attachment_id,
			wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
		);

		$cache[ $relative_path ] = (int) $attachment_id;
		update_option( 'abidar_demo_attachments', $cache );

		return (int) $attachment_id;
	}

	/**
	 * ساخت گالری و تصویر شاخص یک محتوای دمو بر اساس حوزه فعالیت.
	 *
	 * @param int   $post_id شناسه پست.
	 * @param array $item    ردیف مجموعه داده دمو.
	 * @return string شناسه‌های پیوست جداشده با کاما.
	 */
	private static function sector_gallery( $post_id, $item ) {
		$sector = isset( $item['sector'] ) ? $item['sector'] : 'mining';
		$map    = array(
			'mining' => array( '/assets/images/sector-mining.jpg', '/assets/images/sector-energy.jpg', '/assets/images/sector-oil.jpg' ),
			'energy' => array( '/assets/images/sector-energy.jpg', '/assets/images/sector-mining.jpg', '/assets/images/sector-oil.jpg' ),
			'oil'    => array( '/assets/images/sector-oil.jpg', '/assets/images/sector-energy.jpg', '/assets/images/sector-mining.jpg' ),
		);
		$files  = isset( $map[ $sector ] ) ? $map[ $sector ] : $map['mining'];
		$ids    = array();

		foreach ( $files as $file ) {
			$attachment_id = self::import_theme_image( $file, get_the_title( $post_id ) );

			if ( $attachment_id ) {
				$ids[] = $attachment_id;
			}
		}

		if ( $ids && ! has_post_thumbnail( $post_id ) ) {
			set_post_thumbnail( $post_id, $ids[0] );
		}

		return implode( ',', $ids );
	}

	/**
	 * هندلر AJAX برای دکمه «ساخت و بازسازی صفحات دمو المنتور» در پیشخوان وردپرس
	 */
	public static function ajax_install_demo() {
		check_ajax_referer( 'abidar_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'شما دسترسی لازم برای این عملیات را ندارید.', 'abidar-group' ) ), 403 );
		}

		$pages = self::install_full_demo();
		wp_send_json_success(
			array(
				'message' => esc_html__( 'تمامی ۷ برگه اصلی (صفحه اصلی، درباره ما، خدمات، پروژه‌ها، تیم ما، مدارک و اسناد، تماس با ما) به همراه داده‌های المنتور و پست‌تایپ‌ها با موفقیت ایجاد و پیکربندی شدند!', 'abidar-group' ),
				'pages'   => $pages,
			)
		);
	}
}

Abidar_Demo_Importer::init();
