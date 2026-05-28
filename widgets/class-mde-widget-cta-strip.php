<?php
/**
 * CTA strip — live broadcast promo + bookstore promo (animated blob).
 * Both call-to-action panels expose dedicated button color controls.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Cta_Strip extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-cta-strip';
	}

	public function get_title() {
		return __( 'نوار پخش زنده و فروشگاه', 'mde' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec_live', array( 'label' => __( 'پخش زنده', 'mde' ) ) );
		$this->add_control( 'l_badge', array( 'label' => __( 'برچسب', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'در حال پخش زنده', 'mde' ) ) );
		$this->add_control( 'l_title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جلسه تفسیر قرآن — مستقیم از حسینیه', 'mde' ) ) );
		$this->add_control( 'l_text', array( 'label' => __( 'توضیح', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'در حال حاضر سخنرانی حضرت آیت‌الله دستغیب «دامت برکاته» به‌صورت زنده در حال پخش است.', 'mde' ) ) );
		$this->add_control( 'l_btn', array( 'label' => __( 'متن دکمه', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مشاهده زنده', 'mde' ) ) );
		$this->add_control( 'l_url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'l_viewers', array( 'label' => __( 'تعداد بینندگان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => '۱٬۲۸۴' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'sec_shop', array( 'label' => __( 'فروشگاه', 'mde' ) ) );
		$this->add_control( 's_badge', array( 'label' => __( 'برچسب', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'فروشگاه اینترنتی', 'mde' ) ) );
		$this->add_control( 's_title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'آثار مکتوب حضرت آیت‌الله دستغیب', 'mde' ) ) );
		$this->add_control( 's_text', array( 'label' => __( 'توضیح', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'مجموعه کتاب‌های تفسیر قرآن، نهج‌البلاغه و سخنرانی‌های ایشان.', 'mde' ) ) );
		$this->add_control( 's_btn', array( 'label' => __( 'متن دکمه', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'ورود به فروشگاه', 'mde' ) ) );
		$this->add_control( 's_url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->end_controls_section();

		$this->start_controls_section( 'sec_btn_style', array(
			'label' => __( 'رنگ دکمه‌ها', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'l_btn_bg', array(
			'label'     => __( 'پس‌زمینه دکمه پخش زنده', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--live' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'l_btn_color', array(
			'label'     => __( 'رنگ متن دکمه پخش زنده', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1a1a1a',
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--live' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'l_btn_bg_hover', array(
			'label'     => __( 'پس‌زمینه (هاور) پخش زنده', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--live:hover' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'l_btn_color_hover', array(
			'label'     => __( 'رنگ متن (هاور) پخش زنده', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--live:hover' => 'color: {{VALUE}};',
			),
		) );

		$this->add_control( 's_btn_bg', array(
			'label'     => __( 'پس‌زمینه دکمه فروشگاه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--shop' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 's_btn_color', array(
			'label'     => __( 'رنگ متن دکمه فروشگاه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#8b6e3a',
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--shop' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 's_btn_bg_hover', array(
			'label'     => __( 'پس‌زمینه (هاور) فروشگاه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--shop:hover' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 's_btn_color_hover', array(
			'label'     => __( 'رنگ متن (هاور) فروشگاه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cta-btn--shop:hover' => 'color: {{VALUE}};',
			),
		) );

		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s  = $this->get_settings_for_display();
		$lu = ! empty( $s['l_url']['url'] ) ? $s['l_url']['url'] : '#';
		$su = ! empty( $s['s_url']['url'] ) ? $s['s_url']['url'] : '#';
		echo '<div class="mde-scope mde-section--tight" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-cta-row">';

		echo '<a class="mde-cta mde-cta--live mde-reveal" href="' . esc_url( $lu ) . '">';
		echo '<div class="mde-cta__blob"></div>';
		echo '<span class="mde-cta__badge"><span class="dot"></span>' . esc_html( $s['l_badge'] ) . '</span>';
		echo '<h3>' . esc_html( $s['l_title'] ) . '</h3><p>' . esc_html( $s['l_text'] ) . '</p>';
		echo '<div style="margin-top:22px;display:flex;gap:10px;align-items:center;position:relative;">';
		echo '<span class="mde-btn mde-btn--lg mde-cta-btn mde-cta-btn--live">' . MDE_Helpers::icon( 'play' ) . ' ' . esc_html( $s['l_btn'] ) . '</span>'; // phpcs:ignore
		echo '<span style="font-size:12.5px;opacity:.6;">' . MDE_Helpers::icon( 'users', 12 ) . ' ' . esc_html( $s['l_viewers'] ) . ' ' . esc_html__( 'نفر در حال مشاهده', 'mde' ) . '</span>'; // phpcs:ignore
		echo '</div></a>';

		echo '<a class="mde-cta mde-cta--shop mde-reveal" data-delay="100" href="' . esc_url( $su ) . '">';
		echo '<span class="mde-cta__badge">' . MDE_Helpers::icon( 'book', 13 ) . ' ' . esc_html( $s['s_badge'] ) . '</span>'; // phpcs:ignore
		echo '<h3>' . esc_html( $s['s_title'] ) . '</h3><p>' . esc_html( $s['s_text'] ) . '</p>';
		echo '<span class="mde-btn mde-cta-btn mde-cta-btn--shop" style="margin-top:18px;position:relative;">' . esc_html( $s['s_btn'] ) . ' ' . MDE_Helpers::icon( 'chevl', 16 ) . '</span>'; // phpcs:ignore
		echo '</a>';

		echo '</div></div></div>';
	}
}
