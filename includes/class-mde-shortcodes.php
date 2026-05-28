<?php
/**
 * Shortcodes shipped with the plugin so the old TIE/Jannah extensions
 * plugin can be deactivated. Right now we just register `[box]`, which
 * appears thousands of times across the existing post archive. The
 * shortcode renders our themed pill chip + optional inline SVG icon —
 * no Font Awesome dependency.
 *
 *   [box type="success" align="aligncenter" icon="telegram"]تلگرام[/box]
 *
 * `type`  : success | info | warning | error | danger | note | download
 * `align` : aligncenter | alignright | alignleft  (or empty)
 * `class` : extra class names appended to the wrapper
 * `width` : explicit inline width (e.g. "200px")
 * `icon`  : optional override — a key from MDE_Helpers::icon()
 *           (share, play, eye, check, calendar, download, …).
 *           Omit to use the default icon for the type, or pass
 *           `icon="none"` to suppress the icon entirely.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MDE_Shortcodes {

	public static function boot() {
		add_shortcode( 'box', array( __CLASS__, 'box' ) );
	}

	/** Default icon per box type — overridden by the `icon` attribute. */
	private static $default_icons = array(
		'success'  => 'check',
		'info'     => 'info',
		'warning'  => 'warning',
		'error'    => 'close',
		'danger'   => 'warning',
		'note'     => 'none',
		'download' => 'download',
		'play'     => 'play',
	);

	/**
	 * Render `[box]...[/box]`.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Inner content (will be do_shortcode'd).
	 * @return string
	 */
	public static function box( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'type'  => 'success',
				'align' => '',
				'class' => '',
				'width' => '',
				'icon'  => '',
			),
			$atts,
			'box'
		);

		$type  = sanitize_html_class( $atts['type'] );
		$align = sanitize_html_class( $atts['align'] );
		$extra = sanitize_text_field( $atts['class'] );
		$width = sanitize_text_field( $atts['width'] );

		// Resolve icon — explicit attr > default for type > none.
		$icon_key = $atts['icon'];
		if ( '' === $icon_key && isset( self::$default_icons[ $type ] ) ) {
			$icon_key = self::$default_icons[ $type ];
		}
		$icon_html = '';
		if ( '' !== $icon_key && 'none' !== $icon_key ) {
			$icon_html = '<span class="mde-box-icon" aria-hidden="true">' . MDE_Helpers::icon( $icon_key, 14 ) . '</span>';
		}

		// Build class list — both legacy `.box` (so the user's existing
		// custom CSS keeps working if they want) and our scoped
		// `.mde-box` (which the plugin styles directly).
		$classes = array( 'mde-box', 'box', 'mde-box--' . $type, $type );
		if ( $align ) {
			$classes[] = 'mde-box--' . $align;
			$classes[] = $align;
		}
		if ( $extra ) {
			$classes[] = $extra;
		}
		$cls = implode( ' ', array_unique( array_filter( $classes ) ) );

		$style = $width ? ' style="width:' . esc_attr( $width ) . ';max-width:100%;"' : '';

		// Run shortcodes + autop on the inner content so embedded media
		// behaves the same as in the post body.
		$inner = do_shortcode( shortcode_unautop( wpautop( $content ) ) );
		// Strip the outermost <p> so the chip stays single-line.
		$inner = preg_replace( '#^\s*<p>(.*?)</p>\s*$#s', '$1', trim( $inner ) );

		$html  = '<div class="' . esc_attr( $cls ) . '"' . $style . '>';
		$html .= $icon_html;
		$html .= '<span class="mde-box-text">' . $inner . '</span>';
		$html .= '</div>';

		return $html;
	}
}
