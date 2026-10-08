<?php
/**
 * Search dialog (native <dialog>). Popular searches are the 8 concern shortcuts.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;
?>
<dialog class="search-dialog" data-search-dialog aria-label="Tìm kiếm">
	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="sr-only" for="dialog-search">Tìm kiếm</label>
		<input class="input" id="dialog-search" type="search" name="s" placeholder="Bạn muốn tìm gì? VD: trị mụn, nám, triệt lông…" autocomplete="off" required>
		<button class="btn btn--primary btn--sm" type="submit"><?php qd_the_icon( 'search', array( 'size' => 18 ) ); ?><span>Tìm</span></button>
		<button class="icon-btn" type="button" data-search-close><?php qd_the_icon( 'x', array( 'size' => 22 ) ); ?><span class="sr-only">Đóng</span></button>
	</form>
	<div class="search-dialog__hints">
		<p>Tìm nhiều</p>
		<div class="chips">
			<?php foreach ( qd_concerns() as $qd_concern ) : ?>
				<a class="chip" href="<?php echo esc_url( $qd_concern['url'] ); ?>"><?php echo esc_html( $qd_concern['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</dialog>
