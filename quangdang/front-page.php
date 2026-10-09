<?php
/**
 * Home. Each section is a template part so it can be reordered or removed independently.
 * Order: what you can get help with → what is offered → who → what happens → proof → learn → offers → book.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();

foreach ( array(
	'hero',      // H1, the next step, the photograph.
	'concerns',  // The 8 skin concerns as labelled links (also the way into the quiz).
	'feature',   // One service in depth: cover, introduction, first three services with price.
	'doctor',    // Lead doctor (real photo).
	'process',   // The first visit in 5 ordered steps.
	'results',   // Before/after pairs (hidden until real, consented photos exist).
	'articles',  // Latest beauty handbook posts: one feature + rows.
	'promos'     // Active promotions (hidden when none are running).
) as $qd_section ) {
	get_template_part( 'template-parts/home/' . $qd_section );
}
?>
<section class="section">
	<div class="container">
		<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'home' ) ); ?>
	</div>
</section>
<?php
get_footer();
