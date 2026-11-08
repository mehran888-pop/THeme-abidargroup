<?php
/**
 * Abidar Group Holding — Comments Template (v2.0.0)
 *
 * قالب دیدگاه‌ها با استایل هم‌راستا با طراحی هلدینگ؛
 * فرم دیدگاه به صورت خودکار فیلدهای ضد اسپم ماژول امنیتی را دریافت می‌کند.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="abidar-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="abidar-comments__title">
			<?php
			$abidar_comment_count = get_comments_number();
			printf(
				/* translators: %s: comment count */
				esc_html( _n( '%s دیدگاه ثبت شده', '%s دیدگاه ثبت شده', $abidar_comment_count, 'abidar-group' ) ),
				esc_html( number_format_i18n( $abidar_comment_count ) )
			);
			?>
		</h2>

		<ol class="abidar-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 56,
					'callback'    => 'abidar_comment_template',
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => '→ ' . esc_html__( 'دیدگاه‌های قبلی', 'abidar-group' ),
				'next_text' => esc_html__( 'دیدگاه‌های بعدی', 'abidar-group' ) . ' ←',
				'class'     => 'abidar-pagination',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="abidar-comments__closed"><?php esc_html_e( 'امکان ثبت دیدگاه برای این مقاله غیرفعال شده است.', 'abidar-group' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => esc_html__( 'ثبت دیدگاه تخصصی', 'abidar-group' ),
			'title_reply_before'   => '<h3 id="reply-title" class="abidar-comments__reply-title">',
			'title_reply_after'    => '</h3>',
			'class_form'           => 'abidar-comment-form',
			'class_submit'         => 'abidar-btn abidar-btn--gold',
			'label_submit'         => esc_html__( 'ارسال دیدگاه', 'abidar-group' ),
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'متن دیدگاه', 'abidar-group' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required="required" placeholder="' . esc_attr__( 'دیدگاه، پیشنهاد فنی یا پرسش خود را بنویسید...', 'abidar-group' ) . '"></textarea></p>',
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'نشانی ایمیل شما منتشر نخواهد شد. دیدگاه‌ها پس از بررسی توسط دبیرخانه هلدینگ منتشر می‌شوند.', 'abidar-group' ) . '</p>',
			'comment_notes_after'  => '<p class="comment-form-security">🛡️ ' . esc_html__( 'این فرم با فناوری ضد اسپم و لایه‌های امنیتی گروه آبیدر محافظت می‌شود.', 'abidar-group' ) . '</p>',
		)
	);
	?>
</div>
<?php
