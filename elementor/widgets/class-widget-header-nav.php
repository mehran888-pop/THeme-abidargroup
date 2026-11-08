<?php
/**
 * Elementor Free Widget: Abidar Header & Mega Navigation (v2.0.0)
 *
 * المان اختصاصی ساخت هدر با المنتور رایگان با ۴ چیدمان متفاوت و کنترل کامل استایل
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Header_Nav
 */
class Abidar_Widget_Header_Nav extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_header_nav';
	}

	public function get_title() {
		return esc_html__( 'هدر و مگامنوی گروه آبیدر (۴ چیدمان)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return array( 'abidar-header-footer' );
	}

	/**
	 * ثبت کنترل‌های محتوا و استایل در ویرایشگر المنتور رایگان
	 */
	protected function register_controls() {
		if ( ! class_exists( '\Elementor\Controls_Manager' ) ) {
			return;
		}

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'چیدمان و اجزای هدر', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح هدر (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: هدر دوطبقه اجرایی هلدینگ (Executive Dual-Bar)', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: هدر شیشه‌ای شفاف صنعتی (Transparent Glass)', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: هدر لوگو وسط تشریفاتی (Centered Luxury)', 'abidar-group' ),
					'layout_4' => esc_html__( 'طرح ۴: هدر فشرده با شاخص جهانی معادن و نفت (Ticker)', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'show_topbar',
			array(
				'label'        => esc_html__( 'نمایش نوار بالای هدر (Topbar)', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'abidar-group' ),
				'label_off'    => esc_html__( 'خیر', 'abidar-group' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_lang_switcher',
			array(
				'label'        => esc_html__( 'نمایش سوییچر تغییر زبان (FA/EN/ZH+)', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_cta_btn',
			array(
				'label'        => esc_html__( 'نمایش دکمه ارتباط با معاونت‌ها', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		// بخش استایل المنتور
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'استایل و رنگ‌بندی هدر', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'header_bg_color',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه هدر', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#07101E',
				'selectors' => array(
					'{{WRAPPER}} .abidar-site-header' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_accent_color',
			array(
				'label'     => esc_html__( 'رنگ طلایی/تاکیدی لینک‌ها و دکمه', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#D97706',
				'selectors' => array(
					'{{WRAPPER}} .abidar-site-header' => '--abidar-primary: {{VALUE}};',
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
	 * رندر خروجی HTML هدر با پشتیبانی از ۴ چیدمان متفاوت
	 *
	 * @param array $settings تنظیمات ویجت.
	 */
	public static function render_output( $settings = array() ) {
		$layout     = isset( $settings['layout_style'] ) ? $settings['layout_style'] : abidar_get_option( 'header_layout', 'layout_1' );
		$show_top   = ! isset( $settings['show_topbar'] ) || 'yes' === $settings['show_topbar'];
		$show_lang  = ! isset( $settings['show_lang_switcher'] ) || 'yes' === $settings['show_lang_switcher'];
		$show_cta   = ! isset( $settings['show_cta_btn'] ) || 'yes' === $settings['show_cta_btn'];
		$languages  = Abidar_I18n::get_languages();
		$curr_lang  = Abidar_I18n::get_current_lang();
		$curr_page  = is_front_page() ? 'home' : (string) get_post_field( 'post_name', get_queried_object_id() );
		?>
		<header class="abidar-site-header abidar-site-header--<?php echo esc_attr( $layout ); ?>" role="banner">
			<?php if ( $show_top ) : ?>
				<div class="abidar-topbar">
					<div class="abidar-container abidar-topbar__inner">
						<div class="abidar-topbar__tickers">
							<span class="abidar-ticker-badge">
								<span class="abidar-dot abidar-dot--gold"></span>
								<?php echo esc_html( abidar_t( 'holding_badge' ) ); ?>
							</span>
							<span class="abidar-ticker-item" dir="ltr">CU (Copper): <strong>$9,640/t ▲ +1.4%</strong></span>
							<span class="abidar-ticker-item" dir="ltr">BRENT OIL: <strong>$78.85/bbl ▲ +0.8%</strong></span>
							<span class="abidar-ticker-item" dir="ltr">POWER GRID: <strong>4,200 MW ACTIVE</strong></span>
						</div>

						<div class="abidar-topbar__right">
							<a href="tel:+982188104000" class="abidar-topbar__phone" dir="ltr">📞 +98 (21) 8810-4000</a>
							<?php if ( $show_lang ) : ?>
								<div class="abidar-lang-bar" role="navigation" aria-label="<?php esc_attr_e( 'انتخاب زبان سایت', 'abidar-group' ); ?>">
									<?php foreach ( $languages as $code => $l_data ) : ?>
										<a href="<?php echo esc_url( Abidar_I18n::get_language_url( $code ) ); ?>"
										   class="abidar-lang-bar__item <?php echo ( $curr_lang === $code ) ? 'is-active' : ''; ?>"
										   title="<?php echo esc_attr( $l_data['name'] ); ?>">
											<span class="abidar-lang-bar__flag"><?php echo esc_html( $l_data['flag'] ); ?></span>
											<span class="abidar-lang-bar__label"><?php echo esc_html( $l_data['native_name'] ); ?></span>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="abidar-navbar">
				<div class="abidar-container abidar-navbar__inner">
					<!-- برند و لوگوی گروه آبیدر -->
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="abidar-brand" rel="home">
						<div class="abidar-brand__emblem">
							<svg width="36" height="36" viewBox="0 0 40 40" fill="none">
								<polygon points="20,3 37,33 3,33" stroke="#D97706" stroke-width="2.8" fill="rgba(217,119,6,0.12)"/>
								<polygon points="20,12 30,30 10,30" fill="#D97706"/>
							</svg>
						</div>
						<div class="abidar-brand__text">
							<span class="abidar-brand__title"><?php echo esc_html( abidar_t( 'holding_name' ) ); ?></span>
							<span class="abidar-brand__subtitle"><?php echo esc_html( abidar_t( 'holding_tagline' ) ); ?></span>
						</div>
					</a>

					<!-- فهرست اصلی صفحات دمو (ساخته شده از برگه‌های سایت) -->
					<nav class="abidar-main-nav" aria-label="<?php esc_attr_e( 'فهرست اصلی هلدینگ آبیدر', 'abidar-group' ); ?>">
						<ul class="abidar-main-nav__list">
							<li>
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo ( 'home' === $curr_page || empty( $curr_page ) ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_home' ) ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( abidar_page_url( 'about-us' ) ); ?>" class="<?php echo ( 'about-us' === $curr_page ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_about' ) ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>" class="<?php echo ( 'services' === $curr_page ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_services' ) ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( abidar_page_url( 'projects' ) ); ?>" class="<?php echo ( 'projects' === $curr_page ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_projects' ) ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( abidar_page_url( 'our-team' ) ); ?>" class="<?php echo ( 'our-team' === $curr_page ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_team' ) ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( abidar_page_url( 'certificates' ) ); ?>" class="<?php echo ( 'certificates' === $curr_page ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_certificates' ) ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( abidar_page_url( 'blog' ) ); ?>" class="<?php echo ( 'blog' === $curr_page ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_blog' ) ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="<?php echo ( 'contact-us' === $curr_page ) ? 'is-active' : ''; ?>">
									<?php echo esc_html( abidar_t( 'nav_contact' ) ); ?>
								</a>
							</li>
						</ul>
					</nav>

					<!-- دکمه‌های عملیاتی هدر و بازکننده منوی موبایل -->
					<div class="abidar-navbar__actions">
						<?php if ( $show_cta ) : ?>
							<button type="button" class="abidar-btn abidar-btn--gold abidar-btn--sm" data-toggle-floating-hub>
								<span><?php echo esc_html( abidar_t( 'cta_contact_us' ) ); ?></span>
							</button>
						<?php endif; ?>

						<button type="button" class="abidar-mobile-toggle" data-open-mobile-nav aria-label="<?php esc_attr_e( 'باز کردن منوی موبایل', 'abidar-group' ); ?>">
							<span></span>
							<span></span>
							<span></span>
						</button>
					</div>
				</div>
			</div>
		</header>
		<?php
	}
}
