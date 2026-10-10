<?php
/**
 * Area: core pages.
 *
 * /gioi-thieu/ (and its six subpages) and /tin-tuc/ are WordPress pages, not post types, so a fresh
 * install or a site that only received the theme files answers 404 on them. This creates whatever is
 * missing, once per version, on the first request after the files arrive. It never edits or
 * overwrites a page that already exists (published, draft, private or pending; WordPress frees the slug
 * of a trashed page, so a trashed one is replaced by a new page) and never touches the front-page setting
 * or a posts page the owner already chose.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * The pages the templates expect: slug path => [title, excerpt, parent path].
 *
 * @return array<string,array{0:string,1:string,2:string}>
 */
function qd_core_pages() {
	$pages = array(
		'tin-tuc'    => array( 'Tin tức', '', '' ),
		'gioi-thieu' => array( 'Giới thiệu', 'Bác sĩ da liễu trực tiếp thăm khám, soi da và xây dựng phác đồ riêng cho từng làn da.', '' ),
	);
	foreach ( qd_about_pages() as $slug => $page ) {
		$pages[ 'gioi-thieu/' . $slug ] = array( $page[1], $page[2], 'gioi-thieu' );
	}
	return apply_filters( 'qd_core_pages', $pages );
}

/**
 * Create the missing core pages. Returns the number of pages created.
 */
function qd_ensure_core_pages() {
	$created = 0;
	foreach ( qd_core_pages() as $path => list( $title, $excerpt, $parent_path ) ) {
		$exists = get_posts(
			array(
				'post_type'      => 'page',
				'name'           => basename( $path ),
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_parent'    => $parent_path ? (int) ( get_page_by_path( $parent_path )->ID ?? -1 ) : 0,
			)
		);
		if ( $exists ) {
			continue;
		}
		$parent = $parent_path ? get_page_by_path( $parent_path ) : null;
		if ( $parent_path && ! $parent ) {
			continue; // The parent is in the trash or was renamed: leave it for the owner.
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => basename( $path ),
				'post_excerpt' => $excerpt,
				'post_content' => $excerpt ? '<p>' . esc_html( $excerpt ) . '</p>' : '',
				'post_parent'  => $parent ? $parent->ID : 0,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			++$created;
		}
	}

	// The news page is the posts page, unless the owner already chose one.
	if ( ! get_option( 'page_for_posts' ) ) {
		$news = get_page_by_path( 'tin-tuc' );
		if ( $news && 'publish' === $news->post_status ) {
			update_option( 'page_for_posts', $news->ID );
			++$created;
		}
	}
	return $created;
}

/**
 * Run once per theme version, on the first request after the files arrive (a visitor's request is enough,
 * nobody has to open wp-admin). add_option() fails when the lock exists, so two simultaneous first requests
 * cannot both create the pages. A lock left by a crashed request expires after five minutes.
 */
add_action(
	'init',
	function () {
		if ( get_option( 'qd_core_pages_ver' ) === QD_VERSION ) {
			return;
		}
		$lock = (int) get_option( 'qd_core_pages_lock', 0 );
		if ( $lock && time() - $lock < 5 * MINUTE_IN_SECONDS ) {
			return;
		}
		delete_option( 'qd_core_pages_lock' );
		if ( ! add_option( 'qd_core_pages_lock', time(), '', false ) ) {
			return;
		}
		qd_ensure_core_pages();
		update_option( 'qd_core_pages_ver', QD_VERSION, false );
		delete_option( 'qd_core_pages_lock' );
		flush_rewrite_rules( false );
	},
	99 // After the post types are registered, so the rewrite rules flushed here include them.
);
