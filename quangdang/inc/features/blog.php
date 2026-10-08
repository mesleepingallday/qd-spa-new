<?php
/**
 * Blog area: news list (home.php), category/tag archives, the article (single.php) and search.
 *
 * - Page CSS/JS registration
 * - Search also finds services (dich-vu)
 * - List paging: page 1 = 1 featured + 9 cards, then 9 per page (clean 3-column rows)
 * - Sự kiện – Ưu đãi: running promotions first, ended ones last
 * - Article body: ids on H2 (table of contents) + inline service card after the 2nd section
 * - Meta boxes on posts: "Ưu đãi" and "Tham vấn y khoa"
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */

add_filter(
	'qd_page_styles',
	function ( $styles ) {
		$styles['qd-blog'] = array(
			'assets/css/pages/blog.css',
			fn() => is_home() || is_category() || is_tag() || is_singular( 'post' ) || is_search(),
		);
		return $styles;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_singular( 'post' ) ) {
			wp_enqueue_script(
				'qd-article',
				QD_URI . '/assets/js/article.js',
				array(),
				qd_asset_ver( 'assets/js/article.js' ),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
);

/* -------------------------------------------------------------------------
 * Queries
 * ---------------------------------------------------------------------- */

/** Number of cards on the first list page (1 featured + 9) and on the following pages. */
const QD_BLOG_FIRST_PAGE = 10;
const QD_BLOG_PER_PAGE   = 9;

/**
 * Is this the main query of the news list, a category or a tag?
 *
 * @param WP_Query $q Query.
 */
function qd_is_blog_list_query( $q ) {
	return $q->is_main_query() && ! is_admin() && ( $q->is_home() || $q->is_category() || $q->is_tag() );
}

add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}

		// Search: articles and services (the page groups them).
		if ( $q->is_search() ) {
			$q->set( 'post_type', array( 'post', 'dich-vu' ) );
			$q->set( 'posts_per_page', 60 );
			return;
		}

		// Lists: page 1 has 10 posts (featured + 3x3), later pages 9, using an offset.
		if ( qd_is_blog_list_query( $q ) ) {
			$paged = max( 1, (int) $q->get( 'paged' ) );
			if ( 1 === $paged ) {
				$q->set( 'posts_per_page', QD_BLOG_FIRST_PAGE );
			} else {
				$q->set( 'posts_per_page', QD_BLOG_PER_PAGE );
				$q->set( 'offset', QD_BLOG_FIRST_PAGE + ( $paged - 2 ) * QD_BLOG_PER_PAGE );
			}
		}
	}
);

// Correct page count for the 10 + 9 + 9… split.
add_filter(
	'the_posts',
	function ( $posts, $q ) {
		if ( qd_is_blog_list_query( $q ) ) {
			$total             = (int) $q->found_posts;
			$q->max_num_pages = $total <= QD_BLOG_FIRST_PAGE ? 1 : 1 + (int) ceil( ( $total - QD_BLOG_FIRST_PAGE ) / QD_BLOG_PER_PAGE );
		}
		return $posts;
	},
	10,
	2
);

// Sự kiện – Ưu đãi: running (and posts without dates) first, then upcoming, then ended.
add_filter(
	'posts_clauses',
	function ( $clauses, $q ) {
		if ( is_admin() || ! $q->is_main_query() || ! $q->is_category( 'su-kien-uu-dai' ) ) {
			return $clauses;
		}
		global $wpdb;
		$today              = current_datetime()->format( 'Y-m-d' );
		$clauses['join']   .= " LEFT JOIN {$wpdb->postmeta} AS qd_pe ON ({$wpdb->posts}.ID = qd_pe.post_id AND qd_pe.meta_key = '_qd_promo_end')";
		$clauses['join']   .= " LEFT JOIN {$wpdb->postmeta} AS qd_ps ON ({$wpdb->posts}.ID = qd_ps.post_id AND qd_ps.meta_key = '_qd_promo_start')";
		$clauses['orderby'] = $wpdb->prepare(
			"CASE WHEN qd_pe.meta_value IS NOT NULL AND qd_pe.meta_value <> '' AND qd_pe.meta_value < %s THEN 2 WHEN qd_ps.meta_value IS NOT NULL AND qd_ps.meta_value <> '' AND qd_ps.meta_value > %s THEN 1 ELSE 0 END ASC, ", // phpcs:ignore WordPress.DB.PreparedSQL
			$today,
			$today
		) . $clauses['orderby'];
		return $clauses;
	},
	10,
	2
);

