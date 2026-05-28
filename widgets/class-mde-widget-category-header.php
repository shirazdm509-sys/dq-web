<?php
/**
 * Category header — breadcrumb, big title, description and two stat figures.
 * Pulls the current category on archives, or uses manual text elsewhere.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Category_Header extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-category-header';
	}

	public function get_title() {
		return __( 'سربرگ دسته‌بندی', 'mde' );
	}

	public function get_icon() {
		return 'eicon-archive-title';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'auto', array( 'label' => __( 'خواندن خودکار دسته جاری', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => __( 'در صفحات آرشیو، عنوان و توضیح از خود دسته خوانده می‌شود.', 'mde' ) ) );
		$this->add_control( 'show_breadcrumb', array(
			'label'   => __( 'نمایش مسیر راهنما', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_title', array(
			'label'   => __( 'نمایش عنوان', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'آرشیو تفسیر قرآن', 'mde' ), 'condition' => array( 'show_title' => 'yes' ) ) );
		$this->add_control( 'show_description', array(
			'label'       => __( 'نمایش توضیحات', 'mde' ),
			'description' => __( 'اگر در پایین صفحه از ویجت آرشیو دسته توضیحات را نشان می‌دهی، این را خاموش کن.', 'mde' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
		) );
		$this->add_control( 'desc', array( 'label' => __( 'توضیح', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'مجموعه‌ای از تفاسیر سوره‌های قرآن کریم توسط حضرت آیت‌الله سید علی‌محمد دستغیب «دامت برکاته».', 'mde' ), 'condition' => array( 'show_description' => 'yes' ) ) );
		$this->add_control( 'show_stats', array(
			'label'   => __( 'نمایش آمار', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'stat1_n', array( 'label' => __( 'عدد آمار ۱', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => '۷۳۳', 'condition' => array( 'show_stats' => 'yes' ) ) );
		$this->add_control( 'stat1_l', array( 'label' => __( 'برچسب آمار ۱', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جلسه', 'mde' ), 'condition' => array( 'show_stats' => 'yes' ) ) );
		$this->add_control( 'stat2_n', array( 'label' => __( 'عدد آمار ۲', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => '۱۲', 'condition' => array( 'show_stats' => 'yes' ) ) );
		$this->add_control( 'stat2_l', array( 'label' => __( 'برچسب آمار ۲', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'سوره', 'mde' ), 'condition' => array( 'show_stats' => 'yes' ) ) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$title = $s['title'];
		$desc  = $s['desc'];
		$cnt   = $s['stat1_n'];

		if ( 'yes' === $s['auto'] && is_category() ) {
			$obj   = get_queried_object();
			$title = single_cat_title( '', false );
			$d     = category_description();
			if ( $d ) {
				$desc = wp_strip_all_tags( $d );
			}
			if ( $obj && isset( $obj->count ) ) {
				$cnt = MDE_Helpers::fa( $obj->count );
			}
		}

		$show_breadcrumb  = ( 'yes' === ( isset( $s['show_breadcrumb'] ) ? $s['show_breadcrumb'] : 'yes' ) );
		$show_title       = ( 'yes' === ( isset( $s['show_title'] ) ? $s['show_title'] : 'yes' ) );
		$show_description = ( 'yes' === ( isset( $s['show_description'] ) ? $s['show_description'] : 'yes' ) );
		$show_stats       = ( 'yes' === ( isset( $s['show_stats'] ) ? $s['show_stats'] : 'yes' ) );

		echo '<div class="mde-scope mde-cat-hero mde-page-enter" dir="rtl"><div class="mde-container">';
		if ( $show_breadcrumb ) {
			echo '<nav class="mde-breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">' . MDE_Helpers::icon( 'home', 13 ) . esc_html__( 'خانه', 'mde' ) . '</a><span>/</span><span style="color:var(--c-ink-2);">' . esc_html( $title ) . '</span></nav>'; // phpcs:ignore
		}
		echo '<div class="mde-reveal"><div class="mde-cat-hero__grid">';
		echo '<div>';
		if ( $show_title ) {
			echo '<h1>' . esc_html( $title ) . '</h1>';
		}
		if ( $show_description && '' !== trim( (string) $desc ) ) {
			echo '<p>' . esc_html( $desc ) . '</p>';
		}
		echo '</div>';
		if ( $show_stats ) {
			echo '<div style="display:flex;gap:22px;align-items:baseline;">';
			echo '<div class="mde-cat-stat"><div class="n" style="color:var(--c-primary);">' . esc_html( $cnt ) . '</div><div class="l">' . esc_html( $s['stat1_l'] ) . '</div></div>';
			echo '<div class="mde-cat-stat"><div class="n" style="color:var(--c-accent);">' . esc_html( $s['stat2_n'] ) . '</div><div class="l">' . esc_html( $s['stat2_l'] ) . '</div></div>';
			echo '</div>';
		}
		echo '</div></div>';
		echo '</div></div>';
	}
}
