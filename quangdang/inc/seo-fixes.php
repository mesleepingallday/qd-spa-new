<?php
/**
 * Live-site safety nets for SEO:
 * - every internal menu link resolves to content that exists (no 404s from the menu),
 * - 301s for duplicate pages and for design-style URLs that 404,
 * - an automatic, page-specific meta description when the editor hasn't written one
 *   (printed by inc/brand-meta.php when no SEO plugin prints one, or via the plugin's filters),
 * - the pages and categories the theme's flows depend on are created once.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Slug aliases: design slug (theme catalogue) => slug used by the live content.
 *
 * @return array<string,string>
 */
function qd_slug_aliases() {
	return (array) apply_filters(
		'qd_slug_aliases',
		array(
			'filler'                          => 'dich-vu-filler',
			'botox'                           => 'dich-vu-botox',
			'cang-chi'                        => 'dich-vu-cang-chi',
			'meso'                            => 'dich-vu-meso',
			'phuc-hoi-da'                     => 'cham-soc-phuc-hoi-da',
			'tre-hoa-da-cong-nghe-cao'        => 'cham-soc-tre-hoa-da-bang-cong-nghe-cao',
			'kiem-dau-tre-hoa-laser-picosure' => 'kiem-dau-tre-hoa-da-bang-cong-nghe-laser-picosure',
			'xoa-xam-tattoo'                  => 'xoa-xam-hinh-xam',
			'xoa-xam-tatoo'                   => 'xoa-xam-hinh-xam',
		)
	);
}

/**
 * Resolve an internal URL to a URL that exists. External, anchor and home links pass through.
 *
 * @param string $url URL.
 * @return string Working URL, or '' when nothing matches.
 */
