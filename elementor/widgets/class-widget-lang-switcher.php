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
					'pills_flag'  => esc_html__( 'طرح ۱: دکمه‌های کپسولی با پرچم و نام بومی', 'abidar-group' ),
					'compact_iso' => esc_html__( 'طرح ۲: کدهای کوتاه استاندارد (FA | EN | ZH)', 'abidar-group' ),
					'dropdown'    => esc_html__( 'طرح ۳: منوی کشویی مدرن', 'abidar-group' ),
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
		$style     = isset( $settings['layout_style'] ) ? $settings['layout_style'] : abidar_get_option( 'lang_switcher_style', 'pills_flag' );
		$languages = Abidar_I18n::get_languages();
		$curr_lang = Abidar_I18n::get_current_lang();
		?>
		<div class="abidar-lang-widget abidar-lang-widget--<?php echo esc_attr( $style ); ?>">
			<?php foreach ( $languages as $code => $lang ) : ?>
				<a href="<?php echo esc_url( Abidar_I18n::get_language_url( $code ) ); ?>"
				   class="abidar-lang-widget__item <?php echo ( $curr_lang === $code ) ? 'is-active' : ''; ?>">
					<span class="abidar-lang-widget__flag"><?php echo esc_html( $lang['flag'] ); ?></span>
					<span class="abidar-lang-widget__name">
						<?php echo ( 'compact_iso' === $style ) ? esc_html( strtoupper( $code ) ) : esc_html( $lang['native_name'] ); ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
