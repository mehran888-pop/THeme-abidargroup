<?php
/**
 * Template Name: آبیدر — لندینگ پیج (افقی / عمودی)
 * Template Post Type: page
 *
 * قالب لندینگ پیج ارائه/معرفی هلدینگ.
 *
 * دو حالت اسکرول دارد که در ویرایشگر برگه انتخاب می‌شود:
 * - عمودی (Vertical): بخش‌ها پشت سر هم، اسکرول عادی.
 * - افقی (Horizontal): کل سایت افقی اسکرول می‌خورد تا بخش‌ها رو شوند.
 *
 * در حالت افقی: چرخ ماوس عمودی به اسکرول افقی تبدیل می‌شود، کلیدهای
 * جهت‌دار کار می‌کنند و نوار پیشرفت نمایش داده می‌شود. زیر ۱۰۲۴ پیکسل
 * خودکار به عمودی برمی‌گردد (اسکرول افقی در موبایل ممنوع است).
 *
 * @package Abidar_Group
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* phpcs:disable WordPress.NamingConventions.PrefixAllGlobals */
$abidar_landing_id    = get_the_ID();
$abidar_landing_meta  = Abidar_Landing::get_meta( $abidar_landing_id );
$abidar_scroll_mode   = $abidar_landing_meta['scroll'];
$abidar_snap          = $abidar_landing_meta['snap'];
$abidar_show_header   = $abidar_landing_meta['header'];
$abidar_show_footer   = $abidar_landing_meta['footer'];

/**
 * کلاس‌های بدنه. از بستار استفاده می‌شود تا اعلان تابع در فایل قالب
 * (که ممکن است دو بار بارگذاری شود) خطای تکرار تابع ندهد.
 */
$abidar_landing_body_class = static function ( $classes ) use ( $abidar_scroll_mode, $abidar_snap ) {
	$classes[] = 'abidar-landing';
	$classes[] = 'abidar-landing--' . $abidar_scroll_mode;
	$classes[] = 'abidar-landing--snap-' . $abidar_snap;

	return $classes;
};

add_filter( 'body_class', $abidar_landing_body_class );

if ( 'yes' === $abidar_show_header ) {
	get_header();
} else {
	// هدر حذف شده؛ فقط استایل/اسکریپت‌های ضروری وردپرس بارگذاری می‌شوند.
	wp_head();
}
?>

<main
	id="abidar-landing"
	class="abidar-landing__main abidar-landing__main--<?php echo esc_attr( $abidar_scroll_mode ); ?>"
	data-landing-mode="<?php echo esc_attr( $abidar_scroll_mode ); ?>"
	data-landing-snap="<?php echo esc_attr( $abidar_snap ); ?>"
>

	<?php if ( 'horizontal' === $abidar_scroll_mode ) : ?>

		<div class="abidar-landing__rail" data-landing-rail>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<section class="abidar-landing__panel" data-landing-panel>
					<div class="abidar-landing__panel-inner">
						<?php the_content(); ?>
					</div>
				</section>
				<?php
			endwhile;
			?>
		</div>

		<div class="abidar-landing__nav">
			<button type="button" class="abidar-landing__nav-btn" data-landing-prev aria-label="<?php esc_attr_e( 'بخش قبلی', 'abidar-group' ); ?>">
				<span aria-hidden="true">&#8249;</span>
			</button>

			<div class="abidar-landing__progress">
				<span class="abidar-landing__progress-bar" data-landing-progress-bar></span>
			</div>

			<button type="button" class="abidar-landing__nav-btn" data-landing-next aria-label="<?php esc_attr_e( 'بخش بعدی', 'abidar-group' ); ?>">
				<span aria-hidden="true">&#8250;</span>
			</button>
		</div>

	<?php else : ?>

		<div class="abidar-landing__vertical">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>

	<?php endif; ?>

</main>

<?php
if ( 'yes' === $abidar_show_footer ) {
	get_footer();
} else {
	wp_footer();
}
