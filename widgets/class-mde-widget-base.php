<?php
/**
 * Base class for every section widget. Provides the shared widget category,
 * the colour/typography/spacing controls, and a scoped wrapper that carries
 * the CSS-variable palette so the ported design system theme-switches live.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;

abstract class MDE_Widget_Base extends Widget_Base {

	use MDE_Style_Controls;

	/**
	 * All widgets live under the single "مرکز نشر دستغیب" category.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'dastgheib' );
	}

	/**
	 * Shared keywords for the editor search.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'dastgheib', 'دستغیب', 'مرکز نشر', 'markaz', 'tafsir', 'تفسیر' );
	}

	/**
	 * Frontend script handle dependency.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'mde-scripts' );
	}

	/**
	 * Style handle dependency.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'mde-styles' );
	}

	/**
	 * Open the scoped wrapper. The .mde-scope element is the CSS-variable
	 * boundary the palette controls write to, so the whole section recolours.
	 *
	 * @param string $extra_class Additional class for the inner section.
	 */
	protected function open_scope( $extra_class = '' ) {
		echo '<div class="mde-scope ' . esc_attr( $extra_class ) . '" dir="rtl">';
		echo '<div class="mde-container">';
	}

	/**
	 * Close the scoped wrapper.
	 */
	protected function close_scope() {
		echo '</div></div>';
	}

	/**
	 * Convenience: render a standard section heading (eyebrow + title + sub +
	 * optional link). Used by most home/archive sections.
	 *
	 * @param array $a Heading args.
	 */
	protected function section_head( $a ) {
		$a = wp_parse_args(
			$a,
			array(
				'title'      => '',
				'sub'        => '',
				'link_text'  => '',
				'link_url'   => '',
			)
		);
		echo '<div class="mde-section-head">';
		echo '<div>';
		echo '<h2 class="mde-section-head__title"><span class="mde-eyebrow-mark"></span>' . esc_html( $a['title'] ) . '</h2>';
		if ( $a['sub'] ) {
			echo '<p class="mde-section-head__sub">' . esc_html( $a['sub'] ) . '</p>';
		}
		echo '</div>';
		if ( $a['link_text'] ) {
			$url = $a['link_url'] ? $a['link_url'] : '#';
			echo '<a class="mde-section-head__link" href="' . esc_url( $url ) . '">' . esc_html( $a['link_text'] ) . ' ' . MDE_Helpers::icon( 'chevl', 16 ) . '</a>'; // phpcs:ignore
		}
		echo '</div>';
	}
}
