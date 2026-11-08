<?php
/**
 * Abidar Group Dynamic CSS Generator & Custom Font Loader (v2.0.0)
 *
 * تولید خودکار استایل‌های پویا بر اساس تنظیمات طرح، چیدمان، عرض، رنگ‌ها، سایزها و فونت اختصاصی آپلودشده در پیشخوان
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Dynamic_CSS
 */
class Abidar_Dynamic_CSS {

	/**
	 * تولید رشته CSS پویا برای تزریق در هدر سایت
	 *
	 * @return string
	 */
	public static function generate() {
		$container_width   = max( 960, min( 1920, (int) abidar_get_option( 'container_width', 1320 ) ) );
		$grid_gap          = max( 12, min( 64, (int) abidar_get_option( 'grid_gap', 28 ) ) );
		$section_padding   = max( 40, min( 180, (int) abidar_get_option( 'section_padding', 96 ) ) );
		$primary_color     = sanitize_hex_color( abidar_get_option( 'primary_color', '#D97706' ) );
		$secondary_color   = sanitize_hex_color( abidar_get_option( 'secondary_color', '#0B192C' ) );
		$accent_color      = sanitize_hex_color( abidar_get_option( 'accent_color', '#0EA5E9' ) );
		$surface_dark      = sanitize_hex_color( abidar_get_option( 'surface_dark_color', '#07101E' ) );
		$surface_light     = sanitize_hex_color( abidar_get_option( 'surface_light_color', '#F8FAFC' ) );
		$text_main         = sanitize_hex_color( abidar_get_option( 'text_main_color', '#0F172A' ) );
		$border_radius     = max( 0, min( 40, (int) abidar_get_option( 'border_radius', 14 ) ) );
		$base_font_size    = max( 13, min( 22, (int) abidar_get_option( 'base_font_size', 16 ) ) );
		$h1_size           = max( 28, min( 72, (int) abidar_get_option( 'h1_font_size', 46 ) ) );
		$h2_size           = max( 22, min( 56, (int) abidar_get_option( 'h2_font_size', 34 ) ) );
		$h3_size           = max( 18, min( 40, (int) abidar_get_option( 'h3_font_size', 24 ) ) );

		$custom_font_name  = sanitize_text_field( abidar_get_option( 'custom_font_name', '' ) );
		$font_woff2        = esc_url_raw( abidar_get_option( 'custom_font_woff2_url', '' ) );
		$font_woff         = esc_url_raw( abidar_get_option( 'custom_font_woff_url', '' ) );
		$font_ttf          = esc_url_raw( abidar_get_option( 'custom_font_ttf_url', '' ) );

		$css = '';

		// بارگذاری فونت اختصاصی آپلودشده در پیشخوان وردپرس
		if ( ! empty( $custom_font_name ) && ( ! empty( $font_woff2 ) || ! empty( $font_woff ) || ! empty( $font_ttf ) ) ) {
			$sources = array();
			if ( ! empty( $font_woff2 ) ) {
				$sources[] = sprintf( 'url("%s") format("woff2")', esc_url( $font_woff2 ) );
			}
			if ( ! empty( $font_woff ) ) {
				$sources[] = sprintf( 'url("%s") format("woff")', esc_url( $font_woff ) );
			}
			if ( ! empty( $font_ttf ) ) {
				$sources[] = sprintf( 'url("%s") format("truetype")', esc_url( $font_ttf ) );
			}

			$css .= sprintf(
				'@font-face { font-family: "%1$s"; src: %2$s; font-weight: 100 900; font-style: normal; font-display: swap; }',
				esc_attr( $custom_font_name ),
				implode( ', ', $sources )
			);
			$primary_font_stack = sprintf( '"%s", "Vazirmatn", "Inter", "Noto Sans SC", sans-serif', esc_attr( $custom_font_name ) );
		} else {
			$lang = Abidar_I18n::get_current_lang();
			if ( 'zh' === $lang ) {
				$primary_font_stack = '"Noto Sans SC", "Inter", sans-serif';
			} elseif ( 'en' === $lang ) {
				$primary_font_stack = '"Inter", "Vazirmatn", sans-serif';
			} else {
				$font_fa            = sanitize_text_field( abidar_get_option( 'font_family_fa', 'Vazirmatn' ) );
				$primary_font_stack = sprintf( '"%s", "Inter", "Noto Sans SC", sans-serif', esc_attr( $font_fa ) );
			}
		}

		$css .= sprintf(
			':root {
				--abidar-container-width: %1$dpx;
				--abidar-grid-gap: %2$dpx;
				--abidar-section-padding: %3$dpx;
				--abidar-primary: %4$s;
				--abidar-secondary: %5$s;
				--abidar-accent: %6$s;
				--abidar-surface-dark: %7$s;
				--abidar-surface-light: %8$s;
				--abidar-text-main: %9$s;
				--abidar-radius-md: %10$dpx;
				--abidar-base-size: %11$dpx;
				--abidar-font-primary: %12$s;
				--abidar-font-heading: %12$s;
			}
			h1, .abidar-h1 { font-size: clamp(2rem, 3.6vw, %13$dpx); }
			h2, .abidar-h2 { font-size: clamp(1.6rem, 2.8vw, %14$dpx); }
			h3, .abidar-h3 { font-size: clamp(1.25rem, 2vw, %15$dpx); }',
			$container_width,
			$grid_gap,
			$section_padding,
			$primary_color ? $primary_color : '#D97706',
			$secondary_color ? $secondary_color : '#0B192C',
			$accent_color ? $accent_color : '#0EA5E9',
			$surface_dark ? $surface_dark : '#07101E',
			$surface_light ? $surface_light : '#F8FAFC',
			$text_main ? $text_main : '#0F172A',
			$border_radius,
			$base_font_size,
			$primary_font_stack,
			$h1_size,
			$h2_size,
			$h3_size
		);

		return $css;
	}
}
