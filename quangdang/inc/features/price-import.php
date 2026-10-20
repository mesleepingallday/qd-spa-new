<?php
/**
 * Area: real price menu (inc/data/price-menu.php).
 *
 * Git pull only updates theme files, not the database, so this adds
 * Công cụ › Cập nhật dữ liệu dịch vụ: preview what will change on each service, then apply.
 * Rules: price fields always follow the menu; content fields are only written when empty or
 * still the original sample text, so anything edited by hand in wp-admin is kept.
 * It also creates the /dich-vu-khac/ page (page-dich-vu-khac.php) when it is missing.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * The price menu data.
 *
 * @return array{version:string,services:array,other:array,sample_hashes:string[]}
 */
function qd_price_menu() {
	static $menu = null;
	if ( null === $menu ) {
		$menu = require QD_DIR . '/inc/data/price-menu.php';
	}
	return $menu;
}

/**
 * Meta fields the menu sets: key => true when it always overwrites (prices), false when it
 * only fills empty / sample values (content).
 *
 * @return array<string,bool>
 */
function qd_price_menu_fields() {
	return array(
		'price_from' => true,
		'price_unit' => true,
		'sessions'   => true,
		'duration'   => true,
		'prices'     => true,
		'downtime'   => false,
		'suits'      => false,
		'not_suits'  => false,
		'technology' => false,
		'steps'      => false,
		'aftercare'  => false,
		'faq'        => false,
	);
}

/**
 * Normalise a stored value for comparing (line endings + outer whitespace).
 *
 * @param mixed $value Value.
 */
function qd_price_menu_norm( $value ) {
	return trim( str_replace( "\r\n", "\n", (string) $value ) );
}

/**
 * Slugs a menu service may have: the live site often uses longer slugs than the demo seed
 * (dich-vu-filler vs filler). Aliases come from the service tree plan (inc/features/service-tree.php).
 *
 * @param string $slug Menu key.
 * @return string[] Live slugs first, then the key itself.
 */
function qd_price_menu_slugs( $slug ) {
	foreach ( qd_service_tree_plan() as $category ) {
		foreach ( $category[2] as $aliases ) {
			if ( in_array( $slug, $aliases, true ) ) {
				return $aliases;
			}
		}
	}
	return array( $slug );
}

/**
 * What applying the menu would do.
 *
 * @return array{services:array,missing:string[],page:int}
 *   services: [ { post: WP_Post, changes: [ key => [ action, old, new ] ] } ] where action is
 *   overwrite | fill | keep (hand-edited, left alone). Unchanged fields are omitted.
 */
function qd_price_import_plan() {
	$menu    = qd_price_menu();
	$samples = array_flip( $menu['sample_hashes'] );
	$page    = get_page_by_path( 'dich-vu-khac' );
	$plan    = array(
		'services' => array(),
		'missing'  => array(),
		'page'     => $page ? $page->ID : 0,
	);

	foreach ( $menu['services'] as $slug => $data ) {
		$post = null;
		foreach ( qd_price_menu_slugs( $slug ) as $candidate ) {
			$post = qd_service_by_slug( $candidate );
			if ( $post ) {
				break;
			}
		}
		if ( ! $post ) {
			$plan['missing'][] = $slug;
			continue;
		}

		$changes = array();
		foreach ( qd_price_menu_fields() as $key => $always ) {
			if ( ! array_key_exists( $key, $data ) ) {
				continue;
			}
			$old = qd_price_menu_norm( get_post_meta( $post->ID, '_qd_' . $key, true ) );
			$new = qd_price_menu_norm( $data[ $key ] );
			if ( 'price_from' === $key ) {
				$old = (string) (int) $old;
			}
			if ( $old === $new ) {
				continue;
			}
			if ( $always ) {
				$action = 'overwrite';
			} elseif ( '' === $old || isset( $samples[ md5( $old ) ] ) ) {
				$action = 'fill';
			} else {
				$action = 'keep';
			}
			$changes[ $key ] = array( $action, $old, $new );
		}
		$plan['services'][] = array(
			'post'    => $post,
			'changes' => $changes,
		);
	}
	return $plan;
}

