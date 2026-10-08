<?php
/**
 * Doctor card: 4:5 photo, name, title, role, years badge. Whole card links to the profile.
 *
 * @package QuangDang
 *
 * @var array $args { post: WP_Post, heading?: string }
 */

defined( 'ABSPATH' ) || exit;

$qd_doc     = get_post( $args['post'] ?? null );
$qd_heading = tag_escape( $args['heading'] ?? 'h3' );
$qd_title   = (string) get_post_meta( $qd_doc->ID, '_qd_doctor_title', true );
$qd_role    = (string) get_post_meta( $qd_doc->ID, '_qd_doctor_role', true );
$qd_years   = (int) get_post_meta( $qd_doc->ID, '_qd_doctor_years', true );
?>
<article class="card doctor-tile">
	<div class="doctor-tile__photo">
		<?php echo qd_thumb( $qd_doc, 'qd-portrait', '4-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<div class="card__body">
		<?php if ( $qd_role ) : ?>
			<p class="card__kicker"><?php echo esc_html( $qd_role ); ?></p>
		<?php endif; ?>
		<<?php echo $qd_heading; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="card__title"><a href="<?php echo esc_url( get_permalink( $qd_doc ) ); ?>"><?php echo esc_html( get_the_title( $qd_doc ) ); ?></a></<?php echo $qd_heading; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php if ( $qd_title ) : ?>
			<p class="card__text"><?php echo esc_html( $qd_title ); ?></p>
		<?php endif; ?>
		<?php if ( $qd_years ) : ?>
			<span class="badge doctor-tile__years"><?php echo (int) $qd_years; ?>+ năm kinh nghiệm</span>
		<?php endif; ?>
		<div class="card__foot">
			<span class="link-more">Xem hồ sơ<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></span>
		</div>
	</div>
</article>
