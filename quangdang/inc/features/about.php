<?php
/**
 * Area: About (Giới thiệu), Doctors, Training (Đào tạo) and 404.
 *
 * Registers page styles + script, the admin meta boxes for doctors and courses, and the
 * small helpers the templates in template-parts/about/ share.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Styles and script
 * ---------------------------------------------------------------------- */

/**
 * Is the current request one of the About pages (hub or subpage)?
 */
function qd_is_about_area() {
	if ( is_singular( 'bac-si' ) ) {
		return true;
	}
	if ( ! is_page() ) {
		return false;
	}
	$post = get_queried_object();
	return $post && ( 'gioi-thieu' === $post->post_name || ( $post->post_parent && 'gioi-thieu' === get_post_field( 'post_name', $post->post_parent ) ) );
}

add_filter(
	'qd_page_styles',
	function ( $styles ) {
		$styles['qd-about']    = array( 'assets/css/pages/about.css', fn() => qd_is_about_area() || is_404() );
		$styles['qd-trust']    = array( 'assets/css/pages/trust.css', fn() => is_page( 'gioi-thieu' ) || is_post_type_archive( 'khoa-hoc' ) );
		$styles['qd-training'] = array( 'assets/css/pages/training.css', fn() => is_singular( 'khoa-hoc' ) || is_post_type_archive( 'khoa-hoc' ) );
		return $styles;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_page() && qd_is_about_area() && file_exists( QD_DIR . '/assets/js/about.js' ) ) {
			wp_enqueue_script(
				'qd-about',
				QD_URI . '/assets/js/about.js',
				array(),
				qd_asset_ver( 'assets/js/about.js' ),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
);

// Courses read best in curriculum order (Cơ bản → Chuyên sâu), not by date.
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'khoa-hoc' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
			$query->set( 'posts_per_page', 12 );
		}
	}
);

/* -------------------------------------------------------------------------
 * Meta boxes
 * ---------------------------------------------------------------------- */

add_action(
	'init',
	function () {
		foreach ( array( '_qd_course_audience', '_qd_course_outcomes' ) as $key ) {
			register_post_meta(
				'khoa-hoc',
				$key,
				array(
					'type'          => 'string',
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => fn() => current_user_can( 'edit_posts' ),
				)
			);
		}
	}
);

qd_meta_box(
	'qd-doctor',
	'Thông tin bác sĩ',
	'bac-si',
	array(
		'_qd_doctor_title'     => array(
			'label' => 'Học hàm – chuyên môn',
			'help'  => 'VD: Bác sĩ Chuyên khoa I Da liễu',
		),
		'_qd_doctor_role'      => array(
			'label' => 'Vai trò tại phòng khám',
			'help'  => 'VD: Giám đốc chuyên môn',
		),
		'_qd_doctor_years'     => array(
			'label' => 'Số năm kinh nghiệm',
			'type'  => 'number',
		),
		'_qd_doctor_education' => array(
			'label' => 'Học vấn – chứng chỉ',
			'type'  => 'textarea',
			'rows'  => 5,
			'help'  => 'Mỗi dòng một mục. Chỉ ghi những gì có giấy tờ xác thực.',
		),
	)
);

qd_meta_box(
	'qd-course',
	'Thông tin khóa học',
	'khoa-hoc',
	array(
		'_qd_course_fee'      => array(
			'label' => 'Học phí (VNĐ)',
			'type'  => 'number',
			'help'  => 'Để 0 nếu muốn hiển thị "Liên hệ".',
		),
		'_qd_course_length'   => array(
			'label' => 'Thời lượng',
			'help'  => 'VD: 4 tuần (12 buổi)',
		),
		'_qd_course_format'   => array(
			'label' => 'Hình thức học',
			'help'  => 'VD: Học trực tiếp tại phòng khám',
		),
		'_qd_course_schedule' => array(
			'label' => 'Lịch học',
			'help'  => 'VD: Tối thứ 2 – 4 – 6, khai giảng hằng tháng',
		),
		'_qd_course_modules'  => array(
			'label' => 'Chương trình học',
			'type'  => 'textarea',
			'rows'  => 6,
			'help'  => 'Mỗi dòng: Tên học phần | Nội dung',
		),
		'_qd_course_audience' => array(
			'label' => 'Dành cho ai (tùy chọn)',
			'type'  => 'textarea',
			'rows'  => 4,
			'help'  => 'Mỗi dòng một đối tượng. Để trống sẽ dùng nội dung mặc định.',
		),
		'_qd_course_outcomes' => array(
			'label' => 'Sau khóa học bạn nhận được (tùy chọn)',
			'type'  => 'textarea',
			'rows'  => 4,
			'help'  => 'Mỗi dòng một quyền lợi. Để trống sẽ dùng nội dung mặc định.',
		),
	)
);

