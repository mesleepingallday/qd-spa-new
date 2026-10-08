<?php
/**
 * Home. Each section is a template part so it can be reordered or removed independently.
 * Order follows docs/DESIGN-PLAN.md §7: problem → trust → how → who → proof → offers → learn → book.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();

foreach ( array(
	'hero',        // H1, CTAs, concern shortcuts (onboarding entry), trust facts.
	'categories',  // Services by skin problem + quiz tile.
	'why',         // 4 pillars → About subpages.
	'process',     // 5-step first visit.
	'doctor',      // Lead doctor (real photo).
	'results',     // Before/after (real photos, consented).
	'promos',      // Active / upcoming promotions.
	'articles',    // Latest beauty handbook posts.
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
