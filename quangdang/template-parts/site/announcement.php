<?php
/**
 * Dismissible announcement bar (text from Customize → Thông tin phòng khám).
 * Dismissal is remembered per message, so a new message shows again.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

$qd_text = qd_clinic( 'announcement' );
if ( ! $qd_text ) {
	return;
}
$qd_url = qd_clinic( 'announce_url' );
?>
<div class="announce" data-announce="<?php echo esc_attr( substr( md5( $qd_text ), 0, 8 ) ); ?>">
	<div class="container announce__inner">
		<?php qd_the_icon( 'sparkles', array( 'size' => 16 ) ); ?>
		<p>
			<?php echo esc_html( $qd_text ); ?>
			<?php if ( $qd_url ) : ?>
				<a href="<?php echo esc_url( 0 === strpos( $qd_url, '/' ) ? home_url( $qd_url ) : $qd_url ); ?>">Đặt lịch ngay</a>
			<?php endif; ?>
		</p>
		<button class="announce__close" type="button" data-announce-close>
			<?php qd_the_icon( 'x', array( 'size' => 18 ) ); ?><span class="sr-only">Đóng thông báo</span>
		</button>
	</div>
</div>
<script>(function(a){try{if(localStorage.getItem('qd-announce')===a.dataset.announce){a.hidden=true}}catch(e){}})(document.currentScript.previousElementSibling);</script>
