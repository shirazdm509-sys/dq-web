<?php
/**
 * Payment trust strip — a horizontal row of icon + label + description
 * trust signals (secure payment, official receipt, support, religious
 * approval, etc.). Repeater-driven.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class MDE_Widget_Payment_Trust_Strip extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-payment-trust-strip';
	}

	public function get_title() {
		return __( 'پرداخت — نوار اعتماد', 'mde' );
	}

	public function get_icon() {
		return 'eicon-checkbox';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'icon', array(
			'label'   => __( 'آیکن', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'check',
			'options' => array(
				'check'     => __( 'تیک', 'mde' ),
				'lock'      => __( 'قفل (پرداخت امن)', 'mde' ),
				'doc'       => __( 'سند/رسید', 'mde' ),
				'users'     => __( 'پشتیبانی', 'mde' ),
				'shield'    => __( 'سپر/اعتماد', 'mde' ),
				'phones'    => __( 'تماس', 'mde' ),
				'mosque'    => __( 'مسجد', 'mde' ),
				'heart'     => __( 'قلب', 'mde' ),
				'broadcast' => __( 'پخش', 'mde' ),
				'eye'       => __( 'چشم', 'mde' ),
			),
		) );
		$r->add_control( 'label', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'desc', array( 'label' => __( 'توضیح', 'mde' ), 'type' => Controls_Manager::TEXT ) );

		$this->add_control( 'items', array(
			'label'       => __( 'آیتم‌ها', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => array(
				array( 'icon' => 'lock',  'label' => 'پرداخت امن', 'desc' => 'SSL ۲۵۶ بیتی' ),
				array( 'icon' => 'check', 'label' => 'رسید رسمی', 'desc' => 'صدور خودکار' ),
				array( 'icon' => 'users', 'label' => 'پشتیبانی', 'desc' => '۰۹۱۸۱۸۳۸۶۸۰' ),
				array( 'icon' => 'shield', 'label' => 'اعتماد شرعی', 'desc' => 'تأیید دفتر' ),
			),
			'title_field' => '{{{ label }}}',
		) );

		$this->add_control( 'cols', array(
			'label'   => __( 'ستون‌ها', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '4',
			'options' => array( '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'sec_style', array(
			'label' => __( 'استایل', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'card_bg', array(
			'label'     => __( 'پس‌زمینه باکس', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-trust' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'icon_bg', array(
			'label'     => __( 'پس‌زمینه آیکن', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-trust__ic' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'icon_color', array(
			'label'     => __( 'رنگ آیکن', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-trust__ic' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'label_color', array(
			'label'     => __( 'رنگ عنوان', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-trust__label' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'desc_color', array(
			'label'     => __( 'رنگ توضیح', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-trust__desc' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$cols = (int) $s['cols'];
		echo '<div class="mde-scope" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal mde-pay-trust" data-cols="' . esc_attr( $cols ) . '">';
		foreach ( (array) $s['items'] as $it ) {
			$icon  = isset( $it['icon'] ) ? $it['icon'] : 'check';
			$label = isset( $it['label'] ) ? $it['label'] : '';
			$desc  = isset( $it['desc'] ) ? $it['desc'] : '';
			echo '<div class="mde-pay-trust__item">';
			echo '<div class="mde-pay-trust__ic">' . self::trust_icon( $icon ) . '</div>'; // phpcs:ignore
			echo '<div><div class="mde-pay-trust__label">' . esc_html( $label ) . '</div><div class="mde-pay-trust__desc">' . esc_html( $desc ) . '</div></div>';
			echo '</div>';
		}
		echo '</div></div></div>';
	}

	private static function trust_icon( $name ) {
		switch ( $name ) {
			case 'lock':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>';
			case 'shield':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>';
			default:
				return MDE_Helpers::icon( $name, 18 );
		}
	}
}