/**
 * Apply the menu: write meta, create the Dịch vụ khác page, remember the version.
 *
 * @return array{fields:int,services:int,page_created:bool}
 */
function qd_price_import_apply() {
	$plan   = qd_price_import_plan();
	$result = array(
		'fields'       => 0,
		'services'     => 0,
		'page_created' => false,
	);

	foreach ( $plan['services'] as $row ) {
		$touched = false;
		foreach ( $row['changes'] as $key => list( $action, , $new ) ) {
			if ( 'keep' === $action ) {
				continue;
			}
			update_post_meta( $row['post']->ID, '_qd_' . $key, wp_slash( 'price_from' === $key ? (int) $new : $new ) );
			++$result['fields'];
			$touched = true;
		}
		$result['services'] += (int) $touched;
	}

	if ( ! $plan['page'] ) {
		$id                     = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Dịch vụ khác',
				'post_name'    => 'dich-vu-khac',
				'post_excerpt' => qd_price_menu()['other']['intro'],
			)
		);
		$result['page_created'] = $id && ! is_wp_error( $id );
	}

	update_option( 'qd_price_menu_version', qd_price_menu()['version'], false );
	return $result;
}

/* -------------------------------------------------------------------------
 * Front end: the Dịch vụ khác page uses the service page styles.
 * ---------------------------------------------------------------------- */

add_filter(
	'qd_page_styles',
	function ( $styles ) {
		if ( isset( $styles['qd-service'] ) ) {
			$when                    = $styles['qd-service'][1];
			$styles['qd-service'][1] = fn() => call_user_func( $when ) || is_page( 'dich-vu-khac' );
		}
		return $styles;
	}
);

/* -------------------------------------------------------------------------
 * Admin: Công cụ › Cập nhật dữ liệu dịch vụ
 * ---------------------------------------------------------------------- */

add_action(
	'admin_menu',
	function () {
		add_management_page( 'Cập nhật dữ liệu dịch vụ', 'Cập nhật dữ liệu dịch vụ', 'manage_options', 'qd-price-import', 'qd_price_import_screen' );
	}
);

// Remind once a new menu version is pulled and not yet applied.
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'manage_options' ) || get_option( 'qd_price_menu_version' ) === qd_price_menu()['version'] ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'tools_page_qd-price-import' === $screen->id ) {
			return;
		}
		printf(
			'<div class="notice notice-info"><p>Có bảng giá dịch vụ mới (phiên bản %s). <a href="%s">Xem trước và áp dụng</a>.</p></div>',
			esc_html( qd_price_menu()['version'] ),
			esc_url( admin_url( 'tools.php?page=qd-price-import' ) )
		);
	}
);

/**
 * Short, readable form of a field value for the preview table.
 *
 * @param string $key   Field key.
 * @param string $value Value.
 */
function qd_price_import_preview( $key, $value ) {
	if ( '' === $value ) {
		return '<em>(trống)</em>';
	}
	if ( 'price_from' === $key ) {
		return esc_html( '0' === $value ? '0 (Báo giá khi khám)' : qd_price( $value ) );
	}
	$lines = explode( "\n", $value );
	$text  = wp_html_excerpt( $lines[0], 70, '…' );
	return esc_html( $text ) . ( count( $lines ) > 1 ? ' <span class="description">(+' . ( count( $lines ) - 1 ) . ' dòng)</span>' : '' );
}

/**
 * Admin screen.
 */
