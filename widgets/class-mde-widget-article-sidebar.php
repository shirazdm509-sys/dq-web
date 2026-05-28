<?php
/**
 * Article sidebar — flexible side column for single-article (and any
 * other) pages. Each "section" is a Repeater row whose type drives what
 * gets rendered: latest posts, categories list, an HTML/banner block,
 * a newsletter, etc. The user can re-order and remove sections from
 * the Elementor panel.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class MDE_Widget_Article_Sidebar extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-article-sidebar';
	}

	public function get_title() {
		return __( 'سایدبار مقاله', 'mde' );
	}

	public function get_icon() {
		return 'eicon-sidebar';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'بخش‌های سایدبار', 'mde' ) ) );

		$r = new Repeater();
		$r->add_control( 'type', array(
			'label'   => __( 'نوع', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'latest',
			'options' => array(
				'latest'     => __( 'آخرین مقالات', 'mde' ),
				'popular'    => __( 'پربازدیدترین مقالات', 'mde' ),
				'related'    => __( 'مقالات همان دسته', 'mde' ),
				'categories' => __( 'لیست دسته‌بندی‌ها', 'mde' ),
				'banner'     => __( 'بنر تصویری', 'mde' ),
				'html'       => __( 'متن/کد HTML', 'mde' ),
				'newsletter' => __( 'خبرنامه', 'mde' ),
			),
		) );

		$r->add_control( 'title', array(
			'label'   => __( 'عنوان بخش', 'mde' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		) );

		// Latest / popular / related — common controls.
		$r->add_control( 'count', array(
			'label'     => __( 'تعداد', 'mde' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 5,
			'min'       => 1,
			'max'       => 20,
			'condition' => array( 'type' => array( 'latest', 'popular', 'related' ) ),
		) );

		$r->add_control( 'cat', array(
			'label'     => __( 'محدود به دسته', 'mde' ),
			'type'      => Controls_Manager::SELECT2,
			'options'   => MDE_Helpers::category_options(),
			'condition' => array( 'type' => array( 'latest', 'popular' ) ),
		) );

		$r->add_control( 'show_thumb', array(
			'label'     => __( 'نمایش تصویر شاخص', 'mde' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => array( 'type' => array( 'latest', 'popular', 'related' ) ),
		) );

		$r->add_control( 'show_date', array(
			'label'     => __( 'نمایش تاریخ', 'mde' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => array( 'type' => array( 'latest', 'popular', 'related' ) ),
		) );

		// Categories list options.
		$r->add_control( 'cats_show_count', array(
			'label'     => __( 'نمایش تعداد پست‌ها', 'mde' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => array( 'type' => 'categories' ),
		) );
		$r->add_control( 'cats_parent_only', array(
			'label'     => __( 'فقط دسته‌های اصلی', 'mde' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => array( 'type' => 'categories' ),
		) );
		$r->add_control( 'cats_limit', array(
			'label'     => __( 'حداکثر تعداد', 'mde' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 10,
			'min'       => 1,
			'max'       => 50,
			'condition' => array( 'type' => 'categories' ),
		) );
		$r->add_control( 'cats_select', array(
			'label'       => __( 'دسته‌های خاص (اختیاری)', 'mde' ),
			'description' => __( 'اگر انتخاب کنید، فقط همین دسته‌ها نمایش داده می‌شوند و تعداد بالا نادیده گرفته می‌شود.', 'mde' ),
			'type'        => Controls_Manager::SELECT2,
			'options'     => MDE_Helpers::category_options(),
			'multiple'    => true,
			'label_block' => true,
			'condition'   => array( 'type' => 'categories' ),
		) );

		// Banner.
		$r->add_control( 'banner_img', array(
			'label'     => __( 'تصویر بنر', 'mde' ),
			'type'      => Controls_Manager::MEDIA,
			'condition' => array( 'type' => 'banner' ),
		) );
		$r->add_control( 'banner_url', array(
			'label'     => __( 'پیوند بنر', 'mde' ),
			'type'      => Controls_Manager::URL,
			'condition' => array( 'type' => 'banner' ),
		) );
		$r->add_control( 'banner_alt', array(
			'label'     => __( 'متن جایگزین تصویر', 'mde' ),
			'type'      => Controls_Manager::TEXT,
			'condition' => array( 'type' => 'banner' ),
		) );

		// Free HTML.
		$r->add_control( 'html_content', array(
			'label'     => __( 'محتوای HTML', 'mde' ),
			'type'      => Controls_Manager::TEXTAREA,
			'rows'      => 8,
			'condition' => array( 'type' => 'html' ),
		) );

		// Newsletter.
		$r->add_control( 'news_desc', array(
			'label'     => __( 'متن توضیح', 'mde' ),
			'type'      => Controls_Manager::TEXTAREA,
			'rows'      => 3,
			'default'   => __( 'آخرین مقالات و جلسات تفسیر را در ایمیل خود دریافت کنید.', 'mde' ),
			'condition' => array( 'type' => 'newsletter' ),
		) );
		$r->add_control( 'news_placeholder', array(
			'label'     => __( 'متن داخل کادر ایمیل', 'mde' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'ایمیل شما', 'mde' ),
			'condition' => array( 'type' => 'newsletter' ),
		) );
		$r->add_control( 'news_btn', array(
			'label'     => __( 'متن دکمه', 'mde' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'عضویت', 'mde' ),
			'condition' => array( 'type' => 'newsletter' ),
		) );
		$r->add_control( 'news_action', array(
			'label'       => __( 'آدرس فرم (اختیاری)', 'mde' ),
			'type'        => Controls_Manager::URL,
			'condition'   => array( 'type' => 'newsletter' ),
			'description' => __( 'مثلاً URL خبرنامه‌ی Mailchimp. خالی = فرم نمایشی.', 'mde' ),
		) );

		$this->add_control( 'sections', array(
			'label'       => __( 'بخش‌ها', 'mde' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => array(
				array( 'type' => 'latest',     'title' => __( 'آخرین مقالات', 'mde' ) ),
				array( 'type' => 'categories', 'title' => __( 'دسته‌بندی‌ها', 'mde' ) ),
				array( 'type' => 'newsletter', 'title' => __( 'عضویت در خبرنامه', 'mde' ) ),
			),
			'title_field' => '{{{ title || type }}}',
		) );

		$this->add_control( 'sticky', array(
			'label'        => __( 'چسبیدن سایدبار به بالا هنگام اسکرول', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		) );

		$this->add_responsive_control( 'sticky_offset', array(
			'label'      => __( 'فاصله از بالا هنگام چسبیدن', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
			'default'    => array( 'size' => 100, 'unit' => 'px' ),
			'condition'  => array( 'sticky' => 'yes' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-asb' => 'top: {{SIZE}}{{UNIT}};',
			),
		) );

		$this->end_controls_section();

		// Style — section card.
		$this->start_controls_section( 'sec_card_style', array(
			'label' => __( 'استایل کارت بخش', 'mde' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'card_bg', array(
			'label'     => __( 'پس‌زمینه کارت', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-asb__card' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'card_border', array(
			'label'     => __( 'رنگ حاشیه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-asb__card' => 'border-color: {{VALUE}};' ),
		) );
		$this->add_responsive_control( 'card_radius', array(
			'label'      => __( 'گردی گوشه', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
			'default'    => array( 'size' => 14, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .mde-asb__card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'card_padding', array(
			'label'      => __( 'فاصله داخلی', 'mde' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'default'    => array( 'top' => '18', 'right' => '18', 'bottom' => '18', 'left' => '18', 'unit' => 'px', 'isLinked' => true ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-asb__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typo',
				'label'    => __( 'تایپوگرافی عنوان', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-asb__title',
			)
		);
		$this->add_control( 'card_title_color', array(
			'label'     => __( 'رنگ عنوان', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-asb__title' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'card_title_accent', array(
			'label'     => __( 'رنگ خط زیر عنوان', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .mde-asb__title::after' => 'background: {{VALUE}};' ),
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typo',
				'label'    => __( 'تایپوگرافی آیتم‌ها', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-asb__item-title, {{WRAPPER}} .mde-asb__cat',
			)
		);
		$this->add_control( 'item_color', array(
			'label'     => __( 'رنگ آیتم‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-asb__item-title, {{WRAPPER}} .mde-asb__cat' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'item_color_hover', array(
			'label'     => __( 'رنگ آیتم (هاور)', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-asb__item:hover .mde-asb__item-title, {{WRAPPER}} .mde-asb__cat:hover' => 'color: {{VALUE}};',
			),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$sections = isset( $s['sections'] ) ? (array) $s['sections'] : array();
		$sticky   = ( 'yes' === $s['sticky'] );

		$cls = 'mde-scope mde-asb' . ( $sticky ? ' is-sticky' : '' );
		echo '<aside class="' . esc_attr( $cls ) . '" dir="rtl">';
		foreach ( $sections as $sec ) {
			$this->render_section( $sec );
		}
		echo '</aside>';
	}

	/**
	 * Dispatch to the right section renderer.
	 *
	 * @param array $sec Repeater row.
	 */
	private function render_section( $sec ) {
		$type  = isset( $sec['type'] ) ? $sec['type'] : '';
		$title = isset( $sec['title'] ) ? $sec['title'] : '';

		echo '<div class="mde-asb__card mde-asb__card--' . esc_attr( $type ) . ' mde-reveal">';
		if ( '' !== $title ) {
			echo '<h3 class="mde-asb__title">' . esc_html( $title ) . '</h3>';
		}

		switch ( $type ) {
			case 'latest':
			case 'popular':
			case 'related':
				$this->render_posts( $sec, $type );
				break;
			case 'categories':
				$this->render_categories( $sec );
				break;
			case 'banner':
				$this->render_banner( $sec );
				break;
			case 'html':
				$this->render_html( $sec );
				break;
			case 'newsletter':
				$this->render_newsletter( $sec );
				break;
		}

		echo '</div>';
	}

	private function render_posts( $sec, $type ) {
		$count = max( 1, (int) ( isset( $sec['count'] ) ? $sec['count'] : 5 ) );

		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( 'popular' === $type ) {
			$args['meta_key'] = 'mde_views'; // phpcs:ignore
			$args['orderby']  = 'meta_value_num date';
			$args['order']    = 'DESC';
		}

		if ( 'related' === $type && is_singular( 'post' ) ) {
			$current_id = get_the_ID();
			$cats       = wp_get_post_categories( $current_id );
			if ( ! empty( $cats ) ) {
				$args['category__in'] = $cats;
			}
			$args['post__not_in'] = array( $current_id );
		} elseif ( ! empty( $sec['cat'] ) ) {
			$ids = MDE_Helpers::normalize_cat_ids( $sec['cat'] );
			if ( ! empty( $ids ) ) {
				$args['category__in'] = $ids;
			}
		}

		$q = new WP_Query( $args );
		if ( ! $q->have_posts() ) {
			echo '<p class="mde-asb__empty">' . esc_html__( 'موردی برای نمایش وجود ندارد.', 'mde' ) . '</p>';
			return;
		}

		$show_thumb = ( 'yes' === ( isset( $sec['show_thumb'] ) ? $sec['show_thumb'] : 'yes' ) );
		$show_date  = ( 'yes' === ( isset( $sec['show_date'] ) ? $sec['show_date'] : 'yes' ) );

		echo '<ul class="mde-asb__list">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$id    = get_the_ID();
			$link  = get_permalink( $id );
			$thumb = MDE_Helpers::thumb( $id, '', 'thumbnail' );
			echo '<li><a class="mde-asb__item" href="' . esc_url( $link ) . '">';
			if ( $show_thumb ) {
				echo '<span class="mde-asb__item-thumb"><img src="' . esc_url( $thumb ) . '" alt="" loading="lazy" /></span>';
			}
			echo '<span class="mde-asb__item-body">';
			echo '<span class="mde-asb__item-title">' . esc_html( wp_trim_words( get_the_title(), 10 ) ) . '</span>';
			if ( $show_date ) {
				echo '<span class="mde-asb__item-meta">' . MDE_Helpers::icon( 'calendar', 11 ) . ' ' . esc_html( MDE_Helpers::date( $id ) ) . '</span>'; // phpcs:ignore
			}
			echo '</span></a></li>';
		}
		echo '</ul>';
		wp_reset_postdata();
	}

	private function render_categories( $sec ) {
		$show_count  = ( 'yes' === ( isset( $sec['cats_show_count'] ) ? $sec['cats_show_count'] : 'yes' ) );
		$parent_only = ( 'yes' === ( isset( $sec['cats_parent_only'] ) ? $sec['cats_parent_only'] : 'yes' ) );
		$limit       = max( 1, (int) ( isset( $sec['cats_limit'] ) ? $sec['cats_limit'] : 10 ) );
		$selected    = ! empty( $sec['cats_select'] ) ? MDE_Helpers::normalize_cat_ids( $sec['cats_select'] ) : array();
		$totals      = MDE_Helpers::category_total_counts();

		if ( ! empty( $selected ) ) {
			// Honour the chosen order; show all picked categories regardless of
			// whether their own posts are zero (their sub-categories may hold them).
			$cats = get_categories( array( 'include' => $selected, 'hide_empty' => false, 'orderby' => 'include' ) );
		} else {
			$args = array( 'hide_empty' => false );
			if ( $parent_only ) {
				$args['parent'] = 0;
			}
			$cats = get_categories( $args );
			// Sort by TOTAL count (incl. descendants), drop empties, then limit.
			$cats = array_filter( $cats, function ( $c ) use ( $totals ) {
				return ! empty( $totals[ (int) $c->term_id ] );
			} );
			usort( $cats, function ( $a, $b ) use ( $totals ) {
				return $totals[ (int) $b->term_id ] <=> $totals[ (int) $a->term_id ];
			} );
			$cats = array_slice( $cats, 0, $limit );
		}
		if ( empty( $cats ) ) {
			echo '<p class="mde-asb__empty">' . esc_html__( 'دسته‌بندی‌ای موجود نیست.', 'mde' ) . '</p>';
			return;
		}

		echo '<ul class="mde-asb__cats">';
		foreach ( $cats as $c ) {
			$total = isset( $totals[ (int) $c->term_id ] ) ? $totals[ (int) $c->term_id ] : (int) $c->count;
			echo '<li><a class="mde-asb__cat" href="' . esc_url( get_category_link( $c->term_id ) ) . '">';
			echo '<span>' . esc_html( $c->name ) . '</span>';
			if ( $show_count ) {
				echo '<span class="mde-num-badge">' . esc_html( MDE_Helpers::fa( $total ) ) . '</span>';
			}
			echo '</a></li>';
		}
		echo '</ul>';
	}

	private function render_banner( $sec ) {
		$img = isset( $sec['banner_img']['url'] ) ? $sec['banner_img']['url'] : '';
		if ( ! $img ) {
			return;
		}
		$url    = isset( $sec['banner_url']['url'] ) && $sec['banner_url']['url'] ? $sec['banner_url']['url'] : '';
		$alt    = isset( $sec['banner_alt'] ) ? $sec['banner_alt'] : '';
		$target = ! empty( $sec['banner_url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
		echo '<div class="mde-asb__banner">';
		if ( $url ) {
			echo '<a href="' . esc_url( $url ) . '"' . $target . '><img src="' . esc_url( $img ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" /></a>'; // phpcs:ignore
		} else {
			echo '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" />';
		}
		echo '</div>';
	}

	private function render_html( $sec ) {
		$html = isset( $sec['html_content'] ) ? $sec['html_content'] : '';
		if ( '' === trim( $html ) ) {
			return;
		}
		echo '<div class="mde-asb__html">' . do_shortcode( wp_kses_post( $html ) ) . '</div>';
	}

	private function render_newsletter( $sec ) {
		$desc        = isset( $sec['news_desc'] ) ? $sec['news_desc'] : '';
		$placeholder = isset( $sec['news_placeholder'] ) ? $sec['news_placeholder'] : __( 'ایمیل شما', 'mde' );
		$btn         = isset( $sec['news_btn'] ) ? $sec['news_btn'] : __( 'عضویت', 'mde' );
		$action      = isset( $sec['news_action']['url'] ) ? $sec['news_action']['url'] : '';

		if ( '' !== $desc ) {
			echo '<p class="mde-asb__news-desc">' . esc_html( $desc ) . '</p>';
		}
		$action_attr = $action ? ' action="' . esc_url( $action ) . '" method="post" target="_blank"' : ' onsubmit="return false;"';
		echo '<form class="mde-asb__news"' . $action_attr . '>'; // phpcs:ignore
		echo '<input type="email" name="EMAIL" placeholder="' . esc_attr( $placeholder ) . '" required />';
		echo '<button type="submit" class="mde-btn mde-btn--primary">' . esc_html( $btn ) . '</button>';
		echo '</form>';
	}
}
