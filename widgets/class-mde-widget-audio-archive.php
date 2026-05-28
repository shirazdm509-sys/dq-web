<?php
/**
 * Audio archive — collection cards built from the site's own categories.
 * The site admin picks one or more categories; each becomes a card whose
 * post count drives the file counter.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class MDE_Widget_Audio_Archive extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-audio-archive';
	}

	public function get_title() {
		return __( 'آرشیو صوت', 'mde' );
	}

	public function get_icon() {
		return 'eicon-headphones';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان بخش', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'آرشیو صوت', 'mde' ) ) );
		$this->add_control( 'sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مجموعه‌های صوتی به تفکیک ماه و سوره', 'mde' ) ) );
		$this->add_control( 'link_text', array( 'label' => __( 'متن لینک', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'همه آرشیو', 'mde' ) ) );
		$this->add_control( 'link_url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );

		$this->add_control( 'cats', array(
			'label'       => __( 'دسته‌بندی‌ها', 'mde' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => MDE_Helpers::category_options(),
			'multiple'    => true,
			'label_block' => true,
			'description' => __( 'یک یا چند دسته‌بندی از وردپرس را انتخاب کنید؛ هر دسته یک کارت می‌شود.', 'mde' ),
		) );

		$this->add_control( 'cols', array(
			'label'   => __( 'ستون‌ها', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '4',
			'options' => array( '2' => '۲', '3' => '۳', '4' => '۴' ),
		) );

		$this->add_control( 'default_color', array(
			'label'   => __( 'رنگ کاور پیش‌فرض', 'mde' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#0f766e',
		) );

		// Optional per-category cover color overrides.
		$r = new Repeater();
		$r->add_control( 'cat', array(
			'label'   => __( 'دسته', 'mde' ),
			'type'    => Controls_Manager::SELECT2,
			'options' => MDE_Helpers::category_options(),
		) );
		$r->add_control( 'color', array( 'label' => __( 'رنگ کاور', 'mde' ), 'type' => Controls_Manager::COLOR, 'default' => '#0f766e' ) );
		$r->add_control( 'label', array( 'label' => __( 'برچسب جایگزین', 'mde' ), 'type' => Controls_Manager::TEXT ) );

		$this->add_control( 'overrides', array(
			'label'       => __( 'تنظیمات اضافی هر دسته', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'title_field' => '{{{ label || "دسته" }}}',
		) );

		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	/**
	 * Build a fast lookup of category-id => override row.
	 *
	 * @param array $rows Repeater rows.
	 * @return array<int,array>
	 */
	private function override_map( $rows ) {
		$out = array();
		foreach ( (array) $rows as $row ) {
			$id = isset( $row['cat'] ) ? absint( $row['cat'] ) : 0;
			if ( $id ) {
				$out[ $id ] = $row;
			}
		}
		return $out;
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$cat_ids  = MDE_Helpers::normalize_cat_ids( isset( $s['cats'] ) ? $s['cats'] : '' );
		$cols     = isset( $s['cols'] ) ? (int) $s['cols'] : 4;
		$default  = ! empty( $s['default_color'] ) ? $s['default_color'] : '#0f766e';
		$overrides = $this->override_map( isset( $s['overrides'] ) ? $s['overrides'] : array() );

		if ( empty( $cat_ids ) ) {
			$cats = get_categories( array( 'hide_empty' => false, 'number' => 8 ) );
		} else {
			$cats = get_categories( array( 'include' => $cat_ids, 'hide_empty' => false, 'orderby' => 'include' ) );
		}

		echo '<div class="mde-scope mde-section" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-reveal">';
		$this->section_head( array(
			'title'     => $s['title'],
			'sub'       => $s['sub'],
			'link_text' => $s['link_text'],
			'link_url'  => ! empty( $s['link_url']['url'] ) ? $s['link_url']['url'] : '',
		) );
		echo '</div>';

		echo '<div class="mde-grid mde-grid--' . esc_attr( $cols ) . '">';
		$i = 0;
		foreach ( $cats as $cat ) {
			$row   = isset( $overrides[ $cat->term_id ] ) ? $overrides[ $cat->term_id ] : array();
			$color = ! empty( $row['color'] ) ? $row['color'] : $default;
			$label = ! empty( $row['label'] ) ? $row['label'] : $cat->name;
			$url   = get_category_link( $cat->term_id );
			$count = MDE_Helpers::fa( number_format_i18n( $cat->count ) );

			echo '<div class="mde-reveal" data-delay="' . esc_attr( $i * 70 ) . '"><a class="mde-audio-card" href="' . esc_url( $url ) . '">';
			echo '<div class="mde-audio-card__cover" style="background:linear-gradient(180deg,' . esc_attr( $color ) . ' 0%,' . esc_attr( $color ) . '99 100%);">';
			echo '<div class="mde-audio-card__label">' . esc_html( $label ) . '</div>';
			echo '<div class="mde-audio-card__year">' . esc_html( $count ) . '</div>';
			if ( ! empty( $cat->description ) ) {
				echo '<div style="font-size:11px;opacity:.65;margin-top:10px;max-width:80%;">' . esc_html( wp_trim_words( $cat->description, 10 ) ) . '</div>';
			}
			echo '</div>';
			echo '<div class="mde-audio-card__foot"><h3>' . esc_html( $cat->name ) . '</h3><span>' . MDE_Helpers::icon( 'phones', 12 ) . ' ' . esc_html( $count ) . '</span></div>'; // phpcs:ignore
			echo '</a></div>';
			$i++;
		}
		echo '</div></div></div>';
	}
}
