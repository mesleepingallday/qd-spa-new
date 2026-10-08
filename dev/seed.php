<?php
/**
 * Demo content for local development: `wp eval-file dev/seed.php` (run by dev/setup.sh).
 * Builds the services tree, menu, pages, articles, promotions, doctors and courses
 * from the spreadsheet structure (qd_service_catalog / qd_default_menu_tree in the theme).
 *
 * Featured images: dev/demo-images/{type}/{slug}.(webp|jpg|png), falling back to
 * dev/demo-images/_placeholders/{type}/{slug}.webp. Drop generated images in and re-run setup.
 *
 * @package QuangDang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

list( $qd_cat_data, $qd_service_data ) = require __DIR__ . '/seed-data/services.php';
$qd_content                            = require __DIR__ . '/seed-data/content.php';

$qd_log = function ( $msg ) {
	WP_CLI::log( '  · ' . $msg );
};

/**
 * Attach a demo image as featured image.
 */
function qd_seed_image( $key, $post_id ) {
	$base = __DIR__ . '/demo-images/';
	foreach ( array( '', '_placeholders/' ) as $dir ) {
		foreach ( array( 'webp', 'jpg', 'jpeg', 'png' ) as $ext ) {
			$file = $base . $dir . $key . '.' . $ext;
			if ( file_exists( $file ) ) {
				$tmp = wp_tempnam( basename( $file ) );
				copy( $file, $tmp );
				$id = media_handle_sideload(
					array(
						'name'     => basename( $file ),
						'tmp_name' => $tmp,
					),
					$post_id
				);
				if ( ! is_wp_error( $id ) ) {
					set_post_thumbnail( $post_id, $id );
				}
				return;
			}
		}
	}
}

/**
 * Insert a post and its meta.
 */
