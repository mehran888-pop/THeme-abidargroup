<?php
/**
 * Abidar Group Holding Theme Functions and Definitions
 *
 * قالب اختصاصی هلدینگ گروه آبیدر (معادن، انرژی، نفت)
 * طراحی‌شده بر اساس آخرین استانداردهای کدنویسی وردپرس (WPCS) و المنتور
 *
 * @package Abidar_Group
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم به فایل جهت امنیت بالا
}

/**
 * ثوابت اصلی قالب (شروع از نسخه 2.0.0 طبق درخواست کارفرما)
 */
define( 'ABIDAR_VERSION', '2.4.1' );
define( 'ABIDAR_DIR', get_template_directory() );
define( 'ABIDAR_URI', get_template_directory_uri() );
define( 'ABIDAR_OPTION_KEY', 'abidar_theme_options_v2' );

/**
 * دریافت تنظیمات پیش‌فرض قالب گروه آبیدر
 *
 * @return array آرایه کامل تنظیمات پیش‌فرض
 */
function abidar_get_default_options() {
	return array(
		// ۱. تنظیمات طرح و چیدمان (Layout & Design)
		'container_width'          => 1320,
		'layout_mode'              => 'wide', // wide | boxed | fullwidth
		'grid_gap'                 => 28,
		'section_padding'          => 96,
		'primary_color'            => '#D97706', // طلایی صنعتی آبیدر
		'secondary_color'          => '#0B192C', // سرمه‌ای نفتی تیره
		'accent_color'             => '#0EA5E9', // آبی انرژی پاک
		'surface_dark_color'       => '#07101E',
		'surface_light_color'      => '#F8FAFC',
		'text_main_color'          => '#0F172A',
		'border_radius'            => 14,
		'font_family_fa'           => 'Vazirmatn',
		'font_family_en'           => 'Inter',
		'font_family_zh'           => 'Noto Sans SC',
		'base_font_size'           => 16,
		'h1_font_size'             => 46,
		'h2_font_size'             => 34,
		'h3_font_size'             => 24,
		'custom_font_name'         => '',
		'custom_font_woff2_url'    => '',
		'custom_font_woff_url'     => '',
		'custom_font_ttf_url'      => '',
		'header_layout'            => 'layout_1', // layout_1 | layout_2 | layout_3 | layout_4 | elementor_custom
		'custom_header_id'         => 0,
		'sticky_header'            => true,
		'footer_layout'            => 'layout_1', // layout_1 | layout_2 | layout_3 | elementor_custom
		'custom_footer_id'         => 0,

		// ۲. تنظیمات ناوبری موبایل (Mobile Navigation)
		'mobile_nav_enabled'       => true,
		'mobile_nav_layout'        => 'bottom_app_bar', // bottom_app_bar | fullscreen_overlay | slide_drawer | floating_dock
		'mobile_breakpoint'        => 992,
		'mobile_cta_text_fa'       => 'استعلام پروژه و همکاری',
		'mobile_cta_text_en'       => 'Project Inquiry',
		'mobile_cta_text_zh'       => '项目合作咨询',
		'mobile_cta_url'           => '#contact-section',
		'mobile_quick_phone'       => '+982188001122',
		'mobile_nav_items'         => array(
			array(
				'title_fa' => 'خانه',
				'title_en' => 'Home',
				'title_zh' => '首页',
				'url'      => '/',
				'icon'     => 'home',
				'badge'    => '',
			),
			array(
				'title_fa' => 'خدمات',
				'title_en' => 'Services',
				'title_zh' => '核心业务',
				'url'      => '/services/',
				'icon'     => 'layers',
				'badge'    => '3',
			),
			array(
				'title_fa' => 'پروژه‌ها',
				'title_en' => 'Projects',
				'title_zh' => '重点工程',
				'url'      => '/projects/',
				'icon'     => 'briefcase',
				'badge'    => 'جدید',
			),
			array(
				'title_fa' => 'مدارک',
				'title_en' => 'Certificates',
				'title_zh' => '资质荣誉',
				'url'      => '/certificates/',
				'icon'     => 'award',
				'badge'    => 'ISO',
			),
			array(
				'title_fa' => 'تماس',
				'title_en' => 'Contact',
				'title_zh' => '联系我们',
				'url'      => '/contact-us/',
				'icon'     => 'phone',
				'badge'    => '',
			),
		),

		// ۳. تنظیمات دکمه شناور تماس با ما و ارتباط چندبخشی (Floating Contact Widget)
		'floating_contact_enabled' => true,
		'floating_position'        => 'bottom-left', // bottom-left | bottom-right
		'floating_pulse'           => true,
		'floating_title_fa'        => 'مرکز ارتباط مستقیم هلدینگ آبیدر',
		'floating_title_en'        => 'Abidar Group Direct Contact Hub',
		'floating_title_zh'        => '阿比达尔集团直线联系中心',
		'floating_subtitle_fa'     => 'بخش مورد نظر خود را جهت ارتباط مستقیم انتخاب نمایید:',
		'floating_subtitle_en'     => 'Select a department for direct communication:',
		'floating_subtitle_zh'     => '请选择您要联系的业务部门：',
		'floating_socials'         => array(
			'linkedin'  => 'https://linkedin.com/company/abidar-group',
			'telegram'  => 'https://t.me/abidargroup',
			'whatsapp'  => 'https://wa.me/989120001122',
			'instagram' => 'https://instagram.com/abidargroup',
			'wechat'    => 'AbidarGroup_CN',
			'email'     => 'info@abidargroup.com',
		),
		'floating_departments'     => array(
			array(
				'id'         => 'dept_mining',
				'name_fa'    => 'معاونت معادن و صنایع معدنی',
				'name_en'    => 'Mining & Mineral Processing Division',
				'name_zh'    => '矿业与矿物加工事业部',
				'role_fa'    => 'اکتشاف، استخراج و فرآوری سنگ‌آهن و مس',
				'role_en'    => 'Exploration, Extraction & Copper/Iron Processing',
				'role_zh'    => '勘探、开采及铜铁矿深加工',
				'phone'      => '+98-21-88104001',
				'ext'        => '101',
				'whatsapp'   => 'https://wa.me/989121110101',
				'email'      => 'mining@abidargroup.com',
				'hours_fa'   => 'شنبه تا چهارشنبه ۸:۰۰ الی ۱۷:۰۰',
				'hours_en'   => 'Sat - Wed, 08:00 - 17:00',
				'hours_zh'   => '周六至周三 08:00 - 17:00',
				'icon'       => 'pickaxe',
				'badge_color'=> '#D97706',
			),
			array(
				'id'         => 'dept_energy',
				'name_fa'    => 'معاونت انرژی و نیروگاه‌های سیکل ترکیبی',
				'name_en'    => 'Energy & Power Generation Division',
				'name_zh'    => '清洁能源与联合循环发电事业部',
				'role_fa'    => 'نیروگاه‌های خورشیدی، بادی و سیکل ترکیبی',
				'role_en'    => 'Solar, Wind & Combined-Cycle Power Plants',
				'role_zh'    => '光伏、风电及燃气联合循环电站',
				'phone'      => '+98-21-88104002',
				'ext'        => '202',
				'whatsapp'   => 'https://wa.me/989121110202',
				'email'      => 'energy@abidargroup.com',
				'hours_fa'   => 'پشتیبانی ۲۴/۷ بهره‌برداری شبکه',
				'hours_en'   => '24/7 Grid Operations Support',
				'hours_zh'   => '24/7 电网运营支持',
				'icon'       => 'bolt',
				'badge_color'=> '#0EA5E9',
			),
			array(
				'id'         => 'dept_oil',
				'name_fa'    => 'معاونت نفت، گاز و پتروشیمی',
				'name_en'    => 'Oil, Gas & Petrochemical Division',
				'name_zh'    => '石油、天然气与石化事业部',
				'role_fa'    => 'پروژه‌های بالادستی، پالایشگاهی و صادرات مشتقات',
				'role_en'    => 'Upstream, Refinery EPC & Derivatives Export',
				'role_zh'    => '上游开发、炼化EPC及衍生品出口',
				'phone'      => '+98-21-88104003',
				'ext'        => '303',
				'whatsapp'   => 'https://wa.me/989121110303',
				'email'      => 'oilgas@abidargroup.com',
				'hours_fa'   => 'شنبه تا چهارشنبه ۸:۰۰ الی ۱۸:۰۰',
				'hours_en'   => 'Sat - Wed, 08:00 - 18:00',
				'hours_zh'   => '周六至周三 08:00 - 18:00',
				'icon'       => 'flame',
				'badge_color'=> '#10B981',
			),
			array(
				'id'         => 'dept_intl',
				'name_fa'    => 'امور بین‌الملل، سرمایه‌گذاری و سهامداران',
				'name_en'    => 'International Trade & Investor Relations',
				'name_zh'    => '国际贸易与投资者关系部',
				'role_fa'    => 'قراردادهای فراملی (تهران، دبی، شانگهای)',
				'role_en'    => 'Global Contracts (Tehran, Dubai, Shanghai)',
				'role_zh'    => '跨国合作（德黑兰、迪拜、上海）',
				'phone'      => '+98-21-88104004',
				'ext'        => '404',
				'whatsapp'   => 'https://wa.me/989121110404',
				'email'      => 'investors@abidargroup.com',
				'hours_fa'   => 'پاسخگویی به زبان‌های فارسی، انگلیسی و چینی',
				'hours_en'   => 'Support in Persian, English & Chinese',
				'hours_zh'   => '支持波斯语、英语和中文服务',
				'icon'       => 'globe',
				'badge_color'=> '#8B5CF6',
			),
		),

		// ۴. تنظیمات قالب مقاله و پست‌ها (Blog & Single Article Template)
		'single_post_layout'       => 'executive_magazine', // executive_magazine | fullwidth_industrial | classic_sidebar
		'post_reading_progress'    => true,
		'post_reading_time'        => true,
		'post_sticky_toc'          => true,
		'post_author_box'          => true,
		'post_share_buttons'       => true,
		'post_related_articles'    => true,
		'post_related_count'       => 3,

		// ۵. تنظیمات پیش‌بارگذاری سایت (Preloader Settings)
		'preloader_enabled'        => true,
		'preloader_style'          => 'industrial_pulse', // industrial_pulse | minimal_gold | rig_svg | curtain_split
		'preloader_text_fa'        => 'گروه هلدینگ آبیدر | معادن • انرژی • نفت',
		'preloader_text_en'        => 'ABIDAR GROUP HOLDING | MINING • ENERGY • OIL',
		'preloader_text_zh'        => '阿比达尔控股集团 | 矿业 • 能源 • 石油',
		'preloader_bg_color'       => '#07101E',
		'preloader_accent_color'   => '#D97706',
		'preloader_duration'       => 900,

		// ۶. تنظیمات امنیت سایت (High-Security Suite)
		'security_custom_login_enabled' => false, // در صورت فعال‌سازی، آدرس ورود تغییر می‌کند
		'security_custom_login_slug'    => 'abidar-secure-gateway',
		'security_limit_login_enabled'  => true,
		'security_max_login_attempts'   => 4,
		'security_lockout_duration'     => 30, // دقیقه
		'security_ip_whitelist'         => '127.0.0.1',
		'security_antispam_honeypot'    => true,
		'security_antispam_timetrap'    => true,
		'security_antispam_min_seconds' => 3,
		'security_math_captcha'         => true,
		'security_disable_xmlrpc'       => true,
		'security_block_user_enum'      => true,
		'security_hide_wp_version'      => true,
		'security_http_headers'         => true,

		// ۷. تنظیمات زبان سایت (Multilingual & Custom Languages)
		'default_language'         => 'fa',
		'lang_switcher_style'      => 'pills_flag', // pills_flag | compact_iso | flags_only | flags_round | dropdown | dropdown_flags
		'custom_languages'         => array(
			// امکان افزودن زبان‌های جدید علاوه بر فارسی، انگلیسی و چینی
			array(
				'code'        => 'ar',
				'name'        => 'Arabic',
				'native_name' => 'العربية',
				'flag'        => '🇸🇦',
				'dir'         => 'rtl',
				'enabled'     => true,
				'translations'=> array(
					'holding_name'      => 'مجموعة آبيدر القابضة',
					'holding_tagline'   => 'التعدين • الطاقة • النفط والغاز',
					'nav_home'          => 'الرئيسية',
					'nav_about'         => 'من نحن',
					'nav_services'      => 'خدماتنا',
					'nav_projects'      => 'المشاريع',
					'nav_team'          => 'فريق الإدارة',
					'nav_certificates'  => 'الشهادات والوثائق',
					'nav_blog'          => 'المقالات والأخبار',
					'nav_contact'       => 'اتصل بنا',
					'hero_title'        => 'ريادة الصناعات الاستراتيجية في التعدين والطاقة والنفط',
					'hero_subtitle'     => 'مجموعة آبيدر القابضة تجمع بين الهندسة المتقدمة والاستثمار المستدام لتطوير البنية التحتية الصناعية.',
					'cta_explore'       => 'استكشف المشاريع',
					'cta_contact'       => 'تواصل مع الأقسام',
				),
			),
		),
	);
}

