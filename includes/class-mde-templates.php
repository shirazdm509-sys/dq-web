<?php
/**
 * One-click page importer. Builds ready-made Elementor pages (Home, Single
 * Article, Category, Live) out of the plugin's own widgets, arranged like the
 * original prototype. Everything stays fully editable in Elementor afterwards.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MDE_Templates {

	/**
	 * Singleton.
	 *
	 * @var MDE_Templates|null
	 */
	private static $instance = null;

	/**
	 * @return MDE_Templates
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_post_mde_import', array( $this, 'handle_import' ) );
	}

	/**
	 * Admin page under the Elementor menu.
	 */
	public function menu() {
		add_submenu_page(
			'elementor',
			__( 'قالب‌های مرکز نشر دستغیب', 'mde' ),
			__( 'قالب‌های دستغیب', 'mde' ),
			'edit_pages',
			'mde-templates',
			array( $this, 'render_page' )
		);
	}

	/**
	 * The available ready pages.
	 *
	 * @return array<string,string>
	 */
	private function pages() {
		return array(
			'home'     => __( 'صفحه اصلی', 'mde' ),
			'article'  => __( 'تک‌مقاله (الگوی نوشته)', 'mde' ),
			'category' => __( 'دسته‌بندی / آرشیو', 'mde' ),
			'live'     => __( 'پخش زنده', 'mde' ),
		);
	}

	/**
	 * Admin UI.
	 */
	public function render_page() {
		$done = isset( $_GET['mde_done'] ) ? sanitize_text_field( wp_unslash( $_GET['mde_done'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$pid  = isset( $_GET['pid'] ) ? (int) $_GET['pid'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="wrap" dir="rtl" style="max-width:760px;">';
		echo '<h1>' . esc_html__( 'قالب‌های آماده‌ی مرکز نشر دستغیب', 'mde' ) . '</h1>';
		echo '<p>' . esc_html__( 'با یک کلیک، صفحه‌ی آماده و چیده‌شده ساخته می‌شود. سپس می‌توانید همه‌چیز را در المنتور ویرایش کنید.', 'mde' ) . '</p>';

		if ( $done && $pid ) {
			echo '<div class="notice notice-success"><p>';
			printf(
				/* translators: 1: page title, 2: edit link, 3: view link */
				wp_kses_post( __( 'صفحه‌ی «%1$s» ساخته شد. <a href="%2$s">ویرایش با المنتور</a> · <a href="%3$s" target="_blank">مشاهده</a>', 'mde' ) ),
				esc_html( $this->pages()[ $done ] ?? '' ),
				esc_url( admin_url( 'post.php?post=' . $pid . '&action=elementor' ) ),
				esc_url( get_permalink( $pid ) )
			);
			echo '</p></div>';
		}

		echo '<table class="widefat striped" style="margin-top:16px;"><tbody>';
		foreach ( $this->pages() as $slug => $label ) {
			$url = wp_nonce_url(
				admin_url( 'admin-post.php?action=mde_import&page_key=' . $slug ),
				'mde_import_' . $slug
			);
			echo '<tr><td style="padding:16px;font-weight:600;">' . esc_html( $label ) . '</td>';
			echo '<td style="padding:16px;text-align:left;"><a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'ساخت صفحه', 'mde' ) . '</a></td></tr>';
		}
		echo '</tbody></table>';

		echo '<h2 style="margin-top:28px;">' . esc_html__( 'راهنما', 'mde' ) . '</h2><ul style="list-style:disc;padding-right:20px;line-height:2;">';
		echo '<li>' . esc_html__( 'برای منوی هدر، ابتدا از «نمایش ← فهرست‌ها» یک منو بسازید و در ویجت هدر انتخاب کنید.', 'mde' ) . '</li>';
		echo '<li>' . esc_html__( 'هر ویجت یک کنترل «دسته» دارد؛ مقالات و دسته‌ها مستقیماً از وردپرس خوانده می‌شوند.', 'mde' ) . '</li>';
		echo '<li>' . esc_html__( 'رنگ، فونت، لوگو، تصاویر و همه‌ی متن‌ها از پنل المنتور قابل تغییر است.', 'mde' ) . '</li>';
		echo '<li>' . esc_html__( '«تک‌مقاله» را می‌توانید در سازنده‌ی قالب المنتور به‌عنوان الگوی Single استفاده کنید.', 'mde' ) . '</li>';
		echo '</ul></div>';
	}

	/**
	 * Create the page + inject Elementor data.
	 */
	public function handle_import() {
		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز', 'mde' ) );
		}
		$key = isset( $_GET['page_key'] ) ? sanitize_key( wp_unslash( $_GET['page_key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		check_admin_referer( 'mde_import_' . $key );

		$pages = $this->pages();
		if ( ! isset( $pages[ $key ] ) ) {
			wp_die( esc_html__( 'صفحه نامعتبر', 'mde' ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'  => $pages[ $key ],
			'post_status' => 'publish',
			'post_type'   => 'page',
		) );

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			wp_die( esc_html__( 'ایجاد صفحه ناموفق بود', 'mde' ) );
		}

		$data = $this->build( $key );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
		update_post_meta( $post_id, '_wp_page_template', 'elementor_canvas' );
		// Elementor stores data slash-escaped JSON.
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );

		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		wp_safe_redirect( admin_url( 'admin.php?page=mde-templates&mde_done=' . $key . '&pid=' . $post_id ) );
		exit;
	}

	/**
	 * Short random Elementor element id.
	 *
	 * @return string
	 */
	private function id() {
		return substr( md5( uniqid( '', true ) ), 0, 7 );
	}

	/**
	 * Wrap a list of widgets into one full-width section/column.
	 *
	 * @param array $widgets Widget element arrays.
	 * @param array $sec_settings Section settings.
	 * @return array
	 */
	private function section( $widgets, $sec_settings = array() ) {
		return array(
			'id'       => $this->id(),
			'elType'   => 'section',
			'settings' => array_merge(
				array( 'layout' => 'full_width', 'gap' => 'no' ),
				$sec_settings
			),
			'elements' => array(
				array(
					'id'       => $this->id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100, '_inline_size' => null ),
					'elements' => $widgets,
				),
			),
			'isInner'  => false,
		);
	}

	/**
	 * A two-column inner row (content + sidebar) inside a boxed section.
	 *
	 * @param array $main Main column widgets.
	 * @param array $side Side column widgets.
	 * @return array
	 */
	private function two_col( $main, $side ) {
		return array(
			'id'       => $this->id(),
			'elType'   => 'section',
			'settings' => array( 'structure' => '33', 'content_width' => 'boxed' ),
			'elements' => array(
				array(
					'id'       => $this->id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 66, '_inline_size' => 66 ),
					'elements' => $main,
				),
				array(
					'id'       => $this->id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 34, '_inline_size' => 34 ),
					'elements' => $side,
				),
			),
			'isInner'  => false,
		);
	}

	/**
	 * One widget element.
	 *
	 * @param string $type Widget type (get_name()).
	 * @return array
	 */
	private function w( $type ) {
		return array(
			'id'         => $this->id(),
			'elType'     => 'widget',
			'settings'   => array(),
			'widgetType' => $type,
			'elements'   => array(),
		);
	}

	/**
	 * Compose the Elementor element tree for each page.
	 *
	 * @param string $key Page key.
	 * @return array
	 */
	private function build( $key ) {
		switch ( $key ) {
			case 'home':
				return array(
					$this->section( array( $this->w( 'mde-header' ) ) ),
					$this->section( array( $this->w( 'mde-hero' ) ) ),
					$this->section( array( $this->w( 'mde-stats-strip' ) ) ),
					$this->section( array( $this->w( 'mde-posts-grid' ) ) ),
					$this->section( array( $this->w( 'mde-photo-posts' ) ) ),
					$this->section( array( $this->w( 'mde-video-clips' ) ) ),
					$this->section( array( $this->w( 'mde-cta-strip' ) ) ),
					$this->section( array( $this->w( 'mde-martyrs' ) ) ),
					$this->section( array( $this->w( 'mde-audio-archive' ) ) ),
					$this->section( array( $this->w( 'mde-footer' ) ) ),
				);

			case 'article':
				return array(
					$this->section( array( $this->w( 'mde-header' ) ) ),
					$this->two_col(
						array( $this->w( 'mde-single-article' ) ),
						array( $this->w( 'mde-article-tools' ) )
					),
					$this->section( array( $this->w( 'mde-related-posts' ) ) ),
					$this->section( array( $this->w( 'mde-footer' ) ) ),
				);

			case 'category':
				return array(
					$this->section( array( $this->w( 'mde-header' ) ) ),
					$this->section( array( $this->w( 'mde-category-header' ) ) ),
					$this->section( array( $this->w( 'mde-category-archive' ) ) ),
					$this->section( array( $this->w( 'mde-footer' ) ) ),
				);

			case 'live':
				return array(
					$this->section( array( $this->w( 'mde-header' ) ) ),
					$this->two_col(
						array( $this->w( 'mde-live-player' ), $this->w( 'mde-live-schedule' ) ),
						array( $this->w( 'mde-live-latest' ) )
					),
					$this->section( array( $this->w( 'mde-footer' ) ) ),
				);
		}
		return array();
	}
}
