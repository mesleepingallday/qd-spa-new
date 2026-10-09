<?php
/**
 * Service helpers + a small reusable meta box API.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Service groups as they appear in the menu spreadsheet.
 * "zone" = the top heading in the mega menu that groups them.
 *
 * @return array<string,array{title:string,zone:string,desc:string}>
 */
function qd_service_groups() {
	return array(
		'dieu-tri-da'      => array(
			'title' => 'Điều trị da',
			'zone'  => 'Chăm sóc và điều trị da',
			'desc'  => 'Mụn, thâm, nám, sẹo và xóa xăm – bác sĩ da liễu thăm khám và lên phác đồ.',
		),
		'cham-soc-da'      => array(
			'title' => 'Chăm sóc da',
			'zone'  => 'Chăm sóc và điều trị da',
			'desc'  => 'Trẻ hóa, phục hồi, triệt lông và các liệu trình công nghệ cao.',
		),
		'noi-khoa-tham-my' => array(
			'title' => 'Nội khoa thẩm mỹ',
			'zone'  => 'Nội khoa thẩm mỹ',
			'desc'  => 'Filler, botox, căng chỉ, meso – không phẫu thuật, thực hiện bởi bác sĩ.',
		),
	);
}

/**
 * Is this a service CATEGORY (top-level dich-vu post)?
 *
 * @param int|WP_Post|null $post Post.
 */
function qd_is_service_category( $post = null ) {
	$post = get_post( $post );
	return $post && 'dich-vu' === $post->post_type && 0 === (int) $post->post_parent;
}

/**
 * Top-level service categories, optionally filtered by group.
 *
 * @param string|null $group Group key from qd_service_groups().
 * @return WP_Post[]
 */