/**
 * دریافت تمامی تنظیمات ذخیره شده قالب به همراه مقادیر پیش‌فرض
 *
 * @return array
 */
function abidar_get_all_options() {
	$defaults = abidar_get_default_options();
	$saved    = get_option( ABIDAR_OPTION_KEY, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return wp_parse_args( $saved, $defaults );
}

/**
 * دریافت یک کلید خاص از تنظیمات قالب
 *
 * @param string $key     کلید تنظیمات.
 * @param mixed  $default مقدار پیش‌فرض در صورت عدم وجود.
 * @return mixed
 */
function abidar_get_option( $key, $default = null ) {
	$options = abidar_get_all_options();
	if ( isset( $options[ $key ] ) ) {
		return $options[ $key ];
	}
	return $default;
}

/**
 * دریافت آدرس برگه‌ی سایتی که با «نمونه‌ساز خودکار» ساخته می‌شود.
 *
 * این تابع به جای ساختن لینک‌های حدسی، «پیوند یکتای» واقعی برگه‌ی وردپرس را برمی‌گرداند
 * و در صورت نبودِ برگه، به آدرس امن جایگزین (صفحه اصلی یا آرشیو) بازمی‌گردد تا هرگز
 * پیوند ۴۰۴ در منوها و ویجت‌های المنتور نمایش داده نشود.
 *
 * @param string $slug اسلاگ برگه (مثلاً services یا contact-us).
 * @param string $fallback آدرس جایگزین در صورت پیدا نشدن برگه.
 * @return string آدرس کامل امن.
 */
function abidar_page_url( $slug, $fallback = '' ) {
	$slug = sanitize_title( $slug );

	// کش سبک استاتیک برای جلوگیری از اجرای مکرر کوئری در منوها و ویجت‌ها.
	static $cache = array();

	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	// برگه «مقالات» با عنوان blog در واقع صفحه نوشته‌ها (page_for_posts) است.
	if ( 'blog' === $slug ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		if ( $posts_page > 0 ) {
			$cache[ $slug ] = get_permalink( $posts_page );
			return $cache[ $slug ];
		}
	}

	$page = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		$url = get_permalink( $page->ID );
	} else {
		$url = ! empty( $fallback ) ? $fallback : home_url( '/' );
	}

	$cache[ $slug ] = $url;

	return $cache[ $slug ];
}

