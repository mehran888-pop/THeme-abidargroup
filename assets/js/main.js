/**
 * ============================================================================
 * ABIDAR GROUP HOLDING — MAIN FRONTEND JAVASCRIPT (v2.0.0)
 * ----------------------------------------------------------------------------
 * تمام تعاملات سایت بدون وابستگی به jQuery و مطابق استاندارد ES2018+ نوشته شده‌اند:
 *  01. Preloader
 *  02. Mobile navigation drawer
 *  03. Floating multi-department contact hub
 *  04. Widget layout switcher (طرح‌های مختلف هر ویجت المنتور)
 *  05. Sector filters (خدمات، پروژه‌ها)
 *  06. Hero interactive pillars
 *  07. Certificate lightbox / اصل استعلام
 *  08. Table of contents (TOC) + reading progress
 *  09. Secure AJAX contact form
 *  10. Share buttons / copy link
 *  11. Scroll reveal + sticky header shrink
 *  12. Misc helpers (a11y, focus trap, counters)
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

( function () {
	'use strict';

	/**
	 * پیکربندی امن تزریق‌شده از سمت وردپرس (wp_localize_script).
	 */
	const config = window.AbidarConfig || {};

	/**
	 * انتخاب یک عنصر.
	 *
	 * @param {string} selector انتخابگر CSS.
	 * @param {Element|Document} scope محدوده جستجو.
	 * @return {Element|null}
	 */
	const $ = ( selector, scope = document ) => scope.querySelector( selector );

	/**
	 * انتخاب تمام عناصر.
	 *
	 * @param {string} selector انتخابگر CSS.
	 * @param {Element|Document} scope محدوده جستجو.
	 * @return {Array<Element>}
	 */
	const $$ = ( selector, scope = document ) => Array.from( scope.querySelectorAll( selector ) );

	/**
	 * افزودن یا حذف کلاس باز/بسته بودن یک عنصر با به‌روزرسانی صفت aria.
	 *
	 * @param {Element} el عنصر هدف.
	 * @param {boolean} open وضعیت باز بودن.
	 */
	const setOpenState = ( el, open ) => {
		if ( ! el ) {
			return;
		}
		el.classList.toggle( 'is-open', open );
		el.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
	};

	/* ======================================================================
	 * 01. PRELOADER
	 * ==================================================================== */
	const initPreloader = () => {
		const preloader = $( '#abidar-preloader' );
		if ( ! preloader ) {
			return;
		}

		const duration = Number( config.preloaderDuration ) || 900;

		const hide = () => {
			window.setTimeout( () => {
				preloader.classList.add( 'is-hidden' );
				window.setTimeout( () => {
					if ( preloader.parentNode ) {
						preloader.parentNode.removeChild( preloader );
					}
				}, 600 );
			}, duration );
		};

		if ( document.readyState === 'complete' ) {
			hide();
		} else {
			window.addEventListener( 'load', hide, { once: true } );
			// محافظ ایمنی: اگر رویداد load به هر دلیل رخ نداد، پیش‌بارگذاری حداکثر پس از ۴ ثانیه پنهان می‌شود.
			window.setTimeout( hide, 4000 );
		}
	};

	/* ======================================================================
	 * 02. MOBILE NAVIGATION DRAWER
	 * ==================================================================== */
	const initMobileNav = () => {
		const drawer = $( '#abidar-mobile-drawer' );
		if ( ! drawer ) {
			return;
		}

		const openButtons = $$( '[data-open-mobile-nav]' );
		const closeButtons = $$( '[data-close-mobile-nav]', drawer );
		let lastFocused = null;

		const open = () => {
			lastFocused = document.activeElement;
			setOpenState( drawer, true );
			document.body.style.overflow = 'hidden';
			const firstLink = $( 'a, button', drawer );
			if ( firstLink ) {
				window.setTimeout( () => firstLink.focus(), 220 );
			}
		};

		const close = () => {
			setOpenState( drawer, false );
			document.body.style.overflow = '';
			if ( lastFocused && typeof lastFocused.focus === 'function' ) {
				lastFocused.focus();
			}
		};

		openButtons.forEach( ( btn ) => btn.addEventListener( 'click', open ) );
		closeButtons.forEach( ( btn ) => btn.addEventListener( 'click', close ) );

		document.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' === event.key && drawer.classList.contains( 'is-open' ) ) {
				close();
			}
		} );

		// بستن خودکار منو هنگام کلیک روی لینک‌ها.
		$$( 'a', drawer ).forEach( ( link ) => link.addEventListener( 'click', close ) );
	};

	/* ======================================================================
	 * 03. FLOATING CONTACT HUB
	 * ==================================================================== */
	const initFloatingHub = () => {
		const hub = $( '#abidar-floating-hub' );
		if ( ! hub ) {
			return;
		}

		const panel = $( '#abidar-floating-modal', hub );
		const triggers = $$( '[data-toggle-floating-hub]' );
		if ( ! panel || ! triggers.length ) {
			return;
		}

		const toggle = ( force ) => {
			const isOpen = hub.classList.contains( 'is-open' );
			const next = 'boolean' === typeof force ? force : ! isOpen;

			setOpenState( panel, next );
			hub.classList.toggle( 'is-open', next );
			triggers.forEach( ( t ) => t.setAttribute( 'aria-expanded', next ? 'true' : 'false' ) );
		};

		triggers.forEach( ( trigger ) => {
			trigger.addEventListener( 'click', () => toggle() );
		} );

		// بستن با کلیک بیرون از پنل.
		document.addEventListener( 'click', ( event ) => {
			if ( ! hub.classList.contains( 'is-open' ) ) {
				return;
			}
			if ( ! hub.contains( event.target ) ) {
				toggle( false );
			}
		} );

		document.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' === event.key && hub.classList.contains( 'is-open' ) ) {
				toggle( false );
			}
		} );
	};

	/* ======================================================================
	 * 04. WIDGET LAYOUT SWITCHER (پیش‌نمایش زنده طرح‌های ویجت‌های المنتور)
	 * ==================================================================== */
	const initLayoutSwitchers = () => {
		$$( '[data-widget-section]' ).forEach( ( section ) => {
			const switches = $$( '[data-switch-layout]', section );
			if ( ! switches.length ) {
				return;
			}

			const grid = $( '.abidar-widget-grid', section ) || $( '.abidar-timeline__track', section );

			switches.forEach( ( button ) => {
				button.addEventListener( 'click', () => {
					const layout = button.getAttribute( 'data-switch-layout' );
					if ( ! layout ) {
						return;
					}

					section.setAttribute( 'data-active-layout', layout );

					if ( grid ) {
						grid.classList.forEach( ( cls ) => {
							if ( cls.indexOf( 'abidar-widget-grid--' ) === 0 ) {
								grid.classList.remove( cls );
							}
						} );
						grid.classList.add( 'abidar-widget-grid--' + layout );
					}

					switches.forEach( ( s ) => s.classList.toggle( 'is-active', s === button ) );
				} );
			} );
		} );
	};

	/* ======================================================================
	 * 05. SECTOR FILTERS (خدمات، پروژه‌ها)
	 * ==================================================================== */
	const initSectorFilters = () => {
		$$( '[data-filter-group]' ).forEach( ( group ) => {
			const section = group.closest( '[data-widget-section]' );
			if ( ! section ) {
				return;
			}

			const items = $$( '[data-sector-item]', section );
			const pills = $$( '[data-filter]', group );

			pills.forEach( ( pill ) => {
				pill.addEventListener( 'click', () => {
					const filter = pill.getAttribute( 'data-filter' ) || 'all';

					pills.forEach( ( p ) => p.classList.toggle( 'is-active', p === pill ) );

					items.forEach( ( item ) => {
						const sector = item.getAttribute( 'data-sector-item' );
						const show = 'all' === filter || sector === filter;
						item.style.display = show ? '' : 'none';
						item.setAttribute( 'aria-hidden', show ? 'false' : 'true' );
					} );
				} );
			} );
		} );
	};

	/* ======================================================================
	 * 06. HERO INTERACTIVE PILLARS
	 * ==================================================================== */
	const initHeroPillars = () => {
		$$( '[data-sector-pillar]' ).forEach( ( card ) => {
			const activate = () => {
				$$( '[data-sector-pillar]' ).forEach( ( c ) => c.classList.toggle( 'is-expanded', c === card ) );
			};

			card.addEventListener( 'mouseenter', activate );
			card.addEventListener( 'focusin', activate );
			card.addEventListener( 'click', ( event ) => {
				// اگر کاربر روی لینک کلیک کرده باشد، مانع ناوبری نمی‌شویم.
				if ( event.target.closest( 'a' ) ) {
					return;
				}
				activate();
			} );

			card.setAttribute( 'tabindex', '0' );
			card.addEventListener( 'keydown', ( event ) => {
				if ( 'Enter' === event.key || ' ' === event.key ) {
					event.preventDefault();
					activate();
				}
			} );
		} );
	};

	/* ======================================================================
	 * 07. CERTIFICATE LIGHTBOX
	 * ==================================================================== */
	const initCertificateLightbox = () => {
		const lightbox = $( '#abidar-cert-lightbox' );
		if ( ! lightbox ) {
			return;
		}

		const titleEl = $( '#abidar-cert-modal-title', lightbox );
		const issuerEl = $( '#abidar-cert-modal-issuer', lightbox );
		const codeEl = $( '#abidar-cert-modal-code', lightbox );
		const yearEl = $( '#abidar-cert-modal-year', lightbox );
		let lastFocused = null;

		const open = ( card ) => {
			lastFocused = document.activeElement;
			if ( titleEl ) {
				titleEl.textContent = card.getAttribute( 'data-cert-title' ) || '';
			}
			if ( issuerEl ) {
				issuerEl.textContent = card.getAttribute( 'data-cert-issuer' ) || '';
			}
			if ( codeEl ) {
				codeEl.textContent = card.getAttribute( 'data-cert-code' ) || '';
			}
			if ( yearEl ) {
				yearEl.textContent = card.getAttribute( 'data-cert-year' ) || '';
			}

			lightbox.classList.add( 'is-open' );
			lightbox.setAttribute( 'aria-hidden', 'false' );
			document.body.style.overflow = 'hidden';

			const closeBtn = $( '.abidar-cert-lightbox__close', lightbox );
			if ( closeBtn ) {
				closeBtn.focus();
			}
		};

		const close = () => {
			lightbox.classList.remove( 'is-open' );
			lightbox.setAttribute( 'aria-hidden', 'true' );
			document.body.style.overflow = '';
			if ( lastFocused && typeof lastFocused.focus === 'function' ) {
				lastFocused.focus();
			}
		};

		$$( '[data-open-cert-modal]' ).forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				const card = button.closest( '.abidar-cert-card' );
				if ( card ) {
					open( card );
				}
			} );
		} );

		$$( '[data-close-cert-modal]', lightbox ).forEach( ( el ) => el.addEventListener( 'click', close ) );

		document.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' === event.key && lightbox.classList.contains( 'is-open' ) ) {
				close();
			}
		} );
	};

	/* ======================================================================
	 * 08. TABLE OF CONTENTS + READING PROGRESS
	 * ==================================================================== */
	const slugify = ( text ) =>
		text
			.toString()
			.trim()
			.toLowerCase()
			.replace( /[^\p{L}\p{N}]+/gu, '-' )
			.replace( /^-+|-+$/g, '' )
			.slice( 0, 60 ) || 'section';

	const initTableOfContents = () => {
		const body = $( '#abidar-article-body' );
		const list = $( '#abidar-toc-list' );
		if ( ! body || ! list ) {
			return;
		}

		const headings = $$( 'h2, h3', body ).filter( ( h ) => h.textContent.trim().length > 0 );
		if ( headings.length < 2 ) {
			const card = list.closest( '[data-toc]' );
			if ( card ) {
				card.style.display = 'none';
			}
			return;
		}

		const usedIds = new Set();
		const items = [];

		headings.forEach( ( heading ) => {
			let id = heading.id || slugify( heading.textContent );
			let unique = id;
			let counter = 1;
			while ( usedIds.has( unique ) ) {
				unique = id + '-' + counter;
				counter += 1;
			}
			usedIds.add( unique );
			heading.id = unique;

			const li = document.createElement( 'li' );
			li.className = 'is-level-' + heading.tagName.substring( 1 );

			const link = document.createElement( 'a' );
			link.href = '#' + unique;
			link.textContent = heading.textContent.trim();
			li.appendChild( link );
			list.appendChild( li );

			items.push( { link, heading } );
		} );

		const setActive = () => {
			let current = items[ 0 ];
			const offset = window.scrollY + 160;

			items.forEach( ( item ) => {
				if ( item.heading.offsetTop <= offset ) {
					current = item;
				}
			} );

			items.forEach( ( item ) => item.link.classList.toggle( 'is-active', item === current ) );
		};

		window.addEventListener( 'scroll', () => window.requestAnimationFrame( setActive ), { passive: true } );
		setActive();
	};

	const initReadingProgress = () => {
		const bar = $( '.abidar-reading-progress__bar' );
		if ( ! bar ) {
			return;
		}

		const update = () => {
			const doc = document.documentElement;
			const scrollable = doc.scrollHeight - doc.clientHeight;
			const progress = scrollable > 0 ? ( doc.scrollTop / scrollable ) * 100 : 0;
			bar.style.width = Math.min( 100, Math.max( 0, progress ) ) + '%';
		};

		window.addEventListener( 'scroll', () => window.requestAnimationFrame( update ), { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	};

	/* ======================================================================
	 * 09. SECURE AJAX CONTACT FORM
	 * ==================================================================== */
	const initContactForm = () => {
		const form = $( '#abidar-contact-form' );
		if ( ! form || ! config.ajaxUrl ) {
			return;
		}

		const feedback = $( '.abidar-form-feedback', form );
		const submit = $( 'button[type="submit"]', form );

		const showFeedback = ( message, type ) => {
			if ( ! feedback ) {
				return;
			}
			feedback.textContent = message;
			feedback.className = 'abidar-form-feedback is-visible is-' + type;
			feedback.scrollIntoView( { behavior: 'smooth', block: 'center' } );
		};

		form.addEventListener( 'submit', ( event ) => {
			event.preventDefault();

			if ( ! form.checkValidity() ) {
				form.reportValidity();
				showFeedback( 'لطفاً تمامی فیلدهای الزامی را به‌درستی تکمیل نمایید.', 'error' );
				return;
			}

			const formData = new FormData( form );
			formData.append( 'action', 'abidar_submit_contact' );
			formData.append( 'nonce', config.nonce || form.getAttribute( 'data-nonce' ) || '' );

			form.classList.add( 'is-loading' );
			if ( submit ) {
				submit.disabled = true;
			}

			fetch( config.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData,
			} )
				.then( ( response ) => response.json().catch( () => ( { success: false } ) ) )
				.then( ( payload ) => {
					if ( payload && payload.success && payload.data ) {
						showFeedback( payload.data.message || 'درخواست شما با موفقیت ثبت شد.', 'success' );
						form.reset();
					} else {
						const message = payload && payload.data && payload.data.message ? payload.data.message : 'خطا در ارسال درخواست. لطفاً مجدداً تلاش کنید.';
						showFeedback( message, 'error' );
					}
				} )
				.catch( () => {
					showFeedback( 'خطای شبکه: ارتباط با سرور برقرار نشد. لطفاً اتصال اینترنت خود را بررسی کنید.', 'error' );
				} )
				.finally( () => {
					form.classList.remove( 'is-loading' );
					if ( submit ) {
						submit.disabled = false;
					}
				} );
		} );
	};

	/* ======================================================================
	 * 10. SHARE BUTTONS
	 * ==================================================================== */
	const initShareButtons = () => {
		$$( '[data-copy-link]' ).forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				const link = button.getAttribute( 'data-copy-link' ) || window.location.href;

				const done = () => {
					button.classList.add( 'is-copied' );
					const original = button.textContent;
					button.textContent = '✓';
					window.setTimeout( () => {
						button.classList.remove( 'is-copied' );
						button.textContent = original || '🔗';
					}, 1800 );
				};

				if ( navigator.clipboard && navigator.clipboard.writeText ) {
					navigator.clipboard.writeText( link ).then( done ).catch( () => {} );
				} else {
					const temp = document.createElement( 'textarea' );
					temp.value = link;
					temp.setAttribute( 'readonly', 'readonly' );
					document.body.appendChild( temp );
					temp.select();
					try {
						document.execCommand( 'copy' );
						done();
					} catch ( error ) {
						// نادیده گرفتن خطای مرورگرهای قدیمی.
					}
					document.body.removeChild( temp );
				}
			} );
		} );
	};

	/* ======================================================================
	 * 11. SCROLL REVEAL + STICKY HEADER
	 * ==================================================================== */
	const initScrollReveal = () => {
		const targets = $$( '.abidar-service-card, .abidar-project-card, .abidar-team-card, .abidar-cert-card, .abidar-article-card, .abidar-timeline__item, .abidar-contact-dept' );

		if ( ! targets.length ) {
			return;
		}

		if ( ! ( 'IntersectionObserver' in window ) ) {
			targets.forEach( ( el ) => el.classList.add( 'is-inview' ) );
			return;
		}

		targets.forEach( ( el, index ) => {
			el.classList.add( 'abidar-reveal' );
			el.style.animationDelay = ( index % 6 ) * 60 + 'ms';
		} );

		const observer = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-inview' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
		);

		targets.forEach( ( el ) => observer.observe( el ) );
	};

	const initStickyHeader = () => {
		const header = $( '.abidar-site-header' );
		if ( ! header || ! document.body.classList.contains( 'abidar-has-sticky-header' ) ) {
			return;
		}

		let lastState = false;

		const update = () => {
			const state = window.scrollY > 60;
			if ( state !== lastState ) {
				header.classList.toggle( 'is-scrolled', state );
				lastState = state;
			}
		};

		window.addEventListener( 'scroll', () => window.requestAnimationFrame( update ), { passive: true } );
		update();
	};

	/* ======================================================================
	 * 12. MISC HELPERS
	 * ==================================================================== */

	/**
	 * انیمیشن شمارش اعداد آماری (در صورت وجود data-count).
	 */
	const initCounters = () => {
		const counters = $$( '[data-count]' );
		if ( ! counters.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		const animate = ( el ) => {
			const target = parseFloat( el.getAttribute( 'data-count' ) );
			if ( Number.isNaN( target ) ) {
				return;
			}
			const prefix = el.getAttribute( 'data-count-prefix' ) || '';
			const suffix = el.getAttribute( 'data-count-suffix' ) || '';
			const decimals = parseInt( el.getAttribute( 'data-count-decimals' ) || '0', 10 );
			const duration = 1400;
			const start = performance.now();

			const format = ( value ) =>
				prefix +
				value.toLocaleString( undefined, {
					minimumFractionDigits: decimals,
					maximumFractionDigits: decimals,
				} ) +
				suffix;

			const step = ( now ) => {
				const progress = Math.min( 1, ( now - start ) / duration );
				const eased = 1 - Math.pow( 1 - progress, 3 );
				const current = target * eased;
				el.textContent = format( 0 === decimals ? Math.round( current ) : current );
				if ( progress < 1 ) {
					window.requestAnimationFrame( step );
				}
			};

			window.requestAnimationFrame( step );
		};

		const observer = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						animate( entry.target );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.4 }
		);

		counters.forEach( ( el ) => observer.observe( el ) );
	};

	/**
	 * بستن خودکار اعلان‌ها با کلید Escape (برای پنل‌های شناور).
	 */
	const initA11yHelpers = () => {
		// اطمینان از وجود skip-link کاربردی.
		const skip = $( '.skip-link' );
		if ( skip ) {
			skip.addEventListener( 'click', ( event ) => {
				const target = $( '#abidar-main-content' );
				if ( target ) {
					event.preventDefault();
					target.setAttribute( 'tabindex', '-1' );
					target.focus();
					target.scrollIntoView( { behavior: 'smooth' } );
				}
			} );
		}
	};

	/* ======================================================================
	 * BOOTSTRAP
	 * ==================================================================== */
	const boot = () => {
		initPreloader();
		initMobileNav();
		initFloatingHub();
		initLayoutSwitchers();
		initSectorFilters();
		initHeroPillars();
		initCertificateLightbox();
		initTableOfContents();
		initReadingProgress();
		initContactForm();
		initShareButtons();
		initScrollReveal();
		initStickyHeader();
		initCounters();
		initA11yHelpers();
	};

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot, { once: true } );
	} else {
		boot();
	}

	/**
	 * پشتیبانی از پیش‌نمایش زنده ویرایشگر المنتور:
	 * هنگام بارگذاری مجدد ویجت‌ها، تعاملات باید دوباره مقداردهی شوند.
	 */
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', () => {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', () => {
					initLayoutSwitchers();
					initSectorFilters();
					initHeroPillars();
					initCertificateLightbox();
					initContactForm();
					initShareButtons();
				} );
			}
		} );
	}
} )();
