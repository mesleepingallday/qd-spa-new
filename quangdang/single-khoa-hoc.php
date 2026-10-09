<?php
/**
 * Course detail: hero with facts, curriculum accordion, audience, outcomes, instructor,
 * and a registration form (sticky on desktop). Course structured data included.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	$qd_id       = get_the_ID();
	$qd_meta     = fn( $k ) => (string) get_post_meta( $qd_id, '_qd_course_' . $k, true );
	$qd_fee      = (int) $qd_meta( 'fee' );
	$qd_length   = $qd_meta( 'length' );
	$qd_format   = $qd_meta( 'format' );
	$qd_schedule = $qd_meta( 'schedule' );
	$qd_modules  = qd_pipe_rows( $qd_meta( 'modules' ), 2 );
	$qd_audience = qd_lines( $qd_meta( 'audience' ) );
	$qd_outcomes = qd_lines( $qd_meta( 'outcomes' ) );
	$qd_doctor   = qd_service_doctor();
	$qd_others   = get_posts(
		array(
			'post_type'      => 'khoa-hoc',
			'posts_per_page' => 2,
			'post__not_in'   => array( $qd_id ),
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);

	if ( ! $qd_audience ) {
		$qd_audience = array(
			'Người mới, chưa có kinh nghiệm muốn học nghề chăm sóc da.',
			'Kỹ thuật viên, chủ spa muốn có nền tảng y khoa vững hơn.',
			'Người muốn hiểu da mình để tự chăm sóc đúng cách.',
		);
	}
	if ( ! $qd_outcomes ) {
		$qd_outcomes = array(
			'Giấy chứng nhận hoàn thành khóa học của phòng khám.',
			'Tài liệu học tập và quy trình chuẩn để làm theo.',
			'Thực hành trực tiếp có giảng viên kèm.',
			'Được hỏi đáp với giảng viên sau khóa học qua Zalo.',
		);
	}

	// Structured data.
	$qd_node = array(
		'@type'       => 'Course',
		'name'        => get_the_title(),
		'description' => get_the_excerpt(),
		'url'         => get_permalink(),
		'provider'    => array( '@id' => home_url( '/#clinic' ) ),
	);
	if ( $qd_fee ) {
		$qd_node['offers'] = array(
			'@type'         => 'Offer',
			'price'         => $qd_fee,
			'priceCurrency' => 'VND',
			'category'      => 'Paid',
		);
	}
	if ( $qd_format || $qd_length ) {
		$qd_node['hasCourseInstance'] = array_filter(
			array(
				'@type'          => 'CourseInstance',
				'courseMode'     => $qd_format,
				'courseWorkload' => $qd_length,
			)
		);
	}
	qd_schema_add( $qd_node );

	$qd_facts = array_filter(
		array(
			array( 'banknote', 'Học phí', $qd_fee ? qd_price( $qd_fee ) : 'Liên hệ' ),
			$qd_length ? array( 'clock', 'Thời lượng', $qd_length ) : null,
			$qd_format ? array( 'map-pin', 'Hình thức', $qd_format ) : null,
			$qd_schedule ? array( 'calendar-days', 'Lịch học', $qd_schedule ) : null,
		)
	);
	?>

	<section class="course-hero">
		<div class="container">
			<?php qd_breadcrumbs(); ?>
			<div class="course-hero__grid">
				<div class="course-hero__text">
					<h1 class="page-title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<dl class="facts course-facts">
						<?php foreach ( $qd_facts as list( $qd_icon, $qd_label, $qd_value ) ) : ?>
							<div class="facts__item">
								<span class="icon-tile icon-tile--sm"><?php qd_the_icon( $qd_icon, array( 'size' => 20 ) ); ?></span>
								<div><dt><?php echo esc_html( $qd_label ); ?></dt><dd><?php echo esc_html( $qd_value ); ?></dd></div>
							</div>
						<?php endforeach; ?>
					</dl>
					<div class="btn-row course-hero__ctas">
						<?php echo qd_button( 'Đăng ký khóa học', '#dang-ky', array( 'size' => 'lg', 'icon' => 'graduation-cap' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
				<div class="course-hero__media">
					<?php echo qd_thumb( null, 'qd-card', '4-3', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</section>

	<div class="container course-body">
		<div class="with-aside">
			<div class="course-main">

				<?php if ( trim( get_the_content() ) ) : ?>
					<section class="course-section" aria-labelledby="course-overview">
						<h2 class="course-section__title" id="course-overview">Giới thiệu khóa học</h2>
						<div class="prose"><?php the_content(); ?></div>
					</section>
				<?php endif; ?>

				<?php if ( $qd_modules ) : ?>
					<section class="course-section" aria-labelledby="course-curriculum">
						<h2 class="course-section__title" id="course-curriculum">Chương trình học</h2>
						<div class="accordion">
							<?php foreach ( $qd_modules as $qd_i => list( $qd_name, $qd_text ) ) : ?>
								<details<?php echo 0 === $qd_i ? ' open' : ''; ?>>
									<summary><span class="module-num"><?php echo (int) $qd_i + 1; ?></span><span class="module-title"><?php echo esc_html( $qd_name ); ?></span></summary>
									<div class="accordion__body"><p><?php echo esc_html( $qd_text ); ?></p></div>
								</details>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

				<section class="course-section" aria-labelledby="course-audience">
					<h2 class="course-section__title" id="course-audience">Dành cho ai</h2>
					<ul class="checklist">
						<?php foreach ( $qd_audience as $qd_line ) : ?>
							<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</section>

				<section class="course-section" aria-labelledby="course-outcomes">
					<h2 class="course-section__title" id="course-outcomes">Sau khóa học bạn nhận được</h2>
					<ul class="outcomes">
						<?php foreach ( $qd_outcomes as $qd_line ) : ?>
							<li><span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'award', array( 'size' => 20 ) ); ?></span><span><?php echo esc_html( $qd_line ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</section>

				<?php if ( $qd_doctor ) : ?>
					<aside class="doctor-card" aria-label="Giảng viên">
						<span class="avatar avatar--lg"><?php echo get_the_post_thumbnail( $qd_doctor, 'thumbnail', array( 'alt' => '' ) ); ?></span>
						<div>
							<p class="doctor-card__label">Giảng viên</p>
							<p class="doctor-card__name"><?php echo esc_html( get_the_title( $qd_doctor ) ); ?></p>
							<p class="doctor-card__title"><?php echo esc_html( (string) get_post_meta( $qd_doctor->ID, '_qd_doctor_title', true ) ); ?></p>
						</div>
						<a class="link-more" href="<?php echo esc_url( get_permalink( $qd_doctor ) ); ?>">Xem hồ sơ<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
					</aside>
				<?php endif; ?>
			</div>

			<aside class="with-aside__aside register-card" id="dang-ky" aria-labelledby="register-title">
				<h2 class="register-card__title" id="register-title">Đăng ký khóa học</h2>
				<p class="register-card__fee">
					<small>Học phí</small>
					<strong><?php echo esc_html( $qd_fee ? qd_price( $qd_fee ) : 'Liên hệ' ); ?></strong>
				</p>
				<?php if ( $qd_schedule ) : ?>
					<p class="register-card__meta"><?php qd_the_icon( 'calendar-days', array( 'size' => 16 ) ); ?><?php echo esc_html( $qd_schedule ); ?></p>
				<?php endif; ?>
				<?php
				get_template_part(
					'template-parts/training/register-form',
					null,
					array( 'note' => 'Đăng ký khóa học: ' . get_the_title() )
				);
				?>
				<p class="register-card__alt">Hoặc gọi <a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a> · <a href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener">Chat Zalo</a></p>
			</aside>
		</div>
	</div>

	<?php if ( $qd_others ) : ?>
		<section class="section section--mint" aria-labelledby="other-courses">
			<div class="container">
				<?php
				qd_section_head(
					array(
						'id'         => 'other-courses',
						'title'      => 'Khóa học khác',
						'link_label' => 'Tất cả khóa học',
						'link_url'   => get_post_type_archive_link( 'khoa-hoc' ),
					)
				);
				?>
				<div class="grid grid--2 course-grid">
					<?php foreach ( $qd_others as $qd_other ) : ?>
						<article class="card course-card">
							<?php echo qd_thumb( $qd_other, 'qd-wide', '16-9' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<div class="card__body">
								<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $qd_other ) ); ?>"><?php echo esc_html( get_the_title( $qd_other ) ); ?></a></h3>
								<?php $qd_ofee = (int) get_post_meta( $qd_other->ID, '_qd_course_fee', true ); ?>
								<div class="card__foot">
									<p class="price"><small>Học phí</small> <?php echo esc_html( $qd_ofee ? qd_price( $qd_ofee ) : 'Liên hệ' ); ?></p>
									<span class="card__arrow" aria-hidden="true"><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></span>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;
get_footer();
