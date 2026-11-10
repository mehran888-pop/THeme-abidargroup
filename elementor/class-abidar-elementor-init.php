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
 *
 * @return bool آیا کلاس پایه سازگار با Elementor\Widget_Base در دسترس است؟
 */
/**
 * آیا انتخاب‌گر «طرح» باید رندر شود؟
 *
 * انتخاب‌گر طرح یک ابزار ویرایش است، نه بخشی از محتوای سایت. بنابراین:
 * - برای بازدیدکننده عادی هرگز نمایش داده نمی‌شود.
 * - فقط کاربری که واقعاً در حال ویرایش با المنتور است آن را می‌بیند و
 *   می‌تواند با آن طرح ویجت را پیش‌نمایش و انتخاب کند.
 *
 * @return bool
 */
function abidar_show_layout_switcher() {
	// فقط در بوم/پیش‌نمایش ویرایشگر المنتور.
	if ( ! class_exists( 'Abidar_Elementor_Init' ) || ! Abidar_Elementor_Init::is_elementor_editor_context() ) {
		return false;
	}

	// فقط کاربری که اجازه ویرایش دارد؛ نه بازدیدکننده‌ای که تصادفی پارامتر
	// elementor-preview را در آدرس دارد.
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	/**
	 * امکان خاموش کردن انتخاب‌گر طرح برای مدیران.
	 *
	 * @param bool $show آیا نمایش داده شود.
	 */
	return (bool) apply_filters( 'abidar_show_layout_switcher', true );
}

/**
 * آیا کلاس پایه ویجت‌ها واقعاً از Widget_Base المنتور ارث می‌برد؟
 *
 * اگر پاسخ منفی باشد، `class X extends Abidar_Elementor_Widget_Base` یک خطای
 * مهلک (نه Exception) می‌دهد که با try/catch قابل مهار نیست. بنابراین پیش از
 * بارگذاری هر فایل ویجت باید این بررسی انجام شود.
 *
 * @return bool
 */
function abidar_widget_base_is_elementor() {
	if ( ! class_exists( 'Abidar_Elementor_Widget_Base' ) ) {
		return false;
	}

	$elementor_widget_base = 'Elementor' . chr( 92 ) . 'Widget_Base';

	return class_exists( $elementor_widget_base )
		&& is_subclass_of( 'Abidar_Elementor_Widget_Base', $elementor_widget_base );
}

function abidar_declare_elementor_widget_base() {
	$elementor_widget_base = 'Elementor' . chr( 92 ) . 'Widget_Base';

	// کلاس قبلاً اعلان شده است: فقط گزارش می‌کنیم که آیا تبارش درست است.
	if ( class_exists( 'Abidar_Elementor_Widget_Base' ) ) {
		return class_exists( $elementor_widget_base )
			&& is_subclass_of( 'Abidar_Elementor_Widget_Base', $elementor_widget_base );
	}

	if ( class_exists( $elementor_widget_base ) ) {
		// مسیر اصلی: المنتور بارگذاری شده است؛ ویجت‌ها از API رسمی آن ارث می‌برند.
		abstract class Abidar_Elementor_Widget_Base extends \Elementor\Widget_Base {}

		return true;
	}

	// مسیر جانشین: المنتور نصب نیست. کلاس پایه سبک اعلان می‌شود تا هدر و فوتر
	// داخلی قالب روی فرانت‌اند همچنان رندر شوند. این کلاس هرگز در پنل المنتور
	// ثبت نمی‌شود، چون در این حالت المنتوری وجود ندارد.
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

	return false;
}

/**
 * Class Abidar_Elementor_Init
 */
class Abidar_Elementor_Init {

	/**
	 * محافظ بازگشت مجدد (Re-entrancy) برای رندر هدر و فوتر المنتور.
	 *
	 * اگر هدر/فوتر ساخته‌شده با المنتور خودش شامل ویجت هدر/فوتر باشد یا در حالت
	 * پیش‌نمایش ویرایشگر رندر شود، فراخوانی تودرتو می‌تواند حلقه بی‌پایان بسازد و
	 * ویرایشگر المنتور را در حالت «در حال بارگذاری» نگه دارد.
	 *
	 * @var bool
	 */
	private static $rendering_header = false;

	/**
	 * محافظ بازگشت مجدد برای فوتر.
	 *
	 * @var bool
	 */
	private static $rendering_footer = false;

