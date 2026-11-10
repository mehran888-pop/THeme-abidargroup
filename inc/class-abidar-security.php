<?php
/**
 * Abidar Group High-Security Suite (v2.0.0)
 *
 * ماژول امنیت پیشرفته قالب گروه آبیدر:
 * ۱. تغییر آدرس ورود و مخفی‌سازی wp-login.php
 * ۲. محدودیت تعداد تلاش ورود و مسدودسازی هوشمند حملات Brute-Force
 * ۳. سیستم ضد اسپم چندلایه (تله عسل Honeypot، تله زمانی Time-Trap، کپچای ریاضی)
 * ۴. غیرفعال‌سازی XML-RPC، مسدودسازی استخراج کاربران از REST API و هدرهای امنیتی HTTP
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_Security
 */
class Abidar_Security {

	/**
	 * کلید ذخیره لاگ رویدادهای امنیتی در دیتابیس
	 */
	const LOG_OPTION_KEY = 'abidar_security_events_log';

	/**
	 * راه‌اندازی تمامی دیوارهای آتش و قلاب‌های امنیتی
	 */
	public static function init() {
		// ۰. پاک‌کردن گزارش امنیتی از پنل (POST با nonce)
		add_action( 'admin_post_abidar_clear_security_logs', array( __CLASS__, 'handle_clear_logs' ) );

		// ۱. هدرهای امنیتی HTTP
		if ( abidar_get_option( 'security_http_headers', true ) ) {
			add_action( 'send_headers', array( __CLASS__, 'send_security_headers' ) );
		}

		// ۲. مخفی‌سازی نسخه وردپرس برای جلوگیری از شناسایی آسیب‌پذیری‌ها
		if ( abidar_get_option( 'security_hide_wp_version', true ) ) {
			remove_action( 'wp_head', 'wp_generator' );
			add_filter( 'the_generator', '__return_empty_string' );
		}

		// ۳. غیرفعال‌سازی کامل XML-RPC (جلوگیری از حملات DDoS و Brute-Force)
		if ( abidar_get_option( 'security_disable_xmlrpc', true ) ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
			add_filter( 'wp_headers', array( __CLASS__, 'remove_x_pingback' ) );
		}

		// ۴. جلوگیری از لو رفتن نام کاربری ادمین از طریق REST API و ?author=1
		if ( abidar_get_option( 'security_block_user_enum', true ) ) {
			add_filter( 'rest_endpoints', array( __CLASS__, 'restrict_rest_user_endpoints' ) );
			add_action( 'template_redirect', array( __CLASS__, 'block_author_archive_scan' ) );
		}

		// ۵. محدودیت تعداد تلاش ورود و جلوگیری از ورود بیش از حد (Rate Limiting)
		if ( abidar_get_option( 'security_limit_login_enabled', true ) ) {
			add_filter( 'authenticate', array( __CLASS__, 'check_login_lockout' ), 30, 3 );
			add_action( 'wp_login_failed', array( __CLASS__, 'record_failed_login' ) );
			add_action( 'wp_login', array( __CLASS__, 'clear_login_attempts' ), 10, 2 );
			add_filter( 'login_errors', array( __CLASS__, 'mask_login_error_messages' ) );
		}

		// ۶. تغییر آدرس ورود ادمین (Custom Login URL Slug)
		if ( abidar_get_option( 'security_custom_login_enabled', false ) ) {
			add_action( 'init', array( __CLASS__, 'handle_custom_login_endpoint' ), 1 );
			add_action( 'login_init', array( __CLASS__, 'verify_direct_wp_login_access' ), 1 );
			add_filter( 'site_url', array( __CLASS__, 'filter_login_site_url' ), 10, 4 );
			add_filter( 'wp_redirect', array( __CLASS__, 'filter_login_wp_redirect' ), 10, 2 );
		}

		// ۷. سیستم ضد اسپم دیدگاه‌ها و فرم‌های ارتباطی (Honeypot + Time-Trap + Math Captcha)
		if ( abidar_get_option( 'security_antispam_honeypot', true ) || abidar_get_option( 'security_antispam_timetrap', true ) ) {
			add_action( 'comment_form_after_fields', array( __CLASS__, 'render_antispam_fields' ) );
			add_action( 'comment_form_logged_in_after', array( __CLASS__, 'render_antispam_fields' ) );
			add_filter( 'preprocess_comment', array( __CLASS__, 'validate_comment_antispam' ) );
		}

		// ۸. پردازش امن فرم تماس اختصاصی گروه آبیدر (AJAX)
		add_action( 'wp_ajax_abidar_submit_contact', array( __CLASS__, 'handle_secure_contact_ajax' ) );
		add_action( 'wp_ajax_nopriv_abidar_submit_contact', array( __CLASS__, 'handle_secure_contact_ajax' ) );
	}