/**
 * راه‌اندازی ویژگی‌های هسته قالب وردپرس (Theme Setup)
 */
function abidar_theme_setup() {
	// پشتیبانی از فایل‌های ترجمه استاندارد وردپرس
	load_theme_textdomain( 'abidar-group', ABIDAR_DIR . '/languages' );

	// پشتیبانی از تگ عنوان پویا، تصاویر شاخص و فیدهای RSS
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );

	// تعریف سایزهای اختصاصی تصاویر برای پروژه‌ها، خدمات، تیم و مدارک
	add_image_size( 'abidar-hero', 1920, 1080, true );
	add_image_size( 'abidar-project-card', 800, 560, true );
	add_image_size( 'abidar-team-portrait', 600, 720, true );
	add_image_size( 'abidar-certificate', 900, 1200, false );

	// پشتیبانی از لوگوی سفارشی
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 90,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// پشتیبانی از مارکاپ استاندارد HTML5
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// پشتیبانی کامل از صفحه‌ساز المنتور (Elementor Free & Pro)
	add_theme_support( 'elementor' );
	add_theme_support( 'header-footer-elementor' );

	// ثبت فهرست‌های ناوبری وردپرس
	register_nav_menus(
		array(
			'primary_menu' => esc_html__( 'فهرست اصلی هدر (Primary Header Menu)', 'abidar-group' ),
			'mobile_menu'  => esc_html__( 'فهرست اختصاصی موبایل (Mobile Navigation Menu)', 'abidar-group' ),
			'footer_col_1' => esc_html__( 'فوتر - دسترسی سریع (Footer Quick Links)', 'abidar-group' ),
			'footer_col_2' => esc_html__( 'فوتر - شرکت‌ها و معاونت‌ها (Footer Divisions)', 'abidar-group' ),
			'topbar_menu'  => esc_html__( 'نوار بالای هدر (Top Bar Menu)', 'abidar-group' ),
		)
	);
}
add_action( 'after_setup_theme', 'abidar_theme_setup' );

/**
 * ثبت سایدبارها و نواحی ابزارک وردپرس
 */
function abidar_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'سایدبار مقالات و اخبار هلدینگ', 'abidar-group' ),
			'id'            => 'sidebar-blog',
			'description'   => esc_html__( 'ابزارک‌های نمایش داده شده در قالب کلاسیک مقالات و آرشیو اخبار گروه آبیدر.', 'abidar-group' ),
			'before_widget' => '<div id="%1$s" class="abidar-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="abidar-widget__title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'سایدبار خدمات و پروژه‌ها', 'abidar-group' ),
			'id'            => 'sidebar-industrial',
			'description'   => esc_html__( 'ناحیه ابزارک اختصاصی صفحات خدمات معادن، انرژی و نفت.', 'abidar-group' ),
			'before_widget' => '<div id="%1$s" class="abidar-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="abidar-widget__title">',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'abidar_widgets_init' );

/**
 * فراخوانی فایل‌های استایل و جاوااسکریپت در بخش کاربری (Frontend)
 */
