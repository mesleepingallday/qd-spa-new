<?php
/**
 * Home hero. The concern chips are the first step of onboarding: one tap from
 * "I have acne" to the acne page; undecided visitors get the quiz.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_doctor = qd_service_doctor();
?>
<section class="hero">
	<div class="container hero__inner">
		<div class="hero__text">
			<p class="hero__kicker"><?php qd_the_icon( 'badge-check', array( 'size' => 18 ) ); ?>Phòng khám chuyên khoa Da liễu – Thẩm mỹ</p>
			<h1 class="display hero__title">Làn da khỏe đẹp, <em>điều trị chuẩn y khoa</em></h1>
			<p class="hero__lead">Bác sĩ da liễu trực tiếp thăm khám và lên phác đồ riêng cho từng làn da. Ngay tại Quỳnh Lưu, Nghệ An.</p>
			<div class="btn-row hero__ctas">
				<?php
				echo qd_button( 'Đặt lịch khám', qd_booking_url(), array( 'size' => 'lg', 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo qd_button( 'Kiểm tra da 1 phút', home_url( '/tim-lieu-trinh/' ), array( 'size' => 'lg', 'variant' => 'outline', 'icon' => 'scan-face' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</div>
			<div class="hero__concerns">
				<p class="hero__concerns-label" id="hero-concerns">Bạn đang quan tâm vấn đề gì?</p>
				<ul class="chips" aria-labelledby="hero-concerns">
					<?php foreach ( qd_concerns() as $qd_concern ) : ?>
						<li><a class="chip" href="<?php echo esc_url( $qd_concern['url'] ); ?>"><?php echo esc_html( $qd_concern['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="hero__media">
			<div class="hero__photo frame frame--4-5">
				<?php echo qd_asset_img( 'hero/home-portrait', 'Khách hàng với làn da khỏe tại Phòng khám Da liễu Thẩm mỹ Quang Đăng', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width: 1024px) 520px, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<?php if ( $qd_doctor ) : ?>
				<a class="hero__doctor" href="<?php echo esc_url( home_url( '/gioi-thieu/doi-ngu-bac-si/' ) ); ?>">
					<span class="hero__doctor-photo"><?php echo get_the_post_thumbnail( $qd_doctor, 'thumbnail', array( 'alt' => '' ) ); ?></span>
					<span>
						<span class="hero__doctor-label">Bác sĩ trực tiếp thăm khám</span>
						<span class="hero__doctor-name"><?php echo esc_html( get_the_title( $qd_doctor ) ); ?></span>
					</span>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="container">
		<ul class="trust">
			<li><span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'stethoscope', array( 'size' => 20 ) ); ?></span><span><strong>Bác sĩ da liễu</strong> thăm khám trước mọi liệu trình</span></li>
			<li><span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'shield-check', array( 'size' => 20 ) ); ?></span><span><strong>Được cấp phép</strong> hoạt động khám chữa bệnh</span></li>
			<li><span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'badge-check', array( 'size' => 20 ) ); ?></span><span><strong>Thiết bị, sản phẩm</strong> chính hãng, rõ nguồn gốc</span></li>
			<li><span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'banknote', array( 'size' => 20 ) ); ?></span><span><strong>Báo giá rõ ràng</strong> trước khi điều trị</span></li>
		</ul>
	</div>
</section>
