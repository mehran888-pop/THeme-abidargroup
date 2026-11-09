<?php
/**
 * Abidar Group — Professional Content Editor for Custom Post Types (v2.1.0)
 *
 * به‌جای متاباکس‌های ساده و متنی، یک پنل ویرایش حرفه‌ای و تمام‌عرض ارائه می‌دهد:
 * - تب‌بندی منطقی (محتوا / رسانه / مشخصات فنی / چندزبانه / تنظیمات)
 * - انتخاب تصویر شاخص، گالری تصاویر و ویدیو با کتابخانه رسانه وردپرس
 * - آپلود PDF و بروشور
 * - فهرست تکراری (Repeater) برای ویژگی‌ها، مشخصات فنی و دستاوردها
 * - پیش‌نمایش زنده کارت در کنار فرم
 * - پشتیبانی چندزبانه برای عنوان و توضیحات
 *
 * @package Abidar_Group
 * @since 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_CPT_Editor
 */
class Abidar_CPT_Editor {

	/**
	 * پست‌تایپ‌هایی که ویرایشگر حرفه‌ای دارند.
	 *
	 * @var array
	 */
	private static $post_types = array( 'abidar_service', 'abidar_project', 'abidar_team', 'abidar_certificate' );

	/**
	 * راه‌اندازی قلاب‌ها.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'replace_meta_boxes' ), 99 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 20, 3 );
		// پنل حرفه‌ای به‌عنوان ویرایشگر اصلی این چهار پست‌تایپ.
		add_filter( 'use_block_editor_for_post_type', array( __CLASS__, 'use_block_editor' ), 20, 2 );
	}

	/**
	 * استفاده از ویرایشگر کلاسیک برای پست‌تایپ‌های دارای پنل حرفه‌ای.
	 *
	 * با این کار به‌جای بوم گوتنبرگ + متاباکس‌های پایین صفحه، تمام فرم ویرایش
	 * به همان پنل ساخت‌یافته و تب‌بندی‌شده اختصاص می‌یابد. در صورت نیاز، با فیلتر
	 * `abidar_cpt_use_block_editor` می‌توان گوتنبرگ را دوباره فعال کرد.
	 *
	 * @param bool   $use       آیا ویرایشگر بلوک استفاده شود.
	 * @param string $post_type نوع پست.
	 * @return bool
	 */
	public static function use_block_editor( $use, $post_type = '' ) {
		if ( in_array( $post_type, self::$post_types, true ) ) {
			return (bool) apply_filters( 'abidar_cpt_use_block_editor', false, $post_type );
		}

		return $use;
	}

