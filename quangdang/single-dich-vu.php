<?php
/**
 * dich-vu single: a top-level post is a CATEGORY page, a child post is a SERVICE page.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/service/' . ( qd_is_service_category() ? 'category' : 'detail' ) );
endwhile;
get_footer();
