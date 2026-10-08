<?php
/**
 * Contact (/lien-he/): where the clinic is, how to get there, how to reach it.
 *
 * Address card + map (iframe when configured, otherwise a placeholder with an "Open Google Maps" button)
 * → getting-there tips → booking strip.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_embed = qd_clinic( 'map_embed' );
$qd_map   = qd_clinic( 'map_url' );
$qd_fb    = qd_clinic( 'facebook' );

// Sample copy: confirm with the clinic before launch.
$qd_tips = array(
	array( 'building-2', 'Đi vào TTTM Đức Tài – Tâm Đạt', 'Phòng khám nằm trong trung tâm thương mại, bạn không cần tìm biển hiệu ngoài đường.' ),
	array( 'layers', 'Lên Tầng 5 bằng thang máy', 'Từ sảnh, đi thang máy lên Tầng 5. Nếu cần hỗ trợ, bạn gọi hotline, nhân viên sẽ ra đón.' ),
	array( 'navigation', 'Gửi xe ngay tại trung tâm', 'Khu gửi xe của trung tâm thương mại dùng được cho cả xe máy và ô tô. Bạn hỏi nhân viên bảo vệ nếu chưa rõ lối vào.' ),
);

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<section class="page-hero">
		<div class="container">
			<?php qd_breadcrumbs(); ?>
			<h1 class="page-title">Liên hệ phòng khám</h1>
			<p class="lead">Phòng khám ở Tầng 5 TTTM Đức Tài – Tâm Đạt, Quỳnh Lưu. Bạn có thể gọi, nhắn Zalo hoặc đặt lịch trước để được ưu tiên.</p>
		</div>
	</section>

	<section class="section section--tight" aria-labelledby="contact-where">
		<div class="container">
			<div class="contact">
				<div class="contact__card card">
					<div class="card__body">
						<h2 class="section-title" id="contact-where">Địa chỉ &amp; giờ làm việc</h2>
						<ul class="contact__list">
							<li>
								<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'map-pin', array( 'size' => 20 ) ); ?></span>
								<div>
									<p class="contact__label">Địa chỉ</p>
									<p class="contact__value"><?php echo esc_html( qd_clinic( 'address' ) ); ?></p>
									<p class="contact__floor"><?php qd_the_icon( 'layers', array( 'size' => 16 ) ); ?>Phòng khám ở <strong>Tầng 5</strong></p>
								</div>
							</li>
							<li>
								<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'clock', array( 'size' => 20 ) ); ?></span>
								<div>
									<p class="contact__label">Giờ làm việc</p>
									<p class="contact__value"><?php echo esc_html( qd_clinic( 'hours' ) ); ?></p>
								</div>
							</li>
							<li>
								<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'phone', array( 'size' => 20 ) ); ?></span>
								<div>
									<p class="contact__label">Hotline</p>
									<p class="contact__value"><a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a></p>
								</div>
							</li>
							<li>
								<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'message-circle', array( 'size' => 20 ) ); ?></span>
								<div>
									<p class="contact__label">Zalo &amp; Facebook</p>
									<p class="contact__value">
										<a href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener">Nhắn Zalo</a>
										<?php if ( $qd_fb ) : ?>
											<span aria-hidden="true">·</span> <a href="<?php echo esc_url( $qd_fb ); ?>" target="_blank" rel="noopener">Fanpage Facebook</a>
										<?php endif; ?>
									</p>
								</div>
							</li>
						</ul>
						<div class="btn-row contact__actions">
							<?php
							echo qd_button( 'Chỉ đường', $qd_map, array( 'variant' => 'outline', 'icon' => 'navigation', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							echo qd_button( 'Gọi hotline', qd_tel_href(), array( 'variant' => 'ghost', 'icon' => 'phone' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							?>
						</div>
					</div>
				</div>

				<div class="contact__map">
					<?php if ( $qd_embed ) : ?>
						<iframe title="Bản đồ đến <?php echo esc_attr( qd_clinic( 'name' ) ); ?>" src="<?php echo esc_url( $qd_embed ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
					<?php else : ?>
						<div class="contact__map-empty">
							<span class="icon-tile"><?php qd_the_icon( 'map-pin', array( 'size' => 24 ) ); ?></span>
							<p class="contact__map-title">TTTM Đức Tài – Tâm Đạt</p>
							<p>Tầng 5, Quỳnh Lưu, Nghệ An</p>
							<?php echo qd_button( 'Mở Google Maps', $qd_map, array( 'variant' => 'outline', 'icon' => 'external-link', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--mint" aria-labelledby="contact-tips">
		<div class="container">
			<?php qd_section_head( array( 'id' => 'contact-tips', 'title' => 'Cách đến phòng khám', 'desc' => 'Ba bước đơn giản, dành cho bạn lần đầu ghé.' ) ); ?>
			<ol class="contact-tips">
				<?php foreach ( $qd_tips as $qd_n => list( $qd_icon, $qd_title, $qd_text ) ) : ?>
					<li class="contact-tip">
						<span class="contact-tip__num" aria-hidden="true"><?php echo (int) $qd_n + 1; ?></span>
						<div>
							<h3 class="contact-tip__title"><?php echo esc_html( $qd_title ); ?></h3>
							<p><?php echo esc_html( $qd_text ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="section" aria-label="Đặt lịch tư vấn">
		<div class="container">
			<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'lien-he' ) ); ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
