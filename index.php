<?php
/**
 * Abidar Group Holding — Blog Index / Fallback Template (v2.0.0)
 *
 * قالب فهرست نوشته‌ها: اگر برگه‌ای به عنوان «برگه نوشته‌ها» تعیین شده باشد،
 * همان برگه با محتوای المنتور/وردپرس نمایش داده می‌شود؛ در غیر این صورت آرشیو مقالات.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// صفحه‌ای که به عنوان «برگه نوشته‌ها» تعیین شده است.
if ( is_home() && ! is_front_page() && 'page' === get_option( 'show_on_front' ) ) {
	$abidar_blog_page_id = (int) get_option( 'page_for_posts' );

	if ( $abidar_blog_page_id && abidar_is_elementor_page( $abidar_blog_page_id ) ) {
		?>
		<div class="abidar-elementor-content">
			<?php
			$abidar_blog_query = new WP_Query(
				array(
					'post_type'      => 'page',
					'page_id'        => $abidar_blog_page_id,
					'posts_per_page' => 1,
				)
			);
			if ( $abidar_blog_query->have_posts() ) {
				$abidar_blog_query->the_post();
				the_content();
				wp_reset_postdata();
			}
			?>
		</div>
		<?php
		get_footer();
		return;
	}
}

$abidar_archive_title = is_home() ? abidar_t( 'section_blog_title' ) : abidar_clean_archive_title();

abidar_page_hero(
	array(
		'title'    => $abidar_archive_title,
		'subtitle' => abidar_t( 'section_blog_sub' ),
		'eyebrow'  => abidar_t( 'nav_blog' ),
		'image'    => ABIDAR_URI . '/assets/images/sector-energy.jpg',
		'crumb'    => is_home() ? abidar_t( 'nav_blog' ) : abidar_clean_archive_title(),
	)
);
?>

<div class="abidar-section">
	<div class="abidar-container">
		<div class="abidar-with-sidebar">
			<div class="abidar-with-sidebar__main">
				<?php if ( have_posts() ) : ?>
					<div class="abidar-blog-grid abidar-blog-grid--archive">
						<?php
						while ( have_posts() ) :
							the_post();
							abidar_article_card();
						endwhile;
						?>
					</div>

					<?php
					the_posts_pagination(
						array(
							'mid_size'  => 2,
							'prev_text' => '→ ' . esc_html__( 'قبلی', 'abidar-group' ),
							'next_text' => esc_html__( 'بعدی', 'abidar-group' ) . ' ←',
							'class'     => 'abidar-pagination',
						)
					);
					?>
				<?php else : ?>
					<div class="abidar-empty-state">
						<span class="abidar-empty-state__icon" aria-hidden="true">📄</span>
						<h2><?php esc_html_e( 'نوشته‌ای برای نمایش یافت نشد', 'abidar-group' ); ?></h2>
						<p><?php esc_html_e( 'به‌زودی گزارش‌ها و تحلیل‌های جدید گروه هلدینگ آبیدر در این بخش منتشر خواهد شد.', 'abidar-group' ); ?></p>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="abidar-btn abidar-btn--gold">
							<?php echo esc_html( abidar_t( 'nav_home' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
				<aside class="abidar-with-sidebar__aside" aria-label="<?php esc_attr_e( 'ابزارک‌های مقالات', 'abidar-group' ); ?>">
					<?php dynamic_sidebar( 'sidebar-blog' ); ?>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
get_footer();
