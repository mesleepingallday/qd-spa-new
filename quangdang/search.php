<?php
/**
 * Search results, grouped: Dịch vụ (service cards) then Bài viết (post cards).
 * The main query includes both post types (inc/features/blog.php).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$qd_query    = get_search_query();
$qd_services = array();
$qd_articles = array();
foreach ( $wp_query->posts as $qd_hit ) {
	if ( 'dich-vu' === $qd_hit->post_type ) {
		$qd_services[] = $qd_hit;
	} else {
		$qd_articles[] = $qd_hit;
	}
}
$qd_total = count( $qd_services ) + count( $qd_articles );

get_header();
?>
<section class="page-hero search-hero">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<h1 class="page-title">
			<?php if ( '' === $qd_query ) : ?>
				Tìm kiếm
			<?php else : ?>
				Kết quả cho “<?php echo esc_html( $qd_query ); ?>”
			<?php endif; ?>
		</h1>
		<?php if ( '' !== $qd_query ) : ?>
			<p class="lead" role="status">
				<?php
				echo $qd_total
					? esc_html( sprintf( 'Tìm thấy %d kết quả: %d dịch vụ, %d bài viết.', $qd_total, count( $qd_services ), count( $qd_articles ) ) )
					: 'Chưa tìm thấy kết quả phù hợp.';
				?>
			</p>
		<?php endif; ?>

		<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="sr-only" for="search-page-input">Từ khóa tìm kiếm</label>
			<?php qd_the_icon( 'search', array( 'size' => 20 ) ); ?>
			<input class="input" id="search-page-input" type="search" name="s" value="<?php echo esc_attr( $qd_query ); ?>" placeholder="VD: trị mụn, nám, triệt lông…" autocomplete="off">
			<button class="btn btn--primary" type="submit"><span>Tìm kiếm</span></button>
		</form>
	</div>
</section>

<?php if ( $qd_total ) : ?>
	<?php if ( $qd_services ) : ?>
		<section class="section section--tight" aria-labelledby="search-services">
			<div class="container">
				<?php
				qd_section_head(
					array(
						'id'    => 'search-services',
						'title' => sprintf( 'Dịch vụ (%d)', count( $qd_services ) ),
					)
				);
				?>
				<div class="grid grid--3 search-results">
					<?php foreach ( $qd_services as $qd_hit ) : ?>
						<?php get_template_part( 'template-parts/components/service-card', null, array( 'post' => $qd_hit, 'kicker' => true ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $qd_articles ) : ?>
		<section class="section section--tight<?php echo $qd_services ? ' section--mint' : ''; ?>" aria-labelledby="search-posts">
			<div class="container">
				<?php
				qd_section_head(
					array(
						'id'    => 'search-posts',
						'title' => sprintf( 'Bài viết (%d)', count( $qd_articles ) ),
					)
				);
				?>
				<div class="grid grid--3 search-results">
					<?php foreach ( $qd_articles as $qd_hit ) : ?>
						<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_hit ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
<?php else : ?>
	<section class="section section--tight" aria-labelledby="search-empty">
		<div class="container">
			<div class="blog-empty">
				<span class="icon-tile"><?php qd_the_icon( 'search', array( 'size' => 24 ) ); ?></span>
				<h2 class="blog-empty__title" id="search-empty">
					<?php echo '' === $qd_query ? 'Bạn muốn tìm gì?' : 'Không có kết quả cho “' . esc_html( $qd_query ) . '”'; ?>
				</h2>
				<p>Thử từ khóa ngắn hơn (VD: “mụn”, “nám”) hoặc chọn vấn đề da bạn đang quan tâm:</p>
				<ul class="concern-grid concern-grid--narrow blog-empty__concerns">
					<?php foreach ( qd_concerns() as $qd_key => $qd_concern ) : ?>
						<li><?php get_template_part( 'template-parts/components/concern-tile', null, array( 'key' => $qd_key, 'concern' => $qd_concern ) ); ?></li>
					<?php endforeach; ?>
				</ul>
				<p>Chưa biết nên chọn liệu trình nào?</p>
				<div class="btn-row">
					<?php echo qd_button( 'Tìm dịch vụ phù hợp', home_url( '/tim-lieu-trinh/' ), array( 'icon' => 'sparkles' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo qd_button( 'Hỏi bác sĩ qua Zalo', qd_clinic( 'zalo' ), array( 'variant' => 'ghost', 'icon' => 'message-circle', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $qd_total ) : ?>
	<section class="section section--flush-top">
		<div class="container">
			<p class="search-more">Không thấy điều bạn cần? <a href="<?php echo esc_url( home_url( '/tim-lieu-trinh/' ) ); ?>">Tìm dịch vụ phù hợp</a> bằng vài câu hỏi ngắn.</p>
			<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'search' ) ); ?>
		</div>
	</section>
<?php endif; ?>
<?php
get_footer();
