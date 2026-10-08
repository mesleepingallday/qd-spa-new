<?php
/**
 * Menu tree for the mega menu (desktop) and drill-down drawer (mobile).
 *
 * Source: the WP menu assigned to "primary" (Appearance → Menus). If none is assigned,
 * the built-in tree below is used — it mirrors the spreadsheet
 * "Thông tin giao diện Website" exactly, and dev/seed.php builds the WP menu from it.
 *
 * Node shape: [ title, url, desc, classes[], children[], current, ancestor ]
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build one node.
 *
 * @param string $title    Label.
 * @param string $path     Site-relative path or absolute URL.
 * @param string $desc     One-line description (dropdowns show it).
 * @param array  $children Child nodes.
 */
function qd_menu_node( $title, $path, $desc = '', $children = array() ) {
	return array(
		'title'    => $title,
		'url'      => preg_match( '#^https?://#', $path ) ? $path : home_url( $path ),
		'desc'     => $desc,
		'classes'  => array(),
		'children' => $children,
		'current'  => false,
		'ancestor' => false,
	);
}

/**
 * Service catalogue from the spreadsheet: category slug => [title, group, [service slug => title]].
 * Single source for the fallback menu and the demo seed.
 *
 * @return array<string,array{0:string,1:string,2:array<string,string>}>
 */
function qd_service_catalog() {
	return array(
		'dieu-tri-mun'     => array(
			'Điều trị mụn',
			'dieu-tri-da',
			array(
				'tri-mun-khang-khuan-da-tang' => 'Trị mụn kháng khuẩn đa tầng',
				'tri-tham-sau-mun'            => 'Trị thâm sau mụn',
				'tri-mun-viem-o-lung'         => 'Trị mụn viêm ở lưng',
				'tri-mun-viem-o-tay-chan'     => 'Trị mụn viêm ở tay – chân',
			),
		),
		'dieu-tri-tham'    => array(
			'Điều trị thâm',
			'dieu-tri-da',
			array(
				'tri-tham-nach'     => 'Trị thâm nách',
				'tri-tham-mat'      => 'Trị thâm mắt',
				'tri-tham-ben-mong' => 'Trị thâm bẹn – mông',
				'tri-tham-vung-kin' => 'Trị thâm vùng kín',
			),
		),
		'dieu-tri-nam'     => array(
			'Điều trị nám',
			'dieu-tri-da',
			array(
				'dieu-tri-nam-chuyen-sau' => 'Điều trị nám chuyên sâu',
				'dieu-tri-tan-nhang'      => 'Điều trị tàn nhang',
			),
		),
		'dieu-tri-seo'     => array(
			'Điều trị sẹo',
			'dieu-tri-da',
			array(
				'tri-seo-loi' => 'Trị sẹo lồi',
				'tri-seo-lom' => 'Trị sẹo lõm',
			),
		),
		'xoa-xam'          => array(
			'Xóa xăm',
			'dieu-tri-da',
			array(
				'xoa-xam-long-may' => 'Xóa xăm lông mày',
				'xoa-xam-mi-mat'   => 'Xóa xăm mí mắt',
				'xoa-xam-tattoo'   => 'Xóa xăm tattoo',
			),
		),
		'cham-soc-da'      => array(
			'Chăm sóc da',
			'cham-soc-da',
			array(
				'tre-hoa-da-cong-nghe-cao'        => 'Chăm sóc trẻ hóa da bằng công nghệ cao',
				'phuc-hoi-da'                     => 'Chăm sóc phục hồi da',
				'triet-long'                      => 'Triệt lông',
				'xoa-not-ruoi'                    => 'Xóa nốt ruồi',
				'kiem-dau-tre-hoa-laser-picosure' => 'Kiềm dầu, trẻ hóa da bằng công nghệ Laser Picosure',
			),
		),
		'noi-khoa-tham-my' => array(
			'Nội khoa thẩm mỹ',
			'noi-khoa-tham-my',
			array(
				'filler'   => 'Dịch vụ Filler',
				'botox'    => 'Dịch vụ Botox',
				'cang-chi' => 'Dịch vụ Căng chỉ',
				'meso'     => 'Dịch vụ Meso',
			),
		),
	);
}

/**
 * Built-in menu tree (mirrors the spreadsheet).
 */