	/**
	 * آیا در بافت ویرایشگر/پیش‌نمایش المنتور هستیم؟
	 *
	 * @return bool
	 */
	public static function is_elementor_editor_context() {
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}
		if ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}
		try {
			$plugin = \Elementor\Plugin::instance();

			$edit_mode = ( isset( $plugin->editor ) && is_object( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) )
				? (bool) $plugin->editor->is_edit_mode()
				: false;

			$preview = ( isset( $plugin->preview ) && is_object( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) )
				? (bool) $plugin->preview->is_preview_mode()
				: false;

			return $edit_mode || $preview;
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	/**
	 * راه‌اندازی قلاب‌های المنتور
	 */
	public static function init() {
		/**
		 * بارگذاری فایل‌های ویجت — فقط روی `init`.
		 *
		 * چرا نه `elementor/loaded`؟ چون آن اکشن پیش از بارگذاری قالب اجرا
		 * می‌شود: elementor.php در زمان بارگذاری افزونه includes/plugin.php را
		 * require می‌کند و آن فایل در سطح فایل Plugin::instance() را صدا می‌زند.
		 * هر شنونده‌ای که قالب روی آن اکشن ثبت کند هرگز اجرا نمی‌شود.
		 *
		 * چرا نه `after_setup_theme`؟ چون در آن لحظه ممکن است autoloader
		 * المنتور هنوز آماده نباشد. در این حالت کلاس پایه «جانشین» اعلان
		 * می‌شود و چون PHP اجازه اعلان دوباره نمی‌دهد، ویجت‌ها برای همیشه از
		 * Widget_Base واقعی محروم می‌مانند و در پنل المنتور دیده نمی‌شوند.
		 *
		 * در `init` المنتور قطعاً بارگذاری شده است (افزونه‌ها روی
		 * `plugins_loaded` بارگذاری می‌شوند که قبل از `init` است) و قالب‌ها هم
		 * خیلی دیرتر رندر می‌شوند. پس این امن‌ترین نقطه است.
		 */
		add_action( 'init', array( __CLASS__, 'load_widget_files' ), 5 );

		// ثبت دسته‌بندی‌های اختصاصی در پنل المنتور
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_widget_categories' ) );

		// ثبت ویجت‌ها در المنتور (منطبق با آخرین نسخه المنتور 3.25+)
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );

		// فعال‌سازی ویرایشگر المنتور روی پست‌تایپ‌های اختصاصی هلدینگ (هدر/فوتر ساز، خدمات، پروژه‌ها).
		// توجه: قلاب قدیمی `elementor/utils/get_public_post_types` دیگر در المنتور وجود ندارد؛
		// مکانیزم رسمی و پایدار، افزودن پشتیبانی `elementor` به خود پست‌تایپ است.
		add_action( 'init', array( __CLASS__, 'enable_elementor_for_cpts' ), 20 );
	}

	/**
	 * فعال‌سازی ویرایشگر المنتور روی پست‌تایپ‌های اختصاصی هلدینگ.
	 *
	 * المنتور تنها پست‌تایپ‌هایی را ویرایش می‌کند که (یا در تنظیمات
	 * `elementor_cpt_support` باشند و یا) پشتیبانی `elementor` داشته باشند.
	 * چون این پست‌تایپ‌ها توسط خود قالب ثبت می‌شوند، همان‌جا پشتیبانی اضافه شده
	 * است؛ این متد تضمین می‌کند حتی اگر تنظیمات المنتور بازنویسی شده باشد،
	 * ویرایشگر برای این پست‌تایپ‌ها در دسترس بماند.
	 */
	public static function enable_elementor_for_cpts() {
		$extra = array( 'abidar_hf', 'abidar_service', 'abidar_project', 'abidar_team', 'abidar_certificate' );

		foreach ( $extra as $post_type ) {
			if ( post_type_exists( $post_type ) && ! post_type_supports( $post_type, 'elementor' ) ) {
				add_post_type_support( $post_type, 'elementor' );
			}
		}
	}

	/**
	 * بارگذاری فایل‌های ویجت‌های اختصاصی گروه آبیدر
	 */
	public static function load_widget_files() {
		// کلاس پایه را اعلان کن. اگر مسیر «بدون المنتور» از قبل قفل شده باشد،
		// ویجت‌ها همچنان بارگذاری می‌شوند تا هدر/فوتر قالب روی فرانت‌اند از بین نرود؛
		// فقط ثبت آن‌ها در پنل المنتور انجام نمی‌شود (در register_widgets کنترل می‌شود).
		abidar_declare_elementor_widget_base();

		$widget_files = array(
			'/elementor/widgets/class-widget-header-nav.php',
			'/elementor/widgets/class-widget-footer-builder.php',
			'/elementor/widgets/class-widget-lang-switcher.php',
			'/elementor/widgets/class-widget-hero-slider.php',
			'/elementor/widgets/class-widget-hero-power.php',
			'/elementor/widgets/class-widget-services.php',
			'/elementor/widgets/class-widget-projects.php',
			'/elementor/widgets/class-widget-team.php',
			'/elementor/widgets/class-widget-certificates.php',
			'/elementor/widgets/class-widget-about-timeline.php',
			'/elementor/widgets/class-widget-contact-hub.php',
		);

		foreach ( $widget_files as $file ) {
			$full_path = ABIDAR_DIR . $file;
			if ( ! file_exists( $full_path ) ) {
				continue;
			}

			// اگر کلاس پایه جانشین اعلان شده باشد، `extends` خطای مهلک می‌دهد و
			// catch (\Throwable) آن را نمی‌گیرد. پس پیش از require بررسی می‌کنیم.
			if ( ! abidar_widget_base_is_elementor() ) {
				continue;
			}

			// اگر یک فایل ویجت خطای مهلک بدهد، بقیه ویجت‌ها و کل صفحه باید سالم بمانند.
			try {
				require_once $full_path;
			} catch ( \Throwable $e ) {
				unset( $e );
				continue;
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
		// نام قدیمی برای سازگاری؛ بارگذاری واقعی در load_widget_files() انجام می‌شود.
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
		if ( ! is_object( $elements_manager ) || ! method_exists( $elements_manager, 'add_category' ) ) {
			return;
		}

		$categories = array(
			'abidar-header-footer' => array(
				'title' => esc_html__( 'هدر و فوتر ساز گروه آبیدر (Elementor Free)', 'abidar-group' ),
				'icon'  => 'eicon-header',
			),
			'abidar-elements'      => array(
				'title' => esc_html__( 'المان‌های اختصاصی هلدینگ آبیدر (معادن، انرژی، نفت)', 'abidar-group' ),
				'icon'  => 'eicon-apps',
			),
		);

		foreach ( $categories as $slug => $args ) {
			// هر خطا در ثبت دسته‌بندی نباید کل ویرایشگر المنتور را از کار بیندازد.
			try {
				$elements_manager->add_category( $slug, $args );
			} catch ( \Throwable $e ) {
				unset( $e );
			}
		}
	}

	/**
	 * ثبت ویجت‌های اختصاصی در المنتور
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager مدیر ویجت‌های المنتور.
	 */
	public static function register_widgets( $widgets_manager ) {
		$elementor_widget_base = 'Elementor' . chr( 92 ) . 'Widget_Base';

		// اگر ویجت‌ها با کلاس پایه «جانشین» (بدون المنتور) اعلان شده باشند،
		// هرگز نمی‌توان آن‌ها را در المنتور ثبت کرد؛ چون آن کلاس از Widget_Base
		// واقعی ارث نمی‌برد. این حالت زمانی رخ می‌دهد که فایل‌های ویجت پیش از
		// در دسترس قرار گرفتن کلاس‌های المنتور بارگذاری شده باشند.
		if ( class_exists( 'Abidar_Widget_Services' )
			&& ! is_subclass_of( 'Abidar_Widget_Services', $elementor_widget_base ) ) {
			return;
		}

		self::load_widget_files();

		$widget_classes = array(
			'Abidar_Widget_Header_Nav',
			'Abidar_Widget_Footer_Builder',
			'Abidar_Widget_Lang_Switcher',
			'Abidar_Widget_Hero_Slider',
			'Abidar_Widget_Hero_Power',
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

			// محافظ ۱: ساخت نمونه در try/catch تا یک ویجت معیوب کل ویرایشگر را متوقف نکند.
			try {
				$instance = new $class_name();
			} catch ( \Throwable $e ) {
				continue;
			}

			// محافظ ۲: تنها ویجت‌هایی ثبت می‌شوند که واقعاً از Widget_Base المنتور ارث‌بری کرده باشند.
			// این بررسی از خطای Fatal نوع‌شناسی در Widgets_Manager::register() جلوگیری می‌کند.
			if ( ! $instance instanceof \Elementor\Widget_Base ) {
				continue;
			}

			// محافظ ۳: ثبت هر ویجت جداگانه محافظت می‌شود؛ خطای یک ویجت
			// نباید پاسخ ویرایشگر را ۵۰۰ کند و نشانگر بارگذاری را بی‌پایان نگه دارد.
			try {
				$widgets_manager->register( $instance );
			} catch ( \Throwable $e ) {
				continue;
			}
		}
	}

	/**
	 * بررسی و رندر هدر طراحی‌شده با المنتور رایگان (در صورت وجود) یا رندر ویجت هدر اختصاصی
	 */
	public static function render_site_header() {
		// محافظ بازگشت مجدد: جلوگیری از حلقه بی‌پایان هنگام رندر هدر داخل خودش.
		if ( self::$rendering_header ) {
			return;
		}

		$header_layout    = abidar_get_option( 'header_layout', 'layout_1' );
		$custom_header_id = (int) abidar_get_option( 'custom_header_id', 0 );

		/**
		 * اگر در بوم/پیش‌نمایش ویرایشگر المنتور هستیم، هرگز سند المنتور دیگری
		 * (هدر یا فوتر ساخته‌شده در abidar_hf) را تودرتو رندر نمی‌کنیم.
		 *
		 * رندر تودرتوی یک سند کامل المنتور داخل iframe پیش‌نمایش، شنونده‌های
		 * `elementor/frontend/init` را دوباره ثبت می‌کند و پیام پایان بارگذاری
		 * هرگز به پنل ویرایشگر نمی‌رسد؛ نتیجه همان «المنتور در حال بارگذاری
		 * می‌ماند و صفحه ویرایش باز نمی‌شود» است. در این حالت ویجت هدر داخلی
		 * قالب رندر می‌شود تا پیش‌نمایش کامل و سبک باقی بماند.
		 */
		$is_editor = self::is_elementor_editor_context();

		// اگر کاربر هدری در پست‌تایپ abidar_hf ساخته و فعال کرده باشد
		if ( ! $is_editor && ( 'elementor_custom' === $header_layout || $custom_header_id > 0 ) ) {
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
					self::$rendering_header = true;

					try {
						$builder_content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $hf_post->ID, true );
					} catch ( \Throwable $e ) {
						$builder_content = '';
					}

					self::$rendering_header = false;

					if ( '' !== trim( (string) $builder_content ) ) {
						echo $builder_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						wp_reset_postdata();
						return;
					}
				}
			}
			wp_reset_postdata();
		}

		// بارگذاری تضمینی کلاس‌های ویجت پیش از رندر (در صورت نبود المنتور نیز کار می‌کند).
		self::load_widget_files();

		// رندر ویجت هدر اختصاصی گروه آبیدر با چیدمان انتخاب‌شده
		if ( class_exists( 'Abidar_Widget_Header_Nav' ) ) {
			// در ویرایشگر، اگر چیدمان انتخاب‌شده «هدر المنتور» باشد ویجت داخلی جایگزین می‌شود.
			if ( 'elementor_custom' === $header_layout ) {
				$header_layout = 'layout_1';
			}

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
		// محافظ بازگشت مجدد: جلوگیری از حلقه بی‌پایان هنگام رندر فوتر داخل خودش.
		if ( self::$rendering_footer ) {
			return;
		}

		$footer_layout    = abidar_get_option( 'footer_layout', 'layout_1' );
		$custom_footer_id = (int) abidar_get_option( 'custom_footer_id', 0 );

		// همان دلیل هدر: رندر تودرتوی سند المنتور در ویرایشگر ممنوع است.
		$is_editor = self::is_elementor_editor_context();

		if ( ! $is_editor && ( 'elementor_custom' === $footer_layout || $custom_footer_id > 0 ) ) {
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
					self::$rendering_footer = true;

					try {
						$builder_content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $hf_post->ID, true );
					} catch ( \Throwable $e ) {
						$builder_content = '';
					}

					self::$rendering_footer = false;

					if ( '' !== trim( (string) $builder_content ) ) {
						echo $builder_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						wp_reset_postdata();
						return;
					}
				}
			}
			wp_reset_postdata();
		}

		self::load_widget_files();

		if ( class_exists( 'Abidar_Widget_Footer_Builder' ) ) {
			if ( 'elementor_custom' === $footer_layout ) {
				$footer_layout = 'layout_1';
			}

			Abidar_Widget_Footer_Builder::render_output(
				array(
					'layout_style' => $footer_layout,
				)
			);
		}
	}
}

Abidar_Elementor_Init::init();
