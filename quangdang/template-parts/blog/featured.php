<?php
/**
 * Featured post (first on page 1): large cover beside the title, excerpt and a clear "read" link.
 *
 * @package QuangDang
 *
 * @var array $args { post: WP_Post }
 */

defined( 'ABSPATH' ) || exit;

$qd_post   = get_post( $args['post'] );
$qd_tags   = get_the_tags( $qd_post );
$qd_cats   = get_the_category( $qd_post->ID );
$qd_topic  = $qd_tags ? $qd_tags[0]->name : ( $qd_cats ? $qd_cats[0]->name : '' );
$qd_label  = (string) get_post_meta( $qd_post->ID, '_qd_promo_label', true );
$qd_status = qd_promo_status( $qd_post );
?>
<article class="card featured">
	<div class="featured__media">
		<?php echo qd_thumb( $qd_post, 'qd-wide', '16-9', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $qd_status ) : ?>
			<span class="badge <?php echo 'active' === $qd_status['state'] ? 'badge--promo' : 'badge--neutral'; ?> post-card__status"><?php qd_the_icon( 'clock', array( 'size' => 14 ) ); ?><?php echo esc_html( $qd_status['label'] ); ?></span>
		<?php endif; ?>
	</div>
	<div class="featured__body">
		<p class="featured__kicker">
			<span class="badge badge--accent"><?php qd_the_icon( 'sparkles', array( 'size' => 14 ) ); ?>Nổi bật</span>
			<?php if ( $qd_label ) : ?>
				<span class="badge badge--accent"><?php echo esc_html( $qd_label ); ?></span>
			<?php elseif ( $qd_topic ) : ?>
				<span class="card__kicker"><?php echo esc_html( $qd_topic ); ?></span>
			<?php endif; ?>
		</p>
		<h2 class="featured__title"><a href="<?php echo esc_url( get_permalink( $qd_post ) ); ?>"><?php echo esc_html( get_the_title( $qd_post ) ); ?></a></h2>
		<p class="featured__text"><?php echo esc_html( get_the_excerpt( $qd_post ) ); ?></p>
		<div class="featured__foot">
			<?php echo qd_post_meta_line( $qd_post ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="link-more" aria-hidden="true">Đọc bài viết<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></span>
		</div>
	</div>
</article>