function abidar_enqueue_scripts() {
	/**
	 * امکان غیرفعال‌سازی فونت‌های آنلاین گوگل (برای سایت‌های آفلاین یا با میزبانی محلی فونت).
	 *
	 * @param bool $load آیا فونت‌های گوگل بارگذاری شوند.
	 */
	$load_google_fonts = apply_filters( 'abidar_load_google_fonts', true );

	// در بوم ویرایشگر/پیش‌نمایش المنتور، فونت آنلاین بارگذاری نمی‌شود.
	// درخواست مسدود یا کند به fonts.googleapis.com باعث می‌شود iframe پیش‌نمایش
	// هرگز رویداد load را اجرا نکند و نشانگر بارگذاری المنتور بی‌پایان بماند.
	if ( class_exists( 'Abidar_Elementor_Init' ) && Abidar_Elementor_Init::is_elementor_editor_context() ) {
		$load_google_fonts = false;
	}

	if ( $load_google_fonts ) {
		// فونت‌های چندزبانه (وزیرمتن فارسی، اینتر انگلیسی و نوتو چینی)
		wp_enqueue_style(
			'abidar-google-fonts',
			'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+SC:wght@400;500;700&family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap',
			array(),
			null
		);
	}

	// استایل اصلی قالب (style.css)
	wp_enqueue_style(
		'abidar-style',
		get_stylesheet_uri(),
		array(),
		ABIDAR_VERSION
	);

	// استایل جامع المان‌ها، صفحات و انیمیشن‌ها (assets/css/main.css)
	wp_enqueue_style(
		'abidar-main',
		ABIDAR_URI . '/assets/css/main.css',
		array( 'abidar-style' ),
		ABIDAR_VERSION
	);

	// در صورت فعال بودن زبان راست‌به‌چپ (فارسی یا عربی)، بارگذاری استایل RTL
	if ( Abidar_I18n::is_rtl() ) {
		wp_enqueue_style(
			'abidar-rtl',
			ABIDAR_URI . '/rtl.css',
			array( 'abidar-main' ),
			ABIDAR_VERSION
		);
	}

	// تزریق متغیرهای CSS پویا از تنظیمات پیشخوان وردپرس
	$dynamic_css = Abidar_Dynamic_CSS::generate();
	wp_add_inline_style( 'abidar-main', $dynamic_css );

	// اسکریپت اصلی فرانت‌اند قالب
	wp_enqueue_script(
		'abidar-main-js',
		ABIDAR_URI . '/assets/js/main.js',
		array(),
		ABIDAR_VERSION,
		true
	);

	// ارسال اطلاعات امن و چندزبانه به جاوااسکریپت
	wp_localize_script(
		'abidar-main-js',
		'AbidarConfig',
		array(
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
			'nonce'             => wp_create_nonce( 'abidar_frontend_nonce' ),
			'currentLang'       => Abidar_I18n::get_current_lang(),
			'isRtl'             => Abidar_I18n::is_rtl(),
			'preloaderDuration' => (int) abidar_get_option( 'preloader_duration', 900 ),
			'themeVersion'      => ABIDAR_VERSION,
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'abidar_enqueue_scripts' );

/**
 * بارگذاری غیرمسدودکننده (Non-blocking) فونت‌های آنلاین گوگل.
 *
 * استایل فونت با media="print" درج و پس از بارگذاری به all تبدیل می‌شود؛ بنابراین
 * اگر دسترسی به fonts.googleapis.com کند یا مسدود باشد، رندر صفحه و بوم المنتور
 * هرگز پشت این درخواست قفل نمی‌شود.
 *
 * @param string $tag    تگ HTML استایل.
 * @param string $handle نام هندل استایل.
 * @param string $href   آدرس فایل استایل.
 * @return string
 */
function abidar_async_google_fonts( $tag, $handle, $href ) {
	if ( 'abidar-google-fonts' !== $handle ) {
		return $tag;
	}

	return sprintf(
		'<link rel="stylesheet" id="%1$s-css" href="%2$s" media="print" onload="this.media=\'all\'" />' . "\n",
		esc_attr( $handle ),
		esc_url( $href )
	);
}
add_filter( 'style_loader_tag', 'abidar_async_google_fonts', 10, 3 );

/**
 * بارگذاری ماژول‌های تفکیک‌شده قالب گروه آبیدر
 */
require_once ABIDAR_DIR . '/inc/class-abidar-i18n.php';
require_once ABIDAR_DIR . '/inc/class-abidar-cpt.php';
require_once ABIDAR_DIR . '/inc/class-abidar-cpt-editor.php';
require_once ABIDAR_DIR . '/inc/class-abidar-security.php';
require_once ABIDAR_DIR . '/inc/class-abidar-dynamic-css.php';
require_once ABIDAR_DIR . '/inc/class-abidar-preloader.php';
require_once ABIDAR_DIR . '/inc/class-abidar-mobile-nav.php';
require_once ABIDAR_DIR . '/inc/class-abidar-floating-contact.php';
require_once ABIDAR_DIR . '/inc/class-abidar-demo-importer.php';
require_once ABIDAR_DIR . '/inc/class-abidar-admin-settings.php';
require_once ABIDAR_DIR . '/elementor/class-abidar-elementor-init.php';

/**
 * افزودن کلاس‌های کمکی به تگ body بر اساس زبان، چیدمان و وضعیت هدر
 *
 * @param array $classes کلاس‌های فعلی body.
 * @return array
 */
function abidar_body_classes( $classes ) {
	// کلاس‌های قالب فقط روی فرانت‌اند معنا دارند.
	if ( is_admin() ) {
		return $classes;
	}

	$lang         = Abidar_I18n::get_current_lang();
	$is_rtl       = Abidar_I18n::is_rtl();
	$layout_mode  = abidar_get_option( 'layout_mode', 'wide' );
	$header_style = abidar_get_option( 'header_layout', 'layout_1' );

	$classes[] = 'abidar-theme-v2';
	$classes[] = 'abidar-lang-' . sanitize_html_class( $lang );
	$classes[] = $is_rtl ? 'abidar-dir-rtl rtl' : 'abidar-dir-ltr ltr';
	$classes[] = 'abidar-layout-' . sanitize_html_class( $layout_mode );
	$classes[] = 'abidar-header-' . sanitize_html_class( $header_style );

	if ( abidar_get_option( 'sticky_header', true ) ) {
		$classes[] = 'abidar-has-sticky-header';
	}

	return $classes;
}
add_filter( 'body_class', 'abidar_body_classes' );

/**
 * تنظیم ویژگی‌های lang و dir روی تگ html بر اساس زبان انتخاب‌شده کاربر
 *
 * نکته مهم: قلاب `language_attributes` فقط مخصوص فرانت‌اند نیست. هسته وردپرس
 * همان تابع را در `wp-admin/admin-header.php` هم صدا می‌زند. اگر این فیلتر در
 * پیشخوان هم اجرا شود، انتخاب زبان سایت توسط بازدیدکننده (مثلاً انگلیسی) تگ
 * `<html>` کل پنل مدیریت را به `dir="ltr"` تغییر می‌دهد و کل پیشخوان — که
 * راست‌به‌چپ طراحی شده — به هم می‌ریزد. پس در پیشخوان دست نمی‌زنیم و اجازه
 * می‌دهیم وردپرس از زبان واقعی کاربر (`is_rtl()`) استفاده کند.
 *
 * @param string $output ویژگی‌های پیش‌فرض وردپرس.
 * @return string
 */
function abidar_language_attributes( $output ) {
	// پیشخوان، صفحه ورود و درخواست‌های AJAX: رفتار پیش‌فرض وردپرس حفظ می‌شود.
	if ( is_admin() || wp_doing_ajax() ) {
		return $output;
	}

	$lang   = Abidar_I18n::get_current_lang();
	$is_rtl = Abidar_I18n::is_rtl();
	$dir    = $is_rtl ? 'rtl' : 'ltr';

	return sprintf( 'lang="%s" dir="%s"', esc_attr( $lang ), esc_attr( $dir ) );
}
add_filter( 'language_attributes', 'abidar_language_attributes', 20 );

/**
 * آیا صفحه‌ی جاری توسط المنتور ساخته شده است؟
 *
 * اگر برگه با المنتور ویرایش شده باشد، قالب باید فضای کامل را در اختیار المنتور بگذارد
 * و هدر/فوتر اختصاصی هلدینگ را رندر کند.
 *
 * @param int|null $post_id شناسه نوشته؛ در صورت خالی بودن از نوشته جاری استفاده می‌شود.
 * @return bool
 */
function abidar_is_elementor_page( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		return false;
	}

	$edit_mode = get_post_meta( $post_id, '_elementor_edit_mode', true );
	$data      = get_post_meta( $post_id, '_elementor_data', true );

	if ( 'builder' === $edit_mode && ! empty( $data ) ) {
		return true;
	}

	/**
	 * حالت لب‌های بحرانی: برگه‌ای که «تازه» ساخته شده و هنوز هیچ داده‌ای در
	 * `_elementor_data` ندارد.
	 *
	 * در این لحظه `_elementor_edit_mode` معمولاً ست نشده است، بنابراین شرط بالا
	 * false می‌شود و قالب شاخهٔ «بدون المنتور» را رندر می‌کند؛ یعنی
	 * `the_content()` هرگز فرا خوانده نمی‌شود. المنتور برای اتصال سند خود به
	 * بوم پیش‌نمایش به `<div class="elementor elementor-{id}">` که همان
	 * `the_content()` تولید می‌کند نیاز دارد؛ بدون آن پیام پایان بارگذاری
	 * هرگز به پنل نمی‌رسد و ویرایشگر روی «در حال بارگذاری» می‌ماند.
	 *
	 * پس در بوم/پیش‌نمایش المنتور همیشه مسیر محتوا را باز می‌گذاریم.
	 */
	if ( class_exists( 'Abidar_Elementor_Init' ) && Abidar_Elementor_Init::is_elementor_editor_context() ) {
		return true;
	}

	// سند المنتور در حال ساخت است ولی پیش‌نمایش هنوز ست نشده.
	if ( class_exists( '\Elementor\Plugin' ) && 'builder' === $edit_mode ) {
		return true;
	}

	return false;
}

/**
 * تضمین رندر محتوای المنتور در بوم ویرایشگر.
 *
 * قالب‌های اختصاصی این پوسته (و همچنین `front-page.php`) بر اساس
 * `abidar_is_elementor_page()` تصمیم می‌گیرند محتوای المنتور را رندر کنند یا
 * دموی پیش‌فرض را. اما المنتور برای اتصال سند به iframe پیش‌نمایش، به
 * `<div class="elementor elementor-{id}">` که از `the_content()` می‌آید نیاز
 * دارد. اگر به هر دلیلی آن شرط برقرار نباشد، بوم خالی می‌ماند و نشانگر
 * بارگذاری هرگز پایان نمی‌یابد.
 *
 * این متد با قلاب `loop_start` (که قبل از هر `while ( have_posts() )` اجرا
 * می‌شود) محتوا را دقیقاً یک‌بار تضمین می‌کند. اگر قالب خودش `the_content()`
 * را صدا زده باشد، پرچم خروجی تولیدشده روی false برمی‌گردد و چیزی تکرار
 * نمی‌شود.
 */
function abidar_ensure_elementor_canvas() {
	static $done = false;

	if ( $done ) {
		return;
	}

	if ( ! class_exists( 'Abidar_Elementor_Init' ) || ! Abidar_Elementor_Init::is_elementor_editor_context() ) {
		return;
	}

	// فقط روی اسناد قابل ویرایش با المنتور.
	if ( ! is_singular() ) {
		return;
	}

	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}

	global $post;
	if ( ! $post instanceof WP_Post || (int) $post->ID !== (int) $post_id ) {
		return;
	}

	// خروجی قبلاً تولید شده است.
	if ( false !== strpos( (string) $post->post_content, 'elementor elementor-' ) ) {
		return;
	}

	$done = true;

	echo '<div class="abidar-elementor-content">';
	the_content();
	echo '</div>';
}
add_action( 'loop_start', 'abidar_ensure_elementor_canvas', 1 );

