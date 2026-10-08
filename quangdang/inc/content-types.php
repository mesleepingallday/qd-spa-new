<?php
/**
 * Content model.
 *
 * dich-vu   Services. Hierarchical: a top-level post is a CATEGORY (Điều trị mụn),
 *           its children are SERVICES (Trị mụn kháng khuẩn đa tầng).
 *           URLs: /dich-vu/ · /dich-vu/dieu-tri-mun/ · /dich-vu/dieu-tri-mun/tri-mun-khang-khuan-da-tang/
 * khoa-hoc  Training courses, archive at /dao-tao/.
 * bac-si    Doctors, reused on About, service and article pages.
 * lich-hen  Booking requests (private, admin only).
 *
 * Posts use categories cam-nang-lam-dep / su-kien-uu-dai under the /tin-tuc/ base.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Vietnamese CPT labels.
 *
 * @param string $one  Singular.
 * @param string $many Plural / menu name.
 */
function qd_cpt_labels( $one, $many ) {
	return array(
		'name'               => $many,
		'singular_name'      => $one,
		'menu_name'          => $many,
		'add_new'            => 'Thêm mới',
		'add_new_item'       => 'Thêm ' . mb_strtolower( $one ),
		'edit_item'          => 'Sửa ' . mb_strtolower( $one ),
		'new_item'           => $one . ' mới',
		'view_item'          => 'Xem ' . mb_strtolower( $one ),
		'search_items'       => 'Tìm ' . mb_strtolower( $one ),
		'not_found'          => 'Chưa có ' . mb_strtolower( $one ) . ' nào',
		'all_items'          => 'Tất cả ' . mb_strtolower( $many ),
		'parent_item_colon'  => 'Nhóm cha:',
		'items_list'         => $many,
		'featured_image'     => 'Ảnh đại diện',
		'set_featured_image' => 'Chọn ảnh đại diện',
	);
}

add_action(
	'init',
	function () {
		register_post_type(
			'dich-vu',
			array(
				'labels'       => qd_cpt_labels( 'Dịch vụ', 'Dịch vụ' ),
				'description'  => 'Bài cấp 1 là nhóm dịch vụ (VD: Điều trị mụn), bài con là dịch vụ cụ thể.',
				'public'       => true,
				'hierarchical' => true,
				'has_archive'  => 'dich-vu',
				'rewrite'      => array(
					'slug'       => 'dich-vu',
					'with_front' => false,
				),
				'menu_icon'    => 'dashicons-heart',
				'menu_position' => 5,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			)
		);

		register_post_type(
			'khoa-hoc',
			array(
				'labels'       => qd_cpt_labels( 'Khóa học', 'Đào tạo' ),
				'public'       => true,
				'has_archive'  => 'dao-tao',
				'rewrite'      => array(
					'slug'       => 'dao-tao',
					'with_front' => false,
				),
				'menu_icon'    => 'dashicons-welcome-learn-more',
				'menu_position' => 6,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			)
		);

		register_post_type(
			'bac-si',
			array(
				'labels'       => qd_cpt_labels( 'Bác sĩ', 'Bác sĩ' ),
				'public'       => true,
				'has_archive'  => false,
				'rewrite'      => array(
					'slug'       => 'bac-si',
					'with_front' => false,
				),
				'menu_icon'    => 'dashicons-id',
				'menu_position' => 7,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			)
		);

		register_post_type(
			'lich-hen',
			array(
				'labels'          => qd_cpt_labels( 'Lịch hẹn', 'Lịch hẹn' ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-calendar-alt',
				'menu_position'   => 4,
				'capability_type' => 'post',
				'supports'        => array( 'title', 'custom-fields' ),
			)
		);

		// Post meta. Underscore keys stay out of the generic Custom Fields box;
		// they are edited in the theme's own meta boxes.
		$auth = fn() => current_user_can( 'edit_posts' );
		$meta = array(
			'dich-vu'  => array(
				'_qd_group'      => 'string',  // Category only: dieu-tri-da | cham-soc-da | noi-khoa-tham-my.
				'_qd_price_from' => 'integer', // VND.
				'_qd_price_unit' => 'string',  // "/buổi", "/liệu trình", "/vùng".
				'_qd_sessions'   => 'string',
				'_qd_duration'   => 'string',
				'_qd_downtime'   => 'string',
				'_qd_suits'      => 'string',  // One per line.
				'_qd_not_suits'  => 'string',  // One per line.
				'_qd_technology' => 'string',
				'_qd_steps'      => 'string',  // "Tiêu đề | Mô tả" per line.
				'_qd_prices'     => 'string',  // "Gói | Giá | Ghi chú" per line.
				'_qd_aftercare'  => 'string',  // One per line.
				'_qd_faq'        => 'string',  // "Câu hỏi | Trả lời" per line.
				'_qd_doctor'     => 'integer', // bac-si post ID.
			),
			'post'     => array(
				'_qd_promo_start' => 'string', // Y-m-d, Sự kiện – Ưu đãi only.
				'_qd_promo_end'   => 'string',
				'_qd_promo_label' => 'string', // Short badge text, e.g. "Giảm 30%".
				'_qd_reviewer'    => 'integer', // bac-si post ID ("Tham vấn y khoa").
			),
			'khoa-hoc' => array(
				'_qd_course_fee'      => 'integer',
				'_qd_course_length'   => 'string',
				'_qd_course_format'   => 'string',
				'_qd_course_schedule' => 'string',
				'_qd_course_modules'  => 'string', // "Tên học phần | Nội dung" per line.
			),
			'bac-si'   => array(
				'_qd_doctor_title'     => 'string', // "Bác sĩ chuyên khoa I Da liễu".
				'_qd_doctor_role'      => 'string', // "Giám đốc chuyên môn".
				'_qd_doctor_years'     => 'integer',
				'_qd_doctor_education' => 'string', // One per line.
			),
		);
		foreach ( $meta as $type => $keys ) {
			foreach ( $keys as $key => $kind ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'          => $kind,
						'single'        => true,
						'show_in_rest'  => true,
						'auth_callback' => $auth,
					)
				);
			}
		}
	}
);

// On activation: category archives under /tin-tuc/ (e.g. /tin-tuc/cam-nang-lam-dep/) and fresh rewrite rules
// so the CPT URLs work immediately. Only fills the base if the site has not set its own.
add_action(
	'after_switch_theme',
	function () {
		if ( ! get_option( 'category_base' ) ) {
			update_option( 'category_base', 'tin-tuc' );
		}
		flush_rewrite_rules();
	}
);
