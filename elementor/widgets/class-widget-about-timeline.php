<?php
/**
 * Elementor Widget: Abidar About, ESG Metrics & Holding Timeline (v2.0.0)
 *
 * المان اختصاصی معرفی هلدینگ، شاخص‌های پایداری (ESG) و خط زمانی تعاملی تاریخچه گروه آبیدر
 * با ۴ چیدمان مختلف و کنترل کامل استایل توسط المنتور رایگان
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_About_Timeline
 */
class Abidar_Widget_About_Timeline extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_about_timeline';
	}

	public function get_title() {
		return esc_html__( 'درباره ما، شاخص‌های ESG و خط زمانی آبیدر (۴ چیدمان)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return array( 'abidar-elements' );
	}

	/**
	 * داده‌های خط زمانی تاریخچه گروه آبیدر
	 *
	 * @return array
	 */
	public static function get_timeline_points() {
		return array(
			array(
				'year'      => '1388',
				'year_alt'  => '2009',
				'title_fa'  => 'تأسیس گروه آبیدر و آغاز فعالیت در اکتشاف معادن',
				'title_en'  => 'Abidar Group Founded — Mineral Exploration Kick-off',
				'title_zh'  => '阿比达尔集团成立，正式进军矿产勘探领域',
				'desc_fa'   => 'آغاز فعالیت با دو معدن سنگ‌آهن و نخستین تیم مهندسی ژئوفیزیک در استان کردستان.',
				'desc_en'   => 'Started operations with two iron ore mines and the first airborne geophysics engineering team in Kurdistan.',
				'desc_zh'   => '以两座铁矿及首支航空地球物理勘探团队起步于库尔德斯坦省。',
				'icon'      => '⛏️',
				'tag_fa'    => 'تأسیس هلدینگ',
				'tag_en'    => 'Group Foundation',
				'tag_zh'    => '集团创立',
			),
			array(
				'year'      => '1393',
				'year_alt'  => '2014',
				'title_fa'  => 'بهره‌برداری از نخستین کارخانه کنسانتره و گندله‌سازی',
				'title_en'  => 'First Concentrate & Pelletizing Plant Commissioned',
				'title_zh'  => '首座精矿与球团加工厂投产',
				'desc_fa'   => 'ظرفیت ۲.۵ میلیون تن سالانه با اشتغال مستقیم ۹۰۰ نفر از مهندسان و تکنسین‌های بومی.',
				'desc_en'   => '2.5 MTPA capacity commissioned, creating 900 direct local engineering and technician jobs.',
				'desc_zh'   => '实现年产 250 万吨产能，直接提供 900 个本地工程师岗位。',
				'icon'      => '🏭',
				'tag_fa'    => 'توسعه معدنی',
				'tag_en'    => 'Mining Expansion',
				'tag_zh'    => '矿业扩张',
			),
			array(
				'year'      => '1397',
				'year_alt'  => '2018',
				'title_fa'  => 'ورود راهبردی به حوزه انرژی و نیروگاه‌های سیکل ترکیبی',
				'title_en'  => 'Strategic Entry into Energy & Combined-Cycle Power',
				'title_zh'  => '战略性进入能源与联合循环发电领域',
				'desc_fa'   => 'احداث دو واحد گازی کلاس F و شروع توسعه پست‌های انتقال ۴۰۰ کیلوولت.',
				'desc_en'   => 'Construction of two F-class gas units and launch of 400kV high-voltage substation development.',
				'desc_zh'   => '开工建设两台F级燃气机组，同步推进400kV变电站建设。',
				'icon'      => '⚡',
				'tag_fa'    => 'ورود به انرژی',
				'tag_en'    => 'Energy Division',
				'tag_zh'    => '能源业务',
			),
			array(
				'year'      => '1400',
				'year_alt'  => '2021',
				'title_fa'  => 'احداث پالایشگاه میعانات گازی و توسعه زنجیره پتروشیمی',
				'title_en'  => 'Condensate Refinery & Petrochemical Value-Chain Expansion',
				'title_zh'  => '凝析油炼化厂建成及石化产业链延伸',
				'desc_fa'   => 'بهره‌برداری از واحدهای تقطیر و ایجاد پایانه صادراتی اختصاصی در سواحل مکران.',
				'desc_en'   => 'Distillation units commissioned with a dedicated export terminal along the Makran coastline.',
				'desc_zh'   => '蒸馏装置正式投产，并在莫克兰海岸建成专用出口码头。',
				'icon'      => '🛢️',
				'tag_fa'    => 'توسعه نفت و گاز',
				'tag_en'    => 'Oil & Gas',
				'tag_zh'    => '油气业务',
			),
			array(
				'year'      => '1403',
				'year_alt'  => '2024',
				'title_fa'  => 'هوشمندسازی معادن و افتتاح دفاتر منطقه‌ای دبی و شانگهای',
				'title_en'  => 'Smart Mining Rollout & Dubai / Shanghai Regional Offices',
				'title_zh'  => '智慧矿山上线，迪拜与上海区域办事处设立',
				'desc_fa'   => 'استقرار شبکه ۵G صنعتی، ناوگان خودران معدنی و آغاز صادرات به ۱۸ کشور.',
				'desc_en'   => 'Private industrial 5G, autonomous mining fleet, and export growth across 18 countries.',
				'desc_zh'   => '部署工业5G专网与自动驾驶矿卡，产品出口扩展至18个国家。',
				'icon'      => '🌐',
				'tag_fa'    => 'گسترش جهانی',
				'tag_en'    => 'Global Reach',
				'tag_zh'    => '全球布局',
			),
			array(
				'year'      => '1405',
				'year_alt'  => '2026',
				'title_fa'  => 'افق ۱۴۱۰: هیدروژن سبز، ذخیره‌سازها و صفر خالص انتشار',
				'title_en'  => 'Vision 2031: Green Hydrogen, Grid Storage & Net-Zero',
				'title_zh'  => '2031 愿景：绿氢、储能与净零排放',
				'desc_fa'   => 'سرمایه‌گذاری ۴.۲ میلیارد دلاری در انرژی پاک، هوش مصنوعی صنعتی و انطباق کامل با اهداف ESG.',
				'desc_en'   => '$4.2B committed to clean energy, industrial AI, and full compliance with UN SDG & ESG targets.',
				'desc_zh'   => '投资 42 亿美元发展清洁能源、工业人工智能及全面ESG合规体系。',
				'icon'      => '🚀',
				'tag_fa'    => 'افق آینده',
				'tag_en'    => 'Future Horizon',
				'tag_zh'    => '未来愿景',
			),
		);
	}

	protected function register_controls() {
		if ( ! class_exists( '\Elementor\Controls_Manager' ) ) {
			return;
		}

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'چیدمان معرفی هلدینگ و خط زمانی', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح نمایش (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: درباره ما + خط زمانی عمودی طلایی + شاخص‌های ESG', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: خط زمانی افقی تعاملی با اسلایدر', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: دو ستونه تصویر صنعتی + گام‌های تاریخچه', 'abidar-group' ),
					'layout_4' => esc_html__( 'طرح ۴: مینیمال مدیریتی با آمار بزرگ درخشان', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'custom_heading',
			array(
				'label'       => esc_html__( 'عنوان سفارشی بخش (اختیاری)', 'abidar-group' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'در صورت خالی بودن عنوان به صورت خودکار چندزبانه نمایش داده می‌شود', 'abidar-group' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'استایل شاخص‌ها و خط زمانی', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'timeline_accent',
			array(
				'label'     => esc_html__( 'رنگ خط زمانی و اعداد سال', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#D97706',
				'selectors' => array(
					'{{WRAPPER}} .abidar-about-sec' => '--abidar-primary: {{VALUE}};',
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
		$layout  = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'layout_1';
		$heading = ! empty( $settings['custom_heading'] ) ? $settings['custom_heading'] : abidar_t( 'about_heading' );
		$points  = self::get_timeline_points();
		$uri     = defined( 'ABIDAR_URI' ) ? ABIDAR_URI : '';
		?>
		<section class="abidar-section abidar-about-sec" id="about-section" data-widget-section="about" data-active-layout="<?php echo esc_attr( $layout ); ?>">
			<div class="abidar-container">

				<!-- بخش معرفی + شاخص‌های ESG -->
				<div class="abidar-about__intro">
					<div class="abidar-about__text">
						<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'about_eyebrow' ) ); ?></span>
						<h2 class="abidar-section__title"><?php echo esc_html( $heading ); ?></h2>
						<p class="abidar-about__lead">
							<?php echo esc_html( abidar_t( 'about_lead' ) ); ?>
						</p>
						<p class="abidar-about__body">
							<?php echo esc_html( abidar_t( 'about_body' ) ); ?>
						</p>

						<ul class="abidar-about__checklist">
							<li>✔ <?php echo esc_html( abidar_t( 'about_check_1' ) ); ?></li>
							<li>✔ <?php echo esc_html( abidar_t( 'about_check_2' ) ); ?></li>
							<li>✔ <?php echo esc_html( abidar_t( 'about_check_3' ) ); ?></li>
						</ul>

						<a href="<?php echo esc_url( abidar_page_url( 'contact-us' ) ); ?>" class="abidar-btn abidar-btn--dark">
							<?php echo esc_html( abidar_t( 'about_cta' ) ); ?> →
						</a>
					</div>

					<div class="abidar-about__card-visual" style="background-image: url('<?php echo esc_url( $uri . '/assets/images/sector-mining.jpg' ); ?>');">
						<div class="abidar-about__card-overlay"></div>
						<div class="abidar-about__esg">
							<h4>ESG PERFORMANCE 2026</h4>
							<div class="abidar-esg-row">
								<span>♻️ <?php esc_html_e( 'بازیافت آب صنعتی', 'abidar-group' ); ?></span>
								<strong dir="ltr">92%</strong>
							</div>
							<div class="abidar-esg-row">
								<span>🌱 <?php esc_html_e( 'کاهش انتشار CO₂', 'abidar-group' ); ?></span>
								<strong dir="ltr">-38%</strong>
							</div>
							<div class="abidar-esg-row">
								<span>👷 <?php esc_html_e( 'ساعت آموزش ایمنی HSE', 'abidar-group' ); ?></span>
								<strong dir="ltr">184K</strong>
							</div>
							<div class="abidar-esg-row">
								<span>⚡ <?php esc_html_e( 'سهم انرژی تجدیدپذیر', 'abidar-group' ); ?></span>
								<strong dir="ltr">27%</strong>
							</div>
						</div>
					</div>
				</div>

				<!-- خط زمانی تعاملی تاریخچه هلدینگ -->
				<div class="abidar-timeline" data-timeline>
					<div class="abidar-timeline__head">
						<h3 class="abidar-timeline__title"><?php echo esc_html( abidar_t( 'timeline_title' ) ); ?></h3>
						<div class="abidar-layout-switcher">
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_1' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_1">طرح ۱: عمودی</button>
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_2' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_2">طرح ۲: افقی</button>
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_3' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_3">طرح ۳: دو ستونه</button>
							<button type="button" class="abidar-layout-btn <?php echo ( 'layout_4' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_4">طرح ۴: مدیریتی</button>
						</div>
					</div>

					<div class="abidar-timeline__track">
						<?php foreach ( $points as $i => $point ) : ?>
							<?php
							$p_title = abidar_multilang_val( $point, 'title', $point['title_fa'] );
							$p_desc  = abidar_multilang_val( $point, 'desc', $point['desc_fa'] );
							$p_tag   = abidar_multilang_val( $point, 'tag', $point['tag_fa'] );
							?>
							<article class="abidar-timeline__item <?php echo ( 0 === $i ) ? 'is-current' : ''; ?>" data-step="<?php echo (int) $i; ?>">
								<div class="abidar-timeline__marker">
									<span class="abidar-timeline__icon"><?php echo esc_html( $point['icon'] ); ?></span>
									<span class="abidar-timeline__line"></span>
								</div>
								<div class="abidar-timeline__card">
									<div class="abidar-timeline__year">
										<strong><?php echo esc_html( $point['year'] ); ?></strong>
										<span dir="ltr"><?php echo esc_html( $point['year_alt'] ); ?></span>
									</div>
									<span class="abidar-timeline__tag"><?php echo esc_html( $p_tag ); ?></span>
									<h4 class="abidar-timeline__card-title"><?php echo esc_html( $p_title ); ?></h4>
									<p class="abidar-timeline__card-desc"><?php echo esc_html( $p_desc ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