// Timeline for "Quá trình hình thành": one milestone per line on that page.
add_action(
	'add_meta_boxes_page',
	function ( $post ) {
		if ( 'qua-trinh-hinh-thanh' !== $post->post_name ) {
			return;
		}
		add_meta_box(
			'qd-timeline',
			'Các mốc phát triển',
			function ( $post ) {
				wp_nonce_field( 'qd-timeline', 'qd_timeline_nonce' );
				echo '<p><label for="qd_timeline"><strong>Mỗi dòng một mốc: Năm | Tiêu đề | Mô tả</strong></label></p>';
				echo '<textarea class="widefat" rows="8" id="qd_timeline" name="qd_timeline">' . esc_textarea( (string) get_post_meta( $post->ID, '_qd_timeline', true ) ) . '</textarea>';
				echo '<p class="description">Để trống sẽ hiện các mốc mẫu (đánh dấu "cần điền"). Chỉ ghi thông tin có thật.</p>';
			},
			'page',
			'normal',
			'high'
		);
	}
);

add_action(
	'save_post_page',
	function ( $post_id ) {
		if ( ! isset( $_POST['qd_timeline_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['qd_timeline_nonce'] ), 'qd-timeline' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_qd_timeline', sanitize_textarea_field( wp_unslash( $_POST['qd_timeline'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}
);

/* -------------------------------------------------------------------------
 * Helpers shared by the About templates
 * ---------------------------------------------------------------------- */

/**
 * The six About subpages: slug => [tab label, card title, one-line description, icon].
 * Order and wording mirror the "Giới thiệu" menu.
 *
 * @return array<string,array{0:string,1:string,2:string,3:string}>
 */
function qd_about_pages() {
	return apply_filters(
		'qd_about_pages',
		array(
			'qua-trinh-hinh-thanh'   => array( 'Quá trình hình thành', 'Quá trình hình thành', 'Chặng đường phát triển của Quang Đăng.', 'hourglass' ),
			'tam-nhin-su-menh'       => array( 'Tầm nhìn & Sứ mệnh', 'Tầm nhìn & Sứ mệnh', 'Điều chúng tôi cam kết với từng khách hàng.', 'eye' ),
			'doi-ngu-bac-si'         => array( 'Đội ngũ bác sĩ', 'Đội ngũ chuyên gia bác sĩ', 'Bác sĩ da liễu trực tiếp thăm khám và điều trị.', 'stethoscope' ),
			'cong-nghe-san-pham'     => array( 'Công nghệ & Sản phẩm', 'Công nghệ & Sản phẩm', 'Thiết bị và dược mỹ phẩm chính hãng.', 'zap' ),
			'co-so-vat-chat'         => array( 'Cơ sở vật chất', 'Cơ sở vật chất', 'Không gian phòng khám tại Quỳnh Lưu.', 'map-pin' ),
			'quy-trinh-chuan-y-khoa' => array( 'Quy trình chuẩn y khoa', 'Quy trình chuẩn y khoa', 'Từ thăm khám, soi da đến tái khám.', 'clipboard-list' ),
		)
	);
}

/**
 * URL of an About subpage (or the hub when $slug is empty).
 *
 * @param string $slug Subpage slug.
 */
function qd_about_url( $slug = '' ) {
	return home_url( '/gioi-thieu/' . ( $slug ? $slug . '/' : '' ) );
}

/**
 * A theme photo in a fixed-ratio frame, or '' when it must not be shown.
 * In production, placeholder stand-ins are hidden so the site never ships fake "real" photos.
 *
 * @param string $key   Asset key, e.g. "about/le-tan".
 * @param string $alt   Alt text.
 * @param string $ratio Frame ratio suffix (4-3, 16-9, 4-5, 1-1).
 * @param array  $attrs Extra <img> attributes.
 * @param string $class Extra frame classes.
 */
function qd_about_photo( $key, $alt, $ratio = '4-3', $attrs = array(), $class = '' ) {
	$img = qd_asset_image( $key );
	if ( ! $img || ( $img['placeholder'] && ! qd_show_placeholders() ) ) {
		return '';
	}
	return '<span class="frame frame--' . esc_attr( $ratio ) . ' ' . esc_attr( $class ) . '">' . qd_asset_img( $key, $alt, $attrs ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Editor content of the current page, or '' when it only repeats the excerpt
 * (the demo content is just the excerpt in a paragraph).
 */
function qd_about_content() {
	$post = get_post();
	if ( ! $post ) {
		return '';
	}
	$text = trim( wp_strip_all_tags( $post->post_content ) );
	if ( '' === $text || $text === trim( (string) $post->post_excerpt ) ) {
		return '';
	}
	return apply_filters( 'the_content', $post->post_content );
}

/**
 * Find a service by its path under /dich-vu/ ("cham-soc-da/triet-long").
 * Returns [title, url] or null when the service does not exist (so no dead links).
 *
 * @param string $path Service path.
 * @return array{0:string,1:string}|null
 */
function qd_about_service( $path ) {
	$post = get_page_by_path( $path, OBJECT, 'dich-vu' );
	return ( $post && 'publish' === $post->post_status ) ? array( get_the_title( $post ), get_permalink( $post ) ) : null;
}

/**
 * Published doctors in the order set by Thứ tự (menu_order).
 *
 * @param int $count How many (-1 = all).
 * @return WP_Post[]
 */
function qd_about_doctors( $count = -1 ) {
	return get_posts(
		array(
			'post_type'      => 'bac-si',
			'posts_per_page' => $count,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
}
