<?php
/**
 * Skin quiz (onboarding) at /tim-lieu-trinh/.
 *
 * Job: "I don't know what I need" → 4 short questions, one per screen → 1–3 recommended
 * services → book with the top one pre-selected.
 * Markup for the questions is server-rendered (real inputs, so keyboard and screen readers work);
 * the matching rules live in assets/js/quiz.js; service data is handed over as JSON.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

// Service data for the matching rules (title, link, facts).
$qd_quiz_services = array();
$qd_quiz_articles = array();
foreach ( qd_service_categories() as $qd_cat ) {
	foreach ( qd_service_children( $qd_cat->ID ) as $qd_s ) {
		$qd_f                 = qd_service_facts( $qd_s );
		$qd_quiz_services[]   = array(
			'slug'       => $qd_s->post_name,
			'title'      => get_the_title( $qd_s ),
			'url'        => get_permalink( $qd_s ),
			'book'       => qd_booking_url( $qd_s ),
			'category'   => $qd_cat->post_name,
			'excerpt'    => get_the_excerpt( $qd_s ),
			'price_from' => $qd_f['price_from'],
			'price_text' => $qd_f['price_from'] ? qd_price( $qd_f['price_from'] ) : '',
			'unit'       => $qd_f['price_unit'],
			'sessions'   => $qd_f['sessions'],
			'downtime'   => $qd_f['downtime'],
		);
	}
	// Two articles per category for the result screen (tagged with the category slug; falls back to the handbook).
	$qd_posts = qd_service_related_posts( $qd_cat, 2 );
	if ( ! $qd_posts ) {
		$qd_posts = get_posts(
			array(
				'category_name'  => 'cam-nang-lam-dep',
				'posts_per_page' => 2,
				'no_found_rows'  => true,
			)
		);
	}
	$qd_quiz_articles[ $qd_cat->post_name ] = $qd_posts;
}

$qd_areas = array(
	'mat'      => 'Mặt',
	'lung'     => 'Lưng',
	'nach'     => 'Nách',
	'tay-chan' => 'Tay – chân',
	'bikini'   => 'Vùng bikini/bẹn',
);

$qd_skins = array(
	'dau'      => array( 'Da dầu', 'Hay bóng nhờn, lỗ chân lông to', 'droplets' ),
	'kho'      => array( 'Da khô', 'Dễ căng rát, bong tróc', 'sun' ),
	'hon-hop'  => array( 'Da hỗn hợp', 'Vùng chữ T dầu, hai má khô', 'layers' ),
	'nhay-cam' => array( 'Da nhạy cảm', 'Dễ đỏ, châm chích khi đổi sản phẩm', 'heart' ),
	'khong-ro' => array( 'Không rõ', 'Bác sĩ sẽ soi da giúp bạn', 'circle-help' ),
);

$qd_priorities = array(
	'tiet-kiem' => array( 'Tiết kiệm chi phí', 'Ưu tiên liệu trình giá hợp lý', 'banknote' ),
	'nhanh'     => array( 'Hiệu quả nhanh', 'Ưu tiên ít buổi, thấy thay đổi sớm', 'timer' ),
	'khong-nghi' => array( 'Không cần nghỉ dưỡng', 'Đi làm, sinh hoạt bình thường sau điều trị', 'leaf' ),
);

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<script>document.documentElement.className += ' qd-js';</script>

	<section class="page-hero quiz-hero">
		<div class="container">
			<?php qd_breadcrumbs(); ?>
			<h1 class="page-title">Tìm liệu trình hợp với làn da của bạn</h1>
			<p class="lead">Trả lời vài câu hỏi ngắn, chỉ mất khoảng 1 phút. Không cần đăng ký hay để lại số điện thoại.</p>
		</div>
	</section>

	<section class="section section--tight quiz-section" aria-label="Kiểm tra da">
		<div class="container">

			<noscript>
				<div class="quiz-fallback">
					<h2 class="section-title">Chọn vấn đề bạn đang quan tâm</h2>
					<p class="section-lead">Mỗi vấn đề dẫn tới các liệu trình phù hợp, kèm giá và số buổi.</p>
					<ul class="quiz-fallback__list">
						<?php foreach ( qd_concerns() as $qd_key => $qd_c ) : ?>
							<li>
								<a class="quiz-fallback__item" href="<?php echo esc_url( $qd_c['url'] ); ?>">
									<span class="quiz-fallback__img"><?php echo qd_asset_img( 'concerns/' . $qd_key, '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<span><?php echo esc_html( $qd_c['label'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="btn-row">
						<?php echo qd_button( 'Đặt lịch tư vấn miễn phí', qd_booking_url(), array( 'size' => 'lg', 'icon' => 'calendar-days' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
			</noscript>

			<div class="quiz" data-quiz data-zalo="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" data-booking="<?php echo esc_url( qd_booking_url() ); ?>">

				<div class="quiz__top" data-quiz-top>
					<div class="quiz__progress-head">
						<p class="quiz__step-label" data-quiz-label aria-live="polite">Bước 1/4</p>
						<a class="quiz__restore" data-quiz-restore href="#" hidden>Xem lại kết quả lần trước</a>
					</div>
					<div class="quiz__bar" role="progressbar" aria-label="Tiến độ kiểm tra da" aria-valuemin="0" aria-valuemax="4" aria-valuenow="1"><span data-quiz-fill style="width:25%"></span></div>
				</div>

				<form class="quiz__form" data-quiz-form novalidate>

					<div class="quiz__step" data-step="concern" role="group" aria-labelledby="q-concern">
						<h2 class="quiz__q" id="q-concern" tabindex="-1">Bạn đang quan tâm vấn đề nào?</h2>
						<p class="quiz__hint">Chọn một hoặc nhiều vấn đề.</p>
						<div class="quiz__options quiz__options--concerns">
							<?php foreach ( qd_concerns() as $qd_key => $qd_c ) : ?>
								<label class="qopt qopt--pic">
									<input type="checkbox" name="concern" value="<?php echo esc_attr( $qd_key ); ?>" data-label="<?php echo esc_attr( $qd_c['label'] ); ?>">
									<span class="qopt__card">
										<span class="qopt__img"><?php echo qd_asset_img( 'concerns/' . $qd_key, '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<span class="qopt__label"><?php echo esc_html( $qd_c['label'] ); ?></span>
										<span class="qopt__tick" aria-hidden="true"><?php qd_the_icon( 'check', array( 'size' => 16 ) ); ?></span>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="quiz__step" data-step="area" role="group" aria-labelledby="q-area" hidden>
						<h2 class="quiz__q" id="q-area" tabindex="-1">Bạn muốn điều trị ở vùng nào?</h2>
						<p class="quiz__hint">Chọn tất cả vùng phù hợp.</p>
						<div class="quiz__options quiz__options--text">
							<?php foreach ( $qd_areas as $qd_key => $qd_label ) : ?>
								<label class="qopt" data-area="<?php echo esc_attr( $qd_key ); ?>">
									<input type="checkbox" name="area" value="<?php echo esc_attr( $qd_key ); ?>" data-label="<?php echo esc_attr( $qd_label ); ?>">
									<span class="qopt__card">
										<span class="qopt__text"><span class="qopt__label"><?php echo esc_html( $qd_label ); ?></span></span>
										<span class="qopt__tick" aria-hidden="true"><?php qd_the_icon( 'check', array( 'size' => 16 ) ); ?></span>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="quiz__step" data-step="skin" role="radiogroup" aria-labelledby="q-skin" hidden>
						<h2 class="quiz__q" id="q-skin" tabindex="-1">Da của bạn thuộc loại nào?</h2>
						<p class="quiz__hint">Không chắc cũng không sao, bạn chọn “Không rõ”.</p>
						<div class="quiz__options quiz__options--text">
							<?php foreach ( $qd_skins as $qd_key => list( $qd_label, $qd_desc, $qd_icon ) ) : ?>
								<label class="qopt">
									<input type="radio" name="skin" value="<?php echo esc_attr( $qd_key ); ?>" data-label="<?php echo esc_attr( $qd_label ); ?>">
									<span class="qopt__card">
										<span class="icon-tile icon-tile--sm"><?php qd_the_icon( $qd_icon, array( 'size' => 20 ) ); ?></span>
										<span class="qopt__text"><span class="qopt__label"><?php echo esc_html( $qd_label ); ?></span><span class="qopt__desc"><?php echo esc_html( $qd_desc ); ?></span></span>
										<span class="qopt__tick" aria-hidden="true"><?php qd_the_icon( 'check', array( 'size' => 16 ) ); ?></span>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="quiz__step" data-step="priority" role="radiogroup" aria-labelledby="q-priority" hidden>
						<h2 class="quiz__q" id="q-priority" tabindex="-1">Bạn ưu tiên điều gì nhất?</h2>
						<p class="quiz__hint">Chọn một điều quan trọng nhất với bạn.</p>
						<div class="quiz__options quiz__options--text">
							<?php foreach ( $qd_priorities as $qd_key => list( $qd_label, $qd_desc, $qd_icon ) ) : ?>
								<label class="qopt">
									<input type="radio" name="priority" value="<?php echo esc_attr( $qd_key ); ?>" data-label="<?php echo esc_attr( $qd_label ); ?>">
									<span class="qopt__card">
										<span class="icon-tile icon-tile--sm"><?php qd_the_icon( $qd_icon, array( 'size' => 20 ) ); ?></span>
										<span class="qopt__text"><span class="qopt__label"><?php echo esc_html( $qd_label ); ?></span><span class="qopt__desc"><?php echo esc_html( $qd_desc ); ?></span></span>
										<span class="qopt__tick" aria-hidden="true"><?php qd_the_icon( 'check', array( 'size' => 16 ) ); ?></span>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>

					<p class="field__error quiz__error" data-quiz-error role="alert" hidden></p>

					<div class="quiz__nav">
						<button class="btn btn--ghost" type="button" data-quiz-back hidden><?php qd_the_icon( 'arrow-left', array( 'size' => 18 ) ); ?><span>Quay lại</span></button>
						<button class="btn btn--primary btn--lg quiz__next" type="submit" data-quiz-next><span>Tiếp tục</span><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></button>
					</div>
				</form>

				<div class="quiz__result" data-quiz-result hidden></div>
			</div>

			<?php // Related articles, one block per service category; the script shows the one that matches the top result. ?>
			<div hidden data-quiz-articles-source>
				<?php foreach ( $qd_quiz_articles as $qd_cat_slug => $qd_posts ) : ?>
					<?php if ( $qd_posts ) : ?>
						<ul class="article-list" data-articles="<?php echo esc_attr( $qd_cat_slug ); ?>">
							<?php foreach ( $qd_posts as $qd_post ) : ?>
								<li class="article-row">
									<?php echo qd_thumb( $qd_post, 'qd-card', '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<div>
										<h3 class="article-row__title"><a href="<?php echo esc_url( get_permalink( $qd_post ) ); ?>"><?php echo esc_html( get_the_title( $qd_post ) ); ?></a></h3>
										<?php echo qd_post_meta_line( $qd_post ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<script type="application/json" id="quiz-data"><?php echo wp_json_encode( array( 'services' => $qd_quiz_services ), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); ?></script>
		</div>
	</section>
	<?php
endwhile;
get_footer();
