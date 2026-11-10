/**
 * ============================================================================
 * ABIDAR LANDING PAGE — اسکرول افقی (v3.0.1)
 * ----------------------------------------------------------------------------
 * فقط در حالت افقی بارگذاری می‌شود.
 * - چرخ ماوس عمودی ← اسکرول افقی
 * - کلیدهای ← → ↑ ↓ و PageUp/PageDown/Home/End
 * - دکمه‌های قبلی/بعدی و نوار پیشرفت
 * - زیر ۱۰۲۴ پیکسل خودکار غیرفعال می‌شود
 *
 * نکته حیاتی RTL
 * --------------
 * در حالت راست‌به‌چپ، مرورگرها در علامت scrollLeft توافق ندارند:
 *   - کروم و فایرفاکس: از ۰ شروع می‌شود و به منفی می‌رود.
 *   - سافاری: از ۰ شروع می‌شود و به مثبت می‌رود.
 * هر کدی که «scrollLeft = موقعیت» فرض کند در فارسی یا سافاری خراب می‌شود:
 * شاخص بخش غلط، نوار پیشرفت برعکس و دکمه‌ها بی‌اثر.
 *
 * راه‌حل: یک‌بار روی همان عنصر تست می‌کنیم که حرکت به سمت منفی مجاز است
 * یا نه؛ نتیجه در `rtlSign` کش می‌شود (+۱ یا -۱). مقدار مطلقِ scrollLeft
 * هم صرف‌نظر از علامت، فاصله از ابتدا را می‌دهد.
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
	var mq = window.matchMedia( '(min-width: 1025px)' );
	var rtlSign = null;

	/**
	 * جهت صفحه راست‌به‌چپ است؟
	 */
	function isRtl() {
		var dir = ( document.documentElement.getAttribute( 'dir' ) || '' ).toLowerCase();

		if ( dir ) {
			return 'rtl' === dir;
		}

		try {
			return 'rtl' === window.getComputedStyle( document.documentElement ).direction;
		} catch ( e ) {
			return false;
		}
	}

	var RTL = isRtl();

	/**
	 * آیا حالت افقی فعال است؟ (فقط دسکتاپ)
	 */
	function isHorizontal() {
		return mq.matches;
	}

	/**
	 * عرض یک بخش = گام اسکرول.
	 */
	function step() {
		return rail.clientWidth || window.innerWidth || 1;
	}

	/**
	 * بیشترین اسکرول ممکن.
	 */
	function maxScroll() {
		return Math.max( 0, rail.scrollWidth - rail.clientWidth );
	}

	/**
	 * کشف علامت اسکرول در RTL.
	 *
	 * اگر بشود scrollLeft را یک پیکسل منفی کرد، مرورگر از قرارداد
	 * «منفی» استفاده می‌کند (-۱)، وگرنه «مثبت» (+۱).
	 *
	 * @return {number} ۱ یا -۱
	 */
	function detectRtlSign() {
		if ( ! RTL ) {
			return 1;
		}

		if ( null !== rtlSign ) {
			return rtlSign;
		}

		var before = rail.scrollLeft;
		rail.scrollLeft = before - 1;
		var after = rail.scrollLeft;
		rail.scrollLeft = before;

		// اگر مقدار منفی «چسبید»، قرارداد منفی است.
		rtlSign = after !== before ? -1 : 1;

		return rtlSign;
	}

	/**
	 * فاصله از ابتدای مسیر (همیشه مثبت). مستقل از علامت مرورگر.
	 */
	function position() {
		return Math.max( 0, Math.min( Math.abs( rail.scrollLeft || 0 ), maxScroll() ) );
	}

	/**
	 * بخش فعلی.
	 */
	function currentIndex() {
		return Math.round( position() / step() );
	}

	/**
	 * رفتن به بخش مشخص.
	 *
	 * @param {number} index شاخص بخش.
	 */
	function goTo( index ) {
		if ( ! isHorizontal() || ! panels.length ) {
			return;
		}

		var clamped = Math.max( 0, Math.min( index, panels.length - 1 ) );
		var offset = clamped * step() * detectRtlSign();

		try {
			rail.scrollTo( { left: offset, behavior: 'smooth' } );
		} catch ( e ) {
			rail.scrollLeft = offset;
		}

		/*
		 * به syncUi تکیه نکنیم: رویداد scroll در پرش‌های برنامه‌ای (و در
		 * مرورگرهایی که scrollTo با behavior را نادیده می‌گیرند) ممکن است
		 * دیر یا اصلاً اجرا نشود؛ نتیجه‌اش نوار پیشرفت و دکمه‌های بی‌حالت
		 * است. یک بار مستقیم و یک بار پس از پایان انیمیشن به‌روزرسانی می‌کنیم.
		 */
		syncUi();
		window.setTimeout( syncUi, 420 );
	}

	/**
	 * به‌روزرسانی نوار پیشرفت و وضعیت دکمه‌ها.
	 */
	function syncUi() {
		if ( ! isHorizontal() ) {
			return;
		}

		var max = maxScroll();
		var pct = max > 0 ? ( position() / max ) * 100 : 0;
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
	 */
	function onWheel( event ) {
		if ( ! isHorizontal() ) {
			return;
		}

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

		var pos = position();
		var max = maxScroll();

		// در دو انتها رویداد را آزاد بگذار تا کاربر گیر نکند.
		if ( ( delta < 0 && pos <= 1 ) || ( delta > 0 && pos >= max - 1 ) ) {
			return;
		}

		event.preventDefault();

		rail.scrollLeft = ( rail.scrollLeft || 0 ) + delta * detectRtlSign();
	}

	/**
	 * کیبورد.
	 */
	function onKey( event ) {
		if ( ! isHorizontal() ) {
			return;
		}

		var tag = ( event.target.tagName || '' ).toLowerCase();

		if ( 'input' === tag || 'textarea' === tag || 'select' === tag || event.target.isContentEditable ) {
			return;
		}

		var dir = 0;

		if ( 'ArrowRight' === event.key || 'ArrowDown' === event.key || 'PageDown' === event.key ) {
			dir = 1;
		} else if ( 'ArrowLeft' === event.key || 'ArrowUp' === event.key || 'PageUp' === event.key ) {
			dir = -1;
		}

		if ( dir ) {
			event.preventDefault();

			/*
			 * در RTL محتوای بعدی سمت چپ است، پس فلش چپ باید «جلو» ببرد.
			 * یعنی جهت کلید را برای RTL وارونه می‌کنیم.
			 */
			goTo( currentIndex() + ( RTL ? -dir : dir ) );
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

	function onBreakpoint() {
		rtlSign = null;

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
