<?php
/**
 * "Chưa biết chọn dịch vụ nào?" — entry point to the skin quiz (onboarding).
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="help-card">
	<span class="help-card__icon"><?php qd_the_icon( 'scan-face', array( 'size' => 22 ) ); ?></span>
	<p class="help-card__title">Chưa biết chọn dịch vụ nào?</p>
	<p class="help-card__text">Trả lời 4 câu hỏi ngắn, nhận gợi ý liệu trình phù hợp với làn da của bạn.</p>
	<a class="link-more" href="<?php echo esc_url( home_url( '/tim-lieu-trinh/' ) ); ?>">Tìm dịch vụ phù hợp<?php qd_the_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
</div>
