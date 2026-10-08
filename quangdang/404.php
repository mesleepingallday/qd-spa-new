<?php
/**
 * 404: friendly not-found with search, concern chips and the three main ways out.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="not-found" aria-labelledby="nf-title">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<div class="not-found__grid">
			<div class="not-found__media">
				<?php echo qd_asset_img( 'misc/not-found', '', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="not-found__body">
				<p class="about-kicker">Lỗi 404</p>
				<h1 class="page-title" id="nf-title">Rất tiếc, trang này không còn ở đây</h1>
				<p class="lead">Đường dẫn có thể đã thay đổi hoặc gõ chưa đúng. Bạn thử tìm lại bên dưới, hoặc chọn vấn đề da bạn đang quan tâm nhé.</p>

				<form class="not-found__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="sr-only" for="nf-search">Tìm kiếm</label>
					<input class="input" id="nf-search" type="search" name="s" placeholder="VD: trị mụn, nám, triệt lông…" autocomplete="off" required>
					<button class="btn btn--primary" type="submit"><?php qd_the_icon( 'search', array( 'size' => 18 ) ); ?><span>Tìm</span></button>
				</form>

				<p class="not-found__label" id="nf-concerns">Hoặc chọn vấn đề bạn quan tâm</p>
				<ul class="chips" aria-labelledby="nf-concerns">
					<?php foreach ( qd_concerns() as $qd_concern ) : ?>
						<li><a class="chip" href="<?php echo esc_url( $qd_concern['url'] ); ?>"><?php echo esc_html( $qd_concern['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>

				<div class="btn-row not-found__links">
					<?php
					echo qd_button( 'Về trang chủ', home_url( '/' ), array( 'variant' => 'outline', 'icon' => 'arrow-left' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo qd_button( 'Xem dịch vụ', get_post_type_archive_link( 'dich-vu' ), array( 'variant' => 'ghost' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo qd_button( 'Kiểm tra da 1 phút', home_url( '/tim-lieu-trinh/' ), array( 'variant' => 'ghost' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
