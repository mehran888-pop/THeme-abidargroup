<?php
/**
 * Template Name: آبیدر — درباره ما (About & ESG)
 * Template Post Type: page
 *
 * قالب برگه «درباره ما»: معرفی هلدینگ، شاخص‌های ESG، خط زمانی تاریخچه، هیئت مدیره و مدارک.
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
	abidar_page_hero(
		array(
			'title'    => abidar_t( 'nav_about' ),
			'subtitle' => abidar_t( 'footer_about' ),
			'eyebrow'  => abidar_t( 'holding_badge' ),
			'image'    => ABIDAR_URI . '/assets/images/sector-mining.jpg',
			'crumb'    => abidar_t( 'nav_about' ),
		)
	);

	Abidar_Widget_About_Timeline::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Team::render_output( array( 'layout_style' => 'layout_2' ) );
	Abidar_Widget_Certificates::render_output( array( 'layout_style' => 'layout_2' ) );
}

get_footer();
