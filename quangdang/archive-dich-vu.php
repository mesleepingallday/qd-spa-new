<?php
/**
 * Services hub (/dich-vu/).
 *
 * Job: "see everything the clinic does, grouped by problem".
 * Hero → quick-jump chips → zone "Chăm sóc và điều trị da" (Điều trị da categories + Chăm sóc da services)
 * → quiz help card → Nội khoa thẩm mỹ → booking strip.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

get_header();

$qd_groups = qd_service_groups();
$qd_treat  = qd_service_categories( 'dieu-tri-da' );
$qd_flat   = function ( array $cats ) {
	$out = array();
	foreach ( $cats as $cat ) {
		$out = array_merge( $out, qd_service_children( $cat->ID ) );
	}
	return $out;
};
$qd_care_services    = $qd_flat( qd_service_categories( 'cham-soc-da' ) );
$qd_medical_services = $qd_flat( qd_service_categories( 'noi-khoa-tham-my' ) );

$qd_chips = array();
if ( $qd_treat || $qd_care_services ) {
	$qd_chips['cham-soc-va-dieu-tri-da'] = 'Chăm sóc và điều trị da';
}
if ( $qd_treat ) {
	$qd_chips['dieu-tri-da'] = 'Điều trị da';
}
if ( $qd_care_services ) {
	$qd_chips['cham-soc-da'] = 'Chăm sóc da';
}
if ( $qd_medical_services ) {
	$qd_chips['noi-khoa-tham-my'] = 'Nội khoa thẩm mỹ';
}
?>
<section class="page-hero">
	<div class="container">
		<?php qd_breadcrumbs(); ?>
		<h1 class="page-title">Dịch vụ</h1>
		<p class="lead">Từ điều trị mụn, nám, sẹo đến chăm sóc da và nội khoa thẩm mỹ. Chọn theo vấn đề bạn đang gặp, bác sĩ sẽ tư vấn phác đồ phù hợp.</p>
		<?php if ( $qd_chips ) : ?>
			<nav class="hub-jump" aria-label="Đi nhanh tới nhóm dịch vụ">
				<ul class="chips">
					<?php foreach ( $qd_chips as $qd_anchor => $qd_label ) : ?>
						<li><a class="chip" href="#<?php echo esc_attr( $qd_anchor ); ?>"><?php echo esc_html( $qd_label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
</section>

<?php if ( $qd_treat || $qd_care_services ) : ?>
	<section id="cham-soc-va-dieu-tri-da" class="section hub-zone" aria-labelledby="zone-1-title">
		<div class="container">
			<h2 class="hub-zone__title" id="zone-1-title">Chăm sóc và điều trị da</h2>

			<?php if ( $qd_treat ) : ?>
				<div id="dieu-tri-da" class="hub-group">
					<?php
					qd_section_head(
						array(
							'id'    => 'dieu-tri-da-title',
							'tag'   => 'h3',
							'title' => $qd_groups['dieu-tri-da']['title'],
							'desc'  => $qd_groups['dieu-tri-da']['desc'],
						)
					);
					?>
					<div class="grid grid--3 hub-cats">
						<?php foreach ( $qd_treat as $qd_cat ) : ?>
							<?php
							$qd_facts    = qd_service_facts( $qd_cat );
							$qd_children = qd_service_children( $qd_cat->ID );
							?>
							<article class="card hub-cat">
								<?php echo qd_concern_mark( qd_concern_for_post( $qd_cat ), array( 'size' => 'sm', 'class' => 'hub-cat__mark' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<a class="hub-cat__media" href="<?php echo esc_url( get_permalink( $qd_cat ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo qd_thumb( $qd_cat, 'qd-card', '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<div class="card__body">
									<h4 class="card__title"><a href="<?php echo esc_url( get_permalink( $qd_cat ) ); ?>"><?php echo esc_html( get_the_title( $qd_cat ) ); ?></a></h4>
									<p class="hub-cat__meta">
										<?php if ( $qd_facts['count'] ) : ?>
											<span><?php echo (int) $qd_facts['count']; ?> dịch vụ</span>
										<?php endif; ?>
										<?php if ( $qd_facts['price_from'] ) : ?>
											<span class="price"><small>Từ</small> <?php echo esc_html( qd_price( $qd_facts['price_from'] ) ); ?></span>
										<?php endif; ?>
									</p>
									<?php if ( $qd_children ) : ?>
										<ul class="hub-cat__list">
											<?php foreach ( $qd_children as $qd_child ) : ?>
												<li><a href="<?php echo esc_url( get_permalink( $qd_child ) ); ?>"><?php echo esc_html( get_the_title( $qd_child ) ); ?></a></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $qd_care_services ) : ?>
				<div id="cham-soc-da" class="hub-group">
					<?php
					qd_section_head(
						array(
							'id'    => 'cham-soc-da-title',
							'tag'   => 'h3',
							'title' => $qd_groups['cham-soc-da']['title'],
							'desc'  => $qd_groups['cham-soc-da']['desc'],
						)
					);
					?>
					<div class="grid grid--3">
						<?php foreach ( $qd_care_services as $qd_service ) : ?>
							<?php get_template_part( 'template-parts/components/service-card', null, array( 'post' => $qd_service, 'heading' => 'h4' ) ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<section class="section section--tight" aria-label="Gợi ý chọn dịch vụ">
	<div class="container">
		<div class="hub-help"><?php get_template_part( 'template-parts/components/help-card' ); ?></div>
	</div>
</section>

<?php if ( $qd_medical_services ) : ?>
	<section id="noi-khoa-tham-my" class="section section--mint" aria-labelledby="noi-khoa-title">
		<div class="container">
			<?php
			qd_section_head(
				array(
					'id'    => 'noi-khoa-title',
					'title' => $qd_groups['noi-khoa-tham-my']['title'],
					'desc'  => $qd_groups['noi-khoa-tham-my']['desc'],
				)
			);
			?>
			<div class="grid grid--3">
				<?php foreach ( $qd_medical_services as $qd_service ) : ?>
					<?php get_template_part( 'template-parts/components/service-card', null, array( 'post' => $qd_service ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section">
	<div class="container">
		<?php get_template_part( 'template-parts/components/booking-strip', null, array( 'source' => 'services-hub' ) ); ?>
	</div>
</section>
<?php
get_footer();
