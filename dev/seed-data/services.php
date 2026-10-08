<?php
/**
 * Demo service content. Structure (titles, slugs, groups) comes from qd_service_catalog()
 * in the theme; this file only adds sample copy and facts.
 * ALL PRICES, SESSIONS AND DURATIONS ARE SAMPLES — replace with the clinic's real numbers.
 *
 * @package QuangDang
 */

// Category-level copy + defaults shared by its services.
$categories = array(
	'dieu-tri-mun'     => array(
		'excerpt' => 'Phác đồ trị mụn theo từng loại mụn và cơ địa, kết hợp lấy nhân chuẩn y khoa và công nghệ kháng khuẩn.',
		'content' => '<h2>Hiểu đúng về mụn trước khi điều trị</h2><p>Mụn hình thành khi lỗ chân lông bị bít tắc bởi bã nhờn và tế bào chết, kết hợp với vi khuẩn <em>C. acnes</em> gây viêm. Mỗi loại mụn – mụn ẩn, mụn đầu đen, mụn viêm, mụn bọc – cần cách xử lý khác nhau.</p><h2>Vì sao tự nặn mụn khiến tình trạng nặng hơn?</h2><p>Nặn sai cách đẩy vi khuẩn xuống sâu hơn, làm vỡ nang lông và để lại thâm, sẹo rỗ. Tại phòng khám, bác sĩ soi da, phân loại mụn rồi mới chỉ định lấy nhân, điện di hay ánh sáng sinh học.</p><ul><li>Mụn ẩn, mụn đầu đen: làm sạch sâu, lấy nhân, kiểm soát dầu.</li><li>Mụn viêm, mụn mủ: kháng khuẩn đa tầng, giảm viêm, phục hồi hàng rào da.</li><li>Mụn bọc, mụn nang: phối hợp thuốc theo đơn bác sĩ.</li></ul>',
		'faq'     => "Trị mụn mất bao lâu thì thấy hiệu quả? | Thường sau 2–3 buổi da giảm viêm rõ; liệu trình đầy đủ 6–10 buổi tùy mức độ.\nCó phải kiêng ăn uống gì không? | Hạn chế đồ ngọt, sữa và đồ cay nóng trong thời gian điều trị; bác sĩ sẽ dặn cụ thể theo cơ địa.\nMụn có tái phát không? | Mụn có thể tái phát nếu chăm sóc sai cách. Phòng khám hướng dẫn quy trình chăm sóc tại nhà và tái khám miễn phí.",
		'steps'   => "Thăm khám, soi da | Bác sĩ xác định loại mụn, mức độ viêm và nguyên nhân.\nLàm sạch sâu | Tẩy trang, rửa mặt, làm mềm nhân mụn.\nLấy nhân chuẩn y khoa | Dụng cụ vô khuẩn, hạn chế tổn thương nang lông.\nKháng khuẩn – giảm viêm | Ánh sáng sinh học và tinh chất kháng khuẩn theo phác đồ.\nPhục hồi, dặn dò | Đắp mặt nạ phục hồi, hướng dẫn chăm sóc tại nhà.",
		'after'   => "Không tự nặn mụn trong thời gian điều trị.\nDùng sữa rửa mặt dịu nhẹ, tránh sản phẩm chứa cồn.\nChống nắng hằng ngày, kể cả khi ở trong nhà.\nTái khám đúng hẹn để bác sĩ điều chỉnh phác đồ.",
	),
	'dieu-tri-tham'    => array(
		'excerpt' => 'Làm mờ thâm vùng nách, mắt, bẹn và vùng kín bằng phác đồ an toàn cho vùng da mỏng, nhạy cảm.',
		'content' => '<h2>Thâm da do đâu?</h2><p>Thâm là tình trạng tăng sắc tố do ma sát, viêm, cạo – nhổ lông hoặc nội tiết. Vùng da mỏng như nách, bẹn, vùng kín cần sản phẩm và năng lượng thiết bị phù hợp để không gây kích ứng.</p><h2>Điều trị thâm tại Quang Đăng</h2><p>Bác sĩ đánh giá nguyên nhân, mức độ thâm rồi kết hợp hoạt chất làm sáng an toàn với công nghệ ánh sáng phù hợp từng vùng.</p>',
		'faq'     => "Trị thâm vùng kín có đau không? | Không đau; có thể hơi ấm nhẹ. Quy trình riêng tư, do nhân viên nữ thực hiện.\nBao lâu thì thâm mờ? | Thường thấy sáng màu sau 3–4 buổi; cần 6–8 buổi để ổn định.\nCó bị thâm lại không? | Hạn chế ma sát, cạo lông và chống nắng giúp duy trì kết quả lâu dài.",
		'steps'   => "Thăm khám | Xác định nguyên nhân và mức độ thâm.\nLàm sạch, ủ dịu | Chuẩn bị vùng da mỏng trước điều trị.\nĐiều trị | Hoạt chất làm sáng kết hợp ánh sáng phù hợp từng vùng.\nPhục hồi | Dưỡng ẩm, làm dịu và dặn dò chăm sóc.",
		'after'   => "Mặc quần áo rộng, thoáng sau buổi điều trị.\nTránh cạo, nhổ lông vùng điều trị.\nDưỡng ẩm theo hướng dẫn của bác sĩ.",
	),
	'dieu-tri-nam'     => array(
		'excerpt' => 'Kiểm soát nám và tàn nhang từ gốc: phân loại nám, phối hợp laser, thuốc và chăm sóc chống nắng.',
		'content' => '<h2>Nám là bệnh lý mạn tính</h2><p>Nám chịu ảnh hưởng của nội tiết, ánh nắng và di truyền nên không có cách "xóa vĩnh viễn". Mục tiêu điều trị là làm mờ rõ rệt và kiểm soát để nám không đậm lại.</p><h2>Phân loại trước khi điều trị</h2><ul><li>Nám mảng (thượng bì): đáp ứng tốt, cải thiện nhanh.</li><li>Nám chân sâu (trung bì): cần phác đồ kết hợp, thời gian dài hơn.</li><li>Tàn nhang: đốm nhỏ, xử lý tốt bằng laser.</li></ul>',
		'faq'     => "Nám có trị dứt điểm được không? | Nám là bệnh mạn tính; điều trị giúp làm mờ rõ rệt và kiểm soát lâu dài nếu chăm sóc đúng.\nLaser trị nám có làm mỏng da không? | Khi dùng đúng chỉ định và năng lượng, laser không làm mỏng da.\nBầu sau sinh có điều trị được không? | Cần bác sĩ thăm khám; một số phương pháp chỉ áp dụng sau khi ngưng cho con bú.",
		'steps'   => "Soi da phân loại nám | Xác định nám mảng, nám sâu hay hỗn hợp.\nLàm sạch, ủ tê (nếu cần) | Chuẩn bị da trước laser.\nLaser / điện di hoạt chất | Năng lượng phù hợp từng loại nám.\nLàm dịu, phục hồi | Mặt nạ phục hồi, kem chống nắng phổ rộng.\nTheo dõi | Tái khám, điều chỉnh phác đồ theo đáp ứng.",
		'after'   => "Chống nắng phổ rộng SPF 50, thoa lại sau 2–3 giờ.\nTránh xông hơi, tắm nắng 1 tuần sau laser.\nKhông dùng sản phẩm lột tẩy khi chưa có chỉ định.",
	),
	'dieu-tri-seo'     => array(
		'excerpt' => 'Cải thiện sẹo lồi, sẹo lõm sau mụn và sau chấn thương bằng phác đồ phối hợp theo từng loại sẹo.',
		'content' => '<h2>Sẹo lồi và sẹo lõm khác nhau thế nào?</h2><p>Sẹo lồi do tăng sinh mô xơ quá mức; sẹo lõm (sẹo rỗ) do mất mô sau viêm. Hai loại cần cách điều trị ngược nhau, vì vậy bác sĩ phải thăm khám trước khi chỉ định.</p>',
		'faq'     => "Sẹo lâu năm có cải thiện được không? | Có thể cải thiện rõ rệt; mức độ phụ thuộc loại sẹo và cơ địa.\nĐiều trị sẹo có cần nghỉ dưỡng? | Tùy phương pháp, thường da đỏ nhẹ 1–3 ngày.",
		'steps'   => "Thăm khám | Phân loại sẹo, đánh giá độ sâu.\nỦ tê | Giảm khó chịu trong quá trình điều trị.\nĐiều trị | Phương pháp theo loại sẹo.\nPhục hồi | Tái tạo da, dặn dò chăm sóc.",
		'after'   => "Không cạy vảy, để da bong tự nhiên.\nChống nắng kỹ để tránh thâm sau điều trị.",
	),
	'xoa-xam'          => array(
		'excerpt' => 'Xóa xăm lông mày, mí mắt và hình xăm bằng laser, hạn chế tối đa tổn thương và sẹo.',
		'content' => '<h2>Xóa xăm bằng laser hoạt động thế nào?</h2><p>Năng lượng laser phá vỡ hạt mực thành mảnh nhỏ để cơ thể đào thải dần. Số buổi phụ thuộc màu mực, độ sâu và diện tích hình xăm.</p>',
		'faq'     => "Xóa xăm có để lại sẹo không? | Khi dùng đúng năng lượng và chăm sóc đúng, nguy cơ sẹo rất thấp.\nCần bao nhiêu buổi? | Lông mày, mí mắt thường 2–4 buổi; hình xăm màu cần nhiều buổi hơn.",
		'steps'   => "Thăm khám | Đánh giá màu mực, độ sâu, diện tích.\nỦ tê | Giảm cảm giác châm chích.\nChiếu laser | Năng lượng phù hợp từng màu mực.\nLàm dịu | Chườm mát, bôi kem phục hồi.",
		'after'   => "Giữ vùng điều trị khô sạch 24 giờ.\nKhông cạy vảy; tránh nắng trực tiếp.",
	),
	'cham-soc-da'      => array(
		'excerpt' => 'Trẻ hóa, phục hồi, triệt lông và các liệu trình công nghệ cao giúp da khỏe, sáng và mịn hơn.',
		'content' => '<h2>Chăm sóc da khác gì điều trị da?</h2><p>Chăm sóc da tập trung duy trì làn da khỏe: cấp ẩm, phục hồi, trẻ hóa, làm sạch lông. Tất cả liệu trình đều có bác sĩ đánh giá da trước khi thực hiện.</p>',
		'faq'     => "Bao lâu nên chăm sóc da một lần? | Thông thường 2–4 tuần/lần tùy liệu trình và tình trạng da.\nDa nhạy cảm có làm được không? | Có, bác sĩ sẽ chọn liệu trình và sản phẩm dịu nhẹ phù hợp.",
		'steps'   => "Soi da | Đánh giá độ ẩm, dầu, sắc tố.\nLàm sạch | Tẩy trang, làm sạch sâu.\nLiệu trình chính | Công nghệ theo chỉ định.\nPhục hồi | Mặt nạ, dưỡng chất và chống nắng.",
		'after'   => "Uống đủ nước, ngủ đủ giấc.\nChống nắng hằng ngày.",
	),
	'noi-khoa-tham-my' => array(
		'excerpt' => 'Filler, botox, căng chỉ và meso – thẩm mỹ không phẫu thuật, do bác sĩ trực tiếp thực hiện.',
		'content' => '<h2>Nội khoa thẩm mỹ là gì?</h2><p>Là nhóm thủ thuật không phẫu thuật dùng thuốc, chỉ hoặc hoạt chất tiêm để cải thiện nếp nhăn, thể tích và độ săn chắc. Tất cả đều phải do bác sĩ có chứng chỉ thực hiện, dùng sản phẩm có nguồn gốc rõ ràng.</p>',
		'faq'     => "Sản phẩm tiêm có nguồn gốc rõ ràng không? | Phòng khám chỉ dùng sản phẩm chính hãng, có tem nhãn; khách được kiểm tra trước khi tiêm.\nAi thực hiện thủ thuật? | Bác sĩ có chứng chỉ hành nghề trực tiếp thực hiện.\nCó cần nghỉ dưỡng không? | Đa số không cần; có thể sưng nhẹ 1–2 ngày.",
		'steps'   => "Thăm khám, tư vấn | Đánh giá khuôn mặt, nhu cầu và chống chỉ định.\nKiểm tra sản phẩm | Khách xem tem nhãn, hạn dùng.\nSát khuẩn, ủ tê | Đảm bảo vô khuẩn và giảm khó chịu.\nThực hiện | Bác sĩ trực tiếp tiêm / cấy chỉ.\nTheo dõi | Theo dõi 15–30 phút, hẹn tái khám.",
		'after'   => "Không xoa bóp vùng tiêm 24 giờ.\nTránh xông hơi, rượu bia 3 ngày.\nLiên hệ ngay bác sĩ nếu sưng đau bất thường.",
	),
);