function qd_seed_post( $args, $meta = array() ) {
	$args = array_merge(
		array(
			'post_status' => 'publish',
			'post_author' => 1,
		),
		$args
	);
	$id   = wp_insert_post( wp_slash( $args ), true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
	return $id;
}

WP_CLI::log( 'Seeding Quang Đăng demo content…' );

// Settings.
update_option( 'timezone_string', 'Asia/Ho_Chi_Minh' );
update_option( 'date_format', 'd/m/Y' );
update_option( 'time_format', 'H:i' );
update_option( 'category_base', 'tin-tuc' );
update_option( 'posts_per_page', 9 );
foreach ( get_posts( array( 'post_type' => array( 'post', 'page' ), 'numberposts' => -1, 'post_status' => 'any' ) ) as $p ) {
	wp_delete_post( $p->ID, true );
}

// Categories.
$qd_cats = array();
foreach ( array(
	'cam-nang-lam-dep' => array( 'Cẩm nang làm đẹp', 'Kiến thức chăm sóc và điều trị da từ bác sĩ da liễu.' ),
	'su-kien-uu-dai'   => array( 'Sự kiện – Ưu đãi', 'Chương trình ưu đãi, sự kiện và thông báo của phòng khám.' ),
) as $slug => list( $name, $desc ) ) {
	$t                = wp_insert_term( $name, 'category', array( 'slug' => $slug, 'description' => $desc ) );
	$qd_cats[ $slug ] = is_wp_error( $t ) ? (int) $t->get_error_data() : $t['term_id'];
}
$qd_uncat = get_term_by( 'id', 1, 'category' );
if ( $qd_uncat ) {
	wp_update_term( 1, 'category', array( 'name' => 'Tin tức chung', 'slug' => 'tin-tuc-chung' ) );
}
$qd_log( 'categories' );

// Doctors.
$qd_doctors = array();
foreach ( $qd_content['doctors'] as $i => $d ) {
	$qd_doctors[] = qd_seed_post(
		array(
			'post_type'    => 'bac-si',
			'post_title'   => $d['title'],
			'post_excerpt' => $d['excerpt'],
			'post_content' => $d['content'],
			'menu_order'   => $i,
		),
		$d['meta']
	);
	qd_seed_image( 'doctors/' . ( $i + 1 ), end( $qd_doctors ) );
}
$qd_log( 'doctors' );

// Services: categories (top level) + services (children).
$qd_order = 0;
foreach ( qd_service_catalog() as $slug => list( $title, $group, $children ) ) {
	$c      = $qd_cat_data[ $slug ];
	$cat_id = qd_seed_post(
		array(
			'post_type'    => 'dich-vu',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_excerpt' => $c['excerpt'],
			'post_content' => $c['content'],
			'menu_order'   => $qd_order++,
		),
		array(
			'_qd_group' => $group,
			'_qd_faq'   => $c['faq'],
		)
	);
	qd_seed_image( 'categories/' . $slug, $cat_id );
	wp_insert_term( $title, 'post_tag', array( 'slug' => $slug ) );

	$i = 0;
	foreach ( $children as $s_slug => $s_title ) {
		$s    = $qd_service_data[ $s_slug ];
		$s_id = qd_seed_post(
			array(
				'post_type'    => 'dich-vu',
				'post_parent'  => $cat_id,
				'post_title'   => $s_title,
				'post_name'    => $s_slug,
				'post_excerpt' => $s['excerpt'],
				'post_content' => '<p>' . $s['excerpt'] . ' Bác sĩ da liễu thăm khám, soi da và xây dựng phác đồ riêng trước khi điều trị, giúp kết quả an toàn và bền vững hơn.</p><p>Toàn bộ quy trình thực hiện tại phòng điều trị vô khuẩn, dụng cụ dùng một lần, sản phẩm có nguồn gốc rõ ràng.</p>',
				'menu_order'   => $i++,
			),
			array(
				'_qd_price_from' => $s['price'],
				'_qd_price_unit' => $s['unit'],
				'_qd_sessions'   => $s['sessions'],
				'_qd_duration'   => $s['duration'],
				'_qd_downtime'   => $s['downtime'],
				'_qd_suits'      => $s['suits'] ?? '',
				'_qd_not_suits'  => $s['not_suits'] ?? '',
				'_qd_technology' => $s['technology'] ?? '',
				'_qd_steps'      => $s['steps'] ?? $c['steps'],
				'_qd_prices'     => $s['prices'] ?? '',
				'_qd_aftercare'  => $s['after'] ?? $c['after'],
				'_qd_faq'        => $s['faq'] ?? $c['faq'],
				'_qd_doctor'     => 'noi-khoa-tham-my' === $slug ? ( $qd_doctors[1] ?? 0 ) : ( $qd_doctors[0] ?? 0 ),
			)
		);
		// Edge case kept on purpose: "xoa-xam-tattoo" has no image and no price.
		if ( 'xoa-xam-tattoo' !== $s_slug ) {
			qd_seed_image( 'services/' . $s_slug, $s_id );
		}
	}
}
$qd_log( 'services' );

// Articles.
foreach ( $qd_content['posts'] as list( $title, $cat, $tag, $excerpt, $body, $when ) ) {
	$id = qd_seed_post(
		array(
			'post_title'    => $title,
			'post_excerpt'  => $excerpt,
			'post_content'  => $body,
			'post_category' => array( $qd_cats[ $cat ] ),
			'tags_input'    => array( $tag ),
			'post_date'     => wp_date( 'Y-m-d H:i:s', strtotime( $when ) ),
		),
		array( '_qd_reviewer' => $qd_doctors[0] ?? 0 )
	);
	qd_seed_image( 'posts/' . get_post_field( 'post_name', $id ), $id );
}
foreach ( $qd_content['promos'] as list( $title, $label, $start, $end, $excerpt, $body, $when, $tag ) ) {
	$id = qd_seed_post(
		array(
			'post_title'    => $title,
			'post_excerpt'  => $excerpt,
			'post_content'  => $body,
			'post_category' => array( $qd_cats['su-kien-uu-dai'] ),
			'tags_input'    => $tag ? array( $tag ) : array(),
			'post_date'     => wp_date( 'Y-m-d H:i:s', strtotime( $when ) ),
		),
		array(
			'_qd_promo_label' => $label,
			'_qd_promo_start' => $start,
			'_qd_promo_end'   => $end,
		)
	);
	qd_seed_image( 'promos/' . get_post_field( 'post_name', $id ), $id );
}
$qd_log( 'articles + promotions' );

// Courses.
foreach ( $qd_content['courses'] as $i => list( $slug, $title, $excerpt, $fee, $length, $format, $schedule, $modules ) ) {
	$id = qd_seed_post(
		array(
			'post_type'    => 'khoa-hoc',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_excerpt' => $excerpt,
			'post_content' => '<p>' . $excerpt . '</p><p>Học viên được thực hành trực tiếp tại phòng khám, giảng viên là bác sĩ và chuyên viên nhiều năm kinh nghiệm. Hoàn thành khóa học được cấp chứng nhận của phòng khám.</p>',
			'menu_order'   => $i,
		),
		array(
			'_qd_course_fee'      => $fee,
			'_qd_course_length'   => $length,
			'_qd_course_format'   => $format,
			'_qd_course_schedule' => $schedule,
			'_qd_course_modules'  => $modules,
		)
	);
	qd_seed_image( 'courses/' . $slug, $id );
}
$qd_log( 'courses' );

// Pages. Templates are picked by slug (page-{slug}.php) — no template meta needed.
$qd_page = fn( $title, $slug, $content = '', $parent = 0, $excerpt = '' ) => qd_seed_post(
	array(
		'post_type'    => 'page',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
		'post_parent'  => $parent,
	)
);
$qd_home  = $qd_page( 'Trang chủ', 'trang-chu' );
$qd_news  = $qd_page( 'Tin tức', 'tin-tuc' );
$qd_about = $qd_page( 'Giới thiệu', 'gioi-thieu', '<p>Phòng khám Da liễu Thẩm mỹ Quang Đăng – bác sĩ da liễu trực tiếp thăm khám, phác đồ riêng cho từng làn da.</p>' );
foreach ( $qd_content['about'] as $slug => list( $title, $excerpt ) ) {
	$qd_page( $title, $slug, '<p>' . $excerpt . '</p>', $qd_about, $excerpt );
}
$qd_page( 'Đặt lịch khám', 'dat-lich' );
$qd_page( 'Kiểm tra da – Tìm liệu trình phù hợp', 'tim-lieu-trinh' );
$qd_page( 'Liên hệ', 'lien-he' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $qd_home );
update_option( 'page_for_posts', $qd_news );
$qd_log( 'pages' );

// Primary menu from the spreadsheet tree, linking real objects where they exist.
$qd_menu_id = wp_create_nav_menu( 'Menu chính' );
$qd_add     = function ( $nodes, $parent ) use ( &$qd_add, $qd_menu_id ) {
	foreach ( $nodes as $pos => $node ) {
		$item = array(
			'menu-item-title'       => $node['title'],
			'menu-item-url'         => $node['url'],
			'menu-item-description' => $node['desc'],
			'menu-item-status'      => 'publish',
			'menu-item-parent-id'   => $parent,
			'menu-item-position'    => $pos + 1,
			'menu-item-type'        => 'custom',
		);
		$path = trim( (string) wp_parse_url( $node['url'], PHP_URL_PATH ), '/' );
		if ( false === strpos( $node['url'], '#' ) ) {
			$post = get_page_by_path( preg_replace( '#^dich-vu/#', '', $path ), OBJECT, 'dich-vu' )
				?: get_page_by_path( preg_replace( '#^dao-tao/#', '', $path ), OBJECT, 'khoa-hoc' )
				?: get_page_by_path( $path, OBJECT, 'page' );
			$term = 0 === strpos( $path, 'tin-tuc/' ) ? get_category_by_slug( basename( $path ) ) : null;
			if ( $post && ( 0 === strpos( $path, 'dich-vu/' ) || 0 === strpos( $path, 'dao-tao/' ) || 'page' === $post->post_type ) ) {
				$item['menu-item-type']      = 'post_type';
				$item['menu-item-object']    = $post->post_type;
				$item['menu-item-object-id'] = $post->ID;
			} elseif ( $term ) {
				$item['menu-item-type']      = 'taxonomy';
				$item['menu-item-object']    = 'category';
				$item['menu-item-object-id'] = $term->term_id;
			}
		}
		$id = wp_update_nav_menu_item( $qd_menu_id, 0, $item );
		if ( $node['children'] ) {
			$qd_add( $node['children'], $id );
		}
	}
};
$qd_add( qd_default_menu_tree(), 0 );
set_theme_mod( 'nav_menu_locations', array( 'primary' => $qd_menu_id ) );
$qd_log( 'menu' );

// Area-specific demo data (dev/seed-extra/*.php), run after the core content exists.
foreach ( glob( __DIR__ . '/seed-extra/*.php' ) as $qd_extra ) {
	require $qd_extra;
	$qd_log( basename( $qd_extra ) );
}

WP_CLI::success( 'Demo content ready.' );
