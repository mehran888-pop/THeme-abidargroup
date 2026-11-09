<?php
/**
 * Abidar Group Holding — Single Project Template (v2.0.0)
 *
 * شناسنامه فنی کامل پروژه: کارفرما، موقعیت، ظرفیت، پیشرفت فیزیکی، سال بهره‌برداری،
 * توضیحات مهندسی، گالری و پروژه‌های مرتبط.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$abidar_progress = (int) get_post_meta( get_the_ID(), '_abidar_progress', true );
	$abidar_client   = get_post_meta( get_the_ID(), '_abidar_client', true );
	$abidar_location = get_post_meta( get_the_ID(), '_abidar_location', true );
	$abidar_capacity = get_post_meta( get_the_ID(), '_abidar_capacity', true );
	$abidar_year     = get_post_meta( get_the_ID(), '_abidar_year', true );
	$abidar_progress = $abidar_progress ? $abidar_progress : 100;
	$abidar_terms    = get_the_terms( get_the_ID(), 'project_sector' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-single-project' ); ?>>
		<header class="abidar-single-hero" style="background-image: url('<?php echo esc_url( has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'abidar-hero' ) : ABIDAR_URI . '/assets/images/sector-oil.jpg' ); ?>');">
			<div class="abidar-single-hero__overlay"></div>
			<div class="abidar-container abidar-single-hero__inner">
				<nav class="abidar-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهبری پروژه', 'abidar-group' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( abidar_page_url( 'projects' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_projects' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<span class="abidar-breadcrumb__current"><?php echo esc_html( wp_trim_words( get_the_title(), 8 ) ); ?></span>
				</nav>

				<?php if ( ! empty( $abidar_terms ) && ! is_wp_error( $abidar_terms ) ) : ?>
					<span class="abidar-article-hero__cat"><?php echo esc_html( $abidar_terms[0]->name ); ?></span>
				<?php endif; ?>

				<h1 class="abidar-single-hero__title"><?php the_title(); ?></h1>

				<div class="abidar-single-hero__stats">
					<?php if ( $abidar_capacity ) : ?>
						<div><small><?php echo esc_html( abidar_t( 'project_capacity' ) ); ?></small><strong><?php echo esc_html( $abidar_capacity ); ?></strong></div>
					<?php endif; ?>
					<?php if ( $abidar_year ) : ?>
						<div><small><?php echo esc_html( abidar_t( 'project_year' ) ); ?></small><strong><?php echo esc_html( $abidar_year ); ?></strong></div>
					<?php endif; ?>
					<div><small><?php echo esc_html( abidar_t( 'project_progress' ) ); ?></small><strong class="abidar-text-gold"><?php echo (int) $abidar_progress; ?>%</strong></div>
				</div>
			</div>
		</header>

		<div class="abidar-section">
			<div class="abidar-container">
				<div class="abidar-single-layout">
					<div class="abidar-single-layout__content">
						<?php $abidar_summary = abidar_meta( get_the_ID(), 'summary' ); ?>
						<?php if ( $abidar_summary ) : ?>
							<p class="abidar-single-lead"><?php echo esc_html( $abidar_summary ); ?></p>
						<?php endif; ?>

						<?php abidar_render_video_block( get_the_ID() ); ?>

						<div class="abidar-entry-content abidar-article-body">
							<?php the_content(); ?>
						</div>

						<?php abidar_render_repeater_block( get_the_ID(), 'achievements', __( 'دستاوردها و نتایج پروژه', 'abidar-group' ), '🏆' ); ?>

						<?php abidar_render_specs_table( get_the_ID(), __( 'مشخصات فنی تکمیلی', 'abidar-group' ) ); ?>

						<?php abidar_render_gallery_block( get_the_ID() ); ?>

						<?php
						$abidar_related = new WP_Query(
							array(
								'post_type'      => 'abidar_project',
								'posts_per_page' => 3,
								'post__not_in'   => array( get_the_ID() ),
								'no_found_rows'  => true,
							)
						);

						if ( $abidar_related->have_posts() ) :
							?>
							<section class="abidar-related-articles">
								<div class="abidar-section__head abidar-section__head--compact">
									<h2 class="abidar-section__title"><?php esc_html_e( 'سایر پروژه‌های شاخص هلدینگ', 'abidar-group' ); ?></h2>
								</div>
								<div class="abidar-services-grid">
									<?php
									while ( $abidar_related->have_posts() ) :
										$abidar_related->the_post();
										$abidar_r_progress = (int) get_post_meta( get_the_ID(), '_abidar_progress', true );
										$abidar_r_progress = $abidar_r_progress ? $abidar_r_progress : 100;
										?>
										<article class="abidar-project-card">
											<a href="<?php the_permalink(); ?>" class="abidar-project-card__thumb">
												<?php if ( has_post_thumbnail() ) : ?>
													<?php the_post_thumbnail( 'abidar-project-card', array( 'loading' => 'lazy' ) ); ?>
												<?php else : ?>
													<span class="abidar-project-card__thumb-placeholder" style="background-image: url('<?php echo esc_url( ABIDAR_URI . '/assets/images/sector-energy.jpg' ); ?>');"></span>
												<?php endif; ?>
											</a>
											<div class="abidar-project-card__content">
												<h3 class="abidar-project-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
												<div class="abidar-project-card__progress">
													<div class="abidar-project-card__progress-head">
														<span><?php echo esc_html( abidar_t( 'project_progress' ) ); ?></span>
														<strong><?php echo (int) $abidar_r_progress; ?>%</strong>
													</div>
													<div class="abidar-project-card__progress-track">
														<span class="abidar-project-card__progress-fill" style="width: <?php echo (int) $abidar_r_progress; ?>%;"></span>
													</div>
												</div>
											</div>
										</article>
										<?php
									endwhile;
									wp_reset_postdata();
									?>
								</div>
							</section>
						<?php endif; ?>
					</div>

					<aside class="abidar-single-layout__aside">
						<div class="abidar-spec-card">
							<h4><?php esc_html_e( 'شناسنامه فنی پروژه', 'abidar-group' ); ?></h4>
							<dl class="abidar-spec-list">
								<?php if ( $abidar_client ) : ?>
									<div><dt><?php echo esc_html( abidar_t( 'project_client' ) ); ?></dt><dd><?php echo esc_html( $abidar_client ); ?></dd></div>
								<?php endif; ?>
								<?php if ( $abidar_location ) : ?>
									<div><dt><?php echo esc_html( abidar_t( 'project_location' ) ); ?></dt><dd><?php echo esc_html( $abidar_location ); ?></dd></div>
								<?php endif; ?>
								<?php if ( $abidar_capacity ) : ?>
									<div><dt><?php echo esc_html( abidar_t( 'project_capacity' ) ); ?></dt><dd><?php echo esc_html( $abidar_capacity ); ?></dd></div>
								<?php endif; ?>
								<div><dt><?php echo esc_html( abidar_t( 'project_progress' ) ); ?></dt><dd><?php echo (int) $abidar_progress; ?>%</dd></div>
								<?php if ( $abidar_year ) : ?>
									<div><dt><?php echo esc_html( abidar_t( 'project_year' ) ); ?></dt><dd><?php echo esc_html( $abidar_year ); ?></dd></div>
								<?php endif; ?>
							</dl>
							<?php abidar_render_pdf_button( get_the_ID() ); ?>
							<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="abidar-btn abidar-btn--gold abidar-btn--sm abidar-btn--full">
								<?php esc_html_e( 'درخواست اطلاعات فنی بیشتر', 'abidar-group' ); ?>
							</a>
						</div>
					</aside>
				</div>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
