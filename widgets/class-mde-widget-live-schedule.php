<?php
/**
 * Live schedule — upcoming broadcasts list (editable repeater).
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class MDE_Widget_Live_Schedule extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-live-schedule';
	}

	public function get_title() {
		return __( 'برنامه پخش‌های زنده', 'mde' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'برنامه آینده پخش‌های زنده', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'day', array( 'label' => __( 'روز', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'date', array( 'label' => __( 'تاریخ', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'time', array( 'label' => __( 'ساعت', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'item_title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$r->add_control( 'now', array( 'label' => __( 'هم‌اکنون پخش؟', 'mde' ), 'type' => Controls_Manager::SWITCHER ) );
		$r->add_control( 'remind', array( 'label' => __( 'متن یادآوری', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'یادآوری', 'mde' ) ) );

		$this->add_control( 'items', array(
			'label'       => __( 'برنامه‌ها', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => array(
				array( 'day' => __( 'چهارشنبه', 'mde' ), 'date' => '۱۴۰۵/۲/۱۶', 'time' => '۲۰:۳۰', 'item_title' => __( 'تفسیر سوره قصص — جلسه ۳۶', 'mde' ), 'now' => 'yes' ),
				array( 'day' => __( 'پنج‌شنبه', 'mde' ), 'date' => '۱۴۰۵/۲/۱۷', 'time' => '۲۲:۰۰', 'item_title' => __( 'دعای کمیل و سخنرانی شب جمعه', 'mde' ) ),
				array( 'day' => __( 'چهارشنبه', 'mde' ), 'date' => '۱۴۰۵/۲/۲۳', 'time' => '۲۰:۳۰', 'item_title' => __( 'تفسیر سوره قصص — جلسه ۳۷', 'mde' ) ),
				array( 'day' => __( 'پنج‌شنبه', 'mde' ), 'date' => '۱۴۰۵/۲/۲۴', 'time' => '۲۲:۰۰', 'item_title' => __( 'دعای کمیل و سخنرانی شب جمعه', 'mde' ) ),
			),
			'title_field' => '{{{ item_title }}}',
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="mde-scope" dir="rtl"><div class="mde-container"><section class="mde-reveal" style="margin-top:22px;">';
		echo '<div class="mde-section-head" style="margin-bottom:18px;"><h2 class="mde-section-head__title" style="font-size:22px;"><span class="mde-eyebrow-mark"></span>' . esc_html( $s['title'] ) . '</h2></div>';
		echo '<div style="display:flex;flex-direction:column;gap:10px;">';
		foreach ( (array) $s['items'] as $it ) {
			$now = ( 'yes' === $it['now'] );
			echo '<div class="mde-sched-item ' . ( $now ? 'now' : '' ) . '">';
			echo '<div><div style="font-size:13px;font-weight:700;color:' . ( $now ? 'var(--c-primary)' : 'var(--c-ink)' ) . ';">' . esc_html( $it['day'] ) . '</div><div style="font-size:11.5px;color:var(--c-muted);">' . esc_html( $it['date'] ) . '</div></div>';
			echo '<div class="mde-sched-item__time">' . esc_html( $it['time'] ) . '</div>';
			echo '<div style="font-size:14.5px;font-weight:600;">' . esc_html( $it['item_title'] ) . '</div>';
			if ( $now ) {
				echo '<span class="mde-sched-now"><span class="dot"></span>' . esc_html__( 'الان', 'mde' ) . '</span>';
			} else {
				echo '<button class="mde-chip">' . esc_html( $it['remind'] ) . '</button>';
			}
			echo '</div>';
		}
		echo '</div></section></div></div>';
	}
}