function qd_resolve_url( $url ) {
	$home  = wp_parse_url( home_url( '/' ) );
	$parts = wp_parse_url( $url );
	if ( ! $parts || ( isset( $parts['host'] ) && $parts['host'] !== $home['host'] ) || isset( $parts['fragment'] ) ) {
		return $url;
	}
	$path = trim( (string) ( $parts['path'] ?? '' ), '/' );
	if ( '' === $path ) {
		return $url;
	}

	static $map = null;
	if ( null === $map ) {
		$map = get_transient( 'qd_url_map_v2' );
		$map = is_array( $map ) ? $map : array();
	}
	if ( array_key_exists( $path, $map ) ) {
		return $map[ $path ];
	}

	$resolved = '';
	$segments = explode( '/', $path );
	$first    = $segments[0];
	$last     = end( $segments );

	$id = url_to_postid( home_url( "/$path/" ) );
	if ( $id ) {
		$resolved = get_permalink( $id ); // Canonical address, not a path WordPress would 301.
	} elseif ( 1 === count( $segments ) && in_array( $first, array( 'dich-vu', 'dao-tao' ), true ) ) {
		$resolved = home_url( "/$path/" ); // CPT archives.
	} else {
		$slug  = qd_slug_aliases()[ $last ] ?? $last;
		$found = get_posts(
			array(
				'name'           => $slug,
				'post_type'      => array( 'dich-vu', 'page', 'khoa-hoc' ),
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		if ( $found ) {
			$resolved = get_permalink( $found[0] );
		} else {
			$term = get_term_by( 'slug', $slug, 'category' );
			if ( $term && $term->count ) {
				$resolved = get_term_link( $term );
			}
		}
	}

	$map[ $path ] = is_string( $resolved ) ? $resolved : '';
	set_transient( 'qd_url_map_v2', $map, DAY_IN_SECONDS );
	return $map[ $path ];
}

foreach ( array( 'save_post', 'deleted_post', 'created_term', 'edited_term', 'delete_term' ) as $qd_hook ) {
	add_action( $qd_hook, fn() => delete_transient( 'qd_url_map_v2' ) );
}

/**
 * Point menu nodes at working URLs. Dead leaves are dropped; a dead group keeps its
 * children and links to the services archive (or its section root).
 *
 * @param array $nodes Menu nodes (see qd_menu_node()).
 */
function qd_menu_fix_links( $nodes ) {
	$out = array();
	foreach ( $nodes as $node ) {
		$node['children'] = qd_menu_fix_links( $node['children'] );
		$url              = qd_resolve_url( $node['url'] );
		if ( '' === $url ) {
			if ( ! $node['children'] ) {
				continue;
			}
			$section = strtok( trim( (string) wp_parse_url( $node['url'], PHP_URL_PATH ), '/' ), '/' );
			$url     = home_url( '/' . ( $section ? $section . '/' : '' ) );
		}
		$node['url'] = $url;
		$out[]       = $node;
	}
	return $out;
}

/**
 * Paths that permanently move elsewhere (duplicate About pages at the root).
 *
 * @return array<string,string> path => target path.
 */
function qd_redirect_map() {
	return (array) apply_filters(
		'qd_redirect_map',
		array(
			'doi-ngu-chuyen-gia-bac-si' => '/gioi-thieu/doi-ngu-bac-si/',
			'qua-trinh-hinh-thanh'      => '/gioi-thieu/qua-trinh-hinh-thanh/',
			'tam-nhin-su-menh'          => '/gioi-thieu/tam-nhin-su-menh/',
			'cong-nghe-san-pham'        => '/gioi-thieu/cong-nghe-san-pham/',
			'co-so-vat-chat'            => '/gioi-thieu/co-so-vat-chat/',
			'quy-trinh-chuan-y-khoa'    => '/gioi-thieu/quy-trinh-chuan-y-khoa/',
		)
	);
}

add_action(
	'template_redirect',
	function () {
		$path = trim( (string) wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ), '/' );
		$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		if ( $home && 0 === strpos( $path, $home ) ) {
			$path = trim( substr( $path, strlen( $home ) ), '/' );
		}
		if ( '' === $path ) {
			return;
		}

		// The clinic books through SalonHero: send the old /dat-lich/ address there while no local page exists.
		if ( 'dat-lich' === $path && is_404() && qd_clinic( 'booking_url' ) ) {
			wp_redirect( qd_clinic( 'booking_url' ), 302 ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- External booking host set by the admin.
			exit;
		}

		$target = qd_redirect_map()[ $path ] ?? '';
		if ( $target ) {
			$target = home_url( $target );
		} elseif ( is_404() ) {
			$target = qd_resolve_url( home_url( "/$path/" ) );
		}
		if ( $target && trim( (string) wp_parse_url( $target, PHP_URL_PATH ), '/' ) !== trim( $home . '/' . $path, '/' ) ) {
			wp_safe_redirect( $target, 301 );
			exit;
		}
	},
	5
);

/**
 * Trim text to a meta-description length on a word boundary.
 *
 * @param string $text Text.
 * @param int    $max  Max characters.
 */
function qd_trim_description( $text, $max = 155 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( $text ) ) ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $max - 1 );
	$cut = preg_replace( '/\s+\S*$/u', '', $cut );
	return rtrim( $cut, " ,.;:–-" ) . '…';
}

/**
 * Automatic description for the current request ('' when there is nothing better).
 */
