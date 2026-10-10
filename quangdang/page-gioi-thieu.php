<?php
/**
 * About hub (/gioi-thieu/): photo stage, a few real facts, how a visit works (ruler timeline),
 * the six subpages as a bento, the lead doctor, booking strip.
 *
 * Job: "can I trust these people with my skin?"
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	$qd_text     = trim( wp_strip_all_tags( get_the_content() ) );
	$qd_long     = mb_strlen( $qd_text ) > 300;
	$qd_lead     = has_excerpt() ? get_the_excerpt() : ( $qd_text && ! $qd_long ? $qd_text : 'Bác sĩ da liễu trực tiếp thăm khám, soi da và xây dựng phác đồ riêng cho từng làn da, trong một không gian sạch sẽ, riêng tư, ngay tại Quỳnh Lưu.' );
	$qd_docs     = qd_about_doctors();
	$qd_lead_doc = $qd_docs ? $qd_docs[0] : null;
	$qd_years    = $qd_lead_doc ? (int) get_post_meta( $qd_lead_doc->ID, '_qd_doctor_years', true ) : 0;

	// Facts: only what the site really knows. Anything missing is simply left out.
	$qd_service_count = 0;
	foreach ( qd_service_categories() as $qd_cat ) {
		$qd_service_count += count( qd_service_children( $qd_cat->ID ) );
	}
	$qd_facts = apply_filters(
		'qd_about_facts',
		array(
			array( $qd_docs ? (string) count( $qd_docs ) : '', 'bác sĩ da liễu trực tiếp thăm khám' ),
			array( $qd_years ? $qd_years . '+' : '', 'năm kinh nghiệm của bác sĩ phụ trách' ),
			array( $qd_service_count ? (string) $qd_service_count : '', 'dịch vụ điều trị và chăm sóc da' ),
		)
	);

	// A visit, in order. Kept in step with the "Quy trình chuẩn y khoa" page.
	$qd_visit = apply_filters(
		'qd_about_visit_steps',
		array(
			array( 'Đón tiếp', 'Lễ tân ghi nhận vấn đề da của bạn và xếp lịch gặp bác sĩ.' ),
			array( 'Soi da', 'Bác sĩ hỏi tiền sử, soi da và tìm nguyên nhân của vấn đề.' ),
			array( 'Phác đồ riêng', 'Bác sĩ giải thích các lựa chọn, thời gian và chi phí trước khi bạn quyết định.' ),
			array( 'Điều trị', 'Thực hiện đúng phác đồ bằng thiết bị và dược mỹ phẩm chính hãng.' ),
			array( 'Theo dõi', 'Hẹn tái khám để bác sĩ đánh giá kết quả và điều chỉnh khi cần.' ),
		)
	);

	// Bento: two large cards with a photo, four small ones. Photo keys are theme assets (placeholders until real ones exist).
	$qd_pages = qd_about_pages();
	$qd_big   = array(
		'doi-ngu-bac-si'     => array( 'about/doi-ngu', 'Đội ngũ bác sĩ của phòng khám' ),
		'cong-nghe-san-pham' => array( 'about/may-picosure', 'Thiết bị điều trị tại phòng khám' ),
	);
	$qd_order = array_merge( array_intersect_key( $qd_pages, $qd_big ), array_diff_key( $qd_pages, $qd_big ) );

	get_template_part(
		'template-parts/about/hero',
		null,
		array(
			'title'     => 'Phòng khám da liễu do bác sĩ trực tiếp thăm khám',
			'lead'      => $qd_lead,
			'photo'     => 'about/le-tan',
			'photo_alt' => 'Quầy lễ tân Phòng khám Da liễu Thẩm mỹ Quang Đăng',
			'subnav'    => false,
			'stage'     => true,
		)
	);
	?>

	<section class="section section--tight about-proof" aria-labelledby="about-visit">
		<div class="container">
			<?php get_template_part( 'template-parts/about/facts', null, array( 'items' => $qd_facts, 'label' => 'Quang Đăng trong vài con số' ) ); ?>

			<div class="about-proof__visit">
				<?php
				qd_section_head(
					array(
						'id'         => 'about-visit',
						'title'      => 'Một lần khám diễn ra thế nào',
						'desc'       => 'Năm bước, bác sĩ đi cùng bạn từ lúc soi da đến khi tái khám.',
						'link_url'   => qd_about_url( 'quy-trinh-chuan-y-khoa' ),
						'link_label' => 'Xem quy trình chi tiết',
					)
				);
				get_template_part( 'template-parts/about/ruler', null, array( 'steps' => $qd_visit ) );
				?>
			</div>
		</div>
	</section>

	<section class="section section--mint" aria-labelledby="about-cards">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'about-cards',
					'title' => 'Tìm hiểu về phòng khám',
					'desc'  => 'Bác sĩ, thiết bị, không gian và quy trình, những điều bạn nên biết trước khi đến khám.',
				)
			);
			?>
			<ul class="about-bento">
				<?php foreach ( $qd_order as $qd_slug => $qd_page ) : ?>
					<?php
					$qd_photo = isset( $qd_big[ $qd_slug ] ) ? qd_about_photo( $qd_big[ $qd_slug ][0], '', '4-3', array( 'loading' => 'lazy' ), 'about-card__photo' ) : '';
					?>
					<li class="card about-card<?php echo isset( $qd_big[ $qd_slug ] ) ? ' about-card--large' : ''; ?>">
						<?php if ( $qd_photo ) : ?>
							<?php echo $qd_photo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endif; ?>
						<div class="about-card__body">
							<span class="icon-tile"><?php qd_the_icon( $qd_page[3], array( 'size' => 24 ) ); ?></span>
							<h3 class="card__title"><a href="<?php echo esc_url( qd_about_url( $qd_slug ) ); ?>"><?php echo esc_html( $qd_page[1] ); ?></a></h3>
							<p class="card__text"><?php echo esc_html( $qd_page[2] ); ?></p>
							<span class="card__arrow" aria-hidden="true"><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></span>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $qd_long ) : ?>
				<div class="prose about-prose"><?php the_content(); ?></div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $qd_lead_doc ) : ?>
		<?php
		$qd_title     = (string) get_post_meta( $qd_lead_doc->ID, '_qd_doctor_title', true );
		$qd_role      = (string) get_post_meta( $qd_lead_doc->ID, '_qd_doctor_role', true );
		$qd_education = array_slice( array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $qd_lead_doc->ID, '_qd_doctor_education', true ) ) ) ), 0, 3 );
		?>
		<section class="section" aria-labelledby="about-doctor">
			<div class="container about-doctor">
				<div class="about-doctor__photo">
					<?php echo qd_thumb( $qd_lead_doc, 'qd-portrait', '4-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="about-doctor__body">
					<h2 class="section-title" id="about-doctor"><?php echo esc_html( get_the_title( $qd_lead_doc ) ); ?></h2>
					<p class="about-doctor__title">
						<span><?php echo esc_html( trim( $qd_role . ( $qd_role && $qd_title ? ', ' : '' ) . $qd_title ) ); ?></span>
						<?php if ( $qd_years ) : ?>
							<span class="badge"><?php echo (int) $qd_years; ?>+ năm kinh nghiệm</span>
						<?php endif; ?>
					</p>
					<?php if ( has_excerpt( $qd_lead_doc ) ) : ?>
						<p class="lead"><?php echo esc_html( get_the_excerpt( $qd_lead_doc ) ); ?></p>
					<?php endif; ?>
					<?php if ( $qd_education ) : ?>
						<ul class="checklist about-doctor__edu">
							<?php foreach ( $qd_education as $qd_line ) : ?>
								<li><?php qd_the_icon( 'check-circle', array( 'size' => 20 ) ); ?><span><?php echo esc_html( $qd_line ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<div class="btn-row">
						<?php
						echo qd_button( 'Xem hồ sơ bác sĩ', get_permalink( $qd_lead_doc ), array( 'variant' => 'outline', 'icon_end' => 'arrow-right' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						echo qd_button( 'Cả đội ngũ bác sĩ', qd_about_url( 'doi-ngu-bac-si' ), array( 'variant' => 'ghost' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section section--flush-top" aria-label="Đặt lịch">
		<div class="container">
			<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'about' ) ); ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
