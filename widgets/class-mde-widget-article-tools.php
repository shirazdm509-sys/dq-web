<?php
/**
 * Article tools sidebar — bookmark / reader mode / font size, table of
 * contents, and a "next session" promo card.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class MDE_Widget_Article_Tools extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-article-tools';
	}

	public function get_title() {
		return __( 'ابزارهای مطالعه (ساید تک‌مقاله)', 'mde' );
	}

	public function get_icon() {
		return 'eicon-sidebar';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'tools_label', array( 'label' => __( 'عنوان ابزارها', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'ابزارهای مطالعه', 'mde' ) ) );
		$this->add_control( 'toc_label', array( 'label' => __( 'عنوان فهرست', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'فهرست مطالب', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'text', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT ) );
		$this->add_control( 'toc', array(
			'label'       => __( 'آیتم‌های فهرست', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => array(
				array( 'text' => __( 'آیه شریفه', 'mde' ) ),
				array( 'text' => __( 'تفسیر آیه', 'mde' ) ),
				array( 'text' => __( 'عاقبت سرکشان', 'mde' ) ),
				array( 'text' => __( 'نتیجه‌گیری', 'mde' ) ),
			),
			'title_field' => '{{{ text }}}',
		) );

		$this->add_control( 'next_eyebrow', array( 'label' => __( 'پیش‌عنوان جلسه بعدی', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جلسه بعدی', 'mde' ) ) );
		$this->add_control( 'next_title', array( 'label' => __( 'عنوان جلسه بعدی', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'سوره قصص — آیه ۷۹', 'mde' ) ) );
		$this->add_control( 'next_meta', array( 'label' => __( 'توضیح جلسه بعدی', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'به‌زودی منتشر می‌شود', 'mde' ) ) );
		$this->add_control( 'next_url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="mde-scope" dir="rtl"><aside class="mde-article-sidebar" style="display:flex;flex-direction:column;gap:20px;">';

		// Tools.
		echo '<div class="mde-tools"><div class="mde-tools__label">' . esc_html( $s['tools_label'] ) . '</div>';
		echo '<div class="mde-tools__btns">';
		echo '<button class="mde-icon-btn" data-mde-bookmark title="' . esc_attr__( 'ذخیره', 'mde' ) . '">' . MDE_Helpers::icon( 'bookmark' ) . '</button>'; // phpcs:ignore
		echo '<button class="mde-icon-btn" data-mde-reader title="' . esc_attr__( 'حالت مطالعه', 'mde' ) . '">' . MDE_Helpers::icon( 'book' ) . '</button>'; // phpcs:ignore
		echo '<button class="mde-icon-btn" title="' . esc_attr__( 'اشتراک', 'mde' ) . '">' . MDE_Helpers::icon( 'share' ) . '</button>'; // phpcs:ignore
		echo '<button class="mde-icon-btn" title="' . esc_attr__( 'دانلود', 'mde' ) . '">' . MDE_Helpers::icon( 'download' ) . '</button>'; // phpcs:ignore
		echo '</div>';
		echo '<div style="margin-top:14px;padding-top:14px;border-top:1px dashed var(--c-border);">';
		echo '<div style="font-size:12px;color:var(--c-muted);font-weight:600;margin-bottom:10px;display:flex;justify-content:space-between;"><span>' . esc_html__( 'اندازه متن', 'mde' ) . '</span><span data-mde-fontval>۱۷</span></div>';
		echo '<div style="display:flex;gap:6px;">';
		echo '<button class="mde-btn mde-btn--ghost" style="flex:1;height:36px;" data-mde-font="dec">−</button>';
		echo '<button class="mde-btn mde-btn--ghost" style="flex:1;height:36px;" data-mde-font="def">' . esc_html__( 'پیش‌فرض', 'mde' ) . '</button>';
		echo '<button class="mde-btn mde-btn--ghost" style="flex:1;height:36px;" data-mde-font="inc">+</button>';
		echo '</div></div></div>';

		// TOC.
		echo '<div class="mde-tools mde-toc"><div class="mde-tools__label">' . esc_html( $s['toc_label'] ) . '</div><ul>';
		$i = 0;
		foreach ( (array) $s['toc'] as $t ) {
			$i++;
			echo '<li><a href="#" class="' . ( 1 === $i ? 'is-active' : '' ) . '"><span class="n">' . esc_html( MDE_Helpers::fa( $i ) ) . '</span>' . esc_html( $t['text'] ) . '</a></li>';
		}
		echo '</ul></div>';

		// Next.
		$nu = ! empty( $s['next_url']['url'] ) ? $s['next_url']['url'] : '#';
		echo '<a class="mde-next" href="' . esc_url( $nu ) . '">';
		echo '<div style="font-size:12px;opacity:.7;margin-bottom:6px;">' . esc_html( $s['next_eyebrow'] ) . '</div>';
		echo '<div style="font-size:16px;font-weight:700;line-height:1.5;">' . esc_html( $s['next_title'] ) . '</div>';
		echo '<div style="font-size:12px;opacity:.7;margin-top:14px;display:flex;align-items:center;gap:4px;">' . esc_html( $s['next_meta'] ) . ' ' . MDE_Helpers::icon( 'chevl', 14 ) . '</div>'; // phpcs:ignore
		echo '</a>';

		echo '</aside></div>';
	}
}