	/**
	 * دریافت آدرس IP واقعی کاربر به صورت ایمن
	 *
	 * @return string
	 */
	public static function get_client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			$ip = '0.0.0.0';
		}
		return $ip;
	}

	/**
	 * بررسی قرار داشتن IP در لیست سفید (Whitelist)
	 *
	 * @param string $ip آدرس IP.
	 * @return bool
	 */
	public static function is_ip_whitelisted( $ip ) {
		$whitelist_raw = (string) abidar_get_option( 'security_ip_whitelist', '127.0.0.1' );

		// ورودی می‌تواند با کاما، فاصله یا خط جدید جدا شده باشد.
		$entries = preg_split( '/[\s,]+/', $whitelist_raw );
		$entries = is_array( $entries ) ? array_filter( array_map( 'trim', $entries ) ) : array();

		foreach ( $entries as $entry ) {
			if ( $entry === $ip ) {
				return true;
			}

			// پشتیبانی از محدوده CIDR: 198.51.100.0/24
			if ( false !== strpos( $entry, '/' ) && self::ip_in_cidr( $ip, $entry ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * آیا یک IPv4 داخل محدوده CIDR مشخص هست؟
	 *
	 * @param string $ip   آدرس بازدیدکننده.
	 * @param string $cidr محدوده به شکل 198.51.100.0/24.
	 * @return bool
	 */
	private static function ip_in_cidr( $ip, $cidr ) {
		$parts = explode( '/', $cidr );
		if ( 2 !== count( $parts ) ) {
			return false;
		}

		$subnet   = $parts[0];
		$mask_len = (int) $parts[1];

		// فقط IPv4 با این روش قابل مقایسه بیتی است.
		if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
			|| ! filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
			|| $mask_len < 0 || $mask_len > 32 ) {
			return false;
		}

		$mask = -1 << ( 32 - $mask_len );

		return ( ip2long( $ip ) & $mask ) === ( ip2long( $subnet ) & $mask );
	}

	/**
	 * ارسال هدرهای امنیتی استاندارد جهانی در پاسخ‌های HTTP
	 */
	public static function send_security_headers() {
		if ( headers_sent() ) {
			return;
		}
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-XSS-Protection: 1; mode=block' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
	}

	/**
	 * حذف هدر X-Pingback
	 *
	 * @param array $headers هدرهای فعلی.
	 * @return array
	 */
	public static function remove_x_pingback( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}

	/**
	 * مسدودسازی دسترسی غیرمجاز به لیست کاربران در REST API
	 *
	 * @param array $endpoints نقاط پایانی REST.
	 * @return array
	 */
	public static function restrict_rest_user_endpoints( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			if ( isset( $endpoints['/wp/v2/users'] ) ) {
				unset( $endpoints['/wp/v2/users'] );
			}
			if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
				unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
			}
		}
		return $endpoints;
	}

	/**
	 * جلوگیری از اسکن نام کاربری با پارامتر ?author=N
	 */
	public static function block_author_archive_scan() {
		if ( ! is_admin() && isset( $_GET['author'] ) && ! current_user_can( 'edit_posts' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect( home_url(), 301 );
			exit;
		}
	}

	/**
	 * بررسی وضعیت مسدودیت IP پیش از بررسی رمز عبور
	 *
	 * @param WP_User|WP_Error|null $user     شیء کاربر.
	 * @param string                $username نام کاربری.
	 * @param string                $password رمز عبور.
	 * @return WP_User|WP_Error|null
	 */
	public static function check_login_lockout( $user, $username, $password ) {
		if ( empty( $username ) && empty( $password ) ) {
			return $user;
		}

		$ip = self::get_client_ip();
		if ( self::is_ip_whitelisted( $ip ) ) {
			return $user;
		}

		$lockout_key = 'abidar_lockout_' . md5( $ip );
		$locked_until = get_transient( $lockout_key );

		if ( $locked_until && $locked_until > time() ) {
			$remaining_min = ceil( ( $locked_until - time() ) / 60 );
			return new WP_Error(
				'abidar_too_many_attempts',
				sprintf(
					/* translators: %d: remaining lockout minutes */
					esc_html__( 'خطای امنیتی گروه آبیدر: به دلیل تلاش‌های ناموفق بیش از حد مجاز، دسترسی ورود شما به مدت %d دقیقه مسدود شده است.', 'abidar-group' ),
					(int) $remaining_min
				)
			);
		}

		return $user;
	}

	/**
	 * ثبت تلاش ناموفق ورود و اعمال مسدودیت در صورت عبور از سقف مجاز
	 *
	 * @param string $username نام کاربری وارد شده.
	 */
	public static function record_failed_login( $username ) {
		$ip = self::get_client_ip();
		if ( self::is_ip_whitelisted( $ip ) ) {
			return;
		}

		$max_attempts     = max( 2, (int) abidar_get_option( 'security_max_login_attempts', 4 ) );
		$lockout_duration = max( 5, (int) abidar_get_option( 'security_lockout_duration', 30 ) );

		$attempts_key = 'abidar_attempts_' . md5( $ip );
		$attempts     = (int) get_transient( $attempts_key );
		$attempts++;

		set_transient( $attempts_key, $attempts, $lockout_duration * MINUTE_IN_SECONDS );

		if ( $attempts >= $max_attempts ) {
			$lockout_key  = 'abidar_lockout_' . md5( $ip );
			$locked_until = time() + ( $lockout_duration * MINUTE_IN_SECONDS );
			set_transient( $lockout_key, $locked_until, $lockout_duration * MINUTE_IN_SECONDS );

			self::log_security_event(
				'login_lockout',
				sprintf( 'مسدودسازی IP به علت %d تلاش ناموفق ورود با نام کاربری: %s', $attempts, sanitize_user( $username ) ),
				$ip
			);
		} else {
			self::log_security_event(
				'failed_login',
				sprintf( 'تلاش ناموفق ورود (%d از %d) با نام کاربری: %s', $attempts, $max_attempts, sanitize_user( $username ) ),
				$ip
			);
		}
	}

	/**
	 * پاک‌سازی شمارنده خطا پس از ورود موفق
	 */
	public static function clear_login_attempts() {
		$ip = self::get_client_ip();
		delete_transient( 'abidar_attempts_' . md5( $ip ) );
		delete_transient( 'abidar_lockout_' . md5( $ip ) );
	}

	/**
	 * مبهم‌سازی پیام خطای ورود جهت جلوگیری از تشخیص وجود نام کاربری
	 *
	 * @param string $error متن خطا.
	 * @return string
	 */
	public static function mask_login_error_messages( $error ) {
		if ( strpos( $error, 'abidar_too_many_attempts' ) !== false || strpos( $error, 'خطای امنیتی گروه آبیدر' ) !== false ) {
			return $error;
		}
		return esc_html__( 'اطلاعات کاربری یا گذرواژه وارد شده صحیح نمی‌باشد. (سپر امنیتی گروه آبیدر)', 'abidar-group' );
	}

	/**
	 * مدیریت آدرس ورود سفارشی (تغییر آدرس ادمین)
	 */
	public static function handle_custom_login_endpoint() {
		$slug = sanitize_title( abidar_get_option( 'security_custom_login_slug', 'abidar-secure-gateway' ) );
		if ( empty( $slug ) ) {
			return;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path        = trim( wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

		if ( $path === $slug || substr( $path, -strlen( $slug ) ) === $slug ) {
			if ( ! defined( 'ABIDAR_CUSTOM_LOGIN_VERIFIED' ) ) {
				define( 'ABIDAR_CUSTOM_LOGIN_VERIFIED', true );
			}
			require_once ABSPATH . 'wp-login.php';
			exit;
		}
	}

	/**
	 * جلوگیری از باز شدن مستقیم wp-login.php زمانی که آدرس ورود سفارشی فعال است
	 */
	public static function verify_direct_wp_login_access() {
		$slug = sanitize_title( abidar_get_option( 'security_custom_login_slug', 'abidar-secure-gateway' ) );
		if ( empty( $slug ) ) {
			return;
		}

		// اجازه خروج از حساب یا اکشن‌های مجاز دارای توکن
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( in_array( $action, array( 'logout', 'postpass' ), true ) ) {
			return;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		if ( strpos( $request_uri, 'wp-login.php' ) !== false && ! defined( 'ABIDAR_CUSTOM_LOGIN_VERIFIED' ) && ! isset( $_GET['abidar_token'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			self::log_security_event( 'blocked_direct_login', 'تلاش برای دسترسی مستقیم به wp-login.php مسدود شد', self::get_client_ip() );
			status_header( 403 );
			wp_die(
				esc_html__( 'دسترسی مستقیم به این آدرس توسط سپر امنیتی هلدینگ گروه آبیدر مسدود شده است.', 'abidar-group' ),
				esc_html__( 'دسترسی غیرمجاز - 403 Forbidden', 'abidar-group' ),
				array( 'response' => 403 )
			);
		}
	}

	/**
	 * جایگزینی wp-login.php با اسلاگ امن سفارشی در لینک‌های تولید شده وردپرس
	 *
	 * @param string $url     آدرس کامل.
	 * @param string $path    مسیر.
	 * @param string $scheme  پروتکل.
	 * @param int    $blog_id شناسه سایت.
	 * @return string
	 */
	public static function filter_login_site_url( $url, $path, $scheme, $blog_id ) {
		return self::replace_login_url( $url );
	}

	/**
	 * جایگزینی wp-login.php در تغییر مسیرها (Redirects)
	 *
	 * @param string $location آدرس مقصد.
	 * @param int    $status   کد وضعیت HTTP.
	 * @return string
	 */
	public static function filter_login_wp_redirect( $location, $status ) {
		return self::replace_login_url( $location );
	}

	/**
	 * تبدیل لینک پیش‌فرض ورود به آدرس اختصاصی تعریف شده در تنظیمات
	 *
	 * @param string $url آدرس ورودی.
	 * @return string
	 */
	private static function replace_login_url( $url ) {
		if ( strpos( $url, 'wp-login.php' ) === false ) {
			return $url;
		}
		$slug = sanitize_title( abidar_get_option( 'security_custom_login_slug', 'abidar-secure-gateway' ) );
		if ( empty( $slug ) ) {
			return $url;
		}
		return add_query_arg( 'abidar_token', wp_create_nonce( 'abidar_login_gate' ), str_replace( 'wp-login.php', $slug, $url ) );
	}

	/**
	 * رندر فیلدهای ضد اسپم (Honeypot + Time-Trap + کپچای ریاضی) در فرم‌ها
	 */
	public static function render_antispam_fields() {
		$timestamp = time();
		$signature = wp_hash( $timestamp . '|abidar_antispam' );
		$num1      = wp_rand( 2, 9 );
		$num2      = wp_rand( 1, 8 );
		$math_hash = wp_hash( ( $num1 + $num2 ) . '|abidar_math' );
		?>
		<!-- Abidar Enterprise Anti-Spam Shield -->
		<div class="abidar-hp-trap" aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;height:0;width:0;overflow:hidden;">
			<label for="abidar_hp_website_url"><?php esc_html_e( 'Leave this field empty', 'abidar-group' ); ?></label>
			<input type="text" name="abidar_hp_website_url" id="abidar_hp_website_url" value="" tabindex="-1" autocomplete="off" />
		</div>
		<input type="hidden" name="abidar_form_ts" value="<?php echo esc_attr( $timestamp ); ?>" />
		<input type="hidden" name="abidar_form_sig" value="<?php echo esc_attr( $signature ); ?>" />
		<?php if ( abidar_get_option( 'security_math_captcha', true ) ) : ?>
			<div class="abidar-security-captcha" style="margin: 14px 0;">
				<label for="abidar_math_answer" style="display:block;font-weight:600;margin-bottom:6px;">
					<?php
					printf(
						/* translators: 1: first number, 2: second number */
						esc_html__( 'پرسش امنیتی ضد ربات: حاصل عبارت %1$d + %2$d چند می‌شود؟ *', 'abidar-group' ),
						(int) $num1,
						(int) $num2
					);
					?>
				</label>
				<input type="number" name="abidar_math_answer" id="abidar_math_answer" required class="abidar-input" style="max-width:160px;" placeholder="?" />
				<input type="hidden" name="abidar_math_hash" value="<?php echo esc_attr( $math_hash ); ?>" />
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * اعتبارسنجی ضد اسپم روی دیدگاه‌های ارسالی وردپرس
	 *
	 * @param array $commentdata داده‌های دیدگاه.
	 * @return array
	 */
	public static function validate_comment_antispam( $commentdata ) {
		if ( is_user_logged_in() && current_user_can( 'moderate_comments' ) ) {
			return $commentdata;
		}

		$check = self::verify_submission_security( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( is_wp_error( $check ) ) {
			self::log_security_event( 'spam_blocked', $check->get_error_message(), self::get_client_ip() );
			wp_die(
				esc_html( $check->get_error_message() ),
				esc_html__( 'خطای امنیتی ضد اسپم - گروه آبیدر', 'abidar-group' ),
				array( 'response' => 403, 'back_link' => true )
			);
		}

		// بررسی تعداد لینک‌های داخل متن دیدگاه برای جلوگیری از لینک‌اسپم
		$link_count = preg_match_all( '/https?:\/\//i', $commentdata['comment_content'], $matches );
		if ( $link_count > 2 ) {
			self::log_security_event( 'spam_links_blocked', 'دیدگاه حاوی بیش از ۲ لینک خارجی مسدود شد', self::get_client_ip() );
			wp_die(
				esc_html__( 'ارسال بیش از ۲ لینک در دیدگاه‌ها به دلایل امنیتی مجاز نمی‌باشد.', 'abidar-group' ),
				esc_html__( 'جلوگیری از هرزنامه - گروه آبیدر', 'abidar-group' ),
				array( 'response' => 403, 'back_link' => true )
			);
		}

		return $commentdata;
	}

	/**
	 * اعتبارسنجی مشترک تله عسل (Honeypot)، تله زمانی (Time-Trap) و کپچای ریاضی
	 *
	 * @param array $payload داده‌های فرم ارسالی.
	 * @return true|WP_Error
	 */
	public static function verify_submission_security( $payload ) {
		// ۱. بررسی فیلد مخفی Honeypot
		if ( abidar_get_option( 'security_antispam_honeypot', true ) ) {
			if ( ! empty( $payload['abidar_hp_website_url'] ) ) {
				return new WP_Error( 'bot_honeypot', esc_html__( 'ارسال خودکار ربات (Honeypot) شناسایی و مسدود شد.', 'abidar-group' ) );
			}
		}

		// ۲. بررسی حداقل زمان پر کردن فرم (Time-Trap)
		if ( abidar_get_option( 'security_antispam_timetrap', true ) && isset( $payload['abidar_form_ts'], $payload['abidar_form_sig'] ) ) {
			$ts           = (int) $payload['abidar_form_ts'];
			$sig          = sanitize_text_field( wp_unslash( $payload['abidar_form_sig'] ) );
			$expected_sig = wp_hash( $ts . '|abidar_antispam' );
			$min_seconds  = max( 1, (int) abidar_get_option( 'security_antispam_min_seconds', 3 ) );

			if ( ! hash_equals( $expected_sig, $sig ) || ( time() - $ts ) < $min_seconds ) {
				return new WP_Error( 'bot_timetrap', esc_html__( 'فرم بیش از حد سریع ارسال شده است. لطفاً چند ثانیه صبر کرده و مجدداً تلاش کنید.', 'abidar-group' ) );
			}
		}

		// ۳. بررسی کپچای ریاضی
		if ( abidar_get_option( 'security_math_captcha', true ) && isset( $payload['abidar_math_hash'] ) ) {
			$answer        = isset( $payload['abidar_math_answer'] ) ? (int) $payload['abidar_math_answer'] : -1;
			$provided_hash = sanitize_text_field( wp_unslash( $payload['abidar_math_hash'] ) );
			$expected_hash = wp_hash( $answer . '|abidar_math' );

			if ( ! hash_equals( $expected_hash, $provided_hash ) ) {
				return new WP_Error( 'invalid_captcha', esc_html__( 'پاسخ پرسش امنیتی ریاضی صحیح نیست.', 'abidar-group' ) );
			}
		}

		return true;
	}

	/**
	 * پردازش AJAX فرم تماس چندبخشی گروه آبیدر با حفاظت کامل در برابر اسپم
	 */
	public static function handle_secure_contact_ajax() {
		check_ajax_referer( 'abidar_frontend_nonce', 'nonce' );

		$security_check = self::verify_submission_security( $_POST );
		if ( is_wp_error( $security_check ) ) {
			wp_send_json_error( array( 'message' => $security_check->get_error_message() ), 403 );
		}

		$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$department = isset( $_POST['department'] ) ? sanitize_text_field( wp_unslash( $_POST['department'] ) ) : '';
		$subject    = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

		if ( empty( $name ) || empty( $email ) || ! is_email( $email ) || empty( $message ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'لطفاً نام، ایمیل معتبر و متن درخواست خود را به صورت کامل وارد نمایید.', 'abidar-group' ),
				),
				400
			);
		}

		// ثبت درخواست در جدول پیام‌های دریافتی هلدینگ و ارسال ایمیل به مدیر
		$submissions = get_option( 'abidar_contact_submissions', array() );
		if ( ! is_array( $submissions ) ) {
			$submissions = array();
		}

		$tracking_code = 'AB-' . wp_rand( 100000, 999999 );
		array_unshift(
			$submissions,
			array(
				'tracking_code' => $tracking_code,
				'name'          => $name,
				'email'         => $email,
				'phone'         => $phone,
				'department'    => $department,
				'subject'       => $subject,
				'message'       => $message,
				'ip'            => self::get_client_ip(),
				'date'          => current_time( 'mysql' ),
			)
		);

		update_option( 'abidar_contact_submissions', array_slice( $submissions, 0, 100 ), false );

		wp_send_json_success(
			array(
				'tracking_code' => $tracking_code,
				'message'       => sprintf(
					/* translators: %s: tracking code */
					esc_html__( 'درخواست رسمی شما با موفقیت در دبیرخانه هلدینگ گروه آبیدر ثبت شد. کد پیگیری اختصاصی شما: %s', 'abidar-group' ),
					$tracking_code
				),
			)
		);
	}

	/**
	 * ثبت رویداد امنیتی در گزارش پیشخوان وردپرس
	 *
	 * @param string $type    نوع رویداد.
	 * @param string $details شرح رویداد.
	 * @param string $ip      آدرس IP.
	 */
	public static function log_security_event( $type, $details, $ip = '' ) {
		$logs = get_option( self::LOG_OPTION_KEY, array() );
		if ( ! is_array( $logs ) ) {
			$logs = array();
		}

		array_unshift(
			$logs,
			array(
				'type'    => sanitize_key( $type ),
				'details' => sanitize_text_field( $details ),
				'ip'      => sanitize_text_field( $ip ? $ip : self::get_client_ip() ),
				'time'    => current_time( 'mysql' ),
			)
		);

		update_option( self::LOG_OPTION_KEY, array_slice( $logs, 0, 50 ), false );
	}

	/**
	 * دریافت لیست آخرین رویدادهای امنیتی برای نمایش در پنل تنظیمات قالب
	 *
	 * @return array
	 */
	public static function get_security_logs() {
		$logs = get_option( self::LOG_OPTION_KEY, array() );
		if ( ! is_array( $logs ) || empty( $logs ) ) {
			return array(
				array(
					'type'    => 'shield_active',
					'details' => 'سپر امنیتی نسخه ۲.۰ گروه آبیدر فعال است (حفاظت Brute-Force، تغییر آدرس ورود و ضد اسپم)',
					'ip'      => '127.0.0.1',
					'time'    => current_time( 'mysql' ),
				),
			);
		}
		return $logs;
	}

	/**
	 * دریافت یک صفحه از گزارش امنیتی با فیلتر و جست‌وجو.
	 *
	 * گزارش زنده تا ۵۰ رویداد نگه می‌دارد و نمایش همه آن‌ها در یک جدول، پنل را
	 * شلوغ و کند می‌کند. این متد صفحه‌بندی می‌کند تا مدیر فقط همان تعدادی را
	 * ببیند که خواسته و بتواند بین رویدادها جست‌وجو یا بر اساس نوع فیلتر کند.
	 *
	 * @param array $args {
	 *     @type int    $page     شماره صفحه (از ۱).
	 *     @type int    $per_page تعداد رویداد در هر صفحه.
	 *     @type string $type     فیلتر نوع رویداد؛ خالی یعنی همه.
	 *     @type string $search   جست‌وجو در شرح و IP.
	 * }
	 * @return array {
	 *     @type array $items      رویدادهای همین صفحه.
	 *     @type int   $total      کل رویدادهای مطابق فیلتر.
	 *     @type int   $page       صفحه جاری.
	 *     @type int   $per_page   تعداد در هر صفحه.
	 *     @type int   $pages      کل صفحات.
	 *     @type array $types      همه نوع‌های موجود با تعداد.
	 * }
	 */
	public static function get_security_logs_page( $args = array() ) {
		$defaults = array(
			'page'     => 1,
			'per_page' => 10,
			'type'     => '',
			'search'   => '',
		);
		$args     = wp_parse_args( $args, $defaults );

		$per_page = max( 1, min( 50, (int) $args['per_page'] ) );
		$page     = max( 1, (int) $args['page'] );

		$all = self::get_security_logs();

		// شمارش نوع‌ها پیش از فیلتر، تا فهرست کشویی همیشه کامل بماند.
		$types = array();
		foreach ( $all as $row ) {
			$type = isset( $row['type'] ) ? $row['type'] : 'unknown';
			$types[ $type ] = isset( $types[ $type ] ) ? $types[ $type ] + 1 : 1;
		}
		arsort( $types );

		$type   = sanitize_key( $args['type'] );
		$search = trim( (string) $args['search'] );

		$filtered = array();
		foreach ( $all as $row ) {
			if ( '' !== $type && ( isset( $row['type'] ) ? $row['type'] : '' ) !== $type ) {
				continue;
			}

			if ( '' !== $search ) {
				$haystack = ( isset( $row['details'] ) ? $row['details'] : '' ) . ' ' . ( isset( $row['ip'] ) ? $row['ip'] : '' ) . ' ' . ( isset( $row['type'] ) ? $row['type'] : '' );
				// mbstring همیشه روی هاست‌های وردپرس فعال نیست؛ در نبودش به stripos برگرد.
				$found = function_exists( 'mb_stripos' )
					? mb_stripos( $haystack, $search )
					: stripos( $haystack, $search );

				if ( false === $found ) {
					continue;
				}
			}

			$filtered[] = $row;
		}

		$total = count( $filtered );
		$pages = (int) ceil( $total / $per_page );
		$pages = max( 1, $pages );
		$page  = min( $page, $pages );

		return array(
			'items'    => array_slice( $filtered, ( $page - 1 ) * $per_page, $per_page ),
			'total'    => $total,
			'all'      => count( $all ),
			'page'     => $page,
			'per_page' => $per_page,
			'pages'    => $pages,
			'types'    => $types,
		);
	}

	/**
	 * پردازش درخواست پاک‌کردن گزارش از پنل تنظیمات.
	 *
	 * @return void
	 */
	public static function handle_clear_logs() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی مجاز نیست.', 'abidar-group' ), 403 );
		}

		check_admin_referer( 'abidar_clear_logs' );

		self::clear_security_logs();
		self::log_security_event( 'logs_cleared', __( 'گزارش امنیتی توسط مدیر پاک شد.', 'abidar-group' ) );

		$redirect = add_query_arg(
			array(
				'page'    => 'abidar-settings',
				'tab'     => 'security',
				'cleared' => '1',
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect . '#security-log' );
		exit;
	}

	/**
	 * پاک کردن کامل گزارش امنیتی.
	 *
	 * @return bool
	 */
	public static function clear_security_logs() {
		return delete_option( self::LOG_OPTION_KEY );
	}
}

Abidar_Security::init();
