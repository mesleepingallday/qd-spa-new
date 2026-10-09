<?php
/**
 * Site footer, mobile action bar, floating Zalo button.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_service_links = array_map(
	fn( $c ) => array( $c->post_title, get_permalink( $c ) ),
	qd_service_categories()
);
if ( ! $qd_service_links ) {
	foreach ( qd_service_catalog() as $qd_slug => $qd_cat ) {
		$qd_service_links[] = array( $qd_cat[0], home_url( "/dich-vu/$qd_slug/" ) );
	}
}
?>
</main>

<footer class="site-footer">
	<div class="container site-footer__main" id="site-nav">
		<div>
			<?php get_template_part( 'template-parts/site/brand' ); ?>
			<p class="site-footer__about">Phòng khám chuyên khoa Da liễu – Thẩm mỹ. Bác sĩ trực tiếp thăm khám, phác đồ riêng cho từng làn da.</p>
			<div class="site-footer__social">
				<a href="<?php echo esc_url( qd_clinic( 'facebook' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'facebook', array( 'size' => 18, 'label' => 'Facebook' ) ); ?></a>
				<a href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 18, 'label' => 'Zalo' ) ); ?></a>
				<a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php qd_the_icon( 'phone', array( 'size' => 18, 'label' => 'Gọi điện' ) ); ?></a>
			</div>
		</div>

		<div>
			<p class="site-footer__title">Dịch vụ</p>
			<ul class="site-footer__links">
				<?php foreach ( $qd_service_links as list( $qd_label, $qd_url ) ) : ?>
					<li><a href="<?php echo esc_url( $qd_url ); ?>"><?php echo esc_html( $qd_label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div>
			<p class="site-footer__title">Về Quang Đăng</p>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'site-footer__links',
						'depth'          => 1,
					)
				);
			} else {
				echo '<ul class="site-footer__links">';
				foreach ( array(
					'Giới thiệu'           => '/gioi-thieu/',
					'Đội ngũ bác sĩ'       => '/gioi-thieu/doi-ngu-bac-si/',
					'Quy trình chuẩn y khoa' => '/gioi-thieu/quy-trinh-chuan-y-khoa/',
					'Cẩm nang làm đẹp'     => '/tin-tuc/cam-nang-lam-dep/',
					'Sự kiện – Ưu đãi'     => '/tin-tuc/su-kien-uu-dai/',
					'Đào tạo'              => '/dao-tao/',
					'Tìm dịch vụ phù hợp'   => '/tim-lieu-trinh/',
				) as $qd_label => $qd_path ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( $qd_path ) ), esc_html( $qd_label ) );
				}
				echo '</ul>';
			}
			?>
		</div>

		<div>
			<p class="site-footer__title">Liên hệ</p>
			<ul class="site-footer__contact">
				<li><?php qd_the_icon( 'map-pin', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'address' ) ); ?><br><a href="<?php echo esc_url( qd_clinic( 'map_url' ) ); ?>" target="_blank" rel="noopener">Chỉ đường trên Google Maps</a></span></li>
				<li><?php qd_the_icon( 'clock', array( 'size' => 18 ) ); ?><span><?php echo esc_html( qd_clinic( 'hours' ) ); ?></span></li>
				<li><?php qd_the_icon( 'phone', array( 'size' => 18 ) ); ?><a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a></li>
				<?php if ( qd_clinic( 'email' ) ) : ?>
					<li><?php qd_the_icon( 'mail', array( 'size' => 18 ) ); ?><a href="mailto:<?php echo esc_attr( qd_clinic( 'email' ) ); ?>"><?php echo esc_html( qd_clinic( 'email' ) ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<div class="container">
		<div class="site-footer__bottom">
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( qd_clinic( 'name' ) ); ?>. Giấy phép hoạt động: <?php echo esc_html( qd_clinic( 'license' ) ); ?></p>
		<p>Thông tin trên website chỉ mang tính tham khảo, không thay thế chẩn đoán của bác sĩ.</p>
		</div>
	</div>
</footer>

<?php $qd_on_booking = is_page( 'dat-lich' ); // The booking page has its own submit button. ?>
<nav class="action-bar<?php echo $qd_on_booking ? ' action-bar--contact' : ''; ?>" aria-label="Liên hệ nhanh">
	<a class="action-bar__btn" href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php qd_the_icon( 'phone', array( 'size' => 22 ) ); ?>Gọi</a>
	<a class="action-bar__btn" href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 22 ) ); ?>Zalo</a>
	<?php if ( ! $qd_on_booking ) : ?>
		<?php echo qd_button( 'Đặt lịch khám', qd_booking_url( is_singular( 'dich-vu' ) && ! qd_is_service_category() ? get_the_ID() : null ), array( 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>
</nav>

<a class="float-chat" href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 20 ) ); ?>Chat Zalo</a>

<?php wp_footer(); ?>
</body>
</html>
