<?php
/**
 * Abidar Group Custom Post Types, Taxonomies & Meta Boxes (v2.0.0)
 *
 * ثبت پست‌تایپ‌های اختصاصی: خدمات، پروژه‌ها، تیم‌ها، مدارک و لوح‌ها، و هدر/فوتر ساز المنتور رایگان
 *
 * @package Abidar_Group
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abidar_CPT
 */
class Abidar_CPT {

	/**
	 * راه‌اندازی قلاب‌های ثبت پست‌تایپ‌ها و متاباکس‌ها
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 10 );
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 11 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ), 10, 2 );
	}

	/**
	 * ثبت پست‌تایپ‌های اختصاصی قالب گروه آبیدر
	 */
	public static function register_post_types() {

		// ۱. پست‌تایپ خدمات (Services)
		register_post_type(
			'abidar_service',
			array(
				'labels'              => array(
					'name'               => esc_html__( 'خدمات هلدینگ', 'abidar-group' ),
					'singular_name'      => esc_html__( 'خدمت', 'abidar-group' ),
					'add_new'            => esc_html__( 'درج خدمت جدید', 'abidar-group' ),
					'add_new_item'       => esc_html__( 'افزودن خدمت تخصصی جدید', 'abidar-group' ),
					'edit_item'          => esc_html__( 'ویرایش خدمت', 'abidar-group' ),
					'all_items'          => esc_html__( 'همه خدمات', 'abidar-group' ),
					'search_items'       => esc_html__( 'جستجوی خدمات', 'abidar-group' ),
				),
				'public'              => true,
				'has_archive'         => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-hammer',
				'menu_position'       => 21,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ),
				'rewrite'             => array( 'slug' => 'services-item' ),
			)
		);

