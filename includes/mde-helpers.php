<?php
/**
 * Shared helpers: dynamic queries, menus, and reusable card markup.
 * Keeping this DRY lets every section be its own widget without duplicating
 * the post-loop / card rendering logic.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MDE_Helpers {

	/**
	 * Category options for an Elementor SELECT2 control.
	 *
	 * @return array<int|string,string>
	 */
	public static function category_options() {
		$opts = array( '' => __( '— همه دسته‌ها —', 'mde' ) );
		$cats = get_categories( array( 'hide_empty' => false ) );
		foreach ( $cats as $cat ) {
			$opts[ $cat->term_id ] = $cat->name . ' (' . $cat->count . ')';
		}
		return $opts;
	}

	/**
	 * Recent posts as options for a SELECT2 control. Caps at 100 to keep the
	 * editor responsive on big sites.
	 *
	 * @return array<int|string,string>
	 */
	public static function post_options() {
		$opts  = array( '' => __( '— انتخاب نوشته —', 'mde' ) );
		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		foreach ( $posts as $p ) {
			$opts[ $p->ID ] = wp_strip_all_tags( $p->post_title );
		}
		return $opts;
	}

	/**
	 * Registered nav-menu options for a SELECT control.
	 *
	 * @return array<int|string,string>
	 */
	public static function menu_options() {
		$opts  = array( '' => __( '— انتخاب منو —', 'mde' ) );
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu ) {
			$opts[ $menu->term_id ] = $menu->name;
		}
		return $opts;
	}

	/**
	 * Run a posts query for a section widget.
	 *
	 * @param array $args {
	 *     @type int|string|array $category  Term id, csv string, or array of ids.
	 *     @type int              $count     Posts per page.
	 *     @type string           $orderby   WP orderby.
	 *     @type string           $order     ASC|DESC.
	 *     @type bool             $use_query Use the main/current query (archive).
	 *     @type int              $paged     Page number.
	 * }
	 * @return WP_Query
	 */
	public static function query( $args ) {
		$defaults = array(
			'category'  => '',
			'count'     => 6,
			'orderby'   => 'date',
			'order'     => 'DESC',
			'use_query' => false,
			'paged'     => 1,
		);
		$args = wp_parse_args( $args, $defaults );

		if ( $args['use_query'] && ( is_archive() || is_home() || is_search() ) ) {
			// Inherit the original archive query (current category, tag,
			// search term, etc.) but force our own posts_per_page + paged
			// so the widget count actually takes effect — the main query
			// otherwise uses the global "Settings → Reading" value.
			// `$wp_query->query` is the raw input args; `query_vars`
			// contains too many internal flags that confuse a re-issued
			// WP_Query (e.g. error=1, suppress_filters, no_found_rows…).
			global $wp_query;
			$q_args = is_array( $wp_query->query ) ? $wp_query->query : array();
			$q_args['posts_per_page']      = (int) $args['count'];
			$q_args['paged']               = max( 1, (int) $args['paged'] );
			$q_args['ignore_sticky_posts'] = true;
			// Apply widget-supplied orderby (so archive pages respect the
			// "sort by" control). If the widget didn't ask for anything
			// special, leave the default (date DESC) as-is.
			if ( ! empty( $args['orderby'] ) && 'date' !== $args['orderby'] ) {
				$q_args['orderby'] = $args['orderby'];
				$q_args['order']   = $args['order'];
				if ( 'meta_value_num' === $args['orderby'] && ! empty( $args['meta_key'] ) ) {
					$q_args['meta_key'] = $args['meta_key']; // phpcs:ignore
				}
			}
			return new WP_Query( $q_args );
		}

		$q = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => (int) $args['count'],
			'orderby'             => $args['orderby'],
			'order'               => $args['order'],
			'paged'               => max( 1, (int) $args['paged'] ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
		);
		if ( 'meta_value_num' === $args['orderby'] && ! empty( $args['meta_key'] ) ) {
			$q['meta_key'] = $args['meta_key']; // phpcs:ignore
		}
		$cat_ids = self::normalize_cat_ids( $args['category'] );
		if ( ! empty( $cat_ids ) ) {
			$q['category__in'] = $cat_ids;
		}
		return new WP_Query( $q );
	}

	/**
	 * Translate a friendly orderby slug (date_desc, views_desc, …) into
	 * the WP_Query args (orderby, order, meta_key).
	 *
	 * @param string $key Friendly key.
	 * @return array{orderby:string,order:string,meta_key:string}
	 */
	public static function orderby_args( $key ) {
		$map = array(
			'date_desc'     => array( 'orderby' => 'date',           'order' => 'DESC' ),
			'date_asc'      => array( 'orderby' => 'date',           'order' => 'ASC' ),
			'modified_desc' => array( 'orderby' => 'modified',       'order' => 'DESC' ),
			'title_asc'     => array( 'orderby' => 'title',          'order' => 'ASC' ),
			'title_desc'    => array( 'orderby' => 'title',          'order' => 'DESC' ),
			'views_desc'    => array( 'orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => 'mde_views' ),
			'comments_desc' => array( 'orderby' => 'comment_count',  'order' => 'DESC' ),
			'rand'          => array( 'orderby' => 'rand',           'order' => 'DESC' ),
			'menu_order'    => array( 'orderby' => 'menu_order date', 'order' => 'ASC' ),
		);
		if ( ! isset( $map[ $key ] ) ) {
			return array( 'orderby' => 'date', 'order' => 'DESC', 'meta_key' => '' );
		}
		return wp_parse_args( $map[ $key ], array( 'meta_key' => '' ) );
	}

	/**
	 * Coerce a category control value (int / csv / array) into a list of int ids.
	 *
	 * @param mixed $value Raw control value.
	 * @return int[]
	 */
	public static function normalize_cat_ids( $value ) {
		if ( empty( $value ) && '0' !== $value ) {
			return array();
		}
		if ( is_array( $value ) ) {
			$ids = $value;
		} elseif ( is_string( $value ) && false !== strpos( $value, ',' ) ) {
			$ids = explode( ',', $value );
		} else {
			$ids = array( $value );
		}
		$ids = array_filter( array_map( 'absint', $ids ) );
		return array_values( array_unique( $ids ) );
	}

	/**
	 * Featured image URL with a graceful fallback.
	 *
	 * @param int    $post_id  Post id.
	 * @param string $fallback Fallback image URL.
	 * @param string $size     Image size.
	 * @return string
	 */
	public static function thumb( $post_id, $fallback = '', $size = 'large' ) {
		if ( has_post_thumbnail( $post_id ) ) {
			$url = get_the_post_thumbnail_url( $post_id, $size );
			if ( $url ) {
				return $url;
			}
		}
		return $fallback ? $fallback : MDE_ASSETS . 'images/logo.jpg';
	}

	/**
	 * Persian (Eastern Arabic) numerals — matches the prototype's toFa().
	 *
	 * @param string|int $value Value.
	 * @return string
	 */
	public static function fa( $value ) {
		$en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		$fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
		return str_replace( $en, $fa, (string) $value );
	}

	/**
	 * Localised Jalali-ish date string (uses WP date i18n; site locale aware).
	 *
	 * @param int $post_id Post id.
	 * @return string
	 */
	public static function date( $post_id ) {
		return self::fa( get_the_date( 'Y/m/d', $post_id ) );
	}

	/**
	 * Reusable media-card markup (used by tafsir grid, related, category, etc.).
	 * Animations/hover come entirely from the ported CSS classes.
	 *
	 * @param array $a {
	 *     @type int    $id        Post id.
	 *     @type string $variant   default|minimal|elevated.
	 *     @type string $aspect    CSS aspect-ratio value.
	 *     @type string $badge     Badge label (e.g. "ویدئو").
	 *     @type string $badge_ico Eicon-free inline svg key (video|audio|text).
	 *     @type string $fallback  Fallback image.
	 *     @type bool   $show_excerpt Show excerpt.
	 *     @type bool   $play      Show play overlay.
	 * }
	 */
	public static function card( $a ) {
		$a = wp_parse_args(
			$a,
			array(
				'id'           => 0,
				'variant'      => 'default',
				'aspect'       => '16 / 10',
				'badge'        => '',
				'badge_ico'    => 'video',
				'fallback'     => '',
				'show_excerpt' => true,
				'play'         => true,
				'category'     => '',
			)
		);

		$id    = (int) $a['id'];
		$link  = esc_url( get_permalink( $id ) );
		$title = esc_html( get_the_title( $id ) );
		$thumb = esc_url( self::thumb( $id, $a['fallback'] ) );
		$cat   = $a['category'] ? esc_html( $a['category'] ) : '';
		if ( ! $cat ) {
			$cats = get_the_category( $id );
			$cat  = ! empty( $cats ) ? esc_html( $cats[0]->name ) : '';
		}
		$views = self::fa( number_format_i18n( class_exists( 'MDE_Views' ) ? MDE_Views::get_post_views( $id ) : (int) get_post_meta( $id, 'mde_views', true ) ) );
		?>
		<article class="mde-card mde-card--<?php echo esc_attr( $a['variant'] ); ?>" onclick="window.location='<?php echo $link; // phpcs:ignore ?>'">
			<div class="mde-card__media" style="aspect-ratio: <?php echo esc_attr( $a['aspect'] ); ?>;">
				<img src="<?php echo $thumb; // phpcs:ignore ?>" alt="<?php echo $title; // phpcs:ignore ?>" loading="lazy" />
				<?php if ( $a['play'] ) : ?>
					<div class="mde-card__play">
						<span class="mde-play-btn"><?php echo self::icon( 'play' ); // phpcs:ignore ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $a['badge'] ) : ?>
					<span class="mde-card__badge"><?php echo self::icon( $a['badge_ico'] ); // phpcs:ignore ?> <?php echo esc_html( $a['badge'] ); ?></span>
				<?php endif; ?>
			</div>
			<div class="mde-card__body">
				<?php if ( $cat ) : ?><div class="mde-card__category"><?php echo $cat; // phpcs:ignore ?></div><?php endif; ?>
				<h3 class="mde-card__title"><a href="<?php echo $link; // phpcs:ignore ?>"><?php echo $title; // phpcs:ignore ?></a></h3>
				<?php if ( $a['show_excerpt'] ) : ?>
					<p class="mde-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $id ), 22 ) ); ?></p>
				<?php endif; ?>
				<div class="mde-card__meta">
					<span class="mde-views"><?php echo self::icon( 'eye' ); // phpcs:ignore ?> <?php echo esc_html( $views ); ?></span>
					<span><?php echo esc_html( self::date( $id ) ); ?></span>
				</div>
			</div>
		</article>
		<?php
	}

	/**
	 * Inline stroke SVG icons (ported from the prototype icon library).
	 *
	 * @param string $name Icon key.
	 * @param int    $size Pixel size.
	 * @return string
	 */
	public static function icon( $name, $size = 18 ) {
		$paths = array(
			'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
			'menu'     => '<path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/>',
			'close'    => '<path d="M6 6l12 12"/><path d="M18 6l-12 12"/>',
			'home'     => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
			'play'     => '<polygon points="7 5 19 12 7 19 7 5"/>',
			'book'     => '<path d="M4 4h7a3 3 0 0 1 3 3v13"/><path d="M20 4h-7a3 3 0 0 0-3 3v13"/><path d="M4 4v15h7"/><path d="M20 4v15h-7"/>',
			'heart'    => '<path d="M12 21s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 11c0 5.65-7 10-7 10z"/>',
			'bookmark' => '<path d="M6 4h12v17l-6-3.5L6 21z"/>',
			'eye'      => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
			'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
			'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18"/><path d="M8 3v4"/><path d="M16 3v4"/>',
			'phones'   => '<path d="M3 13a9 9 0 0 1 18 0"/><rect x="3" y="13" width="4" height="7" rx="1.5"/><rect x="17" y="13" width="4" height="7" rx="1.5"/>',
			'video'    => '<rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3z"/>',
			'audio'    => '<path d="M3 13a9 9 0 0 1 18 0"/><rect x="3" y="13" width="4" height="7" rx="1.5"/><rect x="17" y="13" width="4" height="7" rx="1.5"/>',
			'share'    => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4"/><path d="m15.4 6.5-6.8 4"/>',
			'chevl'    => '<path d="M15 6l-6 6 6 6"/>',
			'chevr'    => '<path d="M9 6l6 6-6 6"/>',
			'chevd'    => '<path d="M6 9l6 6 6-6"/>',
			'check'    => '<path d="M5 12l5 5 9-11"/>',
			'mosque'   => '<path d="M12 3c-2 2-2 4 0 6"/><path d="M12 3c2 2 2 4 0 6"/><path d="M4 21V13c0-3 3-5 8-5s8 2 8 5v8"/><path d="M4 21h16"/><path d="M10 21v-5a2 2 0 1 1 4 0v5"/>',
			'gift'     => '<rect x="3" y="8" width="18" height="13" rx="2"/><path d="M3 12h18"/><path d="M12 8v13"/><path d="M12 8c-2 0-3-1-3-2.5S10 3 12 5c2-2 3-1 3 .5S14 8 12 8z"/>',
			'users'    => '<circle cx="9" cy="8" r="4"/><path d="M2 21c0-3.5 3-6 7-6s7 2.5 7 6"/><circle cx="17" cy="9" r="3"/><path d="M22 19c0-2.5-2-4.5-5-4.5"/>',
			'doc'      => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4"/>',
			'text'     => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4"/>',
			'download' => '<path d="M12 4v12"/><path d="m7 11 5 5 5-5"/><path d="M4 20h16"/>',
			'broadcast' => '<circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49"/><path d="M7.76 16.24a6 6 0 0 1 0-8.49"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 19.07a10 10 0 0 1 0-14.14"/>',
			'info'      => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6"/><path d="M12 7.5h.01"/>',
			'warning'   => '<path d="M12 3 2 21h20L12 3z"/><path d="M12 10v5"/><path d="M12 18h.01"/>',
		);
		$d = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['chevl'];
		return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
	}
}
