<?php
/**
 * Abidar Group Holding — Main Header Template (v2.0.0)
 *
 * هدر اصلی قالب: شامل پیش‌بارگذاری، هدر ساخته‌شده با المنتور یا ویجت اختصاصی،
 * ناوبری موبایل و دکمه شناور ارتباط چندبخشی؛ همه از پیشخوان قابل تنظیم هستند.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#abidar-main-content">
	<?php esc_html_e( 'پرش به محتوای اصلی', 'abidar-group' ); ?>
</a>

<?php
// پیش‌بارگذاری سایت طبق تنظیمات پیشخوان (در صورت فعال بودن).
Abidar_Preloader::render();

// هدر: اگر کاربر هدر اختصاصی المنتور ساخته باشد رندر می‌شود، در غیر این صورت ویجت هدر خود قالب.
Abidar_Elementor_Init::render_site_header();
?>

<main id="abidar-main-content" class="abidar-main" role="main" tabindex="-1">
	<?php
	// نوار پیشرفت مطالعه در صفحات مقاله تک‌نویس.
	if ( is_singular( 'post' ) && abidar_get_option( 'post_reading_progress', true ) ) :
		?>
		<div class="abidar-reading-progress" role="presentation" aria-hidden="true">
			<span class="abidar-reading-progress__bar"></span>
		</div>
	<?php endif; ?>
