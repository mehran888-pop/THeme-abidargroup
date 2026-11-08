<?php
/**
 * Template Name: آبیدر — مقالات و اخبار (Blog Hub)
 * Template Post Type: page
 *
 * قالب برگه «مقالات و اخبار»: آخرین گزارش‌های فنی و اخبار هلدینگ با چیدمان مجله‌ای.
 * (این برگه می‌تواند به عنوان «برگه نوشته‌ها» در تنظیمات خواندن وردپرس تعیین شود.)
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

abidar_page_hero(
	array(
		'title'    => get_the_title(),
		'subtitle' => abidar_t( 'section_blog_sub' ),
		'eyebrow'  => abidar_t( 'nav_blog' ),
		'image'    => ABIDAR_URI . '/assets/images/sector-energy.jpg',
		'crumb'    => abidar_t( 'nav_blog' ),
	)
);

$abidar_blog_paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$abidar_blog_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 9,
		'paged'          => $abidar_blog_paged,
	)
);
?>

<div class="abidar-section">
	<div class="abidar-container">
		<?php
		// محتوای دلخواه برگه (در صورت وجود) بالای فهرست مقالات نمایش داده می‌شود.
		while ( have_posts() ) :
			the_post();
			if ( trim( get_the_content() ) ) :
				?>
				<div class="abidar-entry-content abidar-entry-content--intro"><?php the_content(); ?></div>
				<?php
			endif;
		endwhile;
		?>

		<?php if ( $abidar_blog_query->have_posts() ) : ?>
			<div class="abidar-blog-grid">
				<?php
				while ( $abidar_blog_query->have_posts() ) :
					$abidar_blog_query->the_post();
					abidar_article_card();
				endwhile;
				?>
			</div>

			<?php
			echo '<div class="abidar-pagination">';
			echo wp_kses_post(
				paginate_links(
					array(
						'base'      => trailingslashit( get_permalink() ) . '%_%',
						'format'    => 'page/%#%/',
						'total'     => (int) $abidar_blog_query->max_num_pages,
						'current'   => (int) $abidar_blog_paged,
						'prev_text' => '→ ' . esc_html__( 'قبلی', 'abidar-group' ),
						'next_text' => esc_html__( 'بعدی', 'abidar-group' ) . ' ←',
					)
				)
			);
			echo '</div>';
			wp_reset_postdata();
			?>
		<?php else : ?>
			<div class="abidar-empty-state">
				<span class="abidar-empty-state__icon" aria-hidden="true">📰</span>
				<h2><?php esc_html_e( 'هنوز مقاله‌ای منتشر نشده است', 'abidar-group' ); ?></h2>
				<p><?php esc_html_e( 'به‌زودی گزارش‌های فنی و اخبار رسمی گروه هلدینگ آبیدر در این بخش منتشر خواهد شد.', 'abidar-group' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
