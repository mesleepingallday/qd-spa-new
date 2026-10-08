<?php
/**
 * About: Đội ngũ bác sĩ — team photo (real-photo slot) + doctor cards from `bac-si` posts.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	$qd_doctors = qd_about_doctors();
	$qd_team    = qd_about_photo( 'about/doi-ngu', 'Đội ngũ bác sĩ và chuyên viên Phòng khám Quang Đăng', '16-9' );
	$qd_license = trim( qd_clinic( 'license' ) );

	get_template_part( 'template-parts/about/hero' );
	?>

	<section class="section" aria-labelledby="doctor-list">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'doctor-list',
					'title' => 'Bác sĩ trực tiếp thăm khám',
					'desc'  => 'Mỗi khách hàng được bác sĩ thăm khám, soi da và đưa ra phác đồ riêng trước khi bắt đầu điều trị.',
				)
			);
			?>
			<?php if ( $qd_doctors ) : ?>
				<div class="grid grid--3 doctor-grid">
					<?php foreach ( $qd_doctors as $qd_doc ) : ?>
						<?php get_template_part( 'template-parts/about/doctor-card', null, array( 'post' => $qd_doc ) ); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="notice"><?php qd_the_icon( 'info', array( 'size' => 18 ) ); ?><span>Thông tin đội ngũ bác sĩ đang được cập nhật. Bạn có thể gọi hotline <?php echo esc_html( qd_clinic( 'hotline' ) ); ?> để được tư vấn trực tiếp.</span></p>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $qd_team || $qd_license ) : ?>
		<section class="section section--mint" aria-labelledby="doctor-trust">
			<div class="container about-split<?php echo $qd_team ? '' : ' about-split--single'; ?>">
				<?php if ( $qd_team ) : ?>
					<figure class="about-split__media"><?php echo $qd_team; // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
				<?php endif; ?>
				<div>
					<h2 class="section-title" id="doctor-trust">Chuyên môn có thể kiểm chứng</h2>
					<ul class="checklist about-split__list">
						<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span>Bác sĩ có bằng cấp và chứng chỉ hành nghề, ghi rõ trong hồ sơ từng người.</span></li>
						<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span>Bác sĩ trực tiếp thăm khám và chỉ định liệu trình, không giao cho người chưa có chuyên môn.</span></li>
						<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span>Bạn được giải thích rõ phác đồ và chi phí trước khi đồng ý điều trị.</span></li>
					</ul>
					<?php if ( $qd_license ) : ?>
						<p class="about-license"><?php qd_the_icon( 'badge-check', array( 'size' => 20 ) ); ?><span>Giấy phép hoạt động số: <strong><?php echo esc_html( $qd_license ); ?></strong></span></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php $qd_content = qd_about_content(); ?>
	<?php if ( $qd_content ) : ?>
		<section class="section section--tight" aria-label="Giới thiệu thêm">
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
