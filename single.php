<?php
/**
 * Abidar Group Holding — Default Single Article Template (v2.0.0)
 *
 * قالب پیش‌فرض مقالات و اخبار (single.php): هر مقاله‌ای که در وردپرس بارگذاری شود
 * به صورت خودکار با این قالب حرفه‌ای (طرح مجله‌ای اجرایی) نمایش داده می‌شود.
 * بخش‌های قابل کنترل از پیشخوان: نوار پیشرفت مطالعه، فهرست مطالب چسبان،
 * جعبه نویسنده، دکمه‌های اشتراک‌گذاری و مقالات مرتبط.
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

	$abidar_categories  = get_the_category();
	$abidar_primary_cat = ! empty( $abidar_categories ) ? $abidar_categories[0] : null;
	$abidar_permalink   = get_permalink();
	$abidar_title       = get_the_title();
	$abidar_excerpt     = get_the_excerpt();
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'abidar-single-article abidar-single-article--' . sanitize_html_class( abidar_get_option( 'single_post_layout', 'executive_magazine' ) ) ); ?>>

		<!-- سربرگ مقاله (Hero) -->
		<header class="abidar-article-hero" style="background-image: url('<?php echo esc_url( has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'abidar-hero' ) : ABIDAR_URI . '/assets/images/sector-mining.jpg' ); ?>');">
			<div class="abidar-article-hero__overlay"></div>
			<div class="abidar-container abidar-article-hero__inner">
				<nav class="abidar-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر راهبری مقاله', 'abidar-group' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<?php if ( $abidar_primary_cat ) : ?>
						<a href="<?php echo esc_url( get_category_link( $abidar_primary_cat->term_id ) ); ?>"><?php echo esc_html( $abidar_primary_cat->name ); ?></a>
						<span aria-hidden="true">/</span>
					<?php endif; ?>
					<span class="abidar-breadcrumb__current"><?php echo esc_html( wp_trim_words( $abidar_title, 8 ) ); ?></span>
				</nav>

				<?php if ( $abidar_primary_cat ) : ?>
					<span class="abidar-article-hero__cat"><?php echo esc_html( $abidar_primary_cat->name ); ?></span>
				<?php endif; ?>

				<h1 class="abidar-article-hero__title"><?php echo esc_html( $abidar_title ); ?></h1>

				<?php if ( $abidar_excerpt ) : ?>
					<p class="abidar-article-hero__lead"><?php echo esc_html( wp_trim_words( $abidar_excerpt, 42 ) ); ?></p>
				<?php endif; ?>

				<div class="abidar-article-hero__meta">
					<div class="abidar-article-author">
						<span class="abidar-article-author__avatar"><?php echo get_avatar( get_the_author_meta( 'ID' ), 52, '', '', array( 'class' => 'abidar-avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<strong><?php echo esc_html( get_the_author() ); ?></strong>
							<small><?php echo esc_html( get_the_author_meta( 'description' ) ? wp_trim_words( get_the_author_meta( 'description' ), 10 ) : abidar_t( 'holding_short' ) ); ?></small>
						</div>
					</div>

					<ul class="abidar-article-meta-list">
						<li><span aria-hidden="true">📅</span> <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></li>
						<?php if ( abidar_get_option( 'post_reading_time', true ) ) : ?>
							<li><span aria-hidden="true">⏱️</span> <?php echo esc_html( abidar_reading_time() ); ?></li>
						<?php endif; ?>
						<li><span aria-hidden="true">💬</span> <?php echo esc_html( get_comments_number_text( 'بدون دیدگاه', '۱ دیدگاه', '% دیدگاه' ) ); ?></li>
					</ul>
				</div>
			</div>
		</header>

		<div class="abidar-section">
			<div class="abidar-container">
				<div class="abidar-single-layout">
					<!-- بدنه مقاله -->
					<div class="abidar-single-layout__content">
						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="abidar-article-cover">
								<?php the_post_thumbnail( 'abidar-hero', array( 'loading' => 'eager' ) ); ?>
								<?php if ( get_the_post_thumbnail_caption() ) : ?>
									<figcaption><?php echo esc_html( get_the_post_thumbnail_caption() ); ?></figcaption>
								<?php endif; ?>
							</figure>
						<?php endif; ?>

						<div class="abidar-entry-content abidar-article-body" id="abidar-article-body">
							<?php
							the_content();

							wp_link_pages(
								array(
									'before' => '<nav class="abidar-page-links">' . esc_html__( 'صفحات مقاله:', 'abidar-group' ),
									'after'  => '</nav>',
								)
							);
							?>
						</div>

						<?php if ( get_the_tags() ) : ?>
							<div class="abidar-article-tags">
								<?php the_tags( '<span class="abidar-article-tags__label">' . esc_html__( 'برچسب‌ها:', 'abidar-group' ) . '</span>', '' ); ?>
							</div>
						<?php endif; ?>

						<?php if ( abidar_get_option( 'post_share_buttons', true ) ) : ?>
							<div class="abidar-share-box">
								<h4><?php echo esc_html( abidar_t( 'share_article' ) ); ?></h4>
								<div class="abidar-share-box__buttons">
									<a href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $abidar_permalink ) ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-share-btn abidar-share-btn--linkedin" aria-label="LinkedIn">in</a>
									<a href="<?php echo esc_url( 'https://t.me/share/url?url=' . rawurlencode( $abidar_permalink ) . '&text=' . rawurlencode( $abidar_title ) ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-share-btn abidar-share-btn--telegram" aria-label="Telegram">✈</a>
									<a href="<?php echo esc_url( 'https://api.whatsapp.com/send?text=' . rawurlencode( $abidar_title . ' ' . $abidar_permalink ) ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-share-btn abidar-share-btn--whatsapp" aria-label="WhatsApp">💬</a>
									<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . rawurlencode( $abidar_permalink ) . '&text=' . rawurlencode( $abidar_title ) ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-share-btn abidar-share-btn--x" aria-label="X">𝕏</a>
									<button type="button" class="abidar-share-btn abidar-share-btn--copy" data-copy-link="<?php echo esc_url( $abidar_permalink ); ?>" aria-label="<?php esc_attr_e( 'کپی لینک مقاله', 'abidar-group' ); ?>">🔗</button>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( abidar_get_option( 'post_author_box', true ) && get_the_author_meta( 'description' ) ) : ?>
							<div class="abidar-author-box">
								<?php echo get_avatar( get_the_author_meta( 'ID' ), 96, '', '', array( 'class' => 'abidar-author-box__avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<div class="abidar-author-box__content">
									<span class="abidar-section__eyebrow"><?php esc_html_e( 'درباره نویسنده', 'abidar-group' ); ?></span>
									<h3><?php echo esc_html( get_the_author() ); ?></h3>
									<p><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
									<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="abidar-link-gold">
										<?php esc_html_e( 'مشاهده تمامی مقالات نویسنده', 'abidar-group' ); ?> →
									</a>
								</div>
							</div>
						<?php endif; ?>

						<?php
						$abidar_related = new WP_Query(
							array(
								'post_type'           => 'post',
								'posts_per_page'      => (int) abidar_get_option( 'post_related_count', 3 ),
								'post__not_in'        => array( get_the_ID() ),
								'category__in'        => $abidar_primary_cat ? array( $abidar_primary_cat->term_id ) : array(),
								'ignore_sticky_posts' => true,
								'no_found_rows'       => true,
							)
						);

						if ( abidar_get_option( 'post_related_articles', true ) && $abidar_related->have_posts() ) :
							?>
							<section class="abidar-related-articles">
								<div class="abidar-section__head abidar-section__head--compact">
									<h2 class="abidar-section__title"><?php echo esc_html( abidar_t( 'related_articles' ) ); ?></h2>
								</div>
								<div class="abidar-blog-grid">
									<?php
									while ( $abidar_related->have_posts() ) :
										$abidar_related->the_post();
										abidar_article_card();
									endwhile;
									wp_reset_postdata();
									?>
								</div>
							</section>
						<?php endif; ?>

						<?php
						if ( comments_open() || get_comments_number() ) {
							comments_template();
						}
						?>
					</div>

					<!-- سایدبار مقاله: فهرست مطالب چسبان + ابزارک‌ها -->
					<aside class="abidar-single-layout__aside" aria-label="<?php esc_attr_e( 'اطلاعات جانبی مقاله', 'abidar-group' ); ?>">
						<?php if ( abidar_get_option( 'post_sticky_toc', true ) ) : ?>
							<div class="abidar-toc-card" data-toc>
								<h4 class="abidar-toc-card__title"><span aria-hidden="true">📑</span> <?php echo esc_html( abidar_t( 'toc_title' ) ); ?></h4>
								<ol class="abidar-toc-card__list" id="abidar-toc-list"></ol>
							</div>
						<?php endif; ?>

						<div class="abidar-sidebar-cta">
							<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'holding_badge' ) ); ?></span>
							<h4><?php esc_html_e( 'درباره گروه هلدینگ آبیدر', 'abidar-group' ); ?></h4>
							<p><?php echo esc_html( abidar_t( 'footer_about' ) ); ?></p>
							<a href="<?php echo esc_url( abidar_page_url( 'our-team' ) ); ?>" class="abidar-btn abidar-btn--gold abidar-btn--sm abidar-btn--full">
								<?php echo esc_html( abidar_t( 'nav_team' ) ); ?>
							</a>
						</div>

						<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
							<?php dynamic_sidebar( 'sidebar-blog' ); ?>
						<?php endif; ?>
					</aside>
				</div>
			</div>
		</div>

		<!-- پیمایش بین مقالات -->
		<nav class="abidar-post-nav" aria-label="<?php esc_attr_e( 'پیمایش مقالات', 'abidar-group' ); ?>">
			<div class="abidar-container abidar-post-nav__inner">
				<?php
				$abidar_prev = get_previous_post();
				$abidar_next = get_next_post();

				if ( $abidar_prev ) :
					?>
					<a href="<?php echo esc_url( get_permalink( $abidar_prev ) ); ?>" class="abidar-post-nav__item abidar-post-nav__item--prev">
						<span class="abidar-post-nav__label">→ <?php esc_html_e( 'مقاله قبلی', 'abidar-group' ); ?></span>
						<strong><?php echo esc_html( wp_trim_words( get_the_title( $abidar_prev ), 10 ) ); ?></strong>
					</a>
				<?php endif; ?>

				<?php
				if ( $abidar_next ) :
					?>
					<a href="<?php echo esc_url( get_permalink( $abidar_next ) ); ?>" class="abidar-post-nav__item abidar-post-nav__item--next">
						<span class="abidar-post-nav__label"><?php esc_html_e( 'مقاله بعدی', 'abidar-group' ); ?> ←</span>
						<strong><?php echo esc_html( wp_trim_words( get_the_title( $abidar_next ), 10 ) ); ?></strong>
					</a>
				<?php endif; ?>
			</div>
		</nav>
	</article>

	<?php
endwhile;

get_footer();
