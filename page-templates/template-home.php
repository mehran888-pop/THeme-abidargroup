<?php
/**
 * Template Name: آبیدر — صفحه اصلی (Home Landing)
 * Template Post Type: page
 *
 * قالب برگه «صفحه اصلی»: اگر برگه با المنتور طراحی شده باشد همان محتوا رندر می‌شود،
 * در غیر این صورت دموی کامل صفحه اصلی با ویجت‌های اختصاصی هلدینگ نمایش داده می‌شود.
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
	Abidar_Widget_Hero_Slider::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_About_Timeline::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Services::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Projects::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Team::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Certificates::render_output( array( 'layout_style' => 'layout_1' ) );
	Abidar_Widget_Contact_Hub::render_output( array( 'layout_style' => 'layout_1' ) );
}

get_footer();
