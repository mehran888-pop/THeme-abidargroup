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
	 * تا کاربر بتواند هر صفحه را مستقیماً در ویرایشگر المنتور رایگان باز و ویرایش کند.
	 *
	 * @param string $page_key کلید برگه.
	 * @param array  $widgets  لیست ویجت‌های اختصاصی صفحه.
	 * @return string رشته JSON معتبر المنتور
	 */
	public static function build_elementor_json_for_page( $page_key, $widgets ) {
		$elements = array();
		$index    = 1;

		foreach ( $widgets as $widget_name ) {
			$settings = array(
				'layout_style' => 'layout_1',
				'sector_filter'=> 'all',
				'posts_count'  => 6,
				'show_filter'  => 'yes',
			);

			if ( 'services' === $page_key && 'abidar_services' === $widget_name ) {
				$settings['layout_style'] = 'layout_2';
			}
			if ( 'projects' === $page_key && 'abidar_projects' === $widget_name ) {
				$settings['layout_style'] = 'layout_1';
			}

			$elements[] = array(
				'id'       => substr( md5( $page_key . '_sec_' . $index ), 0, 7 ),
				'elType'   => 'section',
				'settings' => array(
					'layout'        => 'full_width',
					'stretch_section' => 'section-stretched',
				),
				'elements' => array(
					array(
						'id'       => substr( md5( $page_key . '_col_' . $index ), 0, 7 ),
						'elType'   => 'column',
						'settings' => array( '_column_size' => 100 ),
						'elements' => array(
							array(
								'id'         => substr( md5( $page_key . '_wid_' . $index ), 0, 7 ),
								'elType'     => 'widget',
								'widgetType' => $widget_name,
								'settings'   => $settings,
								'elements'   => array(),
							),
						),
					),
				),
			);
			$index++;
		}

		return wp_json_encode( $elements );
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
