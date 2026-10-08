<?php
/**
 * Article.
 *
 * Job: "get an answer" → keep reading → see the matching service → book.
 * Head (topic, H1, reviewer · updated · reading time) → cover → [sticky TOC | promo box, body, inline
 * service card] → disclaimer → share → reviewer → related articles → booking strip.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$qd_id       = get_the_ID();
	$qd_url      = get_permalink();
	$qd_tags     = get_the_tags();
	$qd_topic    = $qd_tags ? $qd_tags[0] : null;
	$qd_service  = qd_post_topic_service();
	$qd_reviewer = qd_post_reviewer();
	$qd_promo    = qd_promo_status();
	$qd_body     = qd_article_body();
	$qd_toc      = count( $qd_body['toc'] ) >= 2 ? $qd_body['toc'] : array();
	$qd_related  = qd_post_related( $qd_id, 3 );
	$qd_cover    = has_post_thumbnail() ? wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' ) : '';

	// Structured data: the article, reviewed by a physician when one is set.
	$qd_node = array(
		'@type'            => array( 'MedicalWebPage', 'Article' ),
		'@id'              => $qd_url . '#article',
		'mainEntityOfPage' => $qd_url,
		'headline'         => get_the_title(),
		'description'      => get_the_excerpt(),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'inLanguage'       => 'vi',
		'author'           => array( '@id' => home_url( '/#clinic' ) ),
		'publisher'        => array( '@id' => home_url( '/#clinic' ) ),
	);
	if ( $qd_cover ) {
		$qd_node['image'] = array( $qd_cover );
	}
	if ( $qd_reviewer ) {
		$qd_node['reviewedBy'] = array(
			'@type'    => 'Physician',
			'name'     => get_the_title( $qd_reviewer ),
			'jobTitle' => (string) get_post_meta( $qd_reviewer->ID, '_qd_doctor_title', true ),
			'url'      => get_permalink( $qd_reviewer ),
		);
		$qd_node['lastReviewed'] = get_the_modified_date( 'Y-m-d' );
	}
	qd_schema_add( $qd_node );
	?>
	<article id="post-<?php the_ID(); ?>">
		<header class="page-hero article-head">
			<div class="container">
				<div class="article-wrap">
					<?php qd_breadcrumbs(); ?>
					<?php if ( $qd_topic ) : ?>
						<a class="article-head__topic" href="<?php echo esc_url( get_tag_link( $qd_topic ) ); ?>"><?php echo esc_html( $qd_topic->name ); ?></a>
					<?php endif; ?>
					<h1 class="page-title"><?php the_title(); ?></h1>
					<p class="article-meta">
						<?php if ( $qd_reviewer ) : ?>
							<span class="article-meta__item article-meta__item--review">
								<?php qd_the_icon( 'badge-check', array( 'size' => 18 ) ); ?>
								<span>Tham vấn y khoa: <a href="<?php echo esc_url( get_permalink( $qd_reviewer ) ); ?>"><?php echo esc_html( get_the_title( $qd_reviewer ) ); ?></a></span>
							</span>
						<?php endif; ?>
						<span class="article-meta__item">
							<?php qd_the_icon( 'calendar-days', array( 'size' => 16 ) ); ?>
							<span>Cập nhật <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'd/m/Y' ) ); ?></time></span>
						</span>
						<span class="article-meta__item">
							<?php qd_the_icon( 'clock', array( 'size' => 16 ) ); ?>
							<span><?php echo (int) qd_reading_time(); ?> phút đọc</span>
						</span>
					</p>
				</div>
			</div>
		</header>

		<div class="container article-body">
			<div class="article-wrap article-layout<?php echo $qd_toc ? '' : ' article-layout--solo'; ?>">
				<?php if ( $qd_toc ) : ?>
					<aside class="article-toc" aria-label="Mục lục bài viết">
						<nav class="toc" aria-labelledby="toc-title" data-scrollspy>
							<p class="toc__title" id="toc-title"><?php qd_the_icon( 'list-checks', array( 'size' => 18 ) ); ?>Nội dung bài viết</p>
							<ol class="toc__list">
								<?php foreach ( $qd_toc as $qd_item ) : ?>
									<li><a href="#<?php echo esc_attr( $qd_item['id'] ); ?>"><?php echo esc_html( $qd_item['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ol>
						</nav>
					</aside>
				<?php endif; ?>

				<div class="article-main">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="article-cover">
							<?php echo qd_thumb( null, 'qd-wide', '16-9', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => get_the_title() ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</figure>
					<?php endif; ?>

					<?php if ( $qd_promo ) : ?>
						<?php
						$qd_start = (string) get_post_meta( $qd_id, '_qd_promo_start', true );
						$qd_end   = (string) get_post_meta( $qd_id, '_qd_promo_end', true );
						$qd_label = (string) get_post_meta( $qd_id, '_qd_promo_label', true );
						$qd_fmt   = fn( $d ) => $d ? wp_date( 'd/m/Y', strtotime( $d ) ) : '';
						$qd_ended = 'ended' === $qd_promo['state'];
						?>
						<section class="promo-box<?php echo $qd_ended ? ' promo-box--ended' : ''; ?>" aria-label="Thông tin ưu đãi">
							<div class="promo-box__info">
								<p class="promo-box__badges">
									<?php if ( $qd_label ) : ?>
										<span class="badge badge--accent"><?php qd_the_icon( 'gift', array( 'size' => 14 ) ); ?><?php echo esc_html( $qd_label ); ?></span>
									<?php endif; ?>
									<span class="badge <?php echo 'active' === $qd_promo['state'] ? 'badge--promo' : 'badge--neutral'; ?>"><?php qd_the_icon( 'clock', array( 'size' => 14 ) ); ?><?php echo esc_html( $qd_promo['label'] ); ?></span>
								</p>
								<p class="promo-box__dates">
									<?php qd_the_icon( 'calendar-days', array( 'size' => 18 ) ); ?>
									<span>
										<?php if ( $qd_start ) : ?>
											Áp dụng từ <strong><?php echo esc_html( $qd_fmt( $qd_start ) ); ?></strong> đến <strong><?php echo esc_html( $qd_fmt( $qd_end ) ); ?></strong>
										<?php else : ?>
											Áp dụng đến hết <strong><?php echo esc_html( $qd_fmt( $qd_end ) ); ?></strong>
										<?php endif; ?>
									</span>
								</p>
								<?php if ( $qd_ended ) : ?>
									<p class="promo-box__note">Chương trình này đã kết thúc. Bạn vẫn có thể đặt lịch để bác sĩ tư vấn liệu trình phù hợp.</p>
								<?php endif; ?>
							</div>
							<?php
							echo qd_button( // phpcs:ignore WordPress.Security.EscapeOutput
								$qd_ended ? 'Đặt lịch tư vấn' : 'Đặt lịch nhận ưu đãi',
								qd_booking_url( $qd_service ),
								array(
									'icon'    => 'calendar-check',
									'variant' => $qd_ended ? 'outline' : 'primary',
								)
							);
							?>
						</section>
					<?php endif; ?>

					<?php if ( $qd_toc ) : ?>
						<details class="toc-box">
							<summary><?php qd_the_icon( 'list-checks', array( 'size' => 20 ) ); ?><span>Nội dung bài viết</span></summary>
							<nav aria-label="Mục lục (điện thoại)">
								<ol class="toc__list">
									<?php foreach ( $qd_toc as $qd_item ) : ?>
										<li><a href="#<?php echo esc_attr( $qd_item['id'] ); ?>"><?php echo esc_html( $qd_item['label'] ); ?></a></li>
									<?php endforeach; ?>
								</ol>
							</nav>
						</details>
					<?php endif; ?>

					<div class="prose article-prose"><?php echo $qd_body['html']; // phpcs:ignore WordPress.Security.EscapeOutput -- filtered post content. ?></div>

					<p class="notice article-disclaimer">
						<?php qd_the_icon( 'stethoscope', array( 'size' => 20 ) ); ?>
						<span><strong>Lưu ý y khoa:</strong> thông tin trong bài chỉ để tham khảo, không thay thế việc thăm khám và chẩn đoán của bác sĩ. Tình trạng da của mỗi người khác nhau, hãy để bác sĩ thăm khám trực tiếp trước khi dùng thuốc hoặc thực hiện thủ thuật.</span>
					</p>

					<div class="share" data-share>
						<p class="share__label"><?php qd_the_icon( 'share-2', array( 'size' => 18 ) ); ?>Chia sẻ bài viết</p>
						<div class="share__buttons">
							<a class="btn btn--outline btn--sm" href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $qd_url ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'facebook', array( 'size' => 18 ) ); ?><span>Facebook</span></a>
							<a class="btn btn--outline btn--sm" href="<?php echo esc_url( 'https://zalo.me/share?url=' . rawurlencode( $qd_url ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 18 ) ); ?><span>Zalo</span></a>
							<button class="btn btn--outline btn--sm" type="button" hidden data-copy-link="<?php echo esc_url( $qd_url ); ?>"><?php qd_the_icon( 'link', array( 'size' => 18 ) ); ?><span data-copy-label>Sao chép liên kết</span></button>
						</div>
						<span class="sr-only" role="status" aria-live="polite" data-copy-status></span>
					</div>

					<?php if ( $qd_reviewer ) : ?>
						<aside class="reviewer" aria-label="Bác sĩ tham vấn">
							<span class="avatar avatar--lg"><?php echo get_the_post_thumbnail( $qd_reviewer, 'thumbnail', array( 'alt' => '' ) ); ?></span>
							<div class="reviewer__text">
								<p class="reviewer__label">Tham vấn y khoa</p>
								<p class="reviewer__name"><?php echo esc_html( get_the_title( $qd_reviewer ) ); ?></p>
								<p class="reviewer__title"><?php echo esc_html( (string) get_post_meta( $qd_reviewer->ID, '_qd_doctor_title', true ) ); ?></p>
							</div>
							<a class="link-more" href="<?php echo esc_url( get_permalink( $qd_reviewer ) ); ?>">Xem hồ sơ bác sĩ<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
						</aside>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</article>

	<?php if ( $qd_related ) : ?>
		<section class="section section--mint" aria-labelledby="related-posts">
			<div class="container">
				<?php
				qd_section_head(
					array(
						'id'         => 'related-posts',
						'title'      => 'Bài viết liên quan',
						'link_label' => 'Xem tất cả bài viết',
						'link_url'   => get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/tin-tuc/' ),
					)
				);
				?>
				<div class="grid grid--3 scroller">
					<?php foreach ( $qd_related as $qd_rel ) : ?>
						<?php get_template_part( 'template-parts/components/post-card', null, array( 'post' => $qd_rel ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/components/booking-strip',
				null,
				array(
					'service' => $qd_service,
					'source'  => 'article',
				)
			);
			?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
