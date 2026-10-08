<?php
/**
 * Clinic info (Appearance → Customize → Thông tin phòng khám).
 * Every phone number, link and address on the site reads from here.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions: key => [label, default, type].
 * Defaults marked [cần điền] / 0900 000 000 are placeholders — replace before launch.
 *
 * @return array<string,array{0:string,1:string,2:string}>
 */
function qd_clinic_fields() {
	return array(
		'name'          => array( 'Tên phòng khám', 'Phòng khám Da liễu Thẩm mỹ Quang Đăng', 'text' ),
		'brand'         => array( 'Tên ngắn (logo chữ)', 'Quang Đăng', 'text' ),
		'tagline'       => array( 'Dòng phụ dưới logo', 'Viện thẩm mỹ quốc tế', 'text' ),
		'hotline'       => array( 'Hotline (hiển thị)', '0900 000 000', 'text' ),
		'zalo'          => array( 'Link Zalo', 'https://zalo.me/0900000000', 'url' ),
		'facebook'      => array( 'Link Facebook fanpage', 'https://www.facebook.com/', 'url' ),
		'messenger'     => array( 'Link Messenger (m.me/...)', '', 'url' ),
		'email'         => array( 'Email nhận lịch hẹn', '', 'email' ),
		'address'       => array( 'Địa chỉ đầy đủ', 'Tầng 5, TTTM Đức Tài – Tâm Đạt, Quỳnh Lưu, Nghệ An', 'text' ),
		'address_short' => array( 'Địa chỉ ngắn (thanh trên cùng)', 'Tầng 5, TTTM Đức Tài – Tâm Đạt, Quỳnh Lưu', 'text' ),
		'map_url'       => array( 'Link Google Maps (nút Chỉ đường)', 'https://www.google.com/maps/search/?api=1&query=TTTM+%C4%90%E1%BB%A9c+T%C3%A0i+T%C3%A2m+%C4%90%E1%BA%A1t+Qu%E1%BB%B3nh+L%C6%B0u+Ngh%E1%BB%87+An', 'url' ),
		'map_embed'     => array( 'Link nhúng bản đồ (src của iframe)', '', 'url' ),
		'hours'         => array( 'Giờ làm việc', '8:00 – 20:00, tất cả các ngày', 'text' ),
		'license'       => array( 'Số giấy phép hoạt động', '[cần điền]', 'text' ),
		'announcement'  => array( 'Thông báo đầu trang (để trống để ẩn)', 'Phòng khám làm việc xuyên lễ – đặt lịch trước để được ưu tiên.', 'text' ),
		'announce_url'  => array( 'Link của thông báo', '/dat-lich/', 'text' ),
	);
}

/**
 * Read a clinic field.
 *
 * @param string $key Field key.
 */
function qd_clinic( $key ) {
	$fields = qd_clinic_fields();
	$value  = get_theme_mod( 'qd_' . $key, $fields[ $key ][1] ?? '' );
	return is_string( $value ) ? $value : '';
}

/**
 * tel: href from the hotline.
 */
function qd_tel_href() {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', qd_clinic( 'hotline' ) );
}

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			'qd_clinic',
			array(
				'title'    => __( 'Thông tin phòng khám', 'quangdang' ),
				'priority' => 30,
			)
		);
		foreach ( qd_clinic_fields() as $key => list( $label, $default, $type ) ) {
			$sanitize = array(
				'url'   => 'esc_url_raw',
				'email' => 'sanitize_email',
			)[ $type ] ?? 'sanitize_text_field';
			$wp_customize->add_setting(
				'qd_' . $key,
				array(
					'default'           => $default,
					'sanitize_callback' => $sanitize,
				)
			);
			$wp_customize->add_control(
				'qd_' . $key,
				array(
					'label'   => $label,
					'section' => 'qd_clinic',
					'type'    => 'url' === $type ? 'url' : ( 'email' === $type ? 'email' : 'text' ),
				)
			);
		}
	}
);
