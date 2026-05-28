<?php
/**
 * Payment purposes — "جهت پرداخت" card with a check-marked grid of where
 * the collected funds go. Each item is a Repeater row.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class MDE_Widget_Payment_Purposes extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-payment-purposes';
	}

	public function get_title() {
		return __( 'پرداخت — جهت پرداخت', 'mde' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جهت پرداخت', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'این مبالغ صرف موارد زیر می‌گردد:', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'text', array( 'label' => __( 'متن', 'mde' ), 'type' => Controls_Manager::TEXT ) );

		$defaults = array();
		foreach ( array( 'صدقات', 'نذورات', 'خیرات و صدقات', 'وعدۀ افطاری نیازمندان', 'تأمین مخارج درمان', 'تهیه جهیزیه', 'وعدۀ اطعام مستمندان', 'کمک به تهیه مسکن مستمندان', 'و …' ) as $p ) {
			$defaults[] = array( 'text' => $p );
		}

		$this->add_control( 'items', array(
			'label'       => __( 'موارد', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => $defaults,
			'title_field' => '{{{ text }}}',
		) );

		$this->add_control( 'cols', array(
			'label'   => __( 'تعداد ستون', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '3',
			'options' => array( '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴' ),
		) );

		$this->add_control( 'note', array(
			'label'   => __( 'متن یادداشت پایین', 'mde' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 4,
			'default' => __( 'از طریق درگاه اینترنتی مرکز نشر آیت‌الله دستغیب (این صفحه) می‌توان اقدام نمود. همچنین امکان پرداخت هزینهٔ موارد قربانی، عقیقه، کمک به مخارج مسجد، نماز و روزه استیجاری و کمک به مخارج مجالس اهل بیت نیز فراهم می‌باشد.', 'mde' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'sec_typo', array(
			'label' => __( 'استایل', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'label'    => __( 'تیتر', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-pay-purposes h3',
			)
		);
		$this->add_control( 'title_color', array(
			'label'     => __( 'رنگ تیتر', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-purposes h3' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'item_color', array(
			'label'     => __( 'رنگ آیتم‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-purposes__list li' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'check_bg', array(
			'label'     => __( 'پس‌زمینه دایره تیک', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-purposes__check' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'check_color', array(
			'label'     => __( 'رنگ تیک', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-purposes__check' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'card_bg', array(
			'label'     => __( 'پس‌زمینه کارت', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-purposes' => 'background: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$cols = (int) $s['cols'];
		echo '<div class="mde-scope" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal mde-pay-purposes" data-cols="' . esc_attr( $cols ) . '">';
		echo '<h3>' . esc_html( $s['title'] ) . '</h3>';
		echo '<p class="mde-pay-purposes__sub">' . esc_html( $s['sub'] ) . '</p>';
		echo '<ul class="mde-pay-purposes__list">';
		foreach ( (array) $s['items'] as $item ) {
			$text = isset( $item['text'] ) ? $item['text'] : '';
			if ( '' === $text ) {
				continue;
			}
			echo '<li><span class="mde-pay-purposes__check">' . MDE_Helpers::icon( 'check', 11 ) . '</span>' . esc_html( $text ) . '</li>'; // phpcs:ignore
		}
		echo '</ul>';
		if ( ! empty( $s['note'] ) ) {
			echo '<div class="mde-pay-purposes__note">' . nl2br( esc_html( $s['note'] ) ) . '</div>'; // phpcs:ignore
		}
		echo '</div></div></div>';
	}
}
