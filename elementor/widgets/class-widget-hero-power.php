<?php
/**
 * Elementor Free Widget: Abidar Power Hero (v3.0.0)
 *
 * هیرو «قدرت» گروه آبیدر — ۸ چیدمان مستقل، ۶ نوع پس‌زمینه از جمله ویدیو،
 * جلوه‌های بصری متحرک و کنترل کامل استایل در المنتور رایگان.
 *
 * @package Abidar_Group
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Hero_Power
 */
class Abidar_Widget_Hero_Power extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_hero_power';
	}

	public function get_title() {
		return esc_html__( 'هیرو قدرت آبیدر (۸ طرح + پس‌زمینه ویدیویی)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return array( 'abidar-elements' );
	}

	public function get_keywords() {
		return array( 'hero', 'هیرو', 'banner', 'video', 'slider', 'آبیدر' );
	}

	/**
	 * چیدمان‌های موجود. برای افزودن طرح جدید فقط یک کلید اینجا و یک بلوک
	 * رندر پایین‌تر لازم است.
	 *
	 * @return array
	 */
	public static function get_layouts() {
		return array(
			'split_cinematic' => esc_html__( 'طرح ۱: سینمایی دو ستونه + ستون‌های سه‌گانه صنعت', 'abidar-group' ),
			'fullscreen_center' => esc_html__( 'طرح ۲: تمام‌صفحه وسط‌چین با خطوط نورانی', 'abidar-group' ),
			'mosaic'          => esc_html__( 'طرح ۳: موزاییک بنتو (Bento Grid) صنعتی', 'abidar-group' ),
			'asymmetric'      => esc_html__( 'طرح ۴: نامتقارن با تصویر شیب‌دار', 'abidar-group' ),
			'stacked_type'    => esc_html__( 'طرح ۵: تایپوگرافی غول‌پیکر روی پس‌زمینه', 'abidar-group' ),
			'slider'          => esc_html__( 'طرح ۶: اسلایدر چند اسلاید', 'abidar-group' ),
			'glass_card'      => esc_html__( 'طرح ۷: کارت شیشه‌ای شناور', 'abidar-group' ),
			'minimal_wide'    => esc_html__( 'طرح ۸: مینیمال عریض با نوار آمار', 'abidar-group' ),
		);
	}

	/**
	 * نوع پس‌زمینه‌ها.
	 *
	 * @return array
	 */
	public static function get_backgrounds() {
		return array(
			'none'   => esc_html__( 'بدون پس‌زمینه خاص (رنگ تیره قالب)', 'abidar-group' ),
			'mesh'   => esc_html__( 'مش گرادیانت متحرک', 'abidar-group' ),
			'grid'   => esc_html__( 'شبکه فنی مهندسی', 'abidar-group' ),
			'image'  => esc_html__( 'تصویر ثابت', 'abidar-group' ),
			'video'  => esc_html__( 'ویدیو (MP4 خودمیزبان)', 'abidar-group' ),
			'youtube' => esc_html__( 'ویدیو یوتیوب', 'abidar-group' ),
			'particles' => esc_html__( 'ذرات شناور نورانی', 'abidar-group' ),
		);
	}

	protected function register_controls() {
		if ( ! class_exists( '\Elementor\Controls_Manager' ) ) {
			return;
		}

		/* ---------------- چیدمان ---------------- */
		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'چیدمان و طرح هیرو', 'abidar-group' ) )
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'split_cinematic',
				'options' => self::get_layouts(),
			)
		);

		$this->add_control(
			'hero_height',
			array(
				'label'   => esc_html__( 'ارتفاع هیرو', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'tall',
				'options' => array(
					'auto'   => esc_html__( 'خودکار', 'abidar-group' ),
					'medium' => esc_html__( 'متوسط (۷۰vh)', 'abidar-group' ),
					'tall'   => esc_html__( 'بلند (۹۰vh)', 'abidar-group' ),
					'full'   => esc_html__( 'تمام‌صفحه (۱۰۰vh)', 'abidar-group' ),
				),
			)
		);

		$this->end_controls_section();

		/* ---------------- محتوا ---------------- */
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'محتوا و متن‌ها', 'abidar-group' ) )
		);

		$this->add_control(
			'kicker',
			array(
				'label'   => esc_html__( 'برچسب بالای عنوان (Kicker)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'در صورت خالی بودن، از تنظیمات زبان قالب خوانده می‌شود', 'abidar-group' ),
			)
		);

		$this->add_control(
			'custom_title',
			array(
				'label'       => esc_html__( 'عنوان اصلی', 'abidar-group' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'placeholder' => esc_html__( 'خالی بگذارید تا از دیکشنری زبان سایت پر شود', 'abidar-group' ),
			)
		);

		$this->add_control(
			'custom_subtitle',
			array(
				'label'   => esc_html__( 'توضیح زیر عنوان', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '',
			)
		);

		$this->add_control(
			'show_metrics',
			array(
				'label'        => esc_html__( 'نمایش آمار کلیدی', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_pillars',
			array(
				'label'        => esc_html__( 'نمایش کارت‌های سه حوزه (معادن، انرژی، نفت)', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_scroll_hint',
			array(
				'label'        => esc_html__( 'نمایش نشانگر اسکرول پایین هیرو', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->add_control(
			'slide1_title',
			array(
				'label'       => sprintf( esc_html__( 'اسلاید 1 — عنوان', 'abidar-group' ), 1 ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide1_subtitle',
			array(
				'label'     => sprintf( esc_html__( 'اسلاید 1 — زیرعنوان', 'abidar-group' ), 1 ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => '',
				'rows'      => 2,
				'condition' => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide1_image',
			array(
				'label'     => sprintf( esc_html__( 'اسلاید 1 — تصویر پس‌زمینه', 'abidar-group' ), 1 ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide2_title',
			array(
				'label'       => sprintf( esc_html__( 'اسلاید 2 — عنوان', 'abidar-group' ), 2 ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide2_subtitle',
			array(
				'label'     => sprintf( esc_html__( 'اسلاید 2 — زیرعنوان', 'abidar-group' ), 2 ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => '',
				'rows'      => 2,
				'condition' => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide2_image',
			array(
				'label'     => sprintf( esc_html__( 'اسلاید 2 — تصویر پس‌زمینه', 'abidar-group' ), 2 ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide3_title',
			array(
				'label'       => sprintf( esc_html__( 'اسلاید 3 — عنوان', 'abidar-group' ), 3 ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide3_subtitle',
			array(
				'label'     => sprintf( esc_html__( 'اسلاید 3 — زیرعنوان', 'abidar-group' ), 3 ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => '',
				'rows'      => 2,
				'condition' => array( 'layout_style' => 'slider' ),
			)
		);

		$this->add_control(
			'slide3_image',
			array(
				'label'     => sprintf( esc_html__( 'اسلاید 3 — تصویر پس‌زمینه', 'abidar-group' ), 3 ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array( 'layout_style' => 'slider' ),
			)
		);

		/* ---------------- دکمه‌ها ---------------- */
		$this->start_controls_section(
			'section_buttons',
			array( 'label' => esc_html__( 'دکمه‌های اقدام', 'abidar-group' ) )
		);

		$this->add_control(
			'primary_text',
			array(
				'label'   => esc_html__( 'متن دکمه اصلی', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'primary_link',
			array(
				'label'   => esc_html__( 'لینک دکمه اصلی', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array( 'url' => '' ),
			)
		);

		$this->add_control(
			'secondary_text',
			array(
				'label'   => esc_html__( 'متن دکمه دوم', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'secondary_link',
			array(
				'label'   => esc_html__( 'لینک دکمه دوم', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array( 'url' => '' ),
			)
		);

		$this->end_controls_section();

		/* ---------------- پس‌زمینه ---------------- */
		$this->start_controls_section(
			'section_background',
			array( 'label' => esc_html__( 'پس‌زمینه', 'abidar-group' ) )
		);

		$this->add_control(
			'background_type',
			array(
				'label'   => esc_html__( 'نوع پس‌زمینه', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'mesh',
				'options' => self::get_backgrounds(),
			)
		);

		$this->add_control(
			'bg_image',
			array(
				'label'     => esc_html__( 'تصویر پس‌زمینه', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array( 'background_type' => 'image' ),
			)
		);

		$this->add_control(
			'bg_video',
			array(
				'label'       => esc_html__( 'آدرس ویدیو (MP4)', 'abidar-group' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://example.com/hero.mp4',
				'condition'   => array( 'background_type' => 'video' ),
			)
		);

		$this->add_control(
			'bg_video_poster',
			array(
				'label'     => esc_html__( 'تصویر پیش‌نمایش ویدیو (Poster)', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array( 'background_type' => 'video' ),
			)
		);

		$this->add_control(
			'bg_youtube',
			array(
				'label'       => esc_html__( 'شناسه یا آدرس ویدیو یوتیوب', 'abidar-group' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'dQw4w9WgXcQ',
				'condition'   => array( 'background_type' => 'youtube' ),
			)
		);

		$this->add_control(
			'bg_overlay',
			array(
				'label'   => esc_html__( 'شدت پرده تیره روی پس‌زمینه', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'default' => array( 'size' => 60 ),
				'range'   => array(
					'%' => array( 'min' => 0, 'max' => 95 ),
				),
			)
		);

		$this->add_control(
			'bg_overlay_color',
			array(
				'label'   => esc_html__( 'رنگ پرده پس‌زمینه', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#05101f',
			)
		);

		$this->end_controls_section();

		/* ---------------- استایل ---------------- */
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'رنگ و تایپوگرافی', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ تاکیدی', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#D97706',
				'selectors' => array(
					'{{WRAPPER}} .abidar-hp' => '--hp-accent: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_2_color',
			array(
				'label'     => esc_html__( 'رنگ تاکیدی دوم (گرادیانت)', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#0EA5E9',
				'selectors' => array(
					'{{WRAPPER}} .abidar-hp' => '--hp-accent-2: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#f8fafc',
				'selectors' => array(
					'{{WRAPPER}} .abidar-hp__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .abidar-hp__type'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ توضیحات', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(226,232,240,0.82)',
				'selectors' => array(
					'{{WRAPPER}} .abidar-hp__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_size',
			array(
				'label'      => esc_html__( 'اندازه عنوان', 'abidar-group' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 24, 'max' => 130, 'step' => 1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .abidar-hp__title' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_align',
			array(
				'label'   => esc_html__( 'چینش محتوا', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'start',
				'options' => array(
					'start'  => esc_html__( 'ابتدای خط (RTL: راست)', 'abidar-group' ),
					'center' => esc_html__( 'وسط', 'abidar-group' ),
					'end'    => esc_html__( 'انتهای خط (RTL: چپ)', 'abidar-group' ),
				),
				'selectors_dictionary' => array(
					'start'  => 'start',
					'center' => 'center',
					'end'    => 'end',
				),
				'selectors' => array(
					'{{WRAPPER}} .abidar-hp__content' => 'text-align: {{VALUE}}; align-items: {{VALUE}};',
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
	 * استخراج شناسه یوتیوب از هر شکل آدرسی.
	 *
	 * @param string $input آدرس یا شناسه.
	 * @return string
	 */
	private static function youtube_id( $input ) {
		$input = trim( (string) $input );
		if ( '' === $input ) {
			return '';
		}

		if ( preg_match( '~(?:youtu\.be/|v=|embed/)([A-Za-z0-9_-]{6,})~', $input, $m ) ) {
			return $m[1];
		}

		if ( preg_match( '~^[A-Za-z0-9_-]{6,}$~', $input ) ) {
			return $input;
		}

		return '';
	}

	/**
	 * خروجی پس‌زمینه بر اساس نوع انتخاب‌شده.
	 *
	 * @param array $settings تنظیمات ویجت.
	 */
	private static function render_background( $settings ) {
		$type    = isset( $settings['background_type'] ) ? $settings['background_type'] : 'mesh';
		$overlay = isset( $settings['bg_overlay']['size'] ) ? (float) $settings['bg_overlay']['size'] : 60;
		$color   = ! empty( $settings['bg_overlay_color'] ) ? $settings['bg_overlay_color'] : '#05101f';

		// پرده رنگی: همیشه با متغیر CSS تا قابل تغییر باشد.
		$overlay_style = sprintf(
			'--hp-overlay: %.0f%%; --hp-overlay-color: %s;',
			max( 0, min( 95, $overlay ) ),
			$color
		);

		switch ( $type ) {
			case 'image':
				$url = ! empty( $settings['bg_image']['url'] ) ? $settings['bg_image']['url'] : '';
				if ( $url ) {
					printf(
						'<div class="abidar-hp__bg abidar-hp__bg--image" style="%s background-image:url(%s);"></div>',
						esc_attr( $overlay_style ),
						esc_url( $url )
					);
				}
				break;

			case 'video':
				$url    = ! empty( $settings['bg_video']['url'] ) ? $settings['bg_video']['url'] : '';
				$poster = ! empty( $settings['bg_video_poster']['url'] ) ? $settings['bg_video_poster']['url'] : '';
				if ( $url ) {
					printf( '<div class="abidar-hp__bg abidar-hp__bg--video" style="%s">', esc_attr( $overlay_style ) );
					printf(
						'<video autoplay muted loop playsinline preload="metadata" %s><source src="%s" type="video/mp4" /></video>',
						$poster ? 'poster="' . esc_url( $poster ) . '"' : '',
						esc_url( $url )
					);
					echo '</div>';
				}
				break;

			case 'youtube':
				$id = self::youtube_id( isset( $settings['bg_youtube'] ) ? $settings['bg_youtube'] : '' );
				if ( $id ) {
					printf( '<div class="abidar-hp__bg abidar-hp__bg--yt" style="%s">', esc_attr( $overlay_style ) );
					printf(
						'<iframe src="https://www.youtube-nocookie.com/embed/%s?autoplay=1&mute=1&loop=1&playlist=%s&controls=0&showinfo=0&rel=0&playsinline=1&modestbranding=1"
							title="%s" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen tabindex="-1" aria-hidden="true"></iframe>',
						esc_attr( $id ),
						esc_attr( $id ),
						esc_attr__( 'ویدیو پس‌زمینه هیرو', 'abidar-group' )
					);
					echo '</div>';
				}
				break;

			case 'mesh':
				printf( '<div class="abidar-hp__bg abidar-hp__bg--mesh" style="%s" aria-hidden="true"></div>', esc_attr( $overlay_style ) );
				break;

			case 'grid':
				printf( '<div class="abidar-hp__bg abidar-hp__bg--grid" style="%s" aria-hidden="true"></div>', esc_attr( $overlay_style ) );
				break;

			case 'particles':
				printf( '<div class="abidar-hp__bg abidar-hp__bg--particles" style="%s" aria-hidden="true">', esc_attr( $overlay_style ) );
				// ذرات با تاخیر تصادفی و اندازه متفاوت ساخته می‌شوند.
				$seed = array( 12, 28, 41, 55, 63, 77, 84, 91, 18, 33, 47, 58, 69, 73, 88, 96 );
				foreach ( $seed as $i => $pos ) {
					printf(
						'<span class="abidar-hp__particle" style="--x:%d%%; --d:%ss; --s:%dpx; --delay:%ss;"></span>',
						(int) $pos,
						12 + ( $i % 7 ) * 2,
						3 + ( $i % 4 ),
						( $i % 9 ) * 0.7
					);
				}
				echo '</div>';
				break;

			case 'none':
			default:
				break;
		}
	}

	/**
	 * آمار کلیدی هیرو.
	 *
	 * @param array $settings تنظیمات.
	 */
	private static function render_metrics( $settings ) {
		if ( isset( $settings['show_metrics'] ) && 'yes' !== $settings['show_metrics'] ) {
			return;
		}

		$metrics = array(
			array( 'num' => 48, 'prefix' => '+', 'label' => abidar_t( 'stats_projects' ) ),
			array( 'num' => 4200, 'prefix' => '', 'label' => abidar_t( 'stats_capacity' ) ),
			array( 'num' => 18.5, 'prefix' => '', 'suffix' => 'M', 'decimals' => 1, 'label' => abidar_t( 'stats_mineral' ) ),
			array( 'num' => 3400, 'prefix' => '+', 'label' => abidar_t( 'stats_specialists' ) ),
		);

		echo '<div class="abidar-hp__metrics">';
		foreach ( $metrics as $m ) {
			$attrs = sprintf( 'data-count="%s"', esc_attr( $m['num'] ) );
			if ( ! empty( $m['prefix'] ) ) {
				$attrs .= sprintf( ' data-count-prefix="%s"', esc_attr( $m['prefix'] ) );
			}
			if ( ! empty( $m['suffix'] ) ) {
				$attrs .= sprintf( ' data-count-suffix="%s"', esc_attr( $m['suffix'] ) );
			}
			if ( ! empty( $m['decimals'] ) ) {
				$attrs .= sprintf( ' data-count-decimals="%s"', esc_attr( $m['decimals'] ) );
			}

			$display = ( ! empty( $m['prefix'] ) ? $m['prefix'] : '' ) . number_format( (float) $m['num'], isset( $m['decimals'] ) ? (int) $m['decimals'] : 0 ) . ( ! empty( $m['suffix'] ) ? $m['suffix'] : '' );

			printf(
				'<div class="abidar-hp__metric"><strong class="abidar-hp__metric-num"><span %s dir="ltr">%s</span></strong><span class="abidar-hp__metric-label">%s</span></div>',
				$attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html( $display ),
				esc_html( $m['label'] )
			);
		}
		echo '</div>';
	}

	/**
	 * سه کارت حوزه استراتژیک.
	 *
	 * @param array $settings تنظیمات.
	 */
	private static function render_pillars( $settings ) {
		if ( isset( $settings['show_pillars'] ) && 'yes' !== $settings['show_pillars'] ) {
			return;
		}

		$uri = defined( 'ABIDAR_URI' ) ? ABIDAR_URI : '';

		$pillars = array(
			array(
				'key'   => 'mining',
				'num'   => '01',
				'code'  => 'MINING',
				'title' => abidar_t( 'sector_mining' ),
				'desc'  => 'اکتشاف، استخراج هوشمند و کارخانجات کنسانتره و گندله‌سازی مس، آهن و طلا',
				'img'   => $uri . '/assets/images/sector-mining.jpg',
			),
			array(
				'key'   => 'energy',
				'num'   => '02',
				'code'  => 'ENERGY',
				'title' => abidar_t( 'sector_energy' ),
				'desc'  => 'نیروگاه‌های سیکل ترکیبی کلاس F، مزارع خورشیدی مگاواتی و ذخیره‌سازهای شبکه',
				'img'   => $uri . '/assets/images/sector-energy.jpg',
			),
			array(
				'key'   => 'oil',
				'num'   => '03',
				'code'  => 'OIL & GAS',
				'title' => abidar_t( 'sector_oil' ),
				'desc'  => 'پالایشگاه‌های میعانات گازی، مجتمع‌های الفین و پتروشیمی و پایانه‌های صادراتی',
				'img'   => $uri . '/assets/images/sector-oil.jpg',
			),
		);

		echo '<div class="abidar-hp__pillars">';
		foreach ( $pillars as $i => $p ) {
			printf(
				'<article class="abidar-hp__pillar %s" data-hp-pillar="%s" style="background-image:url(%s); --i:%d;">',
				0 === $i ? 'is-open' : '',
				esc_attr( $p['key'] ),
				esc_url( $p['img'] ),
				(int) $i
			);
			echo '<div class="abidar-hp__pillar-shade"></div>';
			echo '<div class="abidar-hp__pillar-body">';
			printf( '<span class="abidar-hp__pillar-num"><span dir="ltr">%s</span> / %s</span>', esc_html( $p['num'] ), esc_html( $p['code'] ) );
			printf( '<h3 class="abidar-hp__pillar-title">%s</h3>', esc_html( $p['title'] ) );
			printf( '<p class="abidar-hp__pillar-desc">%s</p>', esc_html( $p['desc'] ) );
			printf(
				'<a href="%s" class="abidar-hp__pillar-link">%s <span aria-hidden="true">↗</span></a>',
				esc_url( abidar_page_url( 'services' ) ),
				esc_html( abidar_t( 'view_details' ) )
			);
			echo '</div></article>';
		}
		echo '</div>';
	}

	/**
	 * دکمه‌های اقدام.
	 *
	 * @param array $settings تنظیمات.
	 */
	private static function render_actions( $settings ) {
		$primary_text   = ! empty( $settings['primary_text'] ) ? $settings['primary_text'] : abidar_t( 'cta_explore_projects' );
		$secondary_text = ! empty( $settings['secondary_text'] ) ? $settings['secondary_text'] : abidar_t( 'cta_our_services' );

		$primary_url   = ! empty( $settings['primary_link']['url'] ) ? $settings['primary_link']['url'] : abidar_page_url( 'projects' );
		$secondary_url = ! empty( $settings['secondary_link']['url'] ) ? $settings['secondary_link']['url'] : abidar_page_url( 'services' );

		echo '<div class="abidar-hp__actions">';
		printf(
			'<a href="%s" class="abidar-hp__btn abidar-hp__btn--primary"><span>%s</span><span class="abidar-hp__btn-arrow" aria-hidden="true">→</span></a>',
			esc_url( $primary_url ),
			esc_html( $primary_text )
		);
		printf(
			'<a href="%s" class="abidar-hp__btn abidar-hp__btn--ghost"><span>%s</span></a>',
			esc_url( $secondary_url ),
			esc_html( $secondary_text )
		);
		echo '</div>';
	}

	/**
	 * بلوک متنی مشترک بین چیدمان‌ها.
	 *
	 * @param array  $settings تنظیمات.
	 * @param string $extra_class کلاس اضافه.
	 */
	private static function render_text_block( $settings, $extra_class = '' ) {
		$kicker   = ! empty( $settings['kicker'] ) ? $settings['kicker'] : abidar_t( 'holding_badge' );
		$title    = ! empty( $settings['custom_title'] ) ? $settings['custom_title'] : abidar_t( 'hero_title' );
		$subtitle = ! empty( $settings['custom_subtitle'] ) ? $settings['custom_subtitle'] : abidar_t( 'hero_subtitle' );

		printf( '<div class="abidar-hp__content %s">', esc_attr( $extra_class ) );

		if ( $kicker ) {
			printf(
				'<div class="abidar-hp__kicker"><span class="abidar-hp__kicker-dot" aria-hidden="true"></span><span>%s</span></div>',
				esc_html( $kicker )
			);
		}

		printf( '<h1 class="abidar-hp__title">%s</h1>', esc_html( $title ) );

		if ( $subtitle ) {
			printf( '<p class="abidar-hp__subtitle">%s</p>', esc_html( $subtitle ) );
		}

		self::render_actions( $settings );
		echo '</div>';
	}

	/**
	 * نشانگر اسکرول.
	 *
	 * @param array $settings تنظیمات.
	 */
	private static function render_scroll_hint( $settings ) {
		if ( isset( $settings['show_scroll_hint'] ) && 'yes' !== $settings['show_scroll_hint'] ) {
			return;
		}

		echo '<div class="abidar-hp__scroll" aria-hidden="true"><span class="abidar-hp__scroll-track"><span class="abidar-hp__scroll-dot"></span></span></div>';
	}

	/**
	 * رندر خروجی هیرو. از فرانت‌اند، دمو ایمپورتر و هدر قالب قابل فراخوانی است.
	 *
	 * @param array $settings تنظیمات ویجت.
	 */
	public static function render_output( $settings = array() ) {
		$layout = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'split_cinematic';
		$height = isset( $settings['hero_height'] ) ? $settings['hero_height'] : 'tall';
		$align  = isset( $settings['content_align'] ) ? $settings['content_align'] : 'start';

		$classes = array(
			'abidar-hp',
			'abidar-hp--' . sanitize_html_class( $layout ),
			'abidar-hp--h-' . sanitize_html_class( $height ),
			'abidar-hp--align-' . sanitize_html_class( $align ),
		);

		printf(
			'<section class="%s" data-hp-layout="%s">',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( $layout )
		);

		self::render_background( $settings );

		echo '<div class="abidar-container abidar-hp__inner">';

		switch ( $layout ) {
			case 'fullscreen_center':
				echo '<div class="abidar-hp__center">';
				self::render_text_block( $settings, 'abidar-hp__content--center' );
				self::render_metrics( $settings );
				echo '</div>';
				self::render_pillars( $settings );
				break;

			case 'mosaic':
				echo '<div class="abidar-hp__mosaic">';
				echo '<div class="abidar-hp__mosaic-main">';
				self::render_text_block( $settings );
				echo '</div>';
				echo '<div class="abidar-hp__mosaic-side">';
				self::render_metrics( $settings );
				self::render_pillars( $settings );
				echo '</div>';
				echo '</div>';
				break;

			case 'asymmetric':
				echo '<div class="abidar-hp__asym">';
				echo '<div class="abidar-hp__asym-text">';
				self::render_text_block( $settings );
				echo '</div>';
				echo '<div class="abidar-hp__asym-art" aria-hidden="true">';
				echo '<span class="abidar-hp__asym-ring"></span>';
				echo '<span class="abidar-hp__asym-ring abidar-hp__asym-ring--2"></span>';
				echo '<span class="abidar-hp__asym-badge">' . esc_html( abidar_t( 'holding_short' ) ) . '</span>';
				echo '</div>';
				echo '</div>';
				self::render_metrics( $settings );
				break;

			case 'stacked_type':
				echo '<div class="abidar-hp__stacked">';
				$title = ! empty( $settings['custom_title'] ) ? $settings['custom_title'] : abidar_t( 'hero_title' );
				// عنوان غول‌پیکر با کلمات جدا برای انیمیشن پله‌ای.
				$words = preg_split( '/\s+/u', wp_strip_all_tags( $title ) );
				echo '<h1 class="abidar-hp__type" aria-label="' . esc_attr( $title ) . '">';
				foreach ( (array) $words as $i => $word ) {
					printf( '<span class="abidar-hp__type-word" style="--i:%d;">%s</span>', (int) $i, esc_html( $word ) );
				}
				echo '</h1>';
				self::render_text_block( $settings, 'abidar-hp__content--stacked' );
				echo '</div>';
				self::render_metrics( $settings );
				break;

			case 'slider':
				echo '<div class="abidar-hp__slider" data-hp-slider>';
				for ( $s = 1; $s <= 3; $s++ ) {
					$slide_settings             = $settings;
					$slide_settings['custom_title']    = ! empty( $settings[ 'slide' . $s . '_title' ] ) ? $settings[ 'slide' . $s . '_title' ] : $settings['custom_title'];
					$slide_settings['custom_subtitle'] = ! empty( $settings[ 'slide' . $s . '_subtitle' ] ) ? $settings[ 'slide' . $s . '_subtitle' ] : $settings['custom_subtitle'];

					$slide_img = ! empty( $settings[ 'slide' . $s . '_image' ]['url'] ) ? $settings[ 'slide' . $s . '_image' ]['url'] : '';

					printf(
						'<div class="abidar-hp__slide %s" data-hp-slide="%d"%s>',
						1 === $s ? 'is-active' : '',
						(int) $s,
						$slide_img ? ' style="background-image:url(' . esc_url( $slide_img ) . ')"' : ''
					);
					self::render_text_block( $slide_settings );
					echo '</div>';
				}
				echo '<div class="abidar-hp__slider-nav" role="tablist">';
				for ( $s = 1; $s <= 3; $s++ ) {
					printf(
						'<button type="button" class="abidar-hp__slider-dot %s" data-hp-slide-to="%d" role="tab" aria-label="%s" aria-selected="%s"></button>',
						1 === $s ? 'is-active' : '',
						(int) $s,
						esc_attr( sprintf( 'اسلاید %d', $s ) ),
						1 === $s ? 'true' : 'false'
					);
				}
				echo '</div>';
				echo '</div>';
				self::render_metrics( $settings );
				break;

			case 'glass_card':
				echo '<div class="abidar-hp__glass-wrap">';
				self::render_text_block( $settings, 'abidar-hp__content--glass' );
				echo '</div>';
				self::render_metrics( $settings );
				self::render_pillars( $settings );
				break;

			case 'minimal_wide':
				echo '<div class="abidar-hp__minimal">';
				self::render_text_block( $settings, 'abidar-hp__content--minimal' );
				echo '</div>';
				echo '<div class="abidar-hp__strip">';
				self::render_metrics( $settings );
				echo '</div>';
				break;

			case 'split_cinematic':
			default:
				echo '<div class="abidar-hp__split">';
				echo '<div class="abidar-hp__split-text">';
				self::render_text_block( $settings );
				self::render_metrics( $settings );
				echo '</div>';
				echo '<div class="abidar-hp__split-art">';
				self::render_pillars( $settings );
				echo '</div>';
				echo '</div>';
				break;
		}

		echo '</div>';
		self::render_scroll_hint( $settings );
		echo '</section>';
	}
}
