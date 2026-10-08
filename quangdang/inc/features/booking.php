<?php
/**
 * Booking: REST endpoint + no-JS fallback, admin inbox for `lich-hen`, scripts and page styles.
 *
 * Serves the /dat-lich/ page and every `form[data-booking-form]` (the booking strip).
 *
 *   JS     POST /wp-json/quangdang/v1/booking  (JSON)  → { ok:true, message } | { ok:false, errors:{field:msg} }
 *   No-JS  POST admin-post.php?action=qd_booking       → redirect /dat-lich/?da-gui=1  (or ?loi=TOKEN on errors)
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 * Shared data
 * ------------------------------------------------------------------------ */

/**
 * Time slots. `end` is the closing time (HH:MM, 24h) used to hide slots that already passed today.
 *
 * @return array<string,array{label:string,range:string,start:string,end:string}>
 */
function qd_booking_slots() {
	return array(
		'sang'  => array(
			'label' => 'Sáng',
			'range' => '8:00–11:30',
			'start' => '08:00',
			'end'   => '11:30',
		),
		'chieu' => array(
			'label' => 'Chiều',
			'range' => '13:30–17:00',
			'start' => '13:30',
			'end'   => '17:00',
		),
		'toi'   => array(
			'label' => 'Tối',
			'range' => '17:00–20:00',
			'start' => '17:00',
			'end'   => '20:00',
		),
	);
}

/**
 * Lifecycle of a booking request, as the clinic staff track it.
 *
 * @return array<string,string>
 */
function qd_booking_statuses() {
	return array(
		'moi'     => 'Mới',
		'da-goi'  => 'Đã gọi',
		'da-den'  => 'Đã đến',
		'huy'     => 'Hủy',
	);
}

/**
 * Minutes since midnight for "HH:MM".
 *
 * @param string $hhmm Time.
 */
function qd_booking_minutes( $hhmm ) {
	list( $h, $m ) = array_map( 'intval', explode( ':', $hhmm ) );
	return $h * 60 + $m;
}

/**
 * Has this slot already ended today (site timezone)?
 *
 * @param string $slot Slot key.
 */
function qd_booking_slot_passed( $slot ) {
	$slots = qd_booking_slots();
	if ( ! isset( $slots[ $slot ] ) ) {
		return false;
	}
	$now = current_datetime();
	return ( (int) $now->format( 'G' ) * 60 + (int) $now->format( 'i' ) ) >= qd_booking_minutes( $slots[ $slot ]['end'] );
}

/**
 * Normalise a Vietnamese phone number to a local form (0xxxxxxxxx) or return ''.
 * Accepts spaces, dots, dashes, +84 / 84 prefixes. Mobile = 10 digits (03,05,07,08,09); landline = 02x + 9 digits.
 *
 * @param string $raw Raw input.
 */
function qd_booking_normalize_phone( $raw ) {
	$digits = preg_replace( '/[^0-9+]/', '', (string) $raw );
	$digits = preg_replace( '/^\+?84/', '0', $digits );
	$digits = preg_replace( '/[^0-9]/', '', $digits );
	if ( preg_match( '/^0[35789][0-9]{8}$/', $digits ) || preg_match( '/^02[0-9]{9}$/', $digits ) ) {
		return $digits;
	}
	return '';
}

/**
 * "0912345678" → "0912 345 678" for display.
 *
 * @param string $phone Normalised phone.
 */
function qd_booking_format_phone( $phone ) {
	return 10 === strlen( $phone ) ? substr( $phone, 0, 4 ) . ' ' . substr( $phone, 4, 3 ) . ' ' . substr( $phone, 7 ) : $phone;
}

/* ---------------------------------------------------------------------------
 * Core: validate + save + notify (used by REST and admin-post)
 * ------------------------------------------------------------------------ */

/**
 * Validate and store a booking request.
 *
 * @param array $in Raw input (name, phone, service, date, slot, note, source, website).
 * @return array{ok:bool,message?:string,errors?:array<string,string>,id?:int}
 */