function qd_service_categories( $group = null ) {
	$args = array(
		'post_type'      => 'dich-vu',
		'post_parent'    => 0,
		'posts_per_page' => -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'  => true,
	);
	if ( $group ) {
		$args['meta_key']   = '_qd_group'; // phpcs:ignore WordPress.DB.SlowDBQuery
		$args['meta_value'] = $group;      // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	return get_posts( $args );
}

/**
 * Services inside a category.
 *
 * @param int $parent_id Category post ID.
 * @return WP_Post[]
 */
function qd_service_children( $parent_id ) {
	return get_posts(
		array(
			'post_type'      => 'dich-vu',
			'post_parent'    => (int) $parent_id,
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);
}

/**
 * The category a service belongs to (itself if it is a category).
 *
 * @param int|WP_Post|null $post Post.
 */
function qd_service_category( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	$ancestors = get_post_ancestors( $post );
	return $ancestors ? get_post( end( $ancestors ) ) : $post;
}

/**
 * Facts shown on cards and in the facts row. For a category, price_from is the
 * lowest price among its services.
 *
 * @param int|WP_Post|null $post Post.
 * @return array{price_from:int,price_unit:string,sessions:string,duration:string,downtime:string,count:int}
 */
function qd_service_facts( $post = null ) {
	$post = get_post( $post );
	$get  = fn( $k ) => (string) get_post_meta( $post->ID, '_qd_' . $k, true );

	$facts = array(
		'price_from' => (int) $get( 'price_from' ),
		'price_unit' => $get( 'price_unit' ),
		'sessions'   => $get( 'sessions' ),
		'duration'   => $get( 'duration' ),
		'downtime'   => $get( 'downtime' ),
		'count'      => 0,
	);

	if ( qd_is_service_category( $post ) ) {
		$children       = qd_service_children( $post->ID );
		$facts['count'] = count( $children );
		$prices         = array_filter( array_map( fn( $c ) => (int) get_post_meta( $c->ID, '_qd_price_from', true ), $children ) );
		if ( $prices && ! $facts['price_from'] ) {
			$facts['price_from'] = min( $prices );
		}
	}
	return $facts;
}

/**
 * Articles related to a service: posts tagged with the category slug (e.g. tag "dieu-tri-mun").
 *
 * @param int|WP_Post|null $post  Service or category.
 * @param int              $count How many.
 * @return WP_Post[]
 */
function qd_service_related_posts( $post = null, $count = 3 ) {
	$category = qd_service_category( $post );
	if ( ! $category ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => $count,
			'tag'            => $category->post_name,
			'no_found_rows'  => true,
		)
	);
}

/**
 * Doctor responsible for a service (falls back to the first doctor).
 *
 * @param int|WP_Post|null $post Service.
 */
function qd_service_doctor( $post = null ) {
	$post = get_post( $post );
	$id   = $post ? (int) get_post_meta( $post->ID, '_qd_doctor', true ) : 0;
	if ( $id && get_post( $id ) ) {
		return get_post( $id );
	}
	$first = get_posts(
		array(
			'post_type'      => 'bac-si',
			'posts_per_page' => 1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	return $first ? $first[0] : null;
}

/*
 * ---------------------------------------------------------------------------
 * Meta box API — reuse for any post type:
 *
 * qd_meta_box( 'qd-course', 'Thông tin khóa học', 'khoa-hoc', array(
 *     '_qd_course_fee' => array( 'label' => 'Học phí (VNĐ)', 'type' => 'number' ),
 *     '_qd_course_modules' => array( 'label' => 'Học phần', 'type' => 'textarea', 'help' => 'Mỗi dòng: Tên | Nội dung' ),
 * ) );
 *
 * Field types: text | number | date | textarea | select (with 'options' => [value => label] or a
 * callable returning that, so queries only run when the edit screen renders).
 * ---------------------------------------------------------------------------
 */

/**
 * Register a meta box with simple fields.
 *
 * @param string          $id        Box ID.
 * @param string          $title     Box title.
 * @param string|string[] $post_type Post type(s).
 * @param array           $fields    Field definitions keyed by meta key.
 */
function qd_meta_box( $id, $title, $post_type, $fields ) {
	add_action(
		'add_meta_boxes',
		function () use ( $id, $title, $post_type, $fields ) {
			add_meta_box(
				$id,
				$title,
				function ( $post ) use ( $id, $fields ) {
					wp_nonce_field( $id, $id . '_nonce' );
					echo '<div class="qd-meta">';
					foreach ( $fields as $key => $f ) {
						$value = get_post_meta( $post->ID, $key, true );
						$type  = $f['type'] ?? 'text';
						echo '<p class="qd-meta__row"><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $f['label'] ) . '</strong></label><br>';
						if ( 'textarea' === $type ) {
							echo '<textarea class="widefat" rows="' . (int) ( $f['rows'] ?? 4 ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $value ) . '</textarea>';
						} elseif ( 'select' === $type ) {
							echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">';
							$options = $f['options'] ?? array();
							$options = is_callable( $options ) ? call_user_func( $options ) : $options;
							foreach ( (array) $options as $opt => $label ) {
								echo '<option value="' . esc_attr( $opt ) . '"' . selected( (string) $value, (string) $opt, false ) . '>' . esc_html( $label ) . '</option>';
							}
							echo '</select>';
						} else {
							echo '<input class="widefat" type="' . esc_attr( $type ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
						}
						if ( ! empty( $f['help'] ) ) {
							echo '<br><span class="description">' . esc_html( $f['help'] ) . '</span>';
						}
						echo '</p>';
					}
					echo '</div>';
				},
				$post_type,
				'normal',
				'high'
			);
		}
	);

	foreach ( (array) $post_type as $type ) {
		add_action(
			'save_post_' . $type,
			function ( $post_id ) use ( $id, $fields ) {
				if ( ! isset( $_POST[ $id . '_nonce' ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $id . '_nonce' ] ), $id ) ) {
					return;
				}
				if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
				foreach ( $fields as $key => $f ) {
					if ( ! isset( $_POST[ $key ] ) ) {
						continue;
					}
					$raw   = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					$type  = $f['type'] ?? 'text';
					$value = 'number' === $type ? (int) $raw : ( 'textarea' === $type ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw ) );
					update_post_meta( $post_id, $key, $value );
				}
			}
		);
	}
}

// Service meta box (admin only).
add_action(
	'init',
	function () {
		if ( ! is_admin() ) {
			return;
		}
		$groups = array( '' => '— (chỉ cho nhóm cấp 1) —' );
		foreach ( qd_service_groups() as $key => $g ) {
			$groups[ $key ] = $g['zone'] . ' › ' . $g['title'];
		}
		qd_meta_box(
			'qd-service',
			'Thông tin dịch vụ',
			'dich-vu',
			array(
				'_qd_group'      => array(
					'label'   => 'Thuộc nhóm (chỉ chọn cho bài cấp 1, VD: Điều trị mụn)',
					'type'    => 'select',
					'options' => $groups,
				),
				'_qd_price_from' => array(
					'label' => 'Giá từ (VNĐ, chỉ nhập số)',
					'type'  => 'number',
					'help'  => 'Để trống ở bài cấp 1: hệ thống tự lấy giá thấp nhất của các dịch vụ con.',
				),
				'_qd_price_unit' => array(
					'label' => 'Đơn vị giá',
					'help'  => 'VD: /buổi, /liệu trình, /vùng',
				),
				'_qd_sessions'   => array( 'label' => 'Số buổi khuyến nghị (VD: 6–8 buổi)' ),
				'_qd_duration'   => array( 'label' => 'Thời gian mỗi buổi (VD: 45–60 phút)' ),
				'_qd_downtime'   => array( 'label' => 'Thời gian nghỉ dưỡng (VD: Không cần nghỉ dưỡng)' ),
				'_qd_suits'      => array(
					'label' => 'Phù hợp với',
					'type'  => 'textarea',
					'help'  => 'Mỗi dòng một ý.',
				),
				'_qd_not_suits'  => array(
					'label' => 'Chưa phù hợp với',
					'type'  => 'textarea',
					'help'  => 'Mỗi dòng một ý.',
				),
				'_qd_technology' => array(
					'label' => 'Công nghệ / sản phẩm sử dụng',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				'_qd_steps'      => array(
					'label' => 'Quy trình',
					'type'  => 'textarea',
					'rows'  => 6,
					'help'  => 'Mỗi dòng một bước: Tên bước | Mô tả ngắn',
				),
				'_qd_prices'     => array(
					'label' => 'Bảng giá',
					'type'  => 'textarea',
					'help'  => 'Mỗi dòng: Tên gói | Giá (số) | Ghi chú',
				),
				'_qd_aftercare'  => array(
					'label' => 'Lưu ý trước & sau điều trị',
					'type'  => 'textarea',
					'help'  => 'Mỗi dòng một lưu ý.',
				),
				'_qd_faq'        => array(
					'label' => 'Câu hỏi thường gặp',
					'type'  => 'textarea',
					'rows'  => 6,
					'help'  => 'Mỗi dòng: Câu hỏi | Trả lời',
				),
				'_qd_doctor'     => array(
					'label'   => 'Bác sĩ phụ trách',
					'type'    => 'select',
					'options' => fn() => array( '' => '— Bác sĩ đầu tiên trong danh sách —' ) + wp_list_pluck(
						get_posts(
							array(
								'post_type'      => 'bac-si',
								'posts_per_page' => 50,
							)
						),
						'post_title',
						'ID'
					),
				),
			)
		);
	},
	20
);

/**
 * The 8 skin concerns used by the home hero shortcuts, the search dialog and the quiz.
 * Quiz illustrations live at assets/images/concerns/{key} (see docs/IMAGE-PROMPTS.md).
 *
 * `short` is the label used on tiles; `glyph` is the key for qd_concern_glyph(). The concern's colours are
 * CSS tokens (`[data-concern="{key}"]` in main.css), so the palette lives in one place.
 *
 * @return array<string,array{label:string,short:string,glyph:string,url:string}>
 */
function qd_concerns() {
	return apply_filters(
		'qd_concerns',
		array(
			'mun'          => array(
				'label' => 'Mụn',
				'short' => 'Mụn',
				'glyph' => 'mun',
				'url'   => home_url( '/dich-vu/dieu-tri-mun/' ),
			),
			'tham'         => array(
				'label' => 'Thâm',
				'short' => 'Thâm',
				'glyph' => 'tham',
				'url'   => home_url( '/dich-vu/dieu-tri-tham/' ),
			),
			'nam'          => array(
				'label' => 'Nám – tàn nhang',
				'short' => 'Nám',
				'glyph' => 'nam',
				'url'   => home_url( '/dich-vu/dieu-tri-nam/' ),
			),
			'seo'          => array(
				'label' => 'Sẹo',
				'short' => 'Sẹo',
				'glyph' => 'seo',
				'url'   => home_url( '/dich-vu/dieu-tri-seo/' ),
			),
			'xoa-xam'      => array(
				'label' => 'Xóa xăm',
				'short' => 'Xóa xăm',
				'glyph' => 'xoa-xam',
				'url'   => home_url( '/dich-vu/xoa-xam/' ),
			),
			'triet-long'   => array(
				'label' => 'Triệt lông',
				'short' => 'Triệt lông',
				'glyph' => 'triet-long',
				'url'   => home_url( '/dich-vu/cham-soc-da/triet-long/' ),
			),
			'tre-hoa'      => array(
				'label' => 'Trẻ hóa da',
				'short' => 'Trẻ hóa',
				'glyph' => 'tre-hoa',
				'url'   => home_url( '/dich-vu/cham-soc-da/tre-hoa-da-cong-nghe-cao/' ),
			),
			'filler-botox' => array(
				'label' => 'Filler – Botox',
				'short' => 'Filler / Botox',
				'glyph' => 'filler-botox',
				'url'   => home_url( '/dich-vu/noi-khoa-tham-my/' ),
			),
		)
	);
}
