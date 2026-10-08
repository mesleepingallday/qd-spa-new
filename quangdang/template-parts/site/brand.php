<?php
/**
 * Logo: the uploaded custom logo, or a text logo until the official file is uploaded
 * (Appearance → Customize → Site Identity → Logo).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_logo_id = get_theme_mod( 'custom_logo' );
?>
<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( qd_clinic( 'name' ) ); ?> – Trang chủ">
	<?php if ( $qd_logo_id ) : ?>
		<?php echo wp_get_attachment_image( $qd_logo_id, 'full', false, array( 'alt' => '', 'loading' => 'eager' ) ); ?>
	<?php else : ?>
		<span class="brand__mark" aria-hidden="true">Q</span>
		<span class="brand__text" aria-hidden="true">
			<span class="brand__name"><?php echo esc_html( qd_clinic( 'brand' ) ); ?></span>
			<span class="brand__tag"><?php echo esc_html( qd_clinic( 'tagline' ) ); ?></span>
		</span>
	<?php endif; ?>
</a>
