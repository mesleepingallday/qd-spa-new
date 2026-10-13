<?php
/**
 * Brand icons and social sharing.
 *
 * - Favicon: the bundled icons are printed and /favicon.ico is served unless a Site Icon is set in
 *   Customize → Site Identity (WordPress then prints its own links and redirects /favicon.ico to it).
 * - Open Graph / Twitter tags with the default share image, only when no SEO plugin is active
 *   (Rank Math, Yoast and SEOPress print their own; set the default share image in the plugin to
 *   assets/images/brand/og-default.png).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of a file in assets/images/brand/.
 *
 * @param string $file File name.
 */
function qd_brand_url( $file ) {
	return QD_URI . '/assets/images/brand/' . $file;
}

add_action(
	'wp_head',
	function () {
		if ( has_site_icon() ) {
			return;
		}
		printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( home_url( '/favicon.ico' ) ) );
		printf( '<link rel="icon" type="image/png" sizes="48x48" href="%s">' . "\n", esc_url( qd_brand_url( 'icon-48.png' ) ) );
		printf( '<link rel="icon" type="image/png" sizes="192x192" href="%s">' . "\n", esc_url( qd_brand_url( 'icon-192.png' ) ) );
		printf( '<link rel="apple-touch-icon" sizes="180x180" href="%s">' . "\n", esc_url( qd_brand_url( 'apple-touch-icon.png' ) ) );
	},
	3
);

// Crawlers and old browsers ask for /favicon.ico directly; answer with the bundled one.
add_action(
	'template_redirect',
	function () {
		if ( ! is_favicon() || has_site_icon() ) {
			return;
		}
		$file = QD_DIR . '/assets/images/brand/favicon.ico';
		if ( ! is_readable( $file ) ) {
			return;
		}
		status_header( 200 );
		header( 'Content-Type: image/x-icon' );
		header( 'Content-Length: ' . filesize( $file ) );
		header( 'Cache-Control: public, max-age=604800' );
		readfile( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
);

add_action(
	'wp_head',
	function () {
		global $wp;

		if ( qd_seo_plugin_active() ) {
			return;
		}

		$is_post = is_singular( array( 'post', 'dich-vu', 'khoa-hoc', 'bac-si' ) );
		$title   = wp_get_document_title();
		$desc    = $is_post ? wp_strip_all_tags( get_the_excerpt() ) : '';
		$desc    = $desc ? wp_trim_words( $desc, 30, '…' ) : 'Phòng khám chuyên khoa Da liễu – Thẩm mỹ tại Quỳnh Lưu, Nghệ An. Bác sĩ trực tiếp thăm khám, phác đồ riêng cho từng làn da.';
		$url     = is_singular() ? get_permalink() : home_url( $wp->request ? user_trailingslashit( $wp->request ) : '/' );
		$image   = $is_post ? get_the_post_thumbnail_url( null, 'large' ) : '';
		// Facebook and Zalo don't reliably read WebP share images, so those pages use the default PNG.
		if ( $image && preg_match( '/\.webp(\?|$)/i', $image ) ) {
			$image = '';
		}
		$custom = (bool) $image;
		if ( ! $image ) {
			$image = qd_brand_url( 'og-default.png' );
		}

		$tags = array(
			array( 'property', 'og:locale', get_locale() ),
			array( 'property', 'og:type', is_singular( 'post' ) ? 'article' : 'website' ),
			array( 'property', 'og:site_name', get_bloginfo( 'name' ) ),
			array( 'property', 'og:title', $title ),
			array( 'property', 'og:description', $desc ),
			array( 'property', 'og:url', $url ),
			array( 'property', 'og:image', $image ),
		);
		if ( ! $custom ) {
			$tags[] = array( 'property', 'og:image:width', '1200' );
			$tags[] = array( 'property', 'og:image:height', '630' );
			$tags[] = array( 'property', 'og:image:alt', qd_clinic( 'name' ) );
		}
		$tags[] = array( 'name', 'twitter:card', 'summary_large_image' );

		foreach ( $tags as list( $attr, $key, $value ) ) {
			printf( '<meta %s="%s" content="%s">' . "\n", esc_attr( $attr ), esc_attr( $key ), esc_attr( $value ) );
		}
	},
	5
);
