<?php
/**
 * Demo articles, promotions, doctors, courses and pages.
 * Doctor names, promotion terms and course fees are PLACEHOLDERS — replace with real info.
 *
 * @package QuangDang
 */

$long_acne = <<<'HTML'
<p>Mụn ẩn là những nốt nhỏ nằm dưới da, sờ thấy lợn cợn nhưng nhìn không rõ. Đây là loại mụn hay gặp ở sinh viên và người làm văn phòng, đặc biệt khi da tiết nhiều dầu hoặc trang điểm thường xuyên.</p>
<h2>Mụn ẩn hình thành như thế nào?</h2>
<p>Khi bã nhờn, tế bào chết và cặn trang điểm bít tắc lỗ chân lông nhưng chưa bị oxy hóa, chúng tạo thành nhân mụn kín nằm dưới bề mặt da. Nếu để lâu, vi khuẩn phát triển và mụn ẩn chuyển thành mụn viêm.</p>
<ul>
<li>Làm sạch da chưa kỹ, nhất là sau khi trang điểm.</li>
<li>Dùng kem dưỡng quá dày, không hợp loại da.</li>
<li>Thay đổi nội tiết, thức khuya, căng thẳng.</li>
</ul>
<h2>Có nên tự nặn mụn ẩn tại nhà?</h2>
<p>Không nên. Nhân mụn ẩn nằm sâu, tự nặn dễ làm vỡ nang lông, đẩy vi khuẩn xuống sâu hơn và để lại thâm, sẹo rỗ. Lấy nhân cần dụng cụ vô khuẩn và kỹ thuật đúng.</p>
<blockquote><p>Mẹo nhỏ: nếu bạn thấy mụn ẩn tập trung ở trán và cằm, hãy kiểm tra lại sản phẩm làm sạch và thói quen ngủ trước khi đổi kem trị mụn.</p></blockquote>
<h2>Chăm sóc da có mụn ẩn đúng cách</h2>
<ol>
<li>Tẩy trang và rửa mặt hai bước vào buổi tối.</li>
<li>Dùng BHA nồng độ thấp 2–3 lần mỗi tuần để làm thông thoáng lỗ chân lông.</li>
<li>Chọn kem dưỡng mỏng nhẹ, ghi "non-comedogenic".</li>
<li>Chống nắng mỗi sáng, kể cả khi ở trong nhà.</li>
</ol>
<h2>Khi nào cần gặp bác sĩ da liễu?</h2>
<p>Nếu mụn ẩn kéo dài trên 4 tuần, lan rộng hoặc bắt đầu sưng viêm, bạn nên đi khám để được soi da, phân loại mụn và chỉ định phác đồ phù hợp. Điều trị sớm giúp hạn chế thâm và sẹo về sau.</p>
HTML;

$long_laser = <<<'HTML'
<p>Triệt lông bằng laser là cách giảm lông lâu dài được nhiều người lựa chọn thay cho cạo, nhổ hay waxing. Dưới đây là những câu hỏi khách hàng thường đặt ra nhất trước buổi đầu tiên.</p>
<h2>1. Triệt lông laser có đau không?</h2>
<p>Cảm giác giống như búng nhẹ dây thun lên da. Công nghệ làm mát kép giữ bề mặt da mát liên tục nên hầu hết khách hàng không thấy nóng rát.</p>
<h2>2. Cần bao nhiêu buổi?</h2>
<p>Lông mọc theo chu kỳ, laser chỉ tác động hiệu quả lên lông đang ở giai đoạn phát triển. Vì vậy cần trung bình 6–8 buổi, mỗi buổi cách nhau 4–6 tuần.</p>
<h2>3. Trước khi triệt cần chuẩn bị gì?</h2>
<ul>
<li>Cạo sạch lông 1 ngày trước buổi triệt, không nhổ hay waxing.</li>
<li>Không tắm nắng, không dùng sản phẩm lột tẩy 1 tuần trước đó.</li>
</ul>
<h2>4. Sau khi triệt cần lưu ý gì?</h2>
<p>Da có thể hơi ửng đỏ vài giờ. Tránh xông hơi, bơi hồ có clo trong 48 giờ và chống nắng kỹ vùng đã triệt.</p>
HTML;

