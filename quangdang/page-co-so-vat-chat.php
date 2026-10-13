<?php
/**
 * About: Cơ sở vật chất — guided tour rows of the real facility + address / getting-there block.
 * Rows are real-photo slots (about/*). In production a row only renders when one of its photos
 * is real (never a placeholder), following template-parts/home/results.php.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	// Each row: title, text, facts [label => value], candidate photos [key, alt] in priority order.
	$qd_rows = apply_filters(
		'qd_about_facility',
		array(
			array(
				'title' => 'Đón tiếp & sảnh chờ',
				'text'  => 'Khu đón tiếp ngay lối vào. Sảnh chờ có ghế sofa để bạn ngồi thư giãn trước khi thăm khám.',
				'facts' => array(
					'Không gian' => 'Sảnh chờ có sofa',
					'Dành cho'   => 'Khách và người đi cùng',
				),
				'photos' => array(
					array( 'about/sanh-cho', 'Sảnh chờ của phòng khám với ghế sofa màu xanh' ),
					array( 'about/le-tan', 'Lễ tân tại quầy đón tiếp của Phòng khám Quang Đăng' ),
					array( 'about/khu-don-tiep', 'Khu đón tiếp với logo Qbe Quang Đăng' ),
				),
			),
			array(
				'title' => 'Phòng tư vấn & soi da',
				'text'  => 'Bác sĩ thăm khám và soi da bằng máy phân tích trước khi tư vấn liệu trình.',
				'facts' => array(
					'Thực hiện bởi' => 'Bác sĩ da liễu',
					'Thiết bị'      => 'Máy soi da',
				),
				'photos' => array(
					array( 'about/phong-tu-van', 'Bác sĩ thăm khám và soi da cho khách bằng máy phân tích da' ),
				),
			),
			array(
				'title' => 'Phòng chăm sóc da',
				'text'  => 'Mỗi giường có rèm riêng, giúp bạn thoải mái và kín đáo khi chăm sóc da.',
				'facts' => array(
					'Riêng tư' => 'Rèm riêng từng giường',
					'Vệ sinh'  => 'Khăn riêng cho từng khách',
				),
				'photos' => array(
					array( 'about/phong-cham-soc-da', 'Phòng chăm sóc da với giường có rèm trắng riêng cho từng giường' ),
					array( 'about/phong-dieu-tri', 'Nhân viên chăm sóc da cho khách tại phòng điều trị' ),
					array( 'about/loi-vao', 'Nhìn vào phòng chăm sóc da từ cửa ra vào' ),
				),
			),
			array(
				'title' => 'Phòng điều trị laser',
				'text'  => 'Phòng điều trị laser tách biệt, thực hiện theo chỉ định của bác sĩ.',
				'facts' => array(
					'Không gian' => 'Phòng riêng',
					'Thực hiện'  => 'Theo chỉ định của bác sĩ',
				),
				'photos' => array(
					array( 'about/phong-laser', 'Phòng điều trị laser của phòng khám' ),
				),
			),
			array(
				'title' => 'Khu triệt lông',
				'text'  => 'Khu triệt lông bố trí gọn gàng, mỗi lượt điều trị có giường riêng.',
				'facts' => array(
					'Khu vực' => 'Triệt lông',
					'Bố trí'  => 'Giường điều trị riêng',
				),
				'photos' => array(
					array( 'about/khu-triet-long', 'Khu triệt lông với máy và giường điều trị' ),
				),
			),
		)
	);
	// Keep a row only when one of its photos can be shown (never a placeholder in production).
	$qd_tour = array();
	foreach ( $qd_rows as $qd_row ) {
		foreach ( (array) $qd_row['photos'] as list( $qd_key, $qd_alt ) ) {
			$qd_photo = qd_about_photo( $qd_key, $qd_alt, '4-5', qd_about_photo_attrs( $qd_key ), 'tour__frame' );
			if ( $qd_photo ) {
				$qd_row['photo'] = $qd_photo;
				$qd_tour[]       = $qd_row;
				break;
			}
		}
	}

	$qd_embed = qd_clinic( 'map_embed' );

	get_template_part( 'template-parts/about/hero' );
	?>

	<?php if ( $qd_tour ) : ?>
		<section class="section" aria-labelledby="facility-gallery">
			<div class="container">
				<?php
				qd_section_head(
					array(
						'id'    => 'facility-gallery',
						'title' => 'Không gian phòng khám',
						'desc'  => 'Mỗi khu vực được tách riêng để bạn thoải mái và riêng tư khi thăm khám.',
					)
				);
				?>
				<div class="tour">
					<?php foreach ( $qd_tour as $qd_row ) : ?>
						<article class="tour__row">
							<div class="tour__media"><?php echo $qd_row['photo']; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
							<div class="tour__body">
								<h3 class="tour__title"><?php echo esc_html( $qd_row['title'] ); ?></h3>
								<p><?php echo esc_html( $qd_row['text'] ); ?></p>
								<?php if ( ! empty( $qd_row['facts'] ) ) : ?>
									<dl class="facts">
										<?php foreach ( $qd_row['facts'] as $qd_label => $qd_value ) : ?>
											<div><dt><?php echo esc_html( $qd_label ); ?></dt><dd><?php echo esc_html( $qd_value ); ?></dd></div>
										<?php endforeach; ?>
									</dl>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section section--mint" aria-labelledby="getting-there">
		<div class="container visit">
			<div class="visit__info">
				<h2 class="section-title" id="getting-there">Đến phòng khám</h2>
				<ul class="visit__list">
					<li>
						<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'map-pin', array( 'size' => 20 ) ); ?></span>
						<div><p class="visit__label">Địa chỉ</p><p class="visit__value"><?php echo esc_html( qd_clinic( 'address' ) ); ?></p></div>
					</li>
					<li>
						<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'clock', array( 'size' => 20 ) ); ?></span>
						<div><p class="visit__label">Giờ làm việc</p><p class="visit__value"><?php echo esc_html( qd_clinic( 'hours' ) ); ?></p></div>
					</li>
					<li>
						<span class="icon-tile icon-tile--sm"><?php qd_the_icon( 'phone', array( 'size' => 20 ) ); ?></span>
						<div><p class="visit__label">Hotline</p><p class="visit__value"><a href="<?php echo esc_attr( qd_tel_href() ); ?>"><?php echo esc_html( qd_clinic( 'hotline' ) ); ?></a></p></div>
					</li>
				</ul>
				<p class="visit__tip"><?php qd_the_icon( 'info', array( 'size' => 18 ) ); ?><span>Phòng khám nằm ở tầng 5 của trung tâm thương mại. Đặt lịch trước để được ưu tiên và không phải chờ lâu.</span></p>
				<div class="btn-row">
					<?php
					echo qd_button( 'Chỉ đường bằng Google Maps', qd_clinic( 'map_url' ), array( 'variant' => 'outline', 'icon' => 'navigation', 'attrs' => array( 'target' => '_blank', 'rel' => 'noopener' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo qd_button( 'Gọi phòng khám', qd_tel_href(), array( 'variant' => 'ghost', 'icon' => 'phone' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
			</div>
			<div class="visit__map">
				<?php if ( $qd_embed ) : ?>
					<iframe src="<?php echo esc_url( $qd_embed ); ?>" title="Bản đồ đến Phòng khám Quang Đăng" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
				<?php else : ?>
					<a class="visit__map-fallback" href="<?php echo esc_url( qd_clinic( 'map_url' ) ); ?>" target="_blank" rel="noopener">
						<?php qd_the_icon( 'map-pin', array( 'size' => 36 ) ); ?>
						<span>Xem vị trí trên Google Maps</span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php $qd_content = qd_about_content(); ?>
	<?php if ( $qd_content ) : ?>
		<section class="section section--tight" aria-label="Chi tiết">
			<div class="container container--narrow">
				<div class="prose"><?php echo $qd_content; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content filter output. ?></div>
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
