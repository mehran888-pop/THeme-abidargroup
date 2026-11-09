<?php
/**
 * Abidar Group Elementor Free Integration & Custom Widgets Loader (v2.0.0)
 *
 * یکپارچه‌سازی کامل با صفحه‌ساز المنتور رایگان (Elementor Free):
 * - ثبت دسته‌بندی‌های اختصاصی ویجت‌ها (هدر/فوتر ساز + المان‌های صنعتی آبیدر)
 * - ثبت ۹ ویجت اختصاصی با طرح‌های مختلف (Layouts) و کنترل کامل استایل (Style Controls)
 * - رندر خودکار قالب‌های هدر و فوتر طراحی‌شده با المنتور رایگان
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * اعلان کلاس پایه ویجت‌ها، دقیقاً در لحظه‌ای که فایل‌های ویجت بارگذاری می‌شوند.
 *
 * دلیل این طراحی: اگر کلاس پایه در سطح فایل و پیش از آماده شدن المنتور تعریف شود،
 * نسخه «بدون المنتور» قفل می‌شود و در نتیجه کلاس‌های ویجت از \Elementor\Widget_Base
 * ارث‌بری نمی‌کنند؛ سپس Elementor\Widgets_Manager::register() با خطای
 * «TypeError: Argument #1 ($widget_instance) must be of type Elementor\Widget_Base»
 * متوقف می‌شود. با اعلان تنبل (Lazy)، ویجت‌ها همیشه از کلاس صحیح ارث‌بری می‌کنند.
 */
function abidar_declare_elementor_widget_base() {
	if ( class_exists( 'Abidar_Elementor_Widget_Base' ) ) {
		return;
	}

	if ( class_exists( '\Elementor\Widget_Base' ) ) {
		// مسیر اصلی: المنتور فعال است و ویجت‌ها با API رسمی آن ثبت می‌شوند.
		abstract class Abidar_Elementor_Widget_Base extends \Elementor\Widget_Base {}
	} else {
		// مسیر جانشین: المنتور نصب نیست؛ هدر و فوتر داخلی قالب باید همچنان رندر شوند.
		abstract class Abidar_Elementor_Widget_Base {
			protected $settings = array();

			public function __construct( $data = array(), $args = null ) {
				if ( is_array( $data ) ) {
					$this->settings = $data;
				}
			}

			public function get_settings_for_display( $key = null ) {
				if ( null !== $key ) {
					return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
				}
				return $this->settings;
			}

			public function start_controls_section( $id, $args = array() ) {}
			public function end_controls_section() {}
			public function add_control( $id, $args = array() ) {}
			public function add_group_control( $type, $args = array() ) {}
			public function add_responsive_control( $id, $args = array() ) {}
		}
	}
}

/**
 * Class Abidar_Elementor_Init
 */
class Abidar_Elementor_Init {

	/**
	 * راه‌اندازی قلاب‌های المنتور
	 */
	public static function init() {
		// بارگذاری زودهنگام کلاس‌های ویجت، تنها زمانی که المنتور کاملاً آماده شده است.
		add_action( 'elementor/loaded', array( __CLASS__, 'load_widget_files' ), 5 );

		// مسیر جانشین (Fallback): اگر المنتور نصب/فعال نباشد، ویجت‌ها با کلاس پایه سبک
		// بارگذاری می‌شوند تا هدر و فوتر داخلی قالب همچنان رندر شوند.
		add_action( 'after_setup_theme', array( __CLASS__, 'load_widget_files_fallback' ), 20 );

		// ثبت دسته‌بندی‌های اختصاصی در پنل المنتور
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_widget_categories' ) );

		// ثبت ویجت‌ها در المنتور (منطبق با آخرین نسخه المنتور 3.25+)
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );

