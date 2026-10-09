<?php
/**
 * One skin concern as a link: coloured tile with its glyph, and the visible name underneath.
 * Colour comes from the `data-concern` tokens in main.css; the label always carries the meaning.
 *
 * @package QuangDang
 *
 * @var array $args { key: string, concern: array{short:string,glyph:string,url:string} }
 */

defined( 'ABSPATH' ) || exit;

$qd_key     = $args['key'];
$qd_concern = $args['concern'];
?>
<a class="concern-tile" data-concern="<?php echo esc_attr( $qd_key ); ?>" href="<?php echo esc_url( $qd_concern['url'] ); ?>">
	<span class="concern-tile__icon"><?php echo qd_concern_glyph( $qd_concern['glyph'], array( 'size' => 40 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	<span class="concern-tile__label"><?php echo esc_html( $qd_concern['short'] ); ?></span>
</a>
