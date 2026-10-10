<?php
/**
 * Fact row: three or four plain facts about the clinic or a course, each a figure and what it counts.
 * Callers pass only facts they really know; an empty list prints nothing.
 *
 * @package QuangDang
 *
 * @var array $args { items: array<int,array{0:string,1:string}> [figure, label], label?: string, class?: string }
 */

defined( 'ABSPATH' ) || exit;

$qd_items = array_values( array_filter( $args['items'] ?? array(), fn( $item ) => '' !== trim( (string) ( $item[0] ?? '' ) ) ) );
if ( ! $qd_items ) {
	return;
}
?>
<dl class="fact-row <?php echo esc_attr( $args['class'] ?? '' ); ?>" aria-label="<?php echo esc_attr( $args['label'] ?? 'Thông tin nhanh' ); ?>">
	<?php foreach ( $qd_items as list( $qd_figure, $qd_label ) ) : ?>
		<div class="fact-row__item">
			<dt class="fact-row__label"><?php echo esc_html( $qd_label ); ?></dt>
			<dd class="fact-row__figure"><?php echo esc_html( $qd_figure ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>
