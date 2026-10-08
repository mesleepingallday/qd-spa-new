<?php
/**
 * Course registration form — markup only.
 *
 * Follows the submission contract documented in template-parts/components/booking-strip.php:
 *   JS (data-booking-form): POST JSON to /wp-json/quangdang/v1/booking.
 *   No-JS fallback: POST to admin-post.php with action=qd_booking.
 * Fields: name, phone, note (prefilled "Đăng ký khóa học: {title}"), source ("course"),
 * website (honeypot, must stay empty).
 *
 * @package QuangDang
 *
 * @var array $args { note?: string, button?: string, class?: string }
 */

defined( 'ABSPATH' ) || exit;

$qd_uid  = wp_unique_id( 'reg-' );
$qd_note = $args['note'] ?? '';
?>
<form class="booking-form register-form <?php echo esc_attr( $args['class'] ?? '' ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-booking-form novalidate>
	<input type="hidden" name="action" value="qd_booking">
	<input type="hidden" name="source" value="course">
	<div class="sr-only" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

	<div class="field">
		<label for="<?php echo esc_attr( $qd_uid ); ?>-name">Họ và tên <span class="req">*</span></label>
		<input class="input" id="<?php echo esc_attr( $qd_uid ); ?>-name" name="name" type="text" autocomplete="name" required placeholder="VD: Nguyễn Thị Lan">
	</div>
	<div class="field">
		<label for="<?php echo esc_attr( $qd_uid ); ?>-phone">Số điện thoại <span class="req">*</span></label>
		<input class="input" id="<?php echo esc_attr( $qd_uid ); ?>-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" required placeholder="VD: 0912 345 678" pattern="^(0|\+84)[0-9\s.]{8,12}$">
	</div>
	<div class="field">
		<label for="<?php echo esc_attr( $qd_uid ); ?>-note">Ghi chú</label>
		<textarea class="input textarea" id="<?php echo esc_attr( $qd_uid ); ?>-note" name="note" rows="2"><?php echo esc_textarea( $qd_note ); ?></textarea>
	</div>
	<button class="btn btn--primary btn--block" type="submit"><?php qd_the_icon( 'graduation-cap', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $args['button'] ?? 'Đăng ký khóa học' ); ?></span></button>
	<p class="booking-strip__note">Thông tin của bạn chỉ dùng để liên hệ tư vấn khóa học.</p>
	<div class="booking-form__status" role="status" aria-live="polite"></div>
</form>
