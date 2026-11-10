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

		/**
		 * راه‌اندازی انتخاب‌گر رنگ روی هر فیلدی که هنوز راه‌اندازی نشده است.
		 *
		 * ردیف‌های تازه‌ساخته‌شده (افزودن بخش یا دکمه جدید) بعد از بارگذاری
		 * صفحه ساخته می‌شوند؛ بدون این تابع، انتخاب‌گر رنگشان کار نمی‌کرد و
		 * کاربر فقط یک کد رنگ خام می‌دید.
		 *
		 * @param {jQuery} scope محدوده جست‌وجو.
		 */
		const bindColorPickers = ( scope ) => {
			( scope || $( document ) )
				.find( '.abidar-color-picker' )
				.not( '.abidar-color-bound' )
				.each( function () {
					const input = $( this );
					input.addClass( 'abidar-color-bound' );

					input.wpColorPicker( {
						change( event, ui ) {
							const field = $( event.target );
							const color = ui.color.toString();
							field.attr( 'value', color ).trigger( 'change' );
						},
						clear() {
							$( this ).trigger( 'change' );
						},
					} );
				} );
		};

		bindColorPickers( $( document ) );

		// هر ردیف تازه که به فرم اضافه شود، انتخاب‌گر رنگش هم فعال می‌شود.
		$( document ).on( 'abidar:repeater-added', ( event, container ) => {
			bindColorPickers( container ? $( container ) : $( document ) );
		} );
	};

	/* ======================================================================
	 * 02. SETTINGS MENU (per-page navigation)
	 * ====================================================================
	 * با تفکیک صفحات، هر گروه و زیرگروه یک آدرس واقعی است و مرورگر خودش
	 * ناوبری را انجام می‌دهد. پس اینجا هیچ preventDefault و هیچ سوییچ
	 * سمت‌کلاینتی نداریم — اگر داشته باشیم، کلیک روی گروه فقط یک پنل
	 * خالی نشان می‌دهد و صفحه عوض نمی‌شود.
	 *
	 * فقط دو کار باقی می‌ماند: بستن سایدبار موبایل بعد از انتخاب، و
	 * بازکردن گروهِ فعال.
	 */
	const initSettingsMenu = () => {
		const groups = $( '.abidar-admin-menu__group' );

		if ( ! groups.length ) {
			return;
		}

		// گروهی که صفحهٔ جاری در آن است باز می‌ماند؛ بقیه جمع می‌شوند.
		groups.each( function () {
			const group = $( this );
			group.toggleClass( 'is-active', group.find( '.abidar-admin-submenu__link.is-active' ).length > 0 );
		} );

		// روی موبایل، بعد از انتخاب یک صفحه سایدبار بسته شود.
		$( '.abidar-admin-menu__link, .abidar-admin-submenu__link' ).on( 'click', () => {
			const sidebar = $( '.abidar-admin-sidebar' );

			if ( ! sidebar.length ) {
				return;
			}

			// matchMedia در همهٔ محیط‌ها موجود نیست؛ نبودش نباید ناوبری را بشکند.
			const isNarrow = 'function' !== typeof window.matchMedia
				|| window.matchMedia( '(max-width: 960px)' ).matches;

			if ( isNarrow ) {
				sidebar.removeClass( 'is-open' );
				$( '#abidar-menu-toggle' ).attr( 'aria-expanded', 'false' );
			}
		} );

		/*
		 * اگر آدرس قدیمی با #anchor باز شود (پیوندهای ذخیره‌شده در نسخه‌های
		 * قبل)، همان بخش در صفحهٔ جاری اسکرول می‌شود تا کاربر به بن‌بست نخورد.
		 */
		if ( window.location.hash ) {
			const hash = window.location.hash.replace( '#', '' );

			if ( hash && document.getElementById( hash ) ) {
				window.setTimeout( () => {
					const el = document.getElementById( hash );
					if ( el ) {
						el.scrollIntoView( { behavior: 'smooth', block: 'start' } );
					}
				}, 200 );
			}
		}
	};

	/* ======================================================================
	 * 02b. MOBILE MENU TOGGLE
	 * ====================================================================
	 * در موبایل سایدبار جمع می‌شود و با یک دکمه باز/بسته می‌شود.
	 */
	const initSidebarToggle = () => {
		const toggle = $( '#abidar-menu-toggle' );
		const sidebar = $( '.abidar-admin-sidebar' );

		if ( ! toggle.length || ! sidebar.length ) {
			return;
		}

		toggle.on( 'click', () => {
			const isOpen = sidebar.toggleClass( 'is-open' ).hasClass( 'is-open' );
			toggle.attr( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );

		// انتخاب یک گروه، سایدبار موبایل را می‌بندد.
		$( document ).on( 'click', '.abidar-admin-menu__link', () => {
			const isNarrow = 'function' !== typeof window.matchMedia
				|| window.matchMedia( '(max-width: 960px)' ).matches;

			if ( isNarrow ) {
				sidebar.removeClass( 'is-open' );
				toggle.attr( 'aria-expanded', 'false' );
			}
		} );
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
	 * 04. REPEATER HELPERS (افزودن / حذف ردیف)
	 * ====================================================================
	 * یک پیاده‌سازی مشترک برای همه بخش‌هایی که لیست قابل رشد دارند:
	 * بخش‌های دکمه شناور، دکمه‌های منوی موبایل و زبان‌های سفارشی.
	 *
	 * نکته مهم: اگر لیست خالی باشد هیچ «ردیف الگو» وجود ندارد، بنابراین
	 * نمی‌توان با کلون کردن ردیف اول ردیف تازه ساخت. برای همین ابتدا یک
	 * الگوی خالی آماده می‌سازیم و از همان کلون می‌گیریم.
	 */
	const REPEATER_TEMPLATES = {
		'#abidar-departments-list': [
			{ name: 'id', type: 'hidden' },
			{ name: 'name_fa', placeholder: 'نام بخش (فارسی)' },
			{ name: 'name_en', placeholder: 'Department Name (EN)', dir: 'ltr' },
			{ name: 'name_zh', placeholder: '部门名称 (ZH)', dir: 'ltr' },
			{ name: 'role_fa', placeholder: 'شرح وظایف بخش (فارسی)' },
			{ name: 'role_en', placeholder: 'Department Role (EN)', dir: 'ltr' },
			{ name: 'role_zh', placeholder: '部门职责 (ZH)', dir: 'ltr' },
			{ name: 'phone', placeholder: 'شماره تلفن', dir: 'ltr' },
			{ name: 'ext', placeholder: 'داخلی', dir: 'ltr' },
			{ name: 'whatsapp', type: 'url', placeholder: 'لینک واتساپ', dir: 'ltr' },
			{ name: 'email', type: 'email', placeholder: 'ایمیل بخش', dir: 'ltr' },
			{ name: 'hours_fa', placeholder: 'ساعات پاسخگویی (فارسی)' },
			{ name: 'hours_en', placeholder: 'Working Hours (EN)', dir: 'ltr' },
			{ name: 'hours_zh', placeholder: '工作时间 (ZH)', dir: 'ltr' },
			{ name: 'icon', type: 'select', options: [ 'pickaxe', 'bolt', 'droplet', 'flask', 'globe', 'phone', 'mail', 'building' ] },
			{ name: 'badge_color', type: 'color' }
		],
		'#abidar-mobile-nav-list': [
			{ name: 'title_fa', placeholder: 'عنوان فارسی' },
			{ name: 'title_en', placeholder: 'English Title', dir: 'ltr' },
			{ name: 'title_zh', placeholder: '中文标题', dir: 'ltr' },
			{ name: 'url', placeholder: 'URL', dir: 'ltr' },
			{ name: 'icon', placeholder: 'home/layers/briefcase/award/phone', dir: 'ltr' },
			{ name: 'badge', placeholder: 'نشان (Badge)' }
		],
		'#abidar-custom-languages-list': [
			{ name: 'code', placeholder: 'کد زبان (مثلاً ar یا ru)', dir: 'ltr' },
			{ name: 'name', placeholder: 'نام انگلیسی (Arabic / Russian)', dir: 'ltr' },
			{ name: 'native_name', placeholder: 'نام بومی (العربية / Русский)' },
			{ name: 'flag', placeholder: 'پرچم (🇸🇦 / 🇷🇺)' },
			{ name: 'dir', type: 'select', options: [ 'rtl', 'ltr' ], selected: 'ltr' },
			{ name: 'enabled', type: 'checkbox', label: 'فعال' }
		]
	};

	/**
	 * ساختن یک ردیف خالی برای لیست، وقتی هنوز هیچ ردیفی وجود ندارد.
	 */
	const buildBlankRepeaterItem = ( containerId, baseName ) => {
		const fields = REPEATER_TEMPLATES[ containerId ];
		if ( ! fields ) {
			return null;
		}

		const item = $( '<div class="abidar-repeater-item" />' );
		const head = $( '<div class="abidar-repeater-item__head" />' );
		const row = $( '<div class="abidar-repeater-row" />' );

		head.append( $( '<strong />' ).text( 'ردیف جدید' ) );
		head.append(
			$( '<button type="button" class="button abidar-repeater-remove" />' )
				.text( 'حذف' )
				.attr( 'aria-label', 'حذف این ردیف' )
		);

		fields.forEach( ( field ) => {
			const nameAttr = baseName + '[0][' + field.name + ']';
			let control;

			if ( 'select' === field.type ) {
				control = $( '<select />' ).attr( 'name', nameAttr );
				field.options.forEach( ( optionValue ) => {
					const opt = $( '<option />' ).attr( 'value', optionValue ).text( optionValue );
					if ( field.selected === optionValue ) {
						opt.attr( 'selected', 'selected' );
					}
					control.append( opt );
				} );
			} else if ( 'checkbox' === field.type ) {
				const label = $( '<label />' );
				const checkbox = $( '<input type="checkbox" />' )
					.attr( 'name', nameAttr )
					.attr( 'value', '1' )
					.prop( 'checked', true );
				label.append( checkbox ).append( document.createTextNode( ' ' + ( field.label || '' ) ) );
				row.append( label );
				return;
			} else {
				control = $( '<input />' )
					.attr( 'type', field.type || 'text' )
					.attr( 'name', nameAttr );
				if ( field.placeholder ) {
					control.attr( 'placeholder', field.placeholder );
				}
				if ( field.dir ) {
					control.attr( 'dir', field.dir );
				}
				if ( 'color' === field.type ) {
					control.attr( 'type', 'text' ).addClass( 'abidar-color-picker' ).val( '#D97706' );
				}
			}

			row.append( control );
		} );

		item.append( head ).append( row );
		return item;
	};

	const initRepeaters = () => {
		$( document ).on( 'click', '.abidar-repeater-remove', function ( event ) {
			event.preventDefault();
			const item = $( this ).closest( '.abidar-repeater-item' );
			const container = item.parent();

			// ردیف‌های لیست‌های دکمه شناور و منوی موبایل می‌توانند کاملاً خالی شوند،
			// چون سرور برایشان مقدار پیش‌فرض می‌گذارد. زبان سفارشی هم همین‌طور.
			const removableToZero = container.is( '.abidar-repeater-list' );

			if ( ! removableToZero && container.find( '.abidar-repeater-item' ).length <= 1 ) {
				window.alert( 'حداقل یک آیتم باید در این لیست باقی بماند.' );
				return;
			}

			if ( window.confirm( 'آیا از حذف این ردیف مطمئن هستید؟ پس از ذخیره تنظیمات، حذف نهایی می‌شود.' ) ) {
				item.slideUp( 200, function () {
					$( this ).remove();
					refreshRepeaterHeadings( container );
				} );
			}
		} );

		$( document ).on( 'click', '.abidar-repeater-add', function ( event ) {
			event.preventDefault();

			const targetSelector = $( this ).data( 'target' );
			if ( ! targetSelector ) {
				return;
			}

			const container = $( targetSelector );
			if ( ! container.length ) {
				return;
			}

			const baseName = container.find( 'input, select' ).first().attr( 'name' )
				? container.find( 'input, select' ).first().attr( 'name' ).replace( /\[\d+\]\[[^\]]+\]$/, '' )
				: '';

			let source = container.find( '.abidar-repeater-item' ).first();

			// لیست خالی: ردیف را از صفر می‌سازیم تا دکمه هرگز بی‌اثر نباشد.
			if ( ! source.length ) {
				const blank = buildBlankRepeaterItem( targetSelector, baseName );
				if ( ! blank ) {
					return;
				}
				container.append( blank );
				source = blank;
			}

			const nextIndex = container.find( '.abidar-repeater-item' ).length;
			const clone = source.clone( true );

			clone.find( 'input, select, textarea' ).each( function () {
				const field = $( this );
				const name = field.attr( 'name' );

				if ( name ) {
					field.attr( 'name', name.replace( /\[\d+\]/, '[' + nextIndex + ']' ) );
				}

				if ( 'checkbox' === field.attr( 'type' ) ) {
					field.prop( 'checked', true );
				} else if ( 'select' === field.prop( 'tagName' ).toLowerCase() ) {
					// مقدار پیش‌فرض منوی انتخاب، همان انتخاب فعلی ردیف الگو است.
					if ( 'abidar-color-picker' === field.attr( 'class' ) ) {
						field.val( '#D97706' );
					}
				} else if ( field.hasClass( 'abidar-color-picker' ) ) {
					field.val( '#D97706' );
				} else if ( 'hidden' !== field.attr( 'type' ) ) {
					field.val( '' );
				}
			} );

			clone.hide().appendTo( container ).slideDown( 200 );
			refreshRepeaterHeadings( container );

			// به بقیه ماژول‌ها (مثل انتخاب‌گر رنگ) خبر بده که ردیف تازه اضافه شد.
			$( document ).trigger( 'abidar:repeater-added', [ container ] );

			clone.find( 'input' ).first().trigger( 'focus' );
		} );
	};

	/**
	 * شماره‌گذاری مجدد عنوان ردیف‌ها تا پس از حذف/افزودن، ترتیب درست بماند.
	 */
	const refreshRepeaterHeadings = ( container ) => {
		container.find( '.abidar-repeater-item' ).each( function ( index ) {
			const title = $( this ).find( '.abidar-repeater-item__head strong' );
			if ( title.length ) {
				title.text( title.text().replace( /(شماره|ردیف)\s*\d+/, 'شماره ' + ( index + 1 ) ) );
			}

			// نام فیلدها هم دوباره شماره‌گذاری شوند تا اندیس‌ها پشت‌سرهم بمانند.
			$( this ).find( 'input, select, textarea' ).each( function () {
				const field = $( this );
				const name = field.attr( 'name' );
				if ( name ) {
					field.attr( 'name', name.replace( /\[\d+\]/, '[' + index + ']' ) );
				}
			} );
		} );
	};

	/* ======================================================================
	 * 04b. SECURITY LOG FILTER
	 * ====================================================================
	 * نوار ابزار گزارش امنیتی داخل فرم اصلی ذخیره تنظیمات است، پس نمی‌تواند
	 * فرم مستقل داشته باشد. به‌جای آن مقادیر کنترل‌ها به آدرس صفحه اضافه
	 * می‌شوند و صفحه با فیلتر جدید باز می‌شود.
	 */
	const initLogFilter = () => {
		const toolbar = $( '[data-abidar-log-filter]' );
		if ( ! toolbar.length ) {
			return;
		}

		const applyFilters = () => {
			const base = toolbar.data( 'base-url' );
			if ( ! base ) {
				return;
			}

			const url = new URL( base, window.location.origin );

			toolbar.find( '[data-log-key]' ).each( function () {
				const field = $( this );
				const key = field.data( 'log-key' );
				const value = field.val();

				if ( value ) {
					url.searchParams.set( key, value );
				} else {
					url.searchParams.delete( key );
				}
			} );

			// با تغییر فیلتر، از صفحه اول شروع می‌کنیم.
			url.searchParams.delete( 'log_page' );

			window.location.href = url.toString();
		};

		toolbar.on( 'click', '[data-log-apply]', ( event ) => {
			event.preventDefault();
			applyFilters();
		} );

		// تغییر منوها فوراً اعمال می‌شود؛ جست‌وجو با Enter.
		toolbar.on( 'change', 'select[data-log-key]', applyFilters );
		toolbar.on( 'keydown', 'input[data-log-key]', ( event ) => {
			if ( 'Enter' === event.key ) {
				event.preventDefault();
				applyFilters();
			}
		} );

		/*
		 * پاک‌کردن گزارش: یک فرم واقعی داخل فرم تنظیمات جای نمی‌گیرد، پس با
		 * fetch به admin-post.php می‌فرستیم. هدر X-WP-Nonce و فیلد action
		 * هر دو لازم‌اند چون check_admin_referer روی پارامتر _wpnonce کار می‌کند.
		 */
		toolbar.on( 'click', '[data-log-clear]', ( event ) => {
			event.preventDefault();

			const button = event.currentTarget;
			const message = button.getAttribute( 'data-confirm' ) || '';

			if ( message && ! window.confirm( message ) ) {
				return;
			}

			const body = new window.FormData();
			body.append( 'action', 'abidar_clear_security_logs' );
			body.append( '_wpnonce', button.getAttribute( 'data-nonce' ) || '' );

			button.disabled = true;

			/*
			 * هندلر با قلاب admin_post_abidar_clear_security_logs ثبت شده و
			 * آن قلاب فقط روی admin-post.php اجرا می‌شود — نه admin-ajax.php.
			 * ارسال به آدرس اشتباه، بی‌صدا هیچ کاری نمی‌کرد.
			 */
			window.fetch( config.adminPost, {
				method: 'POST',
				credentials: 'same-origin',
				body,
			} )
				.then( () => {
					window.location.reload();
				} )
				.catch( () => {
					button.disabled = false;
				} );
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
		initSettingsMenu();
		initSidebarToggle();
		initMediaUploader();
		initRepeaters();
		initLogFilter();
		initDemoImporter();
		initLiveHints();
	} );
} )( window.jQuery );
