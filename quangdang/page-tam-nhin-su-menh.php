<?php
/**
 * About: Tầm nhìn & Sứ mệnh — vision and mission statements, then core values.
 * The statements are drafts; the clinic should confirm or replace them in the editor
 * (editor content, when present, is shown under the values).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	$qd_values = apply_filters(
		'qd_about_values',
		array(
			array( 'stethoscope', 'Y khoa là nền tảng', 'Mọi liệu trình đều bắt đầu từ thăm khám và chẩn đoán của bác sĩ, không bắt đầu từ bảng giá.' ),
			array( 'shield-check', 'An toàn là ưu tiên số một', 'Dụng cụ vô khuẩn, thiết bị và sản phẩm chính hãng, quy trình rõ ràng ở từng bước.' ),
			array( 'badge-check', 'Trung thực với khách hàng', 'Nói rõ điều gì làm được, điều gì chưa phù hợp, kết quả có thể khác nhau tùy cơ địa.' ),
			array( 'hand-heart', 'Tận tâm, tôn trọng riêng tư', 'Lắng nghe, giải thích dễ hiểu và đồng hành cùng bạn cho đến buổi tái khám cuối.' ),
		)
	);

	get_template_part( 'template-parts/about/hero' );
	?>

	<section class="section" aria-labelledby="vision-mission">
		<div class="container">
			<h2 class="sr-only" id="vision-mission">Tầm nhìn và sứ mệnh</h2>
			<div class="statement-grid">
				<article class="statement statement--vision">
					<span class="icon-tile"><?php qd_the_icon( 'eye', array( 'size' => 24 ) ); ?></span>
					<h3 class="statement__label">Tầm nhìn</h3>
					<p class="statement__text">Trở thành địa chỉ da liễu thẩm mỹ được người dân Nghệ An tin tưởng: nơi mọi người được bác sĩ thăm khám tử tế, điều trị đúng và làm đẹp an toàn.</p>
				</article>
				<article class="statement statement--mission">
					<span class="icon-tile"><?php qd_the_icon( 'heart', array( 'size' => 24 ) ); ?></span>
					<h3 class="statement__label">Sứ mệnh</h3>
					<p class="statement__text">Mang dịch vụ chăm sóc và điều trị da chuẩn y khoa đến gần mọi người, với chi phí rõ ràng và sự đồng hành của đội ngũ bác sĩ trong suốt liệu trình.</p>
				</article>
			</div>
		</div>
	</section>

	<section class="section section--mint" aria-labelledby="core-values">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'core-values',
					'title' => 'Giá trị chúng tôi giữ',
					'desc'  => 'Bốn nguyên tắc dẫn đường cho cách phòng khám làm việc mỗi ngày.',
				)
			);
			?>
			<ul class="grid grid--4 value-grid">
				<?php foreach ( $qd_values as list( $qd_icon, $qd_title, $qd_text ) ) : ?>
					<li class="card value-card">
						<span class="icon-tile icon-tile--sm"><?php qd_the_icon( $qd_icon, array( 'size' => 20 ) ); ?></span>
						<h3 class="card__title"><?php echo esc_html( $qd_title ); ?></h3>
						<p class="value-card__text"><?php echo esc_html( $qd_text ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<?php $qd_content = qd_about_content(); ?>
	<?php if ( $qd_content ) : ?>
		<section class="section section--tight" aria-label="Chi tiết">
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
