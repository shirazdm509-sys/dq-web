<?php
/**
 * Payment form wrapper — renders a Gravity Form inside our `.mde-scope`
 * so all Gravity Forms inputs, labels, buttons inherit the design tokens
 * (font, colors, radii) and the Theme CSS bundled with this plugin.
 *
 * The form itself stays in Gravity Forms; this widget only places it on
 * the page with the proper styled wrapper, optional intro text, and
 * fine-grained colour overrides for the form chrome.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MDE_Widget_Payment_Form extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-payment-form';
	}

	public function get_title() {
		return __( 'پرداخت — فرم گرویتی', 'mde' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/**
	 * Build the SELECT2 options of registered Gravity Forms. Returns an
	 * empty-state hint when the plugin isn't active.
	 *
	 * @return array<int|string,string>
	 */
	private function form_options() {
		$opts = array( '' => __( '— انتخاب فرم —', 'mde' ) );
		if ( ! class_exists( 'GFAPI' ) ) {
			$opts[''] = __( '— Gravity Forms فعال نیست —', 'mde' );
			return $opts;
		}
		$forms = \GFAPI::get_forms();
		if ( is_array( $forms ) ) {
			foreach ( $forms as $f ) {
				if ( isset( $f['id'] ) ) {
					$opts[ (int) $f['id'] ] = sprintf( '#%d — %s', (int) $f['id'], isset( $f['title'] ) ? $f['title'] : '' );
				}
			}
		}
		return $opts;
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'فرم', 'mde' ) ) );

		$this->add_control( 'form_id', array(
			'label'       => __( 'فرم گرویتی', 'mde' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => $this->form_options(),
			'label_block' => true,
		) );

		$this->add_control( 'show_title', array(
			'label'   => __( 'نمایش عنوان فرم', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		) );

		$this->add_control( 'show_description', array(
			'label'   => __( 'نمایش توضیحات فرم', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		) );

		$this->add_control( 'ajax', array(
			'label'   => __( 'ارسال با AJAX', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'intro', array(
			'label'   => __( 'متن مقدمه (اختیاری)', 'mde' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 3,
		) );

		$this->add_control( 'sticky_bar', array(
			'label'        => __( 'نوار چسبان پایین صفحه (مجموع و دکمه پرداخت)', 'mde' ),
			'description'  => __( 'یک نوار شناور با مبلغ کل و دکمه‌ی پرداخت در پایین صفحه نمایش می‌دهد. دکمه‌ی ارسال اصلی فرم پنهان می‌شود.', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		) );

		$this->add_control( 'sticky_btn_text', array(
			'label'     => __( 'متن دکمه نوار چسبان', 'mde' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'پرداخت', 'mde' ),
			'condition' => array( 'sticky_bar' => 'yes' ),
		) );

		$this->add_control( 'sticky_total_label', array(
			'label'     => __( 'برچسب مجموع', 'mde' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'مجموع', 'mde' ),
			'condition' => array( 'sticky_bar' => 'yes' ),
		) );

		$this->add_control( 'sticky_unit', array(
			'label'     => __( 'واحد پول', 'mde' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'ریال', 'mde' ),
			'condition' => array( 'sticky_bar' => 'yes' ),
		) );

		$this->add_control( 'sticky_show_on', array(
			'label'     => __( 'نمایش نوار در', 'mde' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'mobile',
			'options'   => array(
				'always' => __( 'همیشه (موبایل و دسکتاپ)', 'mde' ),
				'mobile' => __( 'فقط موبایل (تا ۱۰۰۰ پیکسل)', 'mde' ),
			),
			'condition' => array( 'sticky_bar' => 'yes' ),
		) );
		$this->end_controls_section();

		// Form chrome — typography + colors that override the GF defaults.
		$this->start_controls_section( 'sec_form_style', array(
			'label' => __( 'استایل فرم', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_typo',
				'label'    => __( 'تایپوگرافی ورودی‌ها', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-pay-form .gform_wrapper input, {{WRAPPER}} .mde-pay-form .gform_wrapper textarea, {{WRAPPER}} .mde-pay-form .gform_wrapper select',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typo',
				'label'    => __( 'تایپوگرافی برچسب‌ها', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-pay-form .gform_wrapper label, {{WRAPPER}} .mde-pay-form .gform_wrapper .gfield_label',
			)
		);

		$this->add_control( 'label_color', array(
			'label'     => __( 'رنگ برچسب‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper label, {{WRAPPER}} .mde-pay-form .gform_wrapper .gfield_label' => 'color: {{VALUE}};',
			),
		) );

		$this->add_control( 'field_bg', array(
			'label'     => __( 'پس‌زمینه ورودی‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper input:not([type=submit]):not([type=button]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .mde-pay-form .gform_wrapper textarea, {{WRAPPER}} .mde-pay-form .gform_wrapper select' => 'background: {{VALUE}};',
			),
		) );

		$this->add_control( 'field_text', array(
			'label'     => __( 'رنگ متن ورودی', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper input:not([type=submit]):not([type=button]), {{WRAPPER}} .mde-pay-form .gform_wrapper textarea, {{WRAPPER}} .mde-pay-form .gform_wrapper select' => 'color: {{VALUE}};',
			),
		) );

		$this->add_control( 'field_border', array(
			'label'     => __( 'رنگ حاشیه ورودی', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper input:not([type=submit]):not([type=button]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .mde-pay-form .gform_wrapper textarea, {{WRAPPER}} .mde-pay-form .gform_wrapper select' => 'border-color: {{VALUE}};',
			),
		) );

		$this->add_control( 'field_border_focus', array(
			'label'     => __( 'رنگ حاشیه (فوکوس)', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper input:focus, {{WRAPPER}} .mde-pay-form .gform_wrapper textarea:focus, {{WRAPPER}} .mde-pay-form .gform_wrapper select:focus' => 'border-color: {{VALUE}}; box-shadow: 0 0 0 3px ' . '{{VALUE}}33' . ';',
			),
		) );

		$this->add_responsive_control( 'field_radius', array(
			'label'      => __( 'گردی گوشه ورودی', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
			'default'    => array( 'size' => 10, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper input:not([type=submit]):not([type=button]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .mde-pay-form .gform_wrapper textarea, {{WRAPPER}} .mde-pay-form .gform_wrapper select' => 'border-radius: {{SIZE}}{{UNIT}};',
			),
		) );

		$this->end_controls_section();

		// Submit button style.
		$this->start_controls_section( 'sec_submit_style', array(
			'label' => __( 'دکمه ارسال', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submit_typo',
				'label'    => __( 'تایپوگرافی دکمه', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-pay-form .gform_wrapper .gform_button, {{WRAPPER}} .mde-pay-form .gform_wrapper input[type=submit]',
			)
		);
		$this->add_control( 'submit_bg', array(
			'label'     => __( 'پس‌زمینه دکمه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper .gform_button, {{WRAPPER}} .mde-pay-form .gform_wrapper input[type=submit]' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'submit_color', array(
			'label'     => __( 'رنگ متن دکمه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper .gform_button, {{WRAPPER}} .mde-pay-form .gform_wrapper input[type=submit]' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'submit_bg_hover', array(
			'label'     => __( 'پس‌زمینه (هاور)', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper .gform_button:hover, {{WRAPPER}} .mde-pay-form .gform_wrapper input[type=submit]:hover' => 'background: {{VALUE}};',
			),
		) );
		$this->add_responsive_control( 'submit_height', array(
			'label'      => __( 'ارتفاع دکمه', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 36, 'max' => 80 ) ),
			'default'    => array( 'size' => 52, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper .gform_button, {{WRAPPER}} .mde-pay-form .gform_wrapper input[type=submit]' => 'height: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'submit_radius', array(
			'label'      => __( 'گردی گوشه دکمه', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
			'default'    => array( 'size' => 12, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-pay-form .gform_wrapper .gform_button, {{WRAPPER}} .mde-pay-form .gform_wrapper input[type=submit]' => 'border-radius: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->end_controls_section();

		// Sticky bottom bar style.
		$this->start_controls_section( 'sec_sticky_style', array(
			'label'     => __( 'استایل نوار چسبان', 'mde' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => array( 'sticky_bar' => 'yes' ),
		) );
		$this->add_control( 'sticky_card_bg', array(
			'label'     => __( 'پس‌زمینه کارت مجموع', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-sticky__total' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'sticky_total_color', array(
			'label'     => __( 'رنگ مبلغ', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-sticky__value' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'sticky_btn_bg', array(
			'label'     => __( 'پس‌زمینه دکمه پرداخت', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-sticky__btn' => 'background: {{VALUE}}; background-image: none;' ),
		) );
		$this->add_control( 'sticky_btn_color', array(
			'label'     => __( 'رنگ متن دکمه پرداخت', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-pay-sticky__btn' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$sticky_on    = ( 'yes' === $s['sticky_bar'] && ! empty( $s['form_id'] ) && class_exists( 'GFAPI' ) );
		$sticky_scope = isset( $s['sticky_show_on'] ) ? $s['sticky_show_on'] : 'mobile';

		$wrap_class = 'mde-scope mde-pay-form';
		if ( $sticky_on ) {
			$wrap_class .= ' mde-pay-form--sticky mde-pay-form--sticky-' . $sticky_scope;
		}
		echo '<div class="' . esc_attr( $wrap_class ) . '" dir="rtl"';
		if ( $sticky_on ) {
			echo ' data-mde-pay-sticky="1"';
			echo ' data-mde-pay-btn-text="' . esc_attr( $s['sticky_btn_text'] ) . '"';
			echo ' data-mde-pay-total-label="' . esc_attr( $s['sticky_total_label'] ) . '"';
			echo ' data-mde-pay-unit="' . esc_attr( $s['sticky_unit'] ) . '"';
		}
		echo '>';
		echo '<div class="mde-container">';

		if ( ! empty( $s['intro'] ) ) {
			echo '<div class="mde-reveal mde-pay-form__intro">' . nl2br( esc_html( $s['intro'] ) ) . '</div>'; // phpcs:ignore
		}

		if ( empty( $s['form_id'] ) ) {
			echo '<div class="mde-pay-form__empty">' . esc_html__( 'لطفاً یک فرم گرویتی انتخاب کنید.', 'mde' ) . '</div>';
		} elseif ( ! class_exists( 'GFAPI' ) ) {
			echo '<div class="mde-pay-form__empty">' . esc_html__( 'افزونه Gravity Forms فعال نیست.', 'mde' ) . '</div>';
		} else {
			$shortcode = sprintf(
				'[gravityform id="%d" title="%s" description="%s" ajax="%s"]',
				(int) $s['form_id'],
				( 'yes' === $s['show_title'] ) ? 'true' : 'false',
				( 'yes' === $s['show_description'] ) ? 'true' : 'false',
				( 'yes' === $s['ajax'] ) ? 'true' : 'false'
			);
			echo '<div class="mde-reveal">' . do_shortcode( $shortcode ) . '</div>'; // phpcs:ignore
		}

		echo '</div>'; // .mde-container
		echo '</div>'; // .mde-scope
	}
}