// /tin-tuc/page/2/ would otherwise be read as the category "page/2" (category base = tin-tuc).
add_action(
	'init',
	function () {
		$news = (int) get_option( 'page_for_posts' );
		$path = $news ? get_page_uri( $news ) : '';
		if ( $path ) {
			add_rewrite_rule( '^' . preg_quote( $path, '#' ) . '/page/([0-9]{1,})/?$', 'index.php?pagename=' . $path . '&paged=$matches[1]', 'top' );
		}
	}
);

/* -------------------------------------------------------------------------
 * Helpers
 * ---------------------------------------------------------------------- */

/**
 * The service category that matches an article's topic (tag slug = service category slug).
 *
 * @param int|WP_Post|null $post Post.
 * @return WP_Post|null
 */
function qd_post_topic_service( $post = null ) {
	$post = get_post( $post );
	$tags = $post ? get_the_tags( $post->ID ) : false;
	foreach ( (array) $tags as $tag ) {
		$service = get_page_by_path( $tag->slug, OBJECT, 'dich-vu' );
		if ( $service && 'publish' === $service->post_status && 0 === (int) $service->post_parent ) {
			return $service;
		}
	}
	return null;
}

/**
 * Doctor who reviewed the article ("Tham vấn y khoa").
 *
 * @param int|WP_Post|null $post Post.
 * @return WP_Post|null
 */
function qd_post_reviewer( $post = null ) {
	$post = get_post( $post );
	$id   = $post ? (int) get_post_meta( $post->ID, '_qd_reviewer', true ) : 0;
	$doc  = $id ? get_post( $id ) : null;
	return ( $doc && 'bac-si' === $doc->post_type && 'publish' === $doc->post_status ) ? $doc : null;
}

/**
 * Related articles: same topic first, then same category. Max $count.
 *
 * @param int|WP_Post|null $post  Post.
 * @param int              $count How many.
 * @return WP_Post[]
 */
function qd_post_related( $post = null, $count = 3 ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}
	$found = array();
	$tags  = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
	if ( $tags ) {
		$found = get_posts(
			array(
				'post__not_in'   => array( $post->ID ),
				'tag__in'        => $tags,
				'posts_per_page' => $count,
				'no_found_rows'  => true,
			)
		);
	}
	if ( count( $found ) < $count ) {
		$cats = wp_get_post_categories( $post->ID );
		if ( $cats ) {
			$more  = get_posts(
				array(
					'post__not_in'   => array_merge( array( $post->ID ), wp_list_pluck( $found, 'ID' ) ),
					'category__in'   => $cats,
					'posts_per_page' => $count - count( $found ),
					'no_found_rows'  => true,
				)
			);
			$found = array_merge( $found, $more );
		}
	}
	return $found;
}

/* -------------------------------------------------------------------------
 * Article body: H2 ids (for the table of contents) + inline service card
 * ---------------------------------------------------------------------- */

/**
 * Render the article body. Returns the HTML and the list of H2 headings.
 * Runs the normal the_content filters, plus ours (only while this function runs).
 *
 * @return array{html:string,toc:array<int,array{id:string,label:string}>}
 */
function qd_article_body() {
	$GLOBALS['qd_article_pass'] = array( 'toc' => array() );
	$html                       = apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	$toc                        = $GLOBALS['qd_article_pass']['toc'];
	unset( $GLOBALS['qd_article_pass'] );
	return array(
		'html' => str_replace( ']]>', ']]&gt;', $html ),
		'toc'  => $toc,
	);
}

