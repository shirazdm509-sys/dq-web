<?php
/**
 * Live latest — sticky sidebar list of newest posts (dynamic from WordPress),
 * replaces the old live-chat panel.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Live_Latest extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-live-latest';
	}

	public function get_title() {
		return __( 'آخرین مطالب (ساید پخش زنده)', 'mde' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'آخرین مطالب', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جدیدترین جلسات و مقالات', 'mde' ) ) );
		$this->add_control( 'link_text', array( 'label' => __( 'متن «همه»', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'همه', 'mde' ) ) );
		$this->add_control( 'link_url', array( 'label' => __( 'پیوند «همه»', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'cat', array( 'label' => __( 'دسته', 'mde' ), 'type' => Controls_Manager::SELECT2, 'options' => MDE_Helpers::category_options() ) );
		$this->add_control( 'count', array( 'label' => __( 'تعداد', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 6 ) );
		$this->add_control( 'fallback', array( 'label' => __( 'تصویر پیش‌فرض', 'mde' ), 'type' => Controls_Manager::MEDIA ) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s  = $this->get_settings_for_display();
		$fb = ! empty( $s['fallback']['url'] ) ? $s['fallback']['url'] : '';
		$lu = ! empty( $s['link_url']['url'] ) ? $s['link_url']['url'] : '#';

		echo '<div class="mde-scope" dir="rtl"><aside class="mde-live-aside">';
		echo '<div class="mde-live-aside__head"><div><div style="font-weight:700;font-size:15px;">' . esc_html( $s['title'] ) . '</div><div style="font-size:11.5px;color:var(--c-muted);margin-top:2px;">' . esc_html( $s['sub'] ) . '</div></div>';
		echo '<a href="' . esc_url( $lu ) . '" style="font-size:12px;color:var(--c-primary);font-weight:600;display:inline-flex;align-items:center;gap:2px;">' . esc_html( $s['link_text'] ) . ' ' . MDE_Helpers::icon( 'chevl', 14 ) . '</a></div>'; // phpcs:ignore
		echo '<div>';
		$q = MDE_Helpers::query( array( 'category' => $s['cat'], 'count' => (int) $s['count'] ) );
		while ( $q->have_posts() ) {
			$q->the_post();
			$img = MDE_Helpers::thumb( get_the_ID(), $fb );
			echo '<a class="mde-la-item" href="' . esc_url( get_permalink() ) . '">';
			echo '<div class="mde-la-item__img"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" /></div>';
			echo '<div style="min-width:0;"><div style="font-size:13.5px;font-weight:700;line-height:1.45;color:var(--c-ink);margin-bottom:4px;">' . esc_html( wp_trim_words( get_the_title(), 6 ) ) . '</div><div style="font-size:11px;color:var(--c-muted);display:flex;gap:10px;"><span>' . MDE_Helpers::icon( 'clock', 10 ) . ' ' . esc_html( MDE_Helpers::fa( get_the_time( 'i:s' ) ) ) . '</span></div></div>'; // phpcs:ignore
			echo '<span style="font-size:10.5px;color:var(--c-muted-2);">' . esc_html( MDE_Helpers::date( get_the_ID() ) ) . '</span>';
			echo '</a>';
		}
		wp_reset_postdata();
		echo '</div></aside></div>';
	}
}