function qd_booking_submit( $in ) {
	// Honeypot: bots fill the hidden field. Pretend it worked, store nothing.
	if ( ! empty( $in['website'] ) ) {
		return array(
			'ok'      => true,
			'message' => 'Đã nhận lịch hẹn.',
		);
	}

	$errors = array();
	$name   = trim( sanitize_text_field( (string) ( $in['name'] ?? '' ) ) );
	$phone  = qd_booking_normalize_phone( $in['phone'] ?? '' );
	$note   = trim( sanitize_textarea_field( (string) ( $in['note'] ?? '' ) ) );
	$source = sanitize_key( (string) ( $in['source'] ?? '' ) );
	$slot   = sanitize_key( (string) ( $in['slot'] ?? '' ) );
	$date   = trim( (string) ( $in['date'] ?? '' ) );
	$slug   = sanitize_title( (string) ( $in['service'] ?? '' ) );

	if ( '' === $name || mb_strlen( $name ) < 2 ) {
		$errors['name'] = 'Bạn vui lòng cho phòng khám biết họ tên để tiện xưng hô.';
	} elseif ( mb_strlen( $name ) > 80 ) {
		$errors['name'] = 'Họ tên hơi dài, bạn rút gọn giúp phòng khám nhé.';
	}

	if ( '' === trim( (string) ( $in['phone'] ?? '' ) ) ) {
		$errors['phone'] = 'Bạn vui lòng nhập số điện thoại để phòng khám gọi xác nhận.';
	} elseif ( '' === $phone ) {
		$errors['phone'] = 'Số điện thoại chưa đúng. Bạn nhập 10 số, ví dụ 0912 345 678.';
	}

	// Service: must be a real dich-vu slug, otherwise treated as "chưa rõ".
	$service_title = '';
	if ( '' !== $slug ) {
		$found = get_posts(
			array(
				'post_type'      => 'dich-vu',
				'name'           => $slug,
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		if ( $found ) {
			$service_title = $found[0]->post_title;
		} else {
			$slug = '';
		}
	}

	// Date: optional, but when given it must be today … +30 days.
	if ( '' !== $date ) {
		$tz  = wp_timezone();
		$day = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, $tz );
		if ( ! $day || $day->format( 'Y-m-d' ) !== $date ) {
			$errors['date'] = 'Ngày hẹn chưa hợp lệ, bạn chọn lại giúp phòng khám nhé.';
		} else {
			$today = current_datetime()->setTime( 0, 0 );
			$diff  = (int) $today->diff( $day )->format( '%r%a' );
			if ( $diff < 0 || $diff > 30 ) {
				$errors['date'] = 'Phòng khám nhận lịch trong 30 ngày tới, bạn chọn lại ngày nhé.';
			}
		}
	}

	$slots = qd_booking_slots();
	if ( '' !== $slot && ! isset( $slots[ $slot ] ) ) {
		$errors['slot'] = 'Buổi hẹn chưa hợp lệ, bạn chọn lại giúp phòng khám nhé.';
	}
	if ( '' !== $slot && '' !== $date && empty( $errors['date'] ) && empty( $errors['slot'] ) && current_datetime()->format( 'Y-m-d' ) === $date && qd_booking_slot_passed( $slot ) ) {
		$errors['slot'] = 'Buổi này đã qua, bạn chọn buổi khác hoặc ngày khác nhé.';
	}

	if ( mb_strlen( $note ) > 600 ) {
		$note = mb_substr( $note, 0, 600 );
	}

	if ( $errors ) {
		return array(
			'ok'     => false,
			'errors' => $errors,
		);
	}

	// Rate limit: 5 accepted requests per IP per 10 minutes.
	$ip_key = 'qd_bk_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 5 ) {
		return array(
			'ok'     => false,
			'errors' => array( 'form' => 'Bạn đã gửi nhiều lần liên tiếp. Vui lòng đợi ít phút rồi thử lại, hoặc gọi hotline ' . qd_clinic( 'hotline' ) . '.' ),
		);
	}
	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$title = $name . ' – ' . qd_booking_format_phone( $phone ) . ' – ' . ( $service_title ? $service_title : 'Chưa rõ dịch vụ' );
	$id    = wp_insert_post(
		array(
			'post_type'   => 'lich-hen',
			'post_status' => 'private',
			'post_title'  => $title,
			'meta_input'  => array(
				'_qd_phone'   => $phone,
				'_qd_service' => $slug,
				'_qd_date'    => $date,
				'_qd_slot'    => $slot,
				'_qd_note'    => $note,
				'_qd_source'  => $source ? $source : 'web',
				'_qd_status'  => 'moi',
			),
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return array(
			'ok'     => false,
			'errors' => array( 'form' => 'Rất tiếc, hệ thống chưa lưu được lịch hẹn. Bạn vui lòng gọi hotline ' . qd_clinic( 'hotline' ) . ' giúp phòng khám nhé.' ),
		);
	}

	qd_booking_notify( (int) $id, $name, $service_title );

	return array(
		'ok'      => true,
		'id'      => (int) $id,
		'message' => 'Đã nhận lịch hẹn. Phòng khám sẽ gọi xác nhận trong 15 phút (giờ làm việc).',
	);
}

/**
 * Email the clinic about a new booking.
 *
 * @param int    $id      lich-hen post ID.
 * @param string $name    Customer name.
 * @param string $service Service title ('' when undecided).
 */
function qd_booking_notify( $id, $name, $service ) {
	$to = qd_clinic( 'email' );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );
	if ( ! $to ) {
		return;
	}
	$meta  = fn( $k ) => (string) get_post_meta( $id, '_qd_' . $k, true );
	$slots = qd_booking_slots();
	$slot  = $slots[ $meta( 'slot' ) ] ?? null;
	$lines = array(
		'Có lịch hẹn mới từ website:',
		'',
		'Họ tên: ' . $name,
		'Điện thoại: ' . qd_booking_format_phone( $meta( 'phone' ) ),
		'Dịch vụ: ' . ( $service ? $service : 'Chưa rõ – cần bác sĩ tư vấn' ),
		'Ngày hẹn: ' . ( $meta( 'date' ) ? wp_date( 'd/m/Y', strtotime( $meta( 'date' ) . ' 12:00:00' ) ) : 'Chưa chọn' ),
		'Buổi: ' . ( $slot ? $slot['label'] . ' (' . $slot['range'] . ')' : 'Chưa chọn' ),
		'Ghi chú: ' . ( $meta( 'note' ) ? $meta( 'note' ) : '—' ),
		'Nguồn: ' . $meta( 'source' ),
		'',
		'Xem trong trang quản trị: ' . admin_url( 'post.php?post=' . $id . '&action=edit' ),
	);
	wp_mail(
		$to,
		'[Lịch hẹn mới] ' . get_the_title( $id ),
		implode( "\n", $lines ),
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);
}