function qd_auto_description() {
	$clinic = 'Phòng khám Da liễu Thẩm mỹ Quang Đăng';
	if ( is_front_page() ) {
		return qd_trim_description( "$clinic tại Quỳnh Lưu, Nghệ An: điều trị mụn, thâm, nám, sẹo, xóa xăm, triệt lông, trẻ hóa da. Bác sĩ da liễu trực tiếp thăm khám. Đặt lịch: " . qd_clinic( 'hotline' ) . '.' );
	}
	if ( is_post_type_archive( 'dich-vu' ) ) {
		return qd_trim_description( "Bảng dịch vụ của $clinic (Quỳnh Lưu, Nghệ An): điều trị mụn, thâm, nám, sẹo, xóa xăm, chăm sóc da, filler, botox. Xem ai phù hợp, số buổi và giá từ." );
	}
	if ( is_post_type_archive( 'khoa-hoc' ) ) {
		return qd_trim_description( "Khóa học chăm sóc da cơ bản và chuyên sâu tại $clinic, Quỳnh Lưu, Nghệ An: học lý thuyết và thực hành cùng bác sĩ, kỹ thuật viên. Xem lịch học và học phí." );
	}
	if ( is_category() ) {
		$term = get_queried_object();
		$desc = term_description( $term );
		return qd_trim_description( $desc ? $desc : single_cat_title( '', false ) . " – bài viết từ bác sĩ $clinic, Quỳnh Lưu, Nghệ An." );
	}
	if ( ! is_singular() ) {
		return '';
	}

	$post = get_queried_object();
	if ( get_post_meta( $post->ID, 'rank_math_description', true ) || get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true ) ) {
		return ''; // The editor's own description wins.
	}
	$title = get_the_title( $post );

	if ( 'dich-vu' === $post->post_type ) {
		$suits    = trim( strtok( (string) get_post_meta( $post->ID, '_qd_suits', true ), "\n" ) );
		$lead     = $suits ? 'Phù hợp: ' . mb_strtolower( mb_substr( $suits, 0, 1 ) ) . mb_substr( $suits, 1 ) : ( has_excerpt( $post ) ? get_the_excerpt( $post ) : '' );
		$sessions = qd_service_facts( $post )['sessions'];
		$text     = "$title tại $clinic (Quỳnh Lưu, Nghệ An). " . ( $lead ? rtrim( $lead, '.' ) . '. ' : '' )
			. 'Bác sĩ trực tiếp thăm khám' . ( $sessions ? ", liệu trình $sessions" : '' ) . '. Đặt lịch: ' . qd_clinic( 'hotline' ) . '.';
		return qd_trim_description( $text );
	}

	$body = has_excerpt( $post ) ? get_the_excerpt( $post ) : $post->post_content;
	$body = qd_trim_description( $body, 400 );
	return qd_trim_description( $body ? $body : "$title – $clinic, Quỳnh Lưu, Nghệ An." );
}

$qd_description_filter = function ( $description ) {
	$auto = qd_auto_description();
	if ( '' === $auto ) {
		return $description;
	}
	// On the home page keep a real description someone set in the SEO plugin.
	if ( is_front_page() && mb_strlen( (string) $description ) >= 70 ) {
		return $description;
	}
	return $auto;
};
foreach ( array( 'rank_math/frontend/description', 'rank_math/opengraph/facebook/og_description', 'rank_math/opengraph/twitter/twitter_description', 'wpseo_metadesc', 'wpseo_opengraph_desc' ) as $qd_hook ) {
	add_filter( $qd_hook, $qd_description_filter, 20 );
}

/**
 * Create the pages and categories the theme's flows link to, once, if they are missing.
 * Templates page-dat-lich.php etc. pick the pages up by slug.
 */
