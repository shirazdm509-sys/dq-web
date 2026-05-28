<?php
/**
 * Footer widget — brand blurb, link columns, social, newsletter.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

class MDE_Widget_Footer extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-footer';
	}

	public function get_title() {
		return __( 'فوتر', 'mde' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	protected function register_controls() {

		$this->start_controls_section( 'sec_brand', array( 'label' => __( 'برند', 'mde' ) ) );
		$this->add_control(
			'brand_title',
			array(
				'label'   => __( 'عنوان', 'mde' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'مرکز نشر آثار و اندیشه‌ها', 'mde' ),
			)
		);
		$this->add_control(
			'brand_text',
			array(
				'label'   => __( 'توضیح', 'mde' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'مرکزی برای نشر آثار، سخنرانی‌ها و تفاسیر قرآن کریم حضرت آیت‌الله العظمی سید علی‌محمد دستغیب «دامت برکاته».', 'mde' ),
			)
		);
		$rep = new Repeater();
		$rep->add_control( 'network', array(
			'label'   => __( 'شبکه', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'telegram',
			'options' => array(
				'telegram'  => __( 'تلگرام', 'mde' ),
				'instagram' => __( 'اینستاگرام', 'mde' ),
				'youtube'   => __( 'یوتیوب', 'mde' ),
				'aparat'    => __( 'آپارات', 'mde' ),
				'twitter'   => __( 'توییتر / X', 'mde' ),
				'whatsapp'  => __( 'واتساپ', 'mde' ),
				'facebook'  => __( 'فیسبوک', 'mde' ),
				'linkedin'  => __( 'لینکدین', 'mde' ),
				'rubika'    => __( 'روبیکا', 'mde' ),
				'eitaa'     => __( 'ایتا', 'mde' ),
				'bale'      => __( 'بله', 'mde' ),
				'soroush'   => __( 'سروش', 'mde' ),
				'email'     => __( 'ایمیل', 'mde' ),
				'phone'     => __( 'تلفن', 'mde' ),
				'website'   => __( 'وب‌سایت', 'mde' ),
				'rss'       => __( 'RSS', 'mde' ),
				'custom'    => __( 'دلخواه (آپلود تصویر)', 'mde' ),
			),
		) );
		$rep->add_control( 'custom_icon', array(
			'label'       => __( 'تصویر آیکن (SVG/PNG)', 'mde' ),
			'type'        => Controls_Manager::MEDIA,
			'condition'   => array( 'network' => 'custom' ),
			'description' => __( 'برای شبکه‌ای که در لیست بالا نیست، خودتان یک تصویر آیکن آپلود کنید (ترجیحاً SVG).', 'mde' ),
		) );
		$rep->add_control( 'label', array(
			'label'       => __( 'برچسب / متن دسترس‌پذیری', 'mde' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => __( 'مثلاً: کانال تلگرام', 'mde' ),
		) );
		$rep->add_control( 'url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
		$this->add_control(
			'socials',
			array(
				'label'       => __( 'شبکه‌های اجتماعی', 'mde' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array( 'network' => 'telegram',  'label' => 'تلگرام' ),
					array( 'network' => 'instagram', 'label' => 'اینستاگرام' ),
					array( 'network' => 'youtube',   'label' => 'یوتیوب' ),
					array( 'network' => 'aparat',    'label' => 'آپارات' ),
					array( 'network' => 'whatsapp',  'label' => 'واتساپ' ),
				),
				'title_field' => '{{{ label || network }}}',
			)
		);
		$this->end_controls_section();

		// Two link columns.
		foreach ( array( 1, 2 ) as $col ) {
			$this->start_controls_section(
				'sec_col' . $col,
				array( 'label' => sprintf( __( 'ستون پیوند %d', 'mde' ), $col ) )
			);
			$this->add_control(
				'col' . $col . '_title',
				array(
					'label'   => __( 'عنوان ستون', 'mde' ),
					'type'    => Controls_Manager::TEXT,
					'default' => 1 === $col ? __( 'دسته‌بندی‌ها', 'mde' ) : __( 'پیوندها', 'mde' ),
				)
			);
			$lr = new Repeater();
			$lr->add_control( 'text', array( 'label' => __( 'متن', 'mde' ), 'type' => Controls_Manager::TEXT ) );
			$lr->add_control( 'url', array( 'label' => __( 'پیوند', 'mde' ), 'type' => Controls_Manager::URL ) );
			$this->add_control(
				'col' . $col . '_links',
				array(
					'label'       => __( 'پیوندها', 'mde' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $lr->get_controls(),
					'default'     => 1 === $col
						? array(
							array( 'text' => __( 'تفسیر قرآن', 'mde' ) ),
							array( 'text' => __( 'رمضان المبارک', 'mde' ) ),
							array( 'text' => __( 'محرم الحرام', 'mde' ) ),
							array( 'text' => __( 'شهدا', 'mde' ) ),
							array( 'text' => __( 'رفیق شفیق', 'mde' ) ),
						)
						: array(
							array( 'text' => __( 'پخش زنده', 'mde' ) ),
							array( 'text' => __( 'فروشگاه کتاب', 'mde' ) ),
							array( 'text' => __( 'ارسال سؤال', 'mde' ) ),
							array( 'text' => __( 'آرشیو صوت', 'mde' ) ),
							array( 'text' => __( 'درباره ما', 'mde' ) ),
						),
					'title_field' => '{{{ text }}}',
				)
			);
			$this->end_controls_section();
		}

		$this->start_controls_section( 'sec_news', array( 'label' => __( 'خبرنامه و کپی‌رایت', 'mde' ) ) );
		$this->add_control( 'news_title', array( 'label' => __( 'عنوان خبرنامه', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'عضویت در خبرنامه', 'mde' ) ) );
		$this->add_control( 'news_text', array( 'label' => __( 'توضیح خبرنامه', 'mde' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'آخرین مطالب و جلسات تفسیر را در ایمیل خود دریافت کنید.', 'mde' ) ) );
		$this->add_control( 'news_btn', array( 'label' => __( 'متن دکمه', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'عضویت', 'mde' ) ) );
		$this->add_control( 'copy', array( 'label' => __( 'متن کپی‌رایت', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => __( '© ۱۴۰۵ — کلیه حقوق برای مرکز نشر آثار آیت‌الله دستغیب محفوظ است.', 'mde' ) ) );
		$this->add_control( 'domain', array( 'label' => __( 'دامنه', 'mde' ), 'type' => Controls_Manager::TEXT, 'default' => 'dastgheibqoba.info' ) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="mde-scope mde-footer" dir="rtl"><div class="mde-container">';

		echo '<div class="mde-footer-col">';
		echo '<h4>' . esc_html( $s['brand_title'] ) . '</h4>';
		echo '<p>' . esc_html( $s['brand_text'] ) . '</p>';
		echo '<div class="mde-social">';
		foreach ( (array) $s['socials'] as $so ) {
			$network = isset( $so['network'] ) ? $so['network'] : 'website';
			$u       = ! empty( $so['url']['url'] ) ? $so['url']['url'] : '#';
			$label   = ! empty( $so['label'] ) ? $so['label'] : self::social_label( $network );
			$target  = ! empty( $so['url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
			echo '<a class="mde-social__link mde-social__link--' . esc_attr( $network ) . '" href="' . esc_url( $u ) . '"' . $target . ' aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '">'; // phpcs:ignore
			if ( 'custom' === $network && ! empty( $so['custom_icon']['url'] ) ) {
				echo '<img src="' . esc_url( $so['custom_icon']['url'] ) . '" alt="" />';
			} else {
				echo self::social_icon( $network ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</a>';
		}
		echo '</div></div>';

		foreach ( array( 1, 2 ) as $col ) {
			echo '<div class="mde-footer-col"><h4>' . esc_html( $s[ 'col' . $col . '_title' ] ) . '</h4><ul>';
			foreach ( (array) $s[ 'col' . $col . '_links' ] as $l ) {
				$u = ! empty( $l['url']['url'] ) ? $l['url']['url'] : '#';
				echo '<li><a href="' . esc_url( $u ) . '">' . esc_html( $l['text'] ) . '</a></li>';
			}
			echo '</ul></div>';
		}

		echo '<div class="mde-footer-col"><h4>' . esc_html( $s['news_title'] ) . '</h4>';
		echo '<p style="margin-bottom:14px;">' . esc_html( $s['news_text'] ) . '</p>';
		echo '<form class="mde-newsletter" onsubmit="return false;"><input type="email" placeholder="' . esc_attr__( 'ایمیل شما', 'mde' ) . '" /><button class="mde-btn mde-btn--primary" style="height:40px;padding:0 16px;font-size:13px;">' . esc_html( $s['news_btn'] ) . '</button></form>';
		echo '</div>';

		echo '</div><div class="mde-footer-bottom"><span>' . esc_html( $s['copy'] ) . '</span><span>' . esc_html( $s['domain'] ) . '</span></div>';
		echo '</div>';
	}

	/**
	 * Inline SVG icon for a given social network. All icons share the same
	 * 24×24 viewBox so they render at a consistent size in the .mde-social
	 * pill — the CSS sizes the wrapper, not the SVG.
	 *
	 * @param string $name Network slug.
	 * @return string SVG markup.
	 */
	private static function social_icon( $name ) {
		$icons = array(
			'telegram'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.665 3.717l-17.73 6.837c-1.21.486-1.203 1.161-.222 1.462l4.552 1.42 10.532-6.642c.498-.302.953-.14.579.192l-8.533 7.706h-.002l.002.001-.314 4.692c.46 0 .663-.211.921-.46l2.211-2.15 4.599 3.397c.848.467 1.457.227 1.668-.787l3.019-14.228c.309-1.239-.473-1.8-1.282-1.44z"/></svg>',
			'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
			'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.5a3 3 0 0 0-2.11-2.12C19.5 4 12 4 12 4s-7.5 0-9.39.38A3 3 0 0 0 .5 6.5C.12 8.38.12 12 .12 12s0 3.62.38 5.5a3 3 0 0 0 2.11 2.12C4.5 20 12 20 12 20s7.5 0 9.39-.38a3 3 0 0 0 2.11-2.12c.38-1.88.38-5.5.38-5.5s0-3.62-.38-5.5zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>',
			'aparat'    => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 4.6a1.6 1.6 0 1 1-1.6 1.6A1.6 1.6 0 0 1 12 6.6zm-5.4 5.4a1.6 1.6 0 1 1 1.6 1.6 1.6 1.6 0 0 1-1.6-1.6zm5.4 5.4a1.6 1.6 0 1 1 1.6-1.6 1.6 1.6 0 0 1-1.6 1.6zm5.4-5.4a1.6 1.6 0 1 1-1.6-1.6 1.6 1.6 0 0 1 1.6 1.6z"/></svg>',
			'twitter'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2H21l-6.51 7.44L22 22h-6.797l-4.79-6.27L4.8 22H2l7.02-8.02L1.5 2h6.95l4.33 5.74L18.244 2zm-1.193 18.4h1.83L7.07 3.5H5.13L17.05 20.4z"/></svg>',
			'whatsapp'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.49 0 .15 5.34.15 11.92a11.9 11.9 0 0 0 1.6 5.97L0 24l6.27-1.64a11.92 11.92 0 0 0 5.78 1.48h.01c6.57 0 11.91-5.34 11.91-11.92a11.86 11.86 0 0 0-3.45-8.44zM12.06 21.8h-.01a9.86 9.86 0 0 1-5.03-1.38l-.36-.21-3.72.97 1-3.63-.23-.37a9.84 9.84 0 0 1-1.51-5.25c0-5.46 4.44-9.9 9.9-9.9a9.86 9.86 0 0 1 9.9 9.91c0 5.46-4.44 9.9-9.9 9.9zm5.43-7.41c-.3-.15-1.76-.87-2.03-.97s-.47-.15-.67.15-.77.97-.95 1.17-.35.22-.65.07a8.13 8.13 0 0 1-2.4-1.48 8.96 8.96 0 0 1-1.66-2.06c-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52a2 2 0 0 0 .3-.5.55.55 0 0 0-.02-.52c-.07-.15-.67-1.62-.92-2.22s-.49-.5-.67-.51l-.57-.01a1.1 1.1 0 0 0-.8.37 3.35 3.35 0 0 0-1.04 2.49c0 1.47 1.07 2.89 1.22 3.09s2.1 3.2 5.1 4.49a17.13 17.13 0 0 0 1.7.63 4.08 4.08 0 0 0 1.88.12c.57-.09 1.76-.72 2.01-1.41a2.48 2.48 0 0 0 .17-1.41c-.07-.13-.27-.2-.57-.35z"/></svg>',
			'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-7H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.27c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.45 2.88h-2.33v7A10 10 0 0 0 22 12z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.13 1.45-2.13 2.94v5.67H9.35V9h3.42v1.56h.05a3.75 3.75 0 0 1 3.37-1.85c3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.07 2.07 0 1 1 2.06-2.07 2.06 2.06 0 0 1-2.06 2.07zM7.12 20.45H3.55V9h3.57v11.45zM22.23 0H1.77A1.75 1.75 0 0 0 0 1.73v20.54A1.75 1.75 0 0 0 1.77 24h20.45A1.75 1.75 0 0 0 24 22.27V1.73A1.75 1.75 0 0 0 22.23 0z"/></svg>',
			'rubika'    => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm3.5 13.5L12 13l-3.5 2.5 1.3-4-3.3-2.5h4.1L12 5l1.4 4h4.1l-3.3 2.5z"/></svg>',
			'eitaa'     => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm5 13.5a1 1 0 0 1-1.5.9l-3.5-2-3.5 2a1 1 0 0 1-1.5-.9V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v8.5z"/></svg>',
			'bale'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm-.4 4.25l-7.18 4.5a.9.9 0 0 1-.84 0L4.4 8.25a.6.6 0 1 1 .64-1l6.96 4.37 6.96-4.37a.6.6 0 1 1 .64 1z"/></svg>',
			'soroush'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.86.51 3.6 1.4 5.09L2 22l4.91-1.4A9.92 9.92 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm4.5 13.5h-9v-1.5h9v1.5zm0-3h-9V11h9v1.5zm0-3h-9V8h9v1.5z"/></svg>',
			'email'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
			'phone'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.96.34 1.9.65 2.81a2 2 0 0 1-.45 2.11L8.1 9.9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.31 1.85.53 2.81.65A2 2 0 0 1 22 16.92z"/></svg>',
			'website'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>',
			'rss'       => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20A2.18 2.18 0 0 1 4 17.82a2.18 2.18 0 0 1 2.18-2.18zM4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44zm0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93v-2.83z"/></svg>',
		);
		return isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['website'];
	}

	/**
	 * Human-readable label fallback (used as the aria-label / title when
	 * the user didn't fill in a custom label).
	 *
	 * @param string $name Network slug.
	 * @return string
	 */
	private static function social_label( $name ) {
		$labels = array(
			'telegram'  => 'تلگرام',
			'instagram' => 'اینستاگرام',
			'youtube'   => 'یوتیوب',
			'aparat'    => 'آپارات',
			'twitter'   => 'توییتر',
			'whatsapp'  => 'واتساپ',
			'facebook'  => 'فیسبوک',
			'linkedin'  => 'لینکدین',
			'rubika'    => 'روبیکا',
			'eitaa'     => 'ایتا',
			'bale'      => 'بله',
			'soroush'   => 'سروش',
			'email'     => 'ایمیل',
			'phone'     => 'تلفن',
			'website'   => 'وب‌سایت',
			'rss'       => 'RSS',
		);
		return isset( $labels[ $name ] ) ? $labels[ $name ] : $name;
	}
}
