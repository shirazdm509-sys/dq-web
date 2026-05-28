<?php
/**
 * Martyrs (رفیق شفیق) — dynamic posts as memorial cards.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

class MDE_Widget_Martyrs extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-martyrs';
	}

	public function get_title() {
		return __( 'رفیق شفیق (شهدا)', 'mde' );
	}

	public function get_icon() {
		return 'eicon-favorite';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان بخش', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'رفیق شفیق — یادنامه شهدا', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'گرامی‌داشت یاد شهدای راه حق', 'mde' ) ) );
		$this->add_control( 'link_text', array( 'label' => __( 'متن لینک', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'همه شهدا', 'mde' ) ) );
		$this->add_control( 'link_url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'cat', array( 'label' => __( 'دسته', 'mde' ), 'type' => Controls_Manager::SELECT2, 'options' => MDE_Helpers::category_options() ) );
		$this->add_control( 'count', array( 'label' => __( 'تعداد', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 4 ) );
		$this->add_control( 'more_text', array( 'label' => __( 'متن «بیشتر بخوانید»', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'بیشتر بخوانید', 'mde' ) ) );
		$this->add_control( 'fallback', array( 'label' => __( 'تصویر پیش‌فرض', 'mde' ), 'type' => Controls_Manager::MEDIA ) );
		$this->end_controls_section();

		// Per-card visual controls.
		$this->start_controls_section( 'sec_cards', array(
			'label' => __( 'استایل کارت‌ها', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'card_bg', array(
			'label'     => __( 'پس‌زمینه کارت', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-martyr' => 'background: {{VALUE}};',
			),
		) );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'card_media_bg',
				'label'    => __( 'پس‌زمینه ناحیه تصویر کارت', 'mde' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .mde-martyr__media',
			)
		);

		$this->add_control( 'card_text', array(
			'label'     => __( 'رنگ متن کارت', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-martyr__body h3' => 'color: {{VALUE}};',
				'{{WRAPPER}} .mde-martyr__body p'  => 'color: {{VALUE}}; opacity: .75;',
			),
		) );

		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s  = $this->get_settings_for_display();
		$fb = ! empty( $s['fallback']['url'] ) ? $s['fallback']['url'] : '';
		echo '<div class="mde-scope mde-section mde-martyrs-section" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal">';
		$this->section_head( array(
			'title'     => $s['title'],
			'sub'       => $s['sub'],
			'link_text' => $s['link_text'],
			'link_url'  => ! empty( $s['link_url']['url'] ) ? $s['link_url']['url'] : '',
		) );
		echo '</div>';
		echo '<div class="mde-grid mde-grid--4">';
		$q = MDE_Helpers::query( array( 'category' => $s['cat'], 'count' => (int) $s['count'] ) );
		$i = 0;
		while ( $q->have_posts() ) {
			$q->the_post();
			$img = MDE_Helpers::thumb( get_the_ID(), $fb );
			echo '<div class="mde-reveal" data-delay="' . esc_attr( $i * 80 ) . '"><article class="mde-martyr" onclick="window.location=\'' . esc_url( get_permalink() ) . '\'">';
			echo '<div class="mde-martyr__media"><img src="' . esc_url( $img ) . '" alt="' . esc_attr( get_the_title() ) . '" loading="lazy" /><span class="mde-martyr__year">' . esc_html__( 'شهید', 'mde' ) . ' · ' . esc_html( MDE_Helpers::fa( get_the_date( 'Y' ) ) ) . '</span></div>';
			echo '<div class="mde-martyr__body"><h3>' . esc_html( get_the_title() ) . '</h3><p>' . esc_html( wp_trim_words( get_the_excerpt(), 16 ) ) . '</p>';
			echo '<a class="mde-more" href="' . esc_url( get_permalink() ) . '">' . esc_html( $s['more_text'] ) . ' ' . MDE_Helpers::icon( 'chevl', 14 ) . '</a>'; // phpcs:ignore
			echo '</div></article></div>';
			$i++;
		}
		wp_reset_postdata();
		echo '</div></div></div>';
	}
}
