<?php
/**
 * Latest beauty-handbook posts: one featured + a compact list.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_posts = get_posts(
	array(
		'category_name'  => 'cam-nang-lam-dep',
		'posts_per_page' => 4,
	)
);
if ( ! $qd_posts ) {
	return;
}
$qd_featured = array_shift( $qd_posts );
$qd_cat      = get_category_by_slug( 'cam-nang-lam-dep' );
?>
<section class="section" aria-labelledby="home-articles">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'         => 'home-articles',
				'title'      => 'Cẩm nang làm đẹp',
				'desc'       => 'Kiến thức chăm sóc và điều trị da, được bác sĩ da liễu tham vấn.',
				'link_label' => 'Xem tất cả bài viết',
				'link_url'   => $qd_cat ? get_category_link( $qd_cat ) : home_url( '/tin-tuc/' ),
			)
		);
		?>
		<div class="article-split">
			<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_featured ) ); ?>
			<ul class="article-list">
				<?php foreach ( $qd_posts as $qd_post ) : ?>
					<li class="article-row">
						<?php echo qd_thumb( $qd_post, 'qd-card', '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<div>
							<h3 class="article-row__title"><a href="<?php echo esc_url( get_permalink( $qd_post ) ); ?>"><?php echo esc_html( get_the_title( $qd_post ) ); ?></a></h3>
							<?php echo qd_post_meta_line( $qd_post ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
