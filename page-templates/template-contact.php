<?php
/**
 * Template Name: آبیدر — تماس با ما (Contact Divisions)
 * Template Post Type: page
 *
 * قالب برگه «تماس با ما»: دایرکتوری معاونت‌ها، دفاتر بین‌المللی، شبکه‌های اجتماعی و فرم استعلام امن.
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
			'title'    => abidar_t( 'nav_contact' ),
			'subtitle' => abidar_t( 'contact_form_title' ),
			'eyebrow'  => abidar_t( 'holding_badge' ),
			'image'    => ABIDAR_URI . '/assets/images/sector-oil.jpg',
			'crumb'    => abidar_t( 'nav_contact' ),
		)
	);

	Abidar_Widget_Contact_Hub::render_output( array( 'layout_style' => 'layout_1' ) );
}

get_footer();
