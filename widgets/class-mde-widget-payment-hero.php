<?php
/**
 * Payment hero — breadcrumb + badge + big title + lede paragraph for the
 * "وجوهات شرعی" landing area.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MDE_Widget_Payment_Hero extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-payment-hero';
	}

	public function get_title() {
		return __( 'پرداخت — هیرو', 'mde' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'show_breadcrumb', array( 'label' => __( 'نمایش مسیر راهنما', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'crumb_label', array( 'label' => __( 'متن مسیر فعلی', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'پرداخت اینترنتی وجوهات', 'mde' ), 'condition' => array( 'show_breadcrumb' => 'yes' ) ) );

		$this->add_control( 'show_badge', array( 'label' => __( 'نمایش برچسب کوچک', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'badge_text', array( 'label' => __( 'متن برچسب', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'پرداخت اینترنتی', 'mde' ), 'condition' => array( 'show_badge' => 'yes' ) ) );

		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => __( 'وجوهات شرعی · دفتر آیت‌الله دستغیب', 'mde' ) ) );
		$this->add_control( 'subtitle', array( 'label' => __( 'متن توضیح', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => __( 'در ادامه می‌توانید تعداد هر مورد را وارد و در پایان همه را با یک پرداخت تسویه کنید. مبالغ بر اساس فتاوای روزآمد دفتر معظم‌له ارائه شده است.', 'mde' ) ) );
		$this->end_controls_section();

		// Title typography (working selector this time).
		$this->start_controls_section( 'sec_typo', array(
			'label' => __( 'تایپوگرافی', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'label'    => __( 'تیتر', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-pay-hero h1',
			)
		);
		$this->add_control( 'title_color', array(
			'label'     => __( 'رنگ تیتر', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-hero h1' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sub_typo',
				'label'    => __( 'متن توضیح', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-pay-hero p',
			)
		);
		$this->add_control( 'sub_color', array(
			'label'     => __( 'رنگ متن توضیح', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-hero p' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'badge_bg', array(
			'label'     => __( 'پس‌زمینه برچسب', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-hero__badge' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'badge_color', array(
			'label'     => __( 'رنگ متن برچسب', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-hero__badge' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="mde-scope mde-pay-hero" dir="rtl"><div class="mde-container">';

		if ( 'yes' === $s['show_breadcrumb'] ) {
			echo '<nav class="mde-pay-hero__crumb">';
			echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . MDE_Helpers::icon( 'home', 13 ) . ' ' . esc_html__( 'خانه', 'mde' ) . '</a>'; // phpcs:ignore
			echo '<span>/</span>';
			echo '<span class="current">' . esc_html( $s['crumb_label'] ) . '</span>';
			echo '</nav>';
		}

		echo '<div class="mde-reveal mde-pay-hero__inner">';
		if ( 'yes' === $s['show_badge'] ) {
			echo '<span class="mde-pay-hero__badge">' . MDE_Helpers::icon( 'mosque', 14 ) . ' ' . esc_html( $s['badge_text'] ) . '</span>'; // phpcs:ignore
		}
		echo '<h1>' . nl2br( esc_html( $s['title'] ) ) . '</h1>'; // phpcs:ignore
		echo '<p>' . nl2br( esc_html( $s['subtitle'] ) ) . '</p>'; // phpcs:ignore
		echo '</div>';

		echo '</div></div>';
	}
}
