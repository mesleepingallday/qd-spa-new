<?php
/**
 * Booking page (/dat-lich/): three steps on one page, summary card, confirmation screen.
 *
 * Steps are plain radio inputs + text fields inside one <form>, so it works without JS
 * (posts to admin-post.php → redirect ?da-gui=1). assets/js/booking.js adds step collapsing,
 * the live summary, REST submit and the confirmation screen.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only query args.
$qd_sent = isset( $_GET['da-gui'] );

// Values to show: errors + earlier input from a failed no-JS post, else ?dich-vu=slug.
$qd_old    = array();
$qd_errors = array();
if ( ! empty( $_GET['loi'] ) ) {
	$qd_saved = get_transient( 'qd_bk_err_' . sanitize_key( wp_unslash( $_GET['loi'] ) ) );
	if ( is_array( $qd_saved ) ) {
		$qd_old    = $qd_saved['values'];
		$qd_errors = $qd_saved['errors'];
	}
}
$qd_pick = $qd_old['service'] ?? ( isset( $_GET['dich-vu'] ) ? sanitize_title( wp_unslash( $_GET['dich-vu'] ) ) : '' );
$qd_has_pick = isset( $_GET['dich-vu'] ) || isset( $qd_old['service'] );
// phpcs:enable

$qd_now    = current_datetime();
$qd_slots  = qd_booking_slots();
$qd_wd     = array( 'CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7' );
$qd_days   = array();
for ( $qd_i = 0; $qd_i < 14; $qd_i++ ) {
	$qd_d      = $qd_now->modify( '+' . $qd_i . ' days' );
	$qd_days[] = array(
		'value' => $qd_d->format( 'Y-m-d' ),
		'top'   => 0 === $qd_i ? 'Hôm nay' : ( 1 === $qd_i ? 'Ngày mai' : $qd_wd[ (int) $qd_d->format( 'w' ) ] ),
		'num'   => $qd_d->format( 'd/m' ),
	);
}
$qd_all_passed = true;
foreach ( array_keys( $qd_slots ) as $qd_k ) {
	$qd_all_passed = $qd_all_passed && qd_booking_slot_passed( $qd_k );
}

$qd_err = function ( $key ) use ( $qd_errors ) {
	return isset( $qd_errors[ $key ] ) ? $qd_errors[ $key ] : '';
};

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<script>document.documentElement.className += ' qd-js';</script>

	<section class="page-hero">
		<div class="container">
			<?php qd_breadcrumbs(); ?>
			<h1 class="page-title">Đặt lịch khám</h1>
			<p class="lead">Chọn dịch vụ, ngày giờ và để lại số điện thoại. Phòng khám gọi xác nhận trong 15 phút (giờ làm việc), tư vấn miễn phí.</p>
		</div>
	</section>

	<section class="section section--tight bk-section" aria-label="Đặt lịch">
		<div class="container">

			<?php // Confirmation: filled and shown by booking.js after a successful submit, or shown as-is after a no-JS post. ?>
			<div class="bk-done" data-booking-done<?php echo $qd_sent ? '' : ' hidden'; ?> tabindex="-1">
				<span class="bk-done__check"><?php qd_the_icon( 'check', array( 'size' => 40 ) ); ?></span>
				<h2 class="bk-done__title">Đã nhận lịch hẹn</h2>
				<p class="bk-done__lead">Phòng khám sẽ gọi xác nhận trong 15 phút (giờ làm việc).</p>
				<dl class="bk-done__summary" data-done-summary hidden></dl>
				<div class="bk-done__actions">
					<?php
					echo qd_button( 'Chỉ đường', qd_clinic( 'map_url' ), array( 'icon' => 'navigation', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo qd_button( 'Nhắn Zalo', qd_clinic( 'zalo' ), array( 'variant' => 'outline', 'icon' => 'message-circle', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
					<button class="btn btn--outline" type="button" data-add-calendar hidden><?php qd_the_icon( 'calendar-check', array( 'size' => 18 ) ); ?><span>Thêm vào lịch</span></button>
				</div>
				<p class="bk-done__where"><?php qd_the_icon( 'map-pin', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'address' ) ); ?></span></p>
				<p class="bk-done__home"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Về trang chủ</a></p>
			</div>

			<?php if ( ! $qd_sent ) : ?>
				<form class="bk" id="dat-lich-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-booking-form data-booking-page data-today="<?php echo esc_attr( $qd_now->format( 'Y-m-d' ) ); ?>" novalidate>
					<input type="hidden" name="action" value="qd_booking">
					<input type="hidden" name="source" value="dat-lich">
					<div class="sr-only" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

					<?php if ( $qd_err( 'form' ) ) : ?>
						<div class="notice notice--error bk__alert" role="alert"><?php qd_the_icon( 'triangle-alert', array( 'size' => 20 ) ); ?><p><?php echo esc_html( $qd_err( 'form' ) ); ?></p></div>
					<?php endif; ?>

					<div class="bk__layout">
						<div class="bk__steps">

							<?php // 1. Service ?>
							<section class="bk-step" data-step="service" aria-labelledby="bk-t1">
								<header class="bk-step__head">
									<span class="bk-step__num" aria-hidden="true">1</span>
									<h2 class="bk-step__title" id="bk-t1">Bạn muốn làm dịch vụ nào?</h2>
									<span class="bk-step__value" data-step-value></span>
									<button class="bk-step__edit" type="button" data-step-edit hidden>Đổi</button>
								</header>
								<div class="bk-step__body" data-step-body>
									<p class="field__error" data-error-for="service" role="alert" hidden></p>
									<div class="bk-options" role="radiogroup" aria-labelledby="bk-t1">
										<label class="bk-opt bk-opt--wide">
											<input type="radio" name="service" value="" data-label="Chưa biết – cần bác sĩ tư vấn"<?php checked( $qd_has_pick && '' === $qd_pick ); ?>>
											<span class="bk-opt__box">
												<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'stethoscope', array( 'size' => 20 ) ); ?></span>
												<span class="bk-opt__text"><strong>Chưa biết – cần bác sĩ tư vấn</strong><small>Bác sĩ sẽ soi da và gợi ý liệu trình phù hợp khi bạn đến.</small></span>
											</span>
										</label>
									</div>
									<?php foreach ( qd_service_categories() as $qd_cat ) : ?>
										<?php $qd_children = qd_service_children( $qd_cat->ID ); ?>
										<?php if ( ! $qd_children ) { continue; } ?>
										<div class="bk-group" role="radiogroup" aria-label="<?php echo esc_attr( $qd_cat->post_title ); ?>">
											<p class="bk-group__title"><?php echo esc_html( $qd_cat->post_title ); ?></p>
											<div class="bk-options bk-options--chips">
												<?php foreach ( $qd_children as $qd_s ) : ?>
													<label class="bk-opt">
														<input type="radio" name="service" value="<?php echo esc_attr( $qd_s->post_name ); ?>" data-label="<?php echo esc_attr( $qd_s->post_title ); ?>"<?php checked( $qd_pick, $qd_s->post_name ); ?>>
														<span class="bk-opt__box"><?php echo esc_html( $qd_s->post_title ); ?></span>
													</label>
												<?php endforeach; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</section>

							<?php // 2. Date and time ?>
							<section class="bk-step" data-step="when" aria-labelledby="bk-t2">
								<header class="bk-step__head">
									<span class="bk-step__num" aria-hidden="true">2</span>
									<h2 class="bk-step__title" id="bk-t2">Bạn muốn đến vào lúc nào?</h2>
									<span class="bk-step__value" data-step-value></span>
									<button class="bk-step__edit" type="button" data-step-edit hidden>Đổi</button>
								</header>
								<div class="bk-step__body" data-step-body>
									<p class="bk-label" id="bk-day-label">Chọn ngày</p>
									<p class="field__error" data-error-for="date" role="alert"<?php echo $qd_err( 'date' ) ? '' : ' hidden'; ?>><?php echo esc_html( $qd_err( 'date' ) ); ?></p>
									<div class="bk-days" role="radiogroup" aria-labelledby="bk-day-label">
										<?php foreach ( $qd_days as $qd_n => $qd_day ) : ?>
											<label class="bk-opt bk-day">
												<input type="radio" name="date" value="<?php echo esc_attr( $qd_day['value'] ); ?>" data-label="<?php echo esc_attr( $qd_day['top'] . ' ' . $qd_day['num'] ); ?>"<?php checked( $qd_old['date'] ?? '', $qd_day['value'] ); ?><?php echo ( 0 === $qd_n && $qd_all_passed ) ? ' disabled' : ''; ?>>
												<span class="bk-opt__box"><span class="bk-day__top"><?php echo esc_html( $qd_day['top'] ); ?></span><span class="bk-day__num"><?php echo esc_html( $qd_day['num'] ); ?></span></span>
											</label>
										<?php endforeach; ?>
									</div>

									<p class="bk-label" id="bk-slot-label">Chọn buổi</p>
									<p class="field__error" data-error-for="slot" role="alert"<?php echo $qd_err( 'slot' ) ? '' : ' hidden'; ?>><?php echo esc_html( $qd_err( 'slot' ) ); ?></p>
									<div class="bk-slots" role="radiogroup" aria-labelledby="bk-slot-label">
										<?php foreach ( $qd_slots as $qd_key => $qd_slot ) : ?>
											<label class="bk-opt bk-slot">
												<input type="radio" name="slot" value="<?php echo esc_attr( $qd_key ); ?>" data-label="<?php echo esc_attr( $qd_slot['label'] . ' ' . $qd_slot['range'] ); ?>"<?php checked( $qd_old['slot'] ?? '', $qd_key ); ?><?php echo qd_booking_slot_passed( $qd_key ) ? ' data-passed="1"' : ''; ?>>
												<span class="bk-opt__box"><strong><?php echo esc_html( $qd_slot['label'] ); ?></strong><small><?php echo esc_html( $qd_slot['range'] ); ?></small></span>
											</label>
										<?php endforeach; ?>
									</div>
									<p class="field__hint">Giờ chỉ là giờ dự kiến. Phòng khám sẽ gọi để chốt giờ chính xác với bạn.</p>
								</div>
							</section>

							<?php // 3. Contact details ?>
							<section class="bk-step" data-step="info" aria-labelledby="bk-t3">
								<header class="bk-step__head">
									<span class="bk-step__num" aria-hidden="true">3</span>
									<h2 class="bk-step__title" id="bk-t3">Thông tin của bạn</h2>
								</header>
								<div class="bk-step__body bk-fields">
									<div class="field">
										<label for="bk-name">Họ và tên <span class="req">*</span></label>
										<input class="input" id="bk-name" name="name" type="text" autocomplete="name" required placeholder="VD: Nguyễn Thị Lan" value="<?php echo esc_attr( $qd_old['name'] ?? '' ); ?>"<?php echo $qd_err( 'name' ) ? ' aria-invalid="true" aria-describedby="bk-name-error"' : ''; ?>>
										<?php if ( $qd_err( 'name' ) ) : ?><p class="field__error" id="bk-name-error"><?php echo esc_html( $qd_err( 'name' ) ); ?></p><?php endif; ?>
									</div>
									<div class="field">
										<label for="bk-phone">Số điện thoại <span class="req">*</span></label>
										<input class="input" id="bk-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" required placeholder="VD: 0912 345 678" value="<?php echo esc_attr( $qd_old['phone'] ?? '' ); ?>"<?php echo $qd_err( 'phone' ) ? ' aria-invalid="true" aria-describedby="bk-phone-error"' : ''; ?>>
										<?php if ( $qd_err( 'phone' ) ) : ?><p class="field__error" id="bk-phone-error"><?php echo esc_html( $qd_err( 'phone' ) ); ?></p><?php endif; ?>
									</div>
									<div class="field">
										<label for="bk-note">Ghi chú <span class="field__hint">(không bắt buộc)</span></label>
										<textarea class="textarea" id="bk-note" name="note" rows="3" maxlength="600" placeholder="VD: Da mình hay nhạy cảm, mình muốn được nữ bác sĩ tư vấn."><?php echo esc_textarea( $qd_old['note'] ?? '' ); ?></textarea>
									</div>
								</div>
							</section>
						</div>

						<aside class="bk-summary" aria-labelledby="bk-sum-title">
							<h2 class="bk-summary__title" id="bk-sum-title">Lịch hẹn của bạn</h2>
							<dl class="bk-summary__list">
								<div><dt>Dịch vụ</dt><dd data-sum="service" data-empty="Chưa chọn">Chưa chọn</dd></div>
								<div><dt>Ngày</dt><dd data-sum="date" data-empty="Chưa chọn">Chưa chọn</dd></div>
								<div><dt>Giờ</dt><dd data-sum="slot" data-empty="Chưa chọn">Chưa chọn</dd></div>
							</dl>
							<ul class="bk-summary__clinic">
								<li><?php qd_the_icon( 'map-pin', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'address' ) ); ?></span></li>
								<li><?php qd_the_icon( 'clock', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'hours' ) ); ?></span></li>
							</ul>
							<button class="btn btn--primary btn--lg btn--block" type="submit"><?php qd_the_icon( 'calendar-check', array( 'size' => 20 ) ); ?><span>Đặt lịch hẹn</span></button>
							<div class="booking-form__status" role="status" aria-live="polite"></div>
							<p class="bk-summary__note">Thông tin chỉ dùng để liên hệ đặt lịch. Hoặc gọi <a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a>.</p>
						</aside>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
