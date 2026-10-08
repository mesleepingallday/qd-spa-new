<?php
/**
 * Tab bar linking the six About subpages. Scrolls sideways when it does not fit;
 * the current page is marked with aria-current and scrolled into view (assets/js/about.js).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_current = is_page() ? (string) get_post_field( 'post_name', get_queried_object_id() ) : '';
?>
<nav class="about-subnav" aria-label="Các trang giới thiệu" data-about-subnav>
	<div class="container">
		<ul class="about-subnav__list">
			<?php foreach ( qd_about_pages() as $qd_slug => $qd_page ) : ?>
				<li>
					<a href="<?php echo esc_url( qd_about_url( $qd_slug ) ); ?>"<?php echo $qd_slug === $qd_current ? ' aria-current="page"' : ''; ?>>
						<?php qd_the_icon( $qd_page[3], array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_page[0] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>
