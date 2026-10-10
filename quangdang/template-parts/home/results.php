<?php
/**
 * Before/after results: a curved carousel of the clinic's square posters.
 * Cases are "Kết quả khách hàng" posts (inc/features/results.php); only those with written
 * consent and a poster render, and the section is hidden when there are none.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_results = qd_results();
if ( ! $qd_results ) {
	return;
}
?>
<section class="section section--mist result-arc" aria-labelledby="home-results" data-result-arc>
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'    => 'home-results',
				'title' => 'Kết quả thật từ khách hàng',
				'desc'  => 'Hình ảnh được khách hàng đồng ý chia sẻ. Kết quả có thể khác nhau tùy cơ địa.',
				'align' => 'center',
			)
		);
		?>
	</div>
	<ul class="result-track" role="list" tabindex="0" aria-label="Kết quả khách hàng, vuốt để xem thêm" data-result-track>
		<?php foreach ( $qd_results as $qd_result ) : ?>
			<li class="result-slide">
				<?php get_template_part( 'template-parts/components/result-card', null, array( 'post' => $qd_result ) ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( count( $qd_results ) > 1 ) : ?>
		<div class="result-nav">
			<button type="button" class="result-nav__btn" data-result-prev aria-label="Kết quả trước"><?php echo qd_icon( 'arrow-left', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			<button type="button" class="result-nav__btn" data-result-next aria-label="Kết quả tiếp theo"><?php echo qd_icon( 'arrow-right', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</div>
	<?php endif; ?>
	<dialog class="result-dialog" data-result-dialog aria-labelledby="result-dialog-title">
		<button type="button" class="result-dialog__close" data-result-close aria-label="Đóng"><?php echo qd_icon( 'x', array( 'size' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		<img class="result-dialog__img" src="data:," alt="" data-result-dialog-img>
		<div class="result-dialog__body">
			<p class="result-dialog__title" id="result-dialog-title" data-result-dialog-title></p>
			<ul class="result-dialog__points" data-result-dialog-points></ul>
		</div>
	</dialog>
</section>
