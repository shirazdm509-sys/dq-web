<?php
/**
 * Reusable Elementor controls shared by every widget.
 *
 * The whole ported design system is driven by CSS custom properties
 * (--c-primary, --c-accent, --c-bg …). By exposing those as colour controls
 * and printing them on the widget wrapper, EVERY colour — including hover,
 * focus and animation states — becomes editable from the Elementor panel
 * without touching the stylesheet. Typography + spacing groups round it out.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

trait MDE_Style_Controls {

	/**
	 * Palette / colour-system controls. Each maps to a CSS variable on the
	 * widget wrapper, so the entire section (cards, buttons, hovers, badges,
	 * gradients, animations) recolours live.
	 *
	 * @param \Elementor\Widget_Base $w Widget.
	 */
	protected function mde_palette_section( $w ) {
		$w->start_controls_section(
			'mde_palette',
			array(
				'label' => __( 'پالت رنگی', 'mde' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$colors = array(
			'c_primary'     => array( __( 'رنگ اصلی', 'mde' ), '#109487', '--c-primary' ),
			'c_primary_600' => array( __( 'رنگ اصلی (تیره)', 'mde' ), '#0c7a6f', '--c-primary-600' ),
			'c_primary_50'  => array( __( 'رنگ اصلی (روشن)', 'mde' ), '#e6f4f2', '--c-primary-50' ),
			'c_accent'      => array( __( 'رنگ دوم', 'mde' ), '#e1e1e1', '--c-accent' ),
			'c_bg'          => array( __( 'پس‌زمینه', 'mde' ), '#faf8f3', '--c-bg' ),
			'c_surface'     => array( __( 'سطح/کارت', 'mde' ), '#ffffff', '--c-surface' ),
			'c_surface_2'   => array( __( 'سطح ثانویه', 'mde' ), '#f5f1e8', '--c-surface-2' ),
			'c_ink'         => array( __( 'متن اصلی', 'mde' ), '#1a1a1a', '--c-ink' ),
			'c_ink_2'       => array( __( 'متن ثانویه', 'mde' ), '#3a3a3a', '--c-ink-2' ),
			'c_muted'       => array( __( 'متن کم‌رنگ', 'mde' ), '#6b6b6b', '--c-muted' ),
			'c_border'      => array( __( 'خط/حاشیه', 'mde' ), '#e8e3d6', '--c-border' ),
		);

		foreach ( $colors as $key => $data ) {
			$w->add_control(
				$key,
				array(
					'label'     => $data[0],
					'type'      => Controls_Manager::COLOR,
					'default'   => $data[1],
					'selectors' => array(
						'{{WRAPPER}} .mde-scope' => $data[2] . ': {{VALUE}};',
					),
				)
			);
		}

		$w->end_controls_section();
	}

	/**
	 * Typography section. Applies font family/size/weight/etc. to the section
	 * heading and body text — fully editable, including the Persian webfont.
	 *
	 * @param \Elementor\Widget_Base $w Widget.
	 */
	protected function mde_typography_section( $w ) {
		$w->start_controls_section(
			'mde_typography',
			array(
				'label' => __( 'تایپوگرافی', 'mde' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$w->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'mde_title_typo',
				'label'    => __( 'تیتر بخش', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-section-head__title, {{WRAPPER}} .mde-hero-feat h1, {{WRAPPER}} .mde-card__title',
			)
		);

		$w->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'mde_body_typo',
				'label'    => __( 'متن', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-scope, {{WRAPPER}} .mde-card__excerpt, {{WRAPPER}} .mde-section-head__sub',
			)
		);

		$w->add_control(
			'mde_font_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'فونت پیش‌فرض «وزیرمتن» است؛ از اینجا می‌توانید هر فونت دیگری انتخاب کنید.', 'mde' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$w->end_controls_section();
	}

	/**
	 * Section spacing (top/bottom padding) — editable per section.
	 *
	 * @param \Elementor\Widget_Base $w Widget.
	 */
	protected function mde_spacing_section( $w ) {
		$w->start_controls_section(
			'mde_spacing',
			array(
				'label' => __( 'فاصله‌گذاری', 'mde' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$w->add_responsive_control(
			'mde_padding',
			array(
				'label'      => __( 'فاصله داخلی بخش', 'mde' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mde-scope' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$w->add_responsive_control(
			'mde_maxwidth',
			array(
				'label'      => __( 'حداکثر عرض محتوا', 'mde' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .mde-container' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$w->end_controls_section();
	}

	/**
	 * Section background — full Elementor background group (color, gradient,
	 * image) applied to the widget's outer scope. Lets the editor recolour the
	 * whole section, including widgets that previously hard-coded a tinted
	 * background (e.g. photo posts).
	 *
	 * @param \Elementor\Widget_Base $w Widget.
	 */
	protected function mde_background_section( $w ) {
		$w->start_controls_section(
			'mde_background',
			array(
				'label' => __( 'پس‌زمینه بخش', 'mde' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$w->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'mde_section_bg',
				'label'    => __( 'پس‌زمینه', 'mde' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .mde-scope',
			)
		);

		$w->end_controls_section();
	}

	/**
	 * Register all shared style sections at once.
	 *
	 * @param \Elementor\Widget_Base $w Widget.
	 */
	protected function mde_register_style_controls( $w ) {
		$this->mde_palette_section( $w );
		$this->mde_typography_section( $w );
		$this->mde_background_section( $w );
		$this->mde_spacing_section( $w );
	}
}
