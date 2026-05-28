<?php
/**
 * Core plugin: registers the Elementor widget category, all widgets, and assets.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MDE_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var MDE_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Widget file slug => class name. One widget per visual section.
	 *
	 * @var array<string,string>
	 */
	private $widgets = array(
		// Shell.
		'header'           => 'MDE_Widget_Header',
		'footer'           => 'MDE_Widget_Footer',
		// Home sections.
		'hero'             => 'MDE_Widget_Hero',
		'stats-strip'      => 'MDE_Widget_Stats_Strip',
		'posts-grid'       => 'MDE_Widget_Posts_Grid',
		'photo-posts'      => 'MDE_Widget_Photo_Posts',
		'video-clips'      => 'MDE_Widget_Video_Clips',
		'cta-strip'        => 'MDE_Widget_Cta_Strip',
		'martyrs'          => 'MDE_Widget_Martyrs',
		'audio-archive'    => 'MDE_Widget_Audio_Archive',
		// Single article sections.
		'single-article'   => 'MDE_Widget_Single_Article',
		'article-tools'    => 'MDE_Widget_Article_Tools',
		'related-posts'    => 'MDE_Widget_Related_Posts',
		'article-sidebar'  => 'MDE_Widget_Article_Sidebar',
		// Category sections.
		'category-header'  => 'MDE_Widget_Category_Header',
		'category-archive' => 'MDE_Widget_Category_Archive',
		// Live sections.
		'live-player'      => 'MDE_Widget_Live_Player',
		'live-schedule'    => 'MDE_Widget_Live_Schedule',
		'live-latest'      => 'MDE_Widget_Live_Latest',
		// Payment page sections.
		'payment-hero'        => 'MDE_Widget_Payment_Hero',
		'payment-purposes'    => 'MDE_Widget_Payment_Purposes',
		'payment-bank-cards'  => 'MDE_Widget_Payment_Bank_Cards',
		'payment-trust-strip' => 'MDE_Widget_Payment_Trust_Strip',
		'payment-help-box'    => 'MDE_Widget_Payment_Help_Box',
		'payment-form'        => 'MDE_Widget_Payment_Form',
	);

	/**
	 * Instance accessor.
	 *
	 * @return MDE_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook everything up.
	 */
	private function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Make sure assets are present inside the editor preview too.
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// AJAX endpoints (used by the posts-grid chip filter).
		add_action( 'wp_ajax_mde_pg_filter', array( $this, 'ajax_pg_filter' ) );
		add_action( 'wp_ajax_nopriv_mde_pg_filter', array( $this, 'ajax_pg_filter' ) );
	}

	/**
	 * Register the "مرکز نشر دستغیب" widget category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'dastgheib',
			array(
				'title' => __( 'مرکز نشر دستغیب', 'mde' ),
				'icon'  => 'eicon-site-identity',
			)
		);
	}

	/**
	 * Load + register every widget.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once MDE_PATH . 'widgets/class-mde-widget-base.php';

		foreach ( $this->widgets as $slug => $class ) {
			$file = MDE_PATH . 'widgets/class-mde-widget-' . $slug . '.php';
			if ( file_exists( $file ) ) {
				require_once $file;
				if ( class_exists( $class ) ) {
					$widgets_manager->register( new $class() );
				}
			}
		}
	}

	/**
	 * Front-end stylesheet (ported design system + all sections).
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'mde-fonts', 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap', array(), MDE_VERSION );
		wp_enqueue_style( 'mde-styles', MDE_ASSETS . 'css/mde.css', array(), MDE_VERSION );
	}

	/**
	 * Register the behaviour script (slider/tabs/player/progress/reveal/etc.).
	 */
	public function register_scripts() {
		wp_register_script( 'mde-scripts', MDE_ASSETS . 'js/mde.js', array(), MDE_VERSION, true );
		wp_localize_script(
			'mde-scripts',
			'mdeData',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'mde_pg_filter' ),
			)
		);
	}

	/**
	 * Enqueue the behaviour script.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'mde-scripts' );
	}

	/**
	 * AJAX handler: render a fresh batch of post cards for the posts-grid
	 * chip filter. Receives one term id and the widget's render args, returns
	 * the same .mde-pg-item markup the server would have rendered.
	 */
	public function ajax_pg_filter() {
		check_ajax_referer( 'mde_pg_filter', 'nonce' );

		// Sanitise inputs.
		$cat      = isset( $_POST['cat'] ) ? absint( $_POST['cat'] ) : 0;
		$cat_csv  = isset( $_POST['cats'] ) ? sanitize_text_field( wp_unslash( $_POST['cats'] ) ) : '';
		$count    = isset( $_POST['count'] ) ? max( 1, absint( $_POST['count'] ) ) : 6;
		$variant  = isset( $_POST['variant'] ) ? sanitize_key( wp_unslash( $_POST['variant'] ) ) : 'default';
		$badge    = isset( $_POST['badge'] ) ? sanitize_text_field( wp_unslash( $_POST['badge'] ) ) : '';
		$fallback = isset( $_POST['fallback'] ) ? esc_url_raw( wp_unslash( $_POST['fallback'] ) ) : '';

		// `cat` > 0 means single-category click; `cat` = 0 means "همه" — use stored selection.
		$category_arg = $cat > 0 ? $cat : $cat_csv;
		$q            = MDE_Helpers::query( array( 'category' => $category_arg, 'count' => $count ) );

		ob_start();
		if ( $q->have_posts() ) {
			$i = 0;
			while ( $q->have_posts() ) {
				$q->the_post();
				echo '<div class="mde-reveal is-in" data-delay="' . esc_attr( $i * 60 ) . '">';
				MDE_Helpers::card( array(
					'id'       => get_the_ID(),
					'variant'  => $variant,
					'badge'    => $badge,
					'fallback' => $fallback,
				) );
				echo '</div>';
				$i++;
			}
			wp_reset_postdata();
		} else {
			echo '<p class="mde-pg-empty" style="grid-column:1/-1;text-align:center;color:var(--c-muted);padding:40px 0;">' . esc_html__( 'مطلبی در این دسته یافت نشد.', 'mde' ) . '</p>';
		}
		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}
}