		// فعال‌سازی ویرایشگر المنتور روی پست‌تایپ‌های اختصاصی هلدینگ (هدر/فوتر ساز، خدمات، پروژه‌ها)
		add_filter( 'elementor/utils/get_public_post_types', array( __CLASS__, 'enable_elementor_for_cpts' ) );
	}

	/**
	 * افزودن پست‌تایپ‌های اختصاصی به فهرست پست‌تایپ‌های قابل ویرایش با المنتور
	 *
	 * @param array $post_types فهرست پست‌تایپ‌های عمومی.
	 * @return array
	 */
	public static function enable_elementor_for_cpts( $post_types ) {
		$extra = array( 'abidar_hf', 'abidar_service', 'abidar_project' );

		foreach ( $extra as $post_type ) {
			if ( post_type_exists( $post_type ) && ! in_array( $post_type, $post_types, true ) ) {
				$post_types[] = $post_type;
			}
		}

		return $post_types;
	}

	/**
	 * بارگذاری فایل‌های ویجت‌های اختصاصی گروه آبیدر
	 */
	public static function load_widget_files() {
		abidar_declare_elementor_widget_base();

		$widget_files = array(
			'/elementor/widgets/class-widget-header-nav.php',
			'/elementor/widgets/class-widget-footer-builder.php',
			'/elementor/widgets/class-widget-lang-switcher.php',
			'/elementor/widgets/class-widget-hero-slider.php',
			'/elementor/widgets/class-widget-services.php',
			'/elementor/widgets/class-widget-projects.php',
			'/elementor/widgets/class-widget-team.php',
			'/elementor/widgets/class-widget-certificates.php',
			'/elementor/widgets/class-widget-about-timeline.php',
			'/elementor/widgets/class-widget-contact-hub.php',
		);

		foreach ( $widget_files as $file ) {
			$full_path = ABIDAR_DIR . $file;
			if ( file_exists( $full_path ) ) {
				require_once $full_path;
			}
		}
	}

	/**
	 * بارگذاری ویجت‌ها در حالت نبود المنتور.
	 *
	 * اگر المنتور نصب یا فعال باشد، این متد هیچ کاری نمی‌کند تا کلاس‌های ویجت
	 * حتماً از \Elementor\Widget_Base ارث‌بری کنند (جلوگیری از خطای TypeError).
	 */
	public static function load_widget_files_fallback() {
		if ( self::is_elementor_active() ) {
			return;
		}

		self::load_widget_files();
	}

	/**
	 * آیا المنتور نصب و در حال اجرا است؟
	 *
	 * @return bool
	 */
	public static function is_elementor_active() {
		return defined( 'ELEMENTOR_VERSION' ) || class_exists( '\Elementor\Widget_Base' );
	}

	/**
	 * ثبت دسته‌بندی‌های اختصاصی گروه آبیدر در ویرایشگر المنتور
	 *
	 * @param \Elementor\Elements_Manager $elements_manager مدیر المان‌های المنتور.
	 */
	public static function register_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'abidar-header-footer',
			array(
				'title' => esc_html__( 'هدر و فوتر ساز گروه آبیدر (Elementor Free)', 'abidar-group' ),
				'icon'  => 'eicon-header',
			)
		);

		$elements_manager->add_category(
			'abidar-elements',
			array(
				'title' => esc_html__( 'المان‌های اختصاصی هلدینگ آبیدر (معادن، انرژی، نفت)', 'abidar-group' ),
				'icon'  => 'eicon-apps',
			)
		);
	}

	/**
	 * ثبت ویجت‌های اختصاصی در المنتور
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager مدیر ویجت‌های المنتور.
	 */
	public static function register_widgets( $widgets_manager ) {
		self::load_widget_files();

		$widget_classes = array(
			'Abidar_Widget_Header_Nav',
			'Abidar_Widget_Footer_Builder',
			'Abidar_Widget_Lang_Switcher',
			'Abidar_Widget_Hero_Slider',
			'Abidar_Widget_Services',
			'Abidar_Widget_Projects',
			'Abidar_Widget_Team',
			'Abidar_Widget_Certificates',
			'Abidar_Widget_About_Timeline',
			'Abidar_Widget_Contact_Hub',
		);

		foreach ( $widget_classes as $class_name ) {
			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			$instance = new $class_name();

			// محافظ نهایی: تنها ویجت‌هایی ثبت می‌شوند که واقعاً از Widget_Base المنتور ارث‌بری کرده باشند.
			// این بررسی از خطای Fatal نوع‌شناسی در Widgets_Manager::register() جلوگیری می‌کند.
			if ( ! $instance instanceof \Elementor\Widget_Base ) {
				continue;
			}

			$widgets_manager->register( $instance );
		}
	}

	/**
	 * بررسی و رندر هدر طراحی‌شده با المنتور رایگان (در صورت وجود) یا رندر ویجت هدر اختصاصی
	 */
	public static function render_site_header() {
		$header_layout    = abidar_get_option( 'header_layout', 'layout_1' );
		$custom_header_id = (int) abidar_get_option( 'custom_header_id', 0 );

		// اگر کاربر هدری در پست‌تایپ abidar_hf ساخته و فعال کرده باشد
		if ( 'elementor_custom' === $header_layout || $custom_header_id > 0 ) {
			$hf_query = new WP_Query(
				array(
					'post_type'      => 'abidar_hf',
					'posts_per_page' => 1,
					'post_status'    => 'publish',
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'   => '_abidar_hf_type',
							'value' => 'header',
						),
					),
				)
			);

			if ( $hf_query->have_posts() ) {
				$hf_post = $hf_query->posts[0];
				if ( self::is_elementor_active() && class_exists( '\Elementor\Plugin' ) ) {
					echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $hf_post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					wp_reset_postdata();
					return;
				}
			}
			wp_reset_postdata();
		}

		// بارگذاری تضمینی کلاس‌های ویجت پیش از رندر (در صورت نبود المنتور نیز کار می‌کند).
		self::load_widget_files();

		// رندر ویجت هدر اختصاصی گروه آبیدر با چیدمان انتخاب‌شده
		if ( class_exists( 'Abidar_Widget_Header_Nav' ) ) {
			Abidar_Widget_Header_Nav::render_output(
				array(
					'layout_style'       => $header_layout,
					'show_topbar'        => 'yes',
					'show_lang_switcher' => 'yes',
					'show_cta_btn'       => 'yes',
				)
			);
			return;
		}

		// جانشین نهایی: هرگز هدر خالی رها نمی‌شود.
		?>
		<header class="abidar-site-header abidar-site-header--fallback" role="banner">
			<div class="abidar-container abidar-header-fallback">
				<a class="abidar-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php echo esc_html( abidar_t( 'holding_name' ) ); ?>
				</a>
			</div>
		</header>
		<?php
	}

	/**
	 * بررسی و رندر فوتر طراحی‌شده با المنتور رایگان (در صورت وجود) یا رندر ویجت فوتر اختصاصی
	 */
	public static function render_site_footer() {
		$footer_layout    = abidar_get_option( 'footer_layout', 'layout_1' );
		$custom_footer_id = (int) abidar_get_option( 'custom_footer_id', 0 );

		if ( 'elementor_custom' === $footer_layout || $custom_footer_id > 0 ) {
			$hf_query = new WP_Query(
				array(
					'post_type'      => 'abidar_hf',
					'posts_per_page' => 1,
					'post_status'    => 'publish',
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'   => '_abidar_hf_type',
							'value' => 'footer',
						),
					),
				)
			);

			if ( $hf_query->have_posts() ) {
				$hf_post = $hf_query->posts[0];
				if ( self::is_elementor_active() && class_exists( '\Elementor\Plugin' ) ) {
					echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $hf_post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					wp_reset_postdata();
					return;
				}
			}
			wp_reset_postdata();
		}

		self::load_widget_files();

		if ( class_exists( 'Abidar_Widget_Footer_Builder' ) ) {
			Abidar_Widget_Footer_Builder::render_output(
				array(
					'layout_style' => $footer_layout,
				)
			);
		}
	}
}

Abidar_Elementor_Init::init();
