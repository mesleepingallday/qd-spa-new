<?php
/**
 * News list (/tin-tuc/). Shared with category.php and tag.php through template-parts/blog/archive.php.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/blog/archive' );
get_footer();
