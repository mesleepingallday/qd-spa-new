<?php
/**
 * Concern directory: the eight things people come for, as labelled links. The one concentrated
 * multicolour moment on the page; every tile has a visible name and a glyph, never colour alone.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section section--tight concerns" id="concerns" aria-labelledby="home-concerns">
	<div class="container">
		<h2 class="section-title" id="home-concerns">Bạn cần hỗ trợ về vấn đề nào?</h2>
		<ul class="concern-grid">
			<?php foreach ( qd_concerns() as $qd_key => $qd_concern ) : ?>
				<li><?php get_template_part( 'template-parts/components/concern-tile', null, array( 'key' => $qd_key, 'concern' => $qd_concern ) ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p class="concerns__guide">Chưa chắc chắn? <a href="<?php echo esc_url( home_url( '/tim-lieu-trinh/' ) ); ?>">Trả lời vài câu hỏi ngắn</a> để xem các dịch vụ phù hợp.</p>
	</div>
</section>
