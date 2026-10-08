<?php
/**
 * Site header: announcement, utility bar, sticky header with mega menu, mobile drawer.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#0A727A">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Bỏ qua, đến nội dung chính</a>

<?php get_template_part( 'template-parts/site/announcement' ); ?>

<div class="utility">
	<div class="container utility__inner">
		<div class="utility__group">
			<a class="utility__item" href="<?php echo esc_url( qd_clinic( 'map_url' ) ); ?>" target="_blank" rel="noopener">
				<?php qd_the_icon( 'map-pin', array( 'size' => 15 ) ); ?><?php echo esc_html( qd_clinic( 'address_short' ) ); ?>
			</a>
			<span class="utility__item"><?php qd_the_icon( 'clock', array( 'size' => 15 ) ); ?><?php echo esc_html( qd_clinic( 'hours' ) ); ?></span>
		</div>
		<div class="utility__group">
			<a class="utility__item" href="<?php echo esc_url( qd_clinic( 'zalo' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'message-circle', array( 'size' => 15 ) ); ?>Zalo</a>
			<a class="utility__item" href="<?php echo esc_url( qd_clinic( 'facebook' ) ); ?>" target="_blank" rel="noopener"><?php qd_the_icon( 'facebook', array( 'size' => 15 ) ); ?>Facebook</a>
			<a class="utility__item" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>">Liên hệ</a>
		</div>
	</div>
</div>

<header class="site-header" data-header>
	<div class="container site-header__inner">
		<button class="icon-btn site-header__menu-btn" type="button" aria-controls="drawer" aria-expanded="false" data-drawer-open>
			<?php qd_the_icon( 'menu', array( 'size' => 24 ) ); ?><span class="sr-only">Mở menu</span>
		</button>

		<?php get_template_part( 'template-parts/site/brand' ); ?>

		<?php get_template_part( 'template-parts/site/nav-desktop' ); ?>

		<div class="site-header__actions">
			<button class="icon-btn" type="button" data-search-open aria-haspopup="dialog">
				<?php qd_the_icon( 'search', array( 'size' => 22 ) ); ?><span class="sr-only">Tìm kiếm</span>
			</button>
			<a class="site-header__hotline" href="<?php echo esc_attr( qd_tel_href() ); ?>">
				<?php qd_the_icon( 'phone', array( 'size' => 18 ) ); ?><?php echo esc_html( qd_clinic( 'hotline' ) ); ?>
			</a>
			<?php
			echo qd_button( // phpcs:ignore WordPress.Security.EscapeOutput
				'Đặt lịch',
				qd_booking_url( is_singular( 'dich-vu' ) && ! qd_is_service_category() ? get_the_ID() : null ),
				array(
					'class' => 'site-header__book',
					'icon'  => 'calendar-days',
				)
			);
			?>
		</div>
	</div>
</header>

<?php get_template_part( 'template-parts/site/drawer' ); ?>
<?php get_template_part( 'template-parts/site/search-dialog' ); ?>

<main id="main" class="site-main" tabindex="-1">
