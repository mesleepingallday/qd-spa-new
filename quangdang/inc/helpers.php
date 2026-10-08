<?php
/**
 * Rendering and formatting helpers.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon.
 *
 * @param string $name Lucide icon name (see inc/icons.php).
 * @param array  $args size (px), class, label (makes it non-decorative).
 */
function qd_icon( $name, $args = array() ) {
	$paths = qd_icon_paths();
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	$size  = (int) ( $args['size'] ?? 20 );
	$class = trim( 'icon icon-' . $name . ' ' . ( $args['class'] ?? '' ) );
	$a11y  = empty( $args['label'] )
		? 'aria-hidden="true" focusable="false"'
		: 'role="img" aria-label="' . esc_attr( $args['label'] ) . '"';

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" %3$s>%4$s</svg>',
		esc_attr( $class ),
		$size,
		$a11y,
		$paths[ $name ] // Static markup from inc/icons.php.
	);
}

/**
 * Echo an icon.
 *
 * @param string $name Icon name.
 * @param array  $args See qd_icon().
 */
function qd_the_icon( $name, $args = array() ) {
	echo qd_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from static markup.
}

/**
 * Format a VND amount: 1500000 → "1.500.000đ".
 *
 * @param int|string $amount Amount in VND.
 */
function qd_price( $amount ) {
	$amount = (int) $amount;
	return $amount > 0 ? number_format( $amount, 0, ',', '.' ) . 'đ' : '';
}

/**
 * Split a textarea value into trimmed, non-empty lines.
 *
 * @param string $text Raw text.
 * @return string[]
 */
function qd_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

/**
 * Split "a | b | c" lines into rows of columns.
 *
 * @param string $text Raw text.
 * @param int    $cols Number of columns to pad to.
 * @return array<int,string[]>
 */
function qd_pipe_rows( $text, $cols = 2 ) {
	$rows = array();
	foreach ( qd_lines( $text ) as $line ) {
		$parts  = array_map( 'trim', explode( '|', $line ) );
		$rows[] = array_pad( $parts, $cols, '' );
	}
	return $rows;
}

/**
 * Theme image (design asset, not media library) — resolves the real file first,
 * then the generated placeholder. Drop a GPT image at assets/images/{key}.png|jpg|webp
 * and it replaces the placeholder with no code change.
 *
 * @param string $key Path without extension, e.g. "hero/home-portrait".
 * @return array{url:string,width:int,height:int,placeholder:bool}|null
 */
function qd_asset_image( $key ) {
	static $cache = array();
	if ( array_key_exists( $key, $cache ) ) {
		return $cache[ $key ];
	}
	$cache[ $key ] = null;
	foreach ( array( '', '_placeholders/' ) as $dir ) {
		foreach ( array( 'webp', 'jpg', 'jpeg', 'png', 'svg' ) as $ext ) {
			$rel  = 'assets/images/' . $dir . $key . '.' . $ext;
			$file = QD_DIR . '/' . $rel;
			if ( file_exists( $file ) ) {
				$size          = 'svg' === $ext ? array( 0, 0 ) : (array) wp_getimagesize( $file );
				$cache[ $key ] = array(
					'url'         => QD_URI . '/' . $rel . '?v=' . filemtime( $file ),
					'width'       => (int) ( $size[0] ?? 0 ),
					'height'      => (int) ( $size[1] ?? 0 ),
					'placeholder' => '' !== $dir,
				);
				return $cache[ $key ];
			}
		}
	}
	return null;
}

/**
 * <img> for a theme design asset.
 *
 * @param string $key   Asset key.
 * @param string $alt   Alt text ('' for decorative).
 * @param array  $attrs Extra attributes (class, loading, fetchpriority, sizes).
 */
