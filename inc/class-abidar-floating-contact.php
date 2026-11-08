<?php
/**
 * Abidar Group Floating Multi-Department Contact & Social Hub (v2.0.0)
 *
 * دکمه شناور تماس با ما و ارتباط چندبخشی هلدینگ آبیدر:
 * با کلیک روی دکمه شناور، لیست بخش‌های مختلف هلدینگ (معادن، انرژی، نفت و گاز، امور بین‌الملل و سهامداران)
 * به همراه شماره مستقیم، داخلی، واتساپ، ایمیل و لینک شبکه‌های اجتماعی با آیکون نمایش داده می‌شود.
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Floating_Contact
 */
class Abidar_Floating_Contact {

	/**
	 * دریافت آیکون SVG برای هر بخش یا شبکه اجتماعی
	 *
	 * @param string $name نام آیکون.
	 * @return string
	 */
	public static function get_svg( $name ) {
		switch ( $name ) {
			case 'pickaxe':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 3.5C18 5 20.5 8.5 21 12l-2.5 1.5c-1-3-3-5.5-6-7L14.5 3.5z"/><path d="M3 21l9.5-9.5"/><path d="M9.5 4.5 19.5 14.5"/></svg>';
			case 'bolt':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
			case 'flame':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>';
			case 'globe':
				return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>';
			case 'linkedin':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>';
			case 'telegram':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>';
			case 'whatsapp':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>';
			case 'instagram':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>';
			case 'email':
			default:
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
		}
	}

