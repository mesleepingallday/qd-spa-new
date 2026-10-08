<?php
/**
 * Fallback template (lists posts). Specific templates override it.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="page-hero">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<h1 class="page-title"><?php echo esc_html( is_archive() ? wp_strip_all_tags( get_the_archive_title() ) : ( is_search() ? 'Kết quả tìm kiếm' : get_bloginfo( 'name' ) ) ); ?></h1>
	</div>
</section>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/components/post-card' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="lead">Chưa có nội dung.</p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
