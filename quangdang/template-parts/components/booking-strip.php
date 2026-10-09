<?php
/**
 * Short consultation form (name + phone + concern) with clinic contacts.
 * Used at the bottom of home, service and category pages.
 *
 * Submission contract (implemented in inc/booking.php):
 *   JS: POST JSON to /wp-json/quangdang/v1/booking → { ok, message } | { ok:false, errors:{field:msg} }
 *   No-JS fallback: POST to admin-post.php?action=qd_booking, redirect to /dat-lich/?da-gui=1
 * Fields: name, phone, service (dich-vu slug or ""), note, source, website (honeypot, must stay empty).
 *
 * @package QuangDang
 *
 * @var array $args { title?, desc?, service?: WP_Post|int, source?: string }
 */

defined( 'ABSPATH' ) || exit;

$qd_service = ! empty( $args['service'] ) ? get_post( $args['service'] ) : null;
$qd_source  = $args['source'] ?? 'strip';
$qd_uid     = wp_unique_id( 'bk-' );
?>
<div class="booking-strip" data-hide-actionbar>
	<div>
		<h2 class="section-title"><?php echo esc_html( $args['title'] ?? 'Đặt lịch tư vấn với bác sĩ' ); ?></h2>
		<p class="section-lead"><?php echo esc_html( $args['desc'] ?? 'Để lại số điện thoại, phòng khám gọi lại trong 15 phút (giờ làm việc). Tư vấn miễn phí, không ràng buộc.' ); ?></p>
		<ul class="booking-strip__contacts">
			<li><?php qd_the_icon( 'map-pin', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'address' ) ); ?> · <a href="<?php echo esc_url( qd_clinic( 'map_url' ) ); ?>" target="_blank" rel="noopener">Chỉ đường</a></span></li>
			<li><?php qd_the_icon( 'clock', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'hours' ) ); ?></span></li>
			<li><?php qd_the_icon( 'phone', array( 'size' => 18 ) ); ?><span>Hotline <a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a> · <a href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener">Chat Zalo</a></span></li>
		</ul>
	</div>

	<form class="booking-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-booking-form novalidate>
		<input type="hidden" name="action" value="qd_booking">
		<input type="hidden" name="source" value="<?php echo esc_attr( $qd_source ); ?>">
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
			<label for="<?php echo esc_attr( $qd_uid ); ?>-service">Bạn quan tâm</label>
			<select class="select" id="<?php echo esc_attr( $qd_uid ); ?>-service" name="service">
				<option value="">Chưa rõ – cần bác sĩ tư vấn</option>
				<?php foreach ( qd_service_categories() as $qd_cat ) : ?>
					<optgroup label="<?php echo esc_attr( $qd_cat->post_title ); ?>">
						<?php foreach ( qd_service_children( $qd_cat->ID ) as $qd_s ) : ?>
							<option value="<?php echo esc_attr( $qd_s->post_name ); ?>" <?php selected( $qd_service && $qd_service->ID === $qd_s->ID ); ?>><?php echo esc_html( $qd_s->post_title ); ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		</div>
		<button class="btn btn--primary btn--block" type="submit"><?php qd_the_icon( 'calendar-check', array( 'size' => 18 ) ); ?><span>Gửi yêu cầu tư vấn</span></button>
		<p class="booking-strip__note">Thông tin của bạn chỉ dùng để liên hệ đặt lịch.</p>
		<div class="booking-form__status" role="status" aria-live="polite"></div>
	</form>
</div>
