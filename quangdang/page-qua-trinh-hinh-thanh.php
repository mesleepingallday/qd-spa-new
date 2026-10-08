<?php
/**
 * About: Quá trình hình thành — vertical timeline of milestones.
 * Milestones come from the page's "Các mốc phát triển" box (Năm | Tiêu đề | Mô tả per line).
 * Until the clinic fills them in, clearly-marked sample rows show; no real-looking facts are invented.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	$qd_rows   = qd_pipe_rows( get_post_meta( get_the_ID(), '_qd_timeline', true ), 3 );
	$qd_sample = ! $qd_rows;
	if ( $qd_sample && qd_show_placeholders() ) {
		$qd_rows = array(
			array( '20xx (cần điền)', 'Những ngày đầu thành lập', 'Phòng khám mở cửa với đội ngũ nhỏ và một mong muốn rõ ràng: mang dịch vụ da liễu chuẩn y khoa đến gần người dân Quỳnh Lưu.' ),
			array( '20xx (cần điền)', 'Mở rộng dịch vụ điều trị', 'Bổ sung các liệu trình điều trị mụn, nám, sẹo và chăm sóc da theo phác đồ riêng cho từng người.' ),
			array( '20xx (cần điền)', 'Đầu tư thiết bị công nghệ cao', 'Trang bị máy laser và thiết bị chăm sóc da mới, có nguồn gốc rõ ràng, được bảo trì định kỳ.' ),
			array( '20xx (cần điền)', 'Chuyển về cơ sở mới', 'Về không gian rộng hơn tại TTTM Đức Tài – Tâm Đạt, tách riêng khu thăm khám, điều trị và tư vấn.' ),
			array( '20xx (cần điền)', 'Mở các khóa đào tạo', 'Chia sẻ kiến thức và kỹ thuật chăm sóc da an toàn cho kỹ thuật viên và người mới vào nghề.' ),
		);
	}

	get_template_part( 'template-parts/about/hero' );
	?>

	<?php if ( $qd_rows ) : ?>
	<section class="section" aria-labelledby="timeline-title">
		<div class="container container--narrow">
			<?php
			qd_section_head(
				array(
					'id'    => 'timeline-title',
					'title' => 'Những mốc đáng nhớ',
				)
			);
			?>
			<?php if ( $qd_sample ) : ?>
				<p class="notice about-sample-note">
					<?php qd_the_icon( 'info', array( 'size' => 18 ) ); ?>
					<span><strong>Nội dung mẫu.</strong> Các mốc dưới đây chỉ để giữ chỗ. Điền thông tin thật tại Trang → Quá trình hình thành → "Các mốc phát triển".</span>
				</p>
			<?php endif; ?>
			<ol class="timeline">
				<?php foreach ( $qd_rows as list( $qd_year, $qd_title, $qd_text ) ) : ?>
					<li class="timeline__item">
						<span class="timeline__dot" aria-hidden="true"></span>
						<p class="timeline__year"><?php echo esc_html( $qd_year ); ?></p>
						<h3 class="timeline__title"><?php echo esc_html( $qd_title ); ?></h3>
						<?php if ( $qd_text ) : ?>
							<p class="timeline__text"><?php echo esc_html( $qd_text ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php endif; ?>

	<?php $qd_content = qd_about_content(); ?>
	<?php if ( $qd_content ) : ?>
		<section class="section section--mint section--tight" aria-label="Câu chuyện của phòng khám">
			<div class="container container--narrow">
				<div class="prose"><?php echo $qd_content; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content filter output. ?></div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section" aria-label="Đặt lịch">
		<div class="container">
			<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'about' ) ); ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