		// ۲. پست‌تایپ پروژه‌ها (Projects)
		register_post_type(
			'abidar_project',
			array(
				'labels'              => array(
					'name'               => esc_html__( 'پروژه‌های هلدینگ', 'abidar-group' ),
					'singular_name'      => esc_html__( 'پروژه', 'abidar-group' ),
					'add_new'            => esc_html__( 'درج پروژه جدید', 'abidar-group' ),
					'add_new_item'       => esc_html__( 'افزودن پروژه جدید', 'abidar-group' ),
					'edit_item'          => esc_html__( 'ویرایش پروژه', 'abidar-group' ),
					'all_items'          => esc_html__( 'همه پروژه‌ها', 'abidar-group' ),
					'search_items'       => esc_html__( 'جستجوی پروژه', 'abidar-group' ),
				),
				'public'              => true,
				'has_archive'         => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-building',
				'menu_position'       => 22,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ),
				'rewrite'             => array( 'slug' => 'project-item' ),
			)
		);

		// ۳. پست‌تایپ تیم‌ها و مدیران (Team Members)
		register_post_type(
			'abidar_team',
			array(
				'labels'              => array(
					'name'               => esc_html__( 'تیم‌ها و مدیران', 'abidar-group' ),
					'singular_name'      => esc_html__( 'عضو تیم', 'abidar-group' ),
					'add_new'            => esc_html__( 'افزودن عضو جدید', 'abidar-group' ),
					'add_new_item'       => esc_html__( 'افزودن مدیر / متخصص جدید', 'abidar-group' ),
					'edit_item'          => esc_html__( 'ویرایش مشخصات عضو تیم', 'abidar-group' ),
					'all_items'          => esc_html__( 'همه اعضای تیم', 'abidar-group' ),
				),
				'public'              => true,
				'has_archive'         => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-groups',
				'menu_position'       => 23,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ),
				'rewrite'             => array( 'slug' => 'team-member' ),
			)
		);

		// ۴. پست‌تایپ مدارک و لوح‌ها (Certificates & Awards)
		register_post_type(
			'abidar_certificate',
			array(
				'labels'              => array(
					'name'               => esc_html__( 'مدارک و لوح‌ها', 'abidar-group' ),
					'singular_name'      => esc_html__( 'مدرک / لوح', 'abidar-group' ),
					'add_new'            => esc_html__( 'درج مدرک جدید', 'abidar-group' ),
					'add_new_item'       => esc_html__( 'افزودن گواهینامه یا لوح افتخار', 'abidar-group' ),
					'edit_item'          => esc_html__( 'ویرایش مدرک', 'abidar-group' ),
					'all_items'          => esc_html__( 'همه مدارک و لوح‌ها', 'abidar-group' ),
				),
				'public'              => true,
				'has_archive'         => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-awards',
				'menu_position'       => 24,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ),
				'rewrite'             => array( 'slug' => 'certificate-item' ),
			)
		);

		// ۵. پست‌تایپ هدر و فوتر ساز اختصاصی با المنتور رایگان (Header & Footer Builder)
		register_post_type(
			'abidar_hf',
			array(
				'labels'              => array(
					'name'               => esc_html__( 'هدر و فوتر ساز (المنتور)', 'abidar-group' ),
					'singular_name'      => esc_html__( 'قالب هدر / فوتر', 'abidar-group' ),
					'add_new'            => esc_html__( 'ساخت هدر یا فوتر جدید', 'abidar-group' ),
					'add_new_item'       => esc_html__( 'طراحی هدر / فوتر با المنتور رایگان', 'abidar-group' ),
					'edit_item'          => esc_html__( 'ویرایش قالب هدر / فوتر', 'abidar-group' ),
					'all_items'          => esc_html__( 'همه هدرها و فوترها', 'abidar-group' ),
				),
				'public'              => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-layout',
				'menu_position'       => 25,
				'supports'            => array( 'title', 'editor', 'elementor' ),
			)
		);
	}

	/**
	 * ثبت طبقه‌بندی‌های (Taxonomies) اختصاصی برای خدمات، پروژه‌ها، تیم و مدارک
	 */
	public static function register_taxonomies() {

		// دسته‌بندی حوزه خدمات (معادن، انرژی، نفت)
		register_taxonomy(
			'service_sector',
			array( 'abidar_service' ),
			array(
				'label'             => esc_html__( 'حوزه فعالیت خدمت', 'abidar-group' ),
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'service-sector' ),
			)
		);

		// دسته‌بندی حوزه پروژه‌ها
		register_taxonomy(
			'project_sector',
			array( 'abidar_project' ),
			array(
				'label'             => esc_html__( 'حوزه پروژه (معادن/انرژی/نفت)', 'abidar-group' ),
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'project-sector' ),
			)
		);

		// دپارتمان اعضای تیم
		register_taxonomy(
			'team_department',
			array( 'abidar_team' ),
			array(
				'label'             => esc_html__( 'معاونت / دپارتمان سازمانی', 'abidar-group' ),
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'department' ),
			)
		);

		// نوع مدرک و گواهینامه
		register_taxonomy(
			'certificate_category',
			array( 'abidar_certificate' ),
			array(
				'label'             => esc_html__( 'نوع گواهینامه / استاندارد', 'abidar-group' ),
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'certificate-type' ),
			)
		);
	}

	/**
	 * ثبت متاباکس‌ها.
	 *
	 * پست‌تایپ‌های خدمات، پروژه‌ها، تیم و مدارک متاباکس متنی ندارند؛
	 * ویرایش این چهار نوع محتوا کاملاً توسط پنل حرفه‌ای Abidar_CPT_Editor
	 * (تب‌بندی، رسانه، ریپیتر و پیش‌نمایش زنده) انجام می‌شود.
	 * بنابراین در این متد فقط متاباکس قالب‌های هدر/فوتر ثبت می‌شود.
	 */
	public static function register_meta_boxes() {
		add_meta_box(
			'abidar_hf_meta',
			esc_html__( 'تنظیمات جایگاه قالب هدر / فوتر المنتور رایگان', 'abidar-group' ),
			array( __CLASS__, 'render_hf_meta_box' ),
			'abidar_hf',
			'side',
			'high'
		);
	}





	/**
	 * رندر متاباکس هدر و فوتر ساز المنتور رایگان
	 *
	 * @param WP_Post $post شیء پست.
	 */
	public static function render_hf_meta_box( $post ) {
		wp_nonce_field( 'abidar_save_cpt_meta', 'abidar_cpt_meta_nonce' );
		$hf_type   = get_post_meta( $post->ID, '_abidar_hf_type', true );
		$is_active = get_post_meta( $post->ID, '_abidar_hf_active', true );
		?>
		<p>
			<label><strong><?php esc_html_e( 'نوع قالب المنتور:', 'abidar-group' ); ?></strong></label><br>
			<select name="_abidar_hf_type" style="width:100%;margin-top:6px;">
				<option value="header" <?php selected( $hf_type, 'header' ); ?>><?php esc_html_e( 'هدر سایت (Site Header)', 'abidar-group' ); ?></option>
				<option value="footer" <?php selected( $hf_type, 'footer' ); ?>><?php esc_html_e( 'فوتر سایت (Site Footer)', 'abidar-group' ); ?></option>
			</select>
		</p>
		<p>
			<label>
				<input type="checkbox" name="_abidar_hf_active" value="1" <?php checked( $is_active, '1' ); ?> />
				<strong><?php esc_html_e( 'تنظیم به عنوان هدر/فوتر پیش‌فرض کل سایت', 'abidar-group' ); ?></strong>
			</label>
		</p>
		<p class="description">
			<?php esc_html_e( 'این قالب را با «المنتور رایگان» ویرایش کرده و از ویجت‌های اختصاصی هدر و فوتر گروه آبیدر در آن استفاده نمایید.', 'abidar-group' ); ?>
		</p>
		<?php
	}

	/**
	 * ذخیره‌سازی امن متادیتای پست‌تایپ‌ها
	 *
	 * @param int     $post_id شناسه پست.
	 * @param WP_Post $post    شیء پست.
	 */
	public static function save_meta_boxes( $post_id, $post ) {
		if ( ! isset( $_POST['abidar_cpt_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abidar_cpt_meta_nonce'] ) ), 'abidar_save_cpt_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// این متد تنها متای قالب‌های هدر/فوتر (abidar_hf) را ذخیره می‌کند.
		// خدمات، پروژه‌ها، تیم و مدارک توسط Abidar_CPT_Editor::save() ذخیره می‌شوند.
		if ( 'abidar_hf' !== $post->post_type ) {
			return;
		}

		if ( isset( $_POST['_abidar_hf_type'] ) ) {
			update_post_meta( $post_id, '_abidar_hf_type', sanitize_key( wp_unslash( $_POST['_abidar_hf_type'] ) ) );
		}

		$active = isset( $_POST['_abidar_hf_active'] ) ? '1' : '0';
		update_post_meta( $post_id, '_abidar_hf_active', $active );
	}

	/**
	 * داده‌های غنی و استاندارد دمو برای خدمات، پروژه‌ها، تیم و مدارک گروه آبیدر
	 * (مورد استفاده در ساخت خودکار دمو و نمایش پیش‌فرض ویجت‌های المنتور)
	 *
	 * @param string $type نوع داده (services | projects | team | certificates | posts).
	 * @return array
	 */
	public static function get_default_dataset( $type ) {
		/**
		 * فیلتر مجموعه داده پیش‌فرض پست‌تایپ‌ها.
		 *
		 * @param array  $dataset آرایه داده‌های پیش‌فرض.
		 * @param string $type    نوع داده (services|projects|team|certificates|posts).
		 */
		return apply_filters( 'abidar_cpt_dataset', self::build_default_dataset( $type ), $type );
	}

	/**
	 * ساخت خام مجموعه داده پیش‌فرض (بدون فیلتر).
	 *
	 * @param string $type نوع داده.
	 * @return array
	 */
	private static function build_default_dataset( $type ) {
		$theme_uri = defined( 'ABIDAR_URI' ) ? ABIDAR_URI : '';

		if ( 'services' === $type ) {
			return array(
				array(
					'id'        => 'srv-1',
					'sector'    => 'mining',
					'icon'      => 'pickaxe',
					'metric'    => '۱۸.۵ میلیون تن ظرفیت استخراج سالانه',
					'metric_en' => '18.5M Tons Annual Extraction Capacity',
					'metric_zh' => '年开采能力 1850 万吨',
					'title_fa'  => 'اکتشاف، استخراج و فرآوری پیشرفته معادن فلزی',
					'title_en'  => 'Advanced Mineral Exploration, Mining & Processing',
					'title_zh'  => '现代化金属矿产勘探、开采与深加工',
					'desc_fa'   => 'اجرای عملیات ژئوفیزیک هوایی، حفاری مغزه‌گیری عمیق، استخراج مکانیزه معادن مس، سنگ‌آهن و طلا و احداث کارخانجات کنسانتره و گندله‌سازی با فناوری‌های دوستدار محیط‌زیست.',
					'desc_en'   => 'Airborne geophysics, deep core drilling, automated open-pit extraction of copper, iron ore, and gold, alongside eco-efficient concentrate and pelletizing plants.',
					'desc_zh'   => '开展航空地球物理勘探、深部岩心钻探、铜铁金矿自动化露天开采，以及绿色环保选矿与球团加工厂建设。',
					'image'     => $theme_uri . '/assets/images/sector-mining.jpg',
					'features'  => array( 'احداث خطوط کنسانتره و گندله‌سازی', 'ناوگان دامپتراک‌های هوشمند ۲۲۰ تنی', 'سیستم لیچینگ و پالایش کاتد مس' ),
				),
				array(
					'id'        => 'srv-2',
					'sector'    => 'energy',
					'icon'      => 'bolt',
					'metric'    => '۴,۲۰۰ مگاوات ظرفیت تولید برق پایدار',
					'metric_en' => '4,200 MW Installed Power Capacity',
					'metric_zh' => '4,200 兆瓦清洁电力装机容量',
					'title_fa'  => 'احداث و بهره‌برداری نیروگاه‌های سیکل ترکیبی و تجدیدپذیر',
					'title_en'  => 'Combined-Cycle & Renewable Power Plant EPC',
					'title_zh'  => '燃气联合循环与可再生能源电站建设运营',
					'desc_fa'   => 'طراحی، تامین تجهیزات و اجرای (EPC) نیروگاه‌های سیکل ترکیبی راندمان بالا (کلاس F و H)، مزارع خورشیدی فتوولتائیک مقیاس مگاواتی و پست‌های انتقال فشار قوی ۴۰۰ کیلوولت.',
					'desc_en'   => 'Full-turnkey EPC and O&M of high-efficiency F & H-class combined-cycle gas power stations, utility-scale solar PV farms, and 400kV high-voltage substations.',
					'desc_zh'   => '提供高效率F/H级燃气联合循环电站、大型光伏风电基地及400kV超高压变电站的EPC总承包与运维服务。',
					'image'     => $theme_uri . '/assets/images/sector-energy.jpg',
					'features'  => array( 'توربین‌های گازی راندمان بالای ۵۸٪', 'مزارع خورشیدی و بادی هیبریدی', 'مدیریت هوشمند دیسپاچینگ و شبکه برق صنعتی' ),
				),
				array(
					'id'        => 'srv-3',
					'sector'    => 'oil',
					'icon'      => 'flame',
					'metric'    => '۳۲۰,۰۰۰ بشکه ظرفیت پالایش و انتقال روزانه',
					'metric_en' => '320,000 BPD Refining & Pipeline Capacity',
					'metric_zh' => '日均 32 万桶炼化与管输能力',
					'title_fa'  => 'مهندسی بالادستی، پالایشگاهی و پتروشیمی نفت و گاز',
					'title_en'  => 'Upstream Oil & Gas, Refining & Petrochemical EPC',
					'title_zh'  => '油气上游开发、炼油化工EPC与国际能源贸易',
					'desc_fa'   => 'توسعه میادین نفتی و گازی، ساخت واحدهای پالایشگاهی و پتروشیمی، خطوط لوله انتقال سراسری، مخازن ذخیره‌سازی استراتژیک و صادرات فرآورده‌های نفتی و پلیمری.',
					'desc_en'   => 'Oilfield development, petrochemical process units, cross-country transmission pipelines, strategic storage terminals, and global export of refined petroleum derivatives.',
					'desc_zh'   => '致力于油气田开发、炼化装置建设、长输管线工程、战略储油库及石化衍生品的跨国贸易与供应链管理。',
					'image'     => $theme_uri . '/assets/images/sector-oil.jpg',
					'features'  => array( 'واحدهای شیرین‌سازی گاز و گوگردزدایی', 'مخازن کروی و استراتژیک پایانه صادراتی', 'تامین تجهیزات سرچاهی و کمپرسورهای فشار قوی' ),
				),
				array(
					'id'        => 'srv-4',
					'sector'    => 'mining',
					'icon'      => 'globe',
					'metric'    => 'صادرات به ۱۸ کشور آسیایی و اروپایی',
					'metric_en' => 'Exporting to 18 Asian & European Markets',
					'metric_zh' => '出口覆盖亚欧 18 个国家和地区',
					'title_fa'  => 'لجستیک ریلی، بندری و بازرگانی بین‌المللی مواد معدنی و انرژی',
					'title_en'  => 'Global Commodity Trading, Rail & Maritime Logistics',
					'title_zh'  => '大宗矿产能源国际贸易与海铁联运物流',
					'desc_fa'   => 'مدیریت زنجیره تامین بین‌المللی از معدن و پالایشگاه تا اسکله‌های صادراتی اختصاصی، حمل‌ونقل ریلی و دریایی فله‌بر و نفتکش با دفاتر فعال در تهران، دبی و شانگهای.',
					'desc_en'   => 'End-to-end supply chain infrastructure from mine and refinery to dedicated port terminals, bulk carrier fleet, and trading desks in Tehran, Dubai, and Shanghai.',
					'desc_zh'   => '打通从矿山、炼厂到专用港口码头的全产业链物流，依托德黑兰、迪拜与上海办事处提供全球大宗商品交付。',
					'image'     => $theme_uri . '/assets/images/sector-mining.jpg',
					'features'  => array( 'ترمینال اختصاصی بارگیری مکانیزه بندری', 'دفاتر بازرگانی فعال در شانگهای و دبی', 'قراردادهای بلندمدت تامین خوراک صنایع فولاد' ),
				),
				array(
					'id'        => 'srv-5',
					'sector'    => 'energy',
					'icon'      => 'shield',
					'metric'    => 'کاهش ۱.۸ میلیون تن آلایندگی کربن سالانه',
					'metric_en' => '1.8M Tons Annual CO2 Offset',
					'metric_zh' => '年减排二氧化碳 180 万吨',
					'title_fa'  => 'ذخیره‌سازهای انرژی، هیدروژن سبز و بهینه‌سازی مصرف صنایع',
					'title_en'  => 'Green Hydrogen, Grid Storage & Industrial Decarbonization',
					'title_zh'  => '绿氢制备、电网级储能与工业节能降碳技术',
					'desc_fa'   => 'پیاده‌سازی سامانه‌های بازیافت حرارت اتلافی (WHR) در صنایع معدنی و فولادی، تولید هیدروژن سبز برای پالایشگاه‌ها و سیستم‌های ذخیره‌ساز باتری مقیاس شبکه (BESS).',
					'desc_en'   => 'Deploying Waste Heat Recovery (WHR) in heavy mining industries, green hydrogen production for refineries, and utility-scale Battery Energy Storage Systems (BESS).',
					'desc_zh'   => '在矿山冶金行业推广余热回收发电（WHR），开发炼化用绿氢技术及电网级电池储能系统（BESS）。',
					'image'     => $theme_uri . '/assets/images/sector-energy.jpg',
					'features'  => array( 'بازیافت حرارت کوره‌ها و تصفیه پساب صنعتی', 'الکترولایزرهای تولید هیدروژن پاک', 'انطباق کامل با استانداردهای ESG جهانی' ),
				),
				array(
					'id'        => 'srv-6',
					'sector'    => 'oil',
					'icon'      => 'layers',
					'metric'    => '۱.۲ میلیون تن ظرفیت محصولات پتروشیمی',
					'metric_en' => '1.2M Tons Annual Petrochemical Output',
					'metric_zh' => '年产 120 万吨高端石化产品',
					'title_fa'  => 'سرمایه‌گذاری و توسعه مجتمع‌های پتروشیمی و پلیمری پیشرفته',
					'title_en'  => 'Downstream Petrochemical & Polymer Complex Development',
					'title_zh'  => '高端石化与新材料园区投资开发',
					'desc_fa'   => 'احداث و مدیریت مجتمع‌های تولید متانول، الفین، پلی‌اتیلن‌های مهندسی و کاتالیست‌های تخصصی مورد نیاز صنایع نفت، گاز و پالایشگاهی.',
					'desc_en'   => 'Financing, engineering, and operating methanol, olefin, and specialty engineering polymer complexes alongside domestic catalyst manufacturing.',
					'desc_zh'   => '投资建设甲醇、烯烃及特种工程塑料生产基地，研发生产炼油化工核心催化剂。',
					'image'     => $theme_uri . '/assets/images/sector-oil.jpg',
					'features'  => array( 'تولید گریدهای خاص پلیمری صادراتی', 'دانش فنی بومی‌سازی کاتالیست‌ها', 'زیرساخت یکپارچه یوتیلیتی و آفسایت' ),
				),
			);
		}

		if ( 'projects' === $type ) {
			return array(
				array(
					'id'        => 'prj-1',
					'sector'    => 'mining',
					'sector_label_fa' => 'معادن و صنایع معدنی',
					'sector_label_en' => 'Mining & Minerals',
					'sector_label_zh' => '矿业工程',
					'title_fa'  => 'مجتمع عظیم کنسانتره و گندله‌سازی مس و آهن آبیدر - فاز ۳',
					'title_en'  => 'Abidar Copper & Iron Ore Pelletizing Mega-Complex (Phase III)',
					'title_zh'  => '阿比达尔铜铁精矿与球团大型综合体项目（三期）',
					'client_fa' => 'شرکت توسعه معادن و صنایع معدنی آبیدر',
					'client_en' => 'Abidar Mining & Mineral Industries Development Co.',
					'client_zh' => '阿比达尔矿业开发股份有限公司',
					'location_fa' => 'کرمان - کمربند فلزایی سیرجان',
					'location_en' => 'Kerman Province - Sirjan Metallogenic Belt',
					'location_zh' => '克尔曼省 - 锡尔詹成矿带',
					'capacity'  => '۵.۵ میلیون تن در سال (5.5 MTPA)',
					'progress'  => 100,
					'year'      => '1404 / 2025',
					'image'     => $theme_uri . '/assets/images/sector-mining.jpg',
					'desc_fa'   => 'طراحی و احداث کامل خط خردایش اولیه، آسیاب‌های گلوله‌ای، فلوتاسیون ستونی و کارخانه گندله‌سازی با سیستم بازیافت ۹۲٪ آب مصرفی.',
					'desc_en'   => 'Complete EPC of primary crushing, ball mills, column flotation, and pelletizing facility featuring a 92% closed-loop water recycling system.',
					'desc_zh'   => '涵盖初级破碎、球磨机、柱式浮选及球团厂全套EPC建设，实现92%工业水闭路循环利用。',
				),
				array(
					'id'        => 'prj-2',
					'sector'    => 'energy',
					'sector_label_fa' => 'انرژی و نیروگاه‌ها',
					'sector_label_en' => 'Power & Clean Energy',
					'sector_label_zh' => '清洁能源',
					'title_fa'  => 'نیروگاه سیکل ترکیبی کلاس F و مزرعه خورشیدی ۱۱۰۰ مگاواتی زاگرس',
					'title_en'  => 'Zagros 1,100 MW Hybrid Combined-Cycle & Solar Power Station',
					'title_zh'  => '扎格罗斯 1100 兆瓦燃气联合循环与光伏混合电站',
					'client_fa' => 'شرکت تولید برق و انرژی پایدار آبیدر',
					'client_en' => 'Abidar Sustainable Power Generation Corp.',
					'client_zh' => '阿比达尔清洁能源发电集团',
					'location_fa' => 'کردستان و منطقه ویژه انرژی عسلویه',
					'location_en' => 'Kurdistan & Assaluyeh Energy Zone',
					'location_zh' => '库尔德斯坦及阿萨卢耶能源特区',
					'capacity'  => '۱,۱۰۰ مگاوات (1,100 MW)',
					'progress'  => 94,
					'year'      => '1405 / 2026',
					'image'     => $theme_uri . '/assets/images/sector-energy.jpg',
					'desc_fa'   => 'تامین برق پایدار صنایع سنگین و شبکه سراسری از طریق ترکیب دو واحد گازی، یک واحد بخار و مزرعه خورشیدی ۲۰۰ مگاواتی متصل به پست ۴۰۰ کیلوولت.',
					'desc_en'   => 'Supplying reliable power to heavy industry via two gas turbines, one steam unit, and a 200 MW solar PV park integrated with a 400kV GIS substation.',
					'desc_zh'   => '结合两台燃气轮机、一台蒸汽轮机及 200MW 光伏阵列，通过 400kV 变电站为重工业基地提供稳定绿色电力。',
				),
				array(
					'id'        => 'prj-3',
					'sector'    => 'oil',
					'sector_label_fa' => 'نفت، گاز و پتروشیمی',
					'sector_label_en' => 'Oil, Gas & Petrochemical',
					'sector_label_zh' => '石油石化',
					'title_fa'  => 'پالایشگاه میعانات گازی و پایانه صادراتی خلیج فارس (طرح مروارید)',
					'title_en'  => 'Persian Gulf Gas Condensate Refinery & Export Terminal',
					'title_zh'  => '波斯湾凝析油炼化厂与深水出口终端工程',
					'client_fa' => 'هلدینگ نفت و پتروشیمی بین‌الملل آبیدر',
					'client_en' => 'Abidar International Oil & Petrochemical Holding',
					'client_zh' => '阿比达尔国际石油石化控股公司',
					'location_fa' => 'بندرعباس - سواحل مکران',
					'location_en' => 'Bandar Abbas & Makran Coast',
					'location_zh' => '阿巴斯港 - 莫克兰海岸',
					'capacity'  => '۱۲۰,۰۰۰ بشکه در روز (120,000 BPD)',
					'progress'  => 88,
					'year'      => '1405 / 2026',
					'image'     => $theme_uri . '/assets/images/sector-oil.jpg',
					'desc_fa'   => 'احداث واحدهای تقطیر، ایزومریزاسیون، تصفیه هیدروژنی نفتا و ۱۲ مخزن ذخیره‌سازی سقف شناور به همراه گوی شناور بارگیری نفتکش‌های اقیانوس‌پیما (SPM).',
					'desc_en'   => 'Distillation, isomerization, naphtha hydrotreating units, 12 floating-roof storage tanks, and Single Point Mooring (SPM) for VLCC tankers.',
					'desc_zh'   => '建设常减压蒸馏、异构化、石脑油加氢装置，配套12座浮顶储罐及超大型油轮单点系泊（SPM）系统。',
				),
				array(
					'id'        => 'prj-4',
					'sector'    => 'mining',
					'sector_label_fa' => 'معادن و صنایع معدنی',
					'sector_label_en' => 'Mining & Minerals',
					'sector_label_zh' => '矿业工程',
					'title_fa'  => 'طرح هوشمندسازی و ناوگان خودران معدن سنگ‌آهن و طلای کردستان',
					'title_en'  => 'Smart Autonomous Fleet & Gold Processing Plant of Kurdistan',
					'title_zh'  => '库尔德斯坦智能矿山车队与金矿选冶综合工程',
					'client_fa' => 'گروه معادن پیشرو آبیدر',
					'client_en' => 'Abidar Advanced Mining Group',
					'client_zh' => '阿比达尔先进矿业集团',
					'location_fa' => 'سنندج - قروه',
					'location_en' => 'Sanandaj - Qorveh Mining Zone',
					'location_zh' => '萨南达季 - 戈尔韦矿区',
					'capacity'  => '۳.۲ میلیون تن کانسنگ طلا و آهن',
					'progress'  => 100,
					'year'      => '1403 / 2024',
					'image'     => $theme_uri . '/assets/images/sector-mining.jpg',
					'desc_fa'   => 'استقرار شبکه اختصاصی 5G صنعتی، سیستم دیسپاچینگ هوشمند ماشین‌آلات معدنی و واحد استحصال طلا با راندمان ۹۱.۵ درصد.',
					'desc_en'   => 'Deployment of private industrial 5G network, AI fleet dispatching, and high-recovery CIL gold processing plant with 91.5% recovery rate.',
					'desc_zh'   => '部署工业级5G专网、智能矿卡调度系统及回收率达91.5%的炭浸法（CIL）黄金选冶厂。',
				),
				array(
					'id'        => 'prj-5',
					'sector'    => 'oil',
					'sector_label_fa' => 'نفت، گاز و پتروشیمی',
					'sector_label_en' => 'Oil, Gas & Petrochemical',
					'sector_label_zh' => '石油石化',
					'title_fa'  => 'مجتمع پتروشیمی الفین و پلیمرهای مهندسی آبیدر پارس',
					'title_en'  => 'Abidar Pars Olefin & Engineering Polymers Complex',
					'title_zh'  => '阿比达尔帕尔斯烯烃与特种工程塑料基地',
					'client_fa' => 'شرکت صنایع پتروشیمی آبیدر پارس',
					'client_en' => 'Abidar Pars Petrochemical Industries',
					'client_zh' => '阿比达尔帕尔斯石化工业公司',
					'location_fa' => 'منطقه ویژه اقتصادی ماهشهر',
					'location_en' => 'Mahshahr Petrochemical Special Zone',
					'location_zh' => '马赫夏赫尔石化经济特区',
					'capacity'  => '۸۵۰,۰۰۰ تن در سال (850 KTPA)',
					'progress'  => 78,
					'year'      => '1405 / 2026',
					'image'     => $theme_uri . '/assets/images/sector-oil.jpg',
					'desc_fa'   => 'تولید گریدهای تخصصی پلی‌اتیلن سنگین و سبک خطی با لیسانس روز و صادرات مستقیم به بازارهای شرق آسیا و اروپا.',
					'desc_en'   => 'Manufacturing specialty HDPE and LLDPE grades for pipe and industrial applications, targeting East Asian and European export markets.',
					'desc_zh'   => '生产高密度聚乙烯（HDPE）与线性低密度聚乙烯（LLDPE）专用料，直接出口东亚与欧洲市场。',
				),
				array(
					'id'        => 'prj-6',
					'sector'    => 'energy',
					'sector_label_fa' => 'انرژی و نیروگاه‌ها',
					'sector_label_en' => 'Power & Clean Energy',
					'sector_label_zh' => '清洁能源',
					'title_fa'  => 'نیروگاه بازیافت حرارت (WHR) و ذخیره‌ساز باتری مجتمع‌های معدنی',
					'title_en'  => 'Industrial Waste Heat Recovery (WHR) & 150 MWh BESS Grid',
					'title_zh'  => '工业余热发电（WHR）与 150MWh 储能微电网项目',
					'client_fa' => 'معاونت انرژی‌های نو گروه آبیدر',
					'client_en' => 'Abidar New Energies Division',
					'client_zh' => '阿比达尔新能源事业部',
					'location_fa' => 'یزد و اصفهان',
					'location_en' => 'Yazd & Isfahan Industrial Corridors',
					'location_zh' => '亚兹德与伊斯法罕工业走廊',
					'capacity'  => '۱۸۰ مگاوات برق بدون سوخت فسیلی',
					'progress'  => 100,
					'year'      => '1404 / 2025',
					'image'     => $theme_uri . '/assets/images/sector-energy.jpg',
					'desc_fa'   => 'تولید برق پاک از حرارت خروجی خطوط گندله‌سازی و فولادسازی بدون مصرف قطره ای سوخت فسیلی به همراه ذخیره‌ساز لیتیومی.',
					'desc_en'   => 'Generating zero-emission electricity from pelletizing exhaust heat paired with utility-scale lithium iron phosphate battery storage.',
					'desc_zh'   => '利用球团与冶金尾气余热实现零燃料发电，并配套磷酸铁锂大型储能电站。',
				),
			);
		}

		if ( 'team' === $type ) {
			return array(
				array(
					'id'         => 'tm-1',
					'name_fa'    => 'دکتر کیوان رستگار آبیدر',
					'name_en'    => 'Dr. Keyvan Rastegar Abidar',
					'name_zh'    => '凯万·拉斯泰加尔 博士',
					'role_fa'    => 'رئیس هیئت مدیره و مدیرعامل گروه هلدینگ آبیدر',
					'role_en'    => 'Chairman of the Board & Group CEO',
					'role_zh'    => '集团董事长兼首席执行官 (CEO)',
					'dept_fa'    => 'هیئت مدیره و رهبری کلان',
					'dept_en'    => 'Executive Board',
					'dept_zh'    => '集团董事会',
					'education'  => 'دکتری مهندسی معدن و اقتصاد انرژی از دانشگاه صنعتی امیرکبیر و ETH زوریخ',
					'experience' => '۲۶ سال سابقه مدیریت کلان صنعتی',
					'email'      => 'ceo@abidargroup.com',
					'linkedin'   => 'https://linkedin.com',
					'initials'   => 'KR',
					'accent'     => '#D97706',
					'bio_fa'     => 'بیش از دو دهه راهبری طرح‌های ملی در حوزه معادن مس و سنگ‌آهن، نیروگاه‌های سیکل ترکیبی و کنسرسیوم‌های بین‌المللی انرژی.',
					'bio_en'     => 'Over two decades leading flagship copper & iron ore mega-projects, combined-cycle power stations, and international energy consortiums.',
					'bio_zh'     => '拥有二十余年大型铜铁矿开发、联合循环电站及跨国能源财团管理经验。',
				),
				array(
					'id'         => 'tm-2',
					'name_fa'    => 'مهندس سارا فرهمند راد',
					'name_en'    => 'Eng. Sara Farahmand Rad',
					'name_zh'    => '萨拉·法拉赫曼德 工程师',
					'role_fa'    => 'قائم‌مقام مدیرعامل و معاون انرژی و نیروگاه‌ها',
					'role_en'    => 'Deputy CEO & VP of Energy & Power Generation',
					'role_zh'    => '集团副总裁兼能源电力事业部总经理',
					'dept_fa'    => 'معاونت انرژی و نیروگاه‌ها',
					'dept_en'    => 'Energy Division',
					'dept_zh'    => '能源电力事业部',
					'education'  => 'کارشناسی ارشد مهندسی قدرت و سیستم‌های انرژی تجدیدپذیر - دانشگاه صنعتی شریف',
					'experience' => '۱۹ سال سابقه در پروژه‌های EPC نیروگاهی',
					'email'      => 's.farahmand@abidargroup.com',
					'linkedin'   => 'https://linkedin.com',
					'initials'   => 'SF',
					'accent'     => '#0EA5E9',
					'bio_fa'     => 'مدیر ارشد اجرایی بیش از ۳,۰۰۰ مگاوات پروژه نیروگاهی سیکل ترکیبی، خورشیدی و سیستم‌های انتقال برق فشار قوی.',
					'bio_en'     => 'Executive director of over 3,000 MW in combined-cycle, utility-scale solar PV, and high-voltage grid transmission projects.',
					'bio_zh'     => '主导超过 3,000 兆瓦联合循环电站、大型光伏基地及超高压电网工程建设。',
				),
				array(
					'id'         => 'tm-3',
					'name_fa'    => 'دکتر سهراب نادری‌پور',
					'name_en'    => 'Dr. Sohrab Naderipour',
					'name_zh'    => '苏赫拉布·纳德里的 博士',
					'role_fa'    => 'معاون ارشد نفت، گاز و صنایع پتروشیمی',
					'role_en'    => 'Senior VP of Oil, Gas & Petrochemicals',
					'role_zh'    => '集团高级副总裁兼石油石化事业部总经理',
					'dept_fa'    => 'معاونت نفت و پتروشیمی',
					'dept_en'    => 'Oil & Gas Division',
					'dept_zh'    => '石油石化事业部',
					'education'  => 'دکتری مهندسی شیمی و فرآیندهای پالایش از دانشگاه تهران',
					'experience' => '۲۲ سال سابقه در پارس جنوبی و پالایشگاه‌ها',
					'email'      => 's.naderipour@abidargroup.com',
					'linkedin'   => 'https://linkedin.com',
					'initials'   => 'SN',
					'accent'     => '#10B981',
					'bio_fa'     => 'متخصص برجسته طراحی واحدهای پالایش میعانات گازی، مجتمع‌های الفین و متانول و پایانه های صادراتی دریایی.',
					'bio_en'     => 'Leading specialist in gas condensate refining, olefin/methanol complexes, and offshore export terminal engineering.',
					'bio_zh'     => '凝析油炼化、烯烃甲醇联合装置及海上出口终端工程领域的资深专家。',
				),
				array(
					'id'         => 'tm-4',
					'name_fa'    => 'مهندس امیرحسین کردستانی',
					'name_en'    => 'Eng. Amirhossein Kordestani',
					'name_zh'    => '阿米尔侯赛因·库尔德斯坦尼',
					'role_fa'    => 'معاون اکتشاف، بهره‌برداری معادن و فرآوری مواد',
					'role_en'    => 'VP of Mining Exploration & Mineral Processing',
					'role_zh'    => '矿产勘探开采与选冶事业部副总裁',
					'dept_fa'    => 'معاونت معادن و صنایع معدنی',
					'dept_en'    => 'Mining Division',
					'dept_zh'    => '矿业与矿物加工事业部',
					'education'  => 'کارشناسی ارشد استخراج معادن و فرآوری مواد معدنی',
					'experience' => '۲۰ سال سابقه راهبری معادن روباز',
					'email'      => 'a.kordestani@abidargroup.com',
					'linkedin'   => 'https://linkedin.com',
					'initials'   => 'AK',
					'accent'     => '#F59E0B',
					'bio_fa'     => 'مسئول مستقیم طراحی معادن هوشمند، خطوط کنسانتره و گندله‌سازی و پایش ژئوتکنیکی ذخایر معدنی گروه آبیدر.',
					'bio_en'     => 'Head of smart open-pit mine planning, concentrate & pelletizing lines, and reserve resource modeling.',
					'bio_zh'     => '全面负责智慧露天矿山规划、选矿球团生产线及矿产资源储量评估。',
				),
				array(
					'id'         => 'tm-5',
					'name_fa'    => 'دکتر چن وی (Chen Wei)',
					'name_en'    => 'Dr. Chen Wei',
					'name_zh'    => '陈伟 博士 (Dr. Chen Wei)',
					'role_fa'    => 'مدیرکل بازرگانی بین‌الملل و دفتر منطقه‌ای شرق آسیا (شانگهای)',
					'role_en'    => 'Managing Director of APAC Trade & Shanghai Office',
					'role_zh'    => '亚太区国际贸易总裁兼上海代表处首席代表',
					'dept_fa'    => 'امور بین‌الملل و سرمایه‌گذاری',
					'dept_en'    => 'International Trade & APAC',
					'dept_zh'    => '国际贸易与亚太总部',
					'education'  => 'دکتری اقتصاد تجارت جهانی و زنجیره تامین انرژی - دانشگاه فودان شانگهای',
					'experience' => '۱۷ سال سابقه تجارت فراملی انرژی و فلزات',
					'email'      => 'chen.wei@abidargroup.com',
					'linkedin'   => 'https://linkedin.com',
					'initials'   => 'CW',
					'accent'     => '#8B5CF6',
					'bio_fa'     => 'راهبر قراردادهای صادراتی کنسانتره، گندله، محصولات پتروشیمی و تامین تجهیزات سنگین صنعتی میان ایران، چین و آسیای میانه.',
					'bio_en'     => 'Directing strategic mineral, petrochemical export agreements and heavy equipment procurement across China, MENA, and Central Asia.',
					'bio_zh'     => '负责集团矿产、石化产品对华出口长协及重型工业装备跨国采购与投融资合作。',
				),
				array(
					'id'         => 'tm-6',
					'name_fa'    => 'دکتر مریم جاویدان',
					'name_en'    => 'Dr. Maryam Javidan',
					'name_zh'    => '玛丽亚姆·贾维丹 博士',
					'role_fa'    => 'معاون مالی، اقتصادی و امور سهامداران (CFO)',
					'role_en'    => 'Chief Financial Officer (CFO) & Investor Relations',
					'role_zh'    => '集团首席财务官 (CFO) 兼投资者关系总监',
					'dept_fa'    => 'معاونت مالی و سرمایه‌گذاری',
					'dept_en'    => 'Finance & Investment',
					'dept_zh'    => '财务与资本运营中心',
					'education'  => 'دکتری مدیریت مالی و مهندسی سرمایه‌گذاری، دارای گواهینامه بین‌المللی CFA',
					'experience' => '۱۸ سال سابقه تامین مالی پروژه‌های کلان',
					'email'      => 'cfo@abidargroup.com',
					'linkedin'   => 'https://linkedin.com',
					'initials'   => 'MJ',
					'accent'     => '#EC4899',
					'bio_fa'     => 'طراح مدل‌های تامین مالی فاینانس، انتشار اوراق پروژه و مدیریت پرتفوی سرمایه‌گذاری شرکت‌های تابعه هلدینگ آبیدر.',
					'bio_en'     => 'Architect of project finance structures, green energy bonds, and multi-billion-dollar subsidiary portfolio governance.',
					'bio_zh'     => '主导大型工业项目结构化融资、绿色能源债券发行及集团下属控股公司资本运作。',
				),
			);
		}

		if ( 'certificates' === $type ) {
			return array(
				array(
					'id'          => 'cert-1',
					'code'        => 'ISO 9001:2015 / AB-99401',
					'year'        => '2025 - 2028',
					'issuer_fa'   => 'مرجع بین‌المللی صدور گواهینامه TÜV NORD آلمان',
					'issuer_en'   => 'TÜV NORD CERT GmbH (Germany)',
					'issuer_zh'   => '德国莱茵 TÜV NORD 国际认证机构',
					'title_fa'    => 'گواهینامه سیستم مدیریت کیفیت یکپارچه در پروژه‌های EPC معادن و انرژی',
					'title_en'    => 'ISO 9001:2015 Quality Management System in Mining & Energy EPC',
					'title_zh'    => 'ISO 9001:2015 矿业与能源EPC工程质量管理体系认证',
					'category_fa' => 'استاندارد بین‌المللی ISO',
					'category_en' => 'International ISO Standard',
					'category_zh' => 'ISO 国际标准认证',
					'badge'       => 'ISO 9001',
				),
				array(
					'id'          => 'cert-2',
					'code'        => 'ISO 14001:2015 & ISO 45001:2018',
					'year'        => '2025 - 2028',
					'issuer_fa'   => 'موسسه استاندارد جهانی SGS سوئیس',
					'issuer_en'   => 'SGS International Certification Services (Switzerland)',
					'issuer_zh'   => '瑞士 SGS 国际通用公证行认证',
					'title_fa'    => 'گواهینامه مدیریت زیست‌محیطی معادن سبز و ایمنی و بهداشت شغلی (HSE)',
					'title_en'    => 'ISO 14001 & 45001 Environmental & Occupational Health Safety (HSE)',
					'title_zh'    => 'ISO 14001 & 45001 绿色矿山环境与职业健康安全 (HSE) 认证',
					'category_fa' => 'ایمنی، بهداشت و محیط‌زیست (HSE)',
					'category_en' => 'HSE & Sustainability',
					'category_zh' => 'HSE 安全与环保体系',
					'badge'       => 'HSE / ISO 45001',
				),
				array(
					'id'          => 'cert-3',
					'code'        => 'ISO/TS 29001:2020 - OIL-7782',
					'year'        => '2024 - 2027',
					'issuer_fa'   => 'انجمن نفت و استانداردهای صنایع پتروشیمی (API / ISO)',
					'issuer_en'   => 'Petroleum, Petrochemical & Natural Gas Sector Certification',
					'issuer_zh'   => '石油、石化与天然气工业质量管理体系特定要求认证',
					'title_fa'    => 'گواهینامه تخصصی مدیریت کیفیت در صنایع نفت، گاز و پتروشیمی',
					'title_en'    => 'ISO 29001:2020 Petroleum, Petrochemical & Natural Gas Quality Standard',
					'title_zh'    => 'ISO 29001:2020 石油、石化及天然气行业专属质量标准认证',
					'category_fa' => 'تخصصی نفت و گاز',
					'category_en' => 'Oil & Gas Compliance',
					'category_zh' => '油气行业权威资质',
					'badge'       => 'ISO 29001',
				),
				array(
					'id'          => 'cert-4',
					'code'        => 'NAT-GRADE-1-EPC-1404',
					'year'        => '1404 / 2025',
					'issuer_fa'   => 'سازمان برنامه و بودجه کشور و وزارت صنعت، معدن و تجارت',
					'issuer_en'   => 'Plan & Budget Organization & Ministry of Industry, Mine & Trade',
					'issuer_zh'   => '国家规划与预算组织及工业、矿业和贸易部',
					'title_fa'    => 'گواهینامه صلاحیت پیمانکاری و EPC پایه یک در رشته‌های معدن، نیرو و نفت و گاز',
					'title_en'    => 'Grade-1 National EPC Contractor License in Mining, Power & Oil/Gas',
					'title_zh'    => '国家矿山、电力及油气工程总承包（EPC）一级资质证书',
					'category_fa' => 'مجوز رسمی حاکمیتی',
					'category_en' => 'National Grade-1 License',
					'category_zh' => '国家级特级/一级资质',
					'badge'       => 'GRADE 1 EPC',
				),
				array(
					'id'          => 'cert-5',
					'code'        => 'EXPORT-GOLD-TROPHY-2025',
					'year'        => '1404 / 2025',
					'issuer_fa'   => 'سازمان توسعه تجارت و اتاق بازرگانی، صنایع، معادن و کشاورزی',
					'issuer_en'   => 'Trade Promotion Organization & Chamber of Commerce, Industries & Mines',
					'issuer_zh'   => '国家贸易促进组织与工矿业商会',
					'title_fa'    => 'لوح زرین و تندیس صادرکننده ممتاز ملی در بخش فرآورده‌های معدنی و پتروشیمی',
					'title_en'    => 'National Distinguished Exporter Gold Plaque in Minerals & Petrochemicals',
					'title_zh'    => '国家级矿产与石化产品卓越出口企业金奖奖牌',
					'category_fa' => 'لوح افتخار و تندیس ملی',
					'category_en' => 'National Excellence Award',
					'category_zh' => '国家级荣誉奖牌',
					'badge'       => 'TOP EXPORTER',
				),
				array(
					'id'          => 'cert-6',
					'code'        => 'ISO 50001:2018 - EN-3310',
					'year'        => '2025 - 2028',
					'issuer_fa'   => 'آژانس بین‌المللی ممیزی انرژی و کربن اروپا',
					'issuer_en'   => 'European Energy Management & Decarbonization Bureau',
					'issuer_zh'   => '欧洲能源管理与低碳工业认证局',
					'title_fa'    => 'گواهینامه سیستم مدیریت انرژی و بهینه‌سازی مصرف نیروگاهی (ISO 50001)',
					'title_en'    => 'ISO 50001:2018 Industrial Energy Management & Efficiency System',
					'title_zh'    => 'ISO 50001:2018 工业能源管理与能效优化体系认证',
					'category_fa' => 'مدیریت انرژی پاک',
					'category_en' => 'Clean Energy Standard',
					'category_zh' => '清洁能源管理标准',
					'badge'       => 'ISO 50001',
				),
			);
		}

		if ( 'posts' === $type ) {
			return array(
				array(
					'id'           => 'post-1',
					'sector'       => 'mining',
					'category_fa'  => 'گزارش فنی معادن',
					'category_en'  => 'Mining Engineering Report',
					'category_zh'  => '矿业技术报告',
					'date_fa'      => '۱۸ آبان ۱۴۰۵',
					'date_en'      => 'November 8, 2026',
					'date_zh'      => '2026年11月8日',
					'reading_time' => '۷',
					'author_fa'    => 'دپارتمان پژوهش و توسعه گروه آبیدر',
					'author_en'    => 'Abidar R&D Engineering Desk',
					'author_zh'    => '阿比达尔集团研发中心',
					'title_fa'     => 'تحول دیجیتال در معادن روباز: چگونه ناوگان هوشمند و شبکه 5G راندمان استخراج گروه آبیدر را ۲۸٪ افزایش داد؟',
					'title_en'     => 'Digital Transformation in Open-Pit Mining: How Private 5G & AI Fleet Dispatching Boosted Abidar Output by 28%',
					'title_zh'     => '露天矿山数字化转型：5G专网与AI智能调度如何助力阿比达尔集团提升28%开采效率',
					'excerpt_fa'   => 'بررسی جامع پیاده‌سازی سنسورهای اینترنت اشیاء صنعتی (IIoT)، بهینه‌سازی مصرف سوخت دامپتراک‌ها و پایش لحظه‌ای عیار کانسنگ در مجتمع‌های معدنی گروه آبیدر.',
					'excerpt_en'   => 'An executive technical review of Industrial IoT sensors, autonomous haulage fuel optimization, and real-time ore grade telemetry across Abidar mining complexes.',
					'excerpt_zh'   => '深入解析工业物联网（IIoT）传感器、矿卡节油优化算法及矿石品位实时遥测系统在阿比达尔矿山基地的工程实践。',
					'image'        => $theme_uri . '/assets/images/sector-mining.jpg',
				),
				array(
					'id'           => 'post-2',
					'sector'       => 'energy',
					'category_fa'  => 'آینده انرژی و نیروگاه‌ها',
					'category_en'  => 'Clean Energy & Power Grid',
					'category_zh'  => '清洁能源与电网洞察',
					'date_fa'      => '۱۲ آبان ۱۴۰۵',
					'date_en'      => 'November 2, 2026',
					'date_zh'      => '2026年11月2日',
					'reading_time' => '۶',
					'author_fa'    => 'معاونت انرژی و نیروگاه‌های سیکل ترکیبی',
					'author_en'    => 'Power & Clean Energy Division',
					'author_zh'    => '清洁能源与发电事业部',
					'title_fa'     => 'ترکیب نیروگاه‌های سیکل ترکیبی کلاس F با مزارع خورشیدی و ذخیره‌سازهای مگاواتی؛ الگوی نوین تامین برق صنایع سنگین',
					'title_en'     => 'Hybridizing F-Class Combined-Cycle Plants with Utility Solar & BESS: The New Blueprint for Heavy Industrial Power',
					'title_zh'     => 'F级燃气联合循环电站与大型光伏储能耦合：重工业稳定绿电供应新范式',
					'excerpt_fa'   => 'چگونه هلدینگ آبیدر با ادغام توربین‌های گازی راندمان بالا و مزارع فتوولتائیک، ناترازی انرژی صنایع معدنی و پتروشیمی را به صفر رسانده است.',
					'excerpt_en'   => 'How Abidar Group Holding eliminates grid intermittency for mineral and petrochemical plants by coupling high-efficiency gas turbines with solar PV and battery storage.',
					'excerpt_zh'   => '阿比达尔控股集团通过高效燃气轮机与光伏储能协同调度，彻底解决矿山与石化园区用电峰谷差问题。',
					'image'        => $theme_uri . '/assets/images/sector-energy.jpg',
				),
				array(
					'id'           => 'post-3',
					'sector'       => 'oil',
					'category_fa'  => 'نفت، گاز و بازارهای جهانی',
					'category_en'  => 'Oil, Gas & Petrochemical Markets',
					'category_zh'  => '油气与石化国际市场',
					'date_fa'      => '۵ آبان ۱۴۰۵',
					'date_en'      => 'October 26, 2026',
					'date_zh'      => '2026年10月26日',
					'reading_time' => '۸',
					'author_fa'    => 'دفتر مطالعات استراتژیک نفت و پتروشیمی',
					'author_en'    => 'Oil & Petrochemical Strategic Desk',
					'author_zh'    => '石油石化战略研究院',
					'title_fa'     => 'توسعه زنجیره ارزش میعانات گازی و الفین‌های صادراتی؛ راهبرد ۱۴۰۵ گروه آبیدر در بازارهای شرق آسیا',
					'title_en'     => 'Expanding Gas Condensate & Olefin Value Chains: Abidar Group 2026-2030 Strategy for East Asian Markets',
					'title_zh'     => '延伸凝析油与烯烃深加工价值链：阿比达尔集团 2026-2030 东亚市场战略布局',
					'excerpt_fa'   => 'تحلیل فرصت‌های صادراتی پلیمرهای مهندسی، ارتقای استانداردهای پایانه دریایی خلیج فارس و قراردادهای بلندمدت با شرکای صنعتی در چین و آسیا.',
					'excerpt_en'   => 'Analyzing export growth in engineering polymers, VLCC terminal upgrades in the Persian Gulf, and long-term supply agreements with industrial partners in China.',
					'excerpt_zh'   => '剖析特种工程塑料出口增长潜力、波斯湾深水码头升级工程及与中国工业伙伴的长期供应链合作协议。',
					'image'        => $theme_uri . '/assets/images/sector-oil.jpg',
				),
			);
		}

		return array();
	}
}

Abidar_CPT::init();
