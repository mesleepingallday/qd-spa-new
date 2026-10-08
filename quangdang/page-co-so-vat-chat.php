<?php
/**
 * About: Cơ sở vật chất — gallery of the real facility + address / getting-there block.
 * Photos are real-photo slots (about/*). In production the gallery only renders photos that
 * are real (never a wall of placeholders), following template-parts/home/results.php.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();

	// [asset key, caption, alt]
	$qd_shots = apply_filters(
		'qd_about_facility',
		array(
			array( 'about/le-tan', 'Quầy lễ tân – nơi bạn được đón tiếp và đăng ký', 'Quầy lễ tân Phòng khám Quang Đăng' ),
			array( 'about/phong-tu-van', 'Phòng tư vấn và soi da riêng tư', 'Phòng tư vấn và soi da' ),
			array( 'about/phong-dieu-tri', 'Phòng điều trị sạch sẽ, thoáng mát', 'Phòng điều trị' ),
			array( 'about/may-picosure', 'Khu đặt máy Laser Picosure', 'Máy Laser Picosure' ),
			array( 'about/may-double-cool', 'Khu đặt máy triệt lông Double Cool', 'Máy triệt lông Double Cool' ),
		)
	);
	$qd_shots = array_values(
		array_filter(
			$qd_shots,
			function ( $s ) {
				$img = qd_asset_image( $s[0] );
				return $img && ( ! $img['placeholder'] || qd_show_placeholders() );
			}
		)
	);

	$qd_embed = qd_clinic( 'map_embed' );

	get_template_part( 'template-parts/about/hero' );
	?>

	<?php if ( $qd_shots ) : ?>
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
				<ul class="gallery">
					<?php foreach ( $qd_shots as list( $qd_key, $qd_caption, $qd_alt ) ) : ?>
						<li class="gallery__item">
							<figure>
								<?php echo qd_about_photo( $qd_key, $qd_alt, '4-3', array(), 'gallery__frame' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<figcaption><?php echo esc_html( $qd_caption ); ?></figcaption>
							</figure>
						</li>
					<?php endforeach; ?>
				</ul>
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
