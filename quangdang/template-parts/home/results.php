<?php
/**
 * Before/after results. MUST be real, consented photos (theme assets results/{n}-truoc, results/{n}-sau).
 * In production the section only renders cases whose photos are real (no placeholders),
 * so it never ships stand-in images. Edit the cases with the `qd_home_results` filter.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_cases = apply_filters(
	'qd_home_results',
	array(
		array( 1, 'Trị mụn kháng khuẩn đa tầng', 'Sau 6 buổi', home_url( '/dich-vu/dieu-tri-mun/tri-mun-khang-khuan-da-tang/' ) ),
		array( 2, 'Điều trị nám chuyên sâu', 'Sau 8 buổi', home_url( '/dich-vu/dieu-tri-nam/dieu-tri-nam-chuyen-sau/' ) ),
		array( 3, 'Trị thâm nách', 'Sau 5 buổi', home_url( '/dich-vu/dieu-tri-tham/tri-tham-nach/' ) ),
	)
);

$qd_cases = array_filter(
	$qd_cases,
	function ( $c ) {
		foreach ( array( 'truoc', 'sau' ) as $side ) {
			$img = qd_asset_image( 'results/' . $c[0] . '-' . $side );
			if ( ! $img || ( $img['placeholder'] && ! qd_show_placeholders() ) ) {
				return false;
			}
		}
		return true;
	}
);
if ( ! $qd_cases ) {
	return;
}
?>
<section class="section" aria-labelledby="home-results">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'    => 'home-results',
				'title' => 'Kết quả thật từ khách hàng',
				'desc'  => 'Hình ảnh được khách hàng đồng ý chia sẻ, không chỉnh sửa. Kết quả có thể khác nhau tùy cơ địa.',
			)
		);
		?>
		<div class="grid grid--3 scroller">
			<?php foreach ( $qd_cases as list( $qd_n, $qd_service, $qd_after, $qd_url ) ) : ?>
				<figure class="card result-card">
					<div class="result-card__pair">
						<div class="frame frame--1-1">
							<?php echo qd_asset_img( 'results/' . $qd_n . '-truoc', 'Trước điều trị: ' . $qd_service ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="result-card__tag">Trước</span>
						</div>
						<div class="frame frame--1-1">
							<?php echo qd_asset_img( 'results/' . $qd_n . '-sau', 'Sau điều trị: ' . $qd_service ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="result-card__tag result-card__tag--after"><?php echo esc_html( $qd_after ); ?></span>
						</div>
					</div>
					<figcaption class="card__body">
						<a class="card__title result-card__link" href="<?php echo esc_url( $qd_url ); ?>"><?php echo esc_html( $qd_service ); ?></a>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
