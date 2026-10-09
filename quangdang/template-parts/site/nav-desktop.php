<?php
/**
 * Desktop navigation. Items with grandchildren open a full-width mega panel, others a compact
 * dropdown. A parent with a real page is a link plus a separate disclosure button (the link works
 * without JavaScript); every panel also starts with a "Xem tất cả" link.
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
					<?php if ( qd_menu_is_page_url( $qd_item['url'] ) ) : ?>
						<?php // A real link to the hub page plus a separate disclosure button: the link works without JavaScript. ?>
						<a class="primary-nav__link primary-nav__link--split" href="<?php echo esc_url( $qd_item['url'] ); ?>"<?php echo $qd_item['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $qd_item['title'] ); ?></a>
						<button class="primary-nav__toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $qd_panel_id ); ?>" data-nav-trigger>
							<?php qd_the_icon( 'chevron-down', array( 'size' => 16 ) ); ?><span class="sr-only">Mở menu <?php echo esc_html( $qd_item['title'] ); ?></span>
						</button>
					<?php else : ?>
						<button class="primary-nav__link" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $qd_panel_id ); ?>" data-nav-trigger>
							<?php echo esc_html( $qd_item['title'] ); ?><?php qd_the_icon( 'chevron-down', array( 'size' => 16 ) ); ?>
						</button>
					<?php endif; ?>
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
