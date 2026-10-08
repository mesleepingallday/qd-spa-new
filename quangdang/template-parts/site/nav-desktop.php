<?php
/**
 * Desktop navigation. Items with grandchildren open a full-width mega panel,
 * others a compact dropdown. Triggers are buttons (disclosure pattern); every
 * panel starts with a "Xem tất cả" link so the hub pages stay one click away.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_tree = qd_menu_tree( 'primary' );
?>
<nav class="primary-nav" aria-label="Menu chính">
	<ul class="primary-nav__list">
		<?php foreach ( $qd_tree as $qd_i => $qd_item ) : ?>
			<?php
			$qd_panel_id = 'nav-panel-' . $qd_i;
			$qd_is_mega  = qd_menu_is_mega( $qd_item );
			$qd_classes  = array( 'primary-nav__item' );
			$qd_classes[] = $qd_is_mega ? 'primary-nav__item--mega' : 'primary-nav__item--dropdown';
			if ( $qd_item['current'] || $qd_item['ancestor'] ) {
				$qd_classes[] = 'is-ancestor';
			}
			?>
			<li class="<?php echo esc_attr( implode( ' ', $qd_classes ) ); ?>" data-nav-item>
				<?php if ( ! $qd_item['children'] ) : ?>
					<a class="primary-nav__link" href="<?php echo esc_url( $qd_item['url'] ); ?>"<?php echo $qd_item['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $qd_item['title'] ); ?></a>
				<?php else : ?>
					<button class="primary-nav__link" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $qd_panel_id ); ?>" data-nav-trigger>
						<?php echo esc_html( $qd_item['title'] ); ?><?php qd_the_icon( 'chevron-down', array( 'size' => 16 ) ); ?>
					</button>
					<?php
					get_template_part(
						$qd_is_mega ? 'template-parts/site/mega-panel' : 'template-parts/site/dropdown',
						null,
						array(
							'item' => $qd_item,
							'id'   => $qd_panel_id,
						)
					);
					?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
