<?php
/**
 * Template Name: آبیدر — خدمات (Services Portfolio)
 * Template Post Type: page
 *
 * قالب برگه «خدمات»: نمایش خدمات هلدینگ در سه حوزه معادن، انرژی و نفت با چیدمان‌های المنتور.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( abidar_is_elementor_page() ) {
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
			'title'    => abidar_t( 'nav_services' ),
			'subtitle' => abidar_t( 'section_services_sub' ),
			'eyebrow'  => abidar_t( 'holding_tagline' ),
			'image'    => ABIDAR_URI . '/assets/images/sector-energy.jpg',
			'crumb'    => abidar_t( 'nav_services' ),
		)
	);

	Abidar_Widget_Services::render_output( array( 'layout_style' => 'layout_2' ) );
	Abidar_Widget_Contact_Hub::render_output( array( 'layout_style' => 'layout_2' ) );
}

get_footer();
