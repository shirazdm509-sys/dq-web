<?php
/**
 * Posts grid — the "آخرین جلسات تفسیر قرآن" section. Reads posts from
 * WordPress / a chosen category. Optional filter chips from categories.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Posts_Grid extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-posts-grid';
	}

	public function get_title() {
		return __( 'گرید مقالات/تفسیر', 'mde' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان بخش', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'آخرین جلسات تفسیر قرآن', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'حضرت آیت‌الله سید علی‌محمد دستغیب — تفسیر سوره قصص', 'mde' ) ) );
		$this->add_control( 'link_text', array( 'label' => __( 'متن لینک', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مشاهده آرشیو کامل', 'mde' ) ) );
		$this->add_control( 'link_url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'cat', array(
			'label'       => __( 'دسته‌ها', 'mde' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => MDE_Helpers::category_options(),
			'multiple'    => true,
			'label_block' => true,
			'description' => __( 'یک یا چند دسته را انتخاب کنید؛ خالی = همه دسته‌ها.', 'mde' ),
		) );
		$this->add_control( 'count', array( 'label' => __( 'تعداد', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 6 ) );
		$this->add_control( 'cols', array(
			'label'   => __( 'ستون‌ها', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '3',
			'options' => array( '2' => '۲', '3' => '۳', '4' => '۴' ),
		) );
		$this->add_control( 'variant', array(
			'label'   => __( 'سبک کارت', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'default',
			'options' => array( 'default' => __( 'استاندارد', 'mde' ), 'minimal' => __( 'مینیمال', 'mde' ), 'elevated' => __( 'برجسته', 'mde' ) ),
		) );
		$this->add_control( 'badge', array( 'label' => __( 'برچسب کارت', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'ویدئو', 'mde' ) ) );
		$this->add_control( 'show_chips', array( 'label' => __( 'نمایش فیلتر دسته‌ها', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
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

		$selected_ids = MDE_Helpers::normalize_cat_ids( isset( $s['cat'] ) ? $s['cat'] : '' );
		$cats_csv     = implode( ',', $selected_ids );

		if ( 'yes' === $s['show_chips'] ) {
			if ( ! empty( $selected_ids ) ) {
				$chip_cats = get_categories( array( 'include' => $selected_ids, 'hide_empty' => false, 'orderby' => 'include' ) );
			} else {
				$chip_cats = get_categories( array( 'hide_empty' => true, 'number' => 7 ) );
			}
			echo '<div class="mde-reveal mde-pg-chips" style="margin-bottom:24px;"';
			echo ' data-mde-ajax="' . esc_attr( admin_url( 'admin-ajax.php' ) ) . '"';
			echo ' data-mde-nonce="' . esc_attr( wp_create_nonce( 'mde_pg_filter' ) ) . '"';
			echo '>';
			echo '<button type="button" class="mde-chip is-active" data-mde-pg-cat="0">' . esc_html__( 'همه', 'mde' ) . '</button>';
			foreach ( $chip_cats as $c ) {
				echo '<button type="button" class="mde-chip" data-mde-pg-cat="' . esc_attr( $c->term_id ) . '">' . esc_html( $c->name ) . '</button>';
			}
			echo '</div>';
		}

		echo '<div class="mde-grid mde-grid--' . esc_attr( $s['cols'] ) . ' mde-pg-grid"';
		echo ' data-mde-pg-cats="' . esc_attr( $cats_csv ) . '"';
		echo ' data-mde-pg-count="' . esc_attr( (int) $s['count'] ) . '"';
		echo ' data-mde-pg-variant="' . esc_attr( $s['variant'] ) . '"';
		echo ' data-mde-pg-badge="' . esc_attr( $s['badge'] ) . '"';
		echo ' data-mde-pg-fallback="' . esc_attr( $fb ) . '"';
		echo '>';
		$q = MDE_Helpers::query( array( 'category' => $s['cat'], 'count' => (int) $s['count'] ) );
		$i = 0;
		while ( $q->have_posts() ) {
			$q->the_post();
			echo '<div class="mde-reveal" data-delay="' . esc_attr( $i * 60 ) . '">';
			MDE_Helpers::card( array(
				'id'       => get_the_ID(),
				'variant'  => $s['variant'],
				'badge'    => $s['badge'],
				'fallback' => $fb,
			) );
			echo '</div>';
			$i++;
		}
		wp_reset_postdata();
		echo '</div></div></div>';
	}
}
