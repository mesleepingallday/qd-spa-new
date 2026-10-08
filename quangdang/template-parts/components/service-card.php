<?php
/**
 * Service card: what people compare on — who it suits (excerpt), sessions, duration, price.
 *
 * @package QuangDang
 *
 * @var array $args { post: WP_Post, heading?: string, kicker?: bool }
 */

defined( 'ABSPATH' ) || exit;

$qd_post    = get_post( $args['post'] );
$qd_facts   = qd_service_facts( $qd_post );
$qd_heading = $args['heading'] ?? 'h3';
$qd_parent  = ! empty( $args['kicker'] ) && $qd_post->post_parent ? get_post( $qd_post->post_parent ) : null;
?>
<article class="card service-card">
	<?php echo qd_thumb( $qd_post, 'qd-card', '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<div class="card__body">
		<?php if ( $qd_parent ) : ?>
			<span class="card__kicker"><?php echo esc_html( $qd_parent->post_title ); ?></span>
		<?php endif; ?>
		<<?php echo tag_escape( $qd_heading ); ?> class="card__title"><a href="<?php echo esc_url( get_permalink( $qd_post ) ); ?>"><?php echo esc_html( get_the_title( $qd_post ) ); ?></a></<?php echo tag_escape( $qd_heading ); ?>>
		<?php if ( has_excerpt( $qd_post ) ) : ?>
			<p class="card__text"><?php echo esc_html( get_the_excerpt( $qd_post ) ); ?></p>
		<?php endif; ?>
		<?php if ( $qd_facts['sessions'] || $qd_facts['duration'] ) : ?>
			<p class="card__meta">
				<?php if ( $qd_facts['sessions'] ) : ?>
					<span><?php qd_the_icon( 'repeat', array( 'size' => 16 ) ); ?><?php echo esc_html( $qd_facts['sessions'] ); ?></span>
				<?php endif; ?>
				<?php if ( $qd_facts['duration'] ) : ?>
					<span><?php qd_the_icon( 'clock', array( 'size' => 16 ) ); ?><?php echo esc_html( $qd_facts['duration'] ); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<div class="card__foot">
			<?php if ( $qd_facts['price_from'] ) : ?>
				<span class="price"><small>Từ</small> <?php echo esc_html( qd_price( $qd_facts['price_from'] ) ); ?><small><?php echo esc_html( $qd_facts['price_unit'] ); ?></small></span>
			<?php else : ?>
				<span class="price-note">Báo giá sau khi thăm khám</span>
			<?php endif; ?>
			<span class="card__arrow" aria-hidden="true"><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></span>
		</div>
	</div>
</article>