function qd_default_menu_tree() {
	$catalog  = qd_service_catalog();
	$category = function ( $slug ) use ( $catalog ) {
		list( $title, , $services ) = $catalog[ $slug ];
		$children                   = array();
		foreach ( $services as $s_slug => $s_title ) {
			$children[] = qd_menu_node( $s_title, "/dich-vu/$slug/$s_slug/" );
		}
		return qd_menu_node( $title, "/dich-vu/$slug/", '', $children );
	};
	$services_of = fn( $slug ) => $category( $slug )['children'];

	return array(
		qd_menu_node(
			'Giới thiệu',
			'/gioi-thieu/',
			'',
			array(
				qd_menu_node( 'Quá trình hình thành', '/gioi-thieu/qua-trinh-hinh-thanh/', 'Chặng đường phát triển của Quang Đăng' ),
				qd_menu_node( 'Tầm nhìn & Sứ mệnh', '/gioi-thieu/tam-nhin-su-menh/', 'Điều chúng tôi cam kết với khách hàng' ),
				qd_menu_node( 'Về đội ngũ chuyên gia bác sĩ', '/gioi-thieu/doi-ngu-bac-si/', 'Bác sĩ da liễu trực tiếp thăm khám' ),
				qd_menu_node( 'Về Công nghệ & Sản phẩm', '/gioi-thieu/cong-nghe-san-pham/', 'Thiết bị và dược mỹ phẩm chính hãng' ),
				qd_menu_node( 'Về Cơ sở vật chất', '/gioi-thieu/co-so-vat-chat/', 'Không gian phòng khám tại Quỳnh Lưu' ),
				qd_menu_node( 'Về Quy trình chuẩn y khoa', '/gioi-thieu/quy-trinh-chuan-y-khoa/', 'Từ thăm khám đến tái khám' ),
			)
		),
		qd_menu_node(
			'Dịch vụ',
			'/dich-vu/',
			'',
			array(
				qd_menu_node(
					'Chăm sóc và điều trị da',
					'/dich-vu/#cham-soc-va-dieu-tri-da',
					'',
					array(
						qd_menu_node(
							'Điều trị da',
							'/dich-vu/#dieu-tri-da',
							'',
							array_map( $category, array( 'dieu-tri-mun', 'dieu-tri-tham', 'dieu-tri-nam', 'dieu-tri-seo', 'xoa-xam' ) )
						),
						qd_menu_node( 'Chăm sóc da', '/dich-vu/cham-soc-da/', '', $services_of( 'cham-soc-da' ) ),
					)
				),
				qd_menu_node( 'Nội khoa thẩm mỹ', '/dich-vu/noi-khoa-tham-my/', '', $services_of( 'noi-khoa-tham-my' ) ),
			)
		),
		qd_menu_node(
			'Tin tức',
			'/tin-tuc/',
			'',
			array(
				qd_menu_node( 'Cẩm nang làm đẹp', '/tin-tuc/cam-nang-lam-dep/', 'Kiến thức chăm sóc và điều trị da' ),
				qd_menu_node( 'Sự kiện – Ưu đãi', '/tin-tuc/su-kien-uu-dai/', 'Chương trình ưu đãi đang diễn ra' ),
			)
		),
		qd_menu_node(
			'Đào tạo',
			'/dao-tao/',
			'',
			array(
				qd_menu_node( 'Khóa học chăm sóc da cơ bản', '/dao-tao/khoa-hoc-cham-soc-da-co-ban/', 'Cho người mới bắt đầu' ),
				qd_menu_node( 'Khóa học chăm sóc da chuyên sâu', '/dao-tao/khoa-hoc-cham-soc-da-chuyen-sau/', 'Cho kỹ thuật viên, chủ spa' ),
			)
		),
	);
}

/**
 * Menu tree for a location, with current/ancestor flags set.
 *
 * @param string $location Menu location.
 */
function qd_menu_tree( $location = 'primary' ) {
	static $cache = array();
	if ( isset( $cache[ $location ] ) ) {
		return $cache[ $location ];
	}

	$tree      = null;
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations[ $location ] ) ) {
		$items = wp_get_nav_menu_items( $locations[ $location ] );
		if ( $items ) {
			$by_parent = array();
			foreach ( $items as $item ) {
				$by_parent[ (int) $item->menu_item_parent ][] = $item;
			}
			$build = function ( $parent ) use ( &$build, $by_parent ) {
				$nodes = array();
				foreach ( $by_parent[ $parent ] ?? array() as $item ) {
					$node            = qd_menu_node( $item->title, $item->url, (string) $item->description, $build( (int) $item->ID ) );
					$node['classes'] = array_filter( (array) $item->classes );
					$nodes[]         = $node;
				}
				return $nodes;
			};
			$tree  = $build( 0 );
		}
	}
	if ( ! $tree ) {
		$tree = qd_default_menu_tree();
	}

	$cache[ $location ] = qd_menu_mark_current( $tree );
	return $cache[ $location ];
}

/**
 * Flag the node matching the current URL and its ancestors.
 *
 * @param array $nodes Nodes.
 */
function qd_menu_mark_current( $nodes ) {
	$here = untrailingslashit( (string) wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) );
	foreach ( $nodes as &$node ) {
		$path             = untrailingslashit( (string) wp_parse_url( $node['url'], PHP_URL_PATH ) );
		$node['children'] = qd_menu_mark_current( $node['children'] );
		$has_hash         = false !== strpos( $node['url'], '#' );
		$node['current']  = ! $has_hash && $path === $here;
		foreach ( $node['children'] as $child ) {
			if ( $child['current'] || $child['ancestor'] ) {
				$node['ancestor'] = true;
			}
		}
		if ( ! $node['current'] && ! $has_hash && '' !== $path && 0 === strpos( $here . '/', $path . '/' ) ) {
			$node['ancestor'] = true;
		}
	}
	return $nodes;
}

/**
 * True when any child has children (renders as a full-width mega panel).
 *
 * @param array $node Node.
 */
function qd_menu_is_mega( $node ) {
	foreach ( $node['children'] as $child ) {
		if ( $child['children'] ) {
			return true;
		}
	}
	return false;
}

/**
 * A "zone" is a grouping node whose every child also has children
 * (e.g. "Chăm sóc và điều trị da" → Điều trị da, Chăm sóc da).
 *
 * @param array $node Node.
 */
function qd_menu_is_zone( $node ) {
	if ( ! $node['children'] ) {
		return false;
	}
	foreach ( $node['children'] as $child ) {
		if ( ! $child['children'] ) {
			return false;
		}
	}
	return true;
}

/**
 * Is a URL a real page (not "#" or an in-page anchor)?
 *
 * @param string $url URL.
 */
function qd_menu_is_page_url( $url ) {
	return '' !== $url && '#' !== $url && false === strpos( $url, '#' );
}
