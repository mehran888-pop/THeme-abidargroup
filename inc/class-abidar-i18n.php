<?php
/**
 * Abidar Group Multilingual & Localization Engine (v2.0.0)
 *
 * سیستم مدیریت سه‌زبانه پیش‌فرض (فارسی، انگلیسی، چینی) + امکان افزودن بی‌نهایت زبان سفارشی از پیشخوان وردپرس
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_I18n
 */
class Abidar_I18n {

	/**
	 * کد زبان فعلی
	 *
	 * @var string
	 */
	private static $current_lang = null;

	/**
	 * راه‌اندازی قلاب‌های تغییر زبان و کوکی
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'detect_language_switch' ), 5 );
	}

	/**
	 * دریافت لیست سه زبان پیش‌فرض قالب گروه آبیدر
	 *
	 * @return array
	 */
	public static function get_core_languages() {
		return array(
			'fa' => array(
				'code'        => 'fa',
				'locale'      => 'fa_IR',
				'name'        => 'Persian',
				'native_name' => 'فارسی',
				'flag'        => '🇮🇷',
				'dir'         => 'rtl',
				'is_core'     => true,
				'enabled'     => true,
			),
			'en' => array(
				'code'        => 'en',
				'locale'      => 'en_US',
				'name'        => 'English',
				'native_name' => 'English',
				'flag'        => '🇬🇧',
				'dir'         => 'ltr',
				'is_core'     => true,
				'enabled'     => true,
			),
			'zh' => array(
				'code'        => 'zh',
				'locale'      => 'zh_CN',
				'name'        => 'Chinese',
				'native_name' => '中文 (简体)',
				'flag'        => '🇨🇳',
				'dir'         => 'ltr',
				'is_core'     => true,
				'enabled'     => true,
			),
		);
	}

	/**
	 * دریافت تمام زبان‌های فعال (۳ زبان اصلی + زبان‌های اضافه‌شده در پیشخوان)
	 *
	 * @return array
	 */
	public static function get_languages() {
		$languages   = self::get_core_languages();
		$custom_list = abidar_get_option( 'custom_languages', array() );

		if ( is_array( $custom_list ) ) {
			foreach ( $custom_list as $custom ) {
				if ( empty( $custom['code'] ) ) {
					continue;
				}
				$code = sanitize_key( $custom['code'] );
				if ( isset( $custom['enabled'] ) && ! $custom['enabled'] ) {
					continue;
				}
				$languages[ $code ] = array(
					'code'         => $code,
					'locale'       => isset( $custom['locale'] ) ? sanitize_text_field( $custom['locale'] ) : $code,
					'name'         => isset( $custom['name'] ) ? sanitize_text_field( $custom['name'] ) : strtoupper( $code ),
					'native_name'  => isset( $custom['native_name'] ) ? sanitize_text_field( $custom['native_name'] ) : strtoupper( $code ),
					'flag'         => isset( $custom['flag'] ) ? sanitize_text_field( $custom['flag'] ) : '🌐',
					'dir'          => ( isset( $custom['dir'] ) && 'rtl' === $custom['dir'] ) ? 'rtl' : 'ltr',
					'is_core'      => false,
					'enabled'      => true,
					'translations' => isset( $custom['translations'] ) && is_array( $custom['translations'] ) ? $custom['translations'] : array(),
				);
			}
		}

		return $languages;
	}

