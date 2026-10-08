<?php
/**
 * Service CATEGORY page (Điều trị mụn…). STUB — the full design is built in phase 2
 * (see docs/DESIGN-PLAN.md §7 "Service category").
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="page-hero">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<h1 class="page-title"><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>
<section class="section section--tight">
	<div class="container grid grid--3">
		<?php foreach ( qd_service_children( get_the_ID() ) as $qd_service ) : ?>
			<?php get_template_part( 'template-parts/components/service-card', null, array( 'post' => $qd_service ) ); ?>
		<?php endforeach; ?>
	</div>
</section>
