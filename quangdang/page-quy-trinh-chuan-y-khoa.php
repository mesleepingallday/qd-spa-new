<?php
/**
 * About: Quy trình chuẩn y khoa — the medical process in detail (vertical steps)
 * + hygiene and safety standards. Same five stages as the home page, with richer descriptions.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	// [title, description, what you get]
	$qd_steps = apply_filters(
		'qd_about_process',
		array(
			array(
				'Thăm khám cùng bác sĩ',
				'Bác sĩ hỏi về vấn đề da, thói quen chăm sóc, bệnh nền, thuốc đang dùng và tiền sử dị ứng. Đây là bước quan trọng để chọn đúng hướng điều trị và tránh rủi ro.',
				array( 'Được lắng nghe, không bị hối thúc', 'Hồ sơ lưu riêng, bảo mật' ),
			),
			array(
				'Soi da và đánh giá tình trạng',
				'Da được soi kỹ để thấy cả những vấn đề chưa lộ rõ bên ngoài: độ ẩm, sắc tố, lỗ chân lông, mức độ viêm. Bác sĩ giải thích kết quả bằng hình ảnh dễ hiểu.',
				array( 'Biết chính xác da đang gặp gì', 'Soi da và tư vấn miễn phí' ),
			),
			array(
				'Xây dựng phác đồ riêng',
				'Bác sĩ đề xuất liệu trình theo tình trạng da, mục tiêu và ngân sách của bạn, nói rõ số buổi, thời gian nghỉ dưỡng, lợi ích và rủi ro có thể gặp. Bạn có thời gian cân nhắc.',
				array( 'Báo giá rõ ràng từ đầu', 'Chỉ làm khi bạn đồng ý' ),
			),
			array(
				'Thực hiện điều trị',
				'Chuyên viên thực hiện dưới sự giám sát của bác sĩ, dùng dụng cụ vô khuẩn, thiết bị và sản phẩm chính hãng. Thông số máy được chọn theo da của bạn và theo dõi suốt buổi.',
				array( 'Vô khuẩn, an toàn', 'Bác sĩ có mặt xử lý khi cần' ),
			),
			array(
				'Chăm sóc sau điều trị và tái khám',
				'Bạn nhận hướng dẫn chăm sóc tại nhà bằng lời và bằng tin nhắn. Đến hẹn tái khám, bác sĩ đánh giá lại kết quả và điều chỉnh liệu trình nếu cần.',
				array( 'Hỏi bác sĩ qua Zalo khi cần', 'Nhắc lịch tái khám' ),
			),
		)
	);

	// Standards: [heading, icon, items]
	$qd_standards = apply_filters(
		'qd_about_standards',
		array(
			array(
				'Vệ sinh và vô khuẩn',
				'droplets',
				array( 'Dụng cụ dùng lại được tiệt khuẩn theo quy trình sau mỗi khách.', 'Kim, đầu tip và vật tư dùng một lần mở ngay trước mặt bạn.', 'Giường, thiết bị và phòng điều trị được làm sạch giữa các lượt khách.', 'Nhân viên rửa tay, đeo găng và khẩu trang khi thực hiện.' ),
			),
			array(
				'An toàn cho người bệnh',
				'shield-check',
				array( 'Khai báo tiền sử dị ứng và bệnh nền trước mọi liệu trình.', 'Thử phản ứng da khi cần trước khi điều trị diện rộng.', 'Thiết bị được bảo trì, hiệu chuẩn định kỳ.', 'Có quy trình xử lý khi xảy ra phản ứng bất thường.' ),
			),
			array(
				'Minh bạch và riêng tư',
				'badge-check',
				array( 'Báo giá và liệu trình được giải thích trước khi bạn đồng ý.', 'Hình ảnh trước – sau chỉ dùng khi bạn cho phép bằng văn bản.', 'Thông tin cá nhân và hồ sơ khám được bảo mật.', 'Bạn có quyền dừng hoặc hỏi lại ở bất kỳ bước nào.' ),
			),
		)
	);

	$qd_prepare = array(
		'Mang theo thuốc, mỹ phẩm bạn đang dùng (hoặc chụp ảnh nhãn).',
		'Cho bác sĩ biết nếu bạn đang mang thai, cho con bú hoặc có bệnh nền.',
		'Không cần trang điểm. Nếu đã trang điểm, phòng khám có sẵn tẩy trang.',
		'Dành khoảng 45–60 phút cho buổi khám đầu tiên.',
	);

	get_template_part( 'template-parts/about/hero' );
	?>

	<section class="section" aria-labelledby="process-title">
		<div class="container">
			<div class="with-aside">
				<div>
					<?php
					qd_section_head(
						array(
							'id'    => 'process-title',
							'title' => 'Từ lần đầu đến buổi tái khám',
							'desc'  => 'Năm bước bạn sẽ đi qua. Ở bước nào bạn cũng có thể hỏi lại hoặc dừng.',
						)
					);
					?>
					<ol class="steps steps--vertical process-steps">
						<?php foreach ( $qd_steps as $qd_i => list( $qd_title, $qd_text, $qd_gets ) ) : ?>
							<li class="steps__item">
								<span class="steps__num" aria-hidden="true"><?php echo (int) $qd_i + 1; ?></span>
								<h3 class="steps__title"><?php echo esc_html( $qd_title ); ?></h3>
								<div class="steps__body">
									<p class="steps__text"><?php echo esc_html( $qd_text ); ?></p>
									<?php if ( $qd_gets ) : ?>
										<ul class="process-gets">
											<?php foreach ( $qd_gets as $qd_get ) : ?>
												<li><?php qd_the_icon( 'check', array( 'size' => 16 ) ); ?><?php echo esc_html( $qd_get ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
				<aside class="with-aside__aside prepare" aria-labelledby="prepare-title">
					<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'clipboard-list', array( 'size' => 20 ) ); ?></span>
					<h2 class="prepare__title" id="prepare-title">Chuẩn bị cho lần khám đầu</h2>
					<ul class="checklist">
						<?php foreach ( $qd_prepare as $qd_item ) : ?>
							<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</aside>
			</div>
		</div>
	</section>

	<section class="section section--mint" aria-labelledby="standards-title">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'standards-title',
					'title' => 'Tiêu chuẩn vệ sinh và an toàn',
					'desc'  => 'Những điều phòng khám luôn làm, dù bạn có nhìn thấy hay không.',
				)
			);
			?>
			<div class="grid grid--3 standards">
				<?php foreach ( $qd_standards as list( $qd_head, $qd_icon, $qd_items ) ) : ?>
					<section class="card standard">
						<span class="icon-tile icon-tile--sm"><?php qd_the_icon( $qd_icon, array( 'size' => 20 ) ); ?></span>
						<h3 class="card__title"><?php echo esc_html( $qd_head ); ?></h3>
						<ul class="checklist">
							<?php foreach ( $qd_items as $qd_item ) : ?>
								<li><?php qd_the_icon( 'check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endforeach; ?>
			</div>
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
