<?php
/**
 * Elementor Free Widget: Abidar Multilingual Language Switcher (v2.0.0)
 *
 * المان اختصاصی سوییچر تغییر زبان (فارسی، انگلیسی، چینی + زبان‌های اضافه‌شده در پیشخوان)
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Lang_Switcher
 */
class Abidar_Widget_Lang_Switcher extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_lang_switcher';
	}

	public function get_title() {
		return esc_html__( 'تغییر زبان هوشمند آبیدر (FA/EN/ZH+)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-globe';
	}

	public function get_categories() {
		return array( 'abidar-header-footer', 'abidar-elements' );
	}

	protected function register_controls() {
		if ( ! class_exists( '\Elementor\Controls_Manager' ) ) {
			return;
		}

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'چیدمان سوییچر زبان', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'طرح نمایش زبان‌ها', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'pills_flag',
				'options' => array(
					'pills_flag'     => esc_html__( 'طرح ۱: دکمه‌های کپسولی با پرچم و نام', 'abidar-group' ),
					'compact_iso'    => esc_html__( 'طرح ۲: کدهای کوتاه استاندارد (FA | EN | ZH)', 'abidar-group' ),
					'flags_only'     => esc_html__( 'طرح ۳: فقط پرچم‌ها (مربعی)', 'abidar-group' ),
					'flags_round'    => esc_html__( 'طرح ۴: فقط پرچم‌ها (دایره‌ای)', 'abidar-group' ),
					'dropdown'       => esc_html__( 'طرح ۵: منوی کشویی حرفه‌ای', 'abidar-group' ),
					'dropdown_flags' => esc_html__( 'طرح ۶: منوی کشویی فقط پرچم', 'abidar-group' ),
				),
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => esc_html__( 'نمایش برچسب متنی کنار انتخاب‌گر', 'abidar-group' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
				'condition'    => array(
					'layout_style!' => array( 'flags_only', 'flags_round' ),
				),
			)
		);

		$this->add_control(
			'label_text',
			array(
				'label'       => esc_html__( 'متن برچسب', 'abidar-group' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'زبان:', 'abidar-group' ),
				'condition'   => array(
					'show_label' => 'yes',
				),
			)
		);

		$this->add_control(
			'dropdown_align',
			array(
				'label'     => esc_html__( 'جهت باز شدن منوی کشویی', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'start',
				'options'   => array(
					'start' => esc_html__( 'سمت شروع (RTL: راست)', 'abidar-group' ),
					'end'   => esc_html__( 'سمت پایان (RTL: چپ)', 'abidar-group' ),
				),
				'condition' => array(
					'layout_style' => array( 'dropdown', 'dropdown_flags' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'رنگ‌ها و ظاهر', 'abidar-group' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'lang_accent_color',
			array(
				'label'     => esc_html__( 'رنگ تاکید (زبان فعال)', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .abidar-lang-widget__item.is-active'       => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .abidar-lang-dropdown__item.is-active'     => 'background-color: color-mix(in srgb, {{VALUE}} 12%, transparent);',
					'{{WRAPPER}} .abidar-lang-dropdown__item.is-active .abidar-lang-dropdown__check' => 'color: {{VALUE}};',
					'{{WRAPPER}} .abidar-lang-widget__current'              => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lang_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'abidar-group' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .abidar-lang-widget__item'          => 'color: {{VALUE}};',
					'{{WRAPPER}} .abidar-lang-dropdown__item'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .abidar-lang-widget__current'       => 'color: {{VALUE}};',
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
	 * خروجی سوییچر زبان با ۶ طرح قابل انتخاب.
	 *
	 * @param array $settings تنظیمات ویجت.
	 */
	public static function render_output( $settings = array() ) {
		$style     = isset( $settings['layout_style'] ) ? $settings['layout_style'] : abidar_get_option( 'lang_switcher_style', 'pills_flag' );
		$languages = Abidar_I18n::get_languages();
		$curr_lang = Abidar_I18n::get_current_lang();

		if ( empty( $languages ) ) {
			return;
		}

		$show_label = isset( $settings['show_label'] ) && 'yes' === $settings['show_label'];
		$label_text = isset( $settings['label_text'] ) && '' !== $settings['label_text']
			? $settings['label_text']
			: esc_html__( 'زبان:', 'abidar-group' );
		$align      = isset( $settings['dropdown_align'] ) && 'end' === $settings['dropdown_align'] ? 'end' : 'start';

		$current = isset( $languages[ $curr_lang ] ) ? $languages[ $curr_lang ] : reset( $languages );
		$is_dropdown = in_array( $style, array( 'dropdown', 'dropdown_flags' ), true );

		// برچسبی که کنار انتخاب‌گر نمایش داده می‌شود.
		$label_html = ( $show_label && 'flags_only' !== $style && 'flags_round' !== $style )
			? '<span class="abidar-lang-widget__label">' . esc_html( $label_text ) . '</span>'
			: '';

		if ( $is_dropdown ) {
			$current_html = 'dropdown_flags' === $style
				? '<span class="abidar-lang-widget__flag">' . esc_html( $current['flag'] ) . '</span>'
				: '<span class="abidar-lang-widget__flag">' . esc_html( $current['flag'] ) . '</span>'
					. '<span class="abidar-lang-widget__name">' . esc_html( $current['native_name'] ) . '</span>';
			?>
			<div class="abidar-lang-widget abidar-lang-widget--<?php echo esc_attr( $style ); ?> abidar-lang-widget--align-<?php echo esc_attr( $align ); ?>" data-lang-dropdown>
				<?php echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="abidar-lang-dropdown">
					<button type="button"
						class="abidar-lang-widget__current"
						aria-haspopup="listbox"
						aria-expanded="false"
						aria-label="<?php esc_attr_e( 'انتخاب زبان سایت', 'abidar-group' ); ?>">
						<?php echo $current_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="abidar-lang-dropdown__caret" aria-hidden="true"></span>
					</button>
					<ul class="abidar-lang-dropdown__menu" role="listbox">
						<?php foreach ( $languages as $code => $lang ) : ?>
							<li role="option" aria-selected="<?php echo ( $curr_lang === $code ) ? 'true' : 'false'; ?>">
								<a href="<?php echo esc_url( Abidar_I18n::get_language_url( $code ) ); ?>"
								   class="abidar-lang-dropdown__item <?php echo ( $curr_lang === $code ) ? 'is-active' : ''; ?>"
								   hreflang="<?php echo esc_attr( $code ); ?>"
								   lang="<?php echo esc_attr( $code ); ?>">
									<span class="abidar-lang-widget__flag"><?php echo esc_html( $lang['flag'] ); ?></span>
									<span class="abidar-lang-widget__name"><?php echo esc_html( $lang['native_name'] ); ?></span>
									<?php if ( $curr_lang === $code ) : ?>
										<span class="abidar-lang-dropdown__check" aria-hidden="true">✓</span>
									<?php endif; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php
			return;
		}

		// طرح‌های افقی (کپسولی، کد کوتاه، فقط پرچم).
		?>
		<div class="abidar-lang-widget abidar-lang-widget--<?php echo esc_attr( $style ); ?>">
			<?php echo $label_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php
			foreach ( $languages as $code => $lang ) :
				$is_active = ( $curr_lang === $code );

				$inner = '<span class="abidar-lang-widget__flag">' . esc_html( $lang['flag'] ) . '</span>';

				if ( 'compact_iso' === $style ) {
					$inner .= '<span class="abidar-lang-widget__name">' . esc_html( strtoupper( $code ) ) . '</span>';
				} elseif ( 'flags_only' !== $style && 'flags_round' !== $style ) {
					$inner .= '<span class="abidar-lang-widget__name">' . esc_html( $lang['native_name'] ) . '</span>';
				}
				?>
				<a href="<?php echo esc_url( Abidar_I18n::get_language_url( $code ) ); ?>"
				   class="abidar-lang-widget__item <?php echo $is_active ? 'is-active' : ''; ?>"
				   hreflang="<?php echo esc_attr( $code ); ?>"
				   lang="<?php echo esc_attr( $code ); ?>"
				   title="<?php echo esc_attr( $lang['native_name'] ); ?>"
				   aria-label="<?php echo esc_attr( $lang['native_name'] ); ?>"
				   <?php echo $is_active ? 'aria-current="true"' : ''; ?>>
					<?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}

}
