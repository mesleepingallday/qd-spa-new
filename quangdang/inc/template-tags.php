<?php
/**
 * Template tags: breadcrumbs, section heads, post meta.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is an SEO plugin handling titles, breadcrumbs schema and sitemaps?
 */
function qd_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Breadcrumb trail for the current request: list of [label, url|null].
 *
 * @return array<int,array{0:string,1:?string}>
 */
function qd_breadcrumb_trail() {
	$trail = array( array( 'Trang chủ', home_url( '/' ) ) );
	$news  = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/tin-tuc/' );

	if ( is_singular( 'dich-vu' ) ) {
		$trail[] = array( 'Dịch vụ', get_post_type_archive_link( 'dich-vu' ) );
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_post_type_archive( 'dich-vu' ) ) {
		$trail[] = array( 'Dịch vụ', null );
	} elseif ( is_singular( 'khoa-hoc' ) ) {
		$trail[] = array( 'Đào tạo', get_post_type_archive_link( 'khoa-hoc' ) );
		$trail[] = array( get_the_title(), null );
	} elseif ( is_post_type_archive( 'khoa-hoc' ) ) {
		$trail[] = array( 'Đào tạo', null );
	} elseif ( is_singular( 'bac-si' ) ) {
		$trail[] = array( 'Giới thiệu', home_url( '/gioi-thieu/' ) );
		$trail[] = array( 'Đội ngũ bác sĩ', home_url( '/gioi-thieu/doi-ngu-bac-si/' ) );
		$trail[] = array( get_the_title(), null );
	} elseif ( is_singular( 'post' ) ) {
		$trail[] = array( 'Tin tức', $news );
		$cats    = get_the_category();
		if ( $cats ) {
			$trail[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_category() || is_tag() ) {
		$trail[] = array( 'Tin tức', $news );
		$trail[] = array( single_term_title( '', false ), null );
	} elseif ( is_home() ) {
		$trail[] = array( 'Tin tức', null );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_search() ) {
		$trail[] = array( 'Tìm kiếm: ' . get_search_query(), null );
	} elseif ( is_404() ) {
		$trail[] = array( 'Không tìm thấy trang', null );
	}

	return apply_filters( 'qd_breadcrumb_trail', $trail );
}

/**
 * Breadcrumb nav. The last crumb is the current page (not a link).
 *
 * @param string $class Extra class.
 */
function qd_breadcrumbs( $class = '' ) {
	$trail = qd_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return;
	}
	echo '<nav class="breadcrumb ' . esc_attr( $class ) . '" aria-label="Đường dẫn"><ol>';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => list( $label, $url ) ) {
		echo '<li>';
		if ( $url && $i !== $last ) {
			echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
			qd_the_icon( 'chevron-right', array( 'size' => 14 ) );
		} else {
			echo '<span aria-current="page">' . esc_html( $label ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Section heading: title, optional lead text, optional "see all" link aligned right.
 *
 * @param array $args title, desc, link_label, link_url, id, align (start|center), tag.
 */
function qd_section_head( $args ) {
	$tag   = $args['tag'] ?? 'h2';
	$align = $args['align'] ?? 'start';
	echo '<div class="section-head section-head--' . esc_attr( $align ) . '">';
	echo '<div class="section-head__text">';
	printf(
		'<%1$s class="section-title"%2$s>%3$s</%1$s>',
		tag_escape( $tag ),
		! empty( $args['id'] ) ? ' id="' . esc_attr( $args['id'] ) . '"' : '',
		esc_html( $args['title'] )
	);
	if ( ! empty( $args['desc'] ) ) {
		echo '<p class="section-lead">' . esc_html( $args['desc'] ) . '</p>';
	}
	echo '</div>';
	if ( ! empty( $args['link_url'] ) ) {
		echo '<a class="link-more" href="' . esc_url( $args['link_url'] ) . '">' . esc_html( $args['link_label'] ?? 'Xem tất cả' ) . qd_icon( 'arrow-right', array( 'size' => 16 ) ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}

/**
 * "08/10/2026 · 6 phút đọc" line for posts.
 *
 * @param int|WP_Post|null $post Post.
 */
function qd_post_meta_line( $post = null ) {
	$post = get_post( $post );
	return sprintf(
		'<span class="meta-line"><time datetime="%1$s">%2$s</time><span aria-hidden="true">·</span><span>%3$d phút đọc</span></span>',
		esc_attr( get_the_modified_date( 'c', $post ) ),
		esc_html( get_the_modified_date( 'd/m/Y', $post ) ),
		qd_reading_time( $post )
	);
}

/**
 * Promotion status from _qd_promo_start / _qd_promo_end (Y-m-d, site timezone).
 *
 * @param int|WP_Post|null $post Post.
 * @return array{state:string,label:string}|null null when the post is not a promotion.
 */
function qd_promo_status( $post = null ) {
	$post = get_post( $post );
	$end  = $post ? (string) get_post_meta( $post->ID, '_qd_promo_end', true ) : '';
	if ( ! $end ) {
		return null;
	}
	$start = (string) get_post_meta( $post->ID, '_qd_promo_start', true );
	$today = current_datetime()->setTime( 0, 0 );
	$tz    = wp_timezone();
	$s     = $start ? new DateTimeImmutable( $start, $tz ) : null;
	$e     = new DateTimeImmutable( $end, $tz );

	if ( $e < $today ) {
		return array( 'state' => 'ended', 'label' => 'Đã kết thúc' );
	}
	if ( $s && $s > $today ) {
		return array( 'state' => 'upcoming', 'label' => 'Bắt đầu ' . $s->format( 'd/m' ) );
	}
	$days = (int) $today->diff( $e )->days;
	return array(
		'state' => 'active',
		'label' => 0 === $days ? 'Kết thúc hôm nay' : 'Còn ' . $days . ' ngày',
	);
}

/**
 * Show demo placeholders (missing real photos) outside production only.
 */
function qd_show_placeholders() {
	return 'production' !== wp_get_environment_type();
}
