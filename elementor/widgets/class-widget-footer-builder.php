<?php
/**
 * Elementor Free Widget: Abidar Footer Builder (v2.0.0)
 *
 * المان اختصاصی ساخت فوتر با المنتور رایگان با ۳ چیدمان متفاوت و کنترل کامل استایل
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Footer_Builder
 */
class Abidar_Widget_Footer_Builder extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_footer_builder';
	}

	public function get_title() {
		return esc_html__( 'فوتر جامع هلدینگ آبیدر (۳ چیدمان)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_categories() {
		return array( 'abidar-header-footer' );
	}

	protected function register_controls() {
		if ( ! class_exists( '\Elementor\Controls_Manager' ) ) {
			return;
		}

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'چیدمان و محتوای فوتر', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح فوتر (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: مگافوتر ۴ ستونه صنعتی + دفاتر بین‌المللی (تهران، دبی، شانگهای)', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: فوتر دوطبقه مدیریتی با کارت‌های مستقیم معاونت‌ها', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: فوتر مینیمال شرکتی', 'abidar-group' ),
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
	 * رندر خروجی فوتر با پشتیبانی از ۳ چیدمان مختلف
	 *
	 * @param array $settings تنظیمات ویجت.
	 */
	public static function render_output( $settings = array() ) {
		$layout = isset( $settings['layout_style'] ) ? $settings['layout_style'] : abidar_get_option( 'footer_layout', 'layout_1' );
		?>
		<footer class="abidar-site-footer abidar-site-footer--<?php echo esc_attr( $layout ); ?>" role="contentinfo">
			<!-- نوار بالای فوتر: دفاتر بین‌المللی گروه آبیدر -->
			<div class="abidar-footer-offices">
				<div class="abidar-container abidar-footer-offices__grid">
					<div class="abidar-office-pill">
						<span class="abidar-office-pill__flag">🇮🇷</span>
						<div>
							<strong>دفتر مرکزی تهران (Tehran HQ)</strong>
							<small>بلوار آفریقا (جردن)، برج ستاره آبیدر، طبقات ۱۲ تا ۱۸ | 021-88104000</small>
						</div>
					</div>
					<div class="abidar-office-pill">
						<span class="abidar-office-pill__flag">🇦🇪</span>
						<div>
							<strong>دفتر منطقه‌ای خاورمیانه (Dubai Hub)</strong>
							<small>DIFC, Gate Village 8, Level 14, Dubai, UAE | +971-4-550-2900</small>
						</div>
					</div>
					<div class="abidar-office-pill">
						<span class="abidar-office-pill__flag">🇨🇳</span>
						<div>
							<strong>دفتر شرق آسیا و چین (Shanghai Office)</strong>
							<small>Pudong Financial Plaza, Lujiazui, Shanghai | +86-21-6880-9100</small>
						</div>
					</div>
				</div>
			</div>

			<div class="abidar-container abidar-site-footer__main">
				<div class="abidar-site-footer__col abidar-site-footer__col--brand">
					<div class="abidar-brand">
						<div class="abidar-brand__emblem">
							<svg width="34" height="34" viewBox="0 0 40 40" fill="none">
								<polygon points="20,3 37,33 3,33" stroke="#D97706" stroke-width="2.8" fill="rgba(217,119,6,0.12)"/>
								<polygon points="20,12 30,30 10,30" fill="#D97706"/>
							</svg>
						</div>
						<div class="abidar-brand__text">
							<span class="abidar-brand__title"><?php echo esc_html( abidar_t( 'holding_name' ) ); ?></span>
							<span class="abidar-brand__subtitle"><?php echo esc_html( abidar_t( 'holding_tagline' ) ); ?></span>
						</div>
					</div>
					<p class="abidar-site-footer__desc">
						<?php echo esc_html( abidar_t( 'footer_about' ) ); ?>
					</p>
					<div class="abidar-security-seal">
						<span>🛡️</span>
						<small><?php echo esc_html( abidar_t( 'security_badge' ) ); ?></small>
					</div>
				</div>

				<div class="abidar-site-footer__col">
					<h4><?php echo esc_html( abidar_t( 'section_services_title' ) ); ?></h4>
					<ul class="abidar-footer-links">
						<li><a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>"><?php echo esc_html( abidar_t( 'sector_mining' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>"><?php echo esc_html( abidar_t( 'sector_energy' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>"><?php echo esc_html( abidar_t( 'sector_oil' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'projects' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_projects' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'certificates' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_certificates' ) ); ?></a></li>
					</ul>
				</div>

				<div class="abidar-site-footer__col">
					<h4><?php esc_html_e( 'دسترسی سریع به برگه‌های سایت', 'abidar-group' ); ?></h4>
					<ul class="abidar-footer-links">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_home' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'about-us' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_about' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'our-team' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_team' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'blog' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_blog' ) ); ?></a></li>
						<li><a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>"><?php echo esc_html( abidar_t( 'nav_contact' ) ); ?></a></li>
					</ul>
				</div>

				<div class="abidar-site-footer__col">
					<h4><?php esc_html_e( 'استانداردها و گواهینامه‌های بین‌المللی', 'abidar-group' ); ?></h4>
					<div class="abidar-footer-iso-badges">
						<span class="abidar-iso-chip">ISO 9001:2015</span>
						<span class="abidar-iso-chip">ISO 14001:2015</span>
						<span class="abidar-iso-chip">ISO 45001:2018</span>
						<span class="abidar-iso-chip">ISO/TS 29001 (Oil & Gas)</span>
						<span class="abidar-iso-chip">ISO 50001 (Energy)</span>
						<span class="abidar-iso-chip">Grade-1 EPC</span>
					</div>
				</div>
			</div>

			<div class="abidar-site-footer__bottom">
				<div class="abidar-container abidar-site-footer__bottom-inner">
					<p><?php echo esc_html( abidar_t( 'footer_rights' ) ); ?></p>
					<span class="abidar-version-tag">Abidar Group Theme v<?php echo esc_html( ABIDAR_VERSION ); ?> | WP 6.7+ & Elementor Ready</span>
				</div>
			</div>
		</footer>
		<?php
	}
}
