<?php
/**
 * Front-end assets.
 *
 * One shared stylesheet + one script. Screens with heavy, page-only styles get their
 * own file in assets/css/pages/ and register it in qd_page_styles() below.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Version string that changes whenever the file changes.
 *
 * @param string $rel Path relative to the theme root.
 */
function qd_asset_ver( $rel ) {
	$file = QD_DIR . '/' . $rel;
	return file_exists( $file ) ? QD_VERSION . '.' . filemtime( $file ) : QD_VERSION;
}

/**
 * Page-only stylesheets: handle => [file, condition callback].
 *
 * @return array<string,array{0:string,1:callable}>
 */
function qd_page_styles() {
	return apply_filters(
		'qd_page_styles',
		array(
			'qd-service' => array( 'assets/css/pages/service.css', fn() => is_singular( 'dich-vu' ) || is_post_type_archive( 'dich-vu' ) ),
			'qd-home'    => array( 'assets/css/pages/home.css', 'is_front_page' ),
		)
	);
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'qd-main', QD_URI . '/assets/css/main.css', array(), qd_asset_ver( 'assets/css/main.css' ) );

		foreach ( qd_page_styles() as $handle => $def ) {
			list( $rel, $when ) = $def;
			if ( file_exists( QD_DIR . '/' . $rel ) && call_user_func( $when ) ) {
				wp_enqueue_style( $handle, QD_URI . '/' . $rel, array( 'qd-main' ), qd_asset_ver( $rel ) );
			}
		}

		wp_enqueue_script(
			'qd-main',
			QD_URI . '/assets/js/main.js',
			array(),
			qd_asset_ver( 'assets/js/main.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
);

// Preload the two font files every page needs first (body text, Vietnamese + Latin).
add_action(
	'wp_head',
	function () {
		foreach ( array( 'be-vietnam-pro-vietnamese-400-normal', 'be-vietnam-pro-latin-400-normal' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( QD_URI . '/assets/fonts/' . $font . '.woff2' )
			);
		}
	},
	2
);
