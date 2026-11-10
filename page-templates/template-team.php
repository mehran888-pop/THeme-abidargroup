<?php
/**
 * Template Name: آبیدر — تیم ما (Board & Executives)
 * Template Post Type: page
 *
 * قالب برگه «تیم ما»: هیئت مدیره، مدیران ارشد و ساختار رهبری گروه هلدینگ آبیدر.
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
			'title'    => abidar_t( 'nav_team' ),
			'subtitle' => abidar_t( 'section_team_sub' ),
			'eyebrow'  => abidar_t( 'holding_badge' ),
			'image'    => ABIDAR_URI . '/assets/images/sector-energy.jpg',
			'crumb'    => abidar_t( 'nav_team' ),
		)
	);

	Abidar_Widget_Team::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Contact_Hub::render_output( array( 'layout_style' => 'layout_3' ) );
}

get_footer();