function qd_asset_img( $key, $alt = '', $attrs = array() ) {
	$img = qd_asset_image( $key );
	if ( ! $img ) {
		return '';
	}
	$attrs = array_merge(
		array(
			'loading'  => 'lazy',
			'decoding' => 'async',
		),
		$attrs
	);
	$html  = '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $alt ) . '"';
	if ( $img['width'] ) {
		$html .= ' width="' . $img['width'] . '" height="' . $img['height'] . '"';
	}
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return $html . '>';
}

/**
 * Featured image inside a fixed-ratio frame, or a branded "no image" frame.
 * The frame keeps layout stable (CLS 0) whether or not an image exists.
 *
 * @param int|WP_Post|null $post  Post.
 * @param string           $size  Registered image size.
 * @param string           $ratio CSS aspect ratio class suffix: 4-3 | 16-9 | 4-5 | 1-1.
 * @param array            $attrs Extra <img> attributes.
 */
function qd_thumb( $post = null, $size = 'qd-card', $ratio = '4-3', $attrs = array() ) {
	$post  = get_post( $post );
	$inner = '';
	if ( $post && has_post_thumbnail( $post ) ) {
		$inner = get_the_post_thumbnail(
			$post,
			$size,
			array_merge(
				array(
					'loading' => 'lazy',
					'alt'     => '',
				),
				$attrs
			)
		);
	}
	if ( ! $inner ) {
		$inner = '<span class="frame__empty" aria-hidden="true">' . qd_icon( 'image', array( 'size' => 28 ) ) . '</span>';
	}
	return '<span class="frame frame--' . esc_attr( $ratio ) . '">' . $inner . '</span>';
}

/**
 * Estimated reading time in minutes (Vietnamese ~ 220 words/min).
 *
 * @param int|WP_Post|null $post Post.
 */
function qd_reading_time( $post = null ) {
	$post  = get_post( $post );
	$words = $post ? count( preg_split( '/\s+/u', wp_strip_all_tags( $post->post_content ), -1, PREG_SPLIT_NO_EMPTY ) ) : 0;
	return max( 1, (int) round( $words / 220 ) );
}

/**
 * URL of the booking page, optionally pre-selecting a service.
 *
 * @param int|WP_Post|null $service Service post.
 */
function qd_booking_url( $service = null ) {
	$url     = home_url( '/dat-lich/' );
	$service = $service ? get_post( $service ) : null;
	// "dv", not "dich-vu": that name is the services post type's query var and would 404 the page.
	return $service ? add_query_arg( 'dv', $service->post_name, $url ) : $url;
}

/**
 * Button markup. Variants: primary | outline | ghost | light (on dark bg).
 *
 * @param string $label Text.
 * @param string $url   Href.
 * @param array  $args  variant, icon, icon_end, size (sm|lg), class, attrs.
 */
function qd_button( $label, $url, $args = array() ) {
	$variant = $args['variant'] ?? 'primary';
	$class   = 'btn btn--' . $variant;
	if ( ! empty( $args['size'] ) ) {
		$class .= ' btn--' . $args['size'];
	}
	if ( ! empty( $args['class'] ) ) {
		$class .= ' ' . $args['class'];
	}
	$extra = '';
	foreach ( (array) ( $args['attrs'] ?? array() ) as $k => $v ) {
		$extra .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	$icon     = ! empty( $args['icon'] ) ? qd_icon( $args['icon'], array( 'size' => 18 ) ) : '';
	$icon_end = ! empty( $args['icon_end'] ) ? qd_icon( $args['icon_end'], array( 'size' => 18 ) ) : '';
	return sprintf(
		'<a class="%s" href="%s"%s>%s<span>%s</span>%s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		$extra,
		$icon,
		esc_html( $label ),
		$icon_end
	);
}

/**
 * Load a template part with arguments, returning the HTML.
 *
 * @param string $slug Template part slug.
 * @param array  $args Arguments.
 */
function qd_get_part( $slug, $args = array() ) {
	ob_start();
	get_template_part( $slug, null, $args );
	return ob_get_clean();
}
