<?php
/**
 * About: Công nghệ & Sản phẩm — device cards (what each treats → services) and
 * "Sản phẩm chính hãng" commitments.
 * Device photos are real-photo slots (about/may-*): hidden in production while they are placeholders.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	// Device: [asset key, name, tagline, description, treats (service paths), tags].
	$qd_devices = apply_filters(
		'qd_about_devices',
		array(
			array(
				'key'     => 'about/may-picosure',
				'name'    => 'Máy Laser Picosure',
				'tagline' => 'Laser xung cực ngắn cho sắc tố và bề mặt da',
				'text'    => 'Dùng xung năng lượng rất ngắn để tác động vào hắc tố, ít sinh nhiệt lên mô xung quanh nên da nhẹ nhàng hơn. Bác sĩ chọn bước sóng và mức năng lượng theo từng loại da.',
				'treats'  => array( 'cham-soc-da/kiem-dau-tre-hoa-laser-picosure', 'dieu-tri-nam/dieu-tri-nam-chuyen-sau', 'xoa-xam/xoa-xam-tattoo', 'dieu-tri-seo/tri-seo-lom' ),
				'points'  => array( 'Kiềm dầu, trẻ hóa da', 'Nám, thâm sắc tố', 'Xóa xăm', 'Sẹo lõm sau mụn' ),
			),
			array(
				'key'     => 'about/may-double-cool',
				'name'    => 'Máy triệt lông Laser Double Cool',
				'tagline' => 'Triệt lông với công nghệ làm mát kép',
				'text'    => 'Đầu phát được làm mát liên tục trước, trong và sau khi bắn laser, giúp giảm cảm giác nóng rát và bảo vệ bề mặt da. Phù hợp cả vùng da nhạy cảm như nách, bikini.',
				'treats'  => array( 'cham-soc-da/triet-long' ),
				'points'  => array( 'Triệt lông nách, tay, chân', 'Triệt lông vùng nhạy cảm', 'Ít nóng rát' ),
			),
		)
	);

	$qd_pledges = apply_filters(
		'qd_about_pledges',
		array(
			'Thiết bị có nguồn gốc rõ ràng, giấy tờ nhập khẩu đầy đủ.',
			'Dược mỹ phẩm chính hãng, còn hạn sử dụng, bạn có thể kiểm tra nhãn trước khi dùng.',
			'Đầu tip, kim và vật tư dùng một lần được mở ngay trước mặt bạn.',
			'Máy được bảo trì, hiệu chuẩn định kỳ và vệ sinh sau mỗi khách.',
			'Bác sĩ chọn thông số và sản phẩm theo từng loại da, không dùng một công thức cho mọi người.',
			'Không dùng sản phẩm trôi nổi, không rõ nhãn mác.',
		)
	);

	get_template_part( 'template-parts/about/hero' );
	?>

	<section class="section" aria-labelledby="devices-title">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'devices-title',
					'title' => 'Thiết bị đang sử dụng',
					'desc'  => 'Mỗi máy được chọn cho một nhóm vấn đề cụ thể. Bác sĩ sẽ tư vấn máy nào phù hợp sau khi soi da.',
				)
			);
			?>
			<div class="device-list">
				<?php foreach ( $qd_devices as $qd_i => $qd_dev ) : ?>
					<?php
					$qd_photo = qd_about_photo( $qd_dev['key'], $qd_dev['name'] . ' tại Phòng khám Quang Đăng', '4-3' );
					$qd_links = array_values( array_filter( array_map( 'qd_about_service', $qd_dev['treats'] ) ) );
					?>
					<article class="device<?php echo $qd_photo ? '' : ' device--no-photo'; ?>">
						<?php if ( $qd_photo ) : ?>
							<div class="device__media"><?php echo $qd_photo; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<?php endif; ?>
						<div class="device__body">
							<p class="about-kicker"><?php echo esc_html( $qd_dev['tagline'] ); ?></p>
							<h3 class="device__name"><?php echo esc_html( $qd_dev['name'] ); ?></h3>
							<p class="device__text"><?php echo esc_html( $qd_dev['text'] ); ?></p>
							<p class="device__label">Hỗ trợ điều trị</p>
							<ul class="chips device__tags">
								<?php foreach ( $qd_dev['points'] as $qd_point ) : ?>
									<li class="badge"><?php echo esc_html( $qd_point ); ?></li>
								<?php endforeach; ?>
							</ul>
							<?php if ( $qd_links ) : ?>
								<ul class="device__links">
									<?php foreach ( $qd_links as list( $qd_title, $qd_url ) ) : ?>
										<li><a class="link-more" href="<?php echo esc_url( $qd_url ); ?>"><?php echo esc_html( $qd_title ); ?><?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section--mint" aria-labelledby="pledge-title">
		<div class="container about-split about-split--single">
			<div>
				<h2 class="section-title" id="pledge-title">Cam kết sản phẩm chính hãng</h2>
				<p class="section-lead">Da của bạn chỉ tiếp xúc với những gì phòng khám dám đưa ra trước mặt bạn.</p>
				<ul class="checklist about-split__list about-split__list--cols">
					<?php foreach ( $qd_pledges as $qd_pledge ) : ?>
						<li><?php qd_the_icon( 'shield-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_pledge ); ?></span></li>
					<?php endforeach; ?>
				</ul>
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
