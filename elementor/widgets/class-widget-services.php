<?php
/**
 * Elementor Widget: Abidar Holding Services Showcase (v2.0.0)
 *
 * المان اختصاصی نمایش خدمات هلدینگ آبیدر (معادن، انرژی، نفت) با ۴ چیدمان متفاوت و کنترل کامل المنتور
 * چیدمان‌ها:
 * - layout_1: کارت‌های سه‌بعدی صنعتی (3D Industrial Cards)
 * - layout_2: تب‌های تعاملی تفکیک‌شده بر اساس حوزه (Interactive Sector Tabs)
 * - layout_3: لیست آکاردئونی شماره‌دار مدیریتی (Executive Numbered Accordion)
 * - layout_4: گرید ماتریسی بنتو (Bento Matrix Layout)
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Services
 */
class Abidar_Widget_Services extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_services';
	}

	public function get_title() {
		return esc_html__( 'خدمات هلدینگ آبیدر (۴ چیدمان خلاقانه)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
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
				'label' => esc_html__( 'چیدمان و کوئری خدمات', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب چیدمان نمایش خدمات (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: کارت‌های سه‌بعدی صنعتی با شاخص ظرفیت (3D Industrial Cards)', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: تب‌های تعاملی معادن، انرژی و نفت (Interactive Sector Tabs)', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: لیست آکاردئونی شماره‌دار مدیریتی (Executive Accordion)', 'abidar-group' ),
					'layout_4' => esc_html__( 'طرح ۴: چیدمان ماتریسی بنتو (Bento Matrix Grid)', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'sector_filter',
			array(
				'label'   => esc_html__( 'فیلتر حوزه فعالیت', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'    => esc_html__( 'همه حوزه‌ها (معادن، انرژی، نفت)', 'abidar-group' ),
					'mining' => esc_html__( 'فقط معادن و صنایع معدنی', 'abidar-group' ),
					'energy' => esc_html__( 'فقط انرژی و نیروگاه‌ها', 'abidar-group' ),
					'oil'    => esc_html__( 'فقط نفت، گاز و پتروشیمی', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'show_layout_switcher',
			array(
				'label'        => esc_html__( 'نمایش نوار تغییر زنده چیدمان برای کاربر', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'استایل کارت‌های خدمات', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg_color',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه کارت‌ها', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .abidar-service-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_accent_color',
			array(
				'label'     => esc_html__( 'رنگ کادر و آیکون طلایی/تاکیدی', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#D97706',
				'selectors' => array(
					'{{WRAPPER}} .abidar-services-sec' => '--abidar-primary: {{VALUE}};',
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
	 * رندر خروجی خدمات با پشتیبانی از ۴ چیدمان متفاوت
	 *
	 * @param array $settings تنظیمات ویجت.
	 */
	public static function render_output( $settings = array() ) {
		$layout        = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'layout_1';
		$sector_filter = isset( $settings['sector_filter'] ) ? $settings['sector_filter'] : 'all';
		$show_switcher = ! isset( $settings['show_layout_switcher'] ) || 'yes' === $settings['show_layout_switcher'];
		$services      = function_exists( 'abidar_get_content_dataset' ) ? abidar_get_content_dataset( 'abidar_service' ) : array();

		// اگر هنوز هیچ خدمتی در پیشخوان ثبت نشده باشد، نمایش مجموعه دمو برای پیش‌نمایش.
		if ( empty( $services ) ) {
			$services = Abidar_CPT::get_default_dataset( 'services' );
		}
		$current_lang  = Abidar_I18n::get_current_lang();

		if ( 'all' !== $sector_filter ) {
			$services = array_filter(
				$services,
				function ( $item ) use ( $sector_filter ) {
					return $item['sector'] === $sector_filter;
				}
			);
		}
		?>
		<section class="abidar-section abidar-services-sec" data-widget-section="services" data-active-layout="<?php echo esc_attr( $layout ); ?>">
			<div class="abidar-container">
				<div class="abidar-section__head">
					<div>
						<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'section_services_sub' ) ); ?></span>
						<h2 class="abidar-section__title"><?php echo esc_html( abidar_t( 'section_services_title' ) ); ?></h2>
					</div>

					<div class="abidar-section__controls">
						<!-- فیلتر حوزه فعالیت -->
						<div class="abidar-filter-pills" data-filter-group="services">
							<button type="button" class="abidar-filter-pill is-active" data-filter="all"><?php echo esc_html( abidar_t( 'sector_all' ) ); ?></button>
							<button type="button" class="abidar-filter-pill" data-filter="mining"><?php echo esc_html( abidar_t( 'sector_mining' ) ); ?></button>
							<button type="button" class="abidar-filter-pill" data-filter="energy"><?php echo esc_html( abidar_t( 'sector_energy' ) ); ?></button>
							<button type="button" class="abidar-filter-pill" data-filter="oil"><?php echo esc_html( abidar_t( 'sector_oil' ) ); ?></button>
						</div>

						<!-- تغییر زنده چیدمان ویجت المنتور (۴ طرح) -->
						<?php if ( $show_switcher ) : ?>
							<div class="abidar-layout-switcher" title="<?php esc_attr_e( 'پیش‌نمایش طرح‌های مختلف این ویجت المنتور', 'abidar-group' ); ?>">
								<button type="button" class="abidar-layout-btn <?php echo ( 'layout_1' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_1">طرح ۱: کارت سه‌بعدی</button>
								<button type="button" class="abidar-layout-btn <?php echo ( 'layout_2' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_2">طرح ۲: تب تعاملی</button>
								<button type="button" class="abidar-layout-btn <?php echo ( 'layout_3' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_3">طرح ۳: آکاردئون</button>
								<button type="button" class="abidar-layout-btn <?php echo ( 'layout_4' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_4">طرح ۴: ماتریس بنتو</button>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="abidar-services-grid abidar-widget-grid abidar-widget-grid--<?php echo esc_attr( $layout ); ?>">
					<?php foreach ( array_values( $services ) as $idx => $srv ) : ?>
						<?php
						$title  = abidar_multilang_val( $srv, 'title', $srv['title_fa'] );
						$desc   = abidar_multilang_val( $srv, 'desc', $srv['desc_fa'] );
						$metric = abidar_multilang_val( $srv, 'metric', $srv['metric'] );
						$sector = $srv['sector'];
						$sector_label = ( 'mining' === $sector ) ? abidar_t( 'sector_mining' ) : ( ( 'energy' === $sector ) ? abidar_t( 'sector_energy' ) : abidar_t( 'sector_oil' ) );
						?>
						<article class="abidar-service-card abidar-service-card--<?php echo esc_attr( $sector ); ?>" data-sector-item="<?php echo esc_attr( $sector ); ?>">
							<div class="abidar-service-card__media" style="background-image: url('<?php echo esc_url( $srv['image'] ); ?>');">
								<span class="abidar-service-card__badge"><?php echo esc_html( $sector_label ); ?></span>
								<span class="abidar-service-card__index">0<?php echo (int) ( $idx + 1 ); ?></span>
							</div>

							<div class="abidar-service-card__body">
								<div class="abidar-service-card__metric">
									<span class="abidar-dot abidar-dot--gold"></span>
									<strong><?php echo esc_html( $metric ); ?></strong>
								</div>

								<h3 class="abidar-service-card__title">
									<?php if ( ! empty( $srv['permalink'] ) ) : ?>
										<a href="<?php echo esc_url( $srv['permalink'] ); ?>"><?php echo esc_html( $title ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $title ); ?>
									<?php endif; ?>
								</h3>
								<p class="abidar-service-card__desc"><?php echo esc_html( $desc ); ?></p>

								<?php if ( ! empty( $srv['features'] ) && 'fa' === $current_lang ) : ?>
									<ul class="abidar-service-card__features">
										<?php foreach ( $srv['features'] as $feat ) : ?>
											<li>✓ <?php echo esc_html( $feat ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>

								<div class="abidar-service-card__footer">
									<a href="<?php echo esc_url( ! empty( $srv['permalink'] ) ? $srv['permalink'] : add_query_arg( 'dept', $sector, abidar_page_url( 'contact-us' ) ) ); ?>" class="abidar-link-gold">
										<span><?php echo esc_html( abidar_t( 'view_details' ) ); ?></span>
										<span class="abidar-icon-arrow">→</span>
									</a>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
