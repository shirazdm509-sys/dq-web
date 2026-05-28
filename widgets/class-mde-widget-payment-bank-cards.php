<?php
/**
 * Payment bank cards — "پرداخت کارت به کارت" with a repeater of bank cards
 * (label, card number, optional account number). Each card has a one-click
 * copy button.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class MDE_Widget_Payment_Bank_Cards extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-payment-bank-cards';
	}

	public function get_title() {
		return __( 'پرداخت — کارت‌های بانکی', 'mde' );
	}

	public function get_icon() {
		return 'eicon-credit-card';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'پرداخت کارت به کارت', 'mde' ) ) );
		$this->add_control( 'intro', array( 'label' => __( 'متن مقدمه', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => __( 'در صورتی که امکان پرداخت از طریق درگاه را ندارید، می‌توانید مبلغ موردنظر را به یکی از حساب‌های زیر واریز و رسید را به شماره ۰۹۱۸۱۸۳۸۶۸۰ ارسال نمایید.', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'label', array( 'label' => __( 'عنوان کارت', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'card', array( 'label' => __( 'شماره کارت', 'mde' ), 'type' => Controls_Manager::TEXT, 'description' => __( 'با فاصله نوشته شود مثل: 6104 3378 0341 5890', 'mde' ) ) );
		$r->add_control( 'acc', array( 'label' => __( 'شماره حساب (اختیاری)', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'iban', array( 'label' => __( 'شبا (اختیاری)', 'mde' ), 'type' => Controls_Manager::TEXT ) );

		$this->add_control( 'items', array(
			'label'       => __( 'کارت‌ها', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => array(
				array( 'label' => 'کارت بانک ملت', 'card' => '6104 3378 0341 5890', 'acc' => '64995668.85' ),
				array( 'label' => 'کارت ملی', 'card' => '6037 9918 5328 3960', 'acc' => '0354781.290907' ),
				array( 'label' => 'حساب مهر (ویژه فطریه سادات)', 'card' => '4803 0251 6203 6930 01' ),
				array( 'label' => 'کارت مهر (ویژه فطریه سادات)', 'card' => '6063 7311 8364 5499' ),
			),
			'title_field' => '{{{ label }}}',
		) );

		$this->add_control( 'cols', array(
			'label'   => __( 'ستون‌ها', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '2',
			'options' => array( '1' => '۱', '2' => '۲', '3' => '۳' ),
		) );

		$this->add_control( 'footer_text', array( 'label' => __( 'متن پایانی', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'همه به‌نام آیت‌الله سید علی‌محمد دستغیب', 'mde' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'sec_style', array(
			'label' => __( 'استایل', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'label'    => __( 'تیتر', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-pay-banks h3',
			)
		);
		$this->add_control( 'title_color', array(
			'label'     => __( 'رنگ تیتر', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-banks h3' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'card_bg', array(
			'label'     => __( 'پس‌زمینه کارت‌های بانک', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-bank' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'card_border', array(
			'label'     => __( 'رنگ حاشیه کارت‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-bank' => 'border-color: {{VALUE}};' ),
		) );
		$this->add_control( 'card_border_hover', array(
			'label'     => __( 'رنگ حاشیه (هاور)', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-bank:hover' => 'border-color: {{VALUE}};' ),
		) );
		$this->add_control( 'card_num_color', array(
			'label'     => __( 'رنگ شماره کارت', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-bank__num' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'copy_bg', array(
			'label'     => __( 'پس‌زمینه دکمه کپی', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-bank__copy' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'copy_color', array(
			'label'     => __( 'رنگ آیکن کپی', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-bank__copy' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$cols = (int) $s['cols'];
		echo '<div class="mde-scope" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal mde-pay-banks" data-cols="' . esc_attr( $cols ) . '">';
		echo '<h3>' . esc_html( $s['title'] ) . '</h3>';
		if ( ! empty( $s['intro'] ) ) {
			echo '<p class="mde-pay-banks__intro">' . nl2br( esc_html( $s['intro'] ) ) . '</p>'; // phpcs:ignore
		}
		echo '<div class="mde-pay-banks__grid">';
		foreach ( (array) $s['items'] as $i => $it ) {
			$card = isset( $it['card'] ) ? $it['card'] : '';
			$copy = preg_replace( '/\s+/', '', $card );
			echo '<div class="mde-pay-bank">';
			echo '<div class="mde-pay-bank__label">' . esc_html( isset( $it['label'] ) ? $it['label'] : '' ) . '</div>';
			echo '<div class="mde-pay-bank__row">';
			echo '<div class="mde-pay-bank__num" dir="ltr">' . esc_html( $card ) . '</div>';
			echo '<button type="button" class="mde-pay-bank__copy" data-mde-copy="' . esc_attr( $copy ) . '" title="' . esc_attr__( 'کپی شماره کارت', 'mde' ) . '" aria-label="' . esc_attr__( 'کپی', 'mde' ) . '">';
			echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>';
			echo '</button>';
			echo '</div>';
			if ( ! empty( $it['acc'] ) ) {
				echo '<div class="mde-pay-bank__acc" dir="ltr">' . esc_html__( 'حساب:', 'mde' ) . ' ' . esc_html( $it['acc'] ) . '</div>';
			}
			if ( ! empty( $it['iban'] ) ) {
				echo '<div class="mde-pay-bank__acc" dir="ltr">' . esc_html__( 'شبا:', 'mde' ) . ' ' . esc_html( $it['iban'] ) . '</div>';
			}
			echo '</div>';
		}
		echo '</div>';
		if ( ! empty( $s['footer_text'] ) ) {
			echo '<div class="mde-pay-banks__footer">' . esc_html( $s['footer_text'] ) . '</div>';
		}
		echo '</div></div></div>';
	}
}