/* ---------------------------------------------------------------------------
 * REST route
 * ------------------------------------------------------------------------ */

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'quangdang/v1',
			'/booking',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true', // Public form; protected by honeypot + rate limit.
				'callback'            => function ( WP_REST_Request $request ) {
					$params = $request->get_json_params();
					if ( ! is_array( $params ) ) {
						$params = $request->get_body_params();
					}
					$result = qd_booking_submit( (array) $params );
					unset( $result['id'] );
					return new WP_REST_Response( $result, $result['ok'] ? 200 : 422 );
				},
			)
		);
	}
);

/* ---------------------------------------------------------------------------
 * No-JS fallback (admin-post)
 * ------------------------------------------------------------------------ */

/**
 * Handle a classic form POST, then redirect to the booking page.
 */
function qd_booking_admin_post() {
	$in = array();
	foreach ( array( 'name', 'phone', 'service', 'date', 'slot', 'note', 'source', 'website' ) as $key ) {
		$in[ $key ] = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput
	}
	$result = qd_booking_submit( $in );
	$base   = home_url( '/dat-lich/' );

	if ( $result['ok'] ) {
		wp_safe_redirect( add_query_arg( 'da-gui', '1', $base ) );
		exit;
	}

	// Keep errors and what the visitor typed for a few minutes, so the page can show them.
	$token = wp_generate_password( 12, false );
	set_transient(
		'qd_bk_err_' . $token,
		array(
			'errors' => $result['errors'],
			'values' => array(
				'name'    => sanitize_text_field( (string) $in['name'] ),
				'phone'   => sanitize_text_field( (string) $in['phone'] ),
				'service' => sanitize_title( (string) $in['service'] ),
				'date'    => sanitize_text_field( (string) $in['date'] ),
				'slot'    => sanitize_key( (string) $in['slot'] ),
				'note'    => sanitize_textarea_field( (string) $in['note'] ),
			),
		),
		10 * MINUTE_IN_SECONDS
	);
	wp_safe_redirect( add_query_arg( 'loi', $token, $base ) . '#dat-lich-form' );
	exit;
}
add_action( 'admin_post_nopriv_qd_booking', 'qd_booking_admin_post' );
add_action( 'admin_post_qd_booking', 'qd_booking_admin_post' );

