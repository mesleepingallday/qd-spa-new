<?php
/**
 * First-visit process — answers "what happens when I come in?" for first-timers.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_steps = array(
	array( 'Thăm khám', 'Bác sĩ hỏi bệnh sử, thói quen chăm sóc da và mong muốn của bạn.' ),
	array( 'Soi da', 'Máy soi da phân tích mụn, sắc tố, độ ẩm và lỗ chân lông.' ),
	array( 'Phác đồ riêng', 'Tư vấn liệu trình, số buổi và chi phí rõ ràng trước khi làm.' ),
	array( 'Điều trị', 'Thực hiện tại phòng điều trị vô khuẩn, đúng chỉ định.' ),
	array( 'Tái khám', 'Theo dõi kết quả, điều chỉnh phác đồ, hỗ trợ qua Zalo.' ),
);
?>
<section class="section section--dark process" aria-labelledby="home-process">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'    => 'home-process',
				'title' => 'Lần đầu đến khám diễn ra thế nào?',
				'desc'  => 'Năm bước, theo thứ tự, giống nhau cho mọi khách hàng.',
			)
		);
		?>
		<ol class="steps">
			<?php foreach ( $qd_steps as $qd_i => list( $qd_title, $qd_text ) ) : ?>
				<li class="steps__item">
					<span class="steps__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $qd_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3 class="steps__title"><?php echo esc_html( $qd_title ); ?></h3>
					<p class="steps__text"><?php echo esc_html( $qd_text ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
