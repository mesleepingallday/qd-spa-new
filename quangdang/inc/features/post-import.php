<?php
/**
 * Area: posts published through git.
 *
 * Each folder in content/posts/ is one post: post.json (title, status, date, category, tags, excerpt,
 * images and their alt text, promotion box, Rank Math fields), content.html (block markup, images
 * written as {{img:file.webp}}) and the image files. On the first request after the files arrive the
 * images go to the Media Library and the post is created.
 *
 * Create-only: a post is imported once, keyed by its folder name (= slug). After that WordPress owns
 * it: edits in wp-admin are never overwritten, and a post deleted in wp-admin is not recreated.
 * A future "date" with status "publish" schedules the post.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post folders in the theme: slug => absolute path.
 *
 * @return array<string,string>
 */
function qd_import_post_dirs() {
	$dirs = array();
	foreach ( glob( QD_DIR . '/content/posts/*/post.json' ) ?: array() as $json ) {
		$dirs[ basename( dirname( $json ) ) ] = dirname( $json );
	}
	return $dirs;
}

/**
 * Attachment ID for an image of a post folder, uploading it the first time.
 *
 * @param string $slug Post folder.
 * @param string $dir  Folder path.
 * @param string $file Image file name.
 * @param string $alt  Alt text.
 * @return int 0 on failure.
 */
function qd_import_image( $slug, $dir, $file, $alt ) {
	$source = $slug . '/' . $file;
	$found  = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_qd_import_source', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $source,             // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	if ( ! is_readable( "$dir/$file" ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $file );
	copy( "$dir/$file", $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0,
		$alt
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_qd_import_source', $source );
	return (int) $id;
}

/**
 * Term IDs for slug => name pairs, creating missing terms.
 *
 * @param array<string,string> $terms    Slug => name.
 * @param string               $taxonomy Taxonomy.
 * @return int[]
 */
function qd_import_terms( $terms, $taxonomy ) {
	$ids = array();
	foreach ( (array) $terms as $slug => $name ) {
		$term = get_term_by( 'slug', $slug, $taxonomy );
		if ( ! $term ) {
			$new  = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
			$term = is_wp_error( $new ) ? null : get_term( $new['term_id'], $taxonomy );
		}
		if ( $term ) {
			$ids[] = (int) $term->term_id;
		}
	}
	return $ids;
}

/**
 * Import one post folder. Returns the post ID, or 0 when it could not be created.
 *
 * @param string $slug Folder name, used as the post slug.
 * @param string $dir  Folder path.
 */
function qd_import_post( $slug, $dir ) {
	$data = json_decode( (string) file_get_contents( "$dir/post.json" ), true );
	$html = is_readable( "$dir/content.html" ) ? (string) file_get_contents( "$dir/content.html" ) : '';
	if ( ! is_array( $data ) || empty( $data['title'] ) ) {
		return 0;
	}

	$images = array();
	foreach ( (array) ( $data['images'] ?? array() ) as $file => $alt ) {
		$images[ $file ] = qd_import_image( $slug, $dir, $file, (string) $alt );
		if ( ! $images[ $file ] ) {
			return 0; // Try again on a later request rather than publish a post with a broken image.
		}
	}
	$html = preg_replace_callback(
		'/\{\{img:([^}]+)\}\}/',
		fn( $m ) => ! empty( $images[ $m[1] ] ) ? esc_url( wp_get_attachment_url( $images[ $m[1] ] ) ) : '',
		$html
	);

	$id = wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => in_array( $data['status'] ?? '', array( 'publish', 'draft', 'pending', 'private' ), true ) ? $data['status'] : 'draft',
			'post_title'    => $data['title'],
			'post_name'     => $slug,
			'post_excerpt'  => (string) ( $data['excerpt'] ?? '' ),
			'post_content'  => $html,
			'post_date'     => (string) ( $data['date'] ?? '' ),
			'post_category' => qd_import_terms( $data['category'] ?? array(), 'category' ),
			'tags_input'    => qd_import_terms( $data['tags'] ?? array(), 'post_tag' ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return 0;
	}

	$featured = $data['featured_image'] ?? '';
	if ( $featured && ! empty( $images[ $featured ] ) ) {
		set_post_thumbnail( $id, $images[ $featured ] );
	}
	$meta = array(
		'_qd_promo_label'         => $data['promo']['label'] ?? '',
		'_qd_promo_start'         => $data['promo']['start'] ?? '',
		'_qd_promo_end'           => $data['promo']['end'] ?? '',
		'_qd_promo_headline'      => $data['promo']['headline'] ?? '',
		'_qd_promo_points'        => implode( "\n", array_map( 'strval', (array) ( $data['promo']['points'] ?? array() ) ) ),
		'_qd_promo_image'         => ! empty( $data['promo']['image'] ) ? (int) ( $images[ $data['promo']['image'] ] ?? 0 ) : 0,
		'rank_math_title'         => $data['rank_math']['title'] ?? '',
		'rank_math_description'   => $data['rank_math']['description'] ?? '',
		'rank_math_focus_keyword' => $data['rank_math']['focus_keyword'] ?? '',
	);
	foreach ( array_filter( $meta, 'strlen' ) as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}
	return (int) $id;
}

/**
 * Import every folder not imported yet. A folder whose slug is already taken (an existing post, in any
 * status) is recorded as done and left alone.
 *
 * @return string[] Slugs imported on this run.
 */
function qd_import_posts() {
	$done     = (array) get_option( 'qd_imported_posts', array() );
	$imported = array();
	$admins   = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => 'ID',
		)
	);
	$previous = get_current_user_id();
	if ( $admins ) {
		wp_set_current_user( (int) $admins[0] ); // Author of the post; also keeps the block markup out of kses.
	}

	foreach ( qd_import_post_dirs() as $slug => $dir ) {
		if ( isset( $done[ $slug ] ) ) {
			continue;
		}
		$taken = get_posts(
			array(
				'post_type'      => 'post',
				'name'           => $slug,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $taken ) {
			$done[ $slug ] = (int) $taken[0];
			continue;
		}
		$id = qd_import_post( $slug, $dir );
		if ( $id ) {
			$done[ $slug ] = $id;
			$imported[]    = $slug;
		}
	}

	wp_set_current_user( $previous );
	update_option( 'qd_imported_posts', $done, false );
	if ( $imported ) {
		delete_transient( 'qd_url_map_v2' );
		do_action( 'litespeed_purge_all' );
	}
	return $imported;
}

