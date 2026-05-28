<?php
/**
 * Single article — reading progress, breadcrumb, title/meta, the_content,
 * tags, share row and native comments. Uses the current queried post
 * (works on a single-post Elementor Theme Builder template).
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class MDE_Widget_Single_Article extends MDE_Widget_Base {

	public function get_name() {
		return 'mde-single-article';
	}

	public function get_title() {
		return __( 'محتوای تک‌مقاله', 'mde' );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

	protected function register_controls() {
		$this->start_controls_section( 'sec', array( 'label' => __( 'تنظیمات', 'mde' ) ) );
		$this->add_control( 'show_progress', array( 'label' => __( 'نوار پیشرفت مطالعه', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'show_breadcrumb', array( 'label' => __( 'مسیر راهنما', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'show_share', array( 'label' => __( 'نوار اشتراک‌گذاری', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'show_comments', array( 'label' => __( 'نظرات', 'mde' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'sec_media', array(
			'label' => __( 'آیه / صوت / ویدیو', 'mde' ),
		) );
		$this->add_control( 'media_intro', array(
			'type' => Controls_Manager::RAW_HTML,
			'raw'  => __( 'هر سه بخش از <b>«آیه و مدیا»</b> در ویرایش هر نوشته خوانده می‌شوند. اگر نوشته‌ای این فیلدها را پر نکرده باشد، چیزی نمایش داده نمی‌شود.', 'mde' ),
			'content_classes' => 'elementor-descriptor',
		) );
		$this->add_control( 'show_ayah', array(
			'label'   => __( 'نمایش کادر آیه شریفه', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_video', array(
			'label'   => __( 'نمایش ویدیو', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_audio', array(
			'label'   => __( 'نمایش پخش‌کننده‌ی صوت', 'mde' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'media_layout', array(
			'label'   => __( 'چینش بخش‌های مدیا', 'mde' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'stacked',
			'options' => array(
				'stacked' => __( 'پشت سر هم (پیش از متن)', 'mde' ),
				'tabs'    => __( 'تب‌بندی شده (متن/ویدیو/صوت)', 'mde' ),
			),
			'description' => __( 'حالت تب فقط وقتی فعال است که حداقل یکی از ویدیو یا صوت پر شده باشد.', 'mde' ),
		) );
		$this->add_control( 'ayah_label', array(
			'label'   => __( 'برچسب کادر آیه', 'mde' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'آیه شریفه', 'mde' ),
			'condition' => array( 'show_ayah' => 'yes' ),
		) );
		$this->add_control( 'audio_label', array(
			'label'   => __( 'برچسب پخش‌کننده صوت', 'mde' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'صوت جلسه', 'mde' ),
			'condition' => array( 'show_audio' => 'yes' ),
		) );
		$this->end_controls_section();

		$this->mde_register_style_controls( $this );
	}

	protected function render() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() && ! is_singular() ) {
			$q = MDE_Helpers::query( array( 'count' => 1 ) );
			if ( $q->have_posts() ) {
				$q->the_post();
			}
		}
		$s  = $this->get_settings_for_display();
		$id = get_the_ID();

		echo '<div class="mde-scope mde-page-enter" dir="rtl">';
		if ( 'yes' === $s['show_progress'] ) {
			echo '<div class="mde-progress"><div class="mde-progress__bar"></div></div>';
		}
		echo '<div class="mde-container">';

		if ( 'yes' === $s['show_breadcrumb'] ) {
			$cats = get_the_category( $id );
			echo '<nav class="mde-breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">' . MDE_Helpers::icon( 'home', 13 ) . esc_html__( 'خانه', 'mde' ) . '</a><span>/</span>'; // phpcs:ignore
			if ( ! empty( $cats ) ) {
				echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a><span>/</span>';
			}
			echo '<span style="color:var(--c-ink-2);">' . esc_html( wp_trim_words( get_the_title(), 6 ) ) . '</span></nav>';
		}

		echo '<article class="mde-article">';

		// Title + meta.
		echo '<div class="mde-reveal" style="margin-bottom:28px;">';
		$cats = get_the_category( $id );
		echo '<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">';
		foreach ( array_slice( $cats, 0, 3 ) as $idx => $c ) {
			echo '<span class="mde-chip ' . ( 0 === $idx ? 'is-active' : '' ) . '">' . esc_html( $c->name ) . '</span>';
		}
		echo '</div>';
		echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
		echo '<div class="mde-article__meta">';
		echo '<span><span class="av">' . esc_html( mb_substr( get_the_author(), 0, 1 ) ) . '</span><span style="color:var(--c-ink);font-weight:600;">' . esc_html( get_the_author() ) . '</span></span>';
		echo '<span>' . MDE_Helpers::icon( 'calendar', 14 ) . ' ' . esc_html( MDE_Helpers::date( $id ) ) . '</span>'; // phpcs:ignore
		echo '<span>' . MDE_Helpers::icon( 'eye', 14 ) . ' ' . esc_html( MDE_Helpers::fa( number_format_i18n( class_exists( 'MDE_Views' ) ? MDE_Views::get_post_views( $id ) : (int) get_post_meta( $id, 'mde_views', true ) ) ) ) . ' ' . esc_html__( 'بازدید', 'mde' ) . '</span>'; // phpcs:ignore
		echo '</div></div>';

		// Pull the per-post media meta. Anything not filled in stays empty
		// and produces no output below.
		$media = class_exists( 'MDE_Post_Meta' ) ? MDE_Post_Meta::get( $id ) : array(
			'ayah_text' => '', 'ayah_caption' => '', 'video_embed' => '',
			'audio_url' => '', 'audio_title' => '', 'audio_duration' => 0,
		);
		$has_ayah  = ( 'yes' === $s['show_ayah'] )  && '' !== trim( $media['ayah_text'] );
		$has_video = ( 'yes' === $s['show_video'] ) && '' !== trim( $media['video_embed'] );
		$has_audio = ( 'yes' === $s['show_audio'] ) && '' !== trim( $media['audio_url'] );
		$tabs      = ( 'tabs' === $s['media_layout'] ) && ( $has_video || $has_audio );

		// Ayah always renders above the tabs/content (matches the original
		// design where the verse "introduces" the discussion).
		if ( $has_ayah ) {
			$this->render_ayah( $media, $s );
		}

		// Tabs UI — only when at least one media is present.
		if ( $tabs ) {
			echo '<div class="mde-reveal"><div class="mde-tabs" style="margin-bottom:22px;">';
			echo '<button data-mde-tab="text" class="is-active">' . MDE_Helpers::icon( 'doc', 14 ) . ' ' . esc_html__( 'متن', 'mde' ) . '</button>'; // phpcs:ignore
			if ( $has_video ) {
				echo '<button data-mde-tab="video">' . MDE_Helpers::icon( 'video', 14 ) . ' ' . esc_html__( 'ویدیو', 'mde' ) . '</button>'; // phpcs:ignore
			}
			if ( $has_audio ) {
				echo '<button data-mde-tab="audio">' . MDE_Helpers::icon( 'phones', 14 ) . ' ' . esc_html__( 'صوت', 'mde' ) . '</button>'; // phpcs:ignore
			}
			echo '</div></div>';

			if ( $has_video ) {
				echo '<div data-mde-pane="video" style="display:none;">';
				$this->render_video( $media );
				echo '</div>';
			}
			if ( $has_audio ) {
				echo '<div data-mde-pane="audio" style="display:none;">';
				$this->render_audio( $media, $s );
				echo '</div>';
			}

			echo '<div data-mde-pane="text">';
			$this->render_body_content();
			echo '</div>';
		} else {
			// Stacked layout — video, audio, then content. Each block is
			// fully self-contained and is omitted when its meta is empty.
			if ( $has_video ) {
				$this->render_video( $media );
			}
			if ( $has_audio ) {
				$this->render_audio( $media, $s );
			}
			$this->render_body_content();
		}

		// Share.
		if ( 'yes' === $s['show_share'] ) {
			$pl = rawurlencode( get_permalink() );
			echo '<div class="mde-reveal"><div style="margin-top:36px;padding:20px;border-radius:14px;background:var(--c-surface);border:1px solid var(--c-border);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">';
			echo '<div><div style="font-weight:700;font-size:14px;">' . esc_html__( 'اشتراک‌گذاری این جلسه', 'mde' ) . '</div><div style="font-size:12.5px;color:var(--c-muted);">' . esc_html__( 'این مطلب را با دیگران به اشتراک بگذارید', 'mde' ) . '</div></div>';
			echo '<div style="display:flex;gap:8px;flex-wrap:wrap;">';
			echo '<a class="mde-chip" target="_blank" rel="noopener" href="https://t.me/share/url?url=' . $pl . '">' . MDE_Helpers::icon( 'share', 13 ) . ' ' . esc_html__( 'تلگرام', 'mde' ) . '</a>'; // phpcs:ignore
			echo '<a class="mde-chip" target="_blank" rel="noopener" href="https://wa.me/?text=' . $pl . '">' . MDE_Helpers::icon( 'share', 13 ) . ' ' . esc_html__( 'واتساپ', 'mde' ) . '</a>'; // phpcs:ignore
			echo '<a class="mde-chip" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=' . $pl . '">' . MDE_Helpers::icon( 'share', 13 ) . ' ' . esc_html__( 'توییتر', 'mde' ) . '</a>'; // phpcs:ignore
			echo '</div></div></div>';
		}

		// Comments.
		if ( 'yes' === $s['show_comments'] && ( is_singular() || \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
			echo '<div class="mde-reveal"><section style="margin-top:50px;">';
			if ( is_singular() ) {
				comments_template();
			} else {
				echo '<h3 style="font-size:20px;font-weight:700;">' . esc_html__( 'نظرات کاربران', 'mde' ) . '</h3><p style="color:var(--c-muted);">' . esc_html__( 'بخش نظرات در صفحه‌ی واقعی نمایش داده می‌شود.', 'mde' ) . '</p>';
			}
			echo '</section></div>';
		}

		echo '</article></div></div>';

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			wp_reset_postdata();
		}
	}

	/**
	 * Render the Ayah verse box.
	 *
	 * @param array $media Output of MDE_Post_Meta::get().
	 * @param array $s     Widget settings.
	 */
	private function render_ayah( $media, $s ) {
		$label = ! empty( $s['ayah_label'] ) ? $s['ayah_label'] : __( 'آیه شریفه', 'mde' );
		echo '<div class="mde-reveal"><div class="mde-ayah">';
		echo '<div class="mde-ayah__tag">' . esc_html( $label ) . '</div>';
		echo '<p class="ar" dir="rtl">' . nl2br( esc_html( $media['ayah_text'] ) ) . '</p>'; // phpcs:ignore
		if ( ! empty( $media['ayah_caption'] ) ) {
			echo '<p class="cap">' . esc_html( $media['ayah_caption'] ) . '</p>';
		}
		echo '</div></div>';
	}

	/**
	 * Render the video embed in the 16:9 media box.
	 *
	 * @param array $media Output of MDE_Post_Meta::get().
	 */
	private function render_video( $media ) {
		echo '<div class="mde-reveal"><div class="mde-media-box mde-single-media-box">';
		echo MDE_Post_Meta::render_video( $media['video_embed'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div></div>';
	}

	/**
	 * Render the audio player block (native <audio>).
	 *
	 * @param array $media Output of MDE_Post_Meta::get().
	 * @param array $s     Widget settings.
	 */
	private function render_audio( $media, $s ) {
		$label = ! empty( $s['audio_label'] ) ? $s['audio_label'] : __( 'صوت جلسه', 'mde' );
		$title = ! empty( $media['audio_title'] ) ? $media['audio_title'] : get_the_title();
		$dur   = (int) $media['audio_duration'];
		echo '<div class="mde-reveal"><div class="mde-audio-player" data-duration="' . esc_attr( max( 1, $dur ) ) . '" data-src="' . esc_url( $media['audio_url'] ) . '">';
		echo '<div class="mde-audio-player__row">';
		echo '<button type="button" class="mde-audio-player__btn" aria-label="' . esc_attr__( 'پخش/توقف', 'mde' ) . '"></button>';
		echo '<div style="flex:1;min-width:0;">';
		echo '<div style="font-size:12.5px;color:var(--c-primary);font-weight:600;margin-bottom:4px;">' . esc_html( $label ) . '</div>';
		echo '<div style="font-size:16px;font-weight:700;color:var(--c-ink);">' . esc_html( $title ) . '</div>';
		echo '</div>';
		echo '<a class="mde-icon-btn" href="' . esc_url( $media['audio_url'] ) . '" download title="' . esc_attr__( 'دانلود', 'mde' ) . '">' . MDE_Helpers::icon( 'download' ) . '</a>'; // phpcs:ignore
		echo '</div>';
		echo '<div style="display:flex;align-items:center;gap:12px;">';
		echo '<span data-mde-cur style="font-size:12px;color:var(--c-muted);min-width:42px;">۰۰:۰۰</span>';
		echo '<div class="mde-audio-player__seek"><div></div></div>';
		echo '<span style="font-size:12px;color:var(--c-muted);min-width:42px;">' . esc_html( self::format_duration( $dur ) ) . '</span>';
		echo '</div>';
		// Native HTML5 audio so it actually plays (the existing JS only fakes a transport).
		echo '<audio preload="metadata" style="display:none;"><source src="' . esc_url( $media['audio_url'] ) . '" /></audio>';
		echo '</div></div>';
	}

	/**
	 * Render the article body + tags. Pulled out so it can sit either
	 * inside the "text" tab pane or directly in the stacked layout.
	 *
	 * NOTE: must NOT be named `render_content` — that collides with
	 * Elementor's public `Widget_Base::render_content()` and PHP refuses
	 * to lower a public method to private in a subclass (fatal error).
	 */
	private function render_body_content() {
		echo '<div class="mde-article__content" style="font-size:17px;">';
		the_content();
		echo '</div>';
		$tags = get_the_tags();
		if ( $tags ) {
			echo '<div class="mde-reveal"><div style="margin-top:40px;display:flex;flex-wrap:wrap;gap:8px;align-items:center;"><span style="font-size:13px;color:var(--c-muted);">' . esc_html__( 'برچسب‌ها:', 'mde' ) . '</span>';
			foreach ( $tags as $tg ) {
				echo '<a href="' . esc_url( get_tag_link( $tg->term_id ) ) . '" class="mde-chip">#' . esc_html( $tg->name ) . '</a>';
			}
			echo '</div></div>';
		}
	}

	private static function format_duration( $secs ) {
		$secs = max( 0, (int) $secs );
		$h = (int) floor( $secs / 3600 );
		$m = (int) floor( ( $secs % 3600 ) / 60 );
		$r = $secs % 60;
		$p = function ( $x ) { return ( $x < 10 ? '0' : '' ) . $x; };
		$str = $h > 0 ? ( $p( $h ) . ':' . $p( $m ) . ':' . $p( $r ) ) : ( $p( $m ) . ':' . $p( $r ) );
		return MDE_Helpers::fa( $str );
	}
}