/**
 * تولید عنوان صفحه (Hero Header) برای صفحات داخلی و آرشیوها
 *
 * @param array $args آرگومان‌های قابل تنظیم عنوان، زیرعنوان و تصویر پس‌زمینه.
 */
function abidar_page_hero( $args = array() ) {
	$defaults = array(
		'title'    => abidar_clean_archive_title(),
		'subtitle' => '',
		'eyebrow'  => abidar_t( 'holding_tagline' ),
		'image'    => ABIDAR_URI . '/assets/images/sector-mining.jpg',
		'crumb'    => '',
	);

	$args = wp_parse_args( $args, $defaults );
	?>
	<section class="abidar-page-hero" style="background-image: url('<?php echo esc_url( $args['image'] ); ?>');">
		<div class="abidar-page-hero__overlay"></div>
		<div class="abidar-container abidar-page-hero__inner">
			<span class="abidar-section__eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
			<h1 class="abidar-page-hero__title"><?php echo wp_kses_post( $args['title'] ); ?></h1>
			<?php if ( ! empty( $args['subtitle'] ) ) : ?>
				<p class="abidar-page-hero__subtitle"><?php echo esc_html( $args['subtitle'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $args['crumb'] ) ) : ?>
				<nav class="abidar-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهبری صفحه', 'abidar-group' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<span class="abidar-breadcrumb__current"><?php echo wp_kses_post( $args['crumb'] ); ?></span>
				</nav>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * قالب پیش‌فرض کارت مقاله (استفاده مشترک در آرشیو، جستجو و مقالات مرتبط)
 *
 * @param int|null $post_id شناسه مقاله.
 */
function abidar_article_card( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms   = get_the_category( $post_id );
	$term    = ! empty( $terms ) ? $terms[0] : null;
	?>
	<article id="post-<?php echo (int) $post_id; ?>" <?php post_class( 'abidar-article-card', $post_id ); ?>>
		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="abidar-article-card__thumb">
			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<?php echo get_the_post_thumbnail( $post_id, 'abidar-project-card', array( 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<span class="abidar-article-card__thumb-placeholder" style="background-image: url('<?php echo esc_url( ABIDAR_URI . '/assets/images/sector-energy.jpg' ); ?>');"></span>
			<?php endif; ?>
			<?php if ( $term ) : ?>
				<span class="abidar-article-card__cat"><?php echo esc_html( $term->name ); ?></span>
			<?php endif; ?>
		</a>
		<div class="abidar-article-card__body">
			<div class="abidar-article-card__meta">
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $post_id ) ); ?>"><?php echo esc_html( get_the_date( '', $post_id ) ); ?></time>
				<span aria-hidden="true">•</span>
				<span><?php echo esc_html( abidar_reading_time( $post_id ) ); ?></span>
			</div>
			<h3 class="abidar-article-card__title">
				<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
			</h3>
			<p class="abidar-article-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_id ), 26 ) ); ?></p>
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="abidar-link-gold">
				<span><?php echo esc_html( abidar_t( 'read_more' ) ); ?></span>
				<span class="abidar-icon-arrow">→</span>
			</a>
		</div>
	</article>
	<?php
}

/**
 * محاسبه زمان تقریبی مطالعه یک مقاله بر اساس تعداد کلمات
 *
 * @param int|null $post_id شناسه مقاله.
 * @return string متن آماده نمایش (با ترجمه چندزبانه).
 */
function abidar_reading_time( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( (string) $content ) );
	$words   = max( $words, 1 );
	$minutes = max( 1, (int) ceil( $words / 200 ) );

	return sprintf( '%d %s', $minutes, abidar_t( 'reading_time' ) );
}

