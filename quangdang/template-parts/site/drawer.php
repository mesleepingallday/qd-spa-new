<?php
/**
 * Mobile drawer with drill-down panels (the service tree is 4 levels deep;
 * nested accordions get unusable, panels keep every list one screen tall).
 *
 * Panel(N) rules:
 *   - leaf child               → row link
 *   - "zone" child             → its children become sections (header + rows)
 *   - other child with kids    → one section (header + rows)
 *   - a row with children      → drills into its own panel (›)
 * Root panel lists top items as rows. Max depth to any service: Dịch vụ › Điều trị mụn › service.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_tree   = qd_menu_tree( 'primary' );
$qd_panels = array();
$qd_seq    = 0;

/**
 * Queue a node for its own panel and return the panel id.
 */
$qd_queue = function ( $node, $parent_id ) use ( &$qd_panels, &$qd_seq ) {
	$id          = 'drawer-panel-' . ( ++$qd_seq );
	$qd_panels[] = array( $id, $node, $parent_id );
	return $id;
};

/**
 * One row: drill button if it has children, link otherwise.
 */
$qd_row = function ( $node, $panel_id ) use ( $qd_queue ) {
	if ( $node['children'] ) {
		$target = $qd_queue( $node, $panel_id );
		return sprintf(
			'<li><button class="drawer__row" type="button" data-drawer-go="%s" aria-controls="%s"><span>%s</span>%s</button></li>',
			esc_attr( $target ),
			esc_attr( $target ),
			esc_html( $node['title'] ),
			qd_icon( 'chevron-right', array( 'size' => 20 ) )
		);
	}
	return sprintf(
		'<li><a class="drawer__row" href="%s"%s>%s</a></li>',
		esc_url( $node['url'] ),
		$node['current'] ? ' aria-current="page"' : '',
		esc_html( $node['title'] )
	);
};

/**
 * Body of panel(N): sections per the rules above.
 */
$qd_panel_body = function ( $node, $panel_id ) use ( $qd_row ) {
	$html  = '';
	$loose = '';
	$section = function ( $group ) use ( $qd_row, $panel_id ) {
		$rows = '';
		foreach ( $group['children'] as $child ) {
			$rows .= $qd_row( $child, $panel_id );
		}
		$all = qd_menu_is_page_url( $group['url'] )
			? '<a href="' . esc_url( $group['url'] ) . '">Xem tất cả</a>'
			: '';
		return '<p class="drawer__section-title"><span>' . esc_html( $group['title'] ) . '</span>' . $all . '</p><ul class="drawer__list">' . $rows . '</ul>';
	};
	foreach ( $node['children'] as $child ) {
		if ( ! $child['children'] ) {
			$loose .= $qd_row( $child, $panel_id );
		} elseif ( qd_menu_is_zone( $child ) ) {
			foreach ( $child['children'] as $group ) {
				$html .= $section( $group );
			}
		} else {
			$html .= $section( $child );
		}
	}
	return ( $loose ? '<ul class="drawer__list">' . $loose . '</ul>' : '' ) . $html;
};

// Root panel rows (queues first-level panels).
$qd_root_rows = '';
foreach ( $qd_tree as $qd_item ) {
	$qd_root_rows .= $qd_row( $qd_item, 'drawer-root' );
}
?>
<div class="drawer" id="drawer" data-drawer aria-hidden="true">
	<div class="drawer__overlay" data-drawer-close></div>
	<div class="drawer__sheet" role="dialog" aria-modal="true" aria-label="Menu">
		<div class="drawer__head">
			<?php get_template_part( 'template-parts/site/brand' ); ?>
			<button class="icon-btn" type="button" data-drawer-close>
				<?php qd_the_icon( 'x', array( 'size' => 24 ) ); ?><span class="sr-only">Đóng menu</span>
			</button>
		</div>

		<div class="drawer__search">
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php qd_the_icon( 'search', array( 'size' => 18 ) ); ?>
				<label class="sr-only" for="drawer-search">Tìm kiếm</label>
				<input class="input" id="drawer-search" type="search" name="s" placeholder="Tìm dịch vụ, bài viết…" autocomplete="off">
			</form>
		</div>

		<div class="drawer__panels">
			<div class="drawer__panel is-active" id="drawer-root" data-drawer-panel>
				<ul class="drawer__list"><?php echo $qd_root_rows; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $qd_row. ?></ul>
				<ul class="drawer__list">
					<li><a class="drawer__row" href="<?php echo esc_url( home_url( '/tim-lieu-trinh/' ) ); ?>"><span>Tìm dịch vụ phù hợp</span><?php qd_the_icon( 'scan-face', array( 'size' => 20 ) ); ?></a></li>
					<li><a class="drawer__row" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>"><span>Liên hệ &amp; chỉ đường</span><?php qd_the_icon( 'map-pin', array( 'size' => 20 ) ); ?></a></li>
				</ul>
				<p class="drawer__address"><?php qd_the_icon( 'map-pin', array( 'size' => 15 ) ); ?><span><?php echo esc_html( qd_clinic( 'address' ) ); ?><br><?php echo esc_html( qd_clinic( 'hours' ) ); ?></span></p>
			</div>
			<?php
			// Panels can queue deeper panels while rendering, so walk the growing list.
			for ( $qd_p = 0; $qd_p < count( $qd_panels ); $qd_p++ ) : // phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall
				list( $qd_pid, $qd_node, $qd_parent ) = $qd_panels[ $qd_p ];
				$qd_body                              = $qd_panel_body( $qd_node, $qd_pid );
				?>
				<div class="drawer__panel" id="<?php echo esc_attr( $qd_pid ); ?>" data-drawer-panel data-parent="<?php echo esc_attr( $qd_parent ); ?>">
					<button class="drawer__back" type="button" data-drawer-back>
						<?php qd_the_icon( 'chevron-left', array( 'size' => 20 ) ); ?><?php echo esc_html( $qd_node['title'] ); ?>
					</button>
					<?php if ( qd_menu_is_page_url( $qd_node['url'] ) ) : ?>
						<ul class="drawer__list">
							<li><a class="drawer__row drawer__row--all" href="<?php echo esc_url( $qd_node['url'] ); ?>">Xem tất cả <?php echo esc_html( mb_strtolower( $qd_node['title'] ) ); ?><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></a></li>
						</ul>
					<?php endif; ?>
					<?php echo $qd_body; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in builders. ?>
				</div>
			<?php endfor; ?>
		</div>

		<div class="drawer__foot">
			<a class="action-bar__btn" href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php qd_the_icon( 'phone', array( 'size' => 22 ) ); ?>Gọi</a>
			<a class="action-bar__btn" href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 22 ) ); ?>Zalo</a>
			<?php echo qd_button( 'Đặt lịch khám', qd_booking_url(), array( 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</div>
