<?php
/**
 * Compact dropdown: title + one-line description per link (WP menu "Description" field).
 *
 * @package QuangDang
 *
 * @var array $args { item, id }
 */

defined( 'ABSPATH' ) || exit;

$qd_item = $args['item'];
$qd_wide = count( $qd_item['children'] ) > 4;
?>
<div class="nav-panel dropdown<?php echo $qd_wide ? ' dropdown--wide' : ''; ?>" id="<?php echo esc_attr( $args['id'] ); ?>" data-nav-panel>
	<ul>
		<?php foreach ( $qd_item['children'] as $qd_child ) : ?>
			<li>
				<a href="<?php echo esc_url( $qd_child['url'] ); ?>"<?php echo $qd_child['current'] ? ' aria-current="page"' : ''; ?>>
					<span class="dropdown__title"><?php echo esc_html( $qd_child['title'] ); ?></span>
					<?php if ( $qd_child['desc'] ) : ?>
						<span class="dropdown__desc"><?php echo esc_html( $qd_child['desc'] ); ?></span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( qd_menu_is_page_url( $qd_item['url'] ) ) : ?>
		<div class="dropdown__all">
			<a class="link-more" href="<?php echo esc_url( $qd_item['url'] ); ?>">Xem tất cả <?php echo esc_html( mb_strtolower( $qd_item['title'] ) ); ?><?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
		</div>
	<?php endif; ?>
</div>