/**
 * رندر سفارشی هر دیدگاه (استایل کارت‌های اجرایی هلدینگ)
 *
 * @param WP_Comment $comment دیدگاه جاری.
 * @param array      $args    آرگومان‌های wp_list_comments.
 * @param int        $depth   عمق دیدگاه.
 */
function abidar_comment_template( $comment, $args, $depth ) {
	?>
	<li <?php comment_class( 'abidar-comment' ); ?> id="comment-<?php comment_ID(); ?>">
		<article class="abidar-comment__inner">
			<div class="abidar-comment__avatar">
				<?php echo get_avatar( $comment, 56 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<div class="abidar-comment__body">
				<div class="abidar-comment__meta">
					<strong class="abidar-comment__author"><?php comment_author_link(); ?></strong>
					<time class="abidar-comment__date" datetime="<?php comment_time( DATE_W3C ); ?>">
						<?php comment_date(); ?> - <?php comment_time(); ?>
					</time>
					<?php if ( '0' === $comment->comment_approved ) : ?>
						<span class="abidar-comment__pending"><?php esc_html_e( 'در انتظار تایید', 'abidar-group' ); ?></span>
					<?php endif; ?>
				</div>

				<div class="abidar-comment__content">
					<?php comment_text(); ?>
				</div>

				<div class="abidar-comment__actions">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="abidar-comment__reply">',
								'after'     => '</span>',
							)
						)
					);
					?>
					<?php edit_comment_link( esc_html__( 'ویرایش', 'abidar-group' ), '<span class="abidar-comment__edit">', '</span>' ); ?>
				</div>
			</div>
		</article>
	<?php
	// تگ li توسط وردپرس بسته می‌شود (طبق استاندارد wp_list_comments).
}

/**
 * سفارشی‌سازی فرم جستجوی اصلی سایت
 *
 * @return string
 */
function abidar_search_form() {
	ob_start();
	?>
	<form role="search" method="get" class="abidar-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="abidar-search-field"><?php esc_html_e( 'جستجو در سایت', 'abidar-group' ); ?></label>
		<span class="abidar-search-form__icon" aria-hidden="true">🔍</span>
		<input type="search" id="abidar-search-field" class="abidar-search-form__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جستجوی خدمات، پروژه‌ها و مقالات...', 'abidar-group' ); ?>" />
		<button type="submit" class="abidar-search-form__submit"><?php esc_html_e( 'جستجو', 'abidar-group' ); ?></button>
	</form>
	<?php
	return ob_get_clean();
}
add_filter( 'get_search_form', 'abidar_search_form' );

/**
 * اضافه کردن کلاس‌های کمکی به محتوای مقالات برای رندر بهتر جداول، نقل‌قول‌ها و تصاویر
 *
 * @param string $content محتوای نوشته.
 * @return string
 */
function abidar_content_filters( $content ) {
	if ( is_admin() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$content = str_replace( '<table>', '<div class="abidar-table-wrap"><table>', $content );
	$content = str_replace( '</table>', '</table></div>', $content );

	return $content;
}
add_filter( 'the_content', 'abidar_content_filters', 20 );

/**
 * حذف پیشوندهای پیش‌فرض وردپرس از عنوان آرشیو («دسته:»، «برچسب:»، «نویسنده:» و ...)
 *
 * @return string عنوان تمیز آرشیو.
 */
function abidar_clean_archive_title() {
	$title = get_the_archive_title();

	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	}

	return $title;
}

/**
 * دریافت نخستین حرف/نویسه یک رشته با پشتیبانی از حروف فارسی، عربی و چینی
 * (بدون وابستگی اجباری به افزونه mbstring).
 *
 * @param string $text متن ورودی.
 * @return string
 */
function abidar_first_char( $text ) {
	$text = trim( wp_strip_all_tags( (string) $text ) );

	if ( '' === $text ) {
		return 'A';
	}

	if ( function_exists( 'mb_substr' ) ) {
		return mb_substr( $text, 0, 1, 'UTF-8' );
	}

	// استخراج نخستین نویسه UTF-8 بدون mbstring.
	if ( preg_match( '/^./us', $text, $matches ) ) {
		return $matches[0];
	}

	return substr( $text, 0, 1 );
}

/**
 * دریافت متای پنل حرفه‌ای با مقدار پیش‌فرض.
 *
 * @param int    $post_id شناسه پست.
 * @param string $key     کلید بدون پیشوند.
 * @param mixed  $default مقدار پیش‌فرض.
 * @return mixed
 */
function abidar_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, '_abidar_' . $key, true );

	if ( '' === $value || null === $value || false === $value ) {
		return $default;
	}

	return $value;
}

/**
 * تبدیل رشته شناسه‌های گالری به آرایه شناسه‌های معتبر.
 *
 * @param int $post_id شناسه پست.
 * @return array
 */
function abidar_gallery_ids( $post_id ) {
	$raw = get_post_meta( $post_id, '_abidar_gallery', true );

	if ( empty( $raw ) ) {
		return array();
	}

	if ( is_array( $raw ) ) {
		return array_filter( array_map( 'absint', $raw ) );
	}

	return array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) );
}

/**
 * استخراج شناسه ویدیوی یوتیوب/ویمئو از آدرس، یا null برای فایل مستقیم.
 *
 * @param string $url آدرس ویدیو.
 * @return array|null
 */
function abidar_parse_video_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return null;
	}

	// YouTube
	if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $m ) ) {
		return array(
			'type' => 'embed',
			'src'  => 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0',
		);
	}

	// Vimeo
	if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~i', $url, $m ) ) {
		return array(
			'type' => 'embed',
			'src'  => 'https://player.vimeo.com/video/' . $m[1],
		);
	}

	// Aparat (Iranian platform)
	if ( preg_match( '~aparat\.com/v/([A-Za-z0-9]+)~i', $url, $m ) ) {
		return array(
			'type' => 'embed',
			'src'  => 'https://www.aparat.com/video/video/embed/videohash/' . $m[1] . '/vt/frame',
		);
	}

	if ( preg_match( '~\.(mp4|webm|ogg|m4v)(\?|$)~i', $url ) ) {
		return array(
			'type' => 'file',
			'src'  => $url,
		);
	}

	return array(
		'type' => 'embed',
		'src'  => $url,
	);
}

