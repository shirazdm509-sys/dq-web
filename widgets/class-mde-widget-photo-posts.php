<?php
/**
 * Photo posts (عکس‌نوشته) — dynamic posts rendered as quote cards with the
 * post title/excerpt overlaid on the featured image, hover share/download.
 * Cards live inside a vertical-scroll viewport with an optional "view all" link.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Photo_Posts extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-photo-posts';
	}

	public function get_title() {
		return __( 'عکس‌نوشته‌ها', 'mde' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان بخش', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'عکس‌نوشته‌ها', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'گزیده‌ای از سخنان و بیانات حضرت آیت‌الله', 'mde' ) ) );
		$this->add_control( 'link_text', array( 'label' => __( 'متن «مشاهده همه»', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مشاهده همه', 'mde' ) ) );
		$this->add_control( 'link_url', array( 'label' => __( 'پیوند «مشاهده همه»', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'cat', array(
			'label'       => __( 'دسته‌ها', 'mde' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => MDE_Helpers::category_options(),
			'multiple'    => true,
			'label_block' => true,
		) );
		$this->add_control( 'count', array( 'label' => __( 'تعداد', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 8 ) );
		$this->add_responsive_control( 'cols', array(
			'label'      => __( 'تعداد کارت قابل مشاهده (هم‌زمان)', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => array( 'px' => array( 'min' => 1, 'max' => 6, 'step' => 1 ) ),
			'default'    => array( 'size' => 4, 'unit' => 'px' ),
			'tablet_default' => array( 'size' => 2, 'unit' => 'px' ),
			'mobile_default' => array( 'size' => 1.2, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-hscroll' => '--mde-visible: {{SIZE}};',
			),
		) );
		$this->add_control( 'src_label', array( 'label' => __( 'برچسب منبع پیش‌فرض', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'بیانات آیت‌الله دستغیب', 'mde' ) ) );
		$this->add_control( 'fallback', array( 'label' => __( 'تصویر پیش‌فرض', 'mde' ), 'type' => Controls_Manager::MEDIA ) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s  = $this->get_settings_for_display();
		$fb = ! empty( $s['fallback']['url'] ) ? $s['fallback']['url'] : '';
		echo '<div class="mde-scope mde-section" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal">';
		$this->section_head( array(
			'title'     => $s['title'],
			'sub'       => $s['sub'],
			'link_text' => $s['link_text'],
			'link_url'  => ! empty( $s['link_url']['url'] ) ? $s['link_url']['url'] : '',
		) );
		echo '</div>';
		echo '<div class="mde-hscroll mde-hscroll--photos">';
		$q = MDE_Helpers::query( array( 'category' => $s['cat'], 'count' => (int) $s['count'] ) );
		$i = 0;
		while ( $q->have_posts() ) {
			$q->the_post();
			$img = MDE_Helpers::thumb( get_the_ID(), $fb );
			echo '<div class="mde-reveal mde-hscroll__item" data-delay="' . esc_attr( $i * 70 ) . '"><article class="mde-photo-card" onclick="window.location=\'' . esc_url( get_permalink() ) . '\'">';
			echo '<div class="mde-photo-card__media"><img src="' . esc_url( $img ) . '" alt="' . esc_attr( get_the_title() ) . '" loading="lazy" />';
			echo '<div class="mde-photo-card__actions"><button>' . MDE_Helpers::icon( 'share', 14 ) . '</button><button>' . MDE_Helpers::icon( 'download', 14 ) . '</button></div>'; // phpcs:ignore
			echo '<div class="mde-photo-card__quote"><p>' . esc_html( wp_trim_words( get_the_title(), 14 ) ) . '</p><div class="mde-photo-card__src">' . esc_html( $s['src_label'] ) . '</div></div>';
			echo '</div>';
			echo '<div class="mde-photo-card__foot"><span>' . esc_html( MDE_Helpers::date( get_the_ID() ) ) . '</span><span>' . MDE_Helpers::icon( 'eye', 12 ) . ' ' . esc_html( MDE_Helpers::fa( number_format_i18n( class_exists( 'MDE_Views' ) ? MDE_Views::get_post_views( get_the_ID() ) : (int) get_post_meta( get_the_ID(), 'mde_views', true ) ) ) ) . '</span></div>'; // phpcs:ignore
			echo '</article></div>';
			$i++;
		}
		wp_reset_postdata();
		echo '</div></div></div>';
	}
}
