<?php
/**
 * Space: the clinic itself as a bento mosaic of real photos (one tall tile + two squares).
 * Hides unless three photos are available, so production never shows placeholders.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

// [asset key, alt] in priority order.
$qd_candidates = apply_filters(
	'qd_home_space',
	array(
		array( 'about/sanh-cho', 'Sảnh chờ với ghế sofa xanh' ),
		array( 'about/phong-dieu-tri', 'Nhân viên chăm sóc da cho khách tại phòng điều trị' ),
		array( 'about/khu-don-tiep', 'Khu đón tiếp với logo Qbe Quang Đăng' ),
		array( 'about/phong-cham-soc-da', 'Phòng chăm sóc da với giường có rèm trắng riêng cho từng giường' ),
		array( 'about/khu-triet-long', 'Khu triệt lông với máy và giường điều trị' ),
		array( 'about/chi-tiet-khan', 'Khăn thêu tên phòng khám xếp gọn, dùng riêng cho từng khách' ),
	)
);

$qd_tiles = array();
foreach ( $qd_candidates as list( $qd_key, $qd_alt ) ) {
	$qd_ratio = $qd_tiles ? '1-1' : '4-5';
	$qd_html  = qd_about_photo( $qd_key, $qd_alt, $qd_ratio, array_merge( qd_about_photo_attrs( $qd_key ), array( 'loading' => 'lazy' ) ) );
	if ( $qd_html ) {
		$qd_tiles[] = $qd_html;
	}
	if ( 3 === count( $qd_tiles ) ) {
		break;
	}
}
if ( count( $qd_tiles ) < 3 ) {
	return;
}
?>
<section class="section section--mist" aria-labelledby="home-space">
	<div class="container space">
		<div class="space__text">
			<h2 class="section-title" id="home-space">Không gian phòng khám</h2>
			<p class="lead">Sạch sẽ, riêng tư, đúng chuẩn y khoa.</p>
			<ul class="checklist">
				<?php foreach ( array( 'Rèm riêng từng giường', 'Phòng laser tách biệt', 'Khăn riêng cho từng khách' ) as $qd_line ) : ?>
					<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
				<?php endforeach; ?>
			</ul>
			<a class="link-more" href="<?php echo esc_url( qd_about_url( 'co-so-vat-chat' ) ); ?>">Xem cơ sở vật chất<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
		</div>
		<div class="space__mosaic">
			<div class="space__big"><?php echo $qd_tiles[0]; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php echo $qd_tiles[1] . $qd_tiles[2]; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</section>
