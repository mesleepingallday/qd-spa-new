<?php
/**
 * Services by skin problem: the 7 categories + a quiz tile (8 tiles = clean 4×2 / 2×4 grid).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_categories = qd_service_categories();
if ( ! $qd_categories ) {
	return;
}
?>
<section class="section" aria-labelledby="home-services">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'         => 'home-services',
				'title'      => 'Dịch vụ theo vấn đề da',
				'desc'       => 'Chọn vấn đề bạn đang gặp để xem liệu trình, số buổi và chi phí tham khảo.',
				'link_label' => 'Tất cả dịch vụ',
				'link_url'   => get_post_type_archive_link( 'dich-vu' ),
			)
		);
		?>
		<div class="grid grid--4 category-grid">
			<?php foreach ( $qd_categories as $qd_cat ) : ?>
				<?php get_template_part( 'template-parts/components/category-card', null, array( 'post' => $qd_cat ) ); ?>
			<?php endforeach; ?>
			<div class="quiz-tile">
				<span class="help-card__icon"><?php qd_the_icon( 'scan-face', array( 'size' => 22 ) ); ?></span>
				<h3 class="card__title">Chưa biết bắt đầu từ đâu?</h3>
				<p class="quiz-tile__text">Trả lời 4 câu hỏi về làn da, nhận gợi ý liệu trình phù hợp trong 1 phút.</p>
				<?php echo qd_button( 'Kiểm tra da', home_url( '/tim-lieu-trinh/' ), array( 'variant' => 'outline', 'size' => 'sm', 'icon_end' => 'arrow-right' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
</section>
