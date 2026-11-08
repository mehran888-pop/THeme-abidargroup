<?php
/**
 * Abidar Group Holding — Single Certificate Template (v2.0.0)
 *
 * نمایش رسمی گواهینامه / لوح افتخار با شماره مدرک، مرجع صادرکننده، بازه اعتبار
 * و امکان استعلام اصالت در کنار محتوای تشریحی.
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

	$abidar_issuer = get_post_meta( get_the_ID(), '_abidar_cert_issuer', true );
	$abidar_code   = get_post_meta( get_the_ID(), '_abidar_cert_code', true );
	$abidar_year   = get_post_meta( get_the_ID(), '_abidar_cert_year', true );
	$abidar_terms  = get_the_terms( get_the_ID(), 'certificate_category' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-single-cert' ); ?>>
		<div class="abidar-section abidar-cert-hero">
			<div class="abidar-container">
				<nav class="abidar-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهبری مدرک', 'abidar-group' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( abidar_page_url( 'certificates' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_certificates' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<span class="abidar-breadcrumb__current"><?php echo esc_html( wp_trim_words( get_the_title(), 8 ) ); ?></span>
				</nav>

				<div class="abidar-cert-hero__grid">
					<div class="abidar-cert-hero__frame">
						<div class="abidar-cert-hero__frame-inner">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
							<?php else : ?>
								<div class="abidar-cert-hero__seal">
									<span>🏆</span>
									<strong>ABIDAR GROUP</strong>
									<small>OFFICIAL ACCREDITATION</small>
								</div>
							<?php endif; ?>
							<span class="abidar-verified-pill">✓ <?php esc_html_e( 'اصالت تایید شده', 'abidar-group' ); ?></span>
						</div>
					</div>

					<div class="abidar-cert-hero__info">
						<?php if ( ! empty( $abidar_terms ) && ! is_wp_error( $abidar_terms ) ) : ?>
							<span class="abidar-section__eyebrow"><?php echo esc_html( $abidar_terms[0]->name ); ?></span>
						<?php endif; ?>

						<h1 class="abidar-cert-hero__title"><?php the_title(); ?></h1>

						<dl class="abidar-spec-list abidar-spec-list--wide">
							<?php if ( $abidar_issuer ) : ?>
								<div><dt><?php echo esc_html( abidar_t( 'cert_issuer' ) ); ?></dt><dd><?php echo esc_html( $abidar_issuer ); ?></dd></div>
							<?php endif; ?>
							<?php if ( $abidar_code ) : ?>
								<div><dt><?php echo esc_html( abidar_t( 'cert_code' ) ); ?></dt><dd><code dir="ltr"><?php echo esc_html( $abidar_code ); ?></code></dd></div>
							<?php endif; ?>
							<?php if ( $abidar_year ) : ?>
								<div><dt><?php esc_html_e( 'بازه اعتبار', 'abidar-group' ); ?></dt><dd><?php echo esc_html( $abidar_year ); ?></dd></div>
							<?php endif; ?>
						</dl>

						<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="abidar-btn abidar-btn--gold">
							<?php echo esc_html( abidar_t( 'cert_verify' ) ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="abidar-section">
			<div class="abidar-container abidar-entry-content abidar-article-body">
				<?php the_content(); ?>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
