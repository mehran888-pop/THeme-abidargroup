<?php
/**
 * Abidar Group Professional Mobile Navigation System (v2.0.0)
 *
 * سیستم ناوبری حرفه‌ای موبایل با ۴ چیدمان متفاوت و امکان تعریف دکمه‌ها و لینک‌های سفارشی از پیشخوان
 * چیدمان‌ها:
 * ۱. bottom_app_bar     (نوار ابزار پایین اپلیکیشنی)
 * ۲. fullscreen_overlay (منوی تمام‌صفحه صنعتی مدرن)
 * ۳. slide_drawer       (کشوی کناری چندلایه شرکتی)
 * ۴. floating_dock      (داک شیشه‌ای شناور کپسولی)
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Mobile_Nav
 */
class Abidar_Mobile_Nav {

	/**
	 * رندر آیکون SVG بر اساس نام آیکون
	 *
	 * @param string $icon نام آیکون.
	 * @return string
	 */
	public static function get_icon_svg( $icon ) {
		switch ( $icon ) {
			case 'home':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V9.5z"/></svg>';
			case 'layers':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>';
			case 'briefcase':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>';
			case 'award':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>';
			case 'users':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>';
			case 'phone':
			default:
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
		}
	}

	/**
	 * رندر کامل ناوبری موبایل (کشویی / تمام‌صفحه + نوار پایین اپلیکیشنی یا داک شناور)
	 */
	public static function render() {
		if ( ! abidar_get_option( 'mobile_nav_enabled', true ) ) {
			return;
		}

		$layout      = abidar_get_option( 'mobile_nav_layout', 'bottom_app_bar' );
		$items       = abidar_get_option( 'mobile_nav_items', array() );
		$cta_text    = abidar_multilang_val( abidar_get_all_options(), 'mobile_cta_text', abidar_t( 'cta_contact_us' ) );
		$cta_url     = abidar_get_option( 'mobile_cta_url', '#contact-section' );

		// آدرس‌های نسبی دکمه اصلی ناوبری موبایل را به آدرس کامل سایت تبدیل می‌کنیم.
		if ( ! empty( $cta_url ) && '/' === $cta_url[0] && '//' !== substr( $cta_url, 0, 2 ) ) {
			$cta_url = home_url( $cta_url );
		}
		$quick_phone = abidar_get_option( 'mobile_quick_phone', '+982188001122' );
		$languages   = Abidar_I18n::get_languages();
		$curr_lang   = Abidar_I18n::get_current_lang();
		?>
		<!-- ۱. پنل منوی موبایل (کشویی یا تمام‌صفحه) -->
		<div id="abidar-mobile-drawer" class="abidar-mobile-drawer abidar-mobile-drawer--<?php echo esc_attr( $layout ); ?>" aria-hidden="true">
			<div class="abidar-mobile-drawer__backdrop" data-close-mobile-nav></div>
			<div class="abidar-mobile-drawer__panel">
				<div class="abidar-mobile-drawer__header">
					<div class="abidar-mobile-drawer__brand">
						<span class="abidar-logo-mark">AG</span>
						<div>
							<strong><?php echo esc_html( abidar_t( 'holding_name' ) ); ?></strong>
							<small><?php echo esc_html( abidar_t( 'holding_tagline' ) ); ?></small>
						</div>
					</div>
					<button type="button" class="abidar-mobile-drawer__close" data-close-mobile-nav aria-label="<?php esc_attr_e( 'بستن منو', 'abidar-group' ); ?>">
						&times;
					</button>
				</div>

				<!-- تغییر زبان سریع در موبایل -->
				<div class="abidar-mobile-drawer__langs">
					<?php
					/*
					 * همان طرحی استفاده می‌شود که مدیر در تنظیمات قالب انتخاب کرده
					 * است تا ظاهر زبان در دسکتاپ و موبایل یکی باشد. در منوی موبایل
					 * حالت کشویی معنا ندارد (داخل کشو می‌شود منوی تودرتو)، پس همان
					 * زبان‌ها به شکل کپسولی نمایش داده می‌شوند.
					 */
					if ( class_exists( 'Abidar_Widget_Lang_Switcher' ) ) {
						$mobile_style = abidar_get_option( 'lang_switcher_style', 'pills_flag' );
						if ( in_array( $mobile_style, array( 'dropdown', 'dropdown_flags' ), true ) ) {
							$mobile_style = 'pills_flag';
						}
						Abidar_Widget_Lang_Switcher::render_output(
							array(
								'layout_style' => $mobile_style,
								'show_label'   => 'yes',
							)
						);
					} else {
						foreach ( $languages as $code => $lang_info ) :
							?>
							<a href="<?php echo esc_url( Abidar_I18n::get_language_url( $code ) ); ?>" class="abidar-lang-pill <?php echo ( $curr_lang === $code ) ? 'is-active' : ''; ?>">
								<span><?php echo esc_html( $lang_info['flag'] ); ?></span>
								<span><?php echo esc_html( $lang_info['native_name'] ); ?></span>
							</a>
							<?php
						endforeach;
					}
					?>
				</div>

				<!-- لینک‌های اصلی صفحات دمو -->
				<nav class="abidar-mobile-drawer__nav" aria-label="<?php esc_attr_e( 'ناوبری موبایل', 'abidar-group' ); ?>">
					<ul class="abidar-mobile-drawer__list">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'about-us' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_about' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_services' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'projects' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_projects' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'our-team' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_team' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'certificates' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_certificates' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'blog' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_blog' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_contact' ) ); ?></a></li>
					</ul>
				</nav>

				<!-- دکمه‌های عملیاتی قابل تنظیم در پیشخوان -->
				<div class="abidar-mobile-drawer__actions">
					<a href="<?php echo esc_url( $cta_url ); ?>" class="abidar-btn abidar-btn--gold abidar-btn--full">
						<?php echo esc_html( $cta_text ); ?>
					</a>
					<?php if ( ! empty( $quick_phone ) ) : ?>
						<a href="tel:<?php echo esc_attr( $quick_phone ); ?>" class="abidar-btn abidar-btn--outline abidar-btn--full">
							<?php echo self::get_icon_svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $quick_phone ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- ۲. نوار ناوبری پایین موبایل (Bottom App Bar / Floating Glass Dock) -->
		<?php if ( in_array( $layout, array( 'bottom_app_bar', 'floating_dock' ), true ) && ! empty( $items ) ) : ?>
			<nav class="abidar-mobile-bottom-bar abidar-mobile-bottom-bar--<?php echo esc_attr( $layout ); ?>" aria-label="<?php esc_attr_e( 'نوار ابزار سریع موبایل', 'abidar-group' ); ?>">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$title = abidar_multilang_val( $item, 'title', isset( $item['title_fa'] ) ? $item['title_fa'] : '' );
					$url   = isset( $item['url'] ) ? $item['url'] : '#';

					// تبدیل آدرس‌های نسبی ذخیره‌شده در تنظیمات به آدرس کامل و امن سایت.
					if ( ! empty( $url ) && '/' === $url[0] && '//' !== substr( $url, 0, 2 ) ) {
						$url = home_url( $url );
					}
					$icon  = isset( $item['icon'] ) ? $item['icon'] : 'home';
					$badge = isset( $item['badge'] ) ? $item['badge'] : '';
					?>
					<a href="<?php echo esc_url( $url ); ?>" class="abidar-mobile-bottom-bar__item">
						<span class="abidar-mobile-bottom-bar__icon">
							<?php echo self::get_icon_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php if ( ! empty( $badge ) ) : ?>
								<em class="abidar-mobile-bottom-bar__badge"><?php echo esc_html( $badge ); ?></em>
							<?php endif; ?>
						</span>
						<span class="abidar-mobile-bottom-bar__label"><?php echo esc_html( $title ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<?php
	}
}
