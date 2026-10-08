<?php
/**
 * Full-width mega panel (Dịch vụ).
 *
 * Children of the top item are ZONES (columns separated by a rule):
 *   - a zone whose children have children (Chăm sóc và điều trị da) renders GROUPS:
 *       · a group with categories (Điều trị da) → masonry columns: category title + services
 *       · a group with plain links (Chăm sóc da) → links in 3 columns
 *   - a zone with plain links (Nội khoa thẩm mỹ) → a single list
 * The last zone ends with the quiz help card for visitors who don't know what they need.
 *
 * @package QuangDang
 *
 * @var array $args { item, id }
 */

defined( 'ABSPATH' ) || exit;

$qd_item  = $args['item'];
$qd_zones = $qd_item['children'];
$qd_split = count( $qd_zones ) > 1 && qd_menu_is_mega( $qd_zones[0] );

$qd_links = function ( $nodes, $class = '' ) {
	echo '<ul class="mega__links ' . esc_attr( $class ) . '">';
	foreach ( $nodes as $node ) {
		printf(
			'<li><a href="%s"%s>%s</a></li>',
			esc_url( $node['url'] ),
			$node['current'] ? ' aria-current="page"' : '',
			esc_html( $node['title'] )
		);
	}
	echo '</ul>';
};

$qd_title_link = function ( $node, $class ) {
	if ( qd_menu_is_page_url( $node['url'] ) ) {
		printf( '<a class="%s" href="%s">%s</a>', esc_attr( $class ), esc_url( $node['url'] ), esc_html( $node['title'] ) );
	} else {
		echo esc_html( $node['title'] );
	}
};
?>
<div class="nav-panel mega" id="<?php echo esc_attr( $args['id'] ); ?>" data-nav-panel>
	<div class="container mega__inner">
		<div class="mega__zones<?php echo $qd_split ? ' mega__zones--split' : ''; ?>">
			<?php foreach ( $qd_zones as $qd_zi => $qd_zone ) : ?>
				<div class="mega__zone">
					<p class="mega__zone-title">
						<span><?php $qd_title_link( $qd_zone, '' ); ?></span>
						<?php if ( 0 === $qd_zi && qd_menu_is_page_url( $qd_item['url'] ) ) : ?>
							<a class="link-more" href="<?php echo esc_url( $qd_item['url'] ); ?>">Xem tất cả dịch vụ<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
						<?php endif; ?>
					</p>

					<?php if ( qd_menu_is_mega( $qd_zone ) ) : ?>
						<?php foreach ( $qd_zone['children'] as $qd_group ) : ?>
							<div class="mega__group">
								<p class="mega__group-title"><?php $qd_title_link( $qd_group, '' ); ?></p>
								<?php if ( qd_menu_is_mega( $qd_group ) ) : ?>
									<div class="mega__cols">
										<?php foreach ( $qd_group['children'] as $qd_cat ) : ?>
											<div class="mega__col">
												<?php if ( $qd_cat['children'] ) : ?>
													<a class="mega__col-title" href="<?php echo esc_url( $qd_cat['url'] ); ?>"><?php echo esc_html( $qd_cat['title'] ); ?><?php qd_the_icon( 'chevron-right', array( 'size' => 15 ) ); ?></a>
													<?php $qd_links( $qd_cat['children'] ); ?>
												<?php else : ?>
													<?php $qd_links( array( $qd_cat ) ); ?>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>
								<?php else : ?>
									<?php $qd_links( $qd_group['children'], 'mega__links--cols' ); ?>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<?php $qd_links( $qd_zone['children'] ? $qd_zone['children'] : array( $qd_zone ) ); ?>
					<?php endif; ?>

					<?php if ( count( $qd_zones ) - 1 === $qd_zi ) : ?>
						<?php get_template_part( 'template-parts/components/help-card' ); ?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
