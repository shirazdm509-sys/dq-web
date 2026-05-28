<?php
/**
 * Plugin Name:       مرکز نشر دستغیب — ویجت‌های المنتور
 * Plugin URI:        https://dastgheibqoba.info
 * Description:        مجموعه ویجت‌های اختصاصی المنتور برای سایت «مرکز نشر آثار و اندیشه‌های آیت‌الله دستغیب». شامل تمام بخش‌های صفحه اصلی، تک‌مقاله، دسته‌بندی و پخش زنده؛ کاملاً قابل تنظیم (فونت، رنگ، لوگو، تصاویر) با خواندن پویای مقالات، دسته‌ها و منو از وردپرس.
 * Version:           1.16.0
 * Author:            Markaz Nashr Dastgheib
 * Text Domain:       mde
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 6.0
 * Elementor tested up to: 3.25.0
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'MDE_VERSION', '1.16.0' );
define( 'MDE_FILE', __FILE__ );
define( 'MDE_PATH', plugin_dir_path( __FILE__ ) );
define( 'MDE_URL', plugin_dir_url( __FILE__ ) );
define( 'MDE_ASSETS', MDE_URL . 'assets/' );

/**
 * Bootstrap once Elementor is loaded; show an admin notice otherwise.
 */
function mde_bootstrap() {

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action(
			'admin_notices',
			function () {
				$msg = sprintf(
					/* translators: %s: Elementor plugin name */
					esc_html__( 'افزونه «مرکز نشر دستغیب» برای کار به %s نیاز دارد.', 'mde' ),
					'<strong>Elementor</strong>'
				);
				printf( '<div class="notice notice-warning"><p>%s</p></div>', wp_kses_post( $msg ) );
			}
		);
		return;
	}

	require_once MDE_PATH . 'includes/mde-helpers.php';
	require_once MDE_PATH . 'includes/trait-mde-style-controls.php';
	require_once MDE_PATH . 'includes/class-mde-plugin.php';
	require_once MDE_PATH . 'includes/class-mde-templates.php';
	require_once MDE_PATH . 'includes/class-mde-post-meta.php';
	require_once MDE_PATH . 'includes/class-mde-shortcodes.php';
	require_once MDE_PATH . 'includes/class-mde-views.php';

	MDE_Plugin::instance();
	MDE_Templates::instance();
	MDE_Post_Meta::boot();
	MDE_Shortcodes::boot();
	MDE_Views::boot();
}
add_action( 'plugins_loaded', 'mde_bootstrap' );
