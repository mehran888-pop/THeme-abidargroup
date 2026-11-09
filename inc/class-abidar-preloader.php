<?php
/**
 * Abidar Group Site Preloader Renderer (v2.0.0)
 *
 * ماژول پیش‌بارگذاری سایت با ۴ طرح صنعتی و قابل تنظیم از پیشخوان وردپرس
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Preloader
 */
class Abidar_Preloader {

	/**
	 * رندر خروجی HTML پیش‌بارگذاری در ابتدای تگ body
	 */
	public static function render() {
		if ( ! abidar_get_option( 'preloader_enabled', true ) ) {
			return;
		}

		// در ویرایشگر/پیش‌نمایش المنتور هرگز پیش‌بارگذاری رندر نمی‌شود؛
		// لایه تمام‌صفحه پیش‌بارگذاری می‌تواند بوم ویرایشگر را بپوشاند و
		// نشانگر بارگذاری المنتور را در حالت انتظار نگه دارد.
		if ( class_exists( 'Abidar_Elementor_Init' ) && Abidar_Elementor_Init::is_elementor_editor_context() ) {
			return;
		}

		$style        = abidar_get_option( 'preloader_style', 'industrial_pulse' );
		$bg_color     = abidar_get_option( 'preloader_bg_color', '#07101E' );
		$accent_color = abidar_get_option( 'preloader_accent_color', '#D97706' );
		$lang         = Abidar_I18n::get_current_lang();
		$text         = abidar_get_option( 'preloader_text_' . $lang, abidar_get_option( 'preloader_text_fa', 'گروه هلدینگ آبیدر | معادن • انرژی • نفت' ) );
		?>
		<div id="abidar-preloader" class="abidar-preloader abidar-preloader--<?php echo esc_attr( $style ); ?>" style="--preloader-bg: <?php echo esc_attr( $bg_color ); ?>; --preloader-accent: <?php echo esc_attr( $accent_color ); ?>;" role="status" aria-live="polite">
			<div class="abidar-preloader__inner">
				<?php if ( 'rig_svg' === $style ) : ?>
					<div class="abidar-preloader__rig">
						<svg viewBox="0 0 120 120" width="88" height="88" fill="none" stroke="currentColor" stroke-width="2.5">
							<polygon points="60,10 95,105 25,105" stroke="var(--preloader-accent)" />
							<line x1="38" y1="70" x2="82" y2="70" stroke="#0EA5E9" />
							<line x1="46" y1="45" x2="74" y2="45" stroke="var(--preloader-accent)" />
							<circle cx="60" cy="10" r="5" fill="var(--preloader-accent)" />
						</svg>
					</div>
				<?php elseif ( 'minimal_gold' === $style ) : ?>
					<div class="abidar-preloader__emblem">
						<span class="abidar-preloader__monogram">AG</span>
					</div>
				<?php else : ?>
					<div class="abidar-preloader__rings">
						<span class="abidar-preloader__ring abidar-preloader__ring--outer"></span>
						<span class="abidar-preloader__ring abidar-preloader__ring--middle"></span>
						<span class="abidar-preloader__core">ABIDAR</span>
					</div>
				<?php endif; ?>

				<div class="abidar-preloader__brand">
					<?php echo esc_html( $text ); ?>
				</div>

				<div class="abidar-preloader__bar">
					<span class="abidar-preloader__bar-fill"></span>
				</div>
			</div>
		</div>
		<?php
	}
}
