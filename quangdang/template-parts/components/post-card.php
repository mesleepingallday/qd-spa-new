<?php
/**
 * Article card: 16:9 cover, topic, 2-line title, 2-line excerpt, date + reading time.
 * Promotions (posts with _qd_promo_end) get a status badge on the cover.
 *
 * @package QuangDang
 *
 * @var array $args { post?: WP_Post, heading?: 'h2'|'h3' }
 */

defined( 'ABSPATH' ) || exit;

$qd_post    = get_post( $args['post'] ?? null );
$qd_heading = $args['heading'] ?? 'h3';
$qd_tags    = get_the_tags( $qd_post );
$qd_cats    = get_the_category( $qd_post->ID );
$qd_topic   = $qd_tags ? $qd_tags[0]->name : ( $qd_cats ? $qd_cats[0]->name : '' );
$qd_promo   = (string) get_post_meta( $qd_post->ID, '_qd_promo_label', true );
$qd_status  = qd_promo_status( $qd_post );
?>
<article class="card post-card<?php echo $qd_status ? ' post-card--' . esc_attr( $qd_status['state'] ) : ''; ?>">
	<div class="post-card__media">
		<?php echo qd_thumb( $qd_post, 'qd-wide', '16-9' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $qd_status ) : ?>
			<span class="badge <?php echo 'active' === $qd_status['state'] ? 'badge--promo' : 'badge--neutral'; ?> post-card__status"><?php qd_the_icon( 'clock', array( 'size' => 14 ) ); ?><?php echo esc_html( $qd_status['label'] ); ?></span>
		<?php endif; ?>
	</div>
	<div class="card__body">
		<div class="post-card__top">
			<?php if ( $qd_promo ) : ?>
				<span class="badge badge--accent"><?php echo esc_html( $qd_promo ); ?></span>
			<?php elseif ( $qd_topic ) : ?>
				<span class="card__kicker"><?php echo esc_html( $qd_topic ); ?></span>
			<?php endif; ?>
		</div>
		<<?php echo tag_escape( $qd_heading ); ?> class="card__title"><a href="<?php echo esc_url( get_permalink( $qd_post ) ); ?>"><?php echo esc_html( get_the_title( $qd_post ) ); ?></a></<?php echo tag_escape( $qd_heading ); ?>>
		<p class="card__text"><?php echo esc_html( get_the_excerpt( $qd_post ) ); ?></p>
		<div class="card__foot"><?php echo qd_post_meta_line( $qd_post ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	</div>
</article>
