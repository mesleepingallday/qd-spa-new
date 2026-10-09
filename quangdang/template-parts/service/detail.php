<?php
/**
 * Service detail — the reference pattern for content pages.
 *
 * Job: "Is this for me, how does it work, how much?" → book.
 * Hero (facts row) → sticky section tabs → main column + sticky booking card (desktop)
 * → related services → related articles → booking strip.
 * Every section renders only when its data exists, so thin services still look finished.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_id       = get_the_ID();
$qd_facts    = qd_service_facts();
$qd_category = qd_service_category();
$qd_concern  = qd_concern_for_post( $qd_id );
$qd_meta     = fn( $k ) => (string) get_post_meta( $qd_id, '_qd_' . $k, true );
$qd_suits    = qd_lines( $qd_meta( 'suits' ) );
$qd_not      = qd_lines( $qd_meta( 'not_suits' ) );
$qd_steps    = qd_pipe_rows( $qd_meta( 'steps' ), 2 );
$qd_prices   = qd_pipe_rows( $qd_meta( 'prices' ), 3 );
$qd_after    = qd_lines( $qd_meta( 'aftercare' ) );
$qd_faq      = qd_pipe_rows( $qd_meta( 'faq' ), 2 );
$qd_tech     = $qd_meta( 'technology' );
$qd_doctor   = qd_service_doctor();
$qd_siblings = array_values( array_filter( qd_service_children( wp_get_post_parent_id( $qd_id ) ), fn( $p ) => $p->ID !== $qd_id ) );
$qd_articles = qd_service_related_posts( $qd_id, 3 );

$qd_tabs = array_filter(
	array(
		'tong-quan' => 'Tổng quan',
		'phu-hop'   => ( $qd_suits || $qd_not ) ? 'Phù hợp với ai' : '',
		'quy-trinh' => $qd_steps ? 'Quy trình' : '',
		'bang-gia'  => 'Bảng giá',
		'luu-y'     => $qd_after ? 'Lưu ý' : '',
		'hoi-dap'   => $qd_faq ? 'Hỏi đáp' : '',
	)
);

// Structured data: the service with its starting price, plus FAQ.
$qd_node = array(
	'@type'       => 'Service',
	'name'        => get_the_title(),
	'description' => get_the_excerpt(),
	'url'         => get_permalink(),
	'serviceType' => $qd_category ? $qd_category->post_title : '',
	'provider'    => array( '@id' => home_url( '/#clinic' ) ),
	'areaServed'  => 'Nghệ An',
);
if ( $qd_facts['price_from'] ) {
	$qd_node['offers'] = array(
		'@type'         => 'Offer',
		'price'         => $qd_facts['price_from'],
		'priceCurrency' => 'VND',
	);
}
qd_schema_add( $qd_node );
qd_schema_faq( $qd_faq );

$qd_fact_items = array_filter(
	array(
		array( 'banknote', 'Giá từ', $qd_facts['price_from'] ? array( qd_price( $qd_facts['price_from'] ), $qd_facts['price_unit'] ) : 'Báo giá khi khám' ),
		$qd_facts['sessions'] ? array( 'repeat', 'Số buổi', $qd_facts['sessions'] ) : null,
		$qd_facts['duration'] ? array( 'clock', 'Mỗi buổi', $qd_facts['duration'] ) : null,
		$qd_facts['downtime'] ? array( 'sun', 'Nghỉ dưỡng', $qd_facts['downtime'] ) : null,
	)
);
?>
<section class="service-hero"<?php echo $qd_concern ? ' data-concern="' . esc_attr( $qd_concern ) . '"' : ''; ?>>
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<div class="service-hero__grid">
			<div class="service-hero__text">
				<?php if ( $qd_category ) : ?>
					<a class="service-hero__cat" href="<?php echo esc_url( get_permalink( $qd_category ) ); ?>"><?php echo qd_concern_mark( $qd_concern, array( 'size' => 'sm' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $qd_category->post_title ); ?></a>
				<?php endif; ?>
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<dl class="facts">
					<?php foreach ( $qd_fact_items as list( $qd_icon, $qd_label, $qd_value ) ) : ?>
						<div class="facts__item">
							<span class="icon-tile icon-tile--sm"><?php qd_the_icon( $qd_icon, array( 'size' => 22, 'weight' => 'duotone' ) ); ?></span>
							<div><dt><?php echo esc_html( $qd_label ); ?></dt><dd><?php echo is_array( $qd_value ) ? esc_html( $qd_value[0] ) . '<small>' . esc_html( $qd_value[1] ) . '</small>' : esc_html( $qd_value ); ?></dd></div>
						</div>
					<?php endforeach; ?>
				</dl>
				<div class="btn-row service-hero__ctas">
					<?php
					echo qd_button( 'Đặt lịch khám', qd_booking_url( $qd_id ), array( 'size' => 'lg', 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo qd_button( 'Hỏi bác sĩ qua Zalo', qd_clinic( 'zalo' ), array( 'size' => 'lg', 'variant' => 'outline', 'icon' => 'message-circle', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
			</div>
			<div class="service-hero__media">
				<?php echo qd_thumb( null, 'qd-card', '4-3', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
</section>

<nav class="section-tabs" aria-label="Nội dung trang" data-scrollspy>
	<div class="container">
		<ul>
			<?php foreach ( $qd_tabs as $qd_anchor => $qd_label ) : ?>
				<li><a href="#<?php echo esc_attr( $qd_anchor ); ?>"><?php echo esc_html( $qd_label ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>

<div class="container service-body">
	<div class="with-aside">
		<div class="service-main">

			<section id="tong-quan" class="service-section">
				<h2 class="service-section__title">Tổng quan</h2>
				<div class="prose"><?php the_content(); ?></div>
				<?php if ( $qd_tech ) : ?>
					<div class="tech-box">
						<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'zap', array( 'size' => 20 ) ); ?></span>
						<div><p class="tech-box__title">Công nghệ &amp; sản phẩm sử dụng</p><p><?php echo esc_html( $qd_tech ); ?></p></div>
					</div>
				<?php endif; ?>
			</section>

			<?php if ( $qd_suits || $qd_not ) : ?>
				<section id="phu-hop" class="service-section">
					<h2 class="service-section__title">Liệu trình này có phù hợp với bạn?</h2>
					<div class="fit">
						<?php if ( $qd_suits ) : ?>
							<div class="fit__col fit__col--yes">
								<p class="fit__title"><?php qd_the_icon( 'circle-check', array( 'size' => 20 ) ); ?>Phù hợp với</p>
								<ul class="checklist">
									<?php foreach ( $qd_suits as $qd_line ) : ?>
										<li><?php qd_the_icon( 'check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php if ( $qd_not ) : ?>
							<div class="fit__col fit__col--no">
								<p class="fit__title"><?php qd_the_icon( 'triangle-alert', array( 'size' => 20 ) ); ?>Cần bác sĩ tư vấn trước nếu bạn</p>
								<ul class="checklist checklist--muted">
									<?php foreach ( $qd_not as $qd_line ) : ?>
										<li><?php qd_the_icon( 'info', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $qd_steps ) : ?>
				<section id="quy-trinh" class="service-section">
					<h2 class="service-section__title">Quy trình thực hiện</h2>
					<ol class="steps steps--vertical">
						<?php foreach ( $qd_steps as $qd_i => list( $qd_title, $qd_text ) ) : ?>
							<li class="steps__item">
								<span class="steps__num" aria-hidden="true"><?php echo (int) $qd_i + 1; ?></span>
								<h3 class="steps__title"><?php echo esc_html( $qd_title ); ?></h3>
								<?php if ( $qd_text ) : ?>
									<p class="steps__text"><?php echo esc_html( $qd_text ); ?></p>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				</section>
			<?php endif; ?>

			<section id="bang-gia" class="service-section">
				<h2 class="service-section__title">Bảng giá tham khảo</h2>
				<?php if ( $qd_prices ) : ?>
					<table class="price-table">
						<caption class="sr-only">Bảng giá <?php the_title_attribute(); ?></caption>
						<thead>
							<tr><th scope="col">Gói</th><th scope="col" class="price-table__amount">Giá tham khảo</th></tr>
						</thead>
						<tbody>
							<?php foreach ( $qd_prices as list( $qd_name, $qd_amount, $qd_note ) ) : ?>
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
				<?php elseif ( $qd_facts['price_from'] ) : ?>
					<table class="price-table">
						<caption class="sr-only">Giá <?php the_title_attribute(); ?></caption>
						<tbody>
							<tr>
								<th scope="row"><span class="price-table__name"><?php the_title(); ?></span></th>
								<td class="price price-table__amount"><small>Từ</small> <?php echo esc_html( qd_price( $qd_facts['price_from'] ) ); ?><small><?php echo esc_html( $qd_facts['price_unit'] ); ?></small></td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>
				<p class="notice">
					<?php qd_the_icon( 'info', array( 'size' => 18 ) ); ?>
					<span>
						<?php echo $qd_facts['price_from'] || $qd_prices ? 'Giá tham khảo. Bác sĩ báo giá chính xác theo tình trạng da sau khi thăm khám – bạn chỉ thanh toán khi đồng ý phác đồ.' : 'Chi phí phụ thuộc diện tích và tình trạng thực tế. Bác sĩ báo giá chính xác sau khi thăm khám – miễn phí.'; ?>
					</span>
				</p>
			</section>

			<?php if ( $qd_after ) : ?>
				<section id="luu-y" class="service-section">
					<h2 class="service-section__title">Lưu ý trước &amp; sau điều trị</h2>
					<ul class="checklist">
						<?php foreach ( $qd_after as $qd_line ) : ?>
							<li><?php qd_the_icon( 'circle-check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( $qd_faq ) : ?>
				<section id="hoi-dap" class="service-section">
					<h2 class="service-section__title">Câu hỏi thường gặp</h2>
					<div class="accordion">
						<?php foreach ( $qd_faq as $qd_i => list( $qd_q, $qd_a ) ) : ?>
							<details<?php echo 0 === $qd_i ? ' open' : ''; ?>>
								<summary><?php echo esc_html( $qd_q ); ?></summary>
								<div class="accordion__body"><p><?php echo esc_html( $qd_a ); ?></p></div>
							</details>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $qd_doctor ) : ?>
				<aside class="doctor-card" aria-label="Bác sĩ phụ trách">
					<span class="avatar avatar--lg"><?php echo get_the_post_thumbnail( $qd_doctor, 'thumbnail', array( 'alt' => '' ) ); ?></span>
					<div>
						<p class="doctor-card__label">Bác sĩ phụ trách</p>
						<p class="doctor-card__name"><?php echo esc_html( get_the_title( $qd_doctor ) ); ?></p>
						<p class="doctor-card__title"><?php echo esc_html( (string) get_post_meta( $qd_doctor->ID, '_qd_doctor_title', true ) ); ?></p>
					</div>
					<a class="link-more" href="<?php echo esc_url( home_url( '/gioi-thieu/doi-ngu-bac-si/' ) ); ?>">Xem hồ sơ<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
				</aside>
			<?php endif; ?>
		</div>

		<aside class="with-aside__aside booking-card" aria-label="Đặt lịch">
			<p class="booking-card__label">Chi phí tham khảo</p>
			<p class="booking-card__price">
				<?php if ( $qd_facts['price_from'] ) : ?>
					<small>Từ</small> <?php echo esc_html( qd_price( $qd_facts['price_from'] ) ); ?><small><?php echo esc_html( $qd_facts['price_unit'] ); ?></small>
				<?php else : ?>
					Báo giá khi thăm khám
				<?php endif; ?>
			</p>
			<?php if ( $qd_facts['sessions'] ) : ?>
				<p class="booking-card__meta"><?php qd_the_icon( 'repeat', array( 'size' => 16 ) ); ?><?php echo esc_html( $qd_facts['sessions'] ); ?></p>
			<?php endif; ?>
			<?php echo qd_button( 'Đặt lịch khám', qd_booking_url( $qd_id ), array( 'class' => 'btn--block', 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="booking-card__alt">
				<a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php qd_the_icon( 'phone', array( 'size' => 18 ) ); ?><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a>
				<a href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 18 ) ); ?>Chat Zalo</a>
			</div>
			<p class="booking-card__note"><?php qd_the_icon( 'shield-check', array( 'size' => 16 ) ); ?>Tư vấn và soi da miễn phí</p>
		</aside>
	</div>
</div>

<?php if ( $qd_siblings ) : ?>
	<section class="section section--mint" aria-labelledby="related-services">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'         => 'related-services',
					'title'      => 'Dịch vụ cùng nhóm ' . mb_strtolower( $qd_category->post_title ),
					'link_label' => 'Xem tất cả',
					'link_url'   => get_permalink( $qd_category ),
				)
			);
			?>
			<div class="grid grid--3">
				<?php foreach ( array_slice( $qd_siblings, 0, 3 ) as $qd_sibling ) : ?>
					<?php get_template_part( 'template-parts/components/service-card', null, array( 'post' => $qd_sibling ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $qd_articles ) : ?>
	<section class="section" aria-labelledby="related-articles">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'related-articles',
					'title' => 'Bài viết liên quan',
				)
			);
			?>
			<div class="grid grid--3">
				<?php foreach ( $qd_articles as $qd_article ) : ?>
					<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_article ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section<?php echo $qd_articles ? ' section--flush-top' : ''; ?>">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/components/booking-strip',
			null,
			array(
				'service' => $qd_id,
				'source'  => 'service',
			)
		);
		?>
	</div>
</section>
