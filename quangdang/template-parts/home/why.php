<?php
/**
 * Why Quang Đăng — 4 pillars, each one links to the About page that proves it.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_pillars = array(
	array( 'stethoscope', 'Bác sĩ chuyên khoa', 'Bác sĩ da liễu trực tiếp thăm khám, chỉ định và theo dõi suốt quá trình điều trị.', '/gioi-thieu/doi-ngu-bac-si/' ),
	array( 'zap', 'Công nghệ chính hãng', 'Laser Picosure, Laser Double Cool… thiết bị có giấy tờ nhập khẩu rõ ràng.', '/gioi-thieu/cong-nghe-san-pham/' ),
	array( 'clipboard-list', 'Quy trình chuẩn y khoa', 'Vô khuẩn, dụng cụ dùng một lần, hồ sơ theo dõi sau từng buổi.', '/gioi-thieu/quy-trinh-chuan-y-khoa/' ),
	array( 'building-2', 'Cơ sở vật chất', 'Phòng điều trị riêng tư tại Tầng 5, TTTM Đức Tài – Tâm Đạt.', '/gioi-thieu/co-so-vat-chat/' ),
);
?>
<section class="section section--ivory" aria-labelledby="home-why">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'    => 'home-why',
				'title' => 'Vì sao chọn Quang Đăng',
				'desc'  => 'Phòng khám chuyên khoa, không phải spa: mọi liệu trình đều bắt đầu từ thăm khám của bác sĩ.',
			)
		);
		?>
		<ul class="grid grid--4 pillars">
			<?php foreach ( $qd_pillars as list( $qd_icon, $qd_title, $qd_text, $qd_path ) ) : ?>
				<li class="pillar">
					<span class="icon-tile"><?php qd_the_icon( $qd_icon, array( 'size' => 24 ) ); ?></span>
					<h3 class="pillar__title"><a href="<?php echo esc_url( home_url( $qd_path ) ); ?>"><?php echo esc_html( $qd_title ); ?></a></h3>
					<p class="pillar__text"><?php echo esc_html( $qd_text ); ?></p>
					<span class="pillar__more" aria-hidden="true">Tìm hiểu<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