/**
 * رندر بلوک ویدیو برای نوشته‌های اختصاصی.
 *
 * @param int $post_id شناسه پست.
 */
function abidar_render_video_block( $post_id ) {
	$video_id  = (int) get_post_meta( $post_id, '_abidar_video_id', true );
	$video_url = (string) get_post_meta( $post_id, '_abidar_video_url', true );

	if ( $video_id > 0 && '' === $video_url ) {
		$video_url = (string) wp_get_attachment_url( $video_id );
	}

	$video = abidar_parse_video_url( $video_url );

	if ( ! $video ) {
		return;
	}

	$poster = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'large' ) : '';
	?>
	<div class="abidar-video-block">
		<?php if ( 'file' === $video['type'] ) : ?>
			<video class="abidar-video-block__player" controls preload="metadata" playsinline
				<?php echo $poster ? 'poster="' . esc_url( $poster ) . '"' : ''; ?>>
				<source src="<?php echo esc_url( $video['src'] ); ?>" />
			</video>
		<?php else : ?>
			<div class="abidar-video-block__frame">
				<iframe src="<?php echo esc_url( $video['src'] ); ?>" title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"
					loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
					allowfullscreen></iframe>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * رندر بلوک گالری تصاویر.
 *
 * @param int $post_id شناسه پست.
 */
function abidar_render_gallery_block( $post_id ) {
	$ids = abidar_gallery_ids( $post_id );

	if ( empty( $ids ) ) {
		return;
	}
	?>
	<div class="abidar-gallery-block">
		<?php foreach ( $ids as $index => $attachment_id ) : ?>
			<a class="abidar-gallery-block__item<?php echo 0 === $index ? ' is-wide' : ''; ?>"
				href="<?php echo esc_url( (string) wp_get_attachment_url( $attachment_id ) ); ?>"
				target="_blank" rel="noopener">
				<?php echo wp_get_attachment_image( $attachment_id, 0 === $index ? 'large' : 'medium', false, array( 'loading' => 'lazy' ) ); ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * رندر جدول مشخصات فنی (ریپیتر specs).
 *
 * @param int    $post_id شناسه پست.
 * @param string $title   عنوان بلوک.
 */
function abidar_render_specs_table( $post_id, $title = '' ) {
	$specs = get_post_meta( $post_id, '_abidar_specs', true );

	if ( empty( $specs ) || ! is_array( $specs ) ) {
		return;
	}

	$title = $title ? $title : __( 'مشخصات فنی', 'abidar-group' );
	?>
	<div class="abidar-specs-table">
		<h3 class="abidar-specs-table__title"><?php echo esc_html( $title ); ?></h3>
		<dl class="abidar-specs-table__list">
			<?php foreach ( $specs as $spec ) : ?>
				<?php
				if ( empty( $spec['label'] ) && empty( $spec['value'] ) ) {
					continue;
				}
				?>
				<div class="abidar-specs-table__row">
					<dt><?php echo esc_html( $spec['label'] ); ?></dt>
					<dd><?php echo esc_html( $spec['value'] ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
	<?php
}

/**
 * رندر بلوک‌های عمومی ریپیتر (دستاوردها، مزیت‌ها، گام‌ها، تخصص‌ها، دامنه).
 *
 * @param int    $post_id شناسه پست.
 * @param string $meta_key کلید بدون پیشوند (achievements|features|steps|expertise|scope).
 * @param string $title    عنوان بلوک.
 * @param string $icon     آیکون پیش‌فرض هر ردیف.
 */
function abidar_render_repeater_block( $post_id, $meta_key, $title, $icon = '✓' ) {
	$rows = get_post_meta( $post_id, '_abidar_' . $meta_key, true );

	if ( empty( $rows ) || ! is_array( $rows ) ) {
		return;
	}
	?>
	<section class="abidar-repeater-block abidar-repeater-block--<?php echo esc_attr( $meta_key ); ?>">
		<h3 class="abidar-repeater-block__title"><?php echo esc_html( $title ); ?></h3>
		<ul class="abidar-repeater-block__list">
			<?php foreach ( $rows as $row ) : ?>
				<?php
				if ( empty( $row['label'] ) && empty( $row['value'] ) ) {
					continue;
				}
				?>
				<li class="abidar-repeater-block__item">
					<span class="abidar-repeater-block__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
					<div>
						<?php if ( ! empty( $row['label'] ) ) : ?>
							<strong><?php echo esc_html( $row['label'] ); ?></strong>
						<?php endif; ?>
						<?php if ( ! empty( $row['value'] ) ) : ?>
							<p><?php echo esc_html( $row['value'] ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
}

/**
 * رندر دکمه دانلود PDF بروشور.
 *
 * @param int $post_id شناسه پست.
 */
function abidar_render_pdf_button( $post_id ) {
	$pdf_id  = (int) get_post_meta( $post_id, '_abidar_pdf_id', true );
	$pdf_url = (string) get_post_meta( $post_id, '_abidar_pdf_url', true );

	if ( $pdf_id > 0 && '' === $pdf_url ) {
		$pdf_url = (string) wp_get_attachment_url( $pdf_id );
	}

	if ( '' === $pdf_url ) {
		return;
	}
	?>
	<a class="abidar-btn abidar-btn--outline-gold abidar-btn--sm abidar-btn--full abidar-pdf-btn"
		href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer">
		<span aria-hidden="true">📄</span>
		<?php echo esc_html( abidar_t( 'cta_download_catalog', 'دریافت کاتالوگ / بروشور PDF' ) ); ?>
	</a>
	<?php
}

/**
 * تبدیل پست‌های واقعی یک پست‌تایپ به ساختار داده‌ای ویجت‌های المنتور.
 *
 * ویجت‌های نمایش (خدمات، پروژه‌ها، تیم، مدارک) در ابتدا از یک مجموعه داده دمو
 * تغذیه می‌شدند. این تابع همان ساختار کلیدها را از پیشخوان و متای پنل حرفه‌ای
 * می‌سازد تا هر محتوایی که مدیر سایت ایجاد می‌کند در سایت دیده شود.
 *
 * @param string $post_type نوع پست (abidar_service|abidar_project|abidar_team|abidar_certificate).
 * @param int    $limit     حداکثر تعداد.
 * @param array  $args      آرگومان‌های اضافی WP_Query.
 * @return array آرایه‌ای از ردیف‌های سازگار با ویجت‌ها (یا آرایه خالی).
 */
function abidar_get_content_dataset( $post_type, $limit = 24, $args = array() ) {
	static $cache = array();

	$cache_key = $post_type . '|' . (int) $limit . '|' . md5( (string) wp_json_encode( $args ) );

	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	if ( ! post_type_exists( $post_type ) ) {
		$cache[ $cache_key ] = array();
		return array();
	}

	$query = new WP_Query(
		wp_parse_args(
			$args,
			array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'posts_per_page'      => (int) $limit,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'meta_key'            => '_abidar_order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'orderby'             => array(
					'meta_value_num' => 'ASC',
					'date'           => 'DESC',
				),
			)
		)
	);

	// اگر هیچ محتوایی ترتیب دستی نداشت، بدون meta_key دوباره بخوان (پست‌های بدون این متا حذف نشوند).
	if ( empty( $query->posts ) ) {
		unset( $args['meta_key'] );

		$query = new WP_Query(
			wp_parse_args(
				$args,
				array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => (int) $limit,
					'no_found_rows'  => true,
					'orderby'        => 'date',
					'order'          => 'DESC',
				)
			)
		);
	}

	$rows = array();

	foreach ( $query->posts as $post ) {
		$row = abidar_normalize_content_item( $post );

		if ( $row ) {
			$rows[] = $row;
		}
	}

	wp_reset_postdata();

	$cache[ $cache_key ] = $rows;

	return $rows;
}

