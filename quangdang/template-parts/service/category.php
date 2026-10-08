<?php
/**
 * Service CATEGORY page (Điều trị mụn…).
 *
 * Job: "compare the treatments for one problem, then open one".
 * Hero (facts) → service cards → comparison table (desktop) → "Hiểu về…" + help card
 * → FAQ → related articles → booking strip. Blocks without data are not rendered.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_id       = get_the_ID();
$qd_facts    = qd_service_facts();
$qd_services = qd_service_children( $qd_id );
$qd_faq      = qd_pipe_rows( (string) get_post_meta( $qd_id, '_qd_faq', true ), 2 );
$qd_articles = qd_service_related_posts( $qd_id, 3 );
$qd_has_body = '' !== trim( wp_strip_all_tags( get_the_content() ) );
$qd_lower    = mb_strtolower( get_the_title() );
$qd_about    = preg_replace( '/^(điều trị|xóa)\s+/u', '', $qd_lower );

qd_schema_add(
	array(
		'@type'       => 'MedicalWebPage',
		'name'        => get_the_title(),
		'description' => get_the_excerpt(),
		'url'         => get_permalink(),
	)
);
qd_schema_faq( $qd_faq );

$qd_fact_items = array_filter(
	array(
		$qd_facts['count'] ? array( 'layers', 'Số dịch vụ', $qd_facts['count'] . ' liệu trình' ) : null,
		$qd_facts['price_from'] ? array( 'banknote', 'Giá từ', array( qd_price( $qd_facts['price_from'] ), $qd_facts['price_unit'] ) ) : null,
		array( 'shield-check', 'Tư vấn và soi da', 'Miễn phí' ),
	)
);
?>
<section class="service-hero service-hero--category">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<div class="service-hero__grid">
			<div class="service-hero__text">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<dl class="facts facts--compact">
					<?php foreach ( $qd_fact_items as list( $qd_icon, $qd_label, $qd_value ) ) : ?>
						<div class="facts__item">
							<span class="icon-tile icon-tile--sm"><?php qd_the_icon( $qd_icon, array( 'size' => 20 ) ); ?></span>
							<div><dt><?php echo esc_html( $qd_label ); ?></dt><dd><?php echo is_array( $qd_value ) ? esc_html( $qd_value[0] ) . ( $qd_value[1] ? '<small>' . esc_html( $qd_value[1] ) . '</small>' : '' ) : esc_html( $qd_value ); ?></dd></div>
						</div>
					<?php endforeach; ?>
				</dl>
				<?php if ( $qd_services ) : ?>
					<div class="btn-row service-hero__ctas">
						<?php echo qd_button( 'Xem các liệu trình', '#lieu-trinh', array( 'size' => 'lg', 'variant' => 'outline', 'icon' => 'chevron-down' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="service-hero__media">
					<?php echo qd_thumb( null, 'qd-card', '4-3', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( $qd_services ) : ?>
	<section id="lieu-trinh" class="section cat-section" aria-labelledby="cat-services-title">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'cat-services-title',
					'title' => 'Chọn liệu trình phù hợp',
					'desc'  => 'So sánh nhanh số buổi, thời gian và chi phí. Chưa chắc nên chọn gì, bác sĩ sẽ soi da và tư vấn miễn phí.',
				)
			);
			?>
			<div class="grid grid--3 scroller">
				<?php foreach ( $qd_services as $qd_service ) : ?>
					<?php get_template_part( 'template-parts/components/service-card', null, array( 'post' => $qd_service ) ); ?>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $qd_services ) > 1 ) : ?>
				<div class="compare" role="region" aria-label="Bảng so sánh liệu trình <?php echo esc_attr( $qd_lower ); ?>" tabindex="0">
					<table class="compare__table">
						<caption class="sr-only">So sánh các liệu trình <?php echo esc_html( $qd_lower ); ?></caption>
						<thead>
							<tr>
								<th scope="col">Liệu trình</th>
								<th scope="col">Phù hợp với</th>
								<th scope="col">Số buổi</th>
								<th scope="col">Mỗi buổi</th>
								<th scope="col">Giá từ</th>
								<th scope="col"><span class="sr-only">Chi tiết</span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $qd_services as $qd_service ) : ?>
								<?php $qd_sf = qd_service_facts( $qd_service ); ?>
								<tr>
									<th scope="row"><a href="<?php echo esc_url( get_permalink( $qd_service ) ); ?>"><?php echo esc_html( get_the_title( $qd_service ) ); ?></a></th>
									<td class="compare__fit"><?php echo has_excerpt( $qd_service ) ? esc_html( get_the_excerpt( $qd_service ) ) : '–'; ?></td>
									<td><?php echo $qd_sf['sessions'] ? esc_html( $qd_sf['sessions'] ) : '–'; ?></td>
									<td><?php echo $qd_sf['duration'] ? esc_html( $qd_sf['duration'] ) : '–'; ?></td>
									<td class="compare__price">
										<?php if ( $qd_sf['price_from'] ) : ?>
											<span class="price"><?php echo esc_html( qd_price( $qd_sf['price_from'] ) ); ?></span><?php echo $qd_sf['price_unit'] ? '<small>' . esc_html( $qd_sf['price_unit'] ) . '</small>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<?php else : ?>
											<span class="price-note">Báo giá khi khám</span>
										<?php endif; ?>
									</td>
									<td><a class="link-more" href="<?php echo esc_url( get_permalink( $qd_service ) ); ?>">Chi tiết<span class="sr-only"> <?php echo esc_html( get_the_title( $qd_service ) ); ?></span><?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( $qd_has_body ) : ?>
	<section id="hieu-ve" class="section section--mint" aria-labelledby="cat-about-title">
		<div class="container">
			<div class="with-aside">
				<div class="cat-about">
					<h2 class="section-title" id="cat-about-title">Hiểu về <?php echo esc_html( $qd_about ); ?></h2>
					<div class="prose"><?php the_content(); ?></div>
				</div>
				<aside class="with-aside__aside" aria-label="Gợi ý chọn dịch vụ">
					<?php get_template_part( 'template-parts/components/help-card' ); ?>
				</aside>
			</div>
		</div>
	</section>
<?php else : ?>
	<section class="section section--tight" aria-label="Gợi ý chọn dịch vụ">
		<div class="container"><div class="hub-help"><?php get_template_part( 'template-parts/components/help-card' ); ?></div></div>
	</section>
<?php endif; ?>

<?php if ( $qd_faq ) : ?>
	<section id="hoi-dap" class="section" aria-labelledby="cat-faq-title">
		<div class="container cat-faq">
			<?php qd_section_head( array( 'id' => 'cat-faq-title', 'title' => 'Câu hỏi thường gặp về ' . $qd_about ) ); ?>
			<div class="accordion">
				<?php foreach ( $qd_faq as $qd_i => list( $qd_q, $qd_a ) ) : ?>
					<details<?php echo 0 === $qd_i ? ' open' : ''; ?>>
						<summary><?php echo esc_html( $qd_q ); ?></summary>
						<div class="accordion__body"><p><?php echo esc_html( $qd_a ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $qd_articles ) : ?>
	<section class="section<?php echo $qd_faq ? ' section--flush-top' : ''; ?>" aria-labelledby="related-articles">
		<div class="container">
			<?php qd_section_head( array( 'id' => 'related-articles', 'title' => 'Bài viết về ' . $qd_about ) ); ?>
			<div class="grid grid--3 scroller">
				<?php foreach ( $qd_articles as $qd_article ) : ?>
					<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_article ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section<?php echo ( $qd_articles || $qd_faq ) ? ' section--flush-top' : ''; ?>">
	<div class="container">
		<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'category' ) ); ?>
	</div>
</section>
