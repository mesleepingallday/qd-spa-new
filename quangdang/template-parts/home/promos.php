<?php
/**
 * Active and upcoming promotions (category su-kien-uu-dai with an end date ≥ today).
 * Hidden when there is nothing running.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_promos = get_posts(
	array(
		'category_name'  => 'su-kien-uu-dai',
		'posts_per_page' => 3,
		'meta_key'       => '_qd_promo_end', // phpcs:ignore WordPress.DB.SlowDBQuery
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'key'     => '_qd_promo_end',
				'value'   => current_datetime()->format( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	)
);
if ( ! $qd_promos ) {
	return;
}
?>
<section class="section section--ivory" aria-labelledby="home-promos">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'         => 'home-promos',
				'title'      => 'Ưu đãi đang diễn ra',
				'link_label' => 'Tất cả ưu đãi',
				'link_url'   => get_category_link( get_category_by_slug( 'su-kien-uu-dai' ) ),
			)
		);
		?>
		<div class="grid grid--3 scroller">
			<?php foreach ( $qd_promos as $qd_promo ) : ?>
				<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_promo ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