/**
 * تبدیل یک پست به ساختار داده‌ای ویجت بر اساس نوع آن.
 *
 * @param WP_Post $post شیء پست.
 * @return array|null
 */
function abidar_normalize_content_item( $post ) {
	if ( ! $post instanceof WP_Post ) {
		return null;
	}

	$id       = (int) $post->ID;
	$title    = get_the_title( $id );
	$sector   = abidar_meta( $id, 'sector', 'mining' );
	$summary  = abidar_meta( $id, 'summary' );
	$excerpt  = $summary ? $summary : wp_strip_all_tags( (string) $post->post_excerpt );
	$image    = get_the_post_thumbnail_url( $id, 'large' );
	$sectors  = array(
		'mining' => 'معادن و صنایع معدنی',
		'energy' => 'انرژی و نیروگاه‌ها',
		'oil'    => 'نفت، گاز و پتروشیمی',
	);
	$row      = array(
		'id'          => $id,
		'permalink'   => get_permalink( $id ),
		'sector'      => $sector,
		'sector_label_fa' => isset( $sectors[ $sector ] ) ? $sectors[ $sector ] : '',
		'image'       => $image ? $image : '',
		'summary'     => $excerpt,
	);

	switch ( $post->post_type ) {
		case 'abidar_service':
			$features = get_post_meta( $id, '_abidar_features', true );

			$row['title_fa']  = $title;
			$row['desc_fa']   = $excerpt;
			$row['metric']    = abidar_meta( $id, 'metric' );
			$row['icon']      = abidar_meta( $id, 'icon', 'pickaxe' );
			$row['features']  = is_array( $features )
				? array_values( array_filter( array_map( 'wp_strip_all_tags', wp_list_pluck( $features, 'label' ) ) ) )
				: array();
			$row['title_en']  = abidar_meta( $id, 'title_en' );
			$row['title_zh']  = abidar_meta( $id, 'title_zh' );
			$row['desc_en']   = abidar_meta( $id, 'desc_en' );
			$row['desc_zh']   = abidar_meta( $id, 'desc_zh' );
			break;

		case 'abidar_project':
			$row['title_fa']     = $title;
			$row['desc_fa']      = $excerpt;
			$row['client_fa']    = abidar_meta( $id, 'client' );
			$row['location_fa']  = abidar_meta( $id, 'location' );
			$row['capacity']     = abidar_meta( $id, 'capacity' );
			$row['progress']     = (int) abidar_meta( $id, 'progress', 0 );
			$row['year']         = abidar_meta( $id, 'year' );
			$row['title_en']     = abidar_meta( $id, 'title_en' );
			$row['title_zh']     = abidar_meta( $id, 'title_zh' );
			$row['desc_en']      = abidar_meta( $id, 'desc_en' );
			$row['desc_zh']      = abidar_meta( $id, 'desc_zh' );
			$row['client_en']    = '';
			$row['location_en']  = '';
			$row['sector_label_en'] = '';
			break;

		case 'abidar_team':
			$row['name_fa']   = $title;
			$row['role_fa']   = abidar_meta( $id, 'role_fa' );
			$row['dept_fa']   = abidar_meta( $id, 'dept_fa' );
			$row['bio_fa']    = $excerpt;
			$row['education'] = abidar_meta( $id, 'education' );
			$row['experience'] = abidar_meta( $id, 'experience' );
			$row['email']     = abidar_meta( $id, 'email' );
			$row['linkedin']  = abidar_meta( $id, 'linkedin' );
			$row['initials']  = function_exists( 'abidar_first_char' ) ? abidar_first_char( $title ) : mb_substr( $title, 0, 1 );
			$row['accent']    = 'gold';
			$row['name_en']   = abidar_meta( $id, 'title_en' );
			$row['name_zh']   = abidar_meta( $id, 'title_zh' );
			$row['role_en']   = '';
			$row['role_zh']   = '';
			$row['dept_en']   = abidar_meta( $id, 'dept_en' );
			$row['dept_zh']   = abidar_meta( $id, 'dept_zh' );
			break;

		case 'abidar_certificate':
			$row['title_fa']     = $title;
			$row['issuer_fa']    = abidar_meta( $id, 'cert_issuer' );
			$row['code']         = abidar_meta( $id, 'cert_code' );
			$row['year']         = abidar_meta( $id, 'cert_year' );
			$row['badge']        = abidar_meta( $id, 'badge' );
			$row['category_fa']  = abidar_meta( $id, 'badge_text' );
			$row['title_en']     = abidar_meta( $id, 'title_en' );
			$row['title_zh']     = abidar_meta( $id, 'title_zh' );
			$row['issuer_en']    = '';
			$row['issuer_zh']    = '';
			$row['category_en']  = '';
			$row['category_zh']  = '';
			break;

		default:
			return null;
	}

	// عنوان و توضیح چندزبانه در قالب کلیدهای مورد انتظار ویجت‌ها.
	foreach ( array( 'title', 'desc', 'name', 'role', 'dept', 'bio', 'client', 'location', 'sector_label', 'issuer', 'category' ) as $base ) {
		foreach ( array( 'fa', 'en', 'zh' ) as $locale ) {
			$key = $base . '_' . $locale;

			if ( ! isset( $row[ $key ] ) ) {
				$row[ $key ] = '';
			}
		}
	}

	return $row;
}
