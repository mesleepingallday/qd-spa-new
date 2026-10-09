<?php
/**
 * Doctor profile: photo, name, title, role, years, education, bio,
 * the services this doctor is in charge of, articles they reviewed, and a booking CTA.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	$qd_id        = get_the_ID();
	$qd_title     = (string) get_post_meta( $qd_id, '_qd_doctor_title', true );
	$qd_role      = (string) get_post_meta( $qd_id, '_qd_doctor_role', true );
	$qd_years     = (int) get_post_meta( $qd_id, '_qd_doctor_years', true );
	$qd_education = qd_lines( get_post_meta( $qd_id, '_qd_doctor_education', true ) );
	$qd_name      = get_the_title();

	// Services this doctor is explicitly in charge of (leaf services, not categories).
	$qd_services = array_values(
		array_filter(
			get_posts(
				array(
					'post_type'      => 'dich-vu',
					'posts_per_page' => 40,
					'orderby'        => 'menu_order title',
					'order'          => 'ASC',
					'meta_key'       => '_qd_doctor', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'     => $qd_id, // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			),
			fn( $s ) => $s->post_parent > 0
		)
	);

	$qd_service_total = count( $qd_services );
	$qd_services      = array_slice( $qd_services, 0, 12 );

	$qd_articles = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 3,
			'meta_key'       => '_qd_reviewer', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $qd_id, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);

	$qd_node = array(
		'@type'            => 'Physician',
		'@id'              => get_permalink() . '#physician',
		'name'             => $qd_name,
		'url'              => get_permalink(),
		'jobTitle'         => $qd_role ? $qd_role : $qd_title,
		'description'      => get_the_excerpt(),
		'medicalSpecialty' => 'Dermatology',
		'worksFor'         => array( '@id' => home_url( '/#clinic' ) ),
	);
	if ( has_post_thumbnail() ) {
		$qd_node['image'] = get_the_post_thumbnail_url( $qd_id, 'qd-portrait' );
	}
	if ( $qd_education ) {
		$qd_node['knowsAbout'] = $qd_education;
	}
	qd_schema_add( array_filter( $qd_node ) );
	?>

	<section class="page-hero doctor-hero">
		<div class="container">
			<?php qd_breadcrumbs(); ?>
			<div class="doctor-hero__grid">
				<div class="doctor-hero__photo">
					<?php echo qd_thumb( null, 'qd-portrait', '4-5', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="doctor-hero__text">
					<?php if ( $qd_role ) : ?>
						<p class="about-kicker"><?php echo esc_html( $qd_role ); ?></p>
					<?php endif; ?>
					<h1 class="page-title"><?php the_title(); ?></h1>
					<?php if ( $qd_title ) : ?>
						<p class="doctor-hero__title"><?php echo esc_html( $qd_title ); ?></p>
					<?php endif; ?>
					<?php if ( $qd_years ) : ?>
						<p class="doctor-hero__years"><span class="badge"><?php qd_the_icon( 'award', array( 'size' => 16 ) ); ?><?php echo (int) $qd_years; ?>+ năm kinh nghiệm</span></p>
					<?php endif; ?>
					<?php if ( has_excerpt() ) : ?>
						<p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<div class="btn-row doctor-hero__ctas">
						<?php
						echo qd_button( 'Đặt lịch với bác sĩ', qd_booking_url(), array( 'size' => 'lg', 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						echo qd_button( 'Nhắn Zalo cho phòng khám', qd_clinic( 'zalo' ), array( 'size' => 'lg', 'variant' => 'outline', 'icon' => 'message-circle', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php if ( $qd_education || trim( get_the_content() ) ) : ?>
		<section class="section" aria-label="Học vấn và giới thiệu">
			<div class="container">
				<div class="doctor-body">
					<?php if ( $qd_education ) : ?>
						<aside class="doctor-edu" aria-labelledby="doctor-edu-title">
							<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'graduation-cap', array( 'size' => 20 ) ); ?></span>
							<h2 class="doctor-edu__title" id="doctor-edu-title">Học vấn và chứng chỉ</h2>
							<ul class="checklist">
								<?php foreach ( $qd_education as $qd_line ) : ?>
									<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
								<?php endforeach; ?>
							</ul>
						</aside>
					<?php endif; ?>
					<?php if ( trim( get_the_content() ) ) : ?>
						<div class="doctor-bio">
							<h2 class="section-title" id="doctor-about">Giới thiệu</h2>
							<div class="prose"><?php the_content(); ?></div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $qd_services ) : ?>
		<section class="section section--mint" aria-labelledby="doctor-services">
			<div class="container">
				<?php
				qd_section_head(
					array(
						'id'    => 'doctor-services',
						'title' => 'Dịch vụ phụ trách',
						'desc'       => 'Bác sĩ trực tiếp thăm khám và theo dõi các liệu trình sau.',
						'link_label' => $qd_service_total > 12 ? 'Xem tất cả dịch vụ' : '',
						'link_url'   => $qd_service_total > 12 ? get_post_type_archive_link( 'dich-vu' ) : '',
					)
				);
				?>
				<ul class="doctor-services">
					<?php foreach ( $qd_services as $qd_service ) : ?>
						<?php $qd_parent = $qd_service->post_parent ? get_the_title( $qd_service->post_parent ) : ''; ?>
						<li>
							<a class="doctor-service" href="<?php echo esc_url( get_permalink( $qd_service ) ); ?>">
								<span class="doctor-service__text">
									<?php if ( $qd_parent ) : ?><small><?php echo esc_html( $qd_parent ); ?></small><?php endif; ?>
									<strong><?php echo esc_html( get_the_title( $qd_service ) ); ?></strong>
								</span>
								<?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $qd_articles ) : ?>
		<section class="section" aria-labelledby="doctor-articles">
			<div class="container">
				<?php
				qd_section_head(
					array(
						'id'    => 'doctor-articles',
						'title' => 'Bài viết do ' . $qd_name . ' tham vấn',
						'desc'  => 'Nội dung được bác sĩ xem lại về mặt chuyên môn.',
					)
				);
				?>
				<div class="grid grid--3">
					<?php foreach ( $qd_articles as $qd_article ) : ?>
						<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_article ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section<?php echo $qd_articles ? ' section--flush-top' : ''; ?>" aria-label="Đặt lịch">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/components/booking-strip',
				null,
				array(
					'title'  => 'Đặt lịch khám với ' . $qd_name,
					'source' => 'doctor',
				)
			);
			?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
