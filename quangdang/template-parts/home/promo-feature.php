<?php
/**
 * Home banner: the running promotion that ends soonest, wide, right under the hero.
 * Data comes from the post: title (or _qd_promo_headline), excerpt, _qd_promo_points, _qd_promo_image
 * (or the featured image) and the badge from _qd_promo_label. Prints nothing when no promotion is running.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_feature = qd_running_promos( 1 );
if ( ! $qd_feature ) {
	return;
}
$qd_post     = $qd_feature[0];
$qd_status   = qd_promo_status( $qd_post );
$qd_headline = (string) get_post_meta( $qd_post->ID, '_qd_promo_headline', true );
$qd_headline = $qd_headline ? $qd_headline : get_the_title( $qd_post );
$qd_points   = array_slice( qd_lines( get_post_meta( $qd_post->ID, '_qd_promo_points', true ) ), 0, 3 );
$qd_label    = trim( (string) get_post_meta( $qd_post->ID, '_qd_promo_label', true ) );
$qd_image    = (int) get_post_meta( $qd_post->ID, '_qd_promo_image', true );
if ( ! $qd_image || ! wp_attachment_is_image( $qd_image ) ) {
	$qd_image = (int) get_post_thumbnail_id( $qd_post );
}
$qd_excerpt = get_the_excerpt( $qd_post );
$qd_icons   = array( 'circle-check', 'sparkles', 'scan-face' );

// "Giảm 50%" → small "Giảm", big "50%". Anything else: the whole label, big.
$qd_small = '';
$qd_big   = $qd_label;
if ( preg_match( '/^(.*?)(\d+\s*%|\d+K)$/u', $qd_label, $qd_m ) && '' !== trim( $qd_m[2] ) ) {
	$qd_small = trim( $qd_m[1] );
	$qd_big   = trim( $qd_m[2] );
}
$qd_link = get_permalink( $qd_post );
?>
<section class="promo-feature" aria-labelledby="promo-feature-title">
	<span class="promo-feature__bubble promo-feature__bubble--1" aria-hidden="true"></span>
	<span class="promo-feature__bubble promo-feature__bubble--2" aria-hidden="true"></span>
	<span class="promo-feature__bubble promo-feature__bubble--3" aria-hidden="true"></span>
	<div class="promo-feature__text">
		<span class="promo-feature__pill"><?php qd_the_icon( 'sparkles', array( 'size' => 16 ) ); ?><span>Ưu đãi đặc biệt</span></span>
		<h2 id="promo-feature-title" class="promo-feature__title"><?php echo esc_html( $qd_headline ); ?></h2>
		<?php if ( $qd_excerpt ) : ?>
			<p class="promo-feature__lead"><?php echo esc_html( $qd_excerpt ); ?></p>
		<?php endif; ?>
		<?php if ( $qd_points ) : ?>
			<ul class="promo-feature__facts">
				<?php foreach ( $qd_points as $qd_i => $qd_point ) : ?>
					<li><span class="promo-feature__fact-ic"><?php qd_the_icon( $qd_icons[ $qd_i % 3 ], array( 'size' => 20 ) ); ?></span><span><?php echo esc_html( $qd_point ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php if ( $qd_image ) : ?>
		<div class="promo-feature__figure">
			<?php
			echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput
				$qd_image,
				'large',
				false,
				array(
					'class'    => 'promo-feature__photo',
					'sizes'    => '(min-width: 1024px) 320px, 100vw',
					'loading'  => 'eager',
					'decoding' => 'async',
				)
			);
			?>
		</div>
	<?php endif; ?>
	<div class="promo-feature__offer">
		<div class="promo-feature__round">
			<?php if ( '' !== $qd_big ) : ?>
				<div class="promo-feature__badge">
					<?php if ( '' !== $qd_small ) : ?>
						<small><?php echo esc_html( $qd_small ); ?></small>
					<?php endif; ?>
					<strong><?php echo esc_html( $qd_big ); ?></strong>
				</div>
			<?php endif; ?>
			<?php if ( $qd_status ) : ?>
				<span class="badge badge--promo promo-feature__days"><?php qd_the_icon( 'clock', array( 'size' => 14 ) ); ?><?php echo esc_html( $qd_status['label'] ); ?></span>
			<?php endif; ?>
		</div>
		<?php
		echo qd_button( 'Nhận ưu đãi', qd_main_booking_url(), array( 'icon_end' => 'arrow-right', 'attrs' => qd_main_booking_attrs() ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo qd_button( 'Xem chi tiết', $qd_link, array( 'variant' => 'outline' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		?>
	</div>
</section>