add_action(
	'admin_init',
	function () {
		if ( get_option( 'qd_core_content_v1' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// No /dat-lich/ page while booking goes through SalonHero (Customizer "Link đặt lịch chính").
		$pages = qd_clinic( 'booking_url' ) ? array() : array( 'dat-lich' => 'Đặt lịch hẹn' );
		$pages += array(
			'lien-he'        => 'Liên hệ & chỉ đường',
			'tim-lieu-trinh' => 'Tìm liệu trình phù hợp',
		);
		foreach ( $pages as $slug => $title ) {
			if ( ! get_page_by_path( $slug ) ) {
				wp_insert_post(
					array(
						'post_type'   => 'page',
						'post_status' => 'publish',
						'post_name'   => $slug,
						'post_title'  => $title,
					)
				);
			}
		}
		$cats = array(
			'cam-nang-lam-dep' => 'Cẩm nang làm đẹp',
			'su-kien-uu-dai'   => 'Sự kiện – Ưu đãi',
		);
		foreach ( $cats as $slug => $name ) {
			if ( ! get_term_by( 'slug', $slug, 'category' ) ) {
				wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
			}
		}
		delete_transient( 'qd_url_map_v2' );
		update_option( 'qd_core_content_v1', time(), false );
	}
);

/**
 * Concern tiles and chips (home, search, 404, quiz) link straight to the canonical page. A concern
 * with no category page of its own falls back to a representative service, never the services
 * archive: qd_concern_for_post() matches by URL prefix and /dich-vu/ would match every service.
 */
add_filter(
	'qd_concerns',
	function ( $concerns ) {
		$fallback = array( 'filler-botox' => '/dich-vu/filler/' );
		foreach ( $concerns as $key => &$concern ) {
			$url = qd_resolve_url( $concern['url'] );
			if ( '' === $url && isset( $fallback[ $key ] ) ) {
				$url = qd_resolve_url( home_url( $fallback[ $key ] ) );
			}
			if ( $url ) {
				$concern['url'] = $url;
			}
		}
		return $concerns;
	},
	20
);

/**
 * Title with search intent and location, used when the editor hasn't set one ('' = keep the plugin's).
 */
function qd_auto_title() {
	$brand = 'Quang Đăng';
	if ( is_post_type_archive( 'dich-vu' ) ) {
		return "Dịch vụ da liễu thẩm mỹ tại Quỳnh Lưu, Nghệ An | $brand";
	}
	if ( is_post_type_archive( 'khoa-hoc' ) ) {
		return "Khóa học chăm sóc da tại Nghệ An | $brand";
	}
	if ( ! is_singular( 'dich-vu' ) ) {
		return '';
	}
	$post = get_queried_object();
	if ( get_post_meta( $post->ID, 'rank_math_title', true ) || get_post_meta( $post->ID, '_yoast_wpseo_title', true ) ) {
		return '';
	}
	$name  = wp_strip_all_tags( get_the_title( $post ) );
	$title = "$name tại Quỳnh Lưu, Nghệ An | $brand";
	return mb_strlen( $title ) <= 60 ? $title : "$name | $brand";
}
foreach ( array( 'rank_math/frontend/title', 'wpseo_title', 'pre_get_document_title' ) as $qd_hook ) {
	add_filter(
		$qd_hook,
		function ( $title ) use ( $qd_hook ) {
			if ( 'pre_get_document_title' === $qd_hook && qd_seo_plugin_active() ) {
				return $title;
			}
			$auto = qd_auto_title();
			return $auto ? $auto : $title;
		},
		20
	);
}

/**
 * Keep redirected duplicates (qd_redirect_map()) out of the XML sitemap.
 */
$qd_sitemap_filter = function ( $entry ) {
	$loc = is_array( $entry ) ? ( $entry['loc'] ?? '' ) : '';
	$path = trim( (string) wp_parse_url( $loc, PHP_URL_PATH ), '/' );
	return $path && isset( qd_redirect_map()[ $path ] ) ? false : $entry;
};
add_filter( 'rank_math/sitemap/entry', $qd_sitemap_filter );
add_filter( 'wpseo_sitemap_entry', $qd_sitemap_filter );

/**
 * One clinic entity: Rank Math's site entity (#organization) becomes a plain Organization with the
 * clinic's name, so the theme's #clinic node is the only MedicalClinic. The @id is kept, so the
 * WebSite / publisher references still resolve.
 */
add_filter(
	'rank_math/json_ld',
	function ( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		foreach ( $data as &$entity ) {
			if ( ! is_array( $entity ) || ! str_ends_with( (string) ( $entity['@id'] ?? '' ), '#organization' ) ) {
				continue;
			}
			$types = (array) ( $entity['@type'] ?? array() );
			if ( array_intersect( $types, array( 'LocalBusiness', 'MedicalClinic', 'MedicalBusiness', 'HealthAndBeautyBusiness', 'BeautySalon', 'DaySpa', 'Physician' ) ) ) {
				$entity['@type'] = 'Organization';
				$entity['name']  = qd_clinic( 'name' );
				unset( $entity['openingHours'], $entity['openingHoursSpecification'], $entity['priceRange'], $entity['geo'] );
			}
		}
		return $data;
	},
	99
);

/**
 * One-time content fix: the tattoo-removal service was published as "Xóa xăm tatoo" / xoa-xam-tatoo.
 * WordPress keeps the old slug, so the old URL 301s to the new one.
 */
add_action(
	'admin_init',
	function () {
		if ( get_option( 'qd_content_fix_v2' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$post = get_posts(
			array(
				'name'           => 'xoa-xam-tatoo',
				'post_type'      => 'dich-vu',
				'post_status'    => 'any',
				'posts_per_page' => 1,
			)
		);
		if ( $post ) {
			wp_update_post(
				array(
					'ID'         => $post[0]->ID,
					'post_name'  => 'xoa-xam-hinh-xam',
					'post_title' => 'Xóa hình xăm',
				)
			);
		}
		delete_transient( 'qd_url_map_v2' );
		update_option( 'qd_content_fix_v2', time(), false );
	}
);