	/**
	 * تشخیص تغییر زبان از طریق پارامتر URL و ذخیره در کوکی امن
	 */
	public static function detect_language_switch() {
		$languages = self::get_languages();

		if ( isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$requested = sanitize_key( wp_unslash( $_GET['lang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $languages[ $requested ] ) ) {
				self::$current_lang = $requested;
				if ( ! headers_sent() ) {
					setcookie( 'abidar_lang', $requested, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
				}
				return;
			}
		}

		if ( isset( $_COOKIE['abidar_lang'] ) ) {
			$cookie_lang = sanitize_key( wp_unslash( $_COOKIE['abidar_lang'] ) );
			if ( isset( $languages[ $cookie_lang ] ) ) {
				self::$current_lang = $cookie_lang;
				return;
			}
		}

		$default = abidar_get_option( 'default_language', 'fa' );
		self::$current_lang = isset( $languages[ $default ] ) ? $default : 'fa';
	}

	/**
	 * دریافت کد زبان جاری سایت
	 *
	 * @return string
	 */
	public static function get_current_lang() {
		if ( null === self::$current_lang ) {
			self::detect_language_switch();
		}
		return self::$current_lang ? self::$current_lang : 'fa';
	}

	/**
	 * بررسی راست‌به‌چپ بودن زبان فعلی یا زبان مشخص شده
	 *
	 * @param string|null $lang کد زبان.
	 * @return bool
	 */
	public static function is_rtl( $lang = null ) {
		if ( null === $lang ) {
			$lang = self::get_current_lang();
		}
		$languages = self::get_languages();
		if ( isset( $languages[ $lang ]['dir'] ) ) {
			return 'rtl' === $languages[ $lang ]['dir'];
		}
		return in_array( $lang, array( 'fa', 'ar', 'ur', 'he' ), true );
	}

	/**
	 * دریافت لینک تغییر زبان برای صفحه جاری
	 *
	 * @param string $lang_code کد زبان مقصد.
	 * @return string
	 */
	public static function get_language_url( $lang_code ) {
		return esc_url( add_query_arg( 'lang', sanitize_key( $lang_code ) ) );
	}

	/**
	 * دیکشنری جامع عبارات قالب در سه زبان اصلی (فارسی، انگلیسی، چینی)
	 *
	 * @return array
	 */
	public static function get_dictionary() {
		return array(
			'fa' => array(
				'holding_name'           => 'گروه هلدینگ آبیدر',
				'holding_short'          => 'گروه آبیدر',
				'holding_tagline'        => 'معادن • انرژی • نفت و پتروشیمی',
				'holding_badge'          => 'پیشگام در صنایع استراتژیک فراملی',
				'nav_home'               => 'صفحه اصلی',
				'nav_about'              => 'درباره ما',
				'nav_services'           => 'خدمات هلدینگ',
				'nav_projects'           => 'پروژه‌ها',
				'nav_team'               => 'تیم ما',
				'nav_certificates'       => 'مدارک و اسناد',
				'nav_blog'               => 'مقالات و اخبار',
				'nav_contact'            => 'تماس با ما',
				'about_heading' => 'درباره گروه هلدینگ آبیدر',
				'about_eyebrow' => 'درباره گروه آبیدر | ABOUT ABIDAR GROUP',
				'about_lead' => 'گروه هلدینگ آبیدر با بیش از یک و نیم دهه تجربه، سبد یکپارچه‌ای از شرکت‌های تخصصی در حوزه معادن، تولید انرژی و صنایع نفت، گاز و پتروشیمی را راهبری می‌کند.',
				'about_body' => 'رسالت ما ایجاد ارزش پایدار برای سهامداران، جوامع محلی و اقتصاد ملی از طریق به‌کارگیری فناوری‌های پیشرفته، رعایت استانداردهای جهانی HSE و توسعه سرمایه انسانی متخصص است. تمامی پروژه‌های هلدینگ بر اساس اصول ESG و کاهش اثرات زیست‌محیطی طراحی و اجرا می‌شوند.',
				'about_check_1' => 'زنجیره ارزش یکپارچه از معدن تا بازارهای جهانی',
				'about_check_2' => 'انطباق کامل با استانداردهای ISO 9001 و 14001 و 45001',
				'about_check_3' => 'حکمرانی شرکتی شفاف و گزارش‌دهی مالی بین‌المللی',
				'about_cta' => 'گزارش عملکرد و ارتباط با ما',
				'timeline_title' => 'مسیر رشد و تاریخچه هلدینگ آبیدر',
				'contact_eyebrow' => 'ارتباط مستقیم با دبیرخانه و معاونت‌های هلدینگ',
				'contact_heading' => 'تماس با گروه هلدینگ آبیدر',
				'sector_all'             => 'همه حوزه‌ها',
				'sector_mining'          => 'معادن و صنایع معدنی',
				'sector_energy'          => 'انرژی و نیروگاه‌ها',
				'sector_oil'             => 'نفت، گاز و پتروشیمی',
				'hero_title'             => 'آینده‌پژوهی و مهندسی پیشرو در معادن، انرژی و نفت',
				'hero_subtitle'          => 'هلدینگ گروه آبیدر با تکیه بر دانش فنی روز، ناوگان صنعتی پیشرفته و سرمایه‌گذاری پایدار، زنجیره ارزش اکتشاف، تولید انرژی پاک و پالایش نفت و گاز را در تراز جهانی هدایت می‌کند.',
				'cta_explore_projects'   => 'مشاهده پروژه‌های استراتژیک',
				'cta_our_services'       => 'خدمات تخصصی هلدینگ',
				'cta_contact_us'         => 'ارتباط با معاونت‌ها',
				'cta_download_catalog'   => 'دریافت کاتالوگ جامع (PDF)',
				'stats_projects'         => 'پروژه ملی و فراملی',
				'stats_capacity'         => 'مگاوات ظرفیت نیروگاهی',
				'stats_mineral'          => 'میلیون تن استخراج سالانه',
				'stats_specialists'      => 'متخصص و مهندس ارشد',
				'section_services_sub'   => 'زنجیره یکپارچه خدمات صنعتی',
				'section_services_title' => 'حوزه‌های تخصصی فعالیت گروه آبیدر',
				'section_projects_sub'   => 'کارنامه اجرایی و طرح‌های کلان',
				'section_projects_title' => 'پروژه‌های شاخص معادن، انرژی و نفت',
				'section_team_sub'       => 'سرمایه‌های انسانی و رهبری',
				'section_team_title'     => 'هیئت مدیره و مدیران ارشد گروه آبیدر',
				'section_certs_sub'      => 'تضمین کیفیت، ایمنی (HSE) و استانداردهای جهانی',
				'section_certs_title'    => 'مدارک، گواهینامه‌های ISO و لوح‌های افتخار',
				'section_blog_sub'       => 'تحلیل بازارهای جهانی و گزارش‌های فنی',
				'section_blog_title'     => 'آخرین مقالات و اخبار هلدینگ آبیدر',
				'read_more'              => 'مطالعه کامل',
				'view_details'           => 'مشاهده جزئیات فنی',
				'reading_time'           => 'دقیقه زمان مطالعه',
				'toc_title'              => 'فهرست سرفصل‌های مقاله',
				'share_article'          => 'اشتراک‌گذاری این گزارش:',
				'related_articles'       => 'مقالات و گزارش‌های مرتبط',
				'project_client'         => 'کارفرما / سرمایه‌گذار:',
				'project_location'       => 'موقعیت جغرافیایی:',
				'project_capacity'       => 'ظرفیت / مقیاس:',
				'project_progress'       => 'پیشرفت فیزیکی:',
				'project_year'           => 'سال بهره‌برداری:',
				'cert_issuer'            => 'مرجع صادرکننده:',
				'cert_code'              => 'شماره گواهینامه:',
				'cert_verify'            => 'استعلام اصالت مدرک',
				'contact_form_title'     => 'ارسال درخواست رسمی و استعلام همکاری',
				'contact_name'           => 'نام و نام خانوادگی / نام شرکت',
				'contact_email'          => 'پست الکترونیکی رسمی',
				'contact_phone'          => 'شماره تماس مستقیم',
				'contact_department'     => 'انتخاب معاونت مربوطه',
				'contact_subject'        => 'موضوع درخواست',
				'contact_message'        => 'شرح کامل درخواست یا پیشنهاد فنی/بازرگانی',
				'contact_submit'         => 'ثبت و ارسال امن به دبیرخانه هلدینگ',
				'security_badge'         => 'محافظت‌شده با سپر امنیتی پیشرفته گروه آبیدر (ضد اسپم و رمزنگاری‌شده)',
				'footer_about'           => 'گروه هلدینگ آبیدر، پیشرو در توسعه پایدار معادن، احداث نیروگاه‌های سیکل ترکیبی و تجدیدپذیر و اجرای پروژه‌های کلان نفت، گاز و پتروشیمی در سطح منطقه‌ای و بین‌المللی.',
				'footer_rights'          => 'تمامی حقوق مادی و معنوی برای «گروه هلدینگ آبیدر» محفوظ است. طراحی شده با قالب اختصاصی نسخه ۲.۰',
			),
			'en' => array(
				'holding_name'           => 'Abidar Group Holding',
				'holding_short'          => 'Abidar Group',
				'holding_tagline'        => 'Mining • Energy • Oil & Petrochemicals',
				'holding_badge'          => 'Pioneering Strategic Global Industries',
				'nav_home'               => 'Home',
				'nav_about'              => 'About Us',
				'nav_services'           => 'Services',
				'nav_projects'           => 'Projects',
				'nav_team'               => 'Our Team',
				'nav_certificates'       => 'Certificates & Docs',
				'nav_blog'               => 'News & Insights',
				'nav_contact'            => 'Contact Us',
				'about_heading' => 'About Abidar Group Holding',
				'about_eyebrow' => 'ABOUT ABIDAR GROUP | Integrated industrial holding',
				'about_lead' => 'Abidar Group Holding draws on more than fifteen years of experience to steer an integrated portfolio of specialised companies across mining, power generation, and the oil, gas and petrochemical industries.',
				'about_body' => 'Our mission is to create lasting value for shareholders, local communities and the national economy by deploying advanced technology, observing world-class HSE standards and developing specialised human capital. Every holding project is designed and delivered on ESG principles and reduced environmental impact.',
				'about_check_1' => 'An integrated value chain from mine to global markets',
				'about_check_2' => 'Full compliance with ISO 9001, 14001 and 45001 standards',
				'about_check_3' => 'Transparent corporate governance and international financial reporting',
				'about_cta' => 'Performance report & contact us',
				'timeline_title' => 'Abidar Group growth path and history',
				'contact_eyebrow' => 'Direct contact with the holding secretariat and divisions',
				'contact_heading' => 'Contact Abidar Holding Group',
				'sector_all'             => 'All Sectors',
				'sector_mining'          => 'Mining & Minerals',
				'sector_energy'          => 'Power & Clean Energy',
				'sector_oil'             => 'Oil, Gas & Petrochemical',
				'hero_title'             => 'Engineering Tomorrow across Mining, Energy & Oil',
				'hero_subtitle'          => 'Abidar Group Holding integrates cutting-edge engineering, heavy industrial fleet operations, and sustainable investment across mineral extraction, utility-scale power generation, and oil refining.',
				'cta_explore_projects'   => 'Explore Strategic Projects',
				'cta_our_services'       => 'Industrial Services',
				'cta_contact_us'         => 'Contact Divisions',
				'cta_download_catalog'   => 'Download Corporate Profile (PDF)',
				'stats_projects'         => 'Mega Industrial Projects',
				'stats_capacity'         => 'MW Power Capacity',
				'stats_mineral'          => 'Million Tons Annual Output',
				'stats_specialists'      => 'Senior Engineers & Experts',
				'section_services_sub'   => 'INTEGRATED INDUSTRIAL VALUE CHAIN',
				'section_services_title' => 'Core Business Divisions & Capabilities',
				'section_projects_sub'   => 'PROVEN TRACK RECORD & MEGA PROJECTS',
				'section_projects_title' => 'Flagship Mining, Energy & Oil Projects',
				'section_team_sub'       => 'EXECUTIVE LEADERSHIP & GOVERNANCE',
				'section_team_title'     => 'Board of Directors & Senior Executives',
				'section_certs_sub'      => 'GLOBAL QUALITY, HSE & ISO COMPLIANCE',
				'section_certs_title'    => 'Certificates, Accreditations & Awards',
				'section_blog_sub'       => 'MARKET INTELLIGENCE & TECHNICAL REPORTS',
				'section_blog_title'     => 'Latest Insights & Holding News',
				'read_more'              => 'Read Full Article',
				'view_details'           => 'Technical Specifications',
				'reading_time'           => 'min read',
				'toc_title'              => 'Table of Contents',
				'share_article'          => 'Share this report:',
				'related_articles'       => 'Related Technical Articles',
				'project_client'         => 'Client / Investor:',
				'project_location'       => 'Location:',
				'project_capacity'       => 'Capacity / Scale:',
				'project_progress'       => 'Completion Progress:',
				'project_year'           => 'Commissioning Year:',
				'cert_issuer'            => 'Issuing Authority:',
				'cert_code'              => 'Certificate ID:',
				'cert_verify'            => 'Verify Authenticity',
				'contact_form_title'     => 'Official Inquiry & Partnership Request',
				'contact_name'           => 'Full Name / Company Name',
				'contact_email'          => 'Corporate Email Address',
				'contact_phone'          => 'Direct Phone Number',
				'contact_department'     => 'Select Target Division',
				'contact_subject'        => 'Inquiry Subject',
				'contact_message'        => 'Detailed Technical or Commercial Proposal',
				'contact_submit'         => 'Submit Secure Inquiry',
				'security_badge'         => 'Protected by Abidar Enterprise Security Shield (Anti-Spam & Encrypted)',
				'footer_about'           => 'Abidar Group Holding is a premier international conglomerate driving sustainable mineral processing, combined-cycle & renewable power plants, and upstream/downstream oil & gas EPC projects.',
				'footer_rights'          => 'All Rights Reserved © Abidar Group Holding. Powered by Abidar Enterprise Theme v2.0',
			),
			'zh' => array(
				'holding_name'           => '阿比达尔控股集团',
				'holding_short'          => '阿比达尔集团',
				'holding_tagline'        => '矿业 • 能源 • 石油与石化',
				'holding_badge'          => '跨国战略工业领域的领航者',
				'nav_home'               => '首页',
				'nav_about'              => '关于集团',
				'nav_services'           => '核心业务',
				'nav_projects'           => '重点工程',
				'nav_team'               => '管理团队',
				'nav_certificates'       => '资质与荣誉',
				'nav_blog'               => '新闻与洞察',
				'nav_contact'            => '联系我们',
				'about_heading' => '关于阿比达尔控股集团',
				'about_eyebrow' => '关于阿比达尔集团 | 综合性工业控股',
				'about_lead' => '阿比达尔控股集团拥有超过十五年的运营经验，统筹管理矿业、能源发电以及石油、天然气与石化领域的专业化子公司组合。',
				'about_body' => '我们的使命是通过应用先进技术、遵循国际 HSE 标准并培养专业人才，为股东、当地社区与国民经济创造持久价值。集团所有项目均依据 ESG 原则与降低环境影响的方针进行设计与实施。',
				'about_check_1' => '从矿山到全球市场的一体化价值链',
				'about_check_2' => '全面符合 ISO 9001、14001 与 45001 标准',
				'about_check_3' => '透明的公司治理与国际财务报告',
				'about_cta' => '业绩报告与联系我们',
				'timeline_title' => '阿比达尔集团发展历程',
				'contact_eyebrow' => '直接联系集团秘书处与各事业部',
				'contact_heading' => '联系阿比达尔控股集团',
				'sector_all'             => '全部领域',
				'sector_mining'          => '矿业与矿物加工',
				'sector_energy'          => '电力与清洁能源',
				'sector_oil'             => '石油、天然气与石化',
				'hero_title'             => '引领矿业、清洁能源与石油工程的未来',
				'hero_subtitle'          => '阿比达尔控股集团凭借顶尖工程技术、现代化重工业装备与可持续投资，打造从矿产勘探开采、清洁能源发电到石油炼化的全球一体化价值链。',
				'cta_explore_projects'   => '浏览重点工程项目',
				'cta_our_services'       => '探索核心业务',
				'cta_contact_us'         => '联系各事业部',
				'cta_download_catalog'   => '下载集团宣传册 (PDF)',
				'stats_projects'         => '国家级与跨国工程',
				'stats_capacity'         => '兆瓦 (MW) 发电装机容量',
				'stats_mineral'          => '百万吨年矿产开采量',
				'stats_specialists'      => '资深工程师与技术专家',
				'section_services_sub'   => '一体化工业价值链',
				'section_services_title' => '阿比达尔集团核心业务板块',
				'section_projects_sub'   => '卓越工程业绩与大型项目',
				'section_projects_title' => '矿业、能源与石油标杆工程',
				'section_team_sub'       => '卓越领导力与公司治理',
				'section_team_title'     => '集团董事会与高级管理团队',
				'section_certs_sub'      => '全球质量、HSE安全与ISO国际认证',
				'section_certs_title'    => '资质证书、国际标准与荣誉奖牌',
				'section_blog_sub'       => '全球市场洞察与技术报告',
				'section_blog_title'     => '集团最新动态与行业深度分析',
				'read_more'              => '阅读全文',
				'view_details'           => '查看技术参数',
				'reading_time'           => '分钟阅读',
				'toc_title'              => '文章目录导航',
				'share_article'          => '分享此技术报告：',
				'related_articles'       => '相关行业报告与文章',
				'project_client'         => '业主 / 投资方：',
				'project_location'       => '项目地点：',
				'project_capacity'       => '产能 / 规模：',
				'project_progress'       => '工程进度：',
				'project_year'           => '投产年份：',
				'cert_issuer'            => '颁发机构：',
				'cert_code'              => '证书编号：',
				'cert_verify'            => '验证证书真伪',
				'contact_form_title'     => '官方合作咨询与项目对接',
				'contact_name'           => '您的姓名 / 企业名称',
				'contact_email'          => '企业电子邮箱',
				'contact_phone'          => '联系电话',
				'contact_department'     => '选择对接事业部',
				'contact_subject'        => '咨询主题',
				'contact_message'        => '请详细描述您的技术或商务合作需求',
				'contact_submit'         => '安全提交合作申请',
				'security_badge'         => '由阿比达尔企业级安全盾保护（防垃圾信息与加密传输）',
				'footer_about'           => '阿比达尔控股集团是国际领先的综合性工业集团，专注于绿色矿山开发、联合循环与可再生能源电站建设以及大型石油天然气EPC工程。',
				'footer_rights'          => '版权所有 © 阿比达尔控股集团 (Abidar Group Holding) | 专属定制主题 v2.0',
			),
		);
	}

	/**
	 * ترجمه کلید مورد نظر بر اساس زبان فعلی
	 *
	 * @param string $key     کلید عبارت.
	 * @param string $default مقدار پیش‌فرض.
	 * @return string
	 */
	public static function translate( $key, $default = '' ) {
		$lang       = self::get_current_lang();
		$dictionary = self::get_dictionary();
		$languages  = self::get_languages();

		/**
		 * فیلتر ترجمه‌های زبان‌های سفارشی.
		 *
		 * @param array  $translations آرایه کلید => ترجمه برای زبان جاری.
		 * @param string $lang         کد زبان جاری.
		 */
		$custom = isset( $languages[ $lang ]['translations'] ) && is_array( $languages[ $lang ]['translations'] )
			? $languages[ $lang ]['translations']
			: array();

		$custom = apply_filters( 'abidar_language_translations', $custom, $lang );

		// ۱. بررسی زبان‌های سفارشی اضافه‌شده در تنظیمات پیشخوان یا فیلتر بالا
		if ( isset( $custom[ $key ] ) && '' !== trim( (string) $custom[ $key ] ) ) {
			return $custom[ $key ];
		}

		// ۲. بررسی دیکشنری سه زبان پیش‌فرض (فارسی، انگلیسی، چینی)
		if ( isset( $dictionary[ $lang ][ $key ] ) ) {
			return $dictionary[ $lang ][ $key ];
		}

		// ۳. بازگشت به انگلیسی، سپس فارسی و در نهایت متن پیش‌فرض
		if ( isset( $dictionary['en'][ $key ] ) ) {
			return $dictionary['en'][ $key ];
		}
		if ( isset( $dictionary['fa'][ $key ] ) ) {
			return $dictionary['fa'][ $key ];
		}

		// ۴. متن پیش‌فرض ارسال‌شده از سمت فراخوان (تنها آخرین راه‌حل)
		if ( '' !== $default ) {
			return $default;
		}

		return $key;
	}
}

Abidar_I18n::init();

/**
 * تابع کمکی سراسری برای ترجمه سریع عبارات در قالب و ویجت‌های المنتور
 *
 * @param string $key     کلید ترجمه.
 * @param string $default متن پیش‌فرض.
 * @return string
 */
function abidar_t( $key, $default = '' ) {
	return Abidar_I18n::translate( $key, $default );
}

/**
 * دریافت مقدار چندزبانه از یک آرایه یا متافیلد (مثلاً title_fa, title_en, title_zh)
 *
 * @param array  $data         آرایه داده‌ها.
 * @param string $field_prefix پیشوند فیلد (مثلاً title یا desc).
 * @param string $fallback     مقدار جایگزین.
 * @return string
 */
function abidar_multilang_val( $data, $field_prefix, $fallback = '' ) {
	if ( ! is_array( $data ) ) {
		return $fallback;
	}

	$lang = Abidar_I18n::get_current_lang();
	$key  = $field_prefix . '_' . $lang;

	// ۱. مقدار زبان جاری (مثلاً title_en یا title_zh).
	if ( isset( $data[ $key ] ) && '' !== trim( (string) $data[ $key ] ) ) {
		return $data[ $key ];
	}

	// ۲. کلید بدون پسوند؛ در داده‌های پیش‌فرض قالب، این کلیدها به زبان پیش‌فرض (فارسی) هستند.
	if ( 'fa' === $lang && isset( $data[ $field_prefix ] ) && '' !== trim( (string) $data[ $field_prefix ] ) ) {
		return $data[ $field_prefix ];
	}

	// ۳. بازگشت به انگلیسی، سپس فارسی و در نهایت کلید بدون پسوند.
	if ( isset( $data[ $field_prefix . '_en' ] ) && '' !== trim( (string) $data[ $field_prefix . '_en' ] ) ) {
		return $data[ $field_prefix . '_en' ];
	}
	if ( isset( $data[ $field_prefix . '_fa' ] ) && '' !== trim( (string) $data[ $field_prefix . '_fa' ] ) ) {
		return $data[ $field_prefix . '_fa' ];
	}
	if ( isset( $data[ $field_prefix ] ) && '' !== trim( (string) $data[ $field_prefix ] ) ) {
		return $data[ $field_prefix ];
	}

	return $fallback;
}
