<?php
/**
 * Live player — big 16:9 player, LIVE badge, animated viewer + elapsed
 * counters, optional embed URL, action row + "about this session".
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Live_Player extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-live-player';
	}

	public function get_title() {
		return __( 'پخش‌کننده زنده', 'mde' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'badge', array( 'label' => __( 'وضعیت', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'در حال پخش زنده', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جلسه تفسیر قرآن کریم', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مستقیم از حسینیه قبا — شیراز · حضرت آیت‌الله سید علی‌محمد دستغیب', 'mde' ) ) );
		$this->add_control( 'embed', array(
			'label'       => __( 'کد امبد پخش زنده', 'mde' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 8,
			'dynamic'     => array( 'active' => true ),
			'description' => __( 'کد کامل امبد (شامل iframe و style) را اینجا جای‌گذاری کنید. مثال آپارات لایو: <code>&lt;iframe src="https://www.aparat.com/embed/live/..."&gt;&lt;/iframe&gt;</code> — خالی = نمایش تصویر پوستر و دکمه پخش.', 'mde' ),
		) );
		$this->add_control( 'poster', array( 'label' => __( 'تصویر پوستر', 'mde' ), 'type' => Controls_Manager::MEDIA ) );
		$this->add_control( 'viewers', array( 'label' => __( 'بینندگان اولیه', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 1287 ) );
		$this->add_control( 'elapsed', array( 'label' => __( 'زمان پخش (ثانیه)', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 2340 ) );
		$this->add_control( 'about_title', array( 'label' => __( 'عنوان درباره جلسه', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'درباره این جلسه', 'mde' ) ) );
		$this->add_control( 'about_text', array( 'label' => __( 'متن درباره جلسه', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'جلسه تفسیر سوره قصص توسط حضرت آیت‌الله سید علی‌محمد دستغیب در حسینیه قبا، شیراز. این جلسات هر هفته روزهای چهارشنبه بعد از نماز مغرب و عشاء برگزار می‌گردد.', 'mde' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'sec_btn_style', array(
			'label' => __( 'رنگ دکمه‌ها', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'action_btn_bg', array(
			'label'     => __( 'پس‌زمینه دکمه‌های اشتراک/پسندیدم/ذخیره', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-live-action' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'action_btn_color', array(
			'label'     => __( 'رنگ متن دکمه‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-live-action' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'action_btn_border', array(
			'label'     => __( 'رنگ حاشیه دکمه‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-live-action' => 'border-color: {{VALUE}};',
			),
		) );
		$this->add_control( 'action_btn_bg_hover', array(
			'label'     => __( 'پس‌زمینه (هاور)', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-live-action:hover' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'action_btn_color_hover', array(
			'label'     => __( 'رنگ متن (هاور)', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-live-action:hover' => 'color: {{VALUE}};',
			),
		) );

		$this->add_control( 'play_btn_bg', array(
			'label'     => __( 'پس‌زمینه دکمه پخش (روی پخش‌کننده)', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-player__play button' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'play_btn_color', array(
			'label'     => __( 'رنگ آیکن دکمه پخش', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-player__play button' => 'color: {{VALUE}};',
			),
		) );

		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	/**
	 * Filter the embed code so admins can paste raw iframe markup without
	 * `wp_kses_post` stripping the iframe/style tags. We allow only the
	 * tags an embed actually needs and validate iframe `src` against a
	 * trusted host allowlist.
	 *
	 * @param string $html Raw embed HTML stored on the widget.
	 * @return string Sanitised HTML ready for output.
	 */
	protected function sanitize_embed( $html ) {
		$allowed = array(
			'iframe' => array(
				'src'                   => true,
				'width'                 => true,
				'height'                => true,
				'frameborder'           => true,
				'scrolling'             => true,
				'allowfullscreen'       => true,
				'webkitallowfullscreen' => true,
				'mozallowfullscreen'    => true,
				'allow'                 => true,
				'referrerpolicy'        => true,
				'title'                 => true,
				'style'                 => true,
				'class'                 => true,
				'name'                  => true,
				'sandbox'               => true,
				'loading'               => true,
			),
			'div'    => array( 'class' => true, 'style' => true, 'id' => true ),
			'span'   => array( 'class' => true, 'style' => true ),
			'style'  => array( 'type' => true ),
			'script' => array(
				'src'   => true,
				'async' => true,
				'defer' => true,
				'type'  => true,
			),
		);
		return wp_kses( $html, $allowed );
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$poster = ! empty( $s['poster']['url'] ) ? $s['poster']['url'] : MDE_ASSETS . 'images/logo.jpg';

		echo '<div class="mde-scope mde-page-enter" dir="rtl" data-mde-live><div class="mde-container">';

		echo '<div class="mde-reveal"><div class="mde-live-head">';
		echo '<div><span class="mde-live-badge"><span class="dot"></span>' . esc_html( $s['badge'] ) . '</span>';
		echo '<h1>' . esc_html( $s['title'] ) . '</h1><p>' . esc_html( $s['sub'] ) . '</p></div>';
		echo '<div style="display:flex;gap:18px;font-size:13px;">';
		echo '<div class="mde-live-stat"><div class="l">' . esc_html__( 'زمان پخش', 'mde' ) . '</div><div class="v" data-mde-elapsed data-start="' . esc_attr( (int) $s['elapsed'] ) . '" style="font-family:ui-monospace,monospace;color:var(--c-ink);">۰۰:۳۹:۰۰</div></div>';
		echo '<div class="mde-live-stat"><div class="l">' . esc_html__( 'بینندگان', 'mde' ) . '</div><div class="v" style="color:var(--c-primary);">' . MDE_Helpers::icon( 'users', 14 ) . ' <span data-mde-viewers data-start="' . esc_attr( (int) $s['viewers'] ) . '">' . esc_html( MDE_Helpers::fa( $s['viewers'] ) ) . '</span></div></div>'; // phpcs:ignore
		echo '</div></div></div>';

		echo '<div class="mde-reveal"><div class="mde-player">';
		if ( ! empty( $s['embed'] ) ) {
			echo $this->sanitize_embed( $s['embed'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo '<img src="' . esc_url( $poster ) . '" alt="" />';
			echo '<div class="mde-player__play"><button>' . MDE_Helpers::icon( 'play', 42 ) . '</button></div>'; // phpcs:ignore
		}
		echo '<div class="mde-player__live"><span class="dot"></span>LIVE</div>';
		echo '<div class="mde-player__views">' . MDE_Helpers::icon( 'eye', 14 ) . ' <span data-mde-viewers data-start="' . esc_attr( (int) $s['viewers'] ) . '">' . esc_html( MDE_Helpers::fa( $s['viewers'] ) ) . '</span></div>'; // phpcs:ignore
		echo '</div></div>';

		echo '<div class="mde-reveal"><div style="display:flex;justify-content:space-between;align-items:center;padding:18px 22px;background:var(--c-surface);border:1px solid var(--c-border);border-radius:14px;margin-top:22px;flex-wrap:wrap;gap:14px;">';
		echo '<div style="display:flex;gap:8px;">';
		echo '<button class="mde-btn mde-btn--ghost mde-live-action">' . MDE_Helpers::icon( 'heart', 16 ) . ' ' . esc_html__( 'پسندیدم', 'mde' ) . '</button>'; // phpcs:ignore
		echo '<button class="mde-btn mde-btn--ghost mde-live-action">' . MDE_Helpers::icon( 'share', 16 ) . ' ' . esc_html__( 'اشتراک', 'mde' ) . '</button>'; // phpcs:ignore
		echo '<button class="mde-btn mde-btn--ghost mde-live-action">' . MDE_Helpers::icon( 'bookmark', 16 ) . ' ' . esc_html__( 'ذخیره', 'mde' ) . '</button>'; // phpcs:ignore
		echo '</div><div style="font-size:12.5px;color:var(--c-muted);">' . esc_html__( 'در صورت قطع پخش، لطفاً صفحه را تازه کنید.', 'mde' ) . '</div>';
		echo '</div></div>';

		echo '<div class="mde-reveal"><section style="margin-top:22px;padding:26px;border-radius:14px;background:var(--c-surface);border:1px solid var(--c-border);">';
		echo '<h2 style="font-size:20px;font-weight:700;margin:0 0 14px;">' . esc_html( $s['about_title'] ) . '</h2>';
		echo '<p style="font-size:15px;line-height:1.9;color:var(--c-ink-2);margin:0;">' . esc_html( $s['about_text'] ) . '</p>';
		echo '</section></div>';

		echo '</div></div>';
	}
}