/* ---------------------------------------------------------------------------
 * Admin: inbox columns + status/details meta box
 * ------------------------------------------------------------------------ */

qd_meta_box(
	'qd-booking',
	'Chi tiết lịch hẹn',
	'lich-hen',
	array(
		'_qd_status'  => array(
			'label'   => 'Trạng thái xử lý',
			'type'    => 'select',
			'options' => 'qd_booking_statuses',
		),
		'_qd_phone'   => array( 'label' => 'Số điện thoại' ),
		'_qd_service' => array(
			'label' => 'Dịch vụ (slug)',
			'help'  => 'Để trống nếu khách chưa rõ.',
		),
		'_qd_date'    => array(
			'label' => 'Ngày hẹn',
			'type'  => 'date',
		),
		'_qd_slot'    => array(
			'label'   => 'Buổi',
			'type'    => 'select',
			'options' => fn() => array( '' => '— Chưa chọn —' ) + wp_list_pluck( qd_booking_slots(), 'label' ),
		),
		'_qd_note'    => array(
			'label' => 'Ghi chú của khách',
			'type'  => 'textarea',
			'rows'  => 3,
		),
		'_qd_source'  => array( 'label' => 'Nguồn (trang khách gửi)' ),
	)
);

add_filter(
	'manage_lich-hen_posts_columns',
	function () {
		return array(
			'cb'            => '<input type="checkbox">',
			'title'         => 'Khách hàng',
			'qd_phone'      => 'SĐT',
			'qd_service'    => 'Dịch vụ',
			'qd_when'       => 'Ngày / Buổi',
			'qd_source'     => 'Nguồn',
			'qd_status'     => 'Trạng thái',
			'date'          => 'Gửi lúc',
		);
	}
);

