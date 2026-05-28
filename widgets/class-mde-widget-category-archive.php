<?php
/**
 * Category archive — categories sidebar (from WordPress), search, toolbar
 * with sort + grid/list toggle, dynamic post loop and real pagination.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MDE_Widget_Category_Archive extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-category-archive';
	}

	public function get_title() {
		return __( 'آرشیو دسته‌بندی', 'mde' );
	}

	public function get_icon() {
		return 'eicon-archive-posts';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'محتوا', 'mde' ) ) );
		$this->add_control( 'source', array(
			'label'   => __( 'منبع', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'query',
			'options' => array(
				'query'  => __( 'کوئری جاری (صفحه آرشیو)', 'mde' ),
				'manual' => __( 'دسته‌ی انتخابی', 'mde' ),
			),
		) );
		$this->add_control( 'cat', array( 'label' => __( 'دسته', 'mde' ), 'type' => Controls_Manager::SELECT2, 'options' => MDE_Helpers::category_options(), 'condition' => array( 'source' => 'manual' ) ) );
		$this->add_control( 'count', array( 'label' => __( 'تعداد در هر صفحه', 'mde' ), 'type' => Controls_Manager::NUMBER, 'default' => 12 ) );

		$this->add_control( 'orderby', array(
			'label'   => __( 'مرتب‌سازی بر اساس', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'date_desc',
			'options' => array(
				'date_desc'   => __( 'جدیدترین (تاریخ نزولی)', 'mde' ),
				'date_asc'    => __( 'قدیمی‌ترین (تاریخ صعودی)', 'mde' ),
				'modified_desc' => __( 'آخرین به‌روزرسانی', 'mde' ),
				'title_asc'   => __( 'عنوان (الفبایی)', 'mde' ),
				'title_desc'  => __( 'عنوان (برعکس الفبایی)', 'mde' ),
				'views_desc'  => __( 'پربازدیدترین', 'mde' ),
				'comments_desc' => __( 'پرنظرترین', 'mde' ),
				'rand'        => __( 'تصادفی', 'mde' ),
				'menu_order'  => __( 'ترتیب دستی (Menu Order)', 'mde' ),
			),
		) );

		$this->add_control( 'show_sort_dropdown', array(
			'label'        => __( 'نمایش دراپ‌داون مرتب‌سازی', 'mde' ),
			'description'  => __( 'دراپ‌داونی که در نوار ابزار بالای آرشیو نشان داده می‌شود و کاربر می‌تواند مرتب‌سازی را عوض کند.', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		) );
		$this->add_control( 'show_sidebar', array( 'label' => __( 'نمایش ساید‌بار', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'top_level_only', array(
			'label'        => __( 'فقط دسته‌های اصلی در ساید‌بار', 'mde' ),
			'description'  => __( 'زیر دسته‌ها نمایش داده نمی‌شوند.', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'condition'    => array( 'show_sidebar' => 'yes' ),
		) );
		$this->add_control( 'sidebar_cats', array(
			'label'        => __( 'دسته‌های ساید‌بار (انتخابی)', 'mde' ),
			'description'  => __( 'دسته‌هایی که می‌خواهید در ساید‌بار نمایش داده شوند. خالی = همه دسته‌ها.', 'mde' ),
			'type'         => Controls_Manager::SELECT2,
			'options'      => MDE_Helpers::category_options(),
			'multiple'     => true,
			'label_block'  => true,
			'condition'    => array( 'show_sidebar' => 'yes' ),
		) );
		$this->add_control( 'search_ph', array( 'label' => __( 'متن جستجو', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'جستجو در آرشیو…', 'mde' ) ) );
		$this->add_control( 'cats_label', array( 'label' => __( 'عنوان دسته‌ها', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'دسته‌بندی‌ها', 'mde' ) ) );
		$this->add_control( 'badge', array( 'label' => __( 'برچسب کارت', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'مقاله', 'mde' ) ) );
		$this->add_control( 'fallback', array( 'label' => __( 'تصویر پیش‌فرض', 'mde' ), 'type' => Controls_Manager::MEDIA ) );
		$this->end_controls_section();

		// Category description block (read from WordPress term description).
		$this->start_controls_section( 'sec_desc', array(
			'label' => __( 'توضیحات دسته‌بندی', 'mde' ),
		) );
		$this->add_control( 'show_description', array(
			'label'        => __( 'نمایش توضیحات دسته', 'mde' ),
			'description'  => __( 'متنی که برای دسته در «نوشته‌ها ← دسته‌ها» تنظیم کرده‌اید.', 'mde' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
		) );
		$this->add_control( 'description_position', array(
			'label'     => __( 'موقعیت', 'mde' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'bottom',
			'options'   => array(
				'top'    => __( 'بالا (پیش از پست‌ها)', 'mde' ),
				'bottom' => __( 'پایین (بعد از پاجینیشن)', 'mde' ),
			),
			'condition' => array( 'show_description' => 'yes' ),
		) );
		$this->add_control( 'description_title', array(
			'label'       => __( 'عنوان بالای توضیحات (اختیاری)', 'mde' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => __( 'مثلاً: درباره این دسته', 'mde' ),
			'condition'   => array( 'show_description' => 'yes' ),
		) );
		$this->end_controls_section();

		// Description styling — typography + colors + layout.
		$this->start_controls_section( 'sec_desc_style', array(
			'label'     => __( 'استایل توضیحات دسته', 'mde' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => array( 'show_description' => 'yes' ),
		) );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_title_typo',
				'label'    => __( 'تایپوگرافی عنوان', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-cat-desc__title',
			)
		);
		$this->add_control( 'desc_title_color', array(
			'label'     => __( 'رنگ عنوان', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cat-desc__title' => 'color: {{VALUE}};',
			),
		) );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typo',
				'label'    => __( 'تایپوگرافی متن', 'mde' ),
				'selector' => '{{WRAPPER}} .mde-cat-desc__body, {{WRAPPER}} .mde-cat-desc__body p',
			)
		);
		$this->add_control( 'desc_color', array(
			'label'     => __( 'رنگ متن', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cat-desc__body, {{WRAPPER}} .mde-cat-desc__body p' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'desc_link_color', array(
			'label'     => __( 'رنگ لینک‌ها', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cat-desc__body a' => 'color: {{VALUE}};',
			),
		) );

		$this->add_control( 'desc_bg', array(
			'label'     => __( 'پس‌زمینه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cat-desc' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'desc_border_color', array(
			'label'     => __( 'رنگ حاشیه', 'mde' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .mde-cat-desc' => 'border-color: {{VALUE}};',
			),
		) );
		$this->add_responsive_control( 'desc_padding', array(
			'label'      => __( 'فاصله داخلی', 'mde' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'default'    => array( 'top' => '22', 'right' => '24', 'bottom' => '22', 'left' => '24', 'unit' => 'px', 'isLinked' => false ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-cat-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'desc_radius', array(
			'label'      => __( 'گردی گوشه', 'mde' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
			'default'    => array( 'size' => 14, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-cat-desc' => 'border-radius: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'desc_margin', array(
			'label'      => __( 'فاصله بیرونی', 'mde' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'default'    => array( 'top' => '28', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ),
			'selectors'  => array(
				'{{WRAPPER}} .mde-cat-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_control( 'desc_text_align', array(
			'label'     => __( 'تراز متن', 'mde' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => array(
				'start'  => array( 'title' => __( 'راست (RTL)', 'mde' ), 'icon' => 'eicon-text-align-right' ),
				'center' => array( 'title' => __( 'وسط', 'mde' ),       'icon' => 'eicon-text-align-center' ),
				'end'    => array( 'title' => __( 'چپ', 'mde' ),        'icon' => 'eicon-text-align-left' ),
				'justify'=> array( 'title' => __( 'دو طرف', 'mde' ),    'icon' => 'eicon-text-align-justify' ),
			),
			'default'   => 'start',
			'selectors' => array(
				'{{WRAPPER}} .mde-cat-desc' => 'text-align: {{VALUE}};',
			),
		) );

		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	/**
	 * Resolve which term's description to render — current archive term
	 * when source=query, or the manually-picked one when source=manual.
	 *
	 * @param array $s Widget settings.
	 * @return WP_Term|null
	 */
	private function description_term( $s ) {
		if ( 'manual' === $s['source'] && ! empty( $s['cat'] ) ) {
			$term = get_term( (int) $s['cat'], 'category' );
		} else {
			$term = is_category() ? get_queried_object() : null;
		}
		return ( $term && ! is_wp_error( $term ) ) ? $term : null;
	}

	/**
	 * Render the optional category-description card. Pulled out of render()
	 * so it can be emitted either above or below the post grid.
	 *
	 * @param array $s Widget settings.
	 */
	private function render_description( $s ) {
		if ( 'yes' !== $s['show_description'] ) {
			return;
		}
		$term = $this->description_term( $s );
		if ( ! $term ) {
			return;
		}
		$desc = trim( (string) $term->description );
		if ( '' === $desc ) {
			return;
		}
		// Run the same filter WordPress runs on category_description() so
		// shortcodes, oEmbeds and wpautop all behave normally.
		$html = apply_filters( 'category_description', $desc, $term->term_id );

		echo '<section class="mde-cat-desc mde-reveal">';
		if ( ! empty( $s['description_title'] ) ) {
			echo '<h3 class="mde-cat-desc__title">' . esc_html( $s['description_title'] ) . '</h3>';
		}
		echo '<div class="mde-cat-desc__body">' . wp_kses_post( $html ) . '</div>';
		echo '</section>';
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$fb   = ! empty( $s['fallback']['url'] ) ? $s['fallback']['url'] : '';
		$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

		// Resolve the orderby — user can override via ?mde_sort=... URL
		// parameter (set by the toolbar dropdown below), otherwise the
		// admin's chosen default in the widget settings wins.
		$sort_key = isset( $_GET['mde_sort'] ) ? sanitize_key( wp_unslash( $_GET['mde_sort'] ) ) : ( isset( $s['orderby'] ) ? $s['orderby'] : 'date_desc' );
		$order    = MDE_Helpers::orderby_args( $sort_key );

		$q = MDE_Helpers::query( array(
			'use_query' => ( 'query' === $s['source'] ),
			'category'  => ( 'manual' === $s['source'] ) ? $s['cat'] : '',
			'count'     => (int) $s['count'],
			'paged'     => $paged,
			'orderby'   => $order['orderby'],
			'order'     => $order['order'],
			'meta_key'  => isset( $order['meta_key'] ) ? $order['meta_key'] : '',
		) );

		echo '<div class="mde-scope" dir="rtl"><div class="mde-container"><div class="mde-cat-archive mde-cat-layout">';

		// Sidebar.
		if ( 'yes' === $s['show_sidebar'] ) {
			echo '<aside class="mde-cat-sidebar">';
			echo '<form class="mde-cat-search" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '"><input type="search" name="s" placeholder="' . esc_attr( $s['search_ph'] ) . '" />' . MDE_Helpers::icon( 'search', 16 ) . '</form>'; // phpcs:ignore
			echo '<div class="mde-cat-box__t" style="margin-bottom:12px;">' . esc_html( $s['cats_label'] ) . '</div>';
			echo '<ul class="mde-cat-list">';
			$cur             = is_category() ? get_queried_object_id() : 0;
			$sidebar_cat_ids = ! empty( $s['sidebar_cats'] ) ? MDE_Helpers::normalize_cat_ids( $s['sidebar_cats'] ) : array();
			if ( ! empty( $sidebar_cat_ids ) ) {
				$cat_args = array( 'include' => $sidebar_cat_ids, 'hide_empty' => false, 'pad_counts' => true, 'orderby' => 'include' );
			} else {
				$cat_args = array( 'hide_empty' => false, 'pad_counts' => true );
				if ( 'yes' === $s['top_level_only'] ) {
					$cat_args['parent'] = 0;
				}
			}
			$cats = get_categories( $cat_args );
			$dot  = array( '#0f766e', '#7c3aed', '#991b1b', '#1e293b', '#a16207', '#0369a1', '#be185d', '#5b21b6' );
			foreach ( $cats as $k => $c ) {
				$active = ( $c->term_id === $cur ) ? 'is-active' : '';
				echo '<li><a class="' . esc_attr( $active ) . '" href="' . esc_url( get_category_link( $c->term_id ) ) . '"><span style="display:flex;align-items:center;gap:8px;"><span class="dot" style="background:' . esc_attr( $dot[ $k % count( $dot ) ] ) . ';"></span>' . esc_html( $c->name ) . '</span><span class="mde-num-badge">' . esc_html( MDE_Helpers::fa( $c->count ) ) . '</span></a></li>';
			}
			echo '</ul>';
			echo '<div class="mde-cat-box"><div class="mde-cat-box__t">' . esc_html__( 'بازه زمانی', 'mde' ) . '</div>';
			foreach ( array( 'همه', 'یک ماه اخیر', '۳ ماه اخیر', 'یک سال اخیر' ) as $i => $r ) {
				echo '<label style="display:flex;align-items:center;gap:8px;font-size:13.5px;padding:4px 0;cursor:pointer;"><input type="radio" name="mde_range" ' . ( 0 === $i ? 'checked' : '' ) . ' style="accent-color:var(--c-primary);" />' . esc_html( $r ) . '</label>';
			}
			echo '</div></aside>';
		}

		// Main.
		echo '<div class="mde-cat-main" style="min-width:0;">';

		// Description at top (if user picked "top").
		if ( isset( $s['description_position'] ) && 'top' === $s['description_position'] ) {
			$this->render_description( $s );
		}

		$total = $q->found_posts;
		$per   = (int) $s['count'];
		$from  = ( $paged - 1 ) * $per + 1;
		$to    = min( $total, $paged * $per );
		echo '<div class="mde-cat-toolbar">';
		echo '<div style="font-size:13.5px;color:var(--c-muted);">' . esc_html__( 'نمایش', 'mde' ) . ' <strong style="color:var(--c-ink);">' . esc_html( MDE_Helpers::fa( $from ) ) . '-' . esc_html( MDE_Helpers::fa( $to ) ) . '</strong> ' . esc_html__( 'از', 'mde' ) . ' <strong style="color:var(--c-ink);">' . esc_html( MDE_Helpers::fa( $total ) ) . '</strong> ' . esc_html__( 'نتیجه', 'mde' ) . '</div>';
		echo '<div class="mde-cat-toolbar__right">';
		if ( 'yes' === ( isset( $s['show_sort_dropdown'] ) ? $s['show_sort_dropdown'] : 'yes' ) ) {
			$sort_options = array(
				'date_desc'     => __( 'جدیدترین', 'mde' ),
				'date_asc'      => __( 'قدیمی‌ترین', 'mde' ),
				'modified_desc' => __( 'آخرین به‌روزرسانی', 'mde' ),
				'title_asc'     => __( 'عنوان (الفبایی)', 'mde' ),
				'title_desc'    => __( 'عنوان (برعکس)', 'mde' ),
				'views_desc'    => __( 'پربازدیدترین', 'mde' ),
				'comments_desc' => __( 'پرنظرترین', 'mde' ),
				'rand'          => __( 'تصادفی', 'mde' ),
			);
			$current_url = remove_query_arg( array( 'mde_sort', 'paged' ) );
			echo '<span class="mde-cat-toolbar__label">' . esc_html__( 'مرتب‌سازی:', 'mde' ) . '</span>';
			echo '<select class="mde-cat-toolbar__sort" data-mde-sort-base="' . esc_attr( $current_url ) . '" onchange="if(this.value){var b=this.getAttribute(\'data-mde-sort-base\');var sep=b.indexOf(\'?\')===-1?\'?\':\'&\';location.href=b+sep+\'mde_sort=\'+this.value;}">';
			foreach ( $sort_options as $val => $label ) {
				$sel = ( $val === $sort_key ) ? ' selected' : '';
				echo '<option value="' . esc_attr( $val ) . '"' . $sel . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
		}
		echo '<div class="mde-viewtoggle"><button data-mde-view="grid" class="is-active">' . MDE_Helpers::icon( 'menu', 14 ) . '</button><button data-mde-view="list">' . MDE_Helpers::icon( 'doc', 14 ) . '</button></div>'; // phpcs:ignore
		echo '</div></div>';

		// Grid view.
		echo '<div data-mde-grid class="mde-grid mde-grid--3">';
		$i = 0;
		while ( $q->have_posts() ) {
			$q->the_post();
			echo '<div class="mde-reveal" data-delay="' . esc_attr( $i * 40 ) . '">';
			MDE_Helpers::card( array( 'id' => get_the_ID(), 'badge' => $s['badge'], 'fallback' => $fb ) );
			echo '</div>';
			$i++;
		}
		echo '</div>';

		// List view.
		echo '<div data-mde-list style="display:none;flex-direction:column;gap:12px;">';
		$q->rewind_posts();
		while ( $q->have_posts() ) {
			$q->the_post();
			$img = MDE_Helpers::thumb( get_the_ID(), $fb );
			echo '<article class="mde-list-item" onclick="window.location=\'' . esc_url( get_permalink() ) . '\'">';
			echo '<div class="mde-list-item__img"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" /></div>';
			echo '<div style="display:flex;flex-direction:column;justify-content:center;gap:8px;min-width:0;"><div style="font-size:11.5px;color:var(--c-primary);font-weight:600;">' . esc_html( get_the_date( '', get_the_ID() ) ) . '</div><h3 style="font-size:16px;font-weight:700;margin:0;line-height:1.5;">' . esc_html( get_the_title() ) . '</h3><p style="font-size:13px;color:var(--c-muted);margin:0;">' . esc_html( wp_trim_words( get_the_excerpt(), 18 ) ) . '</p></div>';
			echo '<div style="display:flex;flex-direction:column;justify-content:space-between;align-items:flex-end;font-size:12px;color:var(--c-muted);"><span>' . MDE_Helpers::icon( 'eye', 12 ) . '</span><span>' . esc_html( MDE_Helpers::date( get_the_ID() ) ) . '</span></div>'; // phpcs:ignore
			echo '</article>';
		}
		echo '</div>';
		wp_reset_postdata();

		// Pagination.
		$big   = 999999999;
		$links = paginate_links( array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => $paged,
			'total'     => max( 1, (int) $q->max_num_pages ),
			'type'      => 'array',
			'prev_text' => MDE_Helpers::icon( 'chevr', 16 ),
			'next_text' => MDE_Helpers::icon( 'chevl', 16 ),
		) );
		if ( $links ) {
			echo '<div class="mde-pagination">';
			foreach ( $links as $lnk ) {
				echo wp_kses_post( str_replace( array( 'page-numbers current', 'page-numbers' ), array( 'current', '' ), $lnk ) );
			}
			echo '</div>';
		}

		// Description at bottom (default).
		if ( ! isset( $s['description_position'] ) || 'bottom' === $s['description_position'] ) {
			$this->render_description( $s );
		}

		echo '</div></div></div></div>';
	}
}
