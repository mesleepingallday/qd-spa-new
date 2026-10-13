<?php
/**
 * Demo "Kết quả khách hàng" cases for the home carousel (poster = dev/demo-images/results/{n}).
 * On the live site staff add these in wp-admin, with real photos and written consent.
 *
 * @package QuangDang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$qd_result_cases = array(
	array( 'Làm sạch sâu Aquapeel (mẫu 1)', 'phuc-hoi-da', 'Sau 60 phút làm với Aquapeel', "Loại bỏ bít tắc\nSạch dầu thừa\nDa căng mịn" ),
	array( 'Làm sạch sâu Aquapeel (mẫu 2)', 'phuc-hoi-da', 'Sau 60 phút làm với Aquapeel', "Loại bỏ bít tắc\nSạch dầu thừa\nDa căng mịn" ),
	array( 'Kiểm soát mụn: má', 'tri-mun-khang-khuan-da-tang', 'Liệu trình kiểm soát mụn', "Giảm đến 80% mụn viêm và mụn mủ\nDa sáng và đều màu hơn\nDa có độ căng bóng tự nhiên, ít bóng dầu" ),
	array( 'Kiểm soát mụn: trán', 'tri-mun-khang-khuan-da-tang', 'Liệu trình kiểm soát mụn', "Mụn viêm, sưng giảm rõ rệt\nDa sáng và đều màu hơn\nLỗ chân lông thông thoáng, giảm tiết dầu" ),
);

foreach ( $qd_result_cases as $qd_i => list( $qd_title, $qd_service, $qd_caption, $qd_points ) ) {
	$qd_service_post = get_page_by_path( $qd_service, OBJECT, 'dich-vu' );
	if ( ! $qd_service_post ) {
		$qd_found        = get_posts(
			array(
				'post_type'      => 'dich-vu',
				'name'           => $qd_service,
				'posts_per_page' => 1,
			)
		);
		$qd_service_post = $qd_found ? $qd_found[0] : null;
	}
	$qd_id = qd_seed_post(
		array(
			'post_type'  => 'ket-qua',
			'post_title' => $qd_title,
			'menu_order' => $qd_i,
		),
		array(
			'_qd_result_service' => $qd_service_post ? $qd_service_post->ID : 0,
			'_qd_result_caption' => $qd_caption,
			'_qd_result_points'  => $qd_points,
			'_qd_result_consent' => 1,
		)
	);
	qd_seed_image( 'results/' . ( $qd_i + 1 ), $qd_id );
}
