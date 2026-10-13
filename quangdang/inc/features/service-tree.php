<?php
/**
 * Service tree: puts every service under its category, once, and keeps old service URLs working.
 *
 * The theme reads the tree from the post parent: a top-level `dich-vu` post is a category and lists
 * its children; a child is a service (and is what the booking form offers). On the live site several
 * services had no parent, so they rendered as empty categories and were missing from the form.
 *
 * The first time an administrator opens wp-admin after this theme version is installed, the tree
 * below is applied (parents, order, category group; the two missing categories are created). It is
 * keyed by slug, skips slugs that do not exist, never touches content, and records what it did in
 * an admin notice. Old URLs (e.g. /dich-vu/triet-long/) now 404, and inc/seo-fixes.php answers
 * them with a 301 to the service with that slug.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

const QD_SERVICE_TREE_VERSION = '1';

/**
 * Target tree: category slug => [title, group, [service slugs in display order]].
 * A service lists every slug it may have (live site first, then the demo seed).
 *
 * @return array<string,array{0:string,1:string,2:array<int,string[]>}>
 */
function qd_service_tree_plan() {
	return apply_filters(
		'qd_service_tree_plan',
		array(
			'dieu-tri-mun'     => array( 'Điều trị mụn', 'dieu-tri-da', array( array( 'tri-mun-khang-khuan-da-tang' ), array( 'tri-tham-sau-mun' ), array( 'tri-mun-viem-o-lung' ), array( 'tri-mun-viem-o-tay-chan' ) ) ),
			'dieu-tri-tham'    => array( 'Điều trị thâm', 'dieu-tri-da', array( array( 'tri-tham-nach' ), array( 'tri-tham-mat' ), array( 'tri-tham-ben-mong' ), array( 'tri-tham-vung-kin' ) ) ),
			'dieu-tri-nam'     => array( 'Điều trị nám', 'dieu-tri-da', array( array( 'dieu-tri-nam-chuyen-sau' ), array( 'dieu-tri-tan-nhang' ) ) ),
			'dieu-tri-seo'     => array( 'Điều trị sẹo', 'dieu-tri-da', array( array( 'tri-seo-loi' ), array( 'tri-seo-lom' ) ) ),
			'xoa-xam'          => array( 'Xóa xăm', 'dieu-tri-da', array( array( 'xoa-xam-long-may' ), array( 'xoa-xam-mi-mat' ), array( 'xoa-xam-hinh-xam', 'xoa-xam-tattoo' ) ) ),
			'cham-soc-da'      => array(
				'Chăm sóc da',
				'cham-soc-da',
				array(
					array( 'cham-soc-tre-hoa-da-bang-cong-nghe-cao', 'tre-hoa-da-cong-nghe-cao' ),
					array( 'cham-soc-phuc-hoi-da', 'phuc-hoi-da' ),
					array( 'triet-long' ),
					array( 'xoa-not-ruoi' ),
					array( 'kiem-dau-tre-hoa-da-bang-cong-nghe-laser-picosure', 'kiem-dau-tre-hoa-laser-picosure' ),
					array( 'lam-sach-sau-voi-aquapeel' ),
				),
			),
			'noi-khoa-tham-my' => array( 'Nội khoa thẩm mỹ', 'noi-khoa-tham-my', array( array( 'dich-vu-filler', 'filler' ), array( 'dich-vu-botox', 'botox' ), array( 'dich-vu-cang-chi', 'cang-chi' ), array( 'dich-vu-meso', 'meso' ) ) ),
		)
	);
}

/**
 * The one `dich-vu` post with this slug, whatever its parent (null when there is none, or more than one).
 *
 * @param string $slug Post slug.
 * @return WP_Post|null
 */
function qd_service_by_slug( $slug ) {
	$found = get_posts(
		array(
			'post_type'      => 'dich-vu',
			'name'           => $slug,
			'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private' ),
			'posts_per_page' => 2,
			'no_found_rows'  => true,
		)
	);
	return 1 === count( $found ) ? $found[0] : null;
}

