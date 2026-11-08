<?php
/**
 * Elementor Widget: Abidar Industrial Hero & 3-Sector Showcase (v2.0.0)
 *
 * المان اختصاصی هیرو و معرفی سه حوزه استراتژیک هلدینگ آبیدر (معادن، انرژی، نفت) با ۳ طرح خلاقانه
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Hero_Slider
 */
class Abidar_Widget_Hero_Slider extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_hero_slider';
	}

	public function get_title() {
		return esc_html__( 'هیرو و ویترین ۳ حوزه آبیدر (۳ چیدمان)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-slider-full-screen';
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
				'label' => esc_html__( 'چیدمان و محتوای هیرو', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح هیرو (Hero Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: هیرو سینمایی دوقسمتی + ستون‌های معادن، انرژی و نفت', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: پنل‌های آکاردئونی تعاملی ۳ حوزه (Interactive Pillar Expander)', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: هیرو تمام‌صفحه مدیریتی با آمار زنده هلدینگ', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'custom_title',
			array(
				'label'       => esc_html__( 'عنوان سفارشی هیرو (اختیاری)', 'abidar-group' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'placeholder' => esc_html__( 'در صورت خالی بودن، به صورت خودکار بر اساس زبان سایت ترجمه می‌شود', 'abidar-group' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'استایل و رنگ‌بندی هیرو', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hero_accent_color',
			array(
				'label'     => esc_html__( 'رنگ طلایی/تاکیدی هیرو', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#D97706',
				'selectors' => array(
					'{{WRAPPER}} .abidar-hero' => '--abidar-primary: {{VALUE}};',
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
		$layout    = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'layout_1';
		$title     = ! empty( $settings['custom_title'] ) ? $settings['custom_title'] : abidar_t( 'hero_title' );
		$subtitle  = abidar_t( 'hero_subtitle' );
		$theme_uri = defined( 'ABIDAR_URI' ) ? ABIDAR_URI : '';
		?>
		<section class="abidar-hero abidar-hero--<?php echo esc_attr( $layout ); ?>">
			<div class="abidar-hero__bg-mesh"></div>
			<div class="abidar-container abidar-hero__container">
				<div class="abidar-hero__grid">
					<!-- ستون محتوا و شاخص‌های کلیدی هلدینگ آبیدر -->
					<div class="abidar-hero__content">
						<div class="abidar-hero__kicker">
							<span class="abidar-hero__kicker-dot"></span>
							<span><?php echo esc_html( abidar_t( 'holding_badge' ) ); ?></span>
						</div>

						<h1 class="abidar-hero__title">
							<?php echo esc_html( $title ); ?>
						</h1>

						<p class="abidar-hero__subtitle">
							<?php echo esc_html( $subtitle ); ?>
						</p>

						<div class="abidar-hero__actions">
							<a href="<?php echo esc_url( abidar_page_url( 'projects' ) ); ?>" class="abidar-btn abidar-btn--gold abidar-btn--lg">
								<span><?php echo esc_html( abidar_t( 'cta_explore_projects' ) ); ?></span>
								<span class="abidar-icon-arrow">→</span>
							</a>
							<a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>" class="abidar-btn abidar-btn--glass abidar-btn--lg">
								<span><?php echo esc_html( abidar_t( 'cta_our_services' ) ); ?></span>
							</a>
						</div>

						<!-- آمار کلیدی هلدینگ -->
						<div class="abidar-hero__metrics">
							<div class="abidar-metric-box">
								<strong class="abidar-metric-box__num"><span data-count="48" data-count-prefix="+" dir="ltr">+48</span></strong>
								<span class="abidar-metric-box__label"><?php echo esc_html( abidar_t( 'stats_projects' ) ); ?></span>
							</div>
							<div class="abidar-metric-box">
								<strong class="abidar-metric-box__num"><span data-count="4200" dir="ltr">4,200</span></strong>
								<span class="abidar-metric-box__label"><?php echo esc_html( abidar_t( 'stats_capacity' ) ); ?></span>
							</div>
							<div class="abidar-metric-box">
								<strong class="abidar-metric-box__num"><span data-count="18.5" data-count-suffix="M" data-count-decimals="1" dir="ltr">18.5M</span></strong>
								<span class="abidar-metric-box__label"><?php echo esc_html( abidar_t( 'stats_mineral' ) ); ?></span>
							</div>
							<div class="abidar-metric-box">
								<strong class="abidar-metric-box__num"><span data-count="3400" data-count-prefix="+" dir="ltr">+3,400</span></strong>
								<span class="abidar-metric-box__label"><?php echo esc_html( abidar_t( 'stats_specialists' ) ); ?></span>
							</div>
						</div>
					</div>

					<!-- ستون تعاملی ۳ حوزه استراتژیک: معادن، انرژی، نفت -->
					<div class="abidar-hero__pillars">
						<article class="abidar-pillar-card is-expanded" data-sector-pillar="mining" style="background-image: url('<?php echo esc_url( $theme_uri . '/assets/images/sector-mining.jpg' ); ?>');">
							<div class="abidar-pillar-card__overlay"></div>
							<div class="abidar-pillar-card__content">
								<span class="abidar-pillar-card__num">01 / MINING</span>
								<h3 class="abidar-pillar-card__title"><?php echo esc_html( abidar_t( 'sector_mining' ) ); ?></h3>
								<p class="abidar-pillar-card__desc">اکتشاف، استخراج هوشمند و کارخانجات کنسانتره و گندله‌سازی مس، آهن و طلا</p>
								<a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>" class="abidar-pillar-card__link">
									<?php echo esc_html( abidar_t( 'view_details' ) ); ?> ↗
								</a>
							</div>
						</article>

						<article class="abidar-pillar-card" data-sector-pillar="energy" style="background-image: url('<?php echo esc_url( $theme_uri . '/assets/images/sector-energy.jpg' ); ?>');">
							<div class="abidar-pillar-card__overlay"></div>
							<div class="abidar-pillar-card__content">
								<span class="abidar-pillar-card__num">02 / ENERGY</span>
								<h3 class="abidar-pillar-card__title"><?php echo esc_html( abidar_t( 'sector_energy' ) ); ?></h3>
								<p class="abidar-pillar-card__desc">نیروگاه‌های سیکل ترکیبی کلاس F، مزارع خورشیدی مگاواتی و ذخیره‌سازهای شبکه</p>
								<a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>" class="abidar-pillar-card__link">
									<?php echo esc_html( abidar_t( 'view_details' ) ); ?> ↗
								</a>
							</div>
						</article>

						<article class="abidar-pillar-card" data-sector-pillar="oil" style="background-image: url('<?php echo esc_url( $theme_uri . '/assets/images/sector-oil.jpg' ); ?>');">
							<div class="abidar-pillar-card__overlay"></div>
							<div class="abidar-pillar-card__content">
								<span class="abidar-pillar-card__num">03 / OIL &amp; GAS</span>
								<h3 class="abidar-pillar-card__title"><?php echo esc_html( abidar_t( 'sector_oil' ) ); ?></h3>
								<p class="abidar-pillar-card__desc">پالایشگاه‌های میعانات گازی، مجتمع‌های الفین و پتروشیمی و پایانه‌های صادراتی</p>
								<a href="<?php echo esc_url( abidar_page_url( 'services' ) ); ?>" class="abidar-pillar-card__link">
									<?php echo esc_html( abidar_t( 'view_details' ) ); ?> ↗
								</a>
							</div>
						</article>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
