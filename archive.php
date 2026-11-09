<?php
/**
 * Abidar Group Holding — Archive Template for CPTs & Taxonomies (v2.0.0)
 *
 * آرشیو خدمات، پروژه‌ها، تیم‌ها و مدارک با کارت‌های اختصاصی هر پست‌تایپ.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$abidar_post_type = get_post_type() ? get_post_type() : get_query_var( 'post_type' );
$abidar_post_type = is_array( $abidar_post_type ) ? reset( $abidar_post_type ) : $abidar_post_type;

$abidar_type_data = array(
	'abidar_service'     => array(
		'image' => ABIDAR_URI . '/assets/images/sector-mining.jpg',
		'label' => abidar_t( 'nav_services' ),
	),
	'abidar_project'     => array(
		'image' => ABIDAR_URI . '/assets/images/sector-oil.jpg',
		'label' => abidar_t( 'nav_projects' ),
	),
	'abidar_team'        => array(
		'image' => ABIDAR_URI . '/assets/images/sector-energy.jpg',
		'label' => abidar_t( 'nav_team' ),
	),
	'abidar_certificate' => array(
		'image' => ABIDAR_URI . '/assets/images/sector-mining.jpg',
		'label' => abidar_t( 'nav_certificates' ),
	),
);

$abidar_meta = isset( $abidar_type_data[ $abidar_post_type ] ) ? $abidar_type_data[ $abidar_post_type ] : array(
	'image' => ABIDAR_URI . '/assets/images/sector-energy.jpg',
	'label' => abidar_clean_archive_title(),
);

abidar_page_hero(
	array(
		'title'   => abidar_clean_archive_title(),
		'eyebrow' => $abidar_meta['label'],
		'image'   => $abidar_meta['image'],
		'crumb'   => abidar_clean_archive_title(),
	)
);
?>

<div class="abidar-section">
	<div class="abidar-container">
		<?php if ( ! empty( get_the_archive_description() ) ) : ?>
			<div class="abidar-archive-description"><?php the_archive_description(); ?></div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="abidar-archive-grid">
				<?php
				while ( have_posts() ) :
					the_post();

					if ( 'abidar_team' === $abidar_post_type ) {
						$abidar_role  = get_post_meta( get_the_ID(), '_abidar_role_fa', true );
						$abidar_dept  = get_post_meta( get_the_ID(), '_abidar_dept_fa', true );
						$abidar_email = get_post_meta( get_the_ID(), '_abidar_email', true );
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-team-card' ); ?>>
							<div class="abidar-team-card__avatar">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'abidar-team-portrait', array( 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<div class="abidar-team-card__monogram"><span><?php echo esc_html( abidar_first_char( get_the_title() ) ); ?></span></div>
								<?php endif; ?>
								<?php if ( $abidar_dept ) : ?>
									<span class="abidar-team-card__dept"><?php echo esc_html( $abidar_dept ); ?></span>
								<?php endif; ?>
							</div>
							<div class="abidar-team-card__body">
								<h2 class="abidar-team-card__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<?php if ( $abidar_role ) : ?>
									<p class="abidar-team-card__role"><?php echo esc_html( $abidar_role ); ?></p>
								<?php endif; ?>
								<p class="abidar-team-card__bio"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
								<?php if ( $abidar_email ) : ?>
									<a href="mailto:<?php echo esc_attr( $abidar_email ); ?>" class="abidar-team-social">✉️ <?php echo esc_html( $abidar_email ); ?></a>
								<?php endif; ?>
							</div>
						</article>
						<?php
					} elseif ( 'abidar_service' === $abidar_post_type ) {
						$abidar_metric   = get_post_meta( get_the_ID(), '_abidar_metric', true );
						$abidar_sector   = abidar_meta( get_the_ID(), 'sector', 'mining' );
						$abidar_features = get_post_meta( get_the_ID(), '_abidar_features', true );
						$abidar_video    = ( '' !== (string) get_post_meta( get_the_ID(), '_abidar_video_url', true ) ) || ( (int) get_post_meta( get_the_ID(), '_abidar_video_id', true ) > 0 );
						$abidar_labels   = array(
							'mining' => abidar_t( 'sector_mining' ),
							'energy' => abidar_t( 'sector_energy' ),
							'oil'    => abidar_t( 'sector_oil' ),
						);
						$abidar_sector_label = isset( $abidar_labels[ $abidar_sector ] ) ? $abidar_labels[ $abidar_sector ] : '';
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-service-card abidar-service-card--' . $abidar_sector ); ?>>
							<?php
							$abidar_sector_file = ( 'oil' === $abidar_sector ) ? 'oil' : ( ( 'energy' === $abidar_sector ) ? 'energy' : 'mining' );
							$abidar_media_url   = has_post_thumbnail()
								? get_the_post_thumbnail_url( get_the_ID(), 'abidar-project-card' )
								: ABIDAR_URI . '/assets/images/sector-' . $abidar_sector_file . '.jpg';
							?>
							<a href="<?php the_permalink(); ?>" class="abidar-service-card__media"
								style="background-image: url('<?php echo esc_url( $abidar_media_url ); ?>');">
								<span class="abidar-service-card__badge"><?php echo esc_html( $abidar_sector_label ); ?></span>
								<?php if ( $abidar_video ) : ?>
									<span class="abidar-service-card__play" aria-hidden="true">▶</span>
								<?php endif; ?>
							</a>
							<div class="abidar-service-card__body">
								<h2 class="abidar-service-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="abidar-service-card__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>

								<?php if ( ! empty( $abidar_features ) && is_array( $abidar_features ) ) : ?>
									<ul class="abidar-service-card__features">
										<?php foreach ( array_slice( $abidar_features, 0, 3 ) as $abidar_feature ) : ?>
											<?php if ( empty( $abidar_feature['label'] ) ) : ?>
												<?php continue; ?>
											<?php endif; ?>
											<li>✓ <?php echo esc_html( $abidar_feature['label'] ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>

								<?php if ( $abidar_metric ) : ?>
									<div class="abidar-service-card__metric">
										<strong><?php echo esc_html( $abidar_metric ); ?></strong>
									</div>
								<?php endif; ?>

								<div class="abidar-service-card__footer">
									<a href="<?php the_permalink(); ?>" class="abidar-link-gold">
										<span><?php echo esc_html( abidar_t( 'view_details' ) ); ?></span>
										<span class="abidar-icon-arrow">→</span>
									</a>
								</div>
							</div>
						</article>
						<?php
					} elseif ( 'abidar_project' === $abidar_post_type ) {
						$abidar_progress = (int) get_post_meta( get_the_ID(), '_abidar_progress', true );
						$abidar_client   = get_post_meta( get_the_ID(), '_abidar_client', true );
						$abidar_location = get_post_meta( get_the_ID(), '_abidar_location', true );
						$abidar_capacity = get_post_meta( get_the_ID(), '_abidar_capacity', true );
						$abidar_progress = $abidar_progress ? $abidar_progress : 100;
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-project-card' ); ?>>
							<a href="<?php the_permalink(); ?>" class="abidar-project-card__thumb">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'abidar-project-card', array( 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<span class="abidar-project-card__thumb-placeholder" style="background-image: url('<?php echo esc_url( ABIDAR_URI . '/assets/images/sector-oil.jpg' ); ?>');"></span>
								<?php endif; ?>
							</a>
							<div class="abidar-project-card__content">
								<h2 class="abidar-project-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="abidar-project-card__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
								<dl class="abidar-project-card__specs">
									<?php if ( $abidar_client ) : ?>
										<div><dt><?php echo esc_html( abidar_t( 'project_client' ) ); ?></dt><dd><?php echo esc_html( $abidar_client ); ?></dd></div>
									<?php endif; ?>
									<?php if ( $abidar_location ) : ?>
										<div><dt><?php echo esc_html( abidar_t( 'project_location' ) ); ?></dt><dd><?php echo esc_html( $abidar_location ); ?></dd></div>
									<?php endif; ?>
									<?php if ( $abidar_capacity ) : ?>
										<div><dt><?php echo esc_html( abidar_t( 'project_capacity' ) ); ?></dt><dd class="abidar-text-gold"><?php echo esc_html( $abidar_capacity ); ?></dd></div>
									<?php endif; ?>
								</dl>
								<div class="abidar-project-card__progress">
									<div class="abidar-project-card__progress-head">
										<span><?php echo esc_html( abidar_t( 'project_progress' ) ); ?></span>
										<strong><?php echo (int) $abidar_progress; ?>%</strong>
									</div>
									<div class="abidar-project-card__progress-track">
										<span class="abidar-project-card__progress-fill" style="width: <?php echo (int) $abidar_progress; ?>%;"></span>
									</div>
								</div>
							</div>
						</article>
						<?php
					} elseif ( 'abidar_certificate' === $abidar_post_type ) {
						$abidar_issuer = get_post_meta( get_the_ID(), '_abidar_cert_issuer', true );
						$abidar_code   = get_post_meta( get_the_ID(), '_abidar_cert_code', true );
						$abidar_year   = get_post_meta( get_the_ID(), '_abidar_cert_year', true );
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-cert-card' ); ?>>
							<div class="abidar-cert-card__seal">
								<span class="abidar-cert-card__badge">🏅</span>
								<span class="abidar-cert-card__year"><?php echo esc_html( $abidar_year ); ?></span>
							</div>
							<div class="abidar-cert-card__body">
								<h2 class="abidar-cert-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<div class="abidar-cert-card__meta">
									<?php if ( $abidar_issuer ) : ?>
										<div><small><?php echo esc_html( abidar_t( 'cert_issuer' ) ); ?></small><strong><?php echo esc_html( $abidar_issuer ); ?></strong></div>
									<?php endif; ?>
									<?php if ( $abidar_code ) : ?>
										<div><small><?php echo esc_html( abidar_t( 'cert_code' ) ); ?></small><code dir="ltr"><?php echo esc_html( $abidar_code ); ?></code></div>
									<?php endif; ?>
								</div>
								<a href="<?php the_permalink(); ?>" class="abidar-btn abidar-btn--outline-gold abidar-btn--sm">
									<?php echo esc_html( abidar_t( 'cert_verify' ) ); ?>
								</a>
							</div>
						</article>
						<?php
					} else {
						abidar_article_card();
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
				<span class="abidar-empty-state__icon" aria-hidden="true">📁</span>
				<h2><?php esc_html_e( 'موردی برای نمایش یافت نشد', 'abidar-group' ); ?></h2>
				<p><?php esc_html_e( 'محتوای این بخش در حال تکمیل است. می‌توانید از طریق منو به سایر بخش‌های هلدینگ گروه آبیدر دسترسی داشته باشید.', 'abidar-group' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="abidar-btn abidar-btn--gold"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
