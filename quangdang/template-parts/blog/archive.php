<?php
/**
 * Blog list for the news page, a category or a topic (tag).
 *
 * Job: "I want to learn about my skin problem" → pick a topic → read.
 * Hero (H1, lead, category tabs, topic chips) → featured post (page 1) → 3-column grid → pagination.
 * In Sự kiện – Ưu đãi, running promotions come first (see inc/features/blog.php) and the ended
 * ones are grouped under their own heading and de-emphasised.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$qd_news_url = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/tin-tuc/' );
$qd_queried  = get_queried_object();
$qd_is_promo = is_category( 'su-kien-uu-dai' );
$qd_paged    = max( 1, (int) get_query_var( 'paged' ) );
$qd_service  = is_tag() && $qd_queried ? get_page_by_path( $qd_queried->slug, OBJECT, 'dich-vu' ) : null;

if ( is_home() ) {
	$qd_title = single_post_title( '', false ) ?: 'Tin tức';
	$qd_lead  = 'Kiến thức chăm sóc da từ bác sĩ da liễu và các chương trình ưu đãi mới nhất của phòng khám.';
} elseif ( is_category() ) {
	$qd_title = single_cat_title( '', false );
	$qd_lead  = trim( wp_strip_all_tags( category_description() ) );
} else {
	$qd_title = single_tag_title( '', false );
	$qd_lead  = 'Các bài viết về ' . mb_strtolower( $qd_title ) . ' do bác sĩ da liễu tham vấn.';
}

// Category tabs: real links, the current one is marked.
$qd_tabs = array(
	array( 'Tất cả', $qd_news_url, is_home() ),
);
foreach ( array( 'cam-nang-lam-dep', 'su-kien-uu-dai' ) as $qd_slug ) {
	$qd_term = get_category_by_slug( $qd_slug );
	if ( $qd_term ) {
		$qd_tabs[] = array( $qd_term->name, get_category_link( $qd_term ), is_category( $qd_slug ) );
	}
}

// Topic chips: tags that have posts, most used first.
$qd_topics = get_tags(
	array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);

// Split the page: featured + the rest. Ended promotions go under their own heading.
$qd_posts    = $wp_query->posts;
$qd_featured = null;
if ( $qd_posts && 1 === $qd_paged ) {
	$qd_first = qd_promo_status( $qd_posts[0] );
	if ( ! $qd_first || 'ended' !== $qd_first['state'] ) {
		$qd_featured = array_shift( $qd_posts );
	}
}
$qd_current = array();
$qd_ended   = array();
foreach ( $qd_posts as $qd_item ) {
	$qd_status = $qd_is_promo ? qd_promo_status( $qd_item ) : null;
	if ( $qd_status && 'ended' === $qd_status['state'] ) {
		$qd_ended[] = $qd_item;
	} else {
		$qd_current[] = $qd_item;
	}
}
?>
<section class="page-hero blog-hero">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<div class="blog-hero__head">
			<div class="blog-hero__text">
			<h1 class="page-title"><?php echo esc_html( $qd_title ); ?></h1>
			<?php if ( $qd_lead ) : ?>
				<p class="lead"><?php echo esc_html( $qd_lead ); ?></p>
			<?php endif; ?>
			<?php if ( $qd_service ) : ?>
				<a class="link-more blog-hero__service" href="<?php echo esc_url( get_permalink( $qd_service ) ); ?>">Xem dịch vụ <?php echo esc_html( mb_strtolower( $qd_service->post_title ) ); ?><?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
			<?php endif; ?>
			</div>
			<nav class="blog-tabs" aria-label="Chuyên mục tin tức">
				<ul>
					<?php foreach ( $qd_tabs as list( $qd_label, $qd_url, $qd_active ) ) : ?>
						<li><a href="<?php echo esc_url( $qd_url ); ?>"<?php echo $qd_active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $qd_label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>

		<?php if ( $qd_topics ) : ?>
			<nav class="blog-topics" aria-label="Chủ đề">
				<span class="blog-topics__label"><?php qd_the_icon( 'tag', array( 'size' => 16 ) ); ?>Chủ đề</span>
				<ul class="blog-topics__list">
					<?php foreach ( $qd_topics as $qd_topic ) : ?>
						<li><a class="chip<?php echo is_tag( $qd_topic->slug ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_tag_link( $qd_topic ) ); ?>"<?php echo is_tag( $qd_topic->slug ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $qd_topic->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
</section>

<section class="section section--tight blog-list" aria-label="Danh sách bài viết">
	<div class="container">
		<?php if ( $qd_featured ) : ?>
			<?php get_template_part( 'template-parts/blog/featured', null, array( 'post' => $qd_featured ) ); ?>
		<?php endif; ?>

		<?php if ( $qd_current ) : ?>
			<div class="grid grid--3 blog-grid">
				<?php foreach ( $qd_current as $qd_item ) : ?>
					<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_item, 'heading' => 'h2' ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $qd_ended ) : ?>
			<h2 class="blog-subhead">Chương trình đã kết thúc</h2>
			<div class="grid grid--3 blog-grid blog-grid--ended">
				<?php foreach ( $qd_ended as $qd_item ) : ?>
					<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_item, 'heading' => 'h3' ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! $qd_featured && ! $qd_current && ! $qd_ended ) : ?>
			<div class="blog-empty">
				<span class="icon-tile"><?php qd_the_icon( 'newspaper', array( 'size' => 24 ) ); ?></span>
				<p class="blog-empty__title">Chưa có bài viết trong mục này</p>
				<p>Bác sĩ đang chuẩn bị nội dung mới. Bạn có thể xem các bài viết khác hoặc đặt lịch để được tư vấn trực tiếp.</p>
				<div class="btn-row">
					<?php
					echo qd_button( 'Xem tất cả bài viết', $qd_news_url, array( 'variant' => 'outline', 'icon' => 'newspaper' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo qd_button( 'Đặt lịch tư vấn', qd_booking_url(), array( 'variant' => 'ghost', 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php
		the_posts_pagination(
			array(
				'mid_size'           => 1,
				'prev_text'          => qd_icon( 'chevron-left', array( 'size' => 18 ) ) . '<span class="pager-label">Trước</span>',
				'next_text'          => '<span class="pager-label">Sau</span>' . qd_icon( 'chevron-right', array( 'size' => 18 ) ),
				'screen_reader_text' => 'Phân trang bài viết',
				'aria_label'         => 'Phân trang bài viết',
			)
		);
		?>
	</div>
</section>

<section class="section section--flush-top">
	<div class="container">
		<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'blog' ) ); ?>
	</div>
</section>
