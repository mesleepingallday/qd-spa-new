<?php
/**
 * JSON-LD structured data. Templates add nodes with qd_schema_add(); everything is
 * printed once in the footer as a single @graph.
 *
 * SEO plugins (Rank Math / Yoast) already output WebPage, Article and BreadcrumbList,
 * so when one is active this theme only adds the medical nodes they don't cover.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add a schema node to the page graph.
 *
 * @param array $node Schema.org node (without @context).
 */
function qd_schema_add( $node ) {
	$GLOBALS['qd_schema_nodes'][] = $node;
}

/**
 * The clinic as a MedicalClinic node (also referenced as provider elsewhere).
 */
function qd_schema_clinic() {
	$node = array(
		'@type'        => array( 'MedicalClinic', 'MedicalBusiness' ),
		'@id'          => home_url( '/#clinic' ),
		'name'         => qd_clinic( 'name' ),
		'url'          => home_url( '/' ),
		'telephone'    => preg_replace( '/[^0-9+]/', '', qd_clinic( 'hotline' ) ),
		'address'      => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Tầng 5, TTTM Đức Tài – Tâm Đạt',
			'addressLocality' => 'Quỳnh Lưu',
			'addressRegion'   => 'Nghệ An',
			'addressCountry'  => 'VN',
		),
		'medicalSpecialty' => 'Dermatology',
		'openingHours'     => 'Mo-Su 08:00-20:00',
		'sameAs'           => array_values( array_filter( array( qd_clinic( 'facebook' ), qd_clinic( 'zalo' ) ) ) ),
	);
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$node['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
	}
	return $node;
}

add_action(
	'wp_footer',
	function () {
		$graph = array( qd_schema_clinic() );

		if ( ! qd_seo_plugin_active() ) {
			$items = array();
			foreach ( qd_breadcrumb_trail() as $i => list( $label, $url ) ) {
				$item = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => $label,
				);
				if ( $url ) {
					$item['item'] = $url;
				}
				$items[] = $item;
			}
			if ( count( $items ) > 1 ) {
				$graph[] = array(
					'@type'           => 'BreadcrumbList',
					'itemListElement' => $items,
				);
			}
		}

		$graph = array_merge( $graph, $GLOBALS['qd_schema_nodes'] ?? array() );
		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode(
				array(
					'@context' => 'https://schema.org',
					'@graph'   => $graph,
				),
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		);
	},
	20
);

/**
 * FAQPage node from "Câu hỏi | Trả lời" rows.
 *
 * @param array<int,string[]> $rows FAQ rows.
 */
function qd_schema_faq( $rows ) {
	if ( ! $rows ) {
		return;
	}
	qd_schema_add(
		array(
			'@type'      => 'FAQPage',
			'mainEntity' => array_map(
				fn( $r ) => array(
					'@type'          => 'Question',
					'name'           => $r[0],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $r[1],
					),
				),
				$rows
			),
		)
	);
}
