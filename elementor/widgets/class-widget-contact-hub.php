<?php
/**
 * Elementor Widget: Abidar Contact Hub & Secure Inquiry Form (v2.0.0)
 *
 * المان اختصاصی تماس با ما شامل دایرکتوری چندبخشی معاونت‌ها، فرم استعلام امن ضد اسپم
 * (هانی‌پات + تله زمانی + کپچای ریاضی از ماژول امنیتی) و نقشه دفاتر بین‌المللی
 * با ۳ چیدمان مختلف و کنترل کامل استایل در المنتور رایگان
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Contact_Hub
 */
class Abidar_Widget_Contact_Hub extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_contact_hub';
	}

	public function get_title() {
		return esc_html__( 'تماس با ما و دایرکتوری معاونت‌ها (۳ چیدمان + فرم امن)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return array( 'abidar-elements' );
	}

	protected function register_controls() {
		if ( ! class_exists( '\Elementor\Controls_Manager' ) ) {
			return;
		}

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'چیدمان و محتوای بخش تماس', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح تماس (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: دایرکتوری کارت‌های معاونت‌ها + فرم استعلام دو ستونه', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: پنل تیره صنعتی با جدول دفاتر بین‌المللی و فرم امن', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: مرکز ارتباط مینیمال با آیکون‌های بزرگ', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'show_form',
			array(
				'label'        => esc_html__( 'نمایش فرم استعلام امن', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_offices',
			array(
				'label'        => esc_html__( 'نمایش جدول دفاتر بین‌المللی', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'استایل فرم و کارت‌ها', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'form_accent_color',
			array(
				'label'     => esc_html__( 'رنگ دکمه ارسال و تاکید فرم', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#D97706',
				'selectors' => array(
					'{{WRAPPER}} .abidar-contact-sec' => '--abidar-primary: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		self::render_output( $settings );
	}

	/**
	 * رندر خروجی بخش تماس با ۳ چیدمان
	 *
	 * @param array $settings تنظیمات ویجت.
	 */
	public static function render_output( $settings = array() ) {
		$layout       = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'layout_1';
		$show_form    = ! isset( $settings['show_form'] ) || 'yes' === $settings['show_form'];
		$show_offices = ! isset( $settings['show_offices'] ) || 'yes' === $settings['show_offices'];
		$departments  = abidar_get_option( 'floating_departments', array() );
		$socials      = abidar_get_option( 'floating_socials', array() );
		?>
		<section class="abidar-section abidar-contact-sec abidar-contact-sec--<?php echo esc_attr( $layout ); ?>" id="contact-section" data-widget-section="contact" data-active-layout="<?php echo esc_attr( $layout ); ?>">
			<div class="abidar-container">
				<div class="abidar-section__head">
					<div>
						<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'contact_eyebrow' ) ); ?></span>
						<h2 class="abidar-section__title"><?php echo esc_html( abidar_t( 'contact_heading' ) ); ?></h2>
					</div>

					<div class="abidar-layout-switcher">
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_1' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_1">طرح ۱: دایرکتوری + فرم</button>
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_2' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_2">طرح ۲: پنل صنعتی</button>
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_3' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_3">طرح ۳: مینیمال</button>
					</div>
				</div>

				<!-- دایرکتوری معاونت‌های تخصصی هلدینگ -->
				<div class="abidar-contact-depts">
					<?php foreach ( $departments as $dept ) : ?>
						<?php
						$dept_name  = abidar_multilang_val( $dept, 'name', $dept['name_fa'] );
						$dept_role  = abidar_multilang_val( $dept, 'role', $dept['role_fa'] );
						$dept_hours = abidar_multilang_val( $dept, 'hours', '' );
						$badge_clr  = isset( $dept['badge_color'] ) ? $dept['badge_color'] : '#D97706';
						?>
						<div class="abidar-contact-dept" style="--dept-color: <?php echo esc_attr( $badge_clr ); ?>;">
							<div class="abidar-contact-dept__head">
								<span class="abidar-contact-dept__icon"><?php echo Abidar_Floating_Contact::get_svg( $dept['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<div>
									<h3><?php echo esc_html( $dept_name ); ?></h3>
									<p><?php echo esc_html( $dept_role ); ?></p>
								</div>
							</div>
							<ul class="abidar-contact-dept__info">
								<li><span>📞</span><a href="tel:<?php echo esc_attr( $dept['phone'] ); ?>" dir="ltr"><?php echo esc_html( $dept['phone'] ); ?></a></li>
								<li><span>✉️</span><a href="mailto:<?php echo esc_attr( $dept['email'] ); ?>" dir="ltr"><?php echo esc_html( $dept['email'] ); ?></a></li>
								<?php if ( ! empty( $dept_hours ) ) : ?>
									<li><span>⏱️</span><?php echo esc_html( $dept_hours ); ?></li>
								<?php endif; ?>
							</ul>
							<a href="<?php echo esc_url( $dept['whatsapp'] ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-contact-dept__wa">
								<span>💬</span> WhatsApp Business
							</a>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="abidar-contact-main">
					<?php if ( $show_form ) : ?>
						<!-- فرم استعلام امن چندلایه (Nonce + Honeypot + Time-trap + Math Captcha) -->
						<div class="abidar-contact-form-wrap">
							<div class="abidar-contact-form-wrap__head">
								<h3><?php echo esc_html( abidar_t( 'contact_form_title' ) ); ?></h3>
								<span class="abidar-secure-badge">🛡️ SSL / AES-256 <?php esc_html_e( 'ارسال امن', 'abidar-group' ); ?></span>
							</div>

							<form id="abidar-contact-form" class="abidar-contact-form" method="post" novalidate
								  data-nonce="<?php echo esc_attr( wp_create_nonce( 'abidar_frontend_nonce' ) ); ?>">
								<div class="abidar-form-row">
									<div class="abidar-form-field">
										<label for="abidar-cf-name"><?php echo esc_html( abidar_t( 'contact_name' ) ); ?> <span>*</span></label>
										<input type="text" id="abidar-cf-name" name="abidar_name" required maxlength="120" autocomplete="name" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی یا عنوان شرکت', 'abidar-group' ); ?>">
									</div>
									<div class="abidar-form-field">
										<label for="abidar-cf-email"><?php echo esc_html( abidar_t( 'contact_email' ) ); ?> <span>*</span></label>
										<input type="email" id="abidar-cf-email" name="abidar_email" required maxlength="180" autocomplete="email" dir="ltr" placeholder="name@company.com">
									</div>
								</div>

								<div class="abidar-form-row">
									<div class="abidar-form-field">
										<label for="abidar-cf-phone"><?php echo esc_html( abidar_t( 'contact_phone' ) ); ?></label>
										<input type="tel" id="abidar-cf-phone" name="abidar_phone" maxlength="32" autocomplete="tel" dir="ltr" placeholder="+98 21 8810 4000">
									</div>
									<div class="abidar-form-field">
										<label for="abidar-cf-dept"><?php echo esc_html( abidar_t( 'contact_department' ) ); ?> <span>*</span></label>
										<select id="abidar-cf-dept" name="abidar_department" required>
											<?php foreach ( $departments as $dept ) : ?>
												<option value="<?php echo esc_attr( $dept['id'] ); ?>">
													<?php echo esc_html( abidar_multilang_val( $dept, 'name', $dept['name_fa'] ) ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>

								<div class="abidar-form-field">
									<label for="abidar-cf-subject"><?php echo esc_html( abidar_t( 'contact_subject' ) ); ?> <span>*</span></label>
									<input type="text" id="abidar-cf-subject" name="abidar_subject" required maxlength="200" placeholder="<?php esc_attr_e( 'موضوع همکاری، استعلام قیمت یا درخواست فنی', 'abidar-group' ); ?>">
								</div>

								<div class="abidar-form-field">
									<label for="abidar-cf-message"><?php echo esc_html( abidar_t( 'contact_message' ) ); ?> <span>*</span></label>
									<textarea id="abidar-cf-message" name="abidar_message" rows="5" required maxlength="4000" placeholder="<?php esc_attr_e( 'شرح کامل درخواست، ظرفیت مورد نیاز و بازه زمانی مدنظر...', 'abidar-group' ); ?>"></textarea>
								</div>

								<!-- فیلدهای امنیتی ضد اسپم (هانی‌پات + تله زمانی + کپچای ریاضی) که توسط ماژول امنیتی رندر و اعتبارسنجی می‌شوند -->
								<div class="abidar-form-security">
									<?php Abidar_Security::render_antispam_fields(); ?>
								</div>

								<label class="abidar-checkbox">
									<input type="checkbox" name="abidar_consent" value="1" required>
									<span><?php esc_html_e( 'با سیاست حفظ حریم خصوصی و پردازش امن اطلاعات موافقم.', 'abidar-group' ); ?></span>
								</label>

								<button type="submit" class="abidar-btn abidar-btn--gold abidar-btn--lg abidar-btn--block">
									<span class="abidar-btn__text"><?php echo esc_html( abidar_t( 'contact_submit' ) ); ?></span>
									<span class="abidar-btn__spinner" aria-hidden="true"></span>
								</button>

								<div class="abidar-form-feedback" role="alert" aria-live="polite"></div>
							</form>
						</div>
					<?php endif; ?>

					<div class="abidar-contact-side">
						<?php if ( $show_offices ) : ?>
							<div class="abidar-offices-card">
								<h4><?php esc_html_e( 'دفاتر و نمایندگی‌های بین‌المللی', 'abidar-group' ); ?></h4>
								<ul class="abidar-offices-list">
									<li>
										<strong>🇮🇷 <?php esc_html_e( 'دفتر مرکزی - تهران', 'abidar-group' ); ?></strong>
										<span><?php esc_html_e( 'بلوار آفریقا، برج ستاره آبیدر، طبقات ۱۲ تا ۱۸', 'abidar-group' ); ?></span>
										<a href="tel:+982188104000" dir="ltr">+98 21 8810 4000</a>
									</li>
									<li>
										<strong>🇦🇪 <?php esc_html_e( 'دفتر خاورمیانه - دبی', 'abidar-group' ); ?></strong>
										<span>DIFC, Gate Village 8, Level 14, Dubai, UAE</span>
										<a href="tel:+97145502900" dir="ltr">+971 4 550 2900</a>
									</li>
									<li>
										<strong>🇨🇳 <?php esc_html_e( 'دفتر شرق آسیا - شانگهای', 'abidar-group' ); ?></strong>
										<span>Pudong Financial Plaza, Lujiazui, Shanghai</span>
										<a href="tel:+862168809100" dir="ltr">+86 21 6880 9100</a>
									</li>
								</ul>
							</div>
						<?php endif; ?>

						<div class="abidar-social-card">
							<h4><?php esc_html_e( 'شبکه‌های ارتباطی رسمی هلدینگ', 'abidar-group' ); ?></h4>
							<div class="abidar-social-grid">
								<?php if ( ! empty( $socials['linkedin'] ) ) : ?>
									<a href="<?php echo esc_url( $socials['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-social-tile">in<span>LinkedIn</span></a>
								<?php endif; ?>
								<?php if ( ! empty( $socials['telegram'] ) ) : ?>
									<a href="<?php echo esc_url( $socials['telegram'] ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-social-tile">✈<span>Telegram</span></a>
								<?php endif; ?>
								<?php if ( ! empty( $socials['whatsapp'] ) ) : ?>
									<a href="<?php echo esc_url( $socials['whatsapp'] ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-social-tile">💬<span>WhatsApp</span></a>
								<?php endif; ?>
								<?php if ( ! empty( $socials['instagram'] ) ) : ?>
									<a href="<?php echo esc_url( $socials['instagram'] ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-social-tile">📷<span>Instagram</span></a>
								<?php endif; ?>
								<?php if ( ! empty( $socials['wechat'] ) ) : ?>
									<span class="abidar-social-tile">微<span>WeChat: <?php echo esc_html( $socials['wechat'] ); ?></span></span>
								<?php endif; ?>
								<?php if ( ! empty( $socials['email'] ) ) : ?>
									<a href="mailto:<?php echo esc_attr( $socials['email'] ); ?>" class="abidar-social-tile">✉<span><?php echo esc_html( $socials['email'] ); ?></span></a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
