<?php
/**
 * Lead doctor + the rest of the team as a compact row. Uses the "bac-si" posts.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_doctors = get_posts(
	array(
		'post_type'      => 'bac-si',
		'posts_per_page' => 4,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
if ( ! $qd_doctors ) {
	return;
}
$qd_lead      = array_shift( $qd_doctors );
$qd_title     = (string) get_post_meta( $qd_lead->ID, '_qd_doctor_title', true );
$qd_role      = (string) get_post_meta( $qd_lead->ID, '_qd_doctor_role', true );
$qd_years     = (int) get_post_meta( $qd_lead->ID, '_qd_doctor_years', true );
$qd_education = qd_lines( get_post_meta( $qd_lead->ID, '_qd_doctor_education', true ) );
?>
<section class="section section--mint" aria-labelledby="home-doctor">
	<div class="container doctor-feature">
		<div class="doctor-feature__photo">
			<?php echo qd_thumb( $qd_lead, 'qd-portrait', '4-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="doctor-feature__body">
			<p class="doctor-feature__role"><?php echo esc_html( $qd_role ? $qd_role : 'Bác sĩ phụ trách chuyên môn' ); ?></p>
			<h2 class="section-title" id="home-doctor"><?php echo esc_html( get_the_title( $qd_lead ) ); ?></h2>
			<p class="doctor-feature__title">
				<?php echo esc_html( $qd_title ); ?>
				<?php if ( $qd_years ) : ?>
					<span class="badge"><?php echo (int) $qd_years; ?>+ năm kinh nghiệm</span>
				<?php endif; ?>
			</p>
			<p class="lead"><?php echo esc_html( get_the_excerpt( $qd_lead ) ); ?></p>
			<?php if ( $qd_education ) : ?>
				<ul class="checklist">
					<?php foreach ( $qd_education as $qd_line ) : ?>
						<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="btn-row">
				<?php
				echo qd_button( 'Đặt lịch với bác sĩ', qd_booking_url(), array( 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo qd_button( 'Đội ngũ bác sĩ', home_url( '/gioi-thieu/doi-ngu-bac-si/' ), array( 'variant' => 'ghost', 'icon_end' => 'arrow-right' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</div>
			<?php if ( $qd_doctors ) : ?>
				<ul class="doctor-feature__team">
					<?php foreach ( $qd_doctors as $qd_doc ) : ?>
						<li>
							<span class="avatar"><?php echo get_the_post_thumbnail( $qd_doc, 'thumbnail', array( 'alt' => '' ) ); ?></span>
							<span><strong><?php echo esc_html( get_the_title( $qd_doc ) ); ?></strong><br><?php echo esc_html( (string) get_post_meta( $qd_doc->ID, '_qd_doctor_title', true ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
