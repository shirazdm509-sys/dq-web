<?php
/**
 * Post meta — admin metabox on the post editor for the three per-post
 * media fields the Single Article widget can render:
 *
 *   • Ayah (Arabic verse + caption)
 *   • Video (embed HTML or URL)
 *   • Audio (file URL + optional title + duration)
 *
 * Storage lives in the `_mde_*` post-meta family. The widget reads these
 * via MDE_Post_Meta::get( $post_id ) — anything left blank by the author
 * simply isn't rendered on the front-end, so empty fields produce
 * absolutely no markup.
 *
 * @package MarkazDastgheibElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MDE_Post_Meta {

	const NONCE_NAME   = 'mde_post_meta_nonce';
	const NONCE_ACTION = 'mde_post_meta_save';

	/** Meta keys. */
	const KEY_AYAH_TEXT    = '_mde_ayah_text';
	const KEY_AYAH_CAPTION = '_mde_ayah_caption';
	const KEY_VIDEO_EMBED  = '_mde_video_embed';
	const KEY_AUDIO_URL    = '_mde_audio_url';
	const KEY_AUDIO_TITLE  = '_mde_audio_title';
	const KEY_AUDIO_DURATION = '_mde_audio_duration';

	/** Post types where the metabox should appear. Filterable. */
	public static function post_types() {
		return apply_filters( 'mde_post_meta_post_types', array( 'post' ) );
	}

	public static function boot() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_metabox' ) );
		add_action( 'save_post', array( __CLASS__, 'save_metabox' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
	}

	/**
	 * Detect Elementor's full-screen editor — at /wp-admin/post.php?action=elementor
	 * (and the editor preview iframe). We skip our metabox entirely there so
	 * its inline <script> / <style> can't interfere with Elementor's own
	 * scripts (jQuery + media library bootstrapping in particular).
	 */
	private static function is_elementor_editor() {
		// Standard editor URL.
		if ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
			return true;
		}
		// Preview iframe.
		if ( isset( $_GET['elementor-preview'] ) ) {
			return true;
		}
		// Elementor's own runtime check, if available.
		if ( class_exists( '\Elementor\Plugin' ) && ! empty( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return true;
		}
		return false;
	}

	public static function register_metabox() {
		if ( self::is_elementor_editor() ) {
			return;
		}
		foreach ( self::post_types() as $pt ) {
			add_meta_box(
				'mde_post_media',
				__( 'مدیا و آیه (مرکز نشر دستغیب)', 'mde' ),
				array( __CLASS__, 'render_metabox' ),
				$pt,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Enqueue the WP media library (used by the "Choose audio file" button)
	 * only on the post-edit screens that show our metabox. Explicitly skip
	 * the Elementor editor — wp.media loaded there can collide with
	 * Elementor's own media wrapper and hang the panel.
	 */
	public static function enqueue_admin_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		if ( self::is_elementor_editor() ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, self::post_types(), true ) ) {
			return;
		}
		wp_enqueue_media();
	}

	public static function render_metabox( $post ) {
		$values = self::get( $post->ID );
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		?>
		<style>
			.mde-meta-grid { display: grid; gap: 18px; margin: 6px 0 4px; }
			.mde-meta-field { display: grid; gap: 6px; }
			.mde-meta-field label { font-weight: 600; font-size: 13px; }
			.mde-meta-field input[type=text],
			.mde-meta-field input[type=url],
			.mde-meta-field textarea { width: 100%; }
			.mde-meta-field textarea { font-family: ui-monospace, "SF Mono", monospace; font-size: 13px; }
			.mde-meta-field .description { color: #6b6b6b; font-size: 12px; }
			.mde-meta-section { padding: 14px 16px; background: #f7f5ee; border-radius: 8px; border: 1px solid #e8e3d6; }
			.mde-meta-section > h3 {
				margin: 0 0 12px;
				font-size: 14px; font-weight: 700;
				display: inline-flex; align-items: center; gap: 8px;
				color: #1a1a1a;
				padding-bottom: 8px; border-bottom: 1px dashed #d6cfbe;
				width: 100%;
			}
			.mde-meta-section > h3 .dashicons { color: #109487; }
			.mde-meta-row { display: flex; gap: 8px; align-items: center; }
			.mde-meta-row input { flex: 1; }
			.mde-meta-help {
				margin-top: 6px;
				padding: 8px 10px;
				background: #eef9f7;
				border-radius: 6px;
				font-size: 12px;
				color: #0c7a6f;
			}
		</style>

		<div class="mde-meta-grid">

			<!-- Ayah -->
			<div class="mde-meta-section">
				<h3><span class="dashicons dashicons-format-quote"></span><?php esc_html_e( 'آیه شریفه', 'mde' ); ?></h3>
				<div class="mde-meta-field">
					<label for="mde_ayah_text"><?php esc_html_e( 'متن آیه (عربی)', 'mde' ); ?></label>
					<textarea id="mde_ayah_text" name="mde_ayah_text" rows="3" dir="rtl" placeholder="<?php esc_attr_e( 'مثلاً: قَالَ إِنَّمَا أُوتِيتُهُ عَلَىٰ عِلْمٍ عِندِي…', 'mde' ); ?>"><?php echo esc_textarea( $values['ayah_text'] ); ?></textarea>
				</div>
				<div class="mde-meta-field" style="margin-top:10px;">
					<label for="mde_ayah_caption"><?php esc_html_e( 'منبع / توضیح آیه', 'mde' ); ?></label>
					<input type="text" id="mde_ayah_caption" name="mde_ayah_caption" value="<?php echo esc_attr( $values['ayah_caption'] ); ?>" placeholder="<?php esc_attr_e( 'مثلاً: سوره قصص — آیه ۷۸', 'mde' ); ?>" />
				</div>
				<p class="mde-meta-help">
					<?php esc_html_e( 'اگر متن آیه خالی باشد، کادر «آیه شریفه» در صفحه نمایش داده نمی‌شود.', 'mde' ); ?>
				</p>
			</div>

			<!-- Video -->
			<div class="mde-meta-section">
				<h3><span class="dashicons dashicons-video-alt3"></span><?php esc_html_e( 'فیلم جلسه', 'mde' ); ?></h3>
				<div class="mde-meta-field">
					<label for="mde_video_embed"><?php esc_html_e( 'پیوند ویدیو یا کد امبد', 'mde' ); ?></label>
					<textarea id="mde_video_embed" name="mde_video_embed" rows="4" dir="ltr" placeholder="https://www.aparat.com/v/xxxxx&#10;یا کد کامل &lt;iframe&gt;..."><?php echo esc_textarea( $values['video_embed'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'می‌توانید فقط یک URL آپارات/یوتیوب وارد کنید یا کد کامل iframe را paste کنید.', 'mde' ); ?></p>
				</div>
				<p class="mde-meta-help">
					<?php esc_html_e( 'اگر چیزی وارد نکنید، بخش ویدیو نشان داده نمی‌شود.', 'mde' ); ?>
				</p>
			</div>

			<!-- Audio -->
			<div class="mde-meta-section">
				<h3><span class="dashicons dashicons-format-audio"></span><?php esc_html_e( 'صوت جلسه', 'mde' ); ?></h3>
				<div class="mde-meta-field">
					<label for="mde_audio_url"><?php esc_html_e( 'پیوند فایل صوتی (MP3)', 'mde' ); ?></label>
					<div class="mde-meta-row">
						<input type="text" id="mde_audio_url" name="mde_audio_url" value="<?php echo esc_attr( $values['audio_url'] ); ?>" placeholder="https://example.com/audio.mp3" />
						<button type="button" class="button" id="mde_audio_pick"><?php esc_html_e( 'انتخاب از رسانه', 'mde' ); ?></button>
					</div>
					<p class="description"><?php esc_html_e( 'لینک مستقیم به فایل صوتی. اگر می‌خواهید آپلود کنید، روی دکمه «انتخاب از رسانه» کلیک کنید.', 'mde' ); ?></p>
				</div>
				<div class="mde-meta-field" style="margin-top:10px;">
					<label for="mde_audio_title"><?php esc_html_e( 'عنوان نمایش (اختیاری)', 'mde' ); ?></label>
					<input type="text" id="mde_audio_title" name="mde_audio_title" value="<?php echo esc_attr( $values['audio_title'] ); ?>" placeholder="<?php esc_attr_e( 'مثلاً: تفسیر سوره قصص — جلسه ۳۵', 'mde' ); ?>" />
					<p class="description"><?php esc_html_e( 'اگر خالی بگذارید، از عنوان خود نوشته استفاده می‌شود.', 'mde' ); ?></p>
				</div>
				<div class="mde-meta-field" style="margin-top:10px;">
					<label for="mde_audio_duration"><?php esc_html_e( 'مدت زمان (به ثانیه، اختیاری)', 'mde' ); ?></label>
					<input type="number" id="mde_audio_duration" name="mde_audio_duration" value="<?php echo esc_attr( $values['audio_duration'] ); ?>" min="0" step="1" placeholder="3492" style="max-width:200px;" />
					<p class="description"><?php esc_html_e( 'مدت زمان فایل به ثانیه — برای نمایش صحیح زمان روی نوار پخش.', 'mde' ); ?></p>
				</div>
				<p class="mde-meta-help">
					<?php esc_html_e( 'اگر پیوند صوتی وارد نشود، بخش پخش‌کننده‌ی صوت در صفحه ظاهر نخواهد شد.', 'mde' ); ?>
				</p>
			</div>

		</div>

		<script>
		(function($) {
			$(function() {
				var frame;
				$('#mde_audio_pick').on('click', function(e) {
					e.preventDefault();
					if (frame) { frame.open(); return; }
					frame = wp.media({
						title: '<?php echo esc_js( __( 'انتخاب فایل صوتی', 'mde' ) ); ?>',
						button: { text: '<?php echo esc_js( __( 'استفاده از این فایل', 'mde' ) ); ?>' },
						library: { type: 'audio' },
						multiple: false
					});
					frame.on('select', function() {
						var att = frame.state().get('selection').first().toJSON();
						if (att && att.url) {
							$('#mde_audio_url').val(att.url);
							if (att.fileLength && !$('#mde_audio_duration').val()) {
								// fileLength is a string "MM:SS" — convert.
								var parts = String(att.fileLength).split(':').map(function(x){return parseInt(x,10)||0;});
								var secs = parts.length === 2 ? parts[0]*60+parts[1] : (parts.length === 3 ? parts[0]*3600+parts[1]*60+parts[2] : 0);
								if (secs) { $('#mde_audio_duration').val(secs); }
							}
							if (att.title && !$('#mde_audio_title').val()) {
								$('#mde_audio_title').val(att.title);
							}
						}
					});
					frame.open();
				});
			});
		})(jQuery);
		</script>
		<?php
	}

	/**
	 * Persist the metabox values on every post save.
	 */
	public static function save_metabox( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! in_array( $post->post_type, self::post_types(), true ) ) {
			return;
		}
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) ||
		     ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Ayah — plain text, line breaks preserved.
		self::save_text( $post_id, self::KEY_AYAH_TEXT, isset( $_POST['mde_ayah_text'] ) ? wp_unslash( $_POST['mde_ayah_text'] ) : '' );
		self::save_text( $post_id, self::KEY_AYAH_CAPTION, isset( $_POST['mde_ayah_caption'] ) ? wp_unslash( $_POST['mde_ayah_caption'] ) : '' );

		// Video — let admins paste raw iframe / style. Pass through wp_kses
		// with the same allowlist the live-player widget uses.
		$video = isset( $_POST['mde_video_embed'] ) ? wp_unslash( $_POST['mde_video_embed'] ) : '';
		update_post_meta( $post_id, self::KEY_VIDEO_EMBED, self::sanitize_embed( $video ) );

		// Audio — URL + optional title + duration.
		$audio_url = isset( $_POST['mde_audio_url'] ) ? esc_url_raw( wp_unslash( $_POST['mde_audio_url'] ) ) : '';
		update_post_meta( $post_id, self::KEY_AUDIO_URL, $audio_url );
		self::save_text( $post_id, self::KEY_AUDIO_TITLE, isset( $_POST['mde_audio_title'] ) ? wp_unslash( $_POST['mde_audio_title'] ) : '' );
		$duration = isset( $_POST['mde_audio_duration'] ) ? absint( $_POST['mde_audio_duration'] ) : 0;
		update_post_meta( $post_id, self::KEY_AUDIO_DURATION, $duration );
	}

	/**
	 * Read all media-meta for a post and return a tidy associative array.
	 * Empty fields stay as empty strings so the widget can do simple
	 * `if ( '' === $x )` checks.
	 *
	 * @param int $post_id Post id.
	 * @return array{
	 *   ayah_text:string, ayah_caption:string,
	 *   video_embed:string,
	 *   audio_url:string, audio_title:string, audio_duration:int
	 * }
	 */
	public static function get( $post_id ) {
		$post_id = (int) $post_id;
		return array(
			'ayah_text'      => (string) get_post_meta( $post_id, self::KEY_AYAH_TEXT, true ),
			'ayah_caption'   => (string) get_post_meta( $post_id, self::KEY_AYAH_CAPTION, true ),
			'video_embed'    => (string) get_post_meta( $post_id, self::KEY_VIDEO_EMBED, true ),
			'audio_url'      => (string) get_post_meta( $post_id, self::KEY_AUDIO_URL, true ),
			'audio_title'    => (string) get_post_meta( $post_id, self::KEY_AUDIO_TITLE, true ),
			'audio_duration' => (int)    get_post_meta( $post_id, self::KEY_AUDIO_DURATION, true ),
		);
	}

	/**
	 * Sanitise an embed/iframe blob the way the live-player widget does —
	 * allow the tags an embed actually needs without going through
	 * wp_kses_post which strips iframe.
	 *
	 * @param string $html Raw embed.
	 * @return string Cleaned HTML or trimmed URL.
	 */
	public static function sanitize_embed( $html ) {
		$html = trim( $html );
		if ( '' === $html ) {
			return '';
		}
		// Plain URL — store as-is.
		if ( false === strpos( $html, '<' ) ) {
			return esc_url_raw( $html );
		}
		$allowed = array(
			'iframe' => array(
				'src'                   => true,
				'width'                 => true,
				'height'                => true,
				'frameborder'           => true,
				'scrolling'             => true,
				'allowfullscreen'       => true,
				'webkitallowfullscreen' => true,
				'mozallowfullscreen'    => true,
				'allow'                 => true,
				'referrerpolicy'        => true,
				'title'                 => true,
				'style'                 => true,
				'class'                 => true,
				'name'                  => true,
				'sandbox'               => true,
				'loading'               => true,
			),
			'div'    => array( 'class' => true, 'style' => true, 'id' => true ),
			'span'   => array( 'class' => true, 'style' => true ),
			'style'  => array( 'type' => true ),
		);
		return wp_kses( $html, $allowed );
	}

	private static function save_text( $post_id, $key, $value ) {
		$value = sanitize_textarea_field( $value );
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Render a video block from the stored embed/URL. Returns '' when empty.
	 *
	 * @param string $embed Embed HTML or URL.
	 * @return string
	 */
	public static function render_video( $embed ) {
		$embed = trim( $embed );
		if ( '' === $embed ) {
			return '';
		}
		// Already-tagged HTML — output as-is (already sanitised on save).
		if ( false !== strpos( $embed, '<' ) ) {
			return $embed;
		}
		// Bare URL — try WP oEmbed first, fall back to <iframe>.
		$oe = wp_oembed_get( esc_url( $embed ) );
		if ( $oe ) {
			return $oe;
		}
		return '<iframe src="' . esc_url( $embed ) . '" allowfullscreen></iframe>';
	}
}
