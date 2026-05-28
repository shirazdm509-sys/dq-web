<?php
/**
 * Payment help/info box — small "i" badge + paragraph text with an optional
 * inline call-to-action link. Designed for the sticky sidebar.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Payment_Help_Box extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-payment-help-box';
	}

	public function get_title() {
		return __( 'پرداخت — کادر راهنما', 'mde' );
	}

	public function get_icon() {
		return 'eicon-info-circle';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'icon_text', array( 'label' => __( 'متن داخل دایره', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => 'i' ) );
		$this->add_control( 'body', array(
			'label'   => __( 'متن', 'mde' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 5,
			'default' => __( 'در صورت سؤال شرعی درباره خمس و وجوهات، از طریق ارسال سؤال با دفتر در ارتباط باشید.', 'mde' ),
		) );
		$this->add_control( 'link_text', array( 'label' => __( 'متن دکمه/لینک', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'ارسال سؤال', 'mde' ) ) );
		$this->add_control( 'link_url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'style', array(
			'label'   => __( 'سبک', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'soft',
			'options' => array(
				'soft'   => __( 'نرم (پس‌زمینه روشن)', 'mde' ),
				'solid'  => __( 'توپر (پس‌زمینه سفید با حاشیه)', 'mde' ),
				'dashed' => __( 'حاشیه نقطه‌چین', 'mde' ),
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'sec_style', array(
			'label' => __( 'استایل', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'box_bg', array(
			'label'     => __( 'پس‌زمینه باکس', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-help' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'box_border', array(
			'label'     => __( 'رنگ حاشیه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-help' => 'border-color: {{VALUE}};' ),
		) );
		$this->add_control( 'icon_bg', array(
			'label'     => __( 'پس‌زمینه دایره', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-help__ic' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'icon_color', array(
			'label'     => __( 'رنگ متن دایره', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-help__ic' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'text_color', array(
			'label'     => __( 'رنگ متن', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-help__body' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'link_color', array(
			'label'     => __( 'رنگ لینک', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-help__body a' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$url  = ! empty( $s['link_url']['url'] ) ? $s['link_url']['url'] : '';
		echo '<div class="mde-scope" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal mde-pay-help mde-pay-help--' . esc_attr( $s['style'] ) . '">';
		echo '<div class="mde-pay-help__ic">' . esc_html( $s['icon_text'] ) . '</div>';
		echo '<div class="mde-pay-help__body">';
		$body = $s['body'];
		if ( $url && ! empty( $s['link_text'] ) ) {
			// Replace the link text inside body with an actual link.
			$linked = '<a href="' . esc_url( $url ) . '">' . esc_html( $s['link_text'] ) . '</a>';
			$replaced = str_replace( $s['link_text'], $linked, esc_html( $body ) );
			echo $replaced; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo nl2br( esc_html( $body ) ); // phpcs:ignore
		}
		echo '</div></div></div></div>';
	}
}
