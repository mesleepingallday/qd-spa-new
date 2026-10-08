<?php
/**
 * Extra demo articles so the news list shows pagination, topic chips and ended promotions.
 * Runs after the core seed (variables $qd_cats and $qd_doctors come from dev/seed.php).
 *
 * @package QuangDang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$qd_extra_body = function ( $topic ) {
	return '<p>' . $topic . ' là điều nhiều khách hàng hỏi bác sĩ khi đến thăm khám. Bài viết tóm tắt những điều cần biết để bạn chăm sóc da đúng cách và biết khi nào nên gặp bác sĩ.</p>'
		. '<h2>Dấu hiệu nhận biết</h2><p>Mỗi người có cơ địa khác nhau nên biểu hiện cũng khác nhau. Quan sát kỹ làn da trong vài tuần giúp bạn mô tả chính xác hơn với bác sĩ.</p><ul><li>Thay đổi màu da, độ ẩm, độ nhạy cảm.</li><li>Xuất hiện nốt, mảng hoặc vùng da sần.</li><li>Tình trạng kéo dài, không cải thiện sau 2–3 tuần.</li></ul>'
		. '<h2>Nguyên nhân thường gặp</h2><p>Thói quen sinh hoạt, ánh nắng, nội tiết và sản phẩm đang dùng đều có thể ảnh hưởng. Đừng tự áp dụng nhiều phương pháp cùng lúc vì dễ làm da tổn thương.</p>'
		. '<h2>Chăm sóc tại nhà</h2><p>Làm sạch dịu nhẹ, dưỡng ẩm đủ và chống nắng mỗi ngày là nền tảng cho mọi liệu trình.</p>'
		. '<h2>Khi nào nên đến gặp bác sĩ?</h2><p>Nếu tình trạng kéo dài, lan rộng hoặc gây đau rát, hãy đặt lịch để bác sĩ da liễu thăm khám và tư vấn phác đồ phù hợp.</p>';
};

$qd_extra_posts = array(
	array( 'Cách chọn kem chống nắng phù hợp cho da dầu mụn', 'dieu-tri-mun', 'mun-an-la-gi-nguyen-nhan-va-cach-xu-ly-dung-cach', '-38 days', 0 ),
	array( 'Thâm sau mụn bao lâu thì mờ? Những điều nên và không nên làm', 'dieu-tri-tham', 'tham-nach-vi-sao-bi-va-dieu-tri-bao-lau-thi-cai-thien', '-42 days', 0 ),
	array( 'Nám da và nội tiết: vì sao nám hay tái phát sau sinh?', 'dieu-tri-nam', 'nam-da-phan-biet-nam-mang-nam-chan-sau-va-tan-nhang', '-46 days', 0 ),
	array( 'Làm sạch sâu da mặt: bao lâu một lần là đủ?', 'cham-soc-da', 'quy-trinh-cham-soc-da-co-ban-4-buoc-cho-nguoi-moi-bat-dau', '-50 days', 0 ),
	array( 'Sẹo lồi và sẹo lõm: hai loại sẹo, hai hướng điều trị', 'dieu-tri-seo', 'seo-ro-sau-mun-co-tri-dut-diem-duoc-khong', '-55 days', 0 ),
	array( 'Botox và filler khác nhau thế nào? Giải thích đơn giản', 'noi-khoa-tham-my', 'tiem-filler-can-luu-y-gi-de-an-toan-huong-dan-chi-tiet-tu-bac-si-da-lieu-cho-nguoi-lan-dau-lam-dep-khong-phau-thuat', '-60 days', 0 ),
	array( 'Chuẩn bị gì trước buổi triệt lông đầu tiên?', 'cham-soc-da', 'triet-long-bang-laser-co-dau-khong-4-cau-hoi-thuong-gap', '-64 days', 0 ),
	array( 'Da nhạy cảm sau khi điều trị: 6 điều cần tránh', 'cham-soc-da', 'quy-trinh-cham-soc-da-co-ban-4-buoc-cho-nguoi-moi-bat-dau', '-70 days', 0 ),
	array( 'Mụn ở lưng và ngực: nguyên nhân và cách chăm sóc', 'dieu-tri-mun', 'mun-an-la-gi-nguyen-nhan-va-cach-xu-ly-dung-cach', '-75 days', 0 ),
	array( 'Tàn nhang có mất đi được không?', 'dieu-tri-nam', 'nam-da-phan-biet-nam-mang-nam-chan-sau-va-tan-nhang', '-80 days', 0 ),
	array( 'Thói quen buổi tối giúp da phục hồi nhanh hơn', 'cham-soc-da', 'quy-trinh-cham-soc-da-co-ban-4-buoc-cho-nguoi-moi-bat-dau', '-85 days', 0 ),
	array( 'Bài viết chưa có bác sĩ tham vấn (kiểm tra bố cục)', 'cham-soc-da', '', '-90 days', 0 ),
);

foreach ( $qd_extra_posts as list( $title, $tag, $image, $when ) ) {
	$id = qd_seed_post(
		array(
			'post_title'    => $title,
			'post_excerpt'  => 'Tóm tắt ngắn gọn những điều cần biết về ' . mb_strtolower( $title ) . ' – được bác sĩ da liễu tham vấn.',
			'post_content'  => $qd_extra_body( mb_strtoupper( mb_substr( $title, 0, 1 ) ) . mb_substr( $title, 1 ) ),
			'post_category' => array( $qd_cats['cam-nang-lam-dep'] ),
			'tags_input'    => array( $tag ),
			'post_date'     => wp_date( 'Y-m-d H:i:s', strtotime( $when ) ),
		),
		$image ? array( '_qd_reviewer' => $qd_doctors[0] ?? 0 ) : array()
	);
	if ( $image ) {
		// Reuse the placeholder covers: copy the file under the new post's own key.
		$qd_src = __DIR__ . '/../demo-images/_placeholders/posts/' . $image . '.webp';
		if ( file_exists( $qd_src ) ) {
			$tmp = wp_tempnam( $image . '.webp' );
			copy( $qd_src, $tmp );
			$att = media_handle_sideload( array( 'name' => get_post_field( 'post_name', $id ) . '.webp', 'tmp_name' => $tmp ), $id );
			if ( ! is_wp_error( $att ) ) {
				set_post_thumbnail( $id, $att );
			}
		}
	}
}

// One more ended promotion and one running without a topic tag.
qd_seed_post(
	array(
		'post_title'    => 'Ưu đãi hè: giảm 30% liệu trình phục hồi da sau nắng',
		'post_excerpt'  => 'Chương trình dành cho khách hàng đặt lịch trong tháng 6 và tháng 7.',
		'post_content'  => '<p>Chương trình đã kết thúc. Vui lòng theo dõi các ưu đãi mới của phòng khám. (Nội dung mẫu.)</p>',
		'post_category' => array( $qd_cats['su-kien-uu-dai'] ),
		'tags_input'    => array( 'cham-soc-da' ),
		'post_date'     => wp_date( 'Y-m-d H:i:s', strtotime( '-95 days' ) ),
	),
	array(
		'_qd_promo_label' => 'Giảm 30%',
		'_qd_promo_start' => '2026-06-01',
		'_qd_promo_end'   => '2026-07-31',
	)
);
