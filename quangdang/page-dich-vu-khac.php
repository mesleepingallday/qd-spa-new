<?php
/**
 * Dịch vụ khác: one page for the menu groups without their own service page
 * (tiểu phẫu, peel, BAP, laser, cấy HA, PRP). Content comes from inc/data/price-menu.php,
 * so it updates on git pull; the editor content (if any) is shown under the price tables.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_other  = qd_price_menu()['other'];
$qd_groups = $qd_other['groups'];

$qd_all_faq = array();
foreach ( $qd_groups as $qd_group ) {
	$qd_all_faq = array_merge( $qd_all_faq, qd_pipe_rows( $qd_group['faq'] ?? '', 2 ) );
}
qd_schema_faq( $qd_all_faq );

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<section class="page-hero">
		<div class="container">
			<?php qd_breadcrumbs(); ?>
			<h1 class="page-title"><?php the_title(); ?></h1>
			<p class="lead"><?php echo esc_html( has_excerpt() ? get_the_excerpt() : $qd_other['intro'] ); ?></p>
		</div>
	</section>

	<nav class="section-tabs" aria-label="Nhóm dịch vụ" data-scrollspy>
		<div class="container">
			<ul>
				<?php foreach ( $qd_groups as $qd_anchor => $qd_group ) : ?>
					<li><a href="#<?php echo esc_attr( $qd_anchor ); ?>"><?php echo esc_html( $qd_group['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>

	<div class="container service-body">
		<div class="with-aside">
			<div class="service-main">
				<?php foreach ( $qd_groups as $qd_anchor => $qd_group ) : ?>
					<section id="<?php echo esc_attr( $qd_anchor ); ?>" class="service-section">
						<h2 class="service-section__title"><?php echo esc_html( $qd_group['title'] ); ?></h2>
						<p><?php echo esc_html( $qd_group['intro'] ); ?></p>

						<?php foreach ( $qd_group['tables'] as $qd_table_title => $qd_rows ) : ?>
							<?php if ( $qd_table_title ) : ?>
								<h3 class="other-services__table-title"><?php echo esc_html( $qd_table_title ); ?></h3>
							<?php endif; ?>
							<table class="price-table">
								<caption class="sr-only"><?php echo esc_html( 'Bảng giá ' . $qd_group['title'] . ( $qd_table_title ? ' – ' . $qd_table_title : '' ) ); ?></caption>
								<tbody>
									<?php foreach ( qd_pipe_rows( $qd_rows, 3 ) as list( $qd_name, $qd_amount, $qd_note ) ) : ?>
										<tr>
											<th scope="row">
												<span class="price-table__name"><?php echo esc_html( $qd_name ); ?></span>
												<?php if ( $qd_note ) : ?>
													<span class="price-note"><?php echo esc_html( $qd_note ); ?></span>
												<?php endif; ?>
											</th>
											<td class="price price-table__amount"><?php echo esc_html( is_numeric( $qd_amount ) ? qd_price( $qd_amount ) : $qd_amount ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endforeach; ?>

						<?php $qd_faq = qd_pipe_rows( $qd_group['faq'] ?? '', 2 ); ?>
						<?php if ( $qd_faq ) : ?>
							<div class="accordion other-services__faq">
								<?php foreach ( $qd_faq as list( $qd_q, $qd_a ) ) : ?>
									<details>
										<summary><?php echo esc_html( $qd_q ); ?></summary>
										<div class="accordion__body"><p><?php echo esc_html( $qd_a ); ?></p></div>
									</details>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>

				<p class="notice">
					<?php qd_the_icon( 'info', array( 'size' => 18 ) ); ?>
					<span>Giá tham khảo. Bác sĩ thăm khám và báo giá chính xác theo tình trạng thực tế – bạn chỉ thanh toán khi đồng ý phác đồ.</span>
				</p>

				<?php if ( get_the_content() ) : ?>
					<div class="prose other-services__content"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>

			<aside class="with-aside__aside booking-card" aria-label="Đặt lịch">
				<p class="booking-card__label">Chưa biết chọn dịch vụ nào?</p>
				<p class="booking-card__price">Tư vấn miễn phí</p>
				<?php echo qd_button( 'Đặt lịch khám', qd_booking_url(), array( 'class' => 'btn--block', 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="booking-card__alt">
					<a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php qd_the_icon( 'phone', array( 'size' => 18 ) ); ?><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a>
					<a href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 18 ) ); ?>Chat Zalo</a>
				</div>
				<p class="booking-card__note"><?php qd_the_icon( 'shield-check', array( 'size' => 16 ) ); ?>Bác sĩ trực tiếp thăm khám</p>
			</aside>
		</div>
	</div>

	<section class="section">
		<div class="container">
			<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'other-services' ) ); ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
