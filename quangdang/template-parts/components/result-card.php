<?php
/**
 * One before/after case: the square poster (opens full size) and its caption.
 * The poster's points are kept as text for screen readers and search, and fill the zoom dialog.
 *
 * @package QuangDang
 * @var array $args { post: WP_Post }
 */

defined( 'ABSPATH' ) || exit;

$qd_post    = $args['post'];
$qd_thumb   = (int) get_post_thumbnail_id( $qd_post );
$qd_full    = wp_get_attachment_image_url( $qd_thumb, 'full' );
$qd_service = (int) get_post_meta( $qd_post->ID, '_qd_result_service', true );
$qd_service = $qd_service ? get_post( $qd_service ) : null;
$qd_title   = $qd_service ? get_the_title( $qd_service ) : get_the_title( $qd_post );
$qd_caption = (string) get_post_meta( $qd_post->ID, '_qd_result_caption', true );
$qd_points  = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $qd_post->ID, '_qd_result_points', true ) ) ) );
$qd_label   = trim( $qd_title . ( $qd_caption ? ' · ' . $qd_caption : '' ) );
?>
<figure class="result-card">
	<a class="result-card__zoom" href="<?php echo esc_url( $qd_full ); ?>" data-result-zoom data-title="<?php echo esc_attr( $qd_label ); ?>">
		<?php
		echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput
			$qd_thumb,
			'qd-result',
			false,
			array(
				'alt'     => 'Ảnh trước và sau: ' . $qd_label,
				'sizes'   => '(min-width: 1280px) 380px, (min-width: 768px) 340px, 78vw',
				'loading' => 'lazy',
			)
		);
		?>
		<span class="result-card__badge" aria-hidden="true"><?php echo qd_icon( 'search', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="sr-only">Phóng to ảnh</span>
	</a>
	<figcaption class="result-card__caption">
		<?php if ( $qd_service ) : ?>
			<a class="result-card__title" href="<?php echo esc_url( get_permalink( $qd_service ) ); ?>"><?php echo esc_html( $qd_title ); ?></a>
		<?php else : ?>
			<span class="result-card__title"><?php echo esc_html( $qd_title ); ?></span>
		<?php endif; ?>
		<?php if ( $qd_caption ) : ?>
			<span class="result-card__meta"><?php echo esc_html( $qd_caption ); ?></span>
		<?php endif; ?>
		<?php if ( $qd_points ) : ?>
			<ul class="sr-only" data-result-points>
				<?php foreach ( $qd_points as $qd_point ) : ?>
					<li><?php echo esc_html( $qd_point ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</figcaption>
</figure>
