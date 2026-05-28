/* ===================================================================
   مرکز نشر دستغیب — behaviour layer.
   Vanilla port of every interactive piece from the React prototype:
   reveal-on-scroll, mobile drawer, search overlay, article tabs +
   audio player + reading progress + reader mode + font size,
   category grid/list toggle, and the live viewer/elapsed counters.
   Idempotent (data-mde-bound guards) so it is safe inside the
   Elementor editor where widgets re-render.
   =================================================================== */
( function () {
	'use strict';

	var fa = function ( n ) {
		return String( n ).replace( /\d/g, function ( d ) {
			return '۰۱۲۳۴۵۶۷۸۹'[ d ];
		} );
	};

	/* ---- Reveal on scroll ---- */
	function initReveal( root ) {
		var els = root.querySelectorAll( '.mde-reveal:not([data-mde-bound])' );
		if ( ! els.length ) {
			return;
		}
		var io = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( e ) {
					if ( e.isIntersecting ) {
						var d = parseInt( e.target.getAttribute( 'data-delay' ) || '0', 10 );
						setTimeout( function () {
							e.target.classList.add( 'is-in' );
						}, d );
						io.unobserve( e.target );
					}
				} );
			},
			{ threshold: 0.12 }
		);
		els.forEach( function ( el ) {
			el.setAttribute( 'data-mde-bound', '1' );
			io.observe( el );
		} );
	}

	/* ---- Header: mobile drawer + search overlay + sub-menus ---- */
	function initHeader( root ) {
		root.querySelectorAll( '.mde-header:not([data-mde-bound])' ).forEach( function ( hdr ) {
			hdr.setAttribute( 'data-mde-bound', '1' );
			var scope = hdr.closest( '.mde-scope' ) || document;
			var drawer = scope.querySelector( '.mde-drawer' );
			var burger = hdr.querySelector( '.mde-hamburger' );
			if ( burger && drawer ) {
				burger.addEventListener( 'click', function ( ev ) {
					ev.preventDefault();
					ev.stopPropagation();
					drawer.classList.add( 'is-open' );
				} );
				drawer.addEventListener( 'click', function ( ev ) {
					if ( ev.target === drawer || ev.target.closest( '[data-mde-close]' ) ) {
						drawer.classList.remove( 'is-open' );
					}
				} );
			}
			var searchBtn = hdr.querySelector( '[data-mde-search]' );
			var overlay = scope.querySelector( '.mde-search-overlay' );
			if ( searchBtn && overlay ) {
				var close = function () {
					overlay.classList.remove( 'is-open' );
				};
				searchBtn.addEventListener( 'click', function ( ev ) {
					ev.preventDefault();
					ev.stopPropagation();
					overlay.classList.add( 'is-open' );
					var inp = overlay.querySelector( 'input' );
					if ( inp ) {
						inp.focus();
					}
				} );
				overlay.addEventListener( 'click', function ( ev ) {
					if ( ev.target === overlay ) {
						close();
					}
				} );
				document.addEventListener( 'keydown', function ( ev ) {
					if ( 'Escape' === ev.key ) {
						close();
					}
				} );
			}

			// Mobile drawer: inject an accordion toggle button after each parent
			// link. The toggle's click is handled by a global delegator below
			// (so it survives Elementor re-renders and theme JS interference).
			scope.querySelectorAll( '.mde-drawer .menu-item-has-children' ).forEach( function ( li ) {
				if ( li.querySelector( ':scope > .mde-sub-toggle' ) ) {
					return;
				}
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'mde-sub-toggle';
				btn.setAttribute( 'aria-label', 'باز/بستن زیرمنو' );
				btn.setAttribute( 'aria-expanded', 'false' );
				// Insert as a sibling of the parent link, NOT inside it (nested
				// interactive elements are invalid HTML).
				var firstChild = li.firstElementChild;
				if ( firstChild ) {
					li.insertBefore( btn, firstChild.nextSibling );
				} else {
					li.appendChild( btn );
				}
			} );

			// Desktop nav: tap-to-toggle the top-level dropdown for touch users,
			// without breaking hover for desktop users. The arrow indicator inside
			// the parent <a> handles the toggle when JS picks up a non-link click.
			scope.querySelectorAll( '.mde-nav .menu-item-has-children > a' ).forEach( function ( a ) {
				a.addEventListener( 'click', function ( ev ) {
					// Only act on devices without hover (touch). Hover devices follow the link normally.
					if ( window.matchMedia && window.matchMedia( '(hover: none)' ).matches ) {
						var li = a.parentElement;
						if ( ! li.classList.contains( 'is-open' ) ) {
							ev.preventDefault();
							scope.querySelectorAll( '.mde-nav .menu-item-has-children.is-open' ).forEach( function ( other ) {
								if ( other !== li ) {
									other.classList.remove( 'is-open' );
								}
							} );
							li.classList.add( 'is-open' );
						}
					}
				} );
			} );
			// Close open desktop dropdowns when clicking outside.
			document.addEventListener( 'click', function ( ev ) {
				if ( ! ev.target.closest( '.mde-nav .menu-item-has-children' ) ) {
					scope.querySelectorAll( '.mde-nav .menu-item-has-children.is-open' ).forEach( function ( li ) {
						li.classList.remove( 'is-open' );
					} );
				}
			} );
		} );
	}

	/* ---- Single article ---- */
	function initArticle( root ) {
		root.querySelectorAll( '.mde-article:not([data-mde-bound])' ).forEach( function ( art ) {
			art.setAttribute( 'data-mde-bound', '1' );
			var scope = art.closest( '.mde-scope' ) || document;

			// Reading progress.
			var bar = scope.querySelector( '.mde-progress__bar' );
			if ( bar ) {
				var onScroll = function () {
					var rect = art.getBoundingClientRect();
					var total = rect.height - window.innerHeight;
					var passed = Math.max( 0, -rect.top );
					var pct = total > 0 ? Math.min( 100, ( passed / total ) * 100 ) : 0;
					bar.style.width = pct + '%';
				};
				window.addEventListener( 'scroll', onScroll, { passive: true } );
				onScroll();
			}

			// Tabs (text / video / audio).
			var tabs = scope.querySelectorAll( '[data-mde-tab]' );
			var panes = scope.querySelectorAll( '[data-mde-pane]' );
			tabs.forEach( function ( t ) {
				t.addEventListener( 'click', function () {
					var key = t.getAttribute( 'data-mde-tab' );
					tabs.forEach( function ( x ) {
						x.classList.toggle( 'is-active', x === t );
					} );
					panes.forEach( function ( p ) {
						p.style.display = p.getAttribute( 'data-mde-pane' ) === key ? '' : 'none';
					} );
				} );
			} );

			// Audio player — drives a real <audio> when a `data-src` is set
			// (post-meta backed), falls back to the simulated transport when
			// no source is available (legacy markup).
			scope.querySelectorAll( '.mde-audio-player:not([data-mde-bound])' ).forEach( function ( ap ) {
				ap.setAttribute( 'data-mde-bound', '1' );
				var src = ap.getAttribute( 'data-src' ) || '';
				var dur = parseInt( ap.getAttribute( 'data-duration' ) || '3492', 10 );
				var btn = ap.querySelector( '.mde-audio-player__btn' );
				var fill = ap.querySelector( '.mde-audio-player__seek > div' );
				var cur = ap.querySelector( '[data-mde-cur]' );
				var iconPlay = '<svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><polygon points="7 5 19 12 7 19 7 5"/></svg>';
				var iconPause = '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14"/><rect x="14" y="5" width="4" height="14"/></svg>';
				var fmt = function ( s ) {
					s = Math.max( 0, Math.floor( s ) );
					var h = Math.floor( s / 3600 );
					var m = Math.floor( ( s % 3600 ) / 60 );
					var r = s % 60;
					var p = function ( x ) { return ( x < 10 ? '0' : '' ) + x; };
					return fa( h > 0 ? ( p( h ) + ':' + p( m ) + ':' + p( r ) ) : ( p( m ) + ':' + p( r ) ) );
				};

				if ( src ) {
					// Real audio path — use the <audio> element embedded by PHP.
					var audio = ap.querySelector( 'audio' );
					if ( ! audio ) {
						audio = document.createElement( 'audio' );
						audio.preload = 'metadata';
						audio.src = src;
						audio.style.display = 'none';
						ap.appendChild( audio );
					}
					if ( btn ) { btn.innerHTML = iconPlay; }
					var setFill = function () {
						var d = isFinite( audio.duration ) && audio.duration > 0 ? audio.duration : dur;
						var pct = d > 0 ? ( audio.currentTime / d ) * 100 : 0;
						if ( fill ) { fill.style.width = pct + '%'; }
						if ( cur ) { cur.textContent = fmt( audio.currentTime ); }
					};
					audio.addEventListener( 'timeupdate', setFill );
					audio.addEventListener( 'loadedmetadata', setFill );
					audio.addEventListener( 'play', function () { if ( btn ) { btn.innerHTML = iconPause; } } );
					audio.addEventListener( 'pause', function () { if ( btn ) { btn.innerHTML = iconPlay; } } );
					audio.addEventListener( 'ended', function () { if ( btn ) { btn.innerHTML = iconPlay; } } );
					if ( btn ) {
						btn.addEventListener( 'click', function () {
							if ( audio.paused ) { audio.play(); } else { audio.pause(); }
						} );
					}
					var seekEl = ap.querySelector( '.mde-audio-player__seek' );
					if ( seekEl ) {
						seekEl.addEventListener( 'click', function ( ev ) {
							var r = seekEl.getBoundingClientRect();
							// RTL: progress fills from the right; clicking near the
							// left jumps to the end. Mirror the click X accordingly.
							var p = 1 - ( ( ev.clientX - r.left ) / r.width );
							var d = isFinite( audio.duration ) && audio.duration > 0 ? audio.duration : dur;
							audio.currentTime = Math.max( 0, Math.min( d, p * d ) );
						} );
					}
					ap.querySelectorAll( '[data-mde-speed]' ).forEach( function ( sp ) {
						sp.addEventListener( 'click', function () {
							var v = parseFloat( sp.getAttribute( 'data-speed' ) || sp.textContent ) || 1;
							audio.playbackRate = v;
							ap.querySelectorAll( '[data-mde-speed]' ).forEach( function ( x ) {
								x.classList.toggle( 'is-active', x === sp );
							} );
						} );
					} );
					setFill();
					return;
				}

				// Fallback simulated transport (no audio src).
				var t = 0;
				var playing = false;
				var timer = null;
				var render = function () {
					if ( fill ) { fill.style.width = ( ( t / dur ) * 100 ) + '%'; }
					if ( cur ) { cur.textContent = fmt( t ); }
				};
				if ( btn ) {
					btn.innerHTML = iconPlay;
					btn.addEventListener( 'click', function () {
						playing = ! playing;
						btn.innerHTML = playing ? iconPause : iconPlay;
						if ( playing ) {
							timer = setInterval( function () {
								t = Math.min( dur, t + 1 );
								render();
								if ( t >= dur ) {
									clearInterval( timer );
									playing = false;
									btn.innerHTML = iconPlay;
								}
							}, 1000 );
						} else {
							clearInterval( timer );
						}
					} );
				}
				var seek = ap.querySelector( '.mde-audio-player__seek' );
				if ( seek ) {
					seek.addEventListener( 'click', function ( ev ) {
						var r = seek.getBoundingClientRect();
						var p = ( ev.clientX - r.left ) / r.width;
						t = Math.floor( ( 1 - p ) * dur ); // RTL track.
						render();
					} );
				}
				ap.querySelectorAll( '[data-mde-speed]' ).forEach( function ( sp ) {
					sp.addEventListener( 'click', function () {
						ap.querySelectorAll( '[data-mde-speed]' ).forEach( function ( x ) {
							x.classList.toggle( 'is-active', x === sp );
						} );
					} );
				} );
				render();
			} );
		} );
	}

	/* ---- Article tools (font size / reader mode / bookmark) ---- */
	function initArticleTools( root ) {
		root.querySelectorAll( '.mde-tools:not([data-mde-bound])' ).forEach( function ( box ) {
			box.setAttribute( 'data-mde-bound', '1' );
			var scope = box.closest( '.mde-scope' ) || document;
			var content = scope.querySelector( '.mde-article__content' );
			var grid = scope.querySelector( '.mde-article-grid' );
			var size = 17;
			var apply = function () {
				if ( content ) {
					content.style.fontSize = size + 'px';
				}
				var lab = box.querySelector( '[data-mde-fontval]' );
				if ( lab ) {
					lab.textContent = fa( size );
				}
			};
			var dec = box.querySelector( '[data-mde-font="dec"]' );
			var def = box.querySelector( '[data-mde-font="def"]' );
			var inc = box.querySelector( '[data-mde-font="inc"]' );
			if ( dec ) {
				dec.addEventListener( 'click', function () {
					size = Math.max( 14, size - 1 );
					apply();
				} );
			}
			if ( def ) {
				def.addEventListener( 'click', function () {
					size = 17;
					apply();
				} );
			}
			if ( inc ) {
				inc.addEventListener( 'click', function () {
					size = Math.min( 24, size + 1 );
					apply();
				} );
			}
			var reader = box.querySelector( '[data-mde-reader]' );
			if ( reader && grid ) {
				reader.addEventListener( 'click', function () {
					var on = grid.classList.toggle( 'is-reader' );
					reader.classList.toggle( 'is-on', on );
				} );
			}
			var bm = box.querySelector( '[data-mde-bookmark]' );
			if ( bm ) {
				bm.addEventListener( 'click', function () {
					bm.classList.toggle( 'is-on' );
				} );
			}
		} );
	}

	/* ---- Category grid/list toggle ---- */
	function initCategory( root ) {
		root.querySelectorAll( '.mde-cat-archive:not([data-mde-bound])' ).forEach( function ( wrap ) {
			wrap.setAttribute( 'data-mde-bound', '1' );
			var gridBtn = wrap.querySelector( '[data-mde-view="grid"]' );
			var listBtn = wrap.querySelector( '[data-mde-view="list"]' );
			var gridWrap = wrap.querySelector( '[data-mde-grid]' );
			var listWrap = wrap.querySelector( '[data-mde-list]' );
			var set = function ( mode ) {
				if ( gridBtn ) {
					gridBtn.classList.toggle( 'is-active', 'grid' === mode );
				}
				if ( listBtn ) {
					listBtn.classList.toggle( 'is-active', 'list' === mode );
				}
				if ( gridWrap ) {
					gridWrap.style.display = 'grid' === mode ? '' : 'none';
				}
				if ( listWrap ) {
					listWrap.style.display = 'list' === mode ? '' : 'none';
				}
			};
			if ( gridBtn ) {
				gridBtn.addEventListener( 'click', function () {
					set( 'grid' );
				} );
			}
			if ( listBtn ) {
				listBtn.addEventListener( 'click', function () {
					set( 'list' );
				} );
			}
			set( 'grid' );
		} );
	}

	/* ---- Posts grid chip filter (AJAX, document-level delegation) ----
	 *
	 * Bound once on `document` in the capture phase so it works regardless of
	 * when chips enter the DOM, what Elementor does, or any stopPropagation
	 * on parent elements.
	 */
	function handlePostsGridChipClick( ev ) {
		var chip = ev.target && ev.target.closest && ev.target.closest( '[data-mde-pg-cat]' );
		if ( ! chip ) {
			return;
		}
		var chips = chip.closest( '.mde-pg-chips' );
		if ( ! chips ) {
			return;
		}

		ev.preventDefault();
		ev.stopPropagation();

		if ( chip.classList.contains( 'is-active' ) ) {
			return;
		}

		// Always give immediate visual feedback, even if AJAX is misconfigured.
		chips.querySelectorAll( '[data-mde-pg-cat]' ).forEach( function ( c ) {
			c.classList.toggle( 'is-active', c === chip );
		} );

		var scope = chips.closest( '.mde-scope' ) || document;
		var grid = scope.querySelector( '.mde-pg-grid' );
		if ( ! grid ) {
			return;
		}
		var ajaxUrl = chips.getAttribute( 'data-mde-ajax' ) ||
			( window.mdeData && window.mdeData.ajax_url ) ||
			( typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '' );
		var nonce = chips.getAttribute( 'data-mde-nonce' ) ||
			( window.mdeData && window.mdeData.nonce ) || '';
		if ( ! ajaxUrl ) {
			return;
		}

		grid.classList.add( 'is-loading' );

		var body = new URLSearchParams();
		body.append( 'action', 'mde_pg_filter' );
		body.append( 'nonce', nonce );
		body.append( 'cat', chip.getAttribute( 'data-mde-pg-cat' ) );
		body.append( 'cats', grid.getAttribute( 'data-mde-pg-cats' ) || '' );
		body.append( 'count', grid.getAttribute( 'data-mde-pg-count' ) || '6' );
		body.append( 'variant', grid.getAttribute( 'data-mde-pg-variant' ) || 'default' );
		body.append( 'badge', grid.getAttribute( 'data-mde-pg-badge' ) || '' );
		body.append( 'fallback', grid.getAttribute( 'data-mde-pg-fallback' ) || '' );

		fetch( ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString()
		} )
			.then( function ( r ) { return r.json(); } )
			.then( function ( res ) {
				grid.classList.remove( 'is-loading' );
				if ( res && res.success && res.data && typeof res.data.html === 'string' ) {
					grid.innerHTML = res.data.html;
					initReveal( grid );
				}
			} )
			.catch( function () {
				grid.classList.remove( 'is-loading' );
			} );
	}

	function bindPostsGridDelegation() {
		if ( document.documentElement.hasAttribute( 'data-mde-pg-delegated' ) ) {
			return;
		}
		document.documentElement.setAttribute( 'data-mde-pg-delegated', '1' );
		// Capture phase so we win over any `stopPropagation` upstream.
		document.addEventListener( 'click', handlePostsGridChipClick, true );
	}
	bindPostsGridDelegation();

	/* ---- Mobile drawer sub-menu accordion (document-level delegation) ----
	 * Robust against Elementor re-renders, theme JS, and event hijacking. */
	function handleDrawerSubToggleClick( ev ) {
		var btn = ev.target && ev.target.closest && ev.target.closest( '.mde-sub-toggle' );
		if ( ! btn ) {
			return;
		}
		var li = btn.closest( '.menu-item-has-children' );
		if ( ! li || ! li.closest( '.mde-drawer' ) ) {
			return;
		}
		ev.preventDefault();
		ev.stopPropagation();
		var isOpen = li.classList.toggle( 'is-open' );
		btn.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	}
	function bindDrawerSubDelegation() {
		if ( document.documentElement.hasAttribute( 'data-mde-drawer-delegated' ) ) {
			return;
		}
		document.documentElement.setAttribute( 'data-mde-drawer-delegated', '1' );
		document.addEventListener( 'click', handleDrawerSubToggleClick, true );
	}
	bindDrawerSubDelegation();

	function initPostsGridFilter() {
		bindPostsGridDelegation();
	}

	/* ---- Hero slider ---- */
	function initHeroSlider( root ) {
		root.querySelectorAll( '[data-mde-slider]:not([data-mde-bound])' ).forEach( function ( wrap ) {
			wrap.setAttribute( 'data-mde-bound', '1' );
			var slides = wrap.querySelectorAll( '.mde-hero-slide' );
			var dots = wrap.querySelectorAll( '.mde-hero-dot' );
			if ( slides.length < 2 ) {
				return;
			}
			var current = 0;
			var autoplay = parseInt( wrap.getAttribute( 'data-autoplay' ) || '0', 10 );
			var timer = null;

			var go = function ( idx ) {
				current = ( idx + slides.length ) % slides.length;
				slides.forEach( function ( s, i ) {
					s.classList.toggle( 'is-active', i === current );
				} );
				dots.forEach( function ( d, i ) {
					d.classList.toggle( 'is-active', i === current );
				} );
			};

			var start = function () {
				if ( autoplay > 0 ) {
					stop();
					timer = setInterval( function () {
						go( current + 1 );
					}, autoplay );
				}
			};
			var stop = function () {
				if ( timer ) {
					clearInterval( timer );
					timer = null;
				}
			};

			wrap.querySelectorAll( '.mde-hero-arrow' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function ( ev ) {
					ev.preventDefault();
					ev.stopPropagation();
					var dir = btn.getAttribute( 'data-dir' );
					go( current + ( 'prev' === dir ? -1 : 1 ) );
					start();
				} );
			} );
			dots.forEach( function ( d, i ) {
				d.addEventListener( 'click', function ( ev ) {
					ev.preventDefault();
					ev.stopPropagation();
					go( i );
					start();
				} );
			} );
			wrap.addEventListener( 'mouseenter', stop );
			wrap.addEventListener( 'mouseleave', start );
			start();
		} );
	}

	/* ---- Sticky payment bar (Gravity Forms total + submit proxy) ---- */
	function initPaymentSticky( root ) {
		root.querySelectorAll( '[data-mde-pay-sticky="1"]:not([data-mde-sticky-bound])' ).forEach( function ( wrap ) {
			wrap.setAttribute( 'data-mde-sticky-bound', '1' );

			var form = wrap.querySelector( '.gform_wrapper form, form.gform' );
			var submitBtn = wrap.querySelector( '.gform_footer .gform_button, .gform_footer input[type=submit], .gform_footer button[type=submit]' );
			if ( ! form || ! submitBtn ) {
				return;
			}

			var btnText = wrap.getAttribute( 'data-mde-pay-btn-text' ) || 'پرداخت';
			var totalLabel = wrap.getAttribute( 'data-mde-pay-total-label' ) || 'مجموع';
			var unit = wrap.getAttribute( 'data-mde-pay-unit' ) || 'ریال';

			// Build the bar DOM.
			var bar = document.createElement( 'div' );
			bar.className = 'mde-pay-sticky';
			bar.innerHTML =
				'<div class="mde-pay-sticky__total">' +
					'<span class="mde-pay-sticky__label"></span>' +
					'<div class="mde-pay-sticky__value-row">' +
						'<span class="mde-pay-sticky__value">۰</span>' +
						'<span class="mde-pay-sticky__unit"></span>' +
					'</div>' +
				'</div>' +
				'<button type="button" class="mde-pay-sticky__btn">' +
					'<span class="mde-pay-sticky__btn-text"></span>' +
					'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>' +
				'</button>';
			wrap.appendChild( bar );

			bar.querySelector( '.mde-pay-sticky__label' ).textContent = totalLabel;
			bar.querySelector( '.mde-pay-sticky__unit' ).textContent = unit;
			bar.querySelector( '.mde-pay-sticky__btn-text' ).textContent = btnText;

			var valueEl = bar.querySelector( '.mde-pay-sticky__value' );
			var proxyBtn = bar.querySelector( '.mde-pay-sticky__btn' );

			// Resolve the GF total element (handles multiple GF versions).
			function findTotalEl() {
				return wrap.querySelector( '.gfield_total .ginput_total, .ginput_total, .gfield_total .gfield_total_value' );
			}

			function readTotal() {
				var el = findTotalEl();
				if ( ! el ) { return ''; }
				var t = ( el.textContent || el.value || '' ).trim();
				return t;
			}

			function syncTotal() {
				var raw = readTotal();
				// If GF outputs raw digits, prettify with thousands separators.
				if ( raw && /^[\d۰-۹.,\s]+$/.test( raw ) ) {
					valueEl.textContent = raw;
				} else if ( raw ) {
					valueEl.textContent = raw;
				} else {
					valueEl.textContent = '۰';
				}
				// Disable the proxy button when total is zero/empty.
				var numeric = raw.replace( /[^\d]/g, '' );
				proxyBtn.disabled = ! numeric || parseInt( numeric, 10 ) === 0;
			}

			// Proxy click → real submit button.
			proxyBtn.addEventListener( 'click', function ( ev ) {
				ev.preventDefault();
				if ( proxyBtn.disabled ) { return; }
				submitBtn.click();
			} );

			// Watch the GF total for any change (recalculations on input).
			var totalEl = findTotalEl();
			if ( totalEl ) {
				var mo = new MutationObserver( syncTotal );
				mo.observe( totalEl, { childList: true, characterData: true, subtree: true, attributes: true, attributeFilter: [ 'value' ] } );
			}
			form.addEventListener( 'input', function () { setTimeout( syncTotal, 50 ); } );
			form.addEventListener( 'change', function () { setTimeout( syncTotal, 50 ); } );
			// GF dispatches 'gform_post_render' after recalc on some setups.
			if ( window.jQuery ) {
				window.jQuery( document ).on( 'gform_post_render', syncTotal );
			}

			// Reveal once the bar is positioned.
			requestAnimationFrame( function () {
				bar.classList.add( 'is-visible' );
				syncTotal();
			} );
		} );
	}

	/* ---- Aparat embed containment ----
	 * Aparat's external <script> injects an iframe with position:absolute
	 * into its parent <div>. If that parent isn't `position: relative`
	 * and doesn't reserve aspect-ratio height, the iframe escapes and
	 * overlaps neighboring content (e.g. the WP [playlist] audio player).
	 * We patch every such wrapper here so it has a real containing block
	 * and 16:9 height — this is a JS fallback for browsers without
	 * :has() support and runs idempotently. */
	function initAparatWrappers( root ) {
		root.querySelectorAll(
			'script[src*="aparat.com"], script[src*="aparat.ir"]'
		).forEach( function ( script ) {
			var wrapper = script.parentElement;
			if ( ! wrapper || wrapper.hasAttribute( 'data-mde-aparat-bound' ) ) {
				return;
			}
			wrapper.setAttribute( 'data-mde-aparat-bound', '1' );
			wrapper.classList.add( 'mde-aparat-wrap' );
		} );
		root.querySelectorAll( 'iframe[src*="aparat.com"], iframe[src*="aparat.ir"]' ).forEach( function ( iframe ) {
			var wrapper = iframe.parentElement;
			if ( ! wrapper || wrapper.hasAttribute( 'data-mde-aparat-bound' ) ) {
				return;
			}
			wrapper.setAttribute( 'data-mde-aparat-bound', '1' );
			wrapper.classList.add( 'mde-aparat-wrap' );
		} );
	}

	/* ---- Bank card copy buttons (payment widget) ---- */
	function initBankCopy( root ) {
		root.querySelectorAll( '[data-mde-copy]:not([data-mde-bound])' ).forEach( function ( btn ) {
			btn.setAttribute( 'data-mde-bound', '1' );
			btn.addEventListener( 'click', function ( ev ) {
				ev.preventDefault();
				var v = btn.getAttribute( 'data-mde-copy' ) || '';
				var done = function () {
					btn.classList.add( 'is-copied' );
					setTimeout( function () { btn.classList.remove( 'is-copied' ); }, 1400 );
				};
				if ( navigator.clipboard && navigator.clipboard.writeText ) {
					navigator.clipboard.writeText( v ).then( done, function () {
						// Fallback for permission-blocked clipboard.
						fallback( v );
						done();
					} );
				} else {
					fallback( v );
					done();
				}
			} );
		} );
		function fallback( text ) {
			var ta = document.createElement( 'textarea' );
			ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
			document.body.appendChild( ta );
			ta.select();
			try { document.execCommand( 'copy' ); } catch ( e ) {}
			document.body.removeChild( ta );
		}
	}

	/* ---- Live counters ---- */
	function initLive( root ) {
		root.querySelectorAll( '[data-mde-live]:not([data-mde-bound])' ).forEach( function ( box ) {
			box.setAttribute( 'data-mde-bound', '1' );
			var vEl = box.querySelector( '[data-mde-viewers]' );
			var eEl = box.querySelector( '[data-mde-elapsed]' );
			var viewers = parseInt( ( vEl && vEl.getAttribute( 'data-start' ) ) || '1287', 10 );
			var elapsed = parseInt( ( eEl && eEl.getAttribute( 'data-start' ) ) || '2340', 10 );
			var fmt = function ( s ) {
				var h = Math.floor( s / 3600 );
				var m = Math.floor( ( s % 3600 ) / 60 );
				var r = s % 60;
				var p = function ( x ) {
					return ( x < 10 ? '0' : '' ) + x;
				};
				return fa( p( h ) + ':' + p( m ) + ':' + p( r ) );
			};
			setInterval( function () {
				viewers = Math.max( 900, viewers + Math.floor( Math.random() * 10 - 4 ) );
				elapsed += 1;
				if ( vEl ) {
					vEl.textContent = fa( viewers.toLocaleString( 'en-US' ).replace( /,/g, '٬' ) );
				}
				if ( eEl ) {
					eEl.textContent = fmt( elapsed );
				}
			}, 2200 );
		} );
	}

	function initAll( root ) {
		root = root || document;
		initReveal( root );
		initHeader( root );
		initArticle( root );
		initArticleTools( root );
		initCategory( root );
		initHeroSlider( root );
		initPostsGridFilter();
		initAparatWrappers( root );
		initBankCopy( root );
		initPaymentSticky( root );
		initLive( root );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initAll( document );
		} );
	} else {
		initAll( document );
	}

	// Re-init when Elementor re-renders a widget in the editor / on the front.
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
					initAll( $scope[ 0 ] || document );
				} );
			}
		} );
	}
} )();
