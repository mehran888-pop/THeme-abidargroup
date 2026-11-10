/**
 * ============================================================================
 * ABIDAR LANDING PAGE — اسکرول افقی (v3.0.0)
 * ----------------------------------------------------------------------------
 * فقط در حالت افقی بارگذاری می‌شود.
 * - چرخ ماوس عمودی ← اسکرول افقی
 * - کلیدهای ← → ↑ ↓ و PageUp/PageDown/Home/End
 * - دکمه‌های قبلی/بعدی و نوار پیشرفت
 * - زیر ۱۰۲۴ پیکسل خودکار غیرفعال می‌شود
 * ============================================================================
 */
( function () {
	'use strict';

	var main = document.getElementById( 'abidar-landing' );

	if ( ! main ) {
		return;
	}

	var rail = main.querySelector( '[data-landing-rail]' );

	if ( ! rail ) {
		return;
	}

	var panels = Array.prototype.slice.call( rail.querySelectorAll( '[data-landing-panel]' ) );
	var prevBtn = main.querySelector( '[data-landing-prev]' );
	var nextBtn = main.querySelector( '[data-landing-next]' );
	var bar = main.querySelector( '[data-landing-progress-bar]' );
	var cfg = window.abidarLanding || {};
	var mq = window.matchMedia( '(min-width: 1025px)' );

	/**
	 * آیا حالت افقی فعال است؟ (فقط دسکتاپ)
	 */
	function isHorizontal() {
		return mq.matches;
	}

	/**
	 * صفحه فعلی بر اساس موقعیت اسکرول.
	 */
	function currentIndex() {
		var w = rail.clientWidth || window.innerWidth;
		return Math.round( rail.scrollLeft / w );
	}

	/**
	 * رفتن به بخش مشخص.
	 */
	function goTo( index ) {
		if ( ! isHorizontal() || ! panels.length ) {
			return;
		}

		var clamped = Math.max( 0, Math.min( index, panels.length - 1 ) );
		var offset = clamped * ( rail.clientWidth || window.innerWidth );

		rail.scrollTo( {
			left: offset,
			behavior: cfg.snap === false ? 'smooth' : 'auto'
		} );
	}

	/**
	 * به‌روزرسانی نوار پیشرفت و وضعیت دکمه‌ها.
	 */
	function syncUi() {
		if ( ! isHorizontal() ) {
			return;
		}

		var max = rail.scrollWidth - rail.clientWidth;
		var pct = max > 0 ? ( rail.scrollLeft / max ) * 100 : 0;
		var idx = currentIndex();

		if ( bar ) {
			bar.style.width = pct.toFixed( 2 ) + '%';
		}

		if ( prevBtn ) {
			prevBtn.disabled = idx <= 0;
		}

		if ( nextBtn ) {
			nextBtn.disabled = idx >= panels.length - 1;
		}
	}

	/**
	 * چرخ ماوس: عمودی ← افقی.
	 * اگر ریل به انتها رسیده باشد، رویداد را آزاد می‌گذاریم تا صفحه
	 * به‌صورت طبیعی ادامه دهد و کاربر گیر نکند.
	 */
	function onWheel( event ) {
		if ( ! isHorizontal() ) {
			return;
		}

		// اگر روی المان اسکرول‌دار داخلی هستیم، دخالت نکن.
		var scrollable = event.target.closest(
			'.abidar-landing__panel [data-no-hscroll], textarea, .abidar-scroll-inner'
		);
		if ( scrollable ) {
			return;
		}

		var delta = Math.abs( event.deltaX ) > Math.abs( event.deltaY ) ? event.deltaX : event.deltaY;

		if ( 0 === delta ) {
			return;
		}

		var atStart = rail.scrollLeft <= 1;
		var atEnd = rail.scrollLeft >= rail.scrollWidth - rail.clientWidth - 1;

		if ( ( delta < 0 && atStart ) || ( delta > 0 && atEnd ) ) {
			return;
		}

		event.preventDefault();
		rail.scrollLeft += delta;
	}

	/**
	 * کیبورد.
	 */
	function onKey( event ) {
		if ( ! isHorizontal() ) {
			return;
		}

		// اگر کاربر در فرم یا ورودی است، دخالت نکن.
		var tag = ( event.target.tagName || '' ).toLowerCase();
		if ( 'input' === tag || 'textarea' === tag || 'select' === tag || event.target.isContentEditable ) {
			return;
		}

		var keys = {
			ArrowLeft: -1,
			ArrowUp: -1,
			ArrowRight: 1,
			ArrowDown: 1,
			PageUp: -1,
			PageDown: 1
		};

		// در RTL جهت‌ها برعکس می‌شوند.
		var rtl = 'rtl' === ( document.documentElement.getAttribute( 'dir' ) || '' ).toLowerCase();
		var dir = keys[ event.key ];

		if ( dir ) {
			event.preventDefault();
			if ( rtl ) {
				dir = -dir;
			}
			goTo( currentIndex() + dir );
			return;
		}

		if ( 'Home' === event.key ) {
			event.preventDefault();
			goTo( 0 );
		} else if ( 'End' === event.key ) {
			event.preventDefault();
			goTo( panels.length - 1 );
		}
	}

	if ( prevBtn ) {
		prevBtn.addEventListener( 'click', function () {
			goTo( currentIndex() - 1 );
		} );
	}

	if ( nextBtn ) {
		nextBtn.addEventListener( 'click', function () {
			goTo( currentIndex() + 1 );
		} );
	}

	rail.addEventListener( 'scroll', syncUi, { passive: true } );
	main.addEventListener( 'wheel', onWheel, { passive: false } );
	document.addEventListener( 'keydown', onKey );

	// وقتی از حالت دسکتاپ خارج شدیم، متن‌ها را از حالت افقی دربیاور.
	function onBreakpoint() {
		if ( isHorizontal() ) {
			syncUi();
		} else {
			rail.scrollLeft = 0;
		}
	}

	if ( mq.addEventListener ) {
		mq.addEventListener( 'change', onBreakpoint );
	} else if ( mq.addListener ) {
		mq.addListener( onBreakpoint );
	}

	window.addEventListener( 'load', syncUi );
	syncUi();
} )();
