<?php
/**
 * Stats / Vojuhat quick-links strip.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class MDE_Widget_Stats_Strip extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-stats-strip';
	}

	public function get_title() {
		return __( 'نوار آمار / وجوهات', 'mde' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'آیتم‌ها', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'icon', array(
			'label'   => __( 'آیکون', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'gift',
			'options' => array(
				'gift'   => __( 'هدیه', 'mde' ),
				'mosque' => __( 'مسجد', 'mde' ),
				'book'   => __( 'کتاب', 'mde' ),
				'doc'    => __( 'سند', 'mde' ),
				'heart'  => __( 'قلب', 'mde' ),
				'users'  => __( 'کاربران', 'mde' ),
			),
		) );
		$r->add_control( 'label', array( 'label' => __( 'برچسب', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'value', array( 'label' => __( 'مقدار', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'desc', array( 'label' => __( 'توضیح', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );

		$this->add_control( 'items', array(
			'label'       => __( 'آیتم‌ها', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => array(
				array( 'icon' => 'gift', 'label' => __( 'میزان پرداخت فطریه', 'mde' ), 'value' => '۱۴۰۵', 'desc' => __( 'هر نفر ۱۸٬۰۰۰ تومان', 'mde' ) ),
				array( 'icon' => 'mosque', 'label' => __( 'وجوهات شرعی', 'mde' ), 'value' => __( 'پرداخت آنلاین', 'mde' ), 'desc' => __( 'خمس، زکات، فطریه', 'mde' ) ),
				array( 'icon' => 'book', 'label' => __( 'فروشگاه کتاب', 'mde' ), 'value' => __( 'هادی', 'mde' ), 'desc' => __( 'آثار حضرت آیت‌الله', 'mde' ) ),
				array( 'icon' => 'doc', 'label' => __( 'ارسال سؤال شرعی', 'mde' ), 'value' => __( 'پاسخ تخصصی', 'mde' ), 'desc' => __( 'استفتائات', 'mde' ) ),
			),
			'title_field' => '{{{ label }}}',
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="mde-scope" dir="rtl" style="background:linear-gradient(180deg,transparent,var(--c-surface-2));"><div class="mde-container">';
		echo '<div class="mde-stats mde-reveal">';
		foreach ( (array) $s['items'] as $it ) {
			$u = ! empty( $it['url']['url'] ) ? $it['url']['url'] : '#';
			echo '<a class="mde-stat" href="' . esc_url( $u ) . '">';
			echo '<div class="mde-stat__ic">' . MDE_Helpers::icon( $it['icon'] ) . '</div>'; // phpcs:ignore
			echo '<div><div class="mde-stat__label">' . esc_html( $it['label'] ) . '</div><div class="mde-stat__value">' . esc_html( $it['value'] ) . '</div><div class="mde-stat__desc">' . esc_html( $it['desc'] ) . '</div></div>';
			echo '</a>';
		}
		echo '</div></div></div>';
	}
}
