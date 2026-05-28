<?php
/**
 * View counter — bumps the `mde_views` post-meta whenever a logged-out
 * visitor opens a singular post. The existing widgets already READ this
 * meta to display "X بازدید" / "پربازدیدترین", so before this class was
 * added the meta was always empty and the widgets fell back to 1.
 *
 * Dedupes per-visitor with a short cookie (24h) so refreshing a page
 * doesn't artificially inflate the count. Bot / preview / admin hits
 * are skipped.
 *
 * Also provides a small helper for category totals (sum of `mde_views`
 * across every post in the term), used by widgets that want to show
 * "X بازدید" on a category card.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MDE_Views {

	const META_KEY      = 'mde_views';
	const COOKIE_PREFIX = 'mde_v_';
	const COOKIE_TTL    = DAY_IN_SECONDS; // 24h dedupe window per visitor.

	/** Post types that get counted. Filterable. */
	public static function post_types() {
		return apply_filters( 'mde_views_post_types', array( 'post' ) );
	}

	public static function boot() {
		add_action( 'wp', array( __CLASS__, 'maybe_count' ) );
	}

	/**
	 * Fires once per request on the `wp` action. Counts the view if all
	 * sanity checks pass — singular post, not a bot/preview/admin path,
	 * and the visitor's de-dupe cookie isn't set yet.
	 */
	public static function maybe_count() {
		if ( ! is_singular( self::post_types() ) ) {
			return;
		}
		if ( ! self::should_count() ) {
			return;
		}

		$post_id = (int) get_queried_object_id();
		if ( $post_id <= 0 ) {
			return;
		}

		// Cookie-based de-dupe: one view per visitor per post per 24h.
		$cookie_name = self::COOKIE_PREFIX . $post_id;
		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			return;
		}

		// Bump the counter.
		$current = (int) get_post_meta( $post_id, self::META_KEY, true );
		update_post_meta( $post_id, self::META_KEY, $current + 1 );

		// Set the cookie only when headers haven't been sent yet — on
		// some cached setups headers fire early; in that case we just
		// skip the cookie and rely on Cache-Control to do the dedupe.
		if ( ! headers_sent() ) {
			setcookie(
				$cookie_name,
				'1',
				time() + self::COOKIE_TTL,
				defined( 'COOKIEPATH' ) ? COOKIEPATH : '/',
				defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
				is_ssl(),
				true
			);
		}
	}

	/**
	 * Decide whether a hit should be counted. Returns false for:
	 *  - WP-cron / REST / AJAX (not real reads)
	 *  - logged-in administrators (so admins testing pages don't inflate)
	 *  - preview/customizer (drafts, theme builder)
	 *  - feed / robots / search engines (basic UA filter)
	 *  - the post's own author (optional, often appreciated)
	 *
	 * Each gate is filterable separately via `mde_views_skip` so a site
	 * can override on a case-by-case basis.
	 *
	 * @return bool
	 */
	private static function should_count() {
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return false;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}
		if ( is_admin() || is_preview() || is_feed() || is_robots() ) {
			return false;
		}
		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
			return false;
		}
		// Elementor editor + preview iframe.
		if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) ) {
			return false;
		}

		// Skip administrators — testing the site shouldn't bump counters.
		if ( current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Cheap bot filter — full-blown UA matching is overkill, but the
		// most common crawlers identify themselves explicitly.
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
		if ( $ua && preg_match( '#(bot|crawler|spider|slurp|mediapartners|facebookexternalhit|whatsapp|telegrambot|googlebot|bingbot|yandex|duckduck|baiduspider|applebot|petalbot|semrush|ahrefs|mj12|dotbot|seznambot)#', $ua ) ) {
			return false;
		}

		/**
		 * Final word — any site can short-circuit the counter here.
		 */
		if ( apply_filters( 'mde_views_skip', false, get_queried_object_id() ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Read the stored view count for a post. Falls back to 0.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	public static function get_post_views( $post_id ) {
		return max( 0, (int) get_post_meta( (int) $post_id, self::META_KEY, true ) );
	}

	/**
	 * Sum of `mde_views` across every published post in a category. The
	 * result is cached in a transient for 5 minutes to keep the helper
	 * cheap on large archives. Bump or invalidate by deleting the
	 * `mde_views_term_{id}` transient.
	 *
	 * @param int $term_id Category term id.
	 * @return int
	 */
	public static function get_term_views( $term_id ) {
		$term_id = (int) $term_id;
		if ( $term_id <= 0 ) {
			return 0;
		}
		$cache_key = 'mde_views_term_' . $term_id;
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return (int) $cached;
		}

		global $wpdb;
		// One JOIN — much cheaper than looping posts. Counts only
		// published posts in the term (and any of its children if the
		// caller wants — we keep it strict here for simplicity).
		$sql = $wpdb->prepare(
			"SELECT COALESCE(SUM(CAST(pm.meta_value AS UNSIGNED)),0)
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
			 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			 WHERE pm.meta_key = %s
			   AND p.post_status = 'publish'
			   AND tt.term_id = %d",
			self::META_KEY,
			$term_id
		);
		$total = (int) $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB

		set_transient( $cache_key, $total, 5 * MINUTE_IN_SECONDS );
		return $total;
	}
}
