<?php
/**
 * Ruler timeline: an ordered list of steps laid along a millimetre scale, the kind that sits beside a
 * lesion in a clinical photo. Only for real sequences (a patient visit, a learning path): the
 * numbers are the order. Horizontal from 1024px, vertical on phones. Styles: assets/css/pages/trust.css.
 *
 * @package QuangDang
 *
 * @var array $args { steps: array<int,array{0:string,1:string}> [title, text], class?: string }
 */

defined( 'ABSPATH' ) || exit;

$qd_steps = $args['steps'] ?? array();
if ( ! $qd_steps ) {
	return;
}
?>
<ol class="ruler <?php echo esc_attr( $args['class'] ?? '' ); ?>" style="--n: <?php echo (int) count( $qd_steps ); ?>">
	<?php foreach ( $qd_steps as $qd_i => list( $qd_title, $qd_text ) ) : ?>
		<li class="ruler__step">
			<span class="ruler__num" aria-hidden="true"><?php echo (int) ( $qd_i + 1 ); ?></span>
			<h3 class="ruler__title"><?php echo esc_html( $qd_title ); ?></h3>
			<p class="ruler__text"><?php echo esc_html( $qd_text ); ?></p>
		</li>
	<?php endforeach; ?>
</ol>