// Per-service facts. price = sample VND; [] = use category defaults.
$services = array(
	'tri-mun-khang-khuan-da-tang'     => array( 'excerpt' => 'Làm sạch sâu, lấy nhân và kháng khuẩn nhiều tầng cho da mụn viêm, mụn ẩn.', 'price' => 450000, 'unit' => '/buổi', 'sessions' => '6–10 buổi', 'duration' => '60–75 phút', 'downtime' => 'Không cần nghỉ dưỡng', 'suits' => "Da mụn ẩn, mụn đầu đen, mụn viêm mức nhẹ đến vừa\nDa dầu, lỗ chân lông to\nNgười đã tự trị mụn tại nhà không hiệu quả", 'not_suits' => "Đang có vết thương hở, nhiễm trùng da cấp\nMụn bọc nặng cần điều trị thuốc trước (bác sĩ sẽ tư vấn)", 'technology' => 'Ánh sáng sinh học xanh – đỏ, điện di tinh chất kháng khuẩn, dụng cụ lấy nhân vô khuẩn dùng một lần.', 'prices' => "Buổi lẻ | 450000 | Bao gồm soi da\nLiệu trình 6 buổi | 2400000 | Tặng 1 buổi phục hồi\nLiệu trình 10 buổi | 3800000 | Tái khám miễn phí 3 tháng" ),
	'tri-tham-sau-mun'                => array( 'excerpt' => 'Làm mờ thâm đỏ, thâm nâu sau mụn, đều màu da mà không bào mòn.', 'price' => 600000, 'unit' => '/buổi', 'sessions' => '4–6 buổi', 'duration' => '45 phút', 'downtime' => 'Đỏ nhẹ vài giờ' ),
	'tri-mun-viem-o-lung'             => array( 'excerpt' => 'Kiểm soát mụn viêm vùng lưng do mồ hôi, ma sát và nội tiết.', 'price' => 550000, 'unit' => '/buổi', 'sessions' => '6–8 buổi', 'duration' => '60 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'tri-mun-viem-o-tay-chan'         => array( 'excerpt' => 'Điều trị mụn viêm, viêm nang lông ở tay – chân, giúp da mịn trở lại.', 'price' => 500000, 'unit' => '/buổi', 'sessions' => '6–8 buổi', 'duration' => '45–60 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'tri-tham-nach'                   => array( 'excerpt' => 'Làm sáng vùng nách thâm do cạo, nhổ lông và ma sát.', 'price' => 350000, 'unit' => '/buổi', 'sessions' => '6–8 buổi', 'duration' => '30 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'tri-tham-mat'                    => array( 'excerpt' => 'Cải thiện quầng thâm mắt do sắc tố và mạch máu, giúp ánh mắt tươi hơn.', 'price' => 700000, 'unit' => '/buổi', 'sessions' => '5–6 buổi', 'duration' => '45 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'tri-tham-ben-mong'               => array( 'excerpt' => 'Làm mờ thâm vùng bẹn, mông do ma sát và viêm nang lông.', 'price' => 500000, 'unit' => '/buổi', 'sessions' => '6–8 buổi', 'duration' => '45 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'tri-tham-vung-kin'               => array( 'excerpt' => 'Phác đồ riêng tư, dịu nhẹ cho vùng da nhạy cảm nhất.', 'price' => 800000, 'unit' => '/buổi', 'sessions' => '6–8 buổi', 'duration' => '45 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'dieu-tri-nam-chuyen-sau'         => array( 'excerpt' => 'Phân loại nám bằng soi da, phối hợp laser và hoạt chất để làm mờ và kiểm soát nám.', 'price' => 1500000, 'unit' => '/buổi', 'sessions' => '8–12 buổi', 'duration' => '60 phút', 'downtime' => 'Đỏ nhẹ 1–2 ngày', 'suits' => "Nám mảng, nám chân sâu, nám hỗn hợp\nNám tái phát sau khi tự điều trị\nNgười muốn kiểm soát nám lâu dài", 'not_suits' => "Phụ nữ đang mang thai\nDa đang kích ứng, bong tróc do dùng sản phẩm không rõ nguồn gốc", 'technology' => 'Laser Picosure, điện di tranexamic acid, kem chống nắng phổ rộng y khoa.', 'prices' => "Buổi lẻ | 1500000 | Đã gồm soi da\nLiệu trình 8 buổi | 10800000 | Tặng kem chống nắng\nLiệu trình 12 buổi | 15000000 | Tái khám miễn phí 6 tháng" ),
	'dieu-tri-tan-nhang'              => array( 'excerpt' => 'Làm mờ đốm tàn nhang nhanh, ít xâm lấn bằng laser bước sóng phù hợp.', 'price' => 1200000, 'unit' => '/buổi', 'sessions' => '3–5 buổi', 'duration' => '45 phút', 'downtime' => 'Đỏ nhẹ, đóng mài 3–5 ngày' ),
	'tri-seo-loi'                     => array( 'excerpt' => 'Làm phẳng sẹo lồi, giảm ngứa và đỏ bằng phác đồ phối hợp.', 'price' => 800000, 'unit' => '/buổi', 'sessions' => '4–6 buổi', 'duration' => '30–45 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'tri-seo-lom'                     => array( 'excerpt' => 'Kích thích tái tạo collagen, làm đầy sẹo rỗ sau mụn.', 'price' => 1800000, 'unit' => '/buổi', 'sessions' => '4–6 buổi', 'duration' => '60–90 phút', 'downtime' => 'Đỏ, bong nhẹ 2–3 ngày' ),
	'xoa-xam-long-may'                => array( 'excerpt' => 'Xóa hình xăm, phun lông mày cũ bị lỗi màu, lệch dáng.', 'price' => 600000, 'unit' => '/buổi', 'sessions' => '2–4 buổi', 'duration' => '30 phút', 'downtime' => 'Đóng mài nhẹ 3–5 ngày' ),
	'xoa-xam-mi-mat'                  => array( 'excerpt' => 'Xóa xăm mí an toàn cho vùng mắt, hạn chế tổn thương.', 'price' => 700000, 'unit' => '/buổi', 'sessions' => '2–4 buổi', 'duration' => '30 phút', 'downtime' => 'Sưng nhẹ 1–2 ngày' ),
	'xoa-xam-tattoo'                  => array( 'excerpt' => 'Xóa hình xăm trên cơ thể, tính giá theo diện tích và màu mực.', 'price' => 0, 'unit' => '', 'sessions' => '4–10 buổi', 'duration' => '30–60 phút', 'downtime' => 'Đóng mài 5–7 ngày' ),
	'tre-hoa-da-cong-nghe-cao'        => array( 'excerpt' => 'Kích thích collagen, giúp da săn chắc, mịn và sáng hơn sau từng buổi.', 'price' => 900000, 'unit' => '/buổi', 'sessions' => '4–6 buổi', 'duration' => '60 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'phuc-hoi-da'                     => array( 'excerpt' => 'Phục hồi da yếu, mỏng, kích ứng do dùng sai mỹ phẩm hoặc lạm dụng corticoid.', 'price' => 400000, 'unit' => '/buổi', 'sessions' => '4–8 buổi', 'duration' => '45 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'triet-long'                      => array( 'excerpt' => 'Triệt lông công nghệ Laser Double Cool – làm mát kép, êm dịu, sạch lông tận gốc.', 'price' => 199000, 'unit' => '/vùng', 'sessions' => '6–8 buổi', 'duration' => '15–45 phút', 'downtime' => 'Không cần nghỉ dưỡng', 'suits' => "Người muốn giảm lông vùng nách, tay, chân, mép, bikini\nNgười hay bị viêm nang lông, thâm do cạo – nhổ\nSinh viên, người đi làm bận rộn (mỗi buổi chỉ 15–45 phút)", 'not_suits' => "Phụ nữ đang mang thai\nVùng da có vết thương hở, đang cháy nắng", 'technology' => 'Laser Diode Double Cool: đầu làm mát kép giữ da mát trong suốt quá trình chiếu, giảm cảm giác nóng rát, phù hợp da châu Á.', 'prices' => "Nách | 199000 | Mỗi buổi\nMép | 149000 | Mỗi buổi\nTay (nửa / cả tay) | 399000 | Mỗi buổi, từ\nChân (nửa / cả chân) | 599000 | Mỗi buổi, từ\nBikini | 499000 | Mỗi buổi, nhân viên nữ thực hiện", 'faq' => "Triệt lông có đau không? | Cảm giác như búng nhẹ; công nghệ làm mát kép giúp hầu như không nóng rát.\nBao nhiêu buổi thì sạch lông? | Trung bình 6–8 buổi, mỗi buổi cách nhau 4–6 tuần theo chu kỳ mọc lông.\nTriệt lông có vĩnh viễn không? | Lông giảm rõ rệt và mọc lại thưa, mảnh; có thể cần 1–2 buổi duy trì mỗi năm.\nCó gây viêm nang lông không? | Không. Triệt đúng kỹ thuật còn giúp giảm viêm nang lông do cạo, nhổ." ),
	'xoa-not-ruoi'                    => array( 'excerpt' => 'Xóa nốt ruồi bằng laser, bác sĩ kiểm tra nốt ruồi trước khi thực hiện.', 'price' => 150000, 'unit' => '/nốt', 'sessions' => '1 buổi', 'duration' => '15 phút', 'downtime' => 'Đóng mài 5–7 ngày' ),
	'kiem-dau-tre-hoa-laser-picosure' => array( 'excerpt' => 'Giảm tiết dầu, se khít lỗ chân lông và làm sáng da bằng laser Picosure.', 'price' => 1200000, 'unit' => '/buổi', 'sessions' => '4–6 buổi', 'duration' => '45 phút', 'downtime' => 'Đỏ nhẹ vài giờ' ),
	'filler'                          => array( 'excerpt' => 'Làm đầy rãnh cười, cằm, môi và thái dương với filler chính hãng, có tan được.', 'price' => 3500000, 'unit' => '/cc', 'sessions' => '1 lần, duy trì 9–12 tháng', 'duration' => '30–45 phút', 'downtime' => 'Sưng nhẹ 1–2 ngày', 'suits' => "Rãnh cười sâu, má hóp, cằm ngắn, môi mỏng\nNgười muốn cải thiện nhanh, không phẫu thuật", 'not_suits' => "Phụ nữ mang thai, cho con bú\nĐang viêm nhiễm vùng cần tiêm\nTiền sử dị ứng thành phần filler", 'technology' => 'Filler HA chính hãng, có tem chống giả; có thể hòa tan bằng enzyme khi cần.', 'prices' => "Filler HA (1cc) | 3500000 | Kiểm tra tem trước khi tiêm\nFiller môi | 4000000 | Theo dáng môi\nFiller cằm | 5000000 | Từ, tùy lượng" ),
	'botox'                           => array( 'excerpt' => 'Xóa nếp nhăn trán, đuôi mắt, gọn hàm – thuốc chính hãng, liều theo chỉ định bác sĩ.', 'price' => 2500000, 'unit' => '/vùng', 'sessions' => '1 lần, duy trì 4–6 tháng', 'duration' => '20–30 phút', 'downtime' => 'Không cần nghỉ dưỡng' ),
	'cang-chi'                        => array( 'excerpt' => 'Nâng cơ, căng da chảy xệ bằng chỉ sinh học tự tiêu, không phẫu thuật.', 'price' => 8000000, 'unit' => '/liệu trình', 'sessions' => '1 lần, duy trì 12–18 tháng', 'duration' => '60 phút', 'downtime' => 'Sưng nhẹ 3–5 ngày' ),
	'meso'                            => array( 'excerpt' => 'Đưa dưỡng chất trực tiếp vào da: cấp ẩm, sáng da, căng bóng.', 'price' => 1500000, 'unit' => '/buổi', 'sessions' => '3–5 buổi', 'duration' => '45 phút', 'downtime' => 'Đỏ nhẹ vài giờ' ),
);

return array( $categories, $services );
