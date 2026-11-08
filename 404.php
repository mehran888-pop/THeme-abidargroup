<?php
/**
 * Abidar Group Holding — 404 Not Found Template (v2.0.0)
 *
 * قالب خطای ۴۰۴ با مسیرهای پیشنهادی حرفه‌ای و جستجوی مستقیم.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="abidar-error-404">
	<div class="abidar-container abidar-error-404__inner">
		<div class="abidar-error-404__code" aria-hidden="true">404</div>
		<h1 class="abidar-error-404__title"><?php esc_html_e( 'صفحه مورد نظر یافت نشد', 'abidar-group' ); ?></h1>
		<p class="abidar-error-404__text">
			<?php esc_html_e( 'آدرس وارد شده در سایت گروه هلدینگ آبیدر وجود ندارد یا جابه‌جا شده است. می‌توانید از بخش‌های زیر مسیر خود را ادامه دهید.', 'abidar-group' ); ?>
		</p>

		<div class="abidar-search-box abidar-search-box--center">
			<?php get_search_form(); ?>
		</div>

		<div class="abidar-error-404__links">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="abidar-btn abidar-btn--gold"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
			<a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>" class="abidar-btn abidar-btn--glass"><?php echo esc_html( abidar_t( 'nav_services' ) ); ?></a>
			<a href="<?php echo esc_url( abidar_page_url( 'projects' ) ); ?>" class="abidar-btn abidar-btn--glass"><?php echo esc_html( abidar_t( 'nav_projects' ) ); ?></a>
			<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="abidar-btn abidar-btn--glass"><?php echo esc_html( abidar_t( 'nav_contact' ) ); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