add_filter(
	'the_content',
	function ( $content ) {
		if ( ! isset( $GLOBALS['qd_article_pass'] ) ) {
			return $content;
		}

		// 1. ids on H2.
		$toc  = array();
		$used = array();
		$content = preg_replace_callback(
			'#<h2([^>]*)>(.*?)</h2>#is',
			function ( $m ) use ( &$toc, &$used ) {
				$label = trim( html_entity_decode( wp_strip_all_tags( $m[2] ), ENT_QUOTES, 'UTF-8' ) );
				if ( '' === $label ) {
					return $m[0];
				}
				$attrs = $m[1];
				if ( preg_match( '/\sid=(["\'])(.*?)\1/', $attrs, $im ) ) {
					$id = $im[2];
				} else {
					$base = sanitize_title( $label ) ?: 'muc';
					$id   = $base;
					for ( $i = 2; in_array( $id, $used, true ); $i++ ) {
						$id = $base . '-' . $i;
					}
					$attrs .= ' id="' . esc_attr( $id ) . '"';
				}
				$used[] = $id;
				$toc[]  = array(
					'id'    => $id,
					'label' => $label,
				);
				return '<h2' . $attrs . '>' . $m[2] . '</h2>';
			},
			$content
		);
		$GLOBALS['qd_article_pass']['toc'] = $toc;

		// 2. Inline service card: after the 2nd section (before the 3rd H2), or at the end
		// of a 2-section article; with fewer headings, after the 3rd paragraph.
		$service = qd_post_topic_service();
		if ( ! $service ) {
			return $content;
		}
		$card = qd_get_part( 'template-parts/blog/service-inline', array( 'service' => $service ) );
		if ( preg_match_all( '#<h2[\s>]#i', $content, $hs, PREG_OFFSET_CAPTURE ) && count( $hs[0] ) >= 3 ) {
			$pos = $hs[0][2][1];
			return substr( $content, 0, $pos ) . $card . substr( $content, $pos );
		}
		if ( count( $toc ) >= 2 ) {
			return $content . $card;
		}
		$parts = preg_split( '#(</p>)#i', $content, 4, PREG_SPLIT_DELIM_CAPTURE );
		if ( count( $parts ) >= 7 ) {
			return $parts[0] . $parts[1] . $parts[2] . $parts[3] . $parts[4] . $parts[5] . $card . $parts[6];
		}
		return $content;
	},
	12
);

/* -------------------------------------------------------------------------
 * Meta boxes on posts
 * ---------------------------------------------------------------------- */

add_action(
	'init',
	function () {
		if ( ! is_admin() ) {
			return;
		}
		qd_meta_box(
			'qd-promo',
			'Ưu đãi (chỉ dùng cho bài trong "Sự kiện – Ưu đãi")',
			'post',
			array(
				'_qd_promo_label' => array(
					'label' => 'Nhãn ưu đãi',
					'help'  => 'Chữ ngắn hiện trên thẻ bài, VD: Giảm 50%, Sinh viên -20%.',
				),
				'_qd_promo_start' => array(
					'label' => 'Bắt đầu',
					'type'  => 'date',
				),
				'_qd_promo_end'   => array(
					'label' => 'Kết thúc',
					'type'  => 'date',
					'help'  => 'Có ngày kết thúc thì bài hiện khung ưu đãi, nút "Đặt lịch nhận ưu đãi" và tự chuyển sang "Đã kết thúc" khi hết hạn. Bỏ trống nếu không phải ưu đãi.',
				),
			)
		);
		qd_meta_box(
			'qd-reviewer',
			'Tham vấn y khoa',
			'post',
			array(
				'_qd_reviewer' => array(
					'label'   => 'Bác sĩ tham vấn',
					'type'    => 'select',
					'help'    => 'Hiện dưới tiêu đề bài ("Tham vấn y khoa: …"), cuối bài và trong dữ liệu có cấu trúc.',
					'options' => fn() => array( '' => '— Không hiển thị —' ) + wp_list_pluck(
						get_posts(
							array(
								'post_type'      => 'bac-si',
								'posts_per_page' => 50,
								'orderby'        => 'menu_order',
								'order'          => 'ASC',
							)
						),
						'post_title',
						'ID'
					),
				),
			)
		);
	},
	20
);