function qd_price_import_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$menu   = qd_price_menu();
	$labels = array(
		'price_from' => 'Giá từ',
		'price_unit' => 'Đơn vị giá',
		'sessions'   => 'Số buổi',
		'duration'   => 'Thời gian',
		'prices'     => 'Bảng giá',
		'downtime'   => 'Nghỉ dưỡng',
		'suits'      => 'Phù hợp với',
		'not_suits'  => 'Chưa phù hợp',
		'technology' => 'Công nghệ',
		'steps'      => 'Quy trình',
		'aftercare'  => 'Lưu ý',
		'faq'        => 'Hỏi đáp',
	);
	$actions = array(
		'overwrite' => array( 'Ghi đè', '#2271b1' ),
		'fill'      => array( 'Điền mới', '#00a32a' ),
		'keep'      => array( 'Giữ nguyên (đã sửa tay)', '#996800' ),
	);

	echo '<div class="wrap"><h1>Cập nhật dữ liệu dịch vụ</h1>';

	if ( isset( $_POST['qd_price_import'] ) && check_admin_referer( 'qd_price_import' ) ) {
		$done = qd_price_import_apply();
		printf(
			'<div class="notice notice-success"><p>Đã cập nhật %d trường trên %d dịch vụ%s.</p></div>',
			(int) $done['fields'],
			(int) $done['services'],
			$done['page_created'] ? ' và tạo trang <a href="' . esc_url( home_url( '/dich-vu-khac/' ) ) . '">Dịch vụ khác</a>' : ''
		);
	}

	$plan    = qd_price_import_plan();
	$pending = 0;
	echo '<p>Phiên bản dữ liệu trong theme: <strong>' . esc_html( $menu['version'] ) . '</strong>. Đã áp dụng: <strong>' . esc_html( (string) get_option( 'qd_price_menu_version', 'chưa' ) ) . '</strong>.</p>';
	echo '<p>Giá, đơn vị, bảng giá, số buổi và thời gian luôn theo menu. Các mục nội dung chỉ được điền khi đang trống hoặc còn là nội dung mẫu; mục bạn đã sửa tay được giữ nguyên.</p>';

	echo '<table class="widefat striped"><thead><tr><th>Dịch vụ</th><th>Trường</th><th>Hiện tại</th><th>Mới</th><th>Thao tác</th></tr></thead><tbody>';
	foreach ( $plan['services'] as $row ) {
		$title = '<a href="' . esc_url( get_edit_post_link( $row['post']->ID ) ) . '">' . esc_html( get_the_title( $row['post'] ) ) . '</a>';
		if ( ! $row['changes'] ) {
			echo '<tr><td>' . $title . '</td><td colspan="4" class="description">Đã khớp menu.</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput
			continue;
		}
		$first = true;
		foreach ( $row['changes'] as $key => list( $action, $old, $new ) ) {
			$pending += (int) ( 'keep' !== $action );
			printf(
				'<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td style="color:%s;font-weight:600">%s</td></tr>',
				$first ? $title : '', // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $labels[ $key ] ),
				qd_price_import_preview( $key, $old ), // phpcs:ignore WordPress.Security.EscapeOutput
				qd_price_import_preview( $key, $new ), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_attr( $actions[ $action ][1] ),
				esc_html( $actions[ $action ][0] )
			);
			$first = false;
		}
	}
	echo '</tbody></table>';

	if ( $plan['missing'] ) {
		echo '<div class="notice notice-warning inline"><p>Không tìm thấy dịch vụ có đường dẫn (slug): ' . implode( ', ', array_map( fn( $m ) => '<code>' . esc_html( $m ) . '</code>', $plan['missing'] ) ) . '. Kiểm tra lại slug trong wp-admin nếu bạn đã đổi.</p></div>';
	}
	echo '<p>' . ( $plan['page'] ? 'Trang <a href="' . esc_url( get_permalink( $plan['page'] ) ) . '">Dịch vụ khác</a> đã có.' : 'Trang <strong>Dịch vụ khác</strong> (/dich-vu-khac/) chưa có và sẽ được tạo.' ) . '</p>';

	echo '<form method="post">';
	wp_nonce_field( 'qd_price_import' );
	submit_button( $pending || ! $plan['page'] ? 'Áp dụng ' . $pending . ' thay đổi' : 'Đánh dấu đã áp dụng', 'primary', 'qd_price_import' );
	echo '</form></div>';
}
