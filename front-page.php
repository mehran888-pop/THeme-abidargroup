<?php
/**
 * Abidar Group Holding — Front Page Template (v2.0.0)
 *
 * صفحه اصلی: در صورت ساخت با المنتور، محتوای المنتور به صورت تمام‌عرض رندر می‌شود؛
 * در غیر این صورت دموی پیش‌فرض هلدینگ با ویجت‌های اختصاصی نمایش داده می‌شود.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php if ( abidar_is_elementor_page() ) : ?>
	<div class="abidar-elementor-content">
		<?php
		// محتوای ساخته‌شده با المنتور.
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
<?php else : ?>
	<?php
	// دموی پیش‌فرض صفحه اصلی با ویجت‌های اختصاصی هلدینگ.
	Abidar_Widget_Hero_Slider::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_About_Timeline::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Services::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Projects::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Team::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Certificates::render_output( array( 'layout_style' => 'layout_1' ) );

	// آخرین مقالات هلدینگ.
	$abidar_latest_posts = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( $abidar_latest_posts->have_posts() ) :
		?>
		<section class="abidar-section abidar-blog-sec">
			<div class="abidar-container">
				<div class="abidar-section__head">
					<div>
						<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'section_blog_sub' ) ); ?></span>
						<h2 class="abidar-section__title"><?php echo esc_html( abidar_t( 'section_blog_title' ) ); ?></h2>
					</div>
					<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' ) ); ?>" class="abidar-btn abidar-btn--outline-gold">
						<?php esc_html_e( 'همه مقالات و اخبار', 'abidar-group' ); ?>
					</a>
				</div>

				<div class="abidar-blog-grid">
					<?php
					while ( $abidar_latest_posts->have_posts() ) :
						$abidar_latest_posts->the_post();
						abidar_article_card();
					endwhile;
					?>
				</div>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;

	Abidar_Widget_Contact_Hub::render_output( array( 'layout_style' => 'layout_1' ) );
	?>
<?php endif; ?>

<?php
get_footer();
