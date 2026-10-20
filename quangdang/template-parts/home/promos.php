<?php
/**
 * Active and upcoming promotions as horizontal rows (category su-kien-uu-dai with an end date ≥ today).
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
<section class="section" aria-labelledby="home-promos">
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
		<ul class="promo-list">
			<?php foreach ( $qd_promos as $qd_promo ) : ?>
				<?php
				$qd_status = qd_promo_status( $qd_promo );
				$qd_label  = (string) get_post_meta( $qd_promo->ID, '_qd_promo_label', true );
				$qd_start  = (string) get_post_meta( $qd_promo->ID, '_qd_promo_start', true );
				$qd_end    = (string) get_post_meta( $qd_promo->ID, '_qd_promo_end', true );
				$qd_dates  = ( $qd_start ? mysql2date( 'j/n/Y', $qd_start ) . ' – ' : 'Đến ' ) . mysql2date( 'j/n/Y', $qd_end );
				$qd_url    = get_permalink( $qd_promo );
				?>
				<li>
					<article class="promo-row">
						<a class="promo-row__thumb" href="<?php echo esc_url( $qd_url ); ?>" tabindex="-1" aria-hidden="true">
							<?php echo get_the_post_thumbnail( $qd_promo, 'qd-wide', array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php if ( $qd_label ) : ?>
								<span class="badge badge--promo"><?php echo esc_html( $qd_label ); ?></span>
							<?php endif; ?>
						</a>
						<div class="promo-row__body">
							<h3 class="promo-row__title"><a href="<?php echo esc_url( $qd_url ); ?>"><?php echo esc_html( get_the_title( $qd_promo ) ); ?></a></h3>
							<p class="promo-row__text"><?php echo esc_html( get_the_excerpt( $qd_promo ) ); ?></p>
							<div class="promo-row__meta">
								<span class="promo-row__date"><?php qd_the_icon( 'calendar-days', array( 'size' => 16 ) ); ?><span><?php echo esc_html( $qd_dates ); ?></span></span>
								<?php if ( $qd_status ) : ?>
									<span class="badge <?php echo 'active' === $qd_status['state'] ? 'badge--promo' : 'badge--neutral'; ?>"><?php qd_the_icon( 'clock', array( 'size' => 14 ) ); ?><?php echo esc_html( $qd_status['label'] ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
