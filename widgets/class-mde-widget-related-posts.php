<?php
/**
 * Related posts — posts from the current post's category (or a chosen one).
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Related_Posts extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-related-posts';
	}

	public function get_title() {
		return __( 'مطالب مرتبط', 'mde' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مطالب مرتبط', 'mde' ) ) );
		$this->add_control( 'cat', array( 'label' => __( 'دسته (اختیاری)', 'mde' ), 'type' => Controls_Manager::SELECT2, 'options' => MDE_Helpers::category_options(), 'description' => __( 'خالی = دسته‌ی نوشته‌ی جاری', 'mde' ) ) );
		$this->add_control( 'count', array( 'label' => __( 'تعداد', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 3 ) );
		$this->add_control( 'badge', array( 'label' => __( 'برچسب کارت', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'ویدئو', 'mde' ) ) );
		$this->add_control( 'fallback', array( 'label' => __( 'تصویر پیش‌فرض', 'mde' ), 'type' => Controls_Manager::MEDIA ) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$cat = $s['cat'];
		if ( empty( $cat ) && is_singular() ) {
			$cs = get_the_category( get_the_ID() );
			if ( ! empty( $cs ) ) {
				$cat = $cs[0]->term_id;
			}
		}
		$fb = ! empty( $s['fallback']['url'] ) ? $s['fallback']['url'] : '';
		echo '<div class="mde-scope mde-section" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal">';
		$this->section_head( array( 'title' => $s['title'] ) );
		echo '</div>';
		echo '<div class="mde-grid mde-grid--3">';
		$q = MDE_Helpers::query( array(
			'category' => $cat,
			'count'    => (int) $s['count'] + 1,
		) );
		$cur = get_the_ID();
		$n   = 0;
		while ( $q->have_posts() && $n < (int) $s['count'] ) {
			$q->the_post();
			if ( get_the_ID() === $cur ) {
				continue;
			}
			echo '<div class="mde-reveal">';
			MDE_Helpers::card( array(
				'id'           => get_the_ID(),
				'badge'        => $s['badge'],
				'fallback'     => $fb,
				'show_excerpt' => false,
			) );
			echo '</div>';
			$n++;
		}
		wp_reset_postdata();
		echo '</div></div></div>';
	}
}
