/**
 * ============================================================================
 * ABIDAR GROUP HOLDING — ADMIN SETTINGS JAVASCRIPT (v2.0.0)
 * ----------------------------------------------------------------------------
 *  01. Initialization of WordPress color pickers
 *  02. Tabs (refresh / hash sync) and active tab hidden input
 *  03. WordPress media library uploader for custom fonts & logos
 *  04. Repeater add / remove helpers
 *  05. Secure AJAX demo importer
 *  06. Live preview of color changes inside admin
 * ============================================================================
 */

( function ( $ ) {
	'use strict';

	const config = window.AbidarAdmin || {};

	/* ======================================================================
	 * 01. COLOR PICKERS
	 * ==================================================================== */
	const initColorPickers = () => {
		if ( ! $.fn.wpColorPicker ) {
			return;
		}

		$( '.abidar-color-picker' ).wpColorPicker( {
			change( event, ui ) {
				const input = $( event.target );
				const color = ui.color.toString();

				// به‌روزرسانی زنده پیش‌نمایش رنگ در پنل.
				if ( input.closest( '.abidar-admin-card' ).length ) {
					input.attr( 'value', color ).trigger( 'change' );
				}
			},
			clear() {
				$( this ).trigger( 'change' );
			},
		} );
	};

	/* ======================================================================
	 * 02. TABS
	 * ==================================================================== */
	const initTabs = () => {
		const tabs = $( '.abidar-admin-tab' );
		const panels = $( '.abidar-admin-panel' );
		const hiddenInput = $( '#abidar-active-tab-input' );

		if ( ! tabs.length || ! panels.length ) {
			return;
		}

		tabs.on( 'click', function ( event ) {
			const target = $( this ).data( 'tab-target' );

			// اگر لینک تب به صفحه فعلی اشاره دارد، فقط نمایش را تغییر می‌دهیم (بدون بارگذاری مجدد).
			if ( target && $( '[data-tab-panel="' + target + '"]' ).length ) {
				event.preventDefault();

				tabs.removeClass( 'is-active' ).attr( 'aria-selected', 'false' );
				$( this ).addClass( 'is-active' ).attr( 'aria-selected', 'true' );

				panels.removeClass( 'is-active' ).attr( 'aria-hidden', 'true' );
				$( '[data-tab-panel="' + target + '"]' ).addClass( 'is-active' ).attr( 'aria-hidden', 'false' );

				if ( hiddenInput.length ) {
					hiddenInput.val( target );
				}

				// به‌روزرسانی آدرس صفحه برای قابلیت بوکمارک بدون بارگذاری مجدد.
				if ( window.history && window.history.replaceState ) {
					const url = new URL( window.location.href );
					url.searchParams.set( 'page', 'abidar-settings' );
					url.searchParams.set( 'tab', target );
					window.history.replaceState( {}, '', url.toString() );
				}
			}
		} );

		tabs.attr( 'role', 'tab' );
		panels.attr( 'role', 'tabpanel' );
	};

	/* ======================================================================
	 * 03. MEDIA UPLOADER (فونت اختصاصی و لوگو)
	 * ==================================================================== */
	const initMediaUploader = () => {
		let frame = null;

		$( document ).on( 'click', '.abidar-media-upload', function ( event ) {
			event.preventDefault();

			const button = $( this );
			const targetSelector = button.data( 'target' );
			const target = $( targetSelector );

			if ( ! target.length ) {
				return;
			}

			if ( frame ) {
				frame.off( 'select' );
				frame = null;
			}

			frame = wp.media( {
				title: button.data( 'title' ) || 'انتخاب یا بارگذاری فایل فونت اختصاصی',
				button: { text: button.data( 'button' ) || 'استفاده از این فایل' },
				library: { type: [ 'font/woff2', 'font/woff', 'font/ttf', 'application/font-woff', 'application/x-font-ttf', 'application/octet-stream', 'image' ] },
				multiple: false,
			} );

			frame.on( 'select', () => {
				const attachment = frame.state().get( 'selection' ).first().toJSON();
				if ( attachment && attachment.url ) {
					target.val( attachment.url ).trigger( 'change' );
					target.after( '<p class="description" style="color:#047857;font-weight:600;">✓ فایل «' + ( attachment.filename || attachment.title ) + '» انتخاب شد.</p>' );
				}
			} );

			frame.open();
		} );
	};

	/* ======================================================================
	 * 04. REPEATER HELPERS
	 * ==================================================================== */
	const initRepeaters = () => {
		$( document ).on( 'click', '.abidar-repeater-remove', function ( event ) {
			event.preventDefault();
			const item = $( this ).closest( '.abidar-repeater-item' );
			const container = item.parent();

			// حداقل یک آیتم باید باقی بماند.
			if ( container.find( '.abidar-repeater-item' ).length <= 1 ) {
				window.alert( 'حداقل یک آیتم باید در این لیست باقی بماند.' );
				return;
			}

			if ( window.confirm( 'آیا از حذف این آیتم مطمئن هستید؟ پس از ذخیره تنظیمات، حذف نهایی می‌شود.' ) ) {
				item.slideUp( 200, () => item.remove() );
			}
		} );

		$( document ).on( 'click', '.abidar-repeater-add', function ( event ) {
			event.preventDefault();
			const container = $( $( this ).data( 'target' ) );
			const template = container.find( '.abidar-repeater-item' ).first();

			if ( ! template.length ) {
				return;
			}

			const nextIndex = container.find( '.abidar-repeater-item' ).length;
			const clone = template.clone( true );

			clone.find( 'input, select, textarea' ).each( function () {
				const field = $( this );
				const name = field.attr( 'name' );

				if ( name ) {
					field.attr( 'name', name.replace( /\[\d+\]/, '[' + nextIndex + ']' ) );
				}

				field.val( '' );
			} );

			clone.hide().appendTo( container ).slideDown( 200 );
			container.find( '.abidar-repeater-item' ).last().find( 'input' ).first().trigger( 'focus' );
		} );
	};

	/**
	 * افزودن ردیف زبان سفارشی جدید (فقط سمت کلاینت؛ پس از ذخیره در دیتابیس ثبت می‌شود).
	 */
	const initAddLanguage = () => {
		$( '#abidar-add-language-btn' ).on( 'click', function ( event ) {
			event.preventDefault();

			const list = $( '#abidar-custom-languages-list' );
			const last = list.find( '.abidar-repeater-item' ).last();

			if ( ! last.length ) {
				return;
			}

			const index = list.find( '.abidar-repeater-item' ).length;
			const clone = last.clone( true );

			clone.find( 'input, select' ).each( function () {
				const field = $( this );
				const name = field.attr( 'name' );

				if ( name ) {
					field.attr( 'name', name.replace( /\[\d+\]/, '[' + index + ']' ) );
				}

				if ( 'checkbox' === field.attr( 'type' ) ) {
					field.prop( 'checked', true );
				} else if ( 'select' === field.prop( 'tagName' ).toLowerCase() ) {
					field.val( 'ltr' );
				} else {
					field.val( '' );
				}
			} );

			clone.hide().appendTo( list ).slideDown( 200 );
			clone.find( 'input' ).first().trigger( 'focus' );
		} );
	};

	/* ======================================================================
	 * 05. DEMO IMPORTER (AJAX امن)
	 * ==================================================================== */
	const initDemoImporter = () => {
		const button = $( '#abidar-run-demo-importer' );
		if ( ! button.length ) {
			return;
		}

		const status = $( '#abidar-demo-import-status' );

		button.on( 'click', function ( event ) {
			event.preventDefault();

			if ( ! window.confirm( 'با این عملیات ۷ برگه اصلی، پست‌تایپ‌ها و مقالات نمونه ساخته یا بازسازی می‌شوند. ادامه می‌دهید؟' ) ) {
				return;
			}

			const originalText = button.text();
			button.text( 'در حال ساخت برگه‌ها و ساختار المنتور...' ).prop( 'disabled', true );
			status.removeClass( 'is-error' ).text( '' );

			$.ajax( {
				url: config.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'abidar_install_demo',
					nonce: config.nonce,
				},
			} )
				.done( ( response ) => {
					if ( response && response.success ) {
						status
							.removeClass( 'is-error' )
							.html( '<div class="abidar-demo-status"><span>✅</span><div><strong>عملیات با موفقیت انجام شد</strong>' + ( response.data.message || '' ) + '</div></div>' )
							.prop( 'hidden', false );
					} else {
						status
							.addClass( 'is-error' )
							.html( '<div class="abidar-demo-status is-error"><span>⛔</span><div><strong>خطا در اجرای نمونه‌ساز</strong>' + ( ( response && response.data && response.data.message ) || 'پاسخ نامعتبر از سرور دریافت شد.' ) + '</div></div>' );
					}
				} )
				.fail( () => {
					status
						.addClass( 'is-error' )
						.html( '<div class="abidar-demo-status is-error"><span>⛔</span><div><strong>خطای ارتباط با سرور</strong>لطفاً صفحه را بازخوانی کرده و مجدداً تلاش نمایید.</div></div>' );
				} )
				.always( () => {
					button.text( originalText ).prop( 'disabled', false );
				} );
		} );
	};

	/* ======================================================================
	 * 06. LIVE COLOR HINTS
	 * ==================================================================== */
	const initLiveHints = () => {
		$( '.abidar-color-picker' ).on( 'change', function () {
			const value = $( this ).val();
			const row = $( this ).closest( '.abidar-admin-card' );
			if ( row.length && /^#[0-9a-f]{6}$/i.test( value ) ) {
				row.css( 'border-inline-start-color', value );
			}
		} ).trigger( 'change' );
	};

	/* ======================================================================
	 * BOOTSTRAP
	 * ==================================================================== */
	$( document ).ready( () => {
		initColorPickers();
		initTabs();
		initMediaUploader();
		initRepeaters();
		initAddLanguage();
		initDemoImporter();
		initLiveHints();
	} );
} )( window.jQuery );
