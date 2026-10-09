<?php
/**
 * One editorial service feature: a category with its cover, a short introduction and its first three
 * services (name, sessions, price). It shows what the clinic does in detail instead of repeating the
 * directory above. Colour = the concern the category belongs to, when it has one.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_feature = qd_home_feature();
if ( ! $qd_feature ) {
	return;
}
$qd_cat      = $qd_feature['category'];
$qd_facts    = qd_service_facts( $qd_cat );
$qd_concern  = qd_concern_for_post( $qd_cat );
?>
<section class="section section--mist" aria-labelledby="home-feature">
	<div class="container">
		<?php
		qd_section_head(
			array(
				'id'         => 'home-feature',
				'title'      => 'Dịch vụ tiêu biểu',
				'link_label' => 'Tất cả dịch vụ',
				'link_url'   => get_post_type_archive_link( 'dich-vu' ),
			)
		);
		?>
		<article class="feature"<?php echo $qd_concern ? ' data-concern="' . esc_attr( $qd_concern ) . '"' : ''; ?>>
			<a class="feature__media" href="<?php echo esc_url( get_permalink( $qd_cat ) ); ?>" tabindex="-1" aria-hidden="true">
				<?php echo qd_thumb( $qd_cat, 'qd-wide', '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<div class="feature__body">
				<h3 class="feature__title"><a href="<?php echo esc_url( get_permalink( $qd_cat ) ); ?>"><?php echo esc_html( get_the_title( $qd_cat ) ); ?></a></h3>
				<?php if ( has_excerpt( $qd_cat ) ) : ?>
					<p class="feature__text"><?php echo esc_html( get_the_excerpt( $qd_cat ) ); ?></p>
				<?php endif; ?>
				<ul class="service-rows">
					<?php foreach ( $qd_feature['services'] as $qd_service ) : ?>
						<?php $qd_sf = qd_service_facts( $qd_service ); ?>
						<li>
							<a class="service-row" href="<?php echo esc_url( get_permalink( $qd_service ) ); ?>">
								<span class="service-row__main">
									<span class="service-row__name"><?php echo esc_html( get_the_title( $qd_service ) ); ?></span>
									<?php if ( $qd_sf['sessions'] || $qd_sf['duration'] ) : ?>
										<span class="service-row__meta"><?php echo esc_html( implode( ' · ', array_filter( array( $qd_sf['sessions'], $qd_sf['duration'] ) ) ) ); ?></span>
									<?php endif; ?>
								</span>
								<?php if ( $qd_sf['price_from'] ) : ?>
									<span class="price"><small>Từ</small> <?php echo esc_html( qd_price( $qd_sf['price_from'] ) ); ?></span>
								<?php endif; ?>
								<?php qd_the_icon( 'chevron-right', array( 'size' => 18 ) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<a class="link-more feature__more" href="<?php echo esc_url( get_permalink( $qd_cat ) ); ?>">
					Xem <?php echo esc_html( mb_strtolower( get_the_title( $qd_cat ) ) ); ?>
					<?php if ( $qd_facts['count'] ) : ?>
						<span class="feature__count">(<?php echo (int) $qd_facts['count']; ?> dịch vụ)</span>
					<?php endif; ?>
					<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?>
				</a>
			</div>
		</article>
	</div>
</section>
