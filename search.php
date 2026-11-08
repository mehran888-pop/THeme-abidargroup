<?php
/**
 * Abidar Group Holding — Search Results Template (v2.0.0)
 *
 * قالب نتایج جستجو با فرم جستجوی سفارشی و کارت‌های یکپارچه.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$abidar_query_term = get_search_query();

abidar_page_hero(
	array(
		/* translators: %s: search query */
		'title'    => sprintf( esc_html__( 'نتایج جستجو برای: %s', 'abidar-group' ), $abidar_query_term ),
		'subtitle' => have_posts() ? sprintf( esc_html__( '%s نتیجه یافت شد.', 'abidar-group' ), number_format_i18n( $GLOBALS['wp_query']->found_posts ) ) : esc_html__( 'هیچ نتیجه‌ای یافت نشد.', 'abidar-group' ),
		'eyebrow'  => esc_html__( 'جستجو در پایگاه دانش گروه آبیدر', 'abidar-group' ),
		'image'    => ABIDAR_URI . '/assets/images/sector-energy.jpg',
		'crumb'    => esc_html__( 'جستجو', 'abidar-group' ),
	)
);
?>

<div class="abidar-section">
	<div class="abidar-container">
		<div class="abidar-search-box">
			<?php get_search_form(); ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="abidar-archive-grid abidar-archive-grid--search">
				<?php
				while ( have_posts() ) :
					the_post();

					if ( 'post' === get_post_type() ) {
						abidar_article_card();
					} else {
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-search-result' ); ?>>
							<span class="abidar-search-result__type">
								<?php
								$abidar_type_obj = get_post_type_object( get_post_type() );
								echo esc_html( $abidar_type_obj ? $abidar_type_obj->labels->singular_name : esc_html__( 'صفحه', 'abidar-group' ) );
								?>
							</span>
							<h2 class="abidar-search-result__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="abidar-search-result__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
							<a href="<?php the_permalink(); ?>" class="abidar-link-gold">
								<span><?php echo esc_html( abidar_t( 'view_details' ) ); ?></span>
								<span class="abidar-icon-arrow">→</span>
							</a>
						</article>
						<?php
					}
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
				<span class="abidar-empty-state__icon" aria-hidden="true">🔍</span>
				<h2><?php esc_html_e( 'نتیجه‌ای برای جستجوی شما یافت نشد', 'abidar-group' ); ?></h2>
				<p><?php esc_html_e( 'لطفاً عبارت دیگری را امتحان کنید یا از طریق منوی اصلی به بخش‌های خدمات، پروژه‌ها و مقالات هلدینگ مراجعه نمایید.', 'abidar-group' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="abidar-btn abidar-btn--gold"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
