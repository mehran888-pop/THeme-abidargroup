<?php
/**
 * Elementor Widget: Abidar Executive Team & Board of Directors (v2.0.0)
 *
 * المان اختصاصی نمایش تیم‌ها و مدیران ارشد گروه آبیدر با ۴ چیدمان متفاوت و کنترل کامل المنتور
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Widget_Team
 */
class Abidar_Widget_Team extends Abidar_Elementor_Widget_Base {

	public function get_name() {
		return 'abidar_team';
	}

	public function get_title() {
		return esc_html__( 'تیم و هیئت مدیره آبیدر (۴ چیدمان)', 'abidar-group' );
	}

	public function get_icon() {
		return 'eicon-person';
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
				'label' => esc_html__( 'چیدمان و نمایش اعضای تیم', 'abidar-group' ),
			)
		);

		$this->add_control(
			'layout_style',
			array(
				'label'   => esc_html__( 'انتخاب طرح نمایش تیم (Layout)', 'abidar-group' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'layout_1',
				'options' => array(
					'layout_1' => esc_html__( 'طرح ۱: کارت‌های مدیران ارشد با نمایش رزومه و ارتباط مستقیم', 'abidar-group' ),
					'layout_2' => esc_html__( 'طرح ۲: ساختار سلسله‌مراتبی هیئت مدیره (Hierarchy Spotlight)', 'abidar-group' ),
					'layout_3' => esc_html__( 'طرح ۳: گرید پرتره مدرن شش‌ضلعی / دایره‌ای', 'abidar-group' ),
					'layout_4' => esc_html__( 'طرح ۴: لیست افقی بیوگرافی تخصصی مدیران', 'abidar-group' ),
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
		$members = function_exists( 'abidar_get_content_dataset' ) ? abidar_get_content_dataset( 'abidar_team' ) : array();

		// اگر هنوز هیچ عضوی در پیشخوان ثبت نشده باشد، نمایش مجموعه دمو برای پیش‌نمایش.
		if ( empty( $members ) ) {
			$members = Abidar_CPT::get_default_dataset( 'team' );
		}
		?>
		<section class="abidar-section abidar-team-sec" data-widget-section="team" data-active-layout="<?php echo esc_attr( $layout ); ?>">
			<div class="abidar-container">
				<div class="abidar-section__head">
					<div>
						<span class="abidar-section__eyebrow"><?php echo esc_html( abidar_t( 'section_team_sub' ) ); ?></span>
						<h2 class="abidar-section__title"><?php echo esc_html( abidar_t( 'section_team_title' ) ); ?></h2>
					</div>

					<?php if ( abidar_show_layout_switcher() ) : ?>
						<div class="abidar-layout-switcher">
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_1' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_1">طرح ۱: کارت مدیران</button>
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_2' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_2">طرح ۲: چارت رهبری</button>
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_3' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_3">طرح ۳: پرتره مدرن</button>
						<button type="button" class="abidar-layout-btn <?php echo ( 'layout_4' === $layout ) ? 'is-active' : ''; ?>" data-switch-layout="layout_4">طرح ۴: رزومه افقی</button>
					</div>
					<?php endif; ?>
				</div>

				<div class="abidar-team-grid abidar-widget-grid abidar-widget-grid--<?php echo esc_attr( $layout ); ?>">
					<?php foreach ( $members as $member ) : ?>
						<?php
						$name = abidar_multilang_val( $member, 'name', $member['name_fa'] );
						$role = abidar_multilang_val( $member, 'role', $member['role_fa'] );
						$dept = abidar_multilang_val( $member, 'dept', $member['dept_fa'] );
						$bio  = abidar_multilang_val( $member, 'bio', $member['bio_fa'] );
						?>
						<article class="abidar-team-card" style="--member-accent: <?php echo esc_attr( $member['accent'] ); ?>;">
							<div class="abidar-team-card__avatar">
								<div class="abidar-team-card__monogram">
									<span><?php echo esc_html( $member['initials'] ); ?></span>
								</div>
								<span class="abidar-team-card__dept"><?php echo esc_html( $dept ); ?></span>
							</div>

							<div class="abidar-team-card__body">
								<h3 class="abidar-team-card__name">
									<?php if ( ! empty( $member['permalink'] ) ) : ?>
										<a href="<?php echo esc_url( $member['permalink'] ); ?>"><?php echo esc_html( $name ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $name ); ?>
									<?php endif; ?>
								</h3>
								<p class="abidar-team-card__role"><?php echo esc_html( $role ); ?></p>
								<p class="abidar-team-card__bio"><?php echo esc_html( $bio ); ?></p>

								<?php if ( 'fa' === Abidar_I18n::get_current_lang() ) : ?>
									<div class="abidar-team-card__meta">
										<span>🎓 <?php echo esc_html( $member['education'] ); ?></span>
										<span>⏱️ <?php echo esc_html( $member['experience'] ); ?></span>
									</div>
								<?php endif; ?>

								<div class="abidar-team-card__socials">
									<a href="mailto:<?php echo esc_attr( $member['email'] ); ?>" class="abidar-team-social" title="<?php echo esc_attr( $member['email'] ); ?>">
										✉️ <?php echo esc_html( $member['email'] ); ?>
									</a>
									<a href="<?php echo esc_url( $member['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" class="abidar-team-social">
										in LinkedIn
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
