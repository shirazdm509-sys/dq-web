<?php
/**
 * Header widget — logo + WordPress nav menu + live pill + search overlay.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MDE_Widget_Header extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-header';
	}

	public function get_title() {
		return __( 'هدر (منو سایت)', 'mde' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'sec_logo',
			array( 'label' => __( 'لوگو', 'mde' ) )
		);
		$this->add_control(
			'logo',
			array(
				'label'   => __( 'تصویر لوگو', 'mde' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => MDE_ASSETS . 'images/logo.jpg' ),
			)
		);
		$this->add_responsive_control(
			'logo_h',
			array(
				'label'     => __( 'ارتفاع لوگو', 'mde' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
				'default'   => array( 'size' => 56 ),
				'selectors' => array( '{{WRAPPER}} .mde-brand__logo' => 'height: {{SIZE}}px;' ),
			)
		);
		$this->add_control(
			'logo_link',
			array(
				'label'   => __( 'پیوند لوگو', 'mde' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => home_url( '/' ) ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'sec_menu',
			array( 'label' => __( 'منو', 'mde' ) )
		);
		$this->add_control(
			'menu',
			array(
				'label'       => __( 'منوی وردپرس', 'mde' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => MDE_Helpers::menu_options(),
				'description' => __( 'منو از «نمایش ← فهرست‌ها»ی وردپرس خوانده می‌شود.', 'mde' ),
			)
		);

		$this->add_responsive_control(
			'menu_font_size',
			array(
				'label'      => __( 'اندازه فونت منو', 'mde' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 10, 'max' => 28 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .mde-nav a, {{WRAPPER}} .mde-drawer a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => __( 'رنگ آیتم منو', 'mde' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mde-nav a, {{WRAPPER}} .mde-drawer a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_color_hover',
			array(
				'label'     => __( 'رنگ آیتم منو هنگام شناور', 'mde' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mde-nav a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_typo',
				'label'    => __( 'تایپوگرافی منو', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-nav a, {{WRAPPER}} .mde-drawer a',
			)
		);

		$this->add_control(
			'drawer_bg',
			array(
				'label'     => __( 'پس‌زمینه منوی موبایل', 'mde' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#faf8f3',
				'selectors' => array(
					'{{WRAPPER}} .mde-drawer__panel' => 'background-color: {{VALUE}} !important; background-image: none;',
				),
			)
		);

		$this->add_control(
			'drawer_text_color',
			array(
				'label'     => __( 'رنگ متن منوی موبایل', 'mde' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mde-drawer__panel, {{WRAPPER}} .mde-drawer a, {{WRAPPER}} .mde-drawer strong' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'sec_actions',
			array( 'label' => __( 'دکمه‌های هدر', 'mde' ) )
		);
		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'نمایش جستجو', 'mde' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_live',
			array(
				'label'   => __( 'نمایش دکمه پخش زنده', 'mde' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'live_text',
			array(
				'label'     => __( 'متن دکمه پخش زنده', 'mde' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'پخش زنده', 'mde' ),
				'condition' => array( 'show_live' => 'yes' ),
			)
		);
		$this->add_control(
			'live_url',
			array(
				'label'     => __( 'پیوند پخش زنده', 'mde' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_live' => 'yes' ),
			)
		);
		$this->add_control(
			'search_ph',
			array(
				'label'   => __( 'متن راهنمای جستجو', 'mde' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'جستجو در مطالب، سخنرانی‌ها، تفاسیر…', 'mde' ),
			)
		);

		$this->add_responsive_control(
			'icon_btn_size',
			array(
				'label'      => __( 'اندازه دکمه‌های جستجو/منو', 'mde' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 28, 'max' => 80 ),
				),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .mde-header-actions .mde-icon-btn' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_btn_icon_size',
			array(
				'label'      => __( 'اندازه آیکن دکمه‌های جستجو/منو', 'mde' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 12, 'max' => 48 ),
				),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .mde-header-actions .mde-icon-btn svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'icon_btn_color',
			array(
				'label'     => __( 'رنگ آیکن دکمه‌های جستجو/منو', 'mde' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mde-header-actions .mde-icon-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_btn_bg',
			array(
				'label'     => __( 'پس‌زمینه دکمه‌های جستجو/منو', 'mde' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mde-header-actions .mde-icon-btn' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$logo = ! empty( $s['logo']['url'] ) ? $s['logo']['url'] : MDE_ASSETS . 'images/logo.jpg';
		$llink = ! empty( $s['logo_link']['url'] ) ? $s['logo_link']['url'] : home_url( '/' );

		echo '<div class="mde-scope mde-header" dir="rtl">';
		echo '<div class="mde-container mde-header__inner">';

		echo '<a class="mde-brand" href="' . esc_url( $llink ) . '">';
		echo '<img class="mde-brand__logo" src="' . esc_url( $logo ) . '" alt="' . esc_attr__( 'مرکز نشر دستغیب', 'mde' ) . '" />';
		echo '</a>';

		echo '<nav class="mde-nav">';
		if ( ! empty( $s['menu'] ) ) {
			wp_nav_menu(
				array(
					'menu'        => (int) $s['menu'],
					'container'   => false,
					'menu_class'  => 'mde-nav__list',
					'fallback_cb' => '__return_false',
					'depth'       => 0,
				)
			);
		} else {
			echo '<ul class="mde-nav__list"><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'یک منو در وردپرس بسازید و انتخاب کنید', 'mde' ) . '</a></li></ul>';
		}
		echo '</nav>';

		echo '<div class="mde-header-actions">';
		if ( 'yes' === $s['show_live'] ) {
			$lu = ! empty( $s['live_url']['url'] ) ? $s['live_url']['url'] : '#';
			echo '<a class="mde-live-pill" href="' . esc_url( $lu ) . '"><span class="dot"></span><span>' . esc_html( $s['live_text'] ) . '</span></a>';
		}
		if ( 'yes' === $s['show_search'] ) {
			echo '<button class="mde-icon-btn" data-mde-search aria-label="' . esc_attr__( 'جستجو', 'mde' ) . '">' . MDE_Helpers::icon( 'search' ) . '</button>'; // phpcs:ignore
		}
		echo '<button class="mde-icon-btn mde-hamburger" aria-label="' . esc_attr__( 'منو', 'mde' ) . '">' . MDE_Helpers::icon( 'menu' ) . '</button>'; // phpcs:ignore
		echo '</div>';

		echo '</div>'; // inner.

		// Mobile drawer.
		echo '<div class="mde-drawer"><aside class="mde-drawer__panel">';
		echo '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;"><strong>' . esc_html__( 'منو', 'mde' ) . '</strong><button class="mde-icon-btn" data-mde-close>' . MDE_Helpers::icon( 'close' ) . '</button></div>'; // phpcs:ignore
		if ( ! empty( $s['menu'] ) ) {
			wp_nav_menu(
				array(
					'menu'        => (int) $s['menu'],
					'container'   => false,
					'menu_class'  => 'mde-drawer__list',
					'fallback_cb' => '__return_false',
					'depth'       => 0,
				)
			);
		}
		echo '</aside></div>';

		// Search overlay.
		if ( 'yes' === $s['show_search'] ) {
			echo '<div class="mde-search-overlay"><div class="mde-search-box">';
			echo '<form role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '">';
			echo MDE_Helpers::icon( 'search', 20 ); // phpcs:ignore
			echo '<input type="search" name="s" placeholder="' . esc_attr( $s['search_ph'] ) . '" />';
			echo '<span class="mde-chip">Esc</span>';
			echo '</form></div></div>';
		}

		echo '</div>'; // scope.
	}
}