$short = function ( $topic ) {
	return '<p>' . $topic . ' là chủ đề được nhiều khách hàng quan tâm khi đến thăm khám tại phòng khám.</p><h2>Nguyên nhân thường gặp</h2><p>Mỗi người có cơ địa và thói quen sinh hoạt khác nhau, vì vậy cần bác sĩ thăm khám để xác định đúng nguyên nhân trước khi điều trị.</p><h2>Hướng xử lý</h2><ul><li>Thăm khám, soi da để phân loại tình trạng.</li><li>Lên phác đồ phù hợp với cơ địa.</li><li>Chăm sóc tại nhà đúng hướng dẫn và tái khám đúng hẹn.</li></ul>';
};

return array(
	'doctors' => array(
		array(
			'title'     => 'BS. CKI Họ Và Tên',
			'excerpt'   => 'Hơn 10 năm kinh nghiệm điều trị mụn, nám và các bệnh lý da liễu thẩm mỹ.',
			'content'   => '<p>Bác sĩ phụ trách chuyên môn, trực tiếp thăm khám và xây dựng phác đồ điều trị cho khách hàng. (Nội dung mẫu – thay bằng tiểu sử thật.)</p>',
			'meta'      => array(
				'_qd_doctor_title'     => 'Bác sĩ Chuyên khoa I Da liễu',
				'_qd_doctor_role'      => 'Giám đốc chuyên môn',
				'_qd_doctor_years'     => 10,
				'_qd_doctor_education' => "Tốt nghiệp Bác sĩ Đa khoa – Đại học Y (cần điền)\nChuyên khoa I Da liễu (cần điền)\nChứng chỉ Laser thẩm mỹ (cần điền)",
			),
		),
		array(
			'title'   => 'ThS. BS. Họ Và Tên',
			'excerpt' => 'Chuyên sâu nội khoa thẩm mỹ: filler, botox, căng chỉ.',
			'content' => '<p>Nội dung mẫu – thay bằng tiểu sử thật.</p>',
			'meta'    => array(
				'_qd_doctor_title' => 'Thạc sĩ – Bác sĩ Da liễu',
				'_qd_doctor_role'  => 'Bác sĩ điều trị',
				'_qd_doctor_years' => 7,
			),
		),
	),
	'posts'   => array(
		array( 'Mụn ẩn là gì? Nguyên nhân và cách xử lý đúng cách', 'cam-nang-lam-dep', 'dieu-tri-mun', 'Mụn ẩn nằm dưới da, dễ chuyển thành mụn viêm nếu xử lý sai. Hiểu đúng nguyên nhân để chăm sóc đúng cách.', $long_acne, '-2 days' ),
		array( 'Triệt lông bằng laser có đau không? 4 câu hỏi thường gặp', 'cam-nang-lam-dep', 'cham-soc-da', 'Giải đáp những thắc mắc phổ biến trước buổi triệt lông đầu tiên: cảm giác, số buổi, chuẩn bị và chăm sóc sau triệt.', $long_laser, '-5 days' ),
		array( 'Nám da: phân biệt nám mảng, nám chân sâu và tàn nhang', 'cam-nang-lam-dep', 'dieu-tri-nam', 'Ba tình trạng sắc tố thường bị nhầm lẫn và vì sao mỗi loại cần cách điều trị khác nhau.', $short( 'Nám và tàn nhang' ), '-9 days' ),
		array( 'Quy trình chăm sóc da cơ bản 4 bước cho người mới bắt đầu', 'cam-nang-lam-dep', 'cham-soc-da', 'Làm sạch, cân bằng, dưỡng ẩm, chống nắng – bốn bước đơn giản cho làn da khỏe mỗi ngày.', $short( 'Chăm sóc da cơ bản' ), '-14 days' ),
		array( 'Thâm nách: vì sao bị và điều trị bao lâu thì cải thiện?', 'cam-nang-lam-dep', 'dieu-tri-tham', 'Cạo, nhổ lông và ma sát là thủ phạm chính gây thâm nách. Cách cải thiện an toàn cho vùng da mỏng.', $short( 'Thâm nách' ), '-20 days' ),
		array( 'Sẹo rỗ sau mụn có trị dứt điểm được không?', 'cam-nang-lam-dep', 'dieu-tri-seo', 'Sẹo rỗ có thể cải thiện rõ rệt với phác đồ phù hợp. Những điều cần biết trước khi điều trị.', $short( 'Sẹo rỗ sau mụn' ), '-27 days' ),
		array( 'Tiêm filler cần lưu ý gì để an toàn? Hướng dẫn chi tiết từ bác sĩ da liễu cho người lần đầu làm đẹp không phẫu thuật', 'cam-nang-lam-dep', 'noi-khoa-tham-my', 'Kiểm tra sản phẩm, người thực hiện và cơ sở được cấp phép – ba điều bắt buộc trước khi tiêm filler.', $short( 'Tiêm filler an toàn' ), '-35 days' ),
	),
	'promos'  => array(
		array( 'Ưu đãi tháng 10: Triệt lông nách chỉ 99.000đ/buổi', 'Giảm 50%', '2026-10-01', '2026-10-31', 'Áp dụng cho khách hàng đặt lịch trước, triệt lông vùng nách công nghệ Laser Double Cool.', '<p>Chương trình áp dụng cho khách hàng đặt lịch trước qua website, hotline hoặc Zalo. Mỗi khách hàng áp dụng 1 lần. (Điều kiện mẫu – thay bằng điều kiện thật.)</p>', '-3 days', 'cham-soc-da' ),
		array( 'Tri ân sinh viên: giảm 20% liệu trình trị mụn', 'Sinh viên -20%', '2026-10-15', '2026-11-30', 'Xuất trình thẻ sinh viên để được giảm 20% liệu trình trị mụn kháng khuẩn đa tầng.', '<p>Áp dụng cho sinh viên có thẻ còn hạn. Không cộng dồn với ưu đãi khác. (Điều kiện mẫu.)</p>', '-1 days', 'dieu-tri-mun' ),
		array( 'Thông báo: Phòng khám làm việc xuyên lễ 2/9', 'Lễ 2/9', '2026-08-28', '2026-09-03', 'Phòng khám vẫn hoạt động và phục vụ khách hàng trong dịp Quốc khánh 2/9.', '<p>Đội ngũ bác sĩ, chuyên viên luôn sẵn sàng phục vụ. Đặt lịch trước để được ưu tiên.</p>', '-40 days', '' ),
	),
	'courses' => array(
		array( 'khoa-hoc-cham-soc-da-co-ban', 'Khóa học chăm sóc da cơ bản', 'Nền tảng kiến thức về da và quy trình chăm sóc chuẩn cho người mới vào nghề.', 4500000, '4 tuần (12 buổi)', 'Học trực tiếp tại phòng khám', 'Tối thứ 2 – 4 – 6, khai giảng hằng tháng', "Cấu trúc da và các loại da | Nhận biết loại da, tình trạng da thường gặp\nQuy trình chăm sóc chuẩn | Làm sạch, tẩy da chết, đắp mặt nạ, massage\nSản phẩm và hoạt chất | Đọc bảng thành phần, chọn sản phẩm an toàn\nThực hành trên mẫu | Thực hành có giảng viên kèm" ),
		array( 'khoa-hoc-cham-soc-da-chuyen-sau', 'Khóa học chăm sóc da chuyên sâu', 'Dành cho kỹ thuật viên, chủ spa muốn nâng cao tay nghề với thiết bị công nghệ cao.', 12000000, '8 tuần (24 buổi)', 'Lý thuyết + thực hành thiết bị', 'Thứ 7 – Chủ nhật', "Da bệnh lý thường gặp | Mụn, nám, thâm – khi nào cần chuyển bác sĩ\nThiết bị công nghệ cao | Nguyên lý và vận hành an toàn\nXây dựng liệu trình | Thiết kế liệu trình theo tình trạng da\nTư vấn khách hàng | Kỹ năng tư vấn, chăm sóc sau dịch vụ" ),
	),
	'about'   => array(
		'qua-trinh-hinh-thanh'   => array( 'Quá trình hình thành', 'Chặng đường phát triển của Phòng khám Da liễu Thẩm mỹ Quang Đăng.' ),
		'tam-nhin-su-menh'       => array( 'Tầm nhìn & Sứ mệnh', 'Điều chúng tôi cam kết với mỗi khách hàng.' ),
		'doi-ngu-bac-si'         => array( 'Về đội ngũ chuyên gia bác sĩ', 'Bác sĩ da liễu trực tiếp thăm khám và điều trị.' ),
		'cong-nghe-san-pham'     => array( 'Về Công nghệ & Sản phẩm', 'Thiết bị và dược mỹ phẩm chính hãng, có nguồn gốc rõ ràng.' ),
		'co-so-vat-chat'         => array( 'Về Cơ sở vật chất', 'Không gian phòng khám tại Tầng 5, TTTM Đức Tài – Tâm Đạt, Quỳnh Lưu.' ),
		'quy-trinh-chuan-y-khoa' => array( 'Về Quy trình chuẩn y khoa', 'Từ thăm khám, soi da đến điều trị và tái khám.' ),
	),
);
