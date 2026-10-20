<?php
/**
 * Area: Kết quả khách hàng (before/after posters on the home page).
 *
 * Staff add one "ket-qua" post per case: the finished square poster is the featured image,
 * plus the related service, a short caption, the poster's points as text and a consent flag.
 * Only cases with written consent render (template-parts/home/results.php).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Content type, meta, image size
 * ---------------------------------------------------------------------- */

add_action(
	'init',
	function () {
		register_post_type(
			'ket-qua',
			array(
				'labels'        => array_merge(
					qd_cpt_labels( 'Kết quả', 'Kết quả khách hàng' ),
					array(
						'featured_image'     => 'Ảnh poster kết quả',
						'set_featured_image' => 'Chọn ảnh poster',
					)
				),
				'description'   => 'Ảnh trước/sau của khách hàng (poster vuông), hiện ở trang chủ.',
				'public'        => false,
				'show_ui'       => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-images-alt2',
				'menu_position' => 8,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			)
		);

		$auth = fn() => current_user_can( 'edit_posts' );
		foreach ( array(
			'_qd_result_service' => 'integer', // dich-vu post ID.
			'_qd_result_caption' => 'string',  // "Sau 6 buổi".
			'_qd_result_points'  => 'string',  // One per line.
			'_qd_result_consent' => 'integer', // 1 = written consent on file.
		) as $key => $kind ) {
			register_post_meta(
				'ket-qua',
				$key,
				array(
					'type'          => $kind,
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => $auth,
				)
			);
		}

		add_image_size( 'qd-result', 800, 800, false );

		if ( ! is_admin() ) {
			return;
		}
		qd_meta_box(
			'qd-result',
			'Thông tin kết quả',
			'ket-qua',
			array(
				'_qd_result_consent' => array(
					'label'   => 'Đồng ý của khách hàng',
					'type'    => 'select',
					'options' => array(
						'0' => 'Chưa có: không hiện trên web',
						'1' => 'Đã có đồng ý bằng văn bản',
					),
					'help'    => 'Chỉ ca đã có đồng ý bằng văn bản mới hiện ở trang chủ.',
				),
				'_qd_result_service' => array(
					'label'   => 'Dịch vụ liên quan',
					'type'    => 'select',
					'options' => fn() => array( '' => '— Chọn dịch vụ —' ) + wp_list_pluck(
						get_posts(
							array(
								'post_type'           => 'dich-vu',
								'posts_per_page'      => 100,
								'post_parent__not_in' => array( 0 ),
								'orderby'             => 'title',
								'order'               => 'ASC',
							)
						),
						'post_title',
						'ID'
					),
				),
				'_qd_result_caption' => array(
					'label' => 'Chú thích ngắn',
					'help'  => 'VD: Sau 6 buổi, Sau 60 phút làm với Aquapeel',
				),
				'_qd_result_points'  => array(
					'label' => 'Nội dung trên poster (mỗi dòng một ý)',
					'type'  => 'textarea',
					'rows'  => 5,
					'help'  => 'Gõ lại các ý trên ảnh để Google và trình đọc màn hình đọc được. VD: Giảm đến 80% mụn viêm',
				),
			)
		);
	}
);

add_filter(
	'admin_post_thumbnail_html',
	function ( $html, $post_id ) {
		if ( 'ket-qua' === get_post_type( $post_id ) ) {
			$html .= '<p class="description">Ảnh vuông, từ 1200×1200 px, WebP hoặc JPG, nên dưới 300 KB.</p>';
		}
		return $html;
	},
	10,
	2
);

/* -------------------------------------------------------------------------
 * Admin list: poster, service, consent
 * ---------------------------------------------------------------------- */

add_filter(
	'manage_ket-qua_posts_columns',
	function ( $cols ) {
		$title = $cols['title'];
		unset( $cols['title'], $cols['date'] );
		return array_merge(
			array_slice( $cols, 0, 1 ),
			array(
				'qd_poster'  => 'Poster',
				'title'      => $title,
				'qd_service' => 'Dịch vụ',
				'qd_consent' => 'Đồng ý',
			)
		);
	}
);

add_action(
	'manage_ket-qua_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'qd_poster' === $col ) {
			echo get_the_post_thumbnail( $post_id, array( 64, 64 ) ) ?: '—'; // phpcs:ignore WordPress.Security.EscapeOutput
		} elseif ( 'qd_service' === $col ) {
			$service = (int) get_post_meta( $post_id, '_qd_result_service', true );
			echo $service ? esc_html( get_the_title( $service ) ) : '—';
		} elseif ( 'qd_consent' === $col ) {
			echo get_post_meta( $post_id, '_qd_result_consent', true ) ? '✓ Có' : '✗ Chưa (ẩn)';
		}
	},
	10,
	2
);

/* -------------------------------------------------------------------------
 * Query + script
 * ---------------------------------------------------------------------- */

/**
 * Published cases that may be shown: written consent and a poster.
 *
 * @return WP_Post[]
 */
function qd_results() {
	return get_posts(
		array(
			'post_type'      => 'ket-qua',
			'posts_per_page' => 12,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'   => '_qd_result_consent',
					'value' => '1',
				),
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
		)
	);
}

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_front_page() ) {
			wp_enqueue_script(
				'qd-results',
				QD_URI . '/assets/js/results.js',
				array(),
				qd_asset_ver( 'assets/js/results.js' ),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
);
