<?php
/**
 * Training archive (/dao-tao/): hero with the reasons to learn here, course cards, how a course runs
 * (ruler on the one dark band), FAQ, registration CTA.
 *
 * Job: "is this course real, what will I do in it, when does it start?"
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_reasons = apply_filters(
	'qd_training_reasons',
	array(
		array( 'stethoscope', 'Do bác sĩ da liễu giảng dạy', 'Kiến thức dựa trên y khoa, biết khi nào là việc của bác sĩ và khi nào kỹ thuật viên có thể làm.' ),
		array( 'layers', 'Thực hành trên thiết bị thật', 'Học và tập ngay tại phòng khám, cạnh những thiết bị đang dùng hằng ngày, có người kèm.' ),
		array( 'users', 'Lớp nhỏ, được cầm tay chỉ việc', 'Số học viên mỗi lớp vừa phải để giảng viên theo sát từng người.' ),
	)
);

// How a course runs, in order. Owner to confirm these four steps match the real course format.
$qd_path = apply_filters(
	'qd_training_path',
	array(
		array( 'Kiến thức nền tảng', 'Bác sĩ da liễu giảng về cấu tạo da và các vấn đề thường gặp, kể cả khi nào cần chuyển cho bác sĩ.' ),
		array( 'Thực hành trên thiết bị', 'Tập ngay tại phòng khám, cạnh những thiết bị đang dùng hằng ngày.' ),
		array( 'Kèm từng người', 'Lớp nhỏ để giảng viên theo sát và chỉnh từng thao tác của bạn.' ),
		array( 'Đánh giá cuối khóa', 'Giảng viên nhận xét điểm mạnh và phần cần luyện thêm trước khi bạn vào nghề.' ),
	)
);

$qd_faq = apply_filters(
	'qd_training_faq',
	array(
		array( 'Tôi chưa biết gì về chăm sóc da, có học được không?', 'Được. Khóa cơ bản dành cho người mới bắt đầu, đi từ cấu tạo da đến quy trình chăm sóc chuẩn.' ),
		array( 'Tôi sẽ thực hành ở đâu?', 'Ngay tại phòng khám, cạnh những thiết bị đang dùng hằng ngày, có giảng viên kèm.' ),
		array( 'Mỗi lớp có bao nhiêu học viên?', 'Lớp nhỏ để giảng viên theo sát từng người. Số học viên cụ thể được báo khi bạn đăng ký.' ),
		array( 'Xem học phí và lịch khai giảng ở đâu?', 'Ở từng khóa học phía trên. Nếu cần hỏi thêm, để lại số điện thoại và bên đào tạo sẽ gọi lại.' ),
	)
);
qd_schema_faq( $qd_faq );

get_header();
?>
<section class="page-hero training-hero">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<div class="training-hero__grid">
			<div class="training-hero__text">
				<h1 class="page-title">Học nghề chăm sóc da cùng bác sĩ</h1>
				<p class="lead">Các khóa học ngắn, thực hành nhiều, dành cho người mới bắt đầu và kỹ thuật viên muốn nâng tay nghề.</p>
				<ul class="checklist training-hero__reasons">
					<?php foreach ( $qd_reasons as list( $qd_icon, $qd_title ) ) : ?>
						<li><?php qd_the_icon( 'check-circle', array( 'size' => 20 ) ); ?><span><?php echo esc_html( $qd_title ); ?></span></li>
					<?php endforeach; ?>
				</ul>
				<div class="btn-row training-hero__cta">
					<?php echo qd_button( 'Xem các khóa học', '#course-list', array( 'variant' => 'outline', 'icon_end' => 'chevron-down' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
			<?php
			$qd_photo = qd_about_photo( 'about/phong-dieu-tri', 'Phòng điều trị tại Quang Đăng, nơi học viên thực hành', '4-3', array( 'loading' => 'eager', 'fetchpriority' => 'high' ), 'training-hero__photo' );
			if ( $qd_photo ) :
				?>
				<div class="training-hero__media"><?php echo $qd_photo; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section" aria-labelledby="course-list">
	<div class="container">
		<h2 class="section-title course-list__title" id="course-list">Các khóa học</h2>
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--2 course-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$qd_fee      = (int) get_post_meta( get_the_ID(), '_qd_course_fee', true );
					$qd_length   = (string) get_post_meta( get_the_ID(), '_qd_course_length', true );
					$qd_format   = (string) get_post_meta( get_the_ID(), '_qd_course_format', true );
					$qd_schedule = (string) get_post_meta( get_the_ID(), '_qd_course_schedule', true );
					?>
					<article class="card course-card">
						<?php echo qd_thumb( null, 'qd-wide', '16-9' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<div class="card__body">
							<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php if ( has_excerpt() ) : ?>
								<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>
							<ul class="course-card__facts">
								<?php if ( $qd_length ) : ?>
									<li><?php qd_the_icon( 'clock', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_length ); ?></span></li>
								<?php endif; ?>
								<?php if ( $qd_format ) : ?>
									<li><?php qd_the_icon( 'map-pin', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_format ); ?></span></li>
								<?php endif; ?>
								<?php if ( $qd_schedule ) : ?>
									<li><?php qd_the_icon( 'calendar-days', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_schedule ); ?></span></li>
								<?php endif; ?>
							</ul>
							<div class="card__foot">
								<p class="price"><small>Học phí</small> <?php echo esc_html( $qd_fee ? qd_price( $qd_fee ) : 'Liên hệ' ); ?></p>
								<span class="link-more" aria-hidden="true">Xem khóa học<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></span>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p class="notice"><?php qd_the_icon( 'info', array( 'size' => 18 ) ); ?><span>Các khóa học đang được cập nhật. Gọi hotline <?php echo esc_html( qd_clinic( 'hotline' ) ); ?> để biết lịch khai giảng gần nhất.</span></p>
		<?php endif; ?>
	</div>
</section>

<section class="section section--dark training-path" aria-labelledby="course-path">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'    => 'course-path',
				'title' => 'Một khóa học diễn ra thế nào',
				'desc'  => 'Từ kiến thức nền đến thao tác thật, mỗi bước đều có giảng viên bên cạnh.',
			)
		);
		get_template_part( 'template-parts/about/ruler', null, array( 'steps' => $qd_path, 'class' => 'ruler--dark' ) );
		?>
	</div>
</section>

<section class="section" aria-labelledby="course-faq">
	<div class="container training-faq">
		<?php
		qd_section_head(
			array(
				'id'    => 'course-faq',
				'title' => 'Câu hỏi thường gặp',
			)
		);
		?>
		<div class="accordion">
			<?php foreach ( $qd_faq as $qd_i => list( $qd_q, $qd_a ) ) : ?>
				<details<?php echo 0 === $qd_i ? ' open' : ''; ?>>
					<summary><?php echo esc_html( $qd_q ); ?></summary>
					<div class="accordion__body"><p><?php echo esc_html( $qd_a ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--flush-top" aria-labelledby="course-cta">
	<div class="container">
		<div class="booking-strip">
			<div>
				<h2 class="section-title" id="course-cta">Chưa biết nên học khóa nào?</h2>
				<p class="section-lead">Để lại số điện thoại, bên đào tạo sẽ gọi tư vấn khóa phù hợp với mục tiêu của bạn và báo lịch khai giảng gần nhất.</p>
				<ul class="booking-strip__contacts">
					<li><?php qd_the_icon( 'map-pin', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'address' ) ); ?></span></li>
					<li><?php qd_the_icon( 'phone', array( 'size' => 18 ) ); ?><span>Hotline <a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a> · <a href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener">Chat Zalo</a></span></li>
				</ul>
			</div>
			<?php get_template_part( 'template-parts/training/register-form', null, array( 'note' => 'Tư vấn khóa học đào tạo', 'button' => 'Nhận tư vấn khóa học' ) ); ?>
		</div>
	</div>
</section>
<?php
get_footer();