	/**
	 * حذف متاباکس‌های قدیمی و ثبت پنل جدید.
	 */
	public static function replace_meta_boxes() {
		// حذف متاباکس‌های پیش‌فرض متنی.
		remove_meta_box( 'abidar_service_meta', 'abidar_service', 'normal' );
		remove_meta_box( 'abidar_project_meta', 'abidar_project', 'normal' );
		remove_meta_box( 'abidar_team_meta', 'abidar_team', 'normal' );
		remove_meta_box( 'abidar_certificate_meta', 'abidar_certificate', 'normal' );

		foreach ( self::$post_types as $post_type ) {
			add_meta_box(
				'abidar_pro_editor',
				esc_html__( 'پنل حرفه‌ای محتوا — گروه آبیدر', 'abidar-group' ),
				array( __CLASS__, 'render_panel' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * بارگذاری دارایی‌های پنل فقط در صفحات ویرایش پست‌تایپ‌های اختصاصی.
	 *
	 * @param string $hook قلاب صفحه فعلی.
	 */
	public static function enqueue_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, self::$post_types, true ) ) {
			return;
		}

		// کتابخانه رسانه + وابستگی‌های لازم برای گالری، ویدیو و PDF.
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );

		// در ویرایشگر بلوک (گوتنبرگ) متاباکس‌ها داخل iframe رندر می‌شوند؛
		// ثبت‌نام wp.media در والد لازم است تا انتخابگر رسانه درست باز شود.
		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'inline_media_bridge' ), 5 );

		wp_enqueue_style(
			'abidar-editor-css',
			ABIDAR_URI . '/assets/css/editor.css',
			array( 'wp-color-picker' ),
			ABIDAR_VERSION
		);

		wp_enqueue_script(
			'abidar-editor-js',
			ABIDAR_URI . '/assets/js/editor.js',
			array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ),
			ABIDAR_VERSION,
			true
		);

		wp_localize_script(
			'abidar-editor-js',
			'AbidarEditor',
			array(
				'i18n' => array(
					'selectImage' => esc_html__( 'انتخاب تصویر', 'abidar-group' ),
					'useImage'    => esc_html__( 'استفاده از این تصویر', 'abidar-group' ),
					'selectVideo' => esc_html__( 'انتخاب ویدیو', 'abidar-group' ),
					'useVideo'    => esc_html__( 'استفاده از این ویدیو', 'abidar-group' ),
					'selectFile'  => esc_html__( 'انتخاب فایل PDF', 'abidar-group' ),
					'useFile'     => esc_html__( 'استفاده از این فایل', 'abidar-group' ),
					'confirmDel'  => esc_html__( 'این مورد حذف شود؟', 'abidar-group' ),
				),
			)
		);
	}

	/**
	 * پل ارتباطی کوچک برای بازشدن درست کتابخانه رسانه در متاباکس‌های گوتنبرگ.
	 *
	 * در ویرایشگر بلوک، متاباکس‌ها در iframe رندر می‌شوند. این اسکریپت تضمین می‌کند
	 * که wp.media حتی اگر در والد مقداردهی نشده باشد، پیش از کلیک کاربر آماده باشد.
	 */
	public static function inline_media_bridge() {
		$screen = get_current_screen();

		if ( ! $screen || ! isset( $screen->post_type ) || ! in_array( $screen->post_type, self::$post_types, true ) ) {
			return;
		}
		?>
		<script type="text/javascript">
			( function () {
				if ( window.wp && window.wp.media ) {
					return;
				}
				// اگر wp.media در دسترس نیست، دکمه‌های رسانه به‌صورت امن غیرفعال می‌شوند
				// تا هرگز خطای جاوااسکریپت در فرم رخ ندهد.
				window.addEventListener( 'load', function () {
					if ( window.wp && window.wp.media ) {
						return;
					}
					document.querySelectorAll( '.abidar-media-picker__select, .abidar-gallery__add' ).forEach( function ( btn ) {
						btn.disabled = true;
						btn.setAttribute( 'title', 'کتابخانه رسانه بارگذاری نشد؛ صفحه را دوباره بارگذاری کنید.' );
					} );
				} );
			} )();
		</script>
		<?php
	}

	/**
	 * نوار ابزار بالای عنوان.
	 *
	 * @param WP_Post $post شیء پست.
	 */
	public static function render_toolbar( $post ) {
		if ( ! in_array( $post->post_type, self::$post_types, true ) ) {
			return;
		}
		?>
		<div class="abidar-editor-toolbar">
			<span class="abidar-editor-toolbar__badge">ABIDAR</span>
			<span class="abidar-editor-toolbar__text">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: post type label */
						__( 'ویرایش حرفه‌ای %s — از تب‌های زیر برای تکمیل محتوا، رسانه و مشخصات فنی استفاده کنید.', 'abidar-group' ),
						self::get_type_label( $post->post_type )
					)
				);
				?>
			</span>
		</div>
		<?php
	}

	/**
	 * برچسب فارسی هر پست‌تایپ.
	 *
	 * @param string $post_type نوع پست.
	 * @return string
	 */
	private static function get_type_label( $post_type ) {
		$labels = array(
			'abidar_service'     => 'خدمت هلدینگ',
			'abidar_project'     => 'پروژه صنعتی',
			'abidar_team'        => 'عضو تیم',
			'abidar_certificate' => 'مدرک و گواهینامه',
		);

		return isset( $labels[ $post_type ] ) ? $labels[ $post_type ] : '';
	}

	/**
	 * رندر پنل اصلی.
	 *
	 * @param WP_Post $post شیء پست.
	 */
	public static function render_panel( $post ) {
		wp_nonce_field( 'abidar_pro_editor_save', 'abidar_pro_editor_nonce' );

		$type = $post->post_type;
		$m    = self::get_all_meta( $post->ID );
		?>
		<?php self::render_toolbar( $post ); ?>
		<div class="abidar-editor" data-post-type="<?php echo esc_attr( $type ); ?>">

			<nav class="abidar-editor__tabs" role="tablist">
				<button type="button" class="abidar-editor__tab is-active" data-tab="content" role="tab">
					<span class="dashicons dashicons-edit"></span><?php esc_html_e( 'محتوا و متن', 'abidar-group' ); ?>
				</button>
				<button type="button" class="abidar-editor__tab" data-tab="media" role="tab">
					<span class="dashicons dashicons-format-gallery"></span><?php esc_html_e( 'رسانه و ویدیو', 'abidar-group' ); ?>
				</button>
				<button type="button" class="abidar-editor__tab" data-tab="specs" role="tab">
					<span class="dashicons dashicons-clipboard"></span><?php esc_html_e( 'مشخصات فنی', 'abidar-group' ); ?>
				</button>
				<button type="button" class="abidar-editor__tab" data-tab="i18n" role="tab">
					<span class="dashicons dashicons-translation"></span><?php esc_html_e( 'چندزبانه', 'abidar-group' ); ?>
				</button>
				<button type="button" class="abidar-editor__tab" data-tab="options" role="tab">
					<span class="dashicons dashicons-admin-generic"></span><?php esc_html_e( 'تنظیمات نمایش', 'abidar-group' ); ?>
				</button>
			</nav>

			<!-- ================= تب ۱: محتوا ================= -->
			<section class="abidar-editor__panel is-active" data-panel="content">
				<div class="abidar-editor__grid">
					<div class="abidar-editor__main">
						<div class="abidar-field">
							<label class="abidar-field__label" for="abidar_subtitle">
								<?php esc_html_e( 'زیرعنوان / شعار کوتاه', 'abidar-group' ); ?>
							</label>
							<input type="text" id="abidar_subtitle" name="abidar_meta[subtitle]"
								value="<?php echo esc_attr( $m['subtitle'] ); ?>"
								placeholder="<?php esc_attr_e( 'مثلاً: راهکار یکپارچه فرآوری مواد معدنی', 'abidar-group' ); ?>" />
						</div>

						<div class="abidar-field">
							<label class="abidar-field__label" for="abidar_summary">
								<?php esc_html_e( 'خلاصه کارت (در گریدها نمایش داده می‌شود)', 'abidar-group' ); ?>
							</label>
							<textarea id="abidar_summary" name="abidar_meta[summary]" rows="4" maxlength="400"
								placeholder="<?php esc_attr_e( 'حداکثر ۲ تا ۳ جمله جذاب برای نمایش در کارت‌ها...', 'abidar-group' ); ?>"><?php echo esc_textarea( $m['summary'] ); ?></textarea>
							<p class="abidar-field__hint"><?php esc_html_e( 'اگر خالی بماند، از چکیده نوشته استفاده می‌شود.', 'abidar-group' ); ?></p>
						</div>

						<div class="abidar-field">
							<label class="abidar-field__label" for="abidar_highlights">
								<?php esc_html_e( 'نکات کلیدی (هر خط یک مورد)', 'abidar-group' ); ?>
							</label>
							<textarea id="abidar_highlights" name="abidar_meta[highlights]" rows="4"
								placeholder="<?php esc_attr_e( "زنجیره ارزش یکپارچه\nاستانداردهای ISO 9001 / 14001 / 45001\nتیم مهندسی ۳۴۰ نفره", 'abidar-group' ); ?>"><?php echo esc_textarea( $m['highlights'] ); ?></textarea>
						</div>
					</div>

					<aside class="abidar-editor__preview">
						<h4><?php esc_html_e( 'پیش‌نمایش کارت', 'abidar-group' ); ?></h4>
						<div class="abidar-preview-card">
							<div class="abidar-preview-card__thumb" data-preview-thumb>
								<span class="abidar-preview-card__placeholder dashicons dashicons-format-image"></span>
							</div>
							<div class="abidar-preview-card__body">
								<span class="abidar-preview-card__badge" data-preview-sector>
									<?php echo esc_html( self::get_sector_label( $m['sector'] ) ); ?>
								</span>
								<strong class="abidar-preview-card__title" data-preview-title>
									<?php echo esc_html( get_the_title( $post ) ); ?>
								</strong>
								<p class="abidar-preview-card__desc" data-preview-summary>
									<?php echo esc_html( $m['summary'] ? $m['summary'] : 'خلاصه کارت اینجا نمایش داده می‌شود…' ); ?>
								</p>
							</div>
						</div>
						<p class="abidar-field__hint">
							<?php esc_html_e( 'برای تعیین تصویر شاخص از باکس «تصویر شاخص» در ستون کنار صفحه استفاده کنید.', 'abidar-group' ); ?>
						</p>
					</aside>
				</div>
			</section>

			<!-- ================= تب ۲: رسانه ================= -->
			<section class="abidar-editor__panel" data-panel="media">
				<div class="abidar-media-grid">

					<div class="abidar-media-field">
						<label class="abidar-field__label"><?php esc_html_e( 'ویدیو معرفی (MP4 یا لینک)', 'abidar-group' ); ?></label>
						<div class="abidar-media-picker" data-picker="video">
							<input type="hidden" name="abidar_meta[video_id]" data-media-id value="<?php echo esc_attr( $m['video_id'] ); ?>" />
							<input type="url" class="abidar-media-picker__url" name="abidar_meta[video_url]"
								value="<?php echo esc_url( $m['video_url'] ); ?>"
								placeholder="https://www.youtube.com/watch?v=... یا آدرس فایل MP4" dir="ltr" />
							<div class="abidar-media-picker__actions">
								<button type="button" class="button abidar-media-picker__select" data-media-type="video">
									<?php esc_html_e( 'انتخاب از کتابخانه', 'abidar-group' ); ?>
								</button>
								<button type="button" class="button-link abidar-media-picker__clear" data-media-type="video">
									<?php esc_html_e( 'حذف', 'abidar-group' ); ?>
								</button>
							</div>
							<div class="abidar-media-picker__preview" data-media-preview></div>
						</div>
					</div>

					<div class="abidar-media-field">
						<label class="abidar-field__label"><?php esc_html_e( 'گالری تصاویر پروژه / خدمت', 'abidar-group' ); ?></label>
						<div class="abidar-gallery" data-gallery>
							<input type="hidden" name="abidar_meta[gallery]" data-gallery-input value="<?php echo esc_attr( $m['gallery'] ); ?>" />
							<div class="abidar-gallery__items" data-gallery-items>
								<?php foreach ( self::parse_ids( $m['gallery'] ) as $img_id ) : ?>
									<?php self::render_gallery_item( $img_id ); ?>
								<?php endforeach; ?>
							</div>
							<button type="button" class="button button-secondary abidar-gallery__add">
								<span class="dashicons dashicons-plus-alt2"></span>
								<?php esc_html_e( 'افزودن تصویر به گالری', 'abidar-group' ); ?>
							</button>
							<p class="abidar-field__hint"><?php esc_html_e( 'می‌توانید ترتیب تصاویر را با کشیدن و رها کردن تغییر دهید.', 'abidar-group' ); ?></p>
						</div>
					</div>

					<div class="abidar-media-field">
						<label class="abidar-field__label"><?php esc_html_e( 'فایل PDF / بروشور / کاتالوگ', 'abidar-group' ); ?></label>
						<div class="abidar-media-picker" data-picker="file">
							<input type="hidden" name="abidar_meta[pdf_id]" data-media-id value="<?php echo esc_attr( $m['pdf_id'] ); ?>" />
							<input type="url" class="abidar-media-picker__url" name="abidar_meta[pdf_url]"
								value="<?php echo esc_url( $m['pdf_url'] ); ?>"
								placeholder="<?php esc_attr_e( 'آدرس فایل PDF', 'abidar-group' ); ?>" dir="ltr" />
							<div class="abidar-media-picker__actions">
								<button type="button" class="button abidar-media-picker__select" data-media-type="file">
									<?php esc_html_e( 'انتخاب از کتابخانه', 'abidar-group' ); ?>
								</button>
								<button type="button" class="button-link abidar-media-picker__clear" data-media-type="file">
									<?php esc_html_e( 'حذف', 'abidar-group' ); ?>
								</button>
							</div>
							<div class="abidar-media-picker__preview" data-media-preview></div>
						</div>
					</div>

					<?php if ( 'abidar_team' === $type ) : ?>
						<div class="abidar-media-field">
							<label class="abidar-field__label"><?php esc_html_e( 'مدارک و رزومه (PDF)', 'abidar-group' ); ?></label>
							<div class="abidar-media-picker" data-picker="file">
								<input type="hidden" name="abidar_meta[resume_id]" data-media-id value="<?php echo esc_attr( $m['resume_id'] ); ?>" />
								<input type="url" class="abidar-media-picker__url" name="abidar_meta[resume_url]"
									value="<?php echo esc_url( $m['resume_url'] ); ?>" placeholder="<?php esc_attr_e( 'آدرس فایل رزومه', 'abidar-group' ); ?>" dir="ltr" />
								<div class="abidar-media-picker__actions">
									<button type="button" class="button abidar-media-picker__select" data-media-type="file"><?php esc_html_e( 'انتخاب از کتابخانه', 'abidar-group' ); ?></button>
									<button type="button" class="button-link abidar-media-picker__clear" data-media-type="file"><?php esc_html_e( 'حذف', 'abidar-group' ); ?></button>
								</div>
								<div class="abidar-media-picker__preview" data-media-preview></div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</section>

			<!-- ================= تب ۳: مشخصات فنی ================= -->
			<section class="abidar-editor__panel" data-panel="specs">
				<?php self::render_specs_panel( $type, $m ); ?>
			</section>

			<!-- ================= تب ۴: چندزبانه ================= -->
			<section class="abidar-editor__panel" data-panel="i18n">
				<p class="abidar-field__hint"><?php esc_html_e( 'عنوان و متن اصلی را در ویرایشگر بالای صفحه (فارسی) وارد کنید. برای زبان‌های دیگر از فیلدهای زیر استفاده کنید.', 'abidar-group' ); ?></p>
				<div class="abidar-editor__grid">
					<div class="abidar-editor__main">
						<div class="abidar-lang-block" dir="ltr">
							<h4>🇬🇧 English</h4>
							<div class="abidar-field">
								<label class="abidar-field__label">Title (EN)</label>
								<input type="text" name="abidar_meta[title_en]" value="<?php echo esc_attr( $m['title_en'] ); ?>" dir="ltr" />
							</div>
							<div class="abidar-field">
								<label class="abidar-field__label">Description (EN)</label>
								<textarea name="abidar_meta[desc_en]" rows="4" dir="ltr"><?php echo esc_textarea( $m['desc_en'] ); ?></textarea>
							</div>
						</div>
					</div>
					<div class="abidar-editor__main">
						<div class="abidar-lang-block" dir="ltr">
							<h4>🇨🇳 中文 (简体)</h4>
							<div class="abidar-field">
								<label class="abidar-field__label">Title (ZH)</label>
								<input type="text" name="abidar_meta[title_zh]" value="<?php echo esc_attr( $m['title_zh'] ); ?>" dir="ltr" />
							</div>
							<div class="abidar-field">
								<label class="abidar-field__label">Description (ZH)</label>
								<textarea name="abidar_meta[desc_zh]" rows="4" dir="ltr"><?php echo esc_textarea( $m['desc_zh'] ); ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- ================= تب ۵: تنظیمات نمایش ================= -->
			<section class="abidar-editor__panel" data-panel="options">
				<?php self::render_options_panel( $type, $m ); ?>
			</section>
		</div>
		<?php
	}

	/**
	 * پنل مشخصات فنی بر اساس نوع پست.
	 *
	 * @param string $type نوع پست.
	 * @param array  $m    متای فعلی.
	 */
	private static function render_specs_panel( $type, $m ) {
		?>
		<div class="abidar-editor__grid abidar-editor__grid--cols-2">

			<?php if ( 'abidar_project' === $type ) : ?>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'شناسنامه پروژه', 'abidar-group' ); ?></h4>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_client"><?php esc_html_e( 'کارفرما', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_client" name="abidar_meta[client]" value="<?php echo esc_attr( $m['client'] ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_location"><?php esc_html_e( 'موقعیت جغرافیایی', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_location" name="abidar_meta[location]" value="<?php echo esc_attr( $m['location'] ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_capacity"><?php esc_html_e( 'ظرفیت / حجم عملیات', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_capacity" name="abidar_meta[capacity]" value="<?php echo esc_attr( $m['capacity'] ); ?>" placeholder="<?php esc_attr_e( 'مثلاً ۴٬۲۰۰ مگاوات', 'abidar-group' ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_year"><?php esc_html_e( 'سال اجرا / بهره‌برداری', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_year" name="abidar_meta[year]" value="<?php echo esc_attr( $m['year'] ); ?>" placeholder="2024 - 2026" dir="ltr" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_progress">
							<?php esc_html_e( 'پیشرفت پروژه (درصد)', 'abidar-group' ); ?>
							<output class="abidar-range-value" data-range-out><?php echo esc_html( $m['progress'] ? $m['progress'] : 0 ); ?>%</output>
						</label>
						<input type="range" id="abidar_progress" name="abidar_meta[progress]" min="0" max="100" step="1"
							value="<?php echo esc_attr( $m['progress'] ? $m['progress'] : 0 ); ?>" data-range-input />
					</div>
				</div>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'مشخصات فنی تکمیلی', 'abidar-group' ); ?></h4>
					<?php self::render_repeater( 'specs', $m['specs'], array( 'label' => 'عنوان مشخصه', 'value' => 'مقدار' ) ); ?>
					<h4 class="abidar-group-title"><?php esc_html_e( 'دستاوردها و نتایج', 'abidar-group' ); ?></h4>
					<?php self::render_repeater( 'achievements', $m['achievements'], array( 'label' => 'عنوان دستاورد', 'value' => 'توضیح کوتاه' ) ); ?>
				</div>

			<?php elseif ( 'abidar_service' === $type ) : ?>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'شاخص کلیدی خدمت', 'abidar-group' ); ?></h4>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_metric"><?php esc_html_e( 'متریک / عدد شاخص', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_metric" name="abidar_meta[metric]" value="<?php echo esc_attr( $m['metric'] ); ?>" placeholder="<?php esc_attr_e( 'مثلاً +۲۴ پروژه فعال', 'abidar-group' ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_icon"><?php esc_html_e( 'آیکون (کلاس Dashicon یا ایموجی)', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_icon" name="abidar_meta[icon]" value="<?php echo esc_attr( $m['icon'] ); ?>" placeholder="dashicons-admin-tools یا ⛏️" dir="ltr" />
					</div>
					<h4 class="abidar-group-title"><?php esc_html_e( 'مزیت‌های خدمت', 'abidar-group' ); ?></h4>
					<?php self::render_repeater( 'features', $m['features'], array( 'label' => 'عنوان مزیت', 'value' => 'توضیح' ) ); ?>
				</div>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'فرآیند اجرا (گام‌به‌گام)', 'abidar-group' ); ?></h4>
					<?php self::render_repeater( 'steps', $m['steps'], array( 'label' => 'عنوان گام', 'value' => 'شرح گام' ) ); ?>
				</div>

			<?php elseif ( 'abidar_team' === $type ) : ?>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'مشخصات سازمانی', 'abidar-group' ); ?></h4>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_role_fa"><?php esc_html_e( 'سمت سازمانی (فارسی)', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_role_fa" name="abidar_meta[role_fa]" value="<?php echo esc_attr( $m['role_fa'] ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_dept_fa"><?php esc_html_e( 'معاونت / دپارتمان', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_dept_fa" name="abidar_meta[dept_fa]" value="<?php echo esc_attr( $m['dept_fa'] ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_education"><?php esc_html_e( 'مدرک تحصیلی', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_education" name="abidar_meta[education]" value="<?php echo esc_attr( $m['education'] ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_experience"><?php esc_html_e( 'سابقه (سال)', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_experience" name="abidar_meta[experience]" value="<?php echo esc_attr( $m['experience'] ); ?>" />
					</div>
				</div>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'ارتباط و شبکه‌های اجتماعی', 'abidar-group' ); ?></h4>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_email"><?php esc_html_e( 'ایمیل سازمانی', 'abidar-group' ); ?></label>
						<input type="email" id="abidar_email" name="abidar_meta[email]" value="<?php echo esc_attr( $m['email'] ); ?>" dir="ltr" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_linkedin"><?php esc_html_e( 'لینکدین', 'abidar-group' ); ?></label>
						<input type="url" id="abidar_linkedin" name="abidar_meta[linkedin]" value="<?php echo esc_url( $m['linkedin'] ); ?>" dir="ltr" />
					</div>
					<h4 class="abidar-group-title"><?php esc_html_e( 'حوزه‌های تخصصی', 'abidar-group' ); ?></h4>
					<?php self::render_repeater( 'expertise', $m['expertise'], array( 'label' => 'عنوان تخصص', 'value' => 'توضیح' ) ); ?>
				</div>

			<?php elseif ( 'abidar_certificate' === $type ) : ?>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'اطلاعات گواهینامه', 'abidar-group' ); ?></h4>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_cert_issuer"><?php esc_html_e( 'مرجع صادرکننده', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_cert_issuer" name="abidar_meta[cert_issuer]" value="<?php echo esc_attr( $m['cert_issuer'] ); ?>" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_cert_code"><?php esc_html_e( 'شماره مدرک', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_cert_code" name="abidar_meta[cert_code]" value="<?php echo esc_attr( $m['cert_code'] ); ?>" dir="ltr" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_cert_year"><?php esc_html_e( 'بازه اعتبار', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_cert_year" name="abidar_meta[cert_year]" value="<?php echo esc_attr( $m['cert_year'] ); ?>" placeholder="2025 - 2028" dir="ltr" />
					</div>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_cert_verify_url"><?php esc_html_e( 'لینک استعلام اصالت', 'abidar-group' ); ?></label>
						<input type="url" id="abidar_cert_verify_url" name="abidar_meta[cert_verify_url]" value="<?php echo esc_url( $m['cert_verify_url'] ); ?>" dir="ltr" />
					</div>
				</div>
				<div class="abidar-editor__main">
					<h4 class="abidar-group-title"><?php esc_html_e( 'دامنه پوشش استاندارد', 'abidar-group' ); ?></h4>
					<?php self::render_repeater( 'scope', $m['scope'], array( 'label' => 'عنوان حوزه', 'value' => 'توضیح' ) ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * پنل تنظیمات نمایش.
	 *
	 * @param string $type نوع پست.
	 * @param array  $m    متای فعلی.
	 */
	private static function render_options_panel( $type, $m ) {
		?>
		<div class="abidar-editor__grid abidar-editor__grid--cols-2">
			<div class="abidar-editor__main">
				<h4 class="abidar-group-title"><?php esc_html_e( 'حوزه فعالیت', 'abidar-group' ); ?></h4>
				<div class="abidar-field">
					<div class="abidar-sector-picker">
						<?php
						$sectors = array(
							'mining' => array( '⛏️', 'معادن و صنایع معدنی' ),
							'energy' => array( '⚡', 'انرژی و نیروگاه‌ها' ),
							'oil'    => array( '🛢️', 'نفت، گاز و پتروشیمی' ),
						);
						foreach ( $sectors as $key => $info ) :
							?>
							<label class="abidar-sector-picker__item <?php echo ( $m['sector'] === $key ) ? 'is-checked' : ''; ?>">
								<input type="radio" name="abidar_meta[sector]" value="<?php echo esc_attr( $key ); ?>"
									<?php checked( $m['sector'], $key ); ?> />
								<span class="abidar-sector-picker__icon"><?php echo esc_html( $info[0] ); ?></span>
								<span class="abidar-sector-picker__label"><?php echo esc_html( $info[1] ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( 'abidar_certificate' === $type ) : ?>
					<div class="abidar-field">
						<label class="abidar-field__label" for="abidar_badge"><?php esc_html_e( 'برچسب نشان (ISO 9001 …)', 'abidar-group' ); ?></label>
						<input type="text" id="abidar_badge" name="abidar_meta[badge]" value="<?php echo esc_attr( $m['badge'] ); ?>" dir="ltr" />
					</div>
				<?php endif; ?>

				<div class="abidar-field">
					<label class="abidar-field__label" for="abidar_order"><?php esc_html_e( 'ترتیب نمایش (عدد کوچک‌تر = بالاتر)', 'abidar-group' ); ?></label>
					<input type="number" id="abidar_order" name="abidar_meta[order]" value="<?php echo esc_attr( $m['order'] ? $m['order'] : 0 ); ?>" min="0" max="999" dir="ltr" />
				</div>
			</div>

			<div class="abidar-editor__main">
				<h4 class="abidar-group-title"><?php esc_html_e( 'نوع نمایش در صفحات', 'abidar-group' ); ?></h4>

				<label class="abidar-switch">
					<input type="checkbox" name="abidar_meta[featured]" value="1" <?php checked( $m['featured'], '1' ); ?> />
					<span class="abidar-switch__track"></span>
					<span class="abidar-switch__label"><?php esc_html_e( 'نمایش در بخش «برجسته» صفحه اصلی', 'abidar-group' ); ?></span>
				</label>

				<label class="abidar-switch">
					<input type="checkbox" name="abidar_meta[show_on_home]" value="1" <?php checked( $m['show_on_home'], '1' ); ?> />
					<span class="abidar-switch__track"></span>
					<span class="abidar-switch__label"><?php esc_html_e( 'نمایش در نوار شاخص‌های صفحه اصلی', 'abidar-group' ); ?></span>
				</label>

				<label class="abidar-switch">
					<input type="checkbox" name="abidar_meta[has_video]" value="1" <?php checked( $m['has_video'], '1' ); ?> />
					<span class="abidar-switch__track"></span>
					<span class="abidar-switch__label"><?php esc_html_e( 'نمایش دکمه پخش ویدیو روی تصویر شاخص', 'abidar-group' ); ?></span>
				</label>

				<div class="abidar-field">
					<label class="abidar-field__label" for="abidar_badge_text"><?php esc_html_e( 'برچسب روی تصویر (Badge)', 'abidar-group' ); ?></label>
					<input type="text" id="abidar_badge_text" name="abidar_meta[badge_text]" value="<?php echo esc_attr( $m['badge_text'] ); ?>" placeholder="<?php esc_attr_e( 'مثلاً: پروژه شاخص ۱۴۰۴', 'abidar-group' ); ?>" />
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * رندر یک ریپیتر حرفه‌ای.
	 *
	 * @param string $name   نام فیلد.
	 * @param array  $rows   ردیف‌های موجود.
	 * @param array  $labels برچسب ستون‌ها.
	 */
	private static function render_repeater( $name, $rows, $labels ) {
		$rows = is_array( $rows ) ? $rows : array();
		?>
		<div class="abidar-repeater" data-repeater="<?php echo esc_attr( $name ); ?>">
			<div class="abidar-repeater__rows" data-repeater-rows>
				<?php
				if ( empty( $rows ) ) {
					$rows = array(
						array(
							'label' => '',
							'value' => '',
						),
					);
				}
				foreach ( $rows as $row ) :
					$row = wp_parse_args( $row, array( 'label' => '', 'value' => '' ) );
					?>
					<div class="abidar-repeater__row">
						<span class="abidar-repeater__handle dashicons dashicons-menu" title="<?php esc_attr_e( 'جابه‌جایی', 'abidar-group' ); ?>"></span>
						<input type="text" class="abidar-repeater__label" name="abidar_meta[<?php echo esc_attr( $name ); ?>][label][]"
							value="<?php echo esc_attr( $row['label'] ); ?>"
							placeholder="<?php echo esc_attr( $labels['label'] ); ?>" />
						<input type="text" class="abidar-repeater__value" name="abidar_meta[<?php echo esc_attr( $name ); ?>][value][]"
							value="<?php echo esc_attr( $row['value'] ); ?>"
							placeholder="<?php echo esc_attr( $labels['value'] ); ?>" />
						<button type="button" class="abidar-repeater__remove" title="<?php esc_attr_e( 'حذف ردیف', 'abidar-group' ); ?>">
							<span class="dashicons dashicons-trash"></span>
						</button>
					</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button abidar-repeater__add">
				<span class="dashicons dashicons-plus-alt2"></span>
				<?php esc_html_e( 'افزودن ردیف', 'abidar-group' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * رندر یک آیتم گالری.
	 *
	 * @param int $attachment_id شناسه پیوست.
	 */
	private static function render_gallery_item( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		if ( $attachment_id <= 0 ) {
			return;
		}
		$thumb = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
		if ( ! $thumb ) {
			return;
		}
		?>
		<div class="abidar-gallery__item" data-id="<?php echo esc_attr( $attachment_id ); ?>">
			<img src="<?php echo esc_url( $thumb ); ?>" alt="" />
			<button type="button" class="abidar-gallery__remove" aria-label="<?php esc_attr_e( 'حذف تصویر', 'abidar-group' ); ?>">×</button>
		</div>
		<?php
	}

	/**
	 * دریافت تمام متای پنل با مقادیر پیش‌فرض.
	 *
	 * @param int $post_id شناسه پست.
	 * @return array
	 */
	private static function get_all_meta( $post_id ) {
		$defaults = array(
			'subtitle'        => '',
			'summary'         => '',
			'highlights'      => '',
			'sector'          => 'mining',
			'order'           => 0,
			'featured'        => '0',
			'show_on_home'    => '0',
			'has_video'       => '0',
			'badge'           => '',
			'badge_text'      => '',
			'video_id'        => '',
			'video_url'       => '',
			'gallery'         => '',
			'pdf_id'          => '',
			'pdf_url'         => '',
			'resume_id'       => '',
			'resume_url'      => '',
			'title_en'        => '',
			'title_zh'        => '',
			'desc_en'         => '',
			'desc_zh'         => '',
			'client'          => '',
			'location'        => '',
			'capacity'        => '',
			'year'            => '',
			'progress'        => 0,
			'metric'          => '',
			'icon'            => '',
			'role_fa'         => '',
			'dept_fa'         => '',
			'education'       => '',
			'experience'      => '',
			'email'           => '',
			'linkedin'        => '',
			'cert_issuer'     => '',
			'cert_code'       => '',
			'cert_year'       => '',
			'cert_verify_url' => '',
			'specs'           => array(),
			'achievements'    => array(),
			'features'        => array(),
			'steps'           => array(),
			'expertise'       => array(),
			'scope'           => array(),
		);

		$meta = array();
		foreach ( $defaults as $key => $default ) {
			$meta[ $key ] = get_post_meta( $post_id, '_abidar_' . $key, true );
			if ( '' === $meta[ $key ] ) {
				$meta[ $key ] = $default;
			}
		}

		return $meta;
	}

	/**
	 * تبدیل رشته شناسه‌های جداشده با کاما به آرایه.
	 *
	 * @param string $ids رشته شناسه‌ها.
	 * @return array
	 */
	private static function parse_ids( $ids ) {
		if ( empty( $ids ) ) {
			return array();
		}

		return array_filter( array_map( 'absint', explode( ',', $ids ) ) );
	}

	/**
	 * برچسب فارسی حوزه فعالیت.
	 *
	 * @param string $sector کلید حوزه.
	 * @return string
	 */
	private static function get_sector_label( $sector ) {
		$labels = array(
			'mining' => '⛏️ معادن',
			'energy' => '⚡ انرژی',
			'oil'    => '🛢️ نفت و گاز',
		);

		return isset( $labels[ $sector ] ) ? $labels[ $sector ] : '⛏️ معادن';
	}

	/**
	 * ذخیره امن تمام فیلدهای پنل.
	 *
	 * @param int     $post_id شناسه پست.
	 * @param WP_Post $post    شیء پست.
	 * @param bool    $update  آیا به‌روزرسانی است.
	 */
	public static function save( $post_id, $post, $update ) {
		if ( ! isset( $_POST['abidar_pro_editor_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abidar_pro_editor_nonce'] ) ), 'abidar_pro_editor_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! in_array( $post->post_type, self::$post_types, true ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw = isset( $_POST['abidar_meta'] ) && is_array( $_POST['abidar_meta'] )
			? wp_unslash( $_POST['abidar_meta'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();

		// ---- فیلدهای متنی ساده ----
		$text_keys = array(
			'subtitle', 'sector', 'badge', 'badge_text', 'video_url', 'gallery',
			'pdf_url', 'resume_url', 'title_en', 'title_zh', 'client', 'location',
			'capacity', 'year', 'metric', 'icon', 'role_fa', 'dept_fa', 'education',
			'experience', 'email', 'cert_issuer', 'cert_code', 'cert_year',
			'linkedin', 'cert_verify_url',
		);

		foreach ( $text_keys as $key ) {
			if ( ! isset( $raw[ $key ] ) ) {
				continue;
			}
			$value = $raw[ $key ];

			if ( in_array( $key, array( 'email' ), true ) ) {
				$value = sanitize_email( $value );
			} elseif ( in_array( $key, array( 'video_url', 'pdf_url', 'resume_url', 'linkedin', 'cert_verify_url' ), true ) ) {
				$value = esc_url_raw( $value );
			} elseif ( in_array( $key, array( 'gallery', 'video_id', 'pdf_id', 'resume_id' ), true ) ) {
				$value = implode( ',', self::parse_ids( $value ) );
			} elseif ( 'sector' === $key ) {
				$value = in_array( $value, array( 'mining', 'energy', 'oil' ), true ) ? $value : 'mining';
			} else {
				$value = sanitize_text_field( $value );
			}

			update_post_meta( $post_id, '_abidar_' . $key, $value );
		}

		// ---- شناسه رسانه‌ها ----
		foreach ( array( 'video_id', 'pdf_id', 'resume_id' ) as $key ) {
			if ( isset( $raw[ $key ] ) ) {
				update_post_meta( $post_id, '_abidar_' . $key, absint( $raw[ $key ] ) );
			}
		}

		// ---- متون بلند ----
		foreach ( array( 'summary', 'highlights', 'desc_en', 'desc_zh' ) as $key ) {
			if ( isset( $raw[ $key ] ) ) {
				update_post_meta( $post_id, '_abidar_' . $key, sanitize_textarea_field( $raw[ $key ] ) );
			}
		}

		// ---- عددها ----
		if ( isset( $raw['progress'] ) ) {
			$progress = absint( $raw['progress'] );
			update_post_meta( $post_id, '_abidar_progress', min( 100, $progress ) );
		}
		if ( isset( $raw['order'] ) ) {
			update_post_meta( $post_id, '_abidar_order', absint( $raw['order'] ) );
		}

		// ---- چک‌باکس‌ها ----
		foreach ( array( 'featured', 'show_on_home', 'has_video' ) as $key ) {
			update_post_meta( $post_id, '_abidar_' . $key, isset( $raw[ $key ] ) ? '1' : '0' );
		}

		// ---- ریپیترها ----
		$repeaters = array( 'specs', 'achievements', 'features', 'steps', 'expertise', 'scope' );
		foreach ( $repeaters as $name ) {
			$rows = array();
			if ( isset( $raw[ $name ]['label'] ) && is_array( $raw[ $name ]['label'] ) ) {
				$labels = $raw[ $name ]['label'];
				$values = isset( $raw[ $name ]['value'] ) && is_array( $raw[ $name ]['value'] ) ? $raw[ $name ]['value'] : array();

				foreach ( $labels as $i => $label ) {
					$label = sanitize_text_field( $label );
					$value = isset( $values[ $i ] ) ? sanitize_text_field( $values[ $i ] ) : '';
					if ( '' === $label && '' === $value ) {
						continue;
					}
					$rows[] = array(
						'label' => $label,
						'value' => $value,
					);
				}
			}
			update_post_meta( $post_id, '_abidar_' . $name, $rows );
		}

		// چکیده پست را با summary همگام کن.
		$summary = get_post_meta( $post_id, '_abidar_summary', true );
		if ( $summary && '' === trim( (string) $post->post_excerpt ) ) {
			remove_action( 'save_post', array( __CLASS__, 'save' ), 20 );
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_excerpt' => wp_trim_words( $summary, 40 ),
				)
			);
			add_action( 'save_post', array( __CLASS__, 'save' ), 20, 3 );
		}

		/**
		 * امکان اجرای کد اضافی پس از ذخیره پنل حرفه‌ای.
		 *
		 * @param int     $post_id شناسه پست.
		 * @param WP_Post $post    شیء پست.
		 */
		do_action( 'abidar_after_save_pro_editor', $post_id, $post );
	}
}

Abidar_CPT_Editor::init();
