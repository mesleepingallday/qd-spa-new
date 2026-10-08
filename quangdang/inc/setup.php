<?php
/**
 * Theme setup.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'quangdang', QD_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 96,
				'width'       => 320,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		add_editor_style( 'assets/css/editor.css' );

		register_nav_menus(
			array(
				'primary' => __( 'Menu chính (mega menu)', 'quangdang' ),
				'footer'  => __( 'Liên kết chân trang', 'quangdang' ),
			)
		);

		// Card 4:3, wide 16:9, portrait 4:5. Templates always render with a fixed aspect-ratio box.
		add_image_size( 'qd-card', 800, 600, true );
		add_image_size( 'qd-wide', 1280, 720, true );
		add_image_size( 'qd-portrait', 960, 1200, true );
	}
);

$GLOBALS['content_width'] = 720;

add_filter( 'excerpt_length', fn() => 28 );
add_filter( 'excerpt_more', fn() => '…' );

// Lighter <head>: no emoji polyfill, no generator tag.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
