<?php
/**
 * Practitioner: one real portrait, name, role, a factual line and a link to the profile.
 * Content comes from the "bac-si" posts; nothing here is invented, and the section hides without a doctor.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_lead = get_posts(
	array(
		'post_type'      => 'bac-si',
		'posts_per_page' => 1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
if ( ! $qd_lead ) {
	return;
}
$qd_lead      = $qd_lead[0];
$qd_title     = (string) get_post_meta( $qd_lead->ID, '_qd_doctor_title', true );
$qd_role      = (string) get_post_meta( $qd_lead->ID, '_qd_doctor_role', true );
$qd_education = qd_lines( get_post_meta( $qd_lead->ID, '_qd_doctor_education', true ) );
?>
<section class="section" aria-labelledby="home-doctor">
	<div class="container doctor-feature">
		<div class="doctor-feature__photo">
			<?php echo qd_thumb( $qd_lead, 'qd-portrait', '4-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="doctor-feature__body">
			<h2 class="section-title" id="home-doctor">Bác sĩ phụ trách chuyên môn</h2>
			<p class="doctor-feature__name"><?php echo esc_html( get_the_title( $qd_lead ) ); ?></p>
			<?php if ( $qd_role || $qd_title ) : ?>
				<p class="doctor-feature__role"><?php echo esc_html( implode( ' · ', array_filter( array( $qd_role, $qd_title ) ) ) ); ?></p>
			<?php endif; ?>
			<?php if ( has_excerpt( $qd_lead ) ) : ?>
				<p class="lead"><?php echo esc_html( get_the_excerpt( $qd_lead ) ); ?></p>
			<?php endif; ?>
			<?php if ( $qd_education ) : ?>
				<ul class="checklist">
					<?php foreach ( $qd_education as $qd_line ) : ?>
						<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<a class="link-more doctor-feature__more" href="<?php echo esc_url( get_permalink( $qd_lead ) ); ?>">Xem hồ sơ bác sĩ<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
		</div>
	</div>
</section>
