<?php
/**
 * Logo: the bundled Qbe+ Quang Đăng lockup.
 *
 * Variants: 'light' (dark lettering, for white backgrounds; the default) and 'dark'
 * (white lettering, for the footer). Files live in assets/images/brand/.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_variant = ( isset( $args['variant'] ) && 'dark' === $args['variant'] ) ? 'dark' : 'light';
?>
<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( qd_clinic( 'name' ) ); ?> – Trang chủ">
	<img
		class="brand__img"
		src="<?php echo esc_url( QD_URI . '/assets/images/brand/logo-on-' . $qd_variant . '.webp' ); ?>"
		width="748"
		height="400"
		alt=""
		decoding="async"
		<?php echo 'dark' === $qd_variant ? 'loading="lazy"' : 'fetchpriority="high"'; ?>
	>
</a>
