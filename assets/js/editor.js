/**
 * Abidar Group — Professional CPT Editor Behaviour (v2.1.0)
 *
 * تب‌بندی پنل، انتخابگر رسانه، گالری با مرتب‌سازی کشیدنی،
 * ریپیتر پویا و پیش‌نمایش زنده کارت.
 *
 * @package Abidar_Group
 * @since 2.1.0
 */
( function ( $ ) {
	'use strict';

	const cfg = window.AbidarEditor || {};
	const i18n = cfg.i18n || {};

	/* ======================================================================
	 * 01. تب‌بندی پنل
	 * ==================================================================== */
	const initTabs = () => {
		const root = $( '.abidar-editor' );
		if ( ! root.length ) {
			return;
		}

		root.find( '.abidar-editor__tab' ).on( 'click', function () {
			const tab = $( this ).data( 'tab' );

			root.find( '.abidar-editor__tab' ).removeClass( 'is-active' ).attr( 'aria-selected', 'false' );
			$( this ).addClass( 'is-active' ).attr( 'aria-selected', 'true' );

			root.find( '.abidar-editor__panel' ).removeClass( 'is-active' );
			root.find( '.abidar-editor__panel[data-panel="' + tab + '"]' ).addClass( 'is-active' );

			// Remember the tab across page reloads.
			try {
				window.sessionStorage.setItem( 'abidarEditorTab', tab );
			} catch ( e ) {}
		} );

		try {
			const saved = window.sessionStorage.getItem( 'abidarEditorTab' );
			if ( saved ) {
				const btn = root.find( '.abidar-editor__tab[data-tab="' + saved + '"]' );
				if ( btn.length ) {
					btn.trigger( 'click' );
				}
			}
		} catch ( e ) {}
	};

	/* ======================================================================
	 * 02. انتخابگر رسانه (ویدیو / PDF)
	 * ==================================================================== */
	const initMediaPickers = () => {
		$( '.abidar-media-picker' ).each( function () {
			const wrap = $( this );
			const typeInput = wrap.find( '[data-media-id]' );
			const urlInput = wrap.find( '.abidar-media-picker__url' );
			const preview = wrap.find( '[data-media-preview]' );
			const kind = wrap.data( 'picker' );

			const renderPreview = ( url, mime ) => {
				preview.empty();
				if ( ! url ) {
					return;
				}

				if ( 'video' === kind ) {
					if ( /\.(mp4|webm|ogg)(\?|$)/i.test( url ) ) {
						preview.append( $( '<video>' ).attr( { src: url, muted: true, controls: true, preload: 'metadata' } ) );
					} else {
						preview.append(
							$( '<div class="abidar-media-picker__file">' )
								.append( $( '<span class="dashicons dashicons-video-alt3">' ) )
								.append( document.createTextNode( 'لینک ویدیو ثبت شد' ) )
						);
					}
					return;
				}

				if ( mime && mime.indexOf( 'image' ) === 0 ) {
					preview.append( $( '<img>' ).attr( 'src', url ).attr( 'alt', '' ) );
					return;
				}

				preview.append(
					$( '<div class="abidar-media-picker__file">' )
						.append( $( '<span class="dashicons dashicons-pdf">' ) )
						.append( document.createTextNode( decodeURIComponent( url.split( '/' ).pop() ) ) )
				);
			};

			const initial = urlInput.val();
			if ( initial ) {
				renderPreview( initial, '' );
			}

			wrap.find( '.abidar-media-picker__select' ).on( 'click', function ( event ) {
				event.preventDefault();

				if ( ! window.wp || ! window.wp.media ) {
					return;
				}

				const frame = window.wp.media( {
					title: 'video' === kind ? i18n.selectVideo : i18n.selectFile,
					button: { text: 'video' === kind ? i18n.useVideo : i18n.useFile },
					library: { type: 'video' === kind ? 'video' : 'application/pdf' },
					multiple: false,
				} );

				frame.on( 'select', () => {
					const attachment = frame.state().get( 'selection' ).first().toJSON();
					typeInput.val( attachment.id );
					urlInput.val( attachment.url );
					renderPreview( attachment.url, attachment.mime );
				} );

				frame.open();
			} );

			wrap.find( '.abidar-media-picker__clear' ).on( 'click', function ( event ) {
				event.preventDefault();
				typeInput.val( '' );
				urlInput.val( '' );
				preview.empty();
			} );
		} );
	};

	/* ======================================================================
	 * 03. گالری تصاویر با مرتب‌سازی کشیدنی
	 * ==================================================================== */
	const initGallery = () => {
		$( '.abidar-gallery' ).each( function () {
			const wrap = $( this );
			const input = wrap.find( '[data-gallery-input]' );
			const items = wrap.find( '[data-gallery-items]' );

			const sync = () => {
				const ids = items
					.children( '.abidar-gallery__item' )
					.map( function () {
						return $( this ).data( 'id' );
					} )
					.get();
				input.val( ids.join( ',' ) );
			};

			if ( $.fn.sortable ) {
				items.sortable( {
					items: '.abidar-gallery__item',
					cursor: 'move',
					placeholder: 'abidar-repeater__placeholder',
					update: sync,
				} );
			}

			wrap.find( '.abidar-gallery__add' ).on( 'click', function ( event ) {
				event.preventDefault();

				if ( ! window.wp || ! window.wp.media ) {
					return;
				}

				const frame = window.wp.media( {
					title: i18n.selectImage,
					button: { text: i18n.useImage },
					library: { type: 'image' },
					multiple: 'add',
				} );

				frame.on( 'select', () => {
					const selection = frame.state().get( 'selection' );
					selection.each( ( attachment ) => {
						const data = attachment.toJSON();
						const url =
							data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;

						items.append(
							$( '<div class="abidar-gallery__item">' )
								.attr( 'data-id', data.id )
								.append( $( '<img>' ).attr( { src: url, alt: '' } ) )
								.append(
									$( '<button type="button" class="abidar-gallery__remove">' )
										.attr( 'aria-label', i18n.confirmDel )
										.text( '×' )
								)
						);
					} );
					sync();
				} );

				frame.open();
			} );

			items.on( 'click', '.abidar-gallery__remove', function ( event ) {
				event.preventDefault();
				$( this ).closest( '.abidar-gallery__item' ).remove();
				sync();
			} );
		} );
	};

	/* ======================================================================
	 * 04. ریپیتر پویا
	 * ==================================================================== */
	const initRepeaters = () => {
		$( '.abidar-repeater' ).each( function () {
			const wrap = $( this );
			const rows = wrap.find( '[data-repeater-rows]' );
			const name = wrap.data( 'repeater' );

			const template = () => {
				const first = rows.children( '.abidar-repeater__row' ).first().clone();
				first.find( 'input' ).val( '' );
				return first;
			};

			if ( $.fn.sortable ) {
				rows.sortable( {
					items: '.abidar-repeater__row',
					handle: '.abidar-repeater__handle',
					cursor: 'move',
					placeholder: 'abidar-repeater__placeholder',
				} );
			}

			wrap.on( 'click', '.abidar-repeater__add', function ( event ) {
				event.preventDefault();
				const row = template();
				rows.append( row );
				row.find( 'input' ).first().trigger( 'focus' );
			} );

			wrap.on( 'click', '.abidar-repeater__remove', function ( event ) {
				event.preventDefault();
				if ( rows.children( '.abidar-repeater__row' ).length > 1 ) {
					$( this ).closest( '.abidar-repeater__row' ).remove();
				} else {
					$( this ).closest( '.abidar-repeater__row' ).find( 'input' ).val( '' );
				}
			} );
		} );
	};

	/* ======================================================================
	 * 05. پیش‌نمایش زنده کارت
	 * ==================================================================== */
	const initPreview = () => {
		const preview = $( '.abidar-preview-card' );
		if ( ! preview.length ) {
			return;
		}

		const titleInput = $( '#title' );
		const summaryInput = $( '#abidar_summary' );
		const sectorRadios = $( 'input[name="abidar_meta[sector]"]' );

		const sectorLabels = {
			mining: '⛏️ معادن',
			energy: '⚡ انرژی',
			oil: '🛢️ نفت و گاز',
		};

		const bindImage = () => {
			// Refresh the preview thumbnail from the Featured Image panel.
			const thumbId = $( '#_thumbnail_id' ).val();
			if ( ! thumbId || ! window.wp || ! window.wp.media ) {
				return;
			}

			const attachment = window.wp.media
				.attachment( thumbId )
				.fetch()
				.done( () => {
					const data = attachment.toJSON();
					const url = data.sizes && data.sizes.medium ? data.sizes.medium.url : data.url;
					$( '[data-preview-thumb]' )
						.css( 'background-image', 'url("' + url + '")' )
						.find( '.abidar-preview-card__placeholder' )
						.hide();
				} );
		};

		titleInput.on( 'input', function () {
			$( '[data-preview-title]' ).text( $( this ).val() || 'عنوان…' );
		} );

		summaryInput.on( 'input', function () {
			$( '[data-preview-summary]' ).text( $( this ).val() || 'خلاصه کارت اینجا نمایش داده می‌شود…' );
		} );

		sectorRadios.on( 'change', function () {
			$( '[data-preview-sector]' ).text( sectorLabels[ $( this ).val() ] || '' );
		} );

		// Sync the selected sector tile styling.
		sectorRadios.on( 'change', function () {
			$( '.abidar-sector-picker__item' ).removeClass( 'is-checked' );
			$( this ).closest( '.abidar-sector-picker__item' ).addClass( 'is-checked' );
		} );

		$( document ).on( 'click', '.abidar-sector-picker__item', function () {
			$( '.abidar-sector-picker__item' ).removeClass( 'is-checked' );
			$( this ).addClass( 'is-checked' );
		} );

		bindImage();
		$( document ).on( 'ajaxSuccess', bindImage );
	};

	/* ======================================================================
	 * 06. نمایش درصد پیشرفت
	 * ==================================================================== */
	const initRange = () => {
		$( '[data-range-input]' ).on( 'input', function () {
			$( this ).closest( '.abidar-field' ).find( '[data-range-out]' ).text( $( this ).val() + '%' );
		} );
	};

	/* ======================================================================
	 * راه‌اندازی
	 * ==================================================================== */
	$( function () {
		initTabs();
		initMediaPickers();
		initGallery();
		initRepeaters();
		initPreview();
		initRange();
	} );
} )( window.jQuery );