/**
 * Apply the plan. Returns a log of lines for the admin notice.
 *
 * @return string[]
 */
function qd_service_tree_apply() {
	$log    = array();
	$groups = qd_service_groups();
	$order  = 0;

	foreach ( qd_service_tree_plan() as $cat_slug => list( $cat_title, $group, $services ) ) {
		$cat = qd_service_by_slug( $cat_slug );
		if ( ! $cat ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'dich-vu',
					'post_status'  => 'publish',
					'post_title'   => $cat_title,
					'post_name'    => $cat_slug,
					'post_excerpt' => $groups[ $group ]['desc'] ?? '',
					'menu_order'   => $order,
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				$log[] = 'Không tạo được nhóm "' . $cat_title . '": ' . $id->get_error_message();
				++$order;
				continue;
			}
			$cat   = get_post( $id );
			$log[] = 'Tạo nhóm mới: ' . $cat_title . ' (/dich-vu/' . $cat_slug . '/)';
		} elseif ( 0 !== (int) $cat->post_parent || (int) $cat->menu_order !== $order ) {
			$moved = 0 !== (int) $cat->post_parent;
			wp_update_post(
				array(
					'ID'          => $cat->ID,
					'post_parent' => 0,
					'menu_order'  => $order,
				)
			);
			if ( $moved ) {
				$log[] = 'Đưa "' . $cat->post_title . '" thành nhóm cấp 1';
			}
		}
		update_post_meta( $cat->ID, '_qd_group', $group );

		foreach ( $services as $i => $slugs ) {
			$service = null;
			foreach ( $slugs as $slug ) {
				$service = qd_service_by_slug( $slug );
				if ( $service ) {
					break;
				}
			}
			if ( ! $service ) {
				$log[] = 'Bỏ qua (không tìm thấy): ' . implode( ' / ', $slugs );
				continue;
			}
			if ( (int) $service->post_parent === (int) $cat->ID && (int) $service->menu_order === $i ) {
				continue;
			}
			$moved = (int) $service->post_parent !== (int) $cat->ID;
			wp_update_post(
				array(
					'ID'          => $service->ID,
					'post_parent' => $cat->ID,
					'menu_order'  => $i,
				)
			);
			delete_post_meta( $service->ID, '_qd_group' ); // Only categories carry a group.
			if ( $moved ) {
				$log[] = 'Chuyển "' . $service->post_title . '" vào ' . $cat->post_title . ' → ' . wp_parse_url( get_permalink( $service->ID ), PHP_URL_PATH );
			}
		}
		++$order;
	}

	// Menu and concern links are resolved through a cached map (inc/seo-fixes.php); cached pages
	// still link to the old URLs. Both would only cost a 301, but refresh them now.
	delete_transient( 'qd_url_map_v2' );
	do_action( 'litespeed_purge_all' );
	return $log;
}

add_action(
	'admin_init',
	function () {
		if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) || QD_SERVICE_TREE_VERSION === get_option( 'qd_service_tree' ) ) {
			return;
		}
		update_option( 'qd_service_tree', QD_SERVICE_TREE_VERSION, false );
		update_option( 'qd_service_tree_log', qd_service_tree_apply(), false );
	}
);

add_action(
	'admin_notices',
	function () {
		$log = get_option( 'qd_service_tree_log' );
		if ( ! is_array( $log ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		delete_option( 'qd_service_tree_log' ); // Shown once.
		echo '<div class="notice notice-success is-dismissible"><p><strong>Đã sắp lại cây dịch vụ.</strong> Đường dẫn cũ tự chuyển 301 sang đường dẫn mới.</p>';
		if ( $log ) {
			echo '<ul style="list-style:disc;margin-left:20px">';
			foreach ( $log as $line ) {
				echo '<li>' . esc_html( $line ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>Cây dịch vụ đã đúng sẵn, không cần thay đổi.</p>';
		}
		echo '</div>';
	}
);