add_action(
	'manage_lich-hen_posts_custom_column',
	function ( $column, $post_id ) {
		$meta = fn( $k ) => (string) get_post_meta( $post_id, '_qd_' . $k, true );
		switch ( $column ) {
			case 'qd_phone':
				$phone = $meta( 'phone' );
				echo $phone ? '<a href="tel:' . esc_attr( $phone ) . '"><strong>' . esc_html( qd_booking_format_phone( $phone ) ) . '</strong></a>' : '—';
				break;
			case 'qd_service':
				$slug = $meta( 'service' );
				if ( $slug ) {
					$found = get_posts(
						array(
							'post_type'      => 'dich-vu',
							'name'           => $slug,
							'posts_per_page' => 1,
							'no_found_rows'  => true,
						)
					);
					echo esc_html( $found ? $found[0]->post_title : $slug );
				} else {
					echo '<em>Chưa rõ</em>';
				}
				break;
			case 'qd_when':
				$slots = qd_booking_slots();
				$date  = $meta( 'date' );
				$slot  = $slots[ $meta( 'slot' ) ] ?? null;
				if ( $date || $slot ) {
					echo esc_html( ( $date ? wp_date( 'd/m/Y', strtotime( $date . ' 12:00:00' ) ) : '' ) . ( $slot ? ' · ' . $slot['label'] : '' ) );
				} else {
					echo '—';
				}
				break;
			case 'qd_source':
				echo esc_html( $meta( 'source' ) ? $meta( 'source' ) : '—' );
				break;
			case 'qd_status':
				$statuses = qd_booking_statuses();
				$key      = $meta( 'status' ) ? $meta( 'status' ) : 'moi';
				$colors   = array(
					'moi'    => '#b4532a',
					'da-goi' => '#0a727a',
					'da-den' => '#067647',
					'huy'    => '#8a9b9d',
				);
				echo '<strong style="color:' . esc_attr( $colors[ $key ] ?? '#14292b' ) . '">' . esc_html( $statuses[ $key ] ?? $key ) . '</strong>';
				break;
		}
	},
	10,
	2
);

// Newest first by default.
add_action(
	'pre_get_posts',
	function ( WP_Query $query ) {
		if ( is_admin() && $query->is_main_query() && 'lich-hen' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
		}
	}
);

/* ---------------------------------------------------------------------------
 * Front end: script (site-wide, the strip is everywhere) + page styles
 * ------------------------------------------------------------------------ */

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script(
			'qd-booking',
			QD_URI . '/assets/js/booking.js',
			array(),
			qd_asset_ver( 'assets/js/booking.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		wp_localize_script(
			'qd-booking',
			'qdBooking',
			array(
				'rest'   => esc_url_raw( rest_url( 'quangdang/v1/booking' ) ),
				'clinic' => array(
					'name'    => qd_clinic( 'name' ),
					'address' => qd_clinic( 'address' ),
					'hours'   => qd_clinic( 'hours' ),
					'map'     => qd_clinic( 'map_url' ),
					'zalo'    => qd_clinic( 'zalo' ),
					'hotline' => qd_clinic( 'hotline' ),
				),
				'slots'  => qd_booking_slots(),
			)
		);
	}
);

/*
 * `?dich-vu=slug` (from qd_booking_url) collides with the dich-vu post type's own query var,
 * which turns /dat-lich/?dich-vu=x into a 404. Drop it from the main query on the booking page;
 * page-dat-lich.php reads it from $_GET.
 */
add_filter(
	'request',
	function ( $vars ) {
		if ( isset( $vars['dich-vu'], $vars['pagename'] ) && 'dat-lich' === $vars['pagename'] ) {
			unset( $vars['dich-vu'], $vars['post_type'], $vars['name'] );
		}
		return $vars;
	}
);

// Tiny site-wide styles for the strip's thank-you/error states (the strip itself is styled in main.css).
add_action(
	'wp_enqueue_scripts',
	function () {
		$file = QD_DIR . '/assets/css/pages/booking-form.css';
		if ( file_exists( $file ) ) {
			wp_add_inline_style( 'qd-main', (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
	},
	20
);

add_filter(
	'qd_page_styles',
	function ( $styles ) {
		$styles['qd-quiz']    = array( 'assets/css/pages/quiz.css', fn() => is_page( 'tim-lieu-trinh' ) );
		$styles['qd-booking'] = array( 'assets/css/pages/booking.css', fn() => is_page( 'dat-lich' ) );
		$styles['qd-contact'] = array( 'assets/css/pages/contact.css', fn() => is_page( 'lien-he' ) );
		return $styles;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_page( 'tim-lieu-trinh' ) ) {
			wp_enqueue_script(
				'qd-quiz',
				QD_URI . '/assets/js/quiz.js',
				array(),
				qd_asset_ver( 'assets/js/quiz.js' ),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
);
