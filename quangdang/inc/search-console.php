<?php
/**
 * Google Search Console ownership check (HTML file method).
 *
 * Serves the verification file at the site root, e.g. https://example.com/googleed72139ba6dbeea5.html,
 * without needing a file in the WordPress install directory. Remove this file once the property is verified
 * if you no longer want the file published.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		global $wp;

		$verification_file = 'googleed72139ba6dbeea5.html';

		if ( ! isset( $wp->request ) || $wp->request !== $verification_file ) {
			return;
		}

		status_header( 200 );
		header( 'Content-Type: text/html; charset=utf-8' );
		echo 'google-site-verification: googleed72139ba6dbeea5.html';
		exit;
	}
);
