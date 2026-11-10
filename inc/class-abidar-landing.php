<?php
/**
 * قابلیت لندینگ پیج.
 *
 * - متاباکس تنظیمات در ویرایشگر برگه (حالت اسکرول، چسبیدن بخش‌ها، هدر/فوتر).
 * - بارگذاری دارایی‌های مخصوص لندینگ فقط روی همان برگه.
 * - خارج کردن هدر/فوتر از حالت افقی برای اینکه تمام‌قد باشد.
 *
 * @package Abidar_Group
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کلاس مدیریت لندینگ پیج.
 */
class Abidar_Landing {

	/**
	 * نام متاکی‌ها.
	 */
	const META_SCROLL = '_abidar_landing_scroll';
	const META_SNAP   = '_abidar_landing_snap';
	const META_HEADER = '_abidar_landing_header';
	const META_FOOTER = '_abidar_landing_footer';

	/**
	 * مقدار پیش‌فرض تنظیمات.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'scroll' => 'vertical',
			'snap'   => 'yes',
			'header' => 'yes',
			'footer' => 'yes',
		);
	}

	/**
	 * خواندن تنظیمات یک برگه با مقادیر پیش‌فرض و اعتبارسنجی.
	 *
	 * @param int $post_id شناسه برگه.
	 * @return array{scroll:string,snap:string,header:string,footer:string}
	 */
	public static function get_meta( $post_id ) {
		$defaults = self::defaults();
		$post_id  = absint( $post_id );

		if ( ! $post_id ) {
			return $defaults;
		}

		$scroll = get_post_meta( $post_id, self::META_SCROLL, true );
		$snap   = get_post_meta( $post_id, self::META_SNAP, true );
		$header = get_post_meta( $post_id, self::META_HEADER, true );
		$footer = get_post_meta( $post_id, self::META_FOOTER, true );

		return array(
			'scroll' => in_array( $scroll, array( 'horizontal', 'vertical' ), true ) ? $scroll : $defaults['scroll'],
			'snap'   => in_array( $snap, array( 'yes', 'no' ), true ) ? $snap : $defaults['snap'],
			'header' => in_array( $header, array( 'yes', 'no' ), true ) ? $header : $defaults['header'],
			'footer' => in_array( $footer, array( 'yes', 'no' ), true ) ? $footer : $defaults['footer'],
		);
	}

	/**
	 * آیا این برگه با قالب لندینگ نمایش داده می‌شود؟
	 *
	 * @param int|null $post_id شناسه برگه.
	 * @return bool
	 */
	public static function is_landing( $post_id = null ) {
		$post_id = $post_id ? absint( $post_id ) : self::queried_id();

		if ( ! $post_id ) {
			return false;
		}

		return 'template-landing.php' === get_page_template_slug( $post_id );
	}

	/**
	 * شناسه برگه‌ای که واقعاً درخواست شده است.
	 *
	 * get_the_ID() شناسه نوشتهٔ جاری در حلقه است؛ پیش از اجرای حلقه
	 * (مثلاً در wp_enqueue_scripts) false برمی‌گرداند و در آرشیوها
	 * به اولین نوشته اشاره می‌کند. get_queried_object_id() همان چیزی
	 * است که کاربر آدرسش را باز کرده.
	 *
	 * @return int
	 */
	public static function queried_id() {
		if ( is_singular() ) {
			return absint( get_queried_object_id() );
		}

		return 0;
	}

	/**
	 * راه‌اندازی.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_page', array( __CLASS__, 'save_meta' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ), 30 );
	}

	/**
	 * متاباکس تنظیمات.
	 *
	 * @return void
	 */
	public static function add_meta_box() {
		add_meta_box(
			'abidar-landing-settings',
			__( 'تنظیمات لندینگ پیج — آبیدر', 'abidar-group' ),
			array( __CLASS__, 'render_meta_box' ),
			'page',
			'side',
			'high'
		);
	}

