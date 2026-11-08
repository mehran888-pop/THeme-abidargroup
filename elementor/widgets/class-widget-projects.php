<?php
/**
 * Elementor Widget: Abidar Mega Projects Portfolio (v2.0.0)
 *
 * المان اختصاصی نمایش پروژه‌های هلدینگ آبیدر با ۴ چیدمان متفاوت، فیلتر زنده و نوار پیشرفت فیزیکی
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Projects
 */
class Abidar_Widget_Projects extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_projects';
	}

	public function get_title() {
		return esc_html__( 'پروژه‌های هلدینگ آبیدر (۴ چیدمان + فیلتر)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-portfolio-grid';
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
				'label' => esc_html__( 'چیدمان و فیلتر پروژه‌ها', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح نمایش پروژه‌ها (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: گرید ویترین پروژه‌ها با شناسنامه فنی و نوار پیشرفت', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: نمای دوطرفه اسپات‌لایت (Spotlight Split Viewer)', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: کارت‌های افقی خط زمانی و ظرفیت تولید', 'abidar-group' ),
					'layout_4' => esc_html__( 'طرح ۴: گالری کاشی‌کاری تمام‌تصویر صنعتی (Masonry Overlay)', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'show_progress_bar',
			array(
				'label'        => esc_html__( 'نمایش نوار درصد پیشرفت فیزیکی پروژه', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'استایل و رنگ‌بندی پروژه‌ها', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'progress_bar_color',
			array(
				'label'     => esc_html__( 'رنگ نوار پیشرفت پروژه', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#D97706',
				'selectors' => array(
					'{{WRAPPER}} .abidar-project-card__progress-fill' => 'background-color: {{VALUE}};',
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
		$layout        = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'layout_1';
		$show_progress = ! isset( $settings['show_progress_bar'] ) || 'yes' === $settings['show_progress_bar'];
		$projects      = Abidar_CPT::get_default_dataset( 'projects' );
		?>
		<section class="abidar-section abidar-section--dark abidar-projects-sec" data-widget-section="projects" data-active-layout="<?php echo esc_attr( $layout ); ?>">
			<div class="abidar-container">
				<div class="abidar-section__head">
					<div>
						<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'section_projects_sub' ) ); ?></span>
						<h2 class="abidar-section__title abidar-section__title--light"><?php echo esc_html( abidar_t( 'section_projects_title' ) ); ?></h2>
					</div>

					<div class="abidar-section__controls">
						<div class="abidar-filter-pills abidar-filter-pills--dark" data-filter-group="projects">
							<button type="button" class="abidar-filter-pill is-active" data-filter="all"><?php echo esc_html( abidar_t( 'sector_all' ) ); ?></button>
							<button type="button" class="abidar-filter-pill" data-filter="mining"><?php echo esc_html( abidar_t( 'sector_mining' ) ); ?></button>
							<button type="button" class="abidar-filter-pill" data-filter="energy"><?php echo esc_html( abidar_t( 'sector_energy' ) ); ?></button>
							<button type="button" class="abidar-filter-pill" data-filter="oil"><?php echo esc_html( abidar_t( 'sector_oil' ) ); ?></button>
						</div>

						<div class="abidar-layout-switcher abidar-layout-switcher--dark">
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_1' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_1">طرح ۱: گرید فنی</button>
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_2' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_2">طرح ۲: اسپات‌لایت</button>
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_3' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_3">طرح ۳: افقی</button>
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_4' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_4">طرح ۴: گالری</button>
						</div>
					</div>
				</div>

				<div class="abidar-projects-grid abidar-widget-grid abidar-widget-grid--<?php echo esc_attr( $layout ); ?>">
					<?php foreach ( $projects as $prj ) : ?>
						<?php
						$title        = abidar_multilang_val( $prj, 'title', $prj['title_fa'] );
						$desc         = abidar_multilang_val( $prj, 'desc', $prj['desc_fa'] );
						$client       = abidar_multilang_val( $prj, 'client', $prj['client_fa'] );
						$location     = abidar_multilang_val( $prj, 'location', $prj['location_fa'] );
						$sector_label = abidar_multilang_val( $prj, 'sector_label', $prj['sector_label_fa'] );
						$progress     = (int) $prj['progress'];
						?>
						<article class="abidar-project-card" data-sector-item="<?php echo esc_attr( $prj['sector'] ); ?>">
							<div class="abidar-project-card__thumb" style="background-image: url('<?php echo esc_url( $prj['image'] ); ?>');">
								<span class="abidar-project-card__sector"><?php echo esc_html( $sector_label ); ?></span>
								<span class="abidar-project-card__year"><?php echo esc_html( $prj['year'] ); ?></span>
							</div>

							<div class="abidar-project-card__content">
								<h3 class="abidar-project-card__title"><?php echo esc_html( $title ); ?></h3>
								<p class="abidar-project-card__desc"><?php echo esc_html( $desc ); ?></p>

								<dl class="abidar-project-card__specs">
									<div>
										<dt><?php echo esc_html( abidar_t( 'project_client' ) ); ?></dt>
										<dd><?php echo esc_html( $client ); ?></dd>
									</div>
									<div>
										<dt><?php echo esc_html( abidar_t( 'project_location' ) ); ?></dt>
										<dd><?php echo esc_html( $location ); ?></dd>
									</div>
									<div>
										<dt><?php echo esc_html( abidar_t( 'project_capacity' ) ); ?></dt>
										<dd class="abidar-text-gold"><?php echo esc_html( $prj['capacity'] ); ?></dd>
									</div>
								</dl>

								<?php if ( $show_progress ) : ?>
									<div class="abidar-project-card__progress">
										<div class="abidar-project-card__progress-head">
											<span><?php echo esc_html( abidar_t( 'project_progress' ) ); ?></span>
											<strong><?php echo (int) $progress; ?>%</strong>
										</div>
										<div class="abidar-project-card__progress-track">
											<span class="abidar-project-card__progress-fill" style="width: <?php echo (int) $progress; ?>%;"></span>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
