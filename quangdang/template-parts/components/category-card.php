<?php
/**
 * Category card (Điều trị mụn…): cover, name, number of services, lowest price.
 *
 * @package QuangDang
 *
 * @var array $args { post: WP_Post, heading?: string }
 */

defined( 'ABSPATH' ) || exit;

$qd_post    = get_post( $args['post'] );
$qd_facts   = qd_service_facts( $qd_post );
$qd_heading = $args['heading'] ?? 'h3';
?>
<article class="card category-card">
	<?php echo qd_thumb( $qd_post, 'qd-card', '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<div class="card__body">
		<<?php echo tag_escape( $qd_heading ); ?> class="card__title"><a href="<?php echo esc_url( get_permalink( $qd_post ) ); ?>"><?php echo esc_html( get_the_title( $qd_post ) ); ?></a></<?php echo tag_escape( $qd_heading ); ?>>
		<div class="card__foot">
			<span class="category-card__meta">
				<?php if ( $qd_facts['count'] ) : ?>
					<span><?php echo (int) $qd_facts['count']; ?> dịch vụ</span>
				<?php endif; ?>
				<?php if ( $qd_facts['price_from'] ) : ?>
					<span class="price"><small>Từ</small> <?php echo esc_html( qd_price( $qd_facts['price_from'] ) ); ?></span>
				<?php endif; ?>
			</span>
			<span class="card__arrow" aria-hidden="true"><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></span>
		</div>
	</div>
</article>
