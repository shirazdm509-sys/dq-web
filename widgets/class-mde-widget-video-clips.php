<?php
/**
 * Video clips strip — vertical clip cards (dynamic posts), play overlay,
 * scrollable viewport + "view all" link.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Video_Clips extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-video-clips';
	}

	public function get_title() {
		return __( 'ویدئو کلیپ‌های کوتاه', 'mde' );
	}

	public function get_icon() {
		return 'eicon-video-playlist';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان بخش', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'ویدئو کلیپ‌های کوتاه', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'گزیده‌ای از سخنرانی‌های اخیر', 'mde' ) ) );
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
		echo '<div class="mde-hscroll mde-hscroll--clips">';
		$q = MDE_Helpers::query( array( 'category' => $s['cat'], 'count' => (int) $s['count'] ) );
		$i = 0;
		while ( $q->have_posts() ) {
			$q->the_post();
			$img = MDE_Helpers::thumb( get_the_ID(), $fb );
			echo '<div class="mde-reveal mde-hscroll__item" data-delay="' . esc_attr( $i * 70 ) . '"><div class="mde-clip" onclick="window.location=\'' . esc_url( get_permalink() ) . '\'">';
			echo '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( get_the_title() ) . '" loading="lazy" />';
			echo '<div class="mde-clip__grad"><span class="mde-clip__dur">' . esc_html( MDE_Helpers::fa( get_the_time( 'i:s' ) ) ) . '</span>';
			echo '<div class="mde-clip__title"><h3>' . esc_html( wp_trim_words( get_the_title(), 8 ) ) . '</h3><div class="v">' . MDE_Helpers::icon( 'eye', 12 ) . ' ' . esc_html( MDE_Helpers::fa( number_format_i18n( class_exists( 'MDE_Views' ) ? MDE_Views::get_post_views( get_the_ID() ) : (int) get_post_meta( get_the_ID(), 'mde_views', true ) ) ) ) . '</div></div>'; // phpcs:ignore
			echo '</div><div class="mde-clip__play">' . MDE_Helpers::icon( 'play', 22 ) . '</div>'; // phpcs:ignore
			echo '</div></div>';
			$i++;
		}
		wp_reset_postdata();
		echo '</div></div></div>';
	}
}
