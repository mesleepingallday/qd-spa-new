<?php
/**
 * About hero: breadcrumb, H1, lead and (optionally) a photo, followed by the subpage tab bar.
 *
 * @package QuangDang
 *
 * @var array $args { lead?: string, photo?: string (asset key), photo_alt?: string, subnav?: bool, kicker?: string, title?: string (H1; defaults to the page title), stage?: bool (large rounded photo), after?: string (trusted HTML) }
 */

defined( 'ABSPATH' ) || exit;

$qd_lead   = $args['lead'] ?? ( has_excerpt() ? get_the_excerpt() : '' );
$qd_photo  = ! empty( $args['photo'] ) ? qd_about_photo( $args['photo'], $args['photo_alt'] ?? '', '4-3', array( 'loading' => 'eager', 'fetchpriority' => 'high' ), 'about-hero__photo' ) : '';
$qd_kicker = $args['kicker'] ?? '';
?>
<section class="page-hero about-hero<?php echo $qd_photo ? ' about-hero--photo' : ''; ?><?php echo ! empty( $args['stage'] ) ? ' about-hero--stage' : ''; ?>">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<div class="about-hero__grid">
			<div class="about-hero__text">
				<?php if ( $qd_kicker ) : ?>
					<p class="about-kicker"><?php echo esc_html( $qd_kicker ); ?></p>
				<?php endif; ?>
				<h1 class="page-title"><?php echo isset( $args['title'] ) ? esc_html( $args['title'] ) : get_the_title(); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
				<?php if ( $qd_lead ) : ?>
					<p class="lead"><?php echo esc_html( $qd_lead ); ?></p>
				<?php endif; ?>
				<?php echo $args['after'] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput -- trusted markup built by the calling template. ?>
			</div>
			<?php if ( $qd_photo ) : ?>
				<div class="about-hero__media"><?php echo $qd_photo; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
if ( ! isset( $args['subnav'] ) || $args['subnav'] ) {
	get_template_part( 'template-parts/about/subnav' );
}
