<?php
/**
 * Abidar Group Holding — Single Team Member Template (v2.0.0)
 *
 * بیوگرافی کامل مدیر / متخصص، سوابق تحصیلی و اجرایی، اطلاعات تماس و شبکه‌های حرفه‌ای.
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

	$abidar_role       = get_post_meta( get_the_ID(), '_abidar_role_fa', true );
	$abidar_dept       = get_post_meta( get_the_ID(), '_abidar_dept_fa', true );
	$abidar_education  = get_post_meta( get_the_ID(), '_abidar_education', true );
	$abidar_experience = get_post_meta( get_the_ID(), '_abidar_experience', true );
	$abidar_email      = get_post_meta( get_the_ID(), '_abidar_email', true );
	$abidar_linkedin   = get_post_meta( get_the_ID(), '_abidar_linkedin', true );
	$abidar_terms      = get_the_terms( get_the_ID(), 'team_department' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-single-member' ); ?>>
		<div class="abidar-section abidar-member-hero">
			<div class="abidar-container">
				<nav class="abidar-breadcrumb abidar-breadcrumb--light" aria-label="<?php esc_attr_e( 'مسیر راهبری تیم', 'abidar-group' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( abidar_page_url( 'our-team' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_team' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<span class="abidar-breadcrumb__current"><?php the_title(); ?></span>
				</nav>

				<div class="abidar-member-hero__grid">
					<div class="abidar-member-hero__portrait">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'abidar-team-portrait', array( 'loading' => 'eager' ) ); ?>
						<?php else : ?>
							<div class="abidar-member-hero__monogram">
								<span><?php echo esc_html( abidar_first_char( get_the_title() ) ); ?></span>
							</div>
						<?php endif; ?>
					</div>

					<div class="abidar-member-hero__content">
						<?php if ( ! empty( $abidar_terms ) && ! is_wp_error( $abidar_terms ) ) : ?>
							<span class="abidar-section__eyebrow"><?php echo esc_html( $abidar_terms[0]->name ); ?></span>
						<?php elseif ( $abidar_dept ) : ?>
							<span class="abidar-section__eyebrow"><?php echo esc_html( $abidar_dept ); ?></span>
						<?php endif; ?>

						<h1 class="abidar-member-hero__name"><?php the_title(); ?></h1>

						<?php if ( $abidar_role ) : ?>
							<p class="abidar-member-hero__role"><?php echo esc_html( $abidar_role ); ?></p>
						<?php endif; ?>

						<div class="abidar-member-hero__cards">
							<?php if ( $abidar_education ) : ?>
								<div class="abidar-member-mini-card">
									<span aria-hidden="true">🎓</span>
									<div>
										<small><?php esc_html_e( 'سوابق تحصیلی', 'abidar-group' ); ?></small>
										<strong><?php echo esc_html( $abidar_education ); ?></strong>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $abidar_experience ) : ?>
								<div class="abidar-member-mini-card">
									<span aria-hidden="true">⏱️</span>
									<div>
										<small><?php esc_html_e( 'سوابق اجرایی', 'abidar-group' ); ?></small>
										<strong><?php echo esc_html( $abidar_experience ); ?></strong>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<div class="abidar-member-hero__actions">
							<?php if ( $abidar_email ) : ?>
								<a href="mailto:<?php echo esc_attr( antispambot( $abidar_email ) ); ?>" class="abidar-btn abidar-btn--gold">
									✉️ <?php echo esc_html( antispambot( $abidar_email ) ); ?>
								</a>
							<?php endif; ?>

							<?php if ( $abidar_linkedin ) : ?>
								<a href="<?php echo esc_url( $abidar_linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-btn abidar-btn--glass">in LinkedIn</a>
							<?php endif; ?>

							<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="abidar-btn abidar-btn--glass">
								<?php echo esc_html( abidar_t( 'nav_contact' ) ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="abidar-section">
			<div class="abidar-container">
				<div class="abidar-single-layout">
					<div class="abidar-single-layout__content">
						<?php $abidar_summary = abidar_meta( get_the_ID(), 'summary' ); ?>
						<?php if ( $abidar_summary ) : ?>
							<p class="abidar-single-lead"><?php echo esc_html( $abidar_summary ); ?></p>
						<?php endif; ?>

						<?php abidar_render_video_block( get_the_ID() ); ?>

						<div class="abidar-entry-content abidar-article-body abidar-member-bio">
							<?php the_content(); ?>
						</div>

						<?php abidar_render_repeater_block( get_the_ID(), 'expertise', __( 'حوزه‌های تخصصی', 'abidar-group' ), '★' ); ?>
					</div>

					<aside class="abidar-single-layout__aside">
						<div class="abidar-spec-card">
							<h4><?php esc_html_e( 'سوابق و تحصیلات', 'abidar-group' ); ?></h4>
							<dl class="abidar-spec-list">
								<?php foreach ( array( 'role_fa' => 'سمت سازمانی', 'dept_fa' => 'حوزه فعالیت', 'education' => 'تحصیلات', 'experience' => 'سابقه اجرایی', 'email' => 'رایانامه' ) as $abidar_key => $abidar_label ) : ?>
									<?php $abidar_value = abidar_meta( get_the_ID(), $abidar_key ); ?>
									<?php if ( $abidar_value ) : ?>
										<div><dt><?php echo esc_html( $abidar_label ); ?></dt><dd><?php echo esc_html( $abidar_value ); ?></dd></div>
									<?php endif; ?>
								<?php endforeach; ?>
							</dl>

							<?php abidar_render_pdf_button( get_the_ID() ); ?>
						</div>

						<div class="abidar-sidebar-cta">
							<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'holding_tagline' ) ); ?></span>
							<h4><?php esc_html_e( 'ارتباط با این همکار', 'abidar-group' ); ?></h4>
							<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="abidar-btn abidar-btn--outline-gold abidar-btn--sm abidar-btn--full">
								<?php echo esc_html( abidar_t( 'nav_contact' ) ); ?>
							</a>
						</div>
					</aside>
				</div>
			</div>
		</div>

		<?php
		$abidar_related = new WP_Query(
			array(
				'post_type'      => 'abidar_team',
				'posts_per_page' => 3,
				'post__not_in'   => array( get_the_ID() ),
				'no_found_rows'  => true,
			)
		);

		if ( $abidar_related->have_posts() ) :
			?>
			<div class="abidar-section abidar-section--muted">
				<div class="abidar-container">
					<div class="abidar-section__head abidar-section__head--compact">
						<h2 class="abidar-section__title"><?php esc_html_e( 'سایر اعضای هیئت مدیره و مدیران ارشد', 'abidar-group' ); ?></h2>
					</div>

					<div class="abidar-team-grid">
						<?php
						while ( $abidar_related->have_posts() ) :
							$abidar_related->the_post();
							$abidar_r_role = get_post_meta( get_the_ID(), '_abidar_role_fa', true );
							?>
							<article class="abidar-team-card">
								<div class="abidar-team-card__avatar">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'abidar-team-portrait', array( 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<div class="abidar-team-card__monogram"><span><?php echo esc_html( abidar_first_char( get_the_title() ) ); ?></span></div>
									<?php endif; ?>
								</div>
								<div class="abidar-team-card__body">
									<h3 class="abidar-team-card__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<?php if ( $abidar_r_role ) : ?>
										<p class="abidar-team-card__role"><?php echo esc_html( $abidar_r_role ); ?></p>
									<?php endif; ?>
									<a href="<?php the_permalink(); ?>" class="abidar-link-gold">
										<span><?php echo esc_html( abidar_t( 'view_details' ) ); ?></span>
										<span class="abidar-icon-arrow">→</span>
									</a>
								</div>
							</article>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</article>

	<?php
endwhile;

get_footer();
