<?php
/**
 * Training archive (/dao-tao/): hero, course cards, "why learn here", registration CTA.
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

get_header();
?>
<section class="page-hero training-hero">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<h1 class="page-title">Học nghề chăm sóc da cùng bác sĩ</h1>
		<p class="lead">Các khóa học ngắn, thực hành nhiều, dành cho người mới bắt đầu và kỹ thuật viên muốn nâng tay nghề.</p>
	</div>
</section>

<section class="section" aria-labelledby="course-list">
	<div class="container">
		<h2 class="sr-only" id="course-list">Danh sách khóa học</h2>
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
								<span class="card__arrow" aria-hidden="true"><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></span>
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

<section class="section section--mint" aria-labelledby="why-learn">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'    => 'why-learn',
				'title' => 'Vì sao học tại Quang Đăng',
			)
		);
		?>
		<ul class="grid grid--3 reasons">
			<?php foreach ( $qd_reasons as list( $qd_icon, $qd_title, $qd_text ) ) : ?>
				<li class="reason">
					<span class="icon-tile"><?php qd_the_icon( $qd_icon, array( 'size' => 24 ) ); ?></span>
					<h3 class="reason__title"><?php echo esc_html( $qd_title ); ?></h3>
					<p class="reason__text"><?php echo esc_html( $qd_text ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section" aria-labelledby="course-cta">
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
