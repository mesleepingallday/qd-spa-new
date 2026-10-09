<?php
/**
 * The eight skin-concern glyphs. Original artwork, 32px grid, 2px round strokes, drawn to stay legible at 24px.
 * Navigation symbols, not anatomical diagrams: they always sit next to the concern's visible name.
 *
 * Colour comes from CSS (`[data-concern]` tokens in main.css), never from here, so there is one palette.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inner SVG markup per concern key (the keys of qd_concerns()).
 *
 * @return array<string,string>
 */
function qd_concern_glyph_data() {
	return array(
		'mun' => '<g transform="translate(0 -5)"><path d="M2 24h1a3 3 0 0 1 6 0h.5a5.5 5.5 0 0 1 11 0h.5a3 3 0 0 1 6 0h2V28.5H2z" fill="currentColor" fill-opacity=".2" stroke="none"/><path d="M2 24h1a3 3 0 0 1 6 0h.5a5.5 5.5 0 0 1 11 0h.5a3 3 0 0 1 6 0h2"/><circle cx="15" cy="21.4" r="1.4" fill="currentColor" stroke="none"/></g>',
		'tham' => '<g transform="rotate(-28 16 16)"><ellipse cx="16" cy="16" rx="12" ry="8.5" opacity=".4"/><ellipse cx="16" cy="16" rx="6.5" ry="4.3" fill="currentColor" stroke="none"/></g>',
		'nam' => '<path d="M4.5 6.5c-1.5 10 4 20 14 21.5"/><circle cx="12" cy="11" r="2" fill="currentColor" stroke="none"/><circle cx="19.5" cy="8.5" r="1.5" fill="currentColor" stroke="none"/><circle cx="25" cy="13.5" r="2.1" fill="currentColor" stroke="none"/><circle cx="17" cy="16" r="1.6" fill="currentColor" stroke="none"/><circle cx="23" cy="21" r="1.6" fill="currentColor" stroke="none"/><circle cx="12.5" cy="21.5" r="1.4" fill="currentColor" stroke="none"/><circle cx="18.5" cy="26" r="1.4" fill="currentColor" stroke="none"/>',
		'seo' => '<path d="M5 22L27 10"/><path d="M7.1 17L10.4 22.9M12 14.3L15.2 20.3M16.8 11.7L20 17.7M21.6 9.1L24.9 15" opacity=".9"/>',
		'xoa-xam' => '<path d="M11.5 11c3.5 4.2 6 7.2 6 10.2a6 6 0 0 1-12 0c0-3 2.5-6 6-10.2z" fill="currentColor" fill-opacity=".28"/><path d="M28 4l-6.5 6.5"/><circle cx="20" cy="12" r="1.5" fill="currentColor" stroke="none"/>',
		'triet-long' => '<path d="M3 16h9M20 16h9"/><path d="M16 16C16 10.5 20.5 8 18.5 3"/><circle cx="16" cy="22" r="4.2"/><path d="M16 16v1.8"/>',
		'tre-hoa' => '<path d="M15 4c.9 6.2 3.3 8.6 9.5 9.5C18.3 14.4 15.9 16.8 15 23c-.9-6.2-3.3-8.6-9.5-9.5C11.7 12.6 14.1 10.2 15 4z"/><path d="M25 19.5c.35 2.4 1.3 3.3 3.7 3.7-2.4.35-3.35 1.3-3.7 3.7-.35-2.4-1.3-3.35-3.7-3.7 2.4-.4 3.35-1.3 3.7-3.7z"/><path d="M7 27.5c3 1.8 6.5 2 10 .5" opacity=".6"/>',
		'filler-botox' => '<path d="M16 3.5c6 0 9.5 4.5 9.5 10.5 0 5-2 9-5.5 12L16 28.5 12 26c-3.5-3-5.5-7-5.5-12C6.5 8 10 3.5 16 3.5z"/><circle cx="20.5" cy="16.5" r="2.3"/><path d="M20.5 12v2M20.5 19v2M15.5 16.5h2M23.5 16.5h2"/>',
	);
}

/**
 * Inline concern glyph. Decorative by default (the visible label carries the meaning).
 *
 * @param string $key  Concern key: mun, tham, nam, seo, xoa-xam, triet-long, tre-hoa, filler-botox.
 * @param array  $args size (px, default 32), class.
 */
function qd_concern_glyph( $key, $args = array() ) {
	$data = qd_concern_glyph_data();
	if ( ! isset( $data[ $key ] ) ) {
		return '';
	}
	$size  = (int) ( $args['size'] ?? 32 );
	$class = trim( 'glyph glyph-' . $key . ' ' . ( $args['class'] ?? '' ) );

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $class ),
		$size,
		$data[ $key ] // Static markup defined above.
	);
}

/**
 * A concern's coloured tile with its glyph, for places that already show the concern's name in text
 * (page heroes, cards). Decorative: never the only carrier of the meaning.
 *
 * @param string $key  Concern key, or '' (returns '').
 * @param array  $args size: sm|md|lg (default md), class.
 */
function qd_concern_mark( $key, $args = array() ) {
	$concerns = function_exists( 'qd_concerns' ) ? qd_concerns() : array();
	if ( ! $key || ! isset( $concerns[ $key ] ) ) {
		return '';
	}
	$size = in_array( $args['size'] ?? 'md', array( 'sm', 'md', 'lg' ), true ) ? $args['size'] : 'md';

	return sprintf(
		'<span class="concern-mark concern-mark--%1$s %2$s" data-concern="%3$s" aria-hidden="true">%4$s</span>',
		esc_attr( $size ),
		esc_attr( $args['class'] ?? '' ),
		esc_attr( $key ),
		qd_concern_glyph( $concerns[ $key ]['glyph'], array( 'size' => 32 ) )
	);
}