/**
 * Run when a folder is not imported yet, on the first request after the files arrive (no need to open
 * wp-admin). Same lock as the core pages: two simultaneous first requests cannot both import.
 */
add_action(
	'init',
	function () {
		$done = (array) get_option( 'qd_imported_posts', array() );
		if ( ! array_diff_key( qd_import_post_dirs(), $done ) ) {
			return;
		}
		$lock = (int) get_option( 'qd_import_posts_lock', 0 );
		if ( $lock && time() - $lock < 5 * MINUTE_IN_SECONDS ) {
			return;
		}
		delete_option( 'qd_import_posts_lock' );
		if ( ! add_option( 'qd_import_posts_lock', time(), '', false ) ) {
			return;
		}
		qd_import_posts();
		delete_option( 'qd_import_posts_lock' );
	},
	100 // After the core pages (99), so the categories and the news page exist.
);

/**
 * One-time backfill (v0.2.1): posts imported before the home banner fields existed get the three new
 * promotion fields from their post.json, only where the field is still empty. Same init + lock pattern
 * as the importer; runs after it.
 */
function qd_backfill_promo_meta() {
	$done = (array) get_option( 'qd_imported_posts', array() );
	foreach ( qd_import_post_dirs() as $slug => $dir ) {
		$data  = json_decode( (string) file_get_contents( "$dir/post.json" ), true );
		$promo = is_array( $data ) ? ( $data['promo'] ?? array() ) : array();
		if ( ! $promo ) {
			continue;
		}
		$id = isset( $done[ $slug ] ) ? (int) $done[ $slug ] : 0;
		if ( ! $id || ! get_post( $id ) ) {
			$found = get_posts(
				array(
					'post_type'      => 'post',
					'name'           => $slug,
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			$id    = $found ? (int) $found[0] : 0;
		}
		if ( ! $id ) {
			continue;
		}
		$values = array(
			'_qd_promo_headline' => (string) ( $promo['headline'] ?? '' ),
			'_qd_promo_points'   => implode( "\n", array_map( 'strval', (array) ( $promo['points'] ?? array() ) ) ),
			'_qd_promo_image'    => 0,
		);
		if ( ! empty( $promo['image'] ) && ! empty( $data['images'][ $promo['image'] ] ) && ! get_post_meta( $id, '_qd_promo_image', true ) ) {
			$values['_qd_promo_image'] = qd_import_image( $slug, $dir, $promo['image'], (string) $data['images'][ $promo['image'] ] );
		}
		foreach ( $values as $key => $value ) {
			if ( $value && ! get_post_meta( $id, $key, true ) ) {
				update_post_meta( $id, $key, $value );
			}
		}
	}
}

add_action(
	'init',
	function () {
		if ( get_option( 'qd_promo_meta_v1' ) ) {
			return;
		}
		$lock = (int) get_option( 'qd_promo_meta_v1_lock', 0 );
		if ( $lock && time() - $lock < 5 * MINUTE_IN_SECONDS ) {
			return;
		}
		delete_option( 'qd_promo_meta_v1_lock' );
		if ( ! add_option( 'qd_promo_meta_v1_lock', time(), '', false ) ) {
			return;
		}
		$admins   = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
				'fields' => 'ID',
			)
		);
		$previous = get_current_user_id();
		if ( $admins ) {
			wp_set_current_user( (int) $admins[0] );
		}
		qd_backfill_promo_meta();
		wp_set_current_user( $previous );
		update_option( 'qd_promo_meta_v1', 1, false );
		delete_option( 'qd_promo_meta_v1_lock' );
		do_action( 'litespeed_purge_all' );
	},
	101 // After the importer (100).
);
