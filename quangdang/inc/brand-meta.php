<?php
/**
 * Brand icons, social sharing and the basic head tags search engines read.
 *
 * - Favicon: the bundled icons are printed and /favicon.ico is served unless a Site Icon is set in
 *   Customize → Site Identity (WordPress then prints its own links and redirects /favicon.ico to it).
 * - Head tags: meta description, canonical, Open Graph and Twitter tags, each added only when nothing
 *   else (Rank Math, Yoast, SEOPress…) has already printed it. The check looks at what was actually
 *   printed into <head>, not at which plugins are installed, so an installed-but-silent SEO plugin
 *   does not leave the page without tags. Set a default share image in the SEO plugin to
 *   assets/images/brand/og-default.png.
 * - The page language is declared as Vietnamese.
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

// The site is Vietnamese whatever the WordPress site language says (it often stays en-US).
add_filter(
	'language_attributes',
	function ( $output ) {
		return preg_replace( '/lang="[^"]*"/', 'lang="vi"', $output );
	}
);

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

// Buffer everything printed into <head> before the late hook below, so it can see what is already there.
add_action(
	'wp_head',
	function () {
		$GLOBALS['qd_head_level'] = ob_get_level() + 1;
		ob_start();
	},
	0
);

add_action(
	'wp_head',
	function () {
		global $wp;

		$level = $GLOBALS['qd_head_level'] ?? null;
		if ( null === $level || ob_get_level() !== $level ) {
			return; // Something else changed the buffer stack: leave the head untouched.
		}
		$printed = (string) ob_get_clean();
		echo $printed; // phpcs:ignore WordPress.Security.EscapeOutput

		if ( is_404() || is_search() ) {
			return;
		}

		$is_post = is_singular( array( 'post', 'dich-vu', 'khoa-hoc', 'bac-si' ) );
		$title   = wp_get_document_title();
		$desc    = '';
		if ( $is_post ) {
			$desc = wp_strip_all_tags( get_the_excerpt() );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$desc = wp_strip_all_tags( term_description() );
		}
		$desc = $desc ? wp_trim_words( $desc, 30, '…' ) : 'Phòng khám chuyên khoa Da liễu – Thẩm mỹ tại Quỳnh Lưu, Nghệ An. Bác sĩ trực tiếp thăm khám, phác đồ riêng cho từng làn da.';
		$url  = is_singular() ? get_permalink() : home_url( $wp->request ? user_trailingslashit( $wp->request ) : '/' );

		$out   = '';
		$added = array();

		if ( ! preg_match( '/name=["\']description["\']/i', $printed ) ) {
			$out    .= sprintf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
			$added[] = 'description';
		}

		if ( ! preg_match( '/rel=["\']canonical["\']/i', $printed ) ) {
			$out    .= sprintf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
			$added[] = 'canonical';
		}

		if ( ! preg_match( '/property=["\']og:/i', $printed ) ) {
			$image = $is_post ? get_the_post_thumbnail_url( null, 'large' ) : '';
			// Facebook and Zalo don't reliably read WebP share images, so those pages use the default PNG.
			if ( $image && preg_match( '/\.webp(\?|$)/i', $image ) ) {
				$image = '';
			}
			$custom = (bool) $image;
			if ( ! $image ) {
				$image = qd_brand_url( 'og-default.png' );
			}

			$tags = array(
				array( 'property', 'og:locale', 'vi_VN' ),
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
				$out .= sprintf( '<meta %s="%s" content="%s">' . "\n", esc_attr( $attr ), esc_attr( $key ), esc_attr( $value ) );
			}
			$added[] = 'open-graph';
		}

		if ( $out ) {
			echo '<!-- quangdang head: added ' . esc_html( implode( ', ', $added ) ) . " -->\n" . $out; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	},
	98
);
