<?php
/**
 * Hero widget — featured slider (left/larger) + dynamic latest-sessions list
 * (right). The slider rotates through any number of repeater slides; the list
 * reads recent posts from WordPress / a chosen category.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class MDE_Widget_Hero extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-hero';
	}

	public function get_title() {
		return __( 'هیرو صفحه اصلی', 'mde' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	protected function register_controls() {

		$this->start_controls_section( 'sec_feat', array( 'label' => __( 'اسلایدر شاخص', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'source', array(
			'label'   => __( 'منبع', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'manual',
			'options' => array(
				'latest'   => __( 'جدیدترین نوشته دسته', 'mde' ),
				'specific' => __( 'یک نوشته مشخص', 'mde' ),
				'manual'   => __( 'محتوای دستی', 'mde' ),
			),
		) );
		$r->add_control( 'cat', array(
			'label'     => __( 'دسته (برای جدیدترین)', 'mde' ),
			'type'      => Controls_Manager::SELECT2,
			'options'   => MDE_Helpers::category_options(),
			'condition' => array( 'source' => 'latest' ),
		) );
		$r->add_control( 'post_id', array(
			'label'       => __( 'انتخاب نوشته', 'mde' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => MDE_Helpers::post_options(),
			'label_block' => true,
			'condition'   => array( 'source' => 'specific' ),
		) );
		$r->add_control( 'tag', array( 'label' => __( 'برچسب بالا', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جدیدترین جلسه تفسیر', 'mde' ) ) );
		$r->add_control( 'eyebrow', array( 'label' => __( 'پیش‌عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'تفسیر سوره قصص · جلسه ۳۵', 'mde' ) ) );
		$r->add_control( 'title', array( 'label' => __( 'عنوان (در صورت خالی، عنوان نوشته)', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$r->add_control( 'hl', array( 'label' => __( 'عنوان رنگی', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$r->add_control( 'excerpt', array( 'label' => __( 'توضیح (در صورت خالی، خلاصه نوشته)', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'default' => '' ) );
		$r->add_control( 'img', array( 'label' => __( 'تصویر (در صورت خالی، تصویر شاخص نوشته)', 'mde' ), 'type' => Controls_Manager::MEDIA ) );
		$r->add_control( 'link', array( 'label' => __( 'پیوند اسلاید (دستی)', 'mde' ), 'type' => Controls_Manager::URL, 'condition' => array( 'source' => 'manual' ) ) );
		$r->add_control( 'btn1', array( 'label' => __( 'دکمه ۱', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'پخش جلسه', 'mde' ) ) );
		$r->add_control( 'btn2', array( 'label' => __( 'دکمه ۲', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'خواندن متن تفسیر', 'mde' ) ) );

		$this->add_control( 'slides', array(
			'label'       => __( 'اسلایدها', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => array(
				array(
					'source'  => 'latest',
					'tag'     => __( 'جدیدترین جلسه تفسیر', 'mde' ),
					'eyebrow' => __( 'تفسیر سوره قصص · جلسه ۳۵', 'mde' ),
					'hl'      => __( 'چهارشنبه ۱۴۰۵/۲/۲', 'mde' ),
					'btn1'    => __( 'پخش جلسه', 'mde' ),
					'btn2'    => __( 'خواندن متن تفسیر', 'mde' ),
				),
			),
			'title_field' => '{{{ title || eyebrow || "اسلاید" }}}',
		) );

		$this->add_control( 'autoplay', array(
			'label'   => __( 'پخش خودکار', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'autoplay_speed', array(
			'label'     => __( 'فاصله پخش خودکار (ثانیه)', 'mde' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 6,
			'min'       => 2,
			'max'       => 30,
			'condition' => array( 'autoplay' => 'yes' ),
		) );
		$this->add_control( 'show_arrows', array(
			'label'   => __( 'نمایش پیکان‌ها', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_dots', array(
			'label'   => __( 'نمایش نقطه‌ها', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->add_control( 'show_slide_title', array(
			'label'        => __( 'نمایش عنوان اسلاید', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'label_on'     => __( 'نمایش', 'mde' ),
			'label_off'    => __( 'مخفی', 'mde' ),
			'return_value' => 'yes',
		) );

		$this->add_control( 'show_slide_eyebrow', array(
			'label'        => __( 'نمایش پیش‌عنوان اسلاید', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		) );

		$this->add_control( 'show_slide_tag', array(
			'label'        => __( 'نمایش برچسب بالای اسلاید', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		) );

		$this->add_control( 'show_slide_excerpt', array(
			'label'        => __( 'نمایش توضیح اسلاید', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		) );

		$this->end_controls_section();

		// Slide title typography (dedicated, so it actually targets the heading).
		$this->start_controls_section( 'sec_slide_title_style', array(
			'label'     => __( 'استایل عنوان اسلاید', 'mde' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => array( 'show_slide_title' => 'yes' ),
		) );

		$this->add_responsive_control( 'slide_title_size', array(
			'label'      => __( 'اندازه فونت عنوان', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'em', 'rem' ),
			'range'      => array(
				'px' => array( 'min' => 14, 'max' => 80 ),
			),
			'default'    => array( 'size' => 36, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-hero-feat h1' => 'font-size: {{SIZE}}{{UNIT}} !important;',
			),
		) );

		$this->add_control( 'slide_title_color', array(
			'label'     => __( 'رنگ عنوان', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-hero-feat h1' => 'color: {{VALUE}};',
			),
		) );

		$this->add_control( 'slide_title_hl_color', array(
			'label'     => __( 'رنگ عنوان رنگی', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-hero-feat h1 .hl' => 'color: {{VALUE}} !important;',
			),
		) );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'slide_title_typo',
				'label'    => __( 'تایپوگرافی کامل عنوان', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-hero-feat h1',
			)
		);

		$this->end_controls_section();

		// Image size + position controls for the featured slide image.
		$this->start_controls_section( 'sec_slide_image', array(
			'label' => __( 'تنظیم تصویر اسلاید', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'img_width', array(
			'label'      => __( 'عرض ناحیه تصویر (٪)', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
			'default'    => array( 'size' => 46, 'unit' => '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-hero-feat__img' => 'width: {{SIZE}}{{UNIT}};',
			),
		) );

		$this->add_control( 'img_side', array(
			'label'   => __( 'جای تصویر در اسلاید', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'left',
			'options' => array(
				'left'  => __( 'سمت چپ', 'mde' ),
				'right' => __( 'سمت راست', 'mde' ),
			),
			'selectors_dictionary' => array(
				'left'  => 'left: 0; right: auto;',
				'right' => 'right: 0; left: auto;',
			),
			'selectors' => array(
				'{{WRAPPER}} .mde-hero-feat__img' => '{{VALUE}};',
			),
		) );

		$this->add_responsive_control( 'img_pos_x', array(
			'label'      => __( 'افست افقی تصویر داخل کادر (٪)', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
			'default'    => array( 'size' => 50, 'unit' => '%' ),
			'size_units' => array( '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-hero-feat__img img' => 'object-position: {{SIZE}}% var(--mde-hero-img-y, 50%);',
				'{{WRAPPER}} .mde-hero-feat__img' => '--mde-hero-img-x: {{SIZE}}%;',
			),
		) );

		$this->add_responsive_control( 'img_pos_y', array(
			'label'      => __( 'افست عمودی تصویر داخل کادر (٪)', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
			'default'    => array( 'size' => 50, 'unit' => '%' ),
			'size_units' => array( '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-hero-feat__img img' => 'object-position: var(--mde-hero-img-x, 50%) {{SIZE}}%;',
				'{{WRAPPER}} .mde-hero-feat__img' => '--mde-hero-img-y: {{SIZE}}%;',
			),
		) );

		$this->add_control( 'img_fit', array(
			'label'   => __( 'نحوه قرارگیری تصویر', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'cover',
			'options' => array(
				'cover'   => __( 'پوشش کامل (cover)', 'mde' ),
				'contain' => __( 'بدون برش (contain)', 'mde' ),
				'fill'    => __( 'کشیده (fill)', 'mde' ),
			),
			'selectors' => array(
				'{{WRAPPER}} .mde-hero-feat__img img' => 'object-fit: {{VALUE}};',
			),
		) );

		$this->add_responsive_control( 'slide_min_height', array(
			'label'      => __( 'ارتفاع حداقل اسلاید', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'vh' ),
			'range'      => array(
				'px' => array( 'min' => 250, 'max' => 900 ),
				'vh' => array( 'min' => 30, 'max' => 100 ),
			),
			'default'    => array( 'size' => 460, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-hero-slides, {{WRAPPER}} .mde-hero-feat' => 'min-height: {{SIZE}}{{UNIT}};',
			),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'sec_side', array( 'label' => __( 'لیست جدیدترین جلسات', 'mde' ) ) );
		$this->add_control( 'side_title', array( 'label' => __( 'عنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جدیدترین جلسات', 'mde' ) ) );
		$this->add_control( 'side_sub', array( 'label' => __( 'زیرعنوان', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'تفسیر سوره قصص', 'mde' ) ) );
		$this->add_control( 'side_link', array( 'label' => __( 'متن «مشاهده همه»', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مشاهده همه', 'mde' ) ) );
		$this->add_control( 'side_link_url', array( 'label' => __( 'پیوند «مشاهده همه»', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'side_cat', array( 'label' => __( 'دسته لیست', 'mde' ), 'type' => Controls_Manager::SELECT2, 'options' => MDE_Helpers::category_options() ) );
		$this->add_control( 'side_count', array( 'label' => __( 'تعداد', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 5 ) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	/**
	 * Resolve a slide repeater row into the post fields we actually render.
	 *
	 * @param array $slide Repeater row.
	 * @return array<string,string>
	 */
	private function resolve_slide( $slide ) {
		$slide = wp_parse_args( $slide, array(
			'source'  => 'manual',
			'cat'     => '',
			'post_id' => '',
			'tag'     => '',
			'eyebrow' => '',
			'title'   => '',
			'hl'      => '',
			'excerpt' => '',
			'img'     => array(),
			'link'    => array(),
			'btn1'    => '',
			'btn2'    => '',
		) );

		// Resolve which post (if any) this slide is mirroring.
		$post_id = 0;
		if ( 'latest' === $slide['source'] ) {
			$q = MDE_Helpers::query( array( 'category' => $slide['cat'], 'count' => 1 ) );
			if ( $q->have_posts() ) {
				$q->the_post();
				$post_id = (int) get_the_ID();
				wp_reset_postdata();
			}
		} elseif ( 'specific' === $slide['source'] ) {
			$post_id = absint( $slide['post_id'] );
		}

		// Build defaults from the resolved post (if any).
		$post_title   = '';
		$post_excerpt = '';
		$post_link    = '';
		$post_img     = '';
		if ( $post_id ) {
			$post_title   = get_the_title( $post_id );
			$post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 28 );
			$post_link    = get_permalink( $post_id );
			$post_img     = MDE_Helpers::thumb( $post_id, '' );
		}

		// Manual overrides win when filled; otherwise fall back to the post.
		$slide_img = ! empty( $slide['img']['url'] ) ? $slide['img']['url'] : '';
		$slide_link = ! empty( $slide['link']['url'] ) ? $slide['link']['url'] : '';

		return array(
			'tag'     => $slide['tag'],
			'eyebrow' => $slide['eyebrow'],
			'title'   => '' !== $slide['title'] ? $slide['title'] : $post_title,
			'hl'      => $slide['hl'],
			'excerpt' => '' !== $slide['excerpt'] ? $slide['excerpt'] : $post_excerpt,
			'img'     => $slide_img ? $slide_img : ( $post_img ? $post_img : MDE_ASSETS . 'images/logo.jpg' ),
			'link'    => $slide_link ? $slide_link : ( $post_link ? $post_link : '#' ),
			'btn1'    => $slide['btn1'],
			'btn2'    => $slide['btn2'],
		);
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$slides   = ! empty( $s['slides'] ) ? (array) $s['slides'] : array();
		$autoplay = ( 'yes' === $s['autoplay'] ) ? max( 2, (int) $s['autoplay_speed'] ) * 1000 : 0;

		echo '<div class="mde-scope mde-page-enter" dir="rtl"><div class="mde-container">';
		echo '<div class="mde-hero-grid">';

		// Slider.
		echo '<div class="mde-hero-feat-wrap mde-reveal" data-mde-slider data-autoplay="' . esc_attr( $autoplay ) . '">';
		echo '<div class="mde-hero-slides">';
		$show_tag     = ! isset( $s['show_slide_tag'] )     || 'yes' === $s['show_slide_tag'];
		$show_eyebrow = ! isset( $s['show_slide_eyebrow'] ) || 'yes' === $s['show_slide_eyebrow'];
		$show_title   = ! isset( $s['show_slide_title'] )   || 'yes' === $s['show_slide_title'];
		$show_excerpt = ! isset( $s['show_slide_excerpt'] ) || 'yes' === $s['show_slide_excerpt'];

		foreach ( $slides as $i => $row ) {
			$slide = $this->resolve_slide( $row );
			$active = ( 0 === $i ) ? ' is-active' : '';
			echo '<a class="mde-hero-feat mde-hero-slide' . esc_attr( $active ) . '" href="' . esc_url( $slide['link'] ) . '" data-slide="' . esc_attr( $i ) . '">';
			echo '<div class="mde-hero-feat__img"><img src="' . esc_url( $slide['img'] ) . '" alt="' . esc_attr( $slide['title'] ) . '" /></div>';
			echo '<div class="mde-hero-feat__body">';
			if ( $show_tag && $slide['tag'] ) {
				echo '<div><span class="mde-hero-feat__tag"><span></span>' . esc_html( $slide['tag'] ) . '</span></div>';
			}
			echo '<div class="mde-hero-feat__main">';
			if ( $show_eyebrow && $slide['eyebrow'] ) {
				echo '<div style="font-size:13px;color:rgba(255,255,255,0.65);margin-bottom:8px;">' . esc_html( $slide['eyebrow'] ) . '</div>';
			}
			if ( $show_title && ( $slide['title'] || $slide['hl'] ) ) {
				echo '<h1>' . esc_html( $slide['title'] );
				if ( $slide['hl'] ) {
					echo '<br><span class="hl">' . esc_html( $slide['hl'] ) . '</span>';
				}
				echo '</h1>';
			}
			if ( $show_excerpt && $slide['excerpt'] ) {
				echo '<p class="mde-hero-feat__excerpt">' . esc_html( $slide['excerpt'] ) . '</p>';
			}
			if ( $slide['btn1'] || $slide['btn2'] ) {
				echo '<div style="display:flex;gap:10px;flex-wrap:wrap;">';
				if ( $slide['btn1'] ) {
					echo '<span class="mde-btn mde-btn--lg mde-hero-btn--primary">' . MDE_Helpers::icon( 'play' ) . ' ' . esc_html( $slide['btn1'] ) . '</span>'; // phpcs:ignore
				}
				if ( $slide['btn2'] ) {
					echo '<span class="mde-btn mde-btn--lg mde-hero-btn--secondary">' . esc_html( $slide['btn2'] ) . '</span>';
				}
				echo '</div>';
			}
			echo '</div></div></a>';
		}
		echo '</div>'; // .mde-hero-slides

		if ( 'yes' === $s['show_arrows'] && count( $slides ) > 1 ) {
			echo '<button type="button" class="mde-hero-arrow mde-hero-arrow--prev" data-dir="prev" aria-label="' . esc_attr__( 'قبلی', 'mde' ) . '">' . MDE_Helpers::icon( 'chevr', 22 ) . '</button>'; // phpcs:ignore
			echo '<button type="button" class="mde-hero-arrow mde-hero-arrow--next" data-dir="next" aria-label="' . esc_attr__( 'بعدی', 'mde' ) . '">' . MDE_Helpers::icon( 'chevl', 22 ) . '</button>'; // phpcs:ignore
		}
		if ( 'yes' === $s['show_dots'] && count( $slides ) > 1 ) {
			echo '<div class="mde-hero-dots">';
			foreach ( $slides as $i => $_row ) {
				$active = ( 0 === $i ) ? ' is-active' : '';
				echo '<button type="button" class="mde-hero-dot' . esc_attr( $active ) . '" data-dot="' . esc_attr( $i ) . '" aria-label="' . esc_attr( sprintf( __( 'اسلاید %d', 'mde' ), $i + 1 ) ) . '"></button>';
			}
			echo '</div>';
		}
		echo '</div>'; // .mde-hero-feat-wrap

		// Side list.
		echo '<div class="mde-hero-side mde-reveal" data-delay="100">';
		echo '<div class="mde-hero-side__head"><h3>' . esc_html( $s['side_title'] ) . '</h3>';
		$su = ! empty( $s['side_link_url']['url'] ) ? $s['side_link_url']['url'] : '#';
		echo '<a href="' . esc_url( $su ) . '">' . esc_html( $s['side_link'] ) . '</a></div>';
		echo '<p class="mde-hero-side__sub">' . esc_html( $s['side_sub'] ) . '</p>';

		$lq = MDE_Helpers::query( array( 'category' => $s['side_cat'], 'count' => (int) $s['side_count'] ) );
		$i  = 0;
		while ( $lq->have_posts() ) {
			$lq->the_post();
			$i++;
			echo '<a class="mde-hs-item" href="' . esc_url( get_permalink() ) . '">';
			echo '<div class="mde-hs-num">' . esc_html( MDE_Helpers::fa( $i ) ) . '</div>';
			echo '<div><h4>' . esc_html( get_the_title() ) . '</h4><div class="meta"><span>' . MDE_Helpers::icon( 'clock', 11 ) . ' ' . esc_html( MDE_Helpers::fa( get_the_date( 'H:i' ) ) ) . '</span><span>' . esc_html( MDE_Helpers::date( get_the_ID() ) ) . '</span></div></div>'; // phpcs:ignore
			if ( $i <= 2 ) {
				echo '<span class="mde-hs-new">' . esc_html__( 'جدید', 'mde' ) . '</span>';
			} else {
				echo '<span></span>';
			}
			echo '</a>';
		}
		wp_reset_postdata();

		echo '</div></div></div></div>';
	}
}