	/**
	 * رندر کامل ویجت شناور تماس چندبخشی در فوتر سایت
	 */
	public static function render() {
		if ( ! abidar_get_option( 'floating_contact_enabled', true ) ) {
			return;
		}

		$all_options = abidar_get_all_options();
		$position    = abidar_get_option( 'floating_position', 'bottom-left' );
		$pulse       = abidar_get_option( 'floating_pulse', true );
		$title       = abidar_multilang_val( $all_options, 'floating_title', abidar_t( 'cta_contact_us' ) );
		$subtitle    = abidar_multilang_val( $all_options, 'floating_subtitle', '' );
		$departments = abidar_get_option( 'floating_departments', array() );
		$socials     = abidar_get_option( 'floating_socials', array() );
		?>
		<div id="abidar-floating-hub" class="abidar-floating-hub abidar-floating-hub--<?php echo esc_attr( $position ); ?>">
			<!-- پنجره پاپ‌آپ تفکیک‌شده بخش‌ها و شبکه‌های اجتماعی -->
			<div id="abidar-floating-modal" class="abidar-floating-hub__panel" aria-hidden="true">
				<div class="abidar-floating-hub__header">
					<div>
						<span class="abidar-floating-hub__status-dot"></span>
						<h4 class="abidar-floating-hub__title"><?php echo esc_html( $title ); ?></h4>
						<?php if ( ! empty( $subtitle ) ) : ?>
							<p class="abidar-floating-hub__subtitle"><?php echo esc_html( $subtitle ); ?></p>
						<?php endif; ?>
					</div>
					<button type="button" class="abidar-floating-hub__close" data-toggle-floating-hub aria-label="<?php esc_attr_e( 'بستن', 'abidar-group' ); ?>">
						&times;
					</button>
				</div>

				<!-- لیست بخش‌های مختلف هلدینگ (معادن، انرژی، نفت، بین‌الملل) -->
				<div class="abidar-floating-hub__departments">
					<?php foreach ( $departments as $dept ) : ?>
						<?php
						$dept_name  = abidar_multilang_val( $dept, 'name', isset( $dept['name_fa'] ) ? $dept['name_fa'] : '' );
						$dept_role  = abidar_multilang_val( $dept, 'role', isset( $dept['role_fa'] ) ? $dept['role_fa'] : '' );
						$dept_hours = abidar_multilang_val( $dept, 'hours', isset( $dept['hours_fa'] ) ? $dept['hours_fa'] : '' );
						$phone      = isset( $dept['phone'] ) ? $dept['phone'] : '';
						$ext        = isset( $dept['ext'] ) ? $dept['ext'] : '';
						$whatsapp   = isset( $dept['whatsapp'] ) ? $dept['whatsapp'] : '';
						$email      = isset( $dept['email'] ) ? $dept['email'] : '';
						$icon       = isset( $dept['icon'] ) ? $dept['icon'] : 'pickaxe';
						$badge_clr  = isset( $dept['badge_color'] ) ? $dept['badge_color'] : '#D97706';
						?>
						<div class="abidar-dept-card" style="--dept-color: <?php echo esc_attr( $badge_clr ); ?>;">
							<div class="abidar-dept-card__top">
								<span class="abidar-dept-card__icon">
									<?php echo self::get_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
								<div class="abidar-dept-card__info">
									<h5><?php echo esc_html( $dept_name ); ?></h5>
									<p><?php echo esc_html( $dept_role ); ?></p>
									<?php if ( ! empty( $dept_hours ) ) : ?>
										<small class="abidar-dept-card__hours"><?php echo esc_html( $dept_hours ); ?></small>
									<?php endif; ?>
								</div>
								<?php if ( ! empty( $ext ) ) : ?>
									<span class="abidar-dept-card__ext">داخلی <?php echo esc_html( $ext ); ?></span>
								<?php endif; ?>
							</div>
							<div class="abidar-dept-card__actions">
								<?php if ( ! empty( $phone ) ) : ?>
									<a href="tel:<?php echo esc_attr( $phone ); ?>" class="abidar-dept-btn abidar-dept-btn--call">
										<span>📞</span>
										<span dir="ltr"><?php echo esc_html( $phone ); ?></span>
									</a>
								<?php endif; ?>
								<?php if ( ! empty( $whatsapp ) ) : ?>
									<a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-dept-btn abidar-dept-btn--wa" title="WhatsApp">
										<?php echo self::get_svg( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								<?php endif; ?>
								<?php if ( ! empty( $email ) ) : ?>
									<a href="mailto:<?php echo esc_attr( $email ); ?>" class="abidar-dept-btn abidar-dept-btn--mail" title="<?php echo esc_attr( $email ); ?>">
										<?php echo self::get_svg( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- نوار آیکون‌های شبکه‌های اجتماعی هلدینگ در پایین ویجت شناور -->
				<div class="abidar-floating-hub__socials">
					<span class="abidar-floating-hub__socials-label">شبکه‌های رسمی هلدینگ:</span>
					<div class="abidar-floating-hub__socials-icons">
						<?php if ( ! empty( $socials['linkedin'] ) ) : ?>
							<a href="<?php echo esc_url( $socials['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
								<?php echo self::get_svg( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $socials['telegram'] ) ) : ?>
							<a href="<?php echo esc_url( $socials['telegram'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Telegram">
								<?php echo self::get_svg( 'telegram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $socials['whatsapp'] ) ) : ?>
							<a href="<?php echo esc_url( $socials['whatsapp'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
								<?php echo self::get_svg( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $socials['instagram'] ) ) : ?>
							<a href="<?php echo esc_url( $socials['instagram'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
								<?php echo self::get_svg( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $socials['email'] ) ) : ?>
							<a href="mailto:<?php echo esc_attr( $socials['email'] ); ?>" aria-label="Email">
								<?php echo self::get_svg( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- دکمه شناور اصلی (Floating Trigger Button) -->
			<button type="button" class="abidar-floating-hub__trigger <?php echo $pulse ? 'has-pulse' : ''; ?>" data-toggle-floating-hub aria-expanded="false" aria-controls="abidar-floating-modal">
				<span class="abidar-floating-hub__trigger-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
						<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
					</svg>
				</span>
				<span class="abidar-floating-hub__trigger-text">
					<?php echo esc_html( abidar_t( 'cta_contact_us' ) ); ?>
				</span>
				<span class="abidar-floating-hub__badge"><?php echo count( $departments ); ?></span>
			</button>
		</div>
		<?php
	}
}