	/**
	 * فرم تنظیمات در سایدبار ویرایشگر.
	 *
	 * @param WP_Post $post برگه جاری.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		$meta = self::get_meta( $post->ID );

		wp_nonce_field( 'abidar_landing_save', 'abidar_landing_nonce' );
		?>
		<div class="abidar-landing-box">
			<p class="abidar-landing-box__intro">
				<?php esc_html_e( 'این تنظیمات فقط وقتی اعمال می‌شود که قالب برگه روی «لندینگ پیج» باشد.', 'abidar-group' ); ?>
			</p>

			<p class="abidar-landing-box__field">
				<label for="abidar-landing-scroll"><strong><?php esc_html_e( 'حالت اسکرول', 'abidar-group' ); ?></strong></label>
				<select name="abidar_landing_scroll" id="abidar-landing-scroll" class="widefat">
					<option value="vertical" <?php selected( $meta['scroll'], 'vertical' ); ?>><?php esc_html_e( 'عمودی — بخش‌ها پشت سر هم', 'abidar-group' ); ?></option>
					<option value="horizontal" <?php selected( $meta['scroll'], 'horizontal' ); ?>><?php esc_html_e( 'افقی — سایت افقی اسکرول می‌خورد', 'abidar-group' ); ?></option>
				</select>
			</p>

			<p class="abidar-landing-box__field">
				<label for="abidar-landing-snap"><strong><?php esc_html_e( 'چسبیدن بخش‌ها', 'abidar-group' ); ?></strong></label>
				<select name="abidar_landing_snap" id="abidar-landing-snap" class="widefat">
					<option value="yes" <?php selected( $meta['snap'], 'yes' ); ?>><?php esc_html_e( 'فعال — هر بخش کامل می‌ایستد', 'abidar-group' ); ?></option>
					<option value="no" <?php selected( $meta['snap'], 'no' ); ?>><?php esc_html_e( 'غیرفعال — اسکرول آزاد', 'abidar-group' ); ?></option>
				</select>
			</p>

			<p class="abidar-landing-box__field">
				<label for="abidar-landing-header"><strong><?php esc_html_e( 'نمایش هدر سایت', 'abidar-group' ); ?></strong></label>
				<select name="abidar_landing_header" id="abidar-landing-header" class="widefat">
					<option value="yes" <?php selected( $meta['header'], 'yes' ); ?>><?php esc_html_e( 'بله', 'abidar-group' ); ?></option>
					<option value="no" <?php selected( $meta['header'], 'no' ); ?>><?php esc_html_e( 'خیر — حالت تمام‌صفحه', 'abidar-group' ); ?></option>
				</select>
			</p>

			<p class="abidar-landing-box__field">
				<label for="abidar-landing-footer"><strong><?php esc_html_e( 'نمایش فوتر سایت', 'abidar-group' ); ?></strong></label>
				<select name="abidar_landing_footer" id="abidar-landing-footer" class="widefat">
					<option value="yes" <?php selected( $meta['footer'], 'yes' ); ?>><?php esc_html_e( 'بله', 'abidar-group' ); ?></option>
					<option value="no" <?php selected( $meta['footer'], 'no' ); ?>><?php esc_html_e( 'خیر', 'abidar-group' ); ?></option>
				</select>
			</p>

			<p class="abidar-landing-box__hint">
				<?php esc_html_e( 'در حالت افقی، زیر ۱۰۲۴ پیکسل (موبایل و تبلت عمودی) خودکار به حالت عمودی برمی‌گردد.', 'abidar-group' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * ذخیره تنظیمات.
	 *
	 * @param int     $post_id شناسه برگه.
	 * @param WP_Post $post    شیء برگه.
	 * @return void
	 */
	public static function save_meta( $post_id, $post ) {
		if ( ! isset( $_POST['abidar_landing_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['abidar_landing_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'abidar_landing_save' ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$map = array(
			'scroll' => self::META_SCROLL,
			'snap'   => self::META_SNAP,
			'header' => self::META_HEADER,
			'footer' => self::META_FOOTER,
		);

		foreach ( $map as $field => $meta_key ) {
			$key = 'abidar_landing_' . $field;

			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			$value = sanitize_key( wp_unslash( $_POST[ $key ] ) );
			$valid = array(
				'scroll' => array( 'horizontal', 'vertical' ),
				'snap'   => array( 'yes', 'no' ),
				'header' => array( 'yes', 'no' ),
				'footer' => array( 'yes', 'no' ),
			);

			if ( in_array( $value, $valid[ $field ], true ) ) {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}
	}

	/**
	 * بارگذاری CSS/JS لندینگ.
	 *
	 * @return void
	 */
	public static function enqueue() {
		$post_id = self::queried_id();

		if ( ! $post_id || ! self::is_landing( $post_id ) ) {
			return;
		}

		$meta = self::get_meta( $post_id );

		wp_enqueue_style(
			'abidar-landing',
			ABIDAR_URI . '/assets/css/landing.css',
			array( 'abidar-main' ),
			ABIDAR_VERSION
		);

		if ( 'horizontal' === $meta['scroll'] ) {
			wp_enqueue_script(
				'abidar-landing',
				ABIDAR_URI . '/assets/js/landing.js',
				array(),
				ABIDAR_VERSION,
				true
			);

			wp_localize_script(
				'abidar-landing',
				'abidarLanding',
				array(
					'snap'  => 'yes' === $meta['snap'],
					'i18n'  => array(
						'prev' => __( 'بخش قبلی', 'abidar-group' ),
						'next' => __( 'بخش بعدی', 'abidar-group' ),
					),
				)
			);
		}
	}
}

Abidar_Landing::init();
