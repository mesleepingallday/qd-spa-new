<?php
/**
 * Generic page: hero band + editor content.
 * Pages with a dedicated design use page-{slug}.php (WordPress picks it automatically).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
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
		<div class="container">
			<div class="prose"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;
get_footer();
