<?php
/**
 * Compact horizontal service card, inserted inside the article after its 2nd section.
 * Job: the reader has just understood the problem → show the treatment and its starting price.
 *
 * @package QuangDang
 *
 * @var array $args { service: WP_Post }
 */

defined( 'ABSPATH' ) || exit;

$qd_service = get_post( $args['service'] );
if ( ! $qd_service ) {
	return;
}
$qd_facts = qd_service_facts( $qd_service );
?>
<aside class="card inline-service" aria-label="Dịch vụ liên quan">
	<div class="inline-service__media"><?php echo qd_thumb( $qd_service, 'qd-card', '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<div class="inline-service__body">
		<p class="inline-service__kicker">Dịch vụ liên quan</p>
		<p class="inline-service__title"><a href="<?php echo esc_url( get_permalink( $qd_service ) ); ?>"><?php echo esc_html( get_the_title( $qd_service ) ); ?></a></p>
		<?php if ( has_excerpt( $qd_service ) ) : ?>
			<p class="inline-service__text"><?php echo esc_html( get_the_excerpt( $qd_service ) ); ?></p>
		<?php endif; ?>
		<p class="inline-service__foot">
			<?php if ( $qd_facts['price_from'] ) : ?>
				<span class="price"><small>Từ</small> <?php echo esc_html( qd_price( $qd_facts['price_from'] ) ); ?><small><?php echo esc_html( $qd_facts['price_unit'] ); ?></small></span>
			<?php else : ?>
				<span class="price-note">Báo giá sau khi thăm khám</span>
			<?php endif; ?>
			<span class="link-more" aria-hidden="true">Xem dịch vụ<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></span>
		</p>
	</div>
</aside>
