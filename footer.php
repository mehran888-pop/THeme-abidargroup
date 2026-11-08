<?php
/**
 * Abidar Group Holding — Main Footer Template (v2.0.0)
 *
 * فوتر اصلی قالب: فوتر ساخته‌شده با المنتور یا ویجت اختصاصی، دکمه شناور ارتباط،
 * و ناوبری حرفه‌ای موبایل (۴ چیدمان قابل انتخاب در پیشخوان).
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main><!-- #abidar-main-content -->

<?php
// فوتر: رندر فوتر المنتور رایگان یا ویجت اختصاصی فوتر هلدینگ.
Abidar_Elementor_Init::render_site_footer();

// دکمه شناور «تماس با ما / ارتباط» با دایرکتوری چندبخشه و شبکه‌های اجتماعی.
Abidar_Floating_Contact::render();

// ناوبری موبایل حرفه‌ای (Bottom App Bar / Dock / Drawer / Fullscreen).
Abidar_Mobile_Nav::render();
?>

<?php wp_footer(); ?>
</body>
</html>
