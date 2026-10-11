<?php
/**
 * Home hero: what the clinic is, the next step, and a photograph. Copy first; the photograph follows
 * on phones. On desktop the filled "Đặt lịch" is here; on phones it lives in the sticky action bar, so
 * the phone hero offers the concern directory instead of a second booking button.
 *
 * The photograph is the real clinic logo wall (assets/images/hero/home-portrait.webp, 2:3). The frame
 * crops it per breakpoint (home.css), so object-position keeps the whole logo in view.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="hero" aria-labelledby="hero-title">
	<div class="container hero__inner">
		<div class="hero__text">
			<h1 class="display hero__title" id="hero-title">Làn da khỏe đẹp, điều trị chuẩn y khoa</h1>
			<p class="hero__lead">Bác sĩ da liễu trực tiếp thăm khám và lên phác đồ riêng cho từng làn da. Ngay tại Quỳnh Lưu, Nghệ An.</p>
			<div class="hero__actions">
				<?php
				echo qd_button( 'Đặt lịch khám', qd_booking_url(), array( 'size' => 'lg', 'class' => 'hero__book' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo qd_button( 'Chọn vấn đề của bạn', '#concerns', array( 'size' => 'lg', 'variant' => 'outline', 'icon_end' => 'arrow-right', 'class' => 'hero__pick' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
				<a class="link-more hero__guide" href="<?php echo esc_url( home_url( '/tim-lieu-trinh/' ) ); ?>">Tìm dịch vụ phù hợp<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
			</div>
		</div>

		<div class="hero__media">
			<div class="hero__photo frame frame--4-5">
				<?php echo qd_asset_img( 'hero/home-portrait', 'Logo Qbe+ Quang Đăng, Viện thẩm mỹ quốc tế, trên tường phòng khám', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width: 1024px) 520px, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
</section>
