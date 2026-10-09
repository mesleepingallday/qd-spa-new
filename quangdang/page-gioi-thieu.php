<?php
/**
 * About hub (/gioi-thieu/): short intro + photo, six cards to the subpages,
 * lead-doctor preview, booking strip.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	$qd_text  = trim( wp_strip_all_tags( get_the_content() ) );
	$qd_long  = mb_strlen( $qd_text ) > 300;
	$qd_lead  = has_excerpt() ? get_the_excerpt() : ( $qd_text && ! $qd_long ? $qd_text : 'Bác sĩ da liễu trực tiếp thăm khám, soi da và xây dựng phác đồ riêng cho từng làn da – trong một không gian sạch sẽ, riêng tư, ngay tại Quỳnh Lưu.' );
	$qd_docs  = qd_about_doctors( 1 );
	$qd_lead_doc = $qd_docs ? $qd_docs[0] : null;

	get_template_part(
		'template-parts/about/hero',
		null,
		array(
			'lead'      => $qd_lead,
			'photo'     => 'about/le-tan',
			'photo_alt' => 'Quầy lễ tân Phòng khám Da liễu Thẩm mỹ Quang Đăng',
			'subnav'    => false,
		)
	);
	?>

	<section class="section" aria-labelledby="about-cards">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'about-cards',
					'title' => 'Tìm hiểu về phòng khám',
					'desc'  => 'Bác sĩ, thiết bị, không gian và quy trình – những điều bạn nên biết trước khi đến khám.',
				)
			);
			?>
			<ul class="grid grid--3 about-cards">
				<?php foreach ( qd_about_pages() as $qd_slug => $qd_page ) : ?>
					<li class="card about-card">
						<span class="icon-tile"><?php qd_the_icon( $qd_page[3], array( 'size' => 24 ) ); ?></span>
						<h3 class="card__title"><a href="<?php echo esc_url( qd_about_url( $qd_slug ) ); ?>"><?php echo esc_html( $qd_page[1] ); ?></a></h3>
						<p class="card__text"><?php echo esc_html( $qd_page[2] ); ?></p>
						<span class="card__arrow" aria-hidden="true"><?php qd_the_icon( 'arrow-right', array( 'size' => 18 ) ); ?></span>
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
		$qd_title = (string) get_post_meta( $qd_lead_doc->ID, '_qd_doctor_title', true );
		$qd_role  = (string) get_post_meta( $qd_lead_doc->ID, '_qd_doctor_role', true );
		$qd_years = (int) get_post_meta( $qd_lead_doc->ID, '_qd_doctor_years', true );
		?>
		<section class="section section--mint" aria-labelledby="about-doctor">
			<div class="container about-doctor">
				<div class="about-doctor__photo">
					<?php echo qd_thumb( $qd_lead_doc, 'qd-portrait', '4-5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="about-doctor__body">
					<p class="about-kicker"><?php echo esc_html( $qd_role ? $qd_role : 'Bác sĩ phụ trách chuyên môn' ); ?></p>
					<h2 class="section-title" id="about-doctor"><?php echo esc_html( get_the_title( $qd_lead_doc ) ); ?></h2>
					<p class="about-doctor__title">
						<?php echo esc_html( $qd_title ); ?>
						<?php if ( $qd_years ) : ?>
							<span class="badge"><?php echo (int) $qd_years; ?>+ năm kinh nghiệm</span>
						<?php endif; ?>
					</p>
					<?php if ( has_excerpt( $qd_lead_doc ) ) : ?>
						<p class="lead"><?php echo esc_html( get_the_excerpt( $qd_lead_doc ) ); ?></p>
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

	<section class="section" aria-label="Đặt lịch">
		<div class="container">
			<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'about' ) ); ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
