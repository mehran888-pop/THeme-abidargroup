<?php
/**
 * Abidar Group Holding — Single Service Template (v2.0.0)
 *
 * جزئیات کامل یک خدمت تخصصی هلدینگ (معادن، انرژی یا نفت) با شاخص ظرفیت و درخواست همکاری.
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

	$abidar_metric  = get_post_meta( get_the_ID(), '_abidar_metric', true );
	$abidar_pdf     = get_post_meta( get_the_ID(), '_abidar_pdf_url', true );
	$abidar_terms   = get_the_terms( get_the_ID(), 'service_sector' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-single-service' ); ?>>
		<header class="abidar-single-hero" style="background-image: url('<?php echo esc_url( has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'abidar-hero' ) : ABIDAR_URI . '/assets/images/sector-mining.jpg' ); ?>');">
			<div class="abidar-single-hero__overlay"></div>
			<div class="abidar-container abidar-single-hero__inner">
				<nav class="abidar-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهبری خدمت', 'abidar-group' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_services' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<span class="abidar-breadcrumb__current"><?php echo esc_html( wp_trim_words( get_the_title(), 8 ) ); ?></span>
				</nav>

				<?php if ( ! empty( $abidar_terms ) && ! is_wp_error( $abidar_terms ) ) : ?>
					<span class="abidar-article-hero__cat"><?php echo esc_html( $abidar_terms[0]->name ); ?></span>
				<?php endif; ?>

				<h1 class="abidar-single-hero__title"><?php the_title(); ?></h1>

				<?php if ( $abidar_metric ) : ?>
					<div class="abidar-single-hero__stats">
						<div><small><?php esc_html_e( 'شاخص کلیدی ظرفیت', 'abidar-group' ); ?></small><strong><?php echo esc_html( $abidar_metric ); ?></strong></div>
					</div>
				<?php endif; ?>
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

						<?php abidar_render_repeater_block( get_the_ID(), 'features', __( 'مزیت‌های کلیدی این خدمت', 'abidar-group' ), '✔' ); ?>

						<?php abidar_render_repeater_block( get_the_ID(), 'steps', __( 'فرآیند اجرا و تحویل', 'abidar-group' ), '◆' ); ?>

						<?php abidar_render_specs_table( get_the_ID(), __( 'مشخصات و ظرفیت‌ها', 'abidar-group' ) ); ?>

						<?php abidar_render_gallery_block( get_the_ID() ); ?>

						<?php if ( ! empty( $abidar_pdf ) ) : ?>
							<div class="abidar-download-box">
								<div>
									<h4><?php esc_html_e( 'کاتالوگ فنی و بروشور خدمت', 'abidar-group' ); ?></h4>
									<p><?php esc_html_e( 'مشخصات فنی، ظرفیت‌ها و استانداردهای اجرایی این خدمت را در قالب PDF دریافت کنید.', 'abidar-group' ); ?></p>
								</div>
								<a href="<?php echo esc_url( $abidar_pdf ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-btn abidar-btn--gold">
									📄 <?php echo esc_html( abidar_t( 'cta_download_catalog' ) ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>

					<aside class="abidar-single-layout__aside">
						<div class="abidar-spec-card">
							<h4><?php esc_html_e( 'درخواست همکاری و استعلام', 'abidar-group' ); ?></h4>
							<p><?php esc_html_e( 'کارشناسان معاونت مربوطه در گروه هلدینگ آبیدر آماده ارائه مشاوره فنی، قیمت‌گذاری و برنامه زمان‌بندی پروژه هستند.', 'abidar-group' ); ?></p>
							<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="abidar-btn abidar-btn--gold abidar-btn--sm abidar-btn--full">
								<?php echo esc_html( abidar_t( 'cta_contact_us' ) ); ?>
							</a>
						</div>

						<div class="abidar-sidebar-cta">
							<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'holding_tagline' ) ); ?></span>
							<h4><?php esc_html_e( 'پروژه‌های مرتبط', 'abidar-group' ); ?></h4>
							<a href="<?php echo esc_url( abidar_page_url( 'projects' ) ); ?>" class="abidar-btn abidar-btn--outline-gold abidar-btn--sm abidar-btn--full">
								<?php echo esc_html( abidar_t( 'nav_projects' ) ); ?>
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
