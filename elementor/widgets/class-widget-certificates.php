<?php
/**
 * Elementor Widget: Abidar Certificates, ISO Standards & Honours (v2.0.0)
 *
 * المان اختصاصی نمایش مدارک، گواهینامه‌های ISO و لوح‌های افتخار با ۳ چیدمان متفاوت و لایت‌باکس استعلام اصالت
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Certificates
 */
class Abidar_Widget_Certificates extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_certificates';
	}

	public function get_title() {
		return esc_html__( 'مدارک، استانداردها و لوح‌های آبیدر (۳ چیدمان)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-award';
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
				'label' => esc_html__( 'چیدمان مدارک و لوح‌ها', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح نمایش مدارک (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: ویترین قاب طلایی گواهینامه‌های ISO با بزرگنمایی لایت‌باکس', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: جدول و کارت‌های رسمی استعلام اصالت استانداردها', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: گرید سه‌بعدی لوح‌های افتخار و تندیس‌های ملی', 'abidar-group' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		self::render_output( $settings );
	}

	public static function render_output( $settings = array() ) {
		$layout       = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'layout_1';
		$certificates = Abidar_CPT::get_default_dataset( 'certificates' );
		?>
		<section class="abidar-section abidar-certs-sec" data-widget-section="certificates" data-active-layout="<?php echo esc_attr( $layout ); ?>">
			<div class="abidar-container">
				<div class="abidar-section__head">
					<div>
						<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'section_certs_sub' ) ); ?></span>
						<h2 class="abidar-section__title"><?php echo esc_html( abidar_t( 'section_certs_title' ) ); ?></h2>
					</div>

					<div class="abidar-layout-switcher">
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_1' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_1">طرح ۱: قاب طلایی</button>
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_2' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_2">طرح ۲: جدول استعلام</button>
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_3' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_3">طرح ۳: لوح سه‌بعدی</button>
					</div>
				</div>

				<div class="abidar-certs-grid abidar-widget-grid abidar-widget-grid--<?php echo esc_attr( $layout ); ?>">
					<?php foreach ( $certificates as $cert ) : ?>
						<?php
						$title    = abidar_multilang_val( $cert, 'title', $cert['title_fa'] );
						$issuer   = abidar_multilang_val( $cert, 'issuer', $cert['issuer_fa'] );
						$category = abidar_multilang_val( $cert, 'category', $cert['category_fa'] );
						?>
						<article class="abidar-cert-card"
								 data-cert-title="<?php echo esc_attr( $title ); ?>"
								 data-cert-issuer="<?php echo esc_attr( $issuer ); ?>"
								 data-cert-code="<?php echo esc_attr( $cert['code'] ); ?>"
								 data-cert-year="<?php echo esc_attr( $cert['year'] ); ?>">
							<div class="abidar-cert-card__seal">
								<span class="abidar-cert-card__badge"><?php echo esc_html( $cert['badge'] ); ?></span>
								<span class="abidar-cert-card__year"><?php echo esc_html( $cert['year'] ); ?></span>
							</div>

							<div class="abidar-cert-card__body">
								<span class="abidar-cert-card__cat"><?php echo esc_html( $category ); ?></span>
								<h3 class="abidar-cert-card__title"><?php echo esc_html( $title ); ?></h3>

								<div class="abidar-cert-card__meta">
									<div>
										<small><?php echo esc_html( abidar_t( 'cert_issuer' ) ); ?></small>
										<strong><?php echo esc_html( $issuer ); ?></strong>
									</div>
									<div>
										<small><?php echo esc_html( abidar_t( 'cert_code' ) ); ?></small>
										<code dir="ltr"><?php echo esc_html( $cert['code'] ); ?></code>
									</div>
								</div>

								<button type="button" class="abidar-btn abidar-btn--outline-gold abidar-btn--sm" data-open-cert-modal>
									🔍 <?php echo esc_html( abidar_t( 'cert_verify' ) ); ?>
								</button>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- مودال لایت‌باکس نمایش و استعلام اصالت گواهینامه -->
			<div id="abidar-cert-lightbox" class="abidar-cert-lightbox" aria-hidden="true">
				<div class="abidar-cert-lightbox__backdrop" data-close-cert-modal></div>
				<div class="abidar-cert-lightbox__dialog">
					<button type="button" class="abidar-cert-lightbox__close" data-close-cert-modal>&times;</button>
					<div class="abidar-cert-lightbox__certificate">
						<div class="abidar-cert-lightbox__crest">🏆 ABIDAR GROUP HOLDING OFFICIAL ACCREDITATION</div>
						<h3 id="abidar-cert-modal-title"></h3>
						<p id="abidar-cert-modal-issuer"></p>
						<div class="abidar-cert-lightbox__stamp">
							<span id="abidar-cert-modal-code"></span>
							<strong id="abidar-cert-modal-year"></strong>
							<span class="abidar-verified-pill">✓ VERIFIED &amp; ACTIVE</span>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
