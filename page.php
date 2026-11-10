<?php
/**
 * Abidar Group Holding — Default Page Template (v2.0.0)
 *
 * قالب پیش‌فرض برگه‌های سایت: در صورت ساخت با المنتور به صورت تمام‌عرض رندر می‌شود؛
 * در غیر این صورت عنوان صفحه و محتوای استاندارد وردپرس در چیدمان حرفه‌ای نمایش داده می‌شود.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( abidar_is_elementor_page() || ( class_exists( 'Abidar_Elementor_Init' ) && Abidar_Elementor_Init::is_elementor_editor_context() ) ) {
	?>
	<div class="abidar-elementor-content">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
	<?php
} else {
	while ( have_posts() ) :
		the_post();

		abidar_page_hero(
			array(
				'title' => get_the_title(),
				'crumb' => get_the_title(),
			)
		);
		?>
		<div class="abidar-section">
			<div class="abidar-container abidar-entry-content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<nav class="abidar-page-links">' . esc_html__( 'صفحات:', 'abidar-group' ),
						'after'  => '</nav>',
					)
				);

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</div>
		<?php
	endwhile;
}

get_footer();
