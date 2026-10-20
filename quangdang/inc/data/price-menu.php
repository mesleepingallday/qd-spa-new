<?php
/**
 * Bảng giá thật của phòng khám (menu Qbe+ Quang Đăng) + nội dung dịch vụ.
 *
 * Nguồn duy nhất cho:
 * - Công cụ › Cập nhật dữ liệu dịch vụ (inc/features/price-import.php) ghi vào từng dịch vụ.
 * - Trang /dich-vu-khac/ (page-dich-vu-khac.php) đọc trực tiếp nhóm `other`.
 *
 * Sửa giá: đổi số ở đây, tăng `version`, pull lên hosting rồi bấm "Áp dụng" trong wp-admin.
 * Trang Dịch vụ khác đổi ngay khi pull (không cần bấm).
 *
 * Định dạng giống meta box "Thông tin dịch vụ": bảng giá mỗi dòng `Tên | Giá | Ghi chú`
 * (giá là số → tự định dạng; chữ như "3.000.000/1cc" → hiển thị nguyên văn).
 *
 * `services[slug]`:
 *  - price_from, price_unit, sessions, duration, prices → luôn ghi đè (theo menu).
 *  - downtime, suits, not_suits, technology, steps, aftercare, faq → chỉ ghi khi đang trống
 *    hoặc còn là nội dung mẫu (xem `sample_hashes`), để giữ chỗ đã sửa tay.
 *
 * @package QuangDang
 */

defined( 'ABSPATH' ) || exit;

return array(
	'version'  => '2026.10.1',

	'services' => array(

		/* ---------------- Điều trị mụn ---------------- */

		'tri-mun-khang-khuan-da-tang'     => array(
			'price_from' => 500000,
			'price_unit' => '/buổi',
			'sessions'   => '6–10 buổi tùy mức độ',
			'duration'   => '90 phút',
			'prices'     => "Điều trị mụn – Cấp độ 1 | 500000 | Nhóm mụn nhẹ, không viêm (mụn ẩn, đầu đen) · 90 phút/buổi\nĐiều trị mụn – Cấp độ 2 | 700000 | Mụn vừa, mụn viêm không mang yếu tố bệnh lý · 90 phút/buổi\nĐiều trị mụn – Cấp độ 3 | 800000 | Mụn nặng, mụn viêm mang yếu tố bệnh lý · 90 phút/buổi",
			'downtime'   => 'Không cần nghỉ dưỡng',
			'suits'      => "Mụn ẩn, mụn đầu đen, mụn đầu trắng\nMụn viêm, mụn mủ mức nhẹ đến nặng\nDa dầu, lỗ chân lông to, dễ bít tắc\nNgười đã tự trị mụn tại nhà nhưng không cải thiện",
			'not_suits'  => "Đang có vết thương hở hoặc nhiễm trùng da cấp ở vùng điều trị\nĐang mang thai, cho con bú (bác sĩ sẽ chọn hoạt chất an toàn)\nĐang uống isotretinoin hoặc thuốc trị mụn kê đơn khác",
			'technology' => 'Phân cấp mụn theo mức độ viêm để chọn phác đồ: làm sạch sâu, lấy nhân vô khuẩn bằng dụng cụ dùng một lần, hoạt chất kháng khuẩn – giảm viêm và phục hồi hàng rào da. Mụn cấp độ 3 được bác sĩ phối hợp thuốc theo đơn khi cần.',
			'faq'        => "Làm sao biết mụn của tôi thuộc cấp độ nào? | Bác sĩ soi da và đánh giá số lượng, mức độ viêm của nốt mụn ở buổi khám đầu (miễn phí), sau đó báo cấp độ và giá cụ thể trước khi làm.\nTrị mụn mất bao lâu thì thấy hiệu quả? | Thường sau 2–3 buổi da giảm viêm rõ; liệu trình đầy đủ khoảng 6–10 buổi tùy mức độ và việc chăm sóc tại nhà.\nCó phải kiêng ăn uống gì không? | Nên hạn chế đồ ngọt, sữa và đồ cay nóng trong thời gian điều trị; bác sĩ sẽ dặn cụ thể theo cơ địa.\nMụn có tái phát không? | Mụn là bệnh mạn tính, có thể tái phát nếu chăm sóc sai cách. Phòng khám hướng dẫn quy trình chăm sóc tại nhà và hẹn tái khám.",
		),

		'tri-tham-sau-mun'                => array(
			'price_from' => 1500000,
			'price_unit' => '/buổi',
			'sessions'   => '3–6 buổi',
			'duration'   => '90 phút',
			'prices'     => 'Gói điều trị thâm mụn bằng Laser Picosure | 1500000 | 90 phút/buổi',
			'downtime'   => 'Đỏ nhẹ vài giờ',
			'suits'      => "Thâm nâu, thâm đỏ còn lại sau khi hết mụn\nDa không đều màu sau mụn\nNgười muốn làm mờ thâm mà không bào mòn da",
			'not_suits'  => "Đang còn mụn viêm nhiều (cần trị mụn trước)\nDa đang cháy nắng hoặc kích ứng\nĐang mang thai",
			'technology' => 'Laser Picosure phát xung pico giây phá vỡ hắc tố thành hạt rất nhỏ để cơ thể đào thải, ít sinh nhiệt nên hạn chế nguy cơ tăng sắc tố sau viêm ở da châu Á.',
			'aftercare'  => "Chống nắng phổ rộng SPF 50 mỗi ngày, thoa lại sau 2–3 giờ khi ra ngoài.\nTránh xông hơi, tắm nắng và tẩy tế bào chết 5–7 ngày sau laser.\nKhông nặn mụn mới để tránh thâm mới.\nDưỡng ẩm, phục hồi theo hướng dẫn của bác sĩ.",
			'faq'        => "Bao lâu thì thâm mờ? | Thâm thường nhạt dần sau 1–2 buổi; cần khoảng 3–6 buổi cách nhau 3–4 tuần để da đều màu.\nLaser có làm mỏng da không? | Không, khi dùng đúng chỉ định và thông số, laser Picosure không làm mỏng da.\nThâm đỏ và thâm nâu có trị giống nhau không? | Không hoàn toàn. Bác sĩ phân biệt loại thâm khi soi da để chọn thông số và hoạt chất phù hợp.",
		),

		'tri-mun-viem-o-lung'             => array(
			'price_from' => 1200000,
			'price_unit' => '/buổi',
			'sessions'   => '4–8 buổi',
			'duration'   => '60–90 phút',
			'prices'     => "Gói điều trị viêm nang lông 1 vùng (lưng) | 1200000 | 60 phút/buổi\nĐiều trị dày sừng nang lông vùng lưng | 1200000 | 90 phút/buổi\nGói điều trị thâm mụn lưng bằng Laser Picosure | 1500000 | 90 phút/buổi\nPeel vùng lưng làm sáng, giảm thâm, giảm mụn | 2500000 | 60 phút/buổi\nGói điều trị thâm, trắng da vùng lưng | 2500000 | Loại bỏ sắc tố kết hợp CNC · 90 phút/buổi",
			'downtime'   => 'Không cần nghỉ dưỡng',
			'suits'      => "Mụn viêm, mụn mủ, viêm nang lông vùng lưng\nLưng sần, dày sừng nang lông\nThâm lưng sau mụn",
			'not_suits'  => "Đang có vết thương hở, nhiễm trùng da cấp vùng lưng\nĐang mang thai (bác sĩ sẽ chọn phương pháp phù hợp)",
			'technology' => 'Phối hợp làm sạch, kháng khuẩn, peel hóa học và Laser Picosure tùy tình trạng: giảm viêm trước, làm mờ thâm sau.',
			'aftercare'  => "Tắm và thay áo ngay sau khi ra nhiều mồ hôi.\nMặc áo cotton rộng, thoáng; hạn chế ba lô cọ xát lưng.\nKhông chà xát mạnh, không tự nặn mụn.\nChống nắng vùng lưng khi mặc áo hở.",
			'faq'        => "Mụn lưng có khác mụn mặt không? | Nhiều trường hợp là viêm nang lông do vi khuẩn hoặc nấm, nên cần soi da để điều trị đúng nguyên nhân.\nBao lâu thì lưng sạch mụn? | Thường giảm viêm rõ sau 2–3 buổi; thâm cần thêm vài buổi để mờ.",
		),

		'tri-mun-viem-o-tay-chan'         => array(
			'price_from' => 1000000,
			'price_unit' => '/buổi',
			'sessions'   => '4–8 buổi',
			'duration'   => '60–90 phút',
			'prices'     => "Điều trị dày sừng nang lông vùng tay/chân | 1000000 | 75 phút/buổi\nGói điều trị viêm nang lông 1 vùng (tay hoặc chân) | 1200000 | 60 phút/buổi\nGói điều trị viêm nang lông toàn thân | 2500000 | 90 phút/buổi",
			'downtime'   => 'Không cần nghỉ dưỡng',
			'suits'      => "Da tay, chân nổi sần như da gà (dày sừng nang lông)\nViêm nang lông, mụn mủ quanh chân lông\nThâm sau viêm nang lông do cạo, nhổ lông",
			'not_suits'  => "Đang có vết thương hở hoặc nhiễm trùng da cấp\nDa đang cháy nắng",
			'technology' => 'Làm mềm và tẩy sừng nang lông, hoạt chất kháng khuẩn – giảm viêm, phục hồi và dưỡng ẩm chuyên sâu theo từng vùng.',
			'aftercare'  => "Dưỡng ẩm hằng ngày, ưu tiên sản phẩm có urea hoặc lactic acid theo hướng dẫn.\nHạn chế cạo, nhổ lông vùng điều trị.\nTắm nước ấm vừa, không chà xát mạnh.",
			'faq'        => "Dày sừng nang lông có khỏi hẳn không? | Đây là tình trạng da có yếu tố cơ địa; điều trị giúp da mịn rõ rệt, cần duy trì dưỡng ẩm để hạn chế tái phát.\nViêm nang lông có lây không? | Thường không lây qua tiếp xúc thông thường, nhưng không nên dùng chung dao cạo, khăn tắm.",
		),

		/* ---------------- Điều trị thâm ---------------- */

		'tri-tham-nach'                   => array(
			'price_from' => 1200000,
			'price_unit' => '/buổi',
			'sessions'   => '3–6 buổi',
			'duration'   => '60 phút',
			'prices'     => 'Peel sáng da vùng nách – Công nghệ Châu Âu | 1200000 | 60 phút/buổi',
			'technology' => 'Peel hóa học Công nghệ Châu Âu nồng độ phù hợp vùng da mỏng: loại bỏ lớp sừng sẫm màu, làm sáng và đều màu da.',
			'aftercare'  => "Mặc áo rộng, thoáng sau buổi peel.\nTránh cạo, nhổ lông và dùng lăn khử mùi 2–3 ngày.\nDưỡng ẩm theo hướng dẫn; không chà xát khi da bong nhẹ.",
		),

		'tri-tham-mat'                    => array(
			'price_from' => 5000000,
			'price_unit' => '/lần',
			'sessions'   => '1–3 lần tùy tình trạng',
			'duration'   => '60 phút',
			'prices'     => "Cấy HA điều trị mờ thâm quầng mắt | 5000000 | 60 phút/lần\nTrị thâm, tái cấu trúc đa bào vùng mắt | 8000000 | Filler 1cc / 2 bên · 60 phút",
			'downtime'   => 'Sưng, bầm nhẹ 1–3 ngày',
			'suits'      => "Quầng thâm mắt do sắc tố hoặc da mỏng lộ mạch máu\nVùng dưới mắt khô, nhăn, thiếu sức sống\nRãnh lệ, mắt trũng làm khuôn mặt mệt mỏi",
			'not_suits'  => "Đang mang thai, cho con bú\nĐang viêm nhiễm vùng mắt\nTiền sử dị ứng thành phần HA\nBọng mỡ mi dưới lớn (cần tư vấn tiểu phẫu)",
			'technology' => 'Cấy HA (hyaluronic acid) vào lớp bì mỏng dưới mắt để cấp ẩm, kích thích collagen và làm sáng màu da. Với rãnh lệ, bác sĩ dùng filler HA chính hãng để tái cấu trúc vùng mắt.',
			'steps'      => "Thăm khám | Xác định thâm do sắc tố, mạch máu hay rãnh lệ.\nKiểm tra sản phẩm | Khách xem tem nhãn, hạn dùng HA/filler.\nSát khuẩn, ủ tê | Vô khuẩn và giảm khó chịu.\nCấy HA / tiêm filler | Bác sĩ thực hiện bằng kim hoặc cannula vi điểm.\nTheo dõi | Chườm mát, theo dõi 15–30 phút, hẹn tái khám.",
			'aftercare'  => "Chườm mát nhẹ trong ngày đầu nếu sưng.\nKhông xoa bóp, dụi mắt 24–48 giờ.\nTránh xông hơi, rượu bia và vận động mạnh 2–3 ngày.\nBáo ngay bác sĩ nếu đau tăng, đổi màu da hoặc nhìn mờ.",
			'faq'        => "Cấy HA khác tiêm filler rãnh lệ thế nào? | Cấy HA đưa HA mỏng vào lớp bì để dưỡng ẩm, làm sáng da; filler dùng HA có độ nâng để làm đầy rãnh lõm. Bác sĩ chọn theo nguyên nhân thâm.\nHiệu quả kéo dài bao lâu? | Thường 6–12 tháng tùy sản phẩm và cơ địa.\nCó đau không? | Vùng mắt được ủ tê trước, cảm giác châm nhẹ và chịu được.",
		),

		'tri-tham-ben-mong'               => array(
			'price_from' => 1200000,
			'price_unit' => '/buổi',
			'sessions'   => '3–6 buổi',
			'duration'   => '60 phút',
			'prices'     => 'Peel sáng da vùng kín (nách, mông, bikini) – Công nghệ Châu Âu | 1200000 | 60 phút/buổi',
			'technology' => 'Peel hóa học Công nghệ Châu Âu làm sáng vùng bẹn, mông thâm do ma sát và viêm nang lông, nồng độ phù hợp vùng da nhạy cảm.',
		),

		'tri-tham-vung-kin'               => array(
			'price_from' => 1200000,
			'price_unit' => '/buổi',
			'sessions'   => '3–6 buổi',
			'duration'   => '60 phút',
			'prices'     => 'Peel sáng da vùng kín (nách, mông, bikini) – Công nghệ Châu Âu | 1200000 | 60 phút/buổi',
			'technology' => 'Peel hóa học Công nghệ Châu Âu dịu nhẹ cho vùng bikini, thực hiện trong phòng riêng; nồng độ được bác sĩ chọn theo độ nhạy của da.',
		),

		/* ---------------- Điều trị nám ---------------- */

		'dieu-tri-nam-chuyen-sau'         => array(
			'price_from' => 10000000,
			'price_unit' => '/liệu trình',
			'sessions'   => '10 buổi',
			'duration'   => '75 phút',
			'prices'     => "Điều trị nám bằng Laser – Cấp độ 1 | 10000000 | Nám mảng, hỗn hợp, nám đốm – sắc tố không mang yếu tố bệnh lý · 10 buổi, 75 phút/buổi\nĐiều trị nám bằng Laser – Cấp độ 2 | 18000000 | Nám hỗn hợp + bệnh lý · 10 buổi, 75 phút/buổi",
			'downtime'   => 'Đỏ nhẹ 1–2 ngày',
			'suits'      => "Nám mảng, nám đốm, nám hỗn hợp\nNám tái phát sau khi tự điều trị\nNgười muốn kiểm soát nám lâu dài",
			'not_suits'  => "Phụ nữ đang mang thai\nDa đang kích ứng, bong tróc do dùng sản phẩm không rõ nguồn gốc\nĐang dùng thuốc gây nhạy cảm ánh sáng",
			'technology' => 'Laser điều trị sắc tố với thông số theo từng loại nám, phối hợp hoạt chất ức chế sắc tố và chống nắng y khoa. Cấp độ 2 dành cho nám có yếu tố bệnh lý cần bác sĩ phối hợp thuốc.',
		),

		'dieu-tri-tan-nhang'              => array(
			'price_from' => 6000000,
			'price_unit' => '/liệu trình',
			'sessions'   => '5 buổi',
			'duration'   => '60–90 phút',
			'prices'     => "Điều trị tàn nhang bằng Laser – Cấp độ 1 | 6000000 | Tàn nhang theo điểm + vùng nhỏ · 5 buổi, 60 phút/buổi\nĐiều trị tàn nhang bằng Laser – Cấp độ 2 | 12000000 | Tàn nhang, đồi mồi diện tích rộng · 5 buổi, 90 phút/buổi",
			'downtime'   => 'Đỏ nhẹ, đóng mài mỏng 3–5 ngày',
			'suits'      => "Tàn nhang rải rác hoặc tập trung vùng má, mũi\nĐồi mồi, đốm nâu do ánh nắng\nNgười muốn da đều màu nhanh, ít xâm lấn",
			'not_suits'  => "Đang mang thai\nDa đang cháy nắng\nCơ địa sẹo lồi (cần bác sĩ đánh giá)",
			'technology' => 'Laser điều trị sắc tố tác động chọn lọc lên đốm nâu, ít ảnh hưởng vùng da lành xung quanh.',
			'aftercare'  => "Để mài bong tự nhiên, không cạy.\nChống nắng phổ rộng SPF 50 hằng ngày.\nTránh xông hơi, tắm nắng 1 tuần sau laser.",
		),

		/* ---------------- Điều trị sẹo ---------------- */

		'tri-seo-loi'                     => array(
			'price_from' => 1500000,
			'price_unit' => '/buổi',
			'sessions'   => '3–6 buổi',
			'duration'   => '60 phút',
			'prices'     => "Điều trị sẹo lồi dưới 3cm² | 1500000 | 60 phút/buổi\nĐiều trị sẹo lồi 3–5cm² | 2500000 | 60 phút/buổi",
			'suits'      => "Sẹo lồi, sẹo phì đại sau mụn, phẫu thuật, xỏ khuyên, bỏng\nSẹo ngứa, đỏ, cứng\nSẹo mới hình thành cần kiểm soát sớm",
			'not_suits'  => "Đang mang thai, cho con bú\nSẹo đang viêm nhiễm\nSẹo rất lớn hoặc ở vị trí đặc biệt (bác sĩ tư vấn phối hợp)",
			'technology' => 'Phác đồ phối hợp theo kích thước và độ dày sẹo: thuốc tiêm nội sẹo làm mềm – phẳng sẹo, kết hợp laser giảm đỏ khi cần. Giá tính theo diện tích sẹo.',
			'faq'        => "Sẹo lồi có hết hẳn không? | Sẹo có thể phẳng, mềm và nhạt màu rõ rệt; sẹo lồi dễ tái phát nên cần theo dõi và tái khám đúng hẹn.\nTính giá theo diện tích thế nào? | Bác sĩ đo diện tích sẹo khi thăm khám: dưới 3cm² hoặc 3–5cm²; sẹo lớn hơn sẽ được báo giá riêng.",
		),

		'tri-seo-lom'                     => array(
			'price_from' => 1500000,
			'price_unit' => '/buổi',
			'sessions'   => '3–5 buổi',
			'duration'   => '60–90 phút',
			'prices'     => "Lăn kim điều trị sẹo rỗ kết hợp tách đáy sẹo | 1500000 | 90 phút/buổi\nĐiều trị se khít lỗ chân lông / sẹo với RF Needle | 2500000 | 60 phút/buổi\nĐiều trị sẹo lõm toàn mặt bằng Fractional CO2 Laser kết hợp PRP | 15000000 | Liệu trình 5 buổi · 90 phút/buổi",
			'downtime'   => 'Đỏ, bong nhẹ 3–7 ngày (CO2 lâu hơn)',
			'suits'      => "Sẹo rỗ sau mụn: sẹo đáy nhọn, đáy vuông, đáy tròn\nSẹo lõm sau thủy đậu, chấn thương\nLỗ chân lông to kèm sẹo",
			'not_suits'  => "Đang có mụn viêm nhiều (cần trị mụn trước)\nĐang uống isotretinoin hoặc vừa ngưng dưới 6 tháng\nCơ địa sẹo lồi, rối loạn đông máu\nĐang mang thai",
			'technology' => 'Tách đáy sẹo giải phóng dải xơ kéo da; lăn kim, RF Needle (vi kim kết hợp sóng cao tần) và Fractional CO2 Laser tạo vi tổn thương có kiểm soát để kích thích tăng sinh collagen; PRP (huyết tương giàu tiểu cầu tự thân) giúp da hồi phục nhanh hơn.',
			'steps'      => "Thăm khám | Phân loại sẹo, đánh giá độ sâu và chọn phương pháp.\nLàm sạch, ủ tê | Ủ tê 30–45 phút để giảm khó chịu.\nĐiều trị | Tách đáy sẹo, lăn kim, RF Needle hoặc CO2 Fractional theo phác đồ.\nPhục hồi | Bôi PRP/tinh chất phục hồi, chườm mát.\nTái khám | Hẹn buổi tiếp theo sau 4–6 tuần.",
			'aftercare'  => "Không trang điểm 24–48 giờ, không cạy mài.\nDùng sữa rửa mặt dịu nhẹ và kem phục hồi theo hướng dẫn.\nChống nắng kỹ để tránh tăng sắc tố sau điều trị.\nTạm ngưng retinoid, AHA/BHA 5–7 ngày.",
			'faq'        => "Bao lâu thì sẹo đầy lên? | Collagen mới hình thành dần trong 1–3 tháng; thường thấy cải thiện rõ sau 2–3 buổi và tốt nhất khoảng 3 tháng sau buổi cuối.\nCó cần nghỉ làm không? | Lăn kim, RF Needle thường đỏ 1–3 ngày; CO2 Fractional có thể đỏ, đóng mài 5–7 ngày.\nSẹo có cải thiện 100% không? | Sẹo lõm thường cải thiện rõ rệt nhưng khó phẳng hoàn toàn; bác sĩ sẽ nói mức cải thiện dự kiến khi thăm khám.",
		),

		/* ---------------- Xóa xăm ---------------- */

		'xoa-xam-long-may'                => array(
			'price_from' => 500000,
			'price_unit' => '/buổi',
			'sessions'   => '2–4 buổi',
			'duration'   => '30 phút',
			'prices'     => 'Xóa xăm lông mày bằng công nghệ Laser | 500000 | 30 phút/buổi',
		),

		'xoa-xam-mi-mat'                  => array(
			'price_from' => 0,
			'price_unit' => '',
			'prices'     => '',
		),

		'xoa-xam-tattoo'                  => array(
			'price_from' => 300000,
			'price_unit' => '/cm²',
			'sessions'   => '4–10 buổi tùy màu mực',
			'duration'   => '60 phút',
			'prices'     => "Xóa xăm mực Đen / Xanh đen bằng Laser | 300000 | Mỗi cm², mỗi buổi · 60 phút\nXóa xăm mực Đỏ / Nâu đỏ bằng Laser | 400000 | Mỗi cm², mỗi buổi · 60 phút",
			'suits'      => "Hình xăm trên cơ thể muốn xóa hoặc làm mờ để xăm đè\nHình xăm mực đen, xanh đen, đỏ, nâu đỏ",
			'not_suits'  => "Vùng xăm đang viêm, chảy dịch\nĐang mang thai\nCơ địa sẹo lồi (cần bác sĩ đánh giá trước)",
			'technology' => 'Laser phá vỡ hạt mực thành mảnh nhỏ để hệ bạch huyết đào thải dần. Giá tính theo diện tích (cm²) và màu mực.',
			'faq'        => "Tính giá xóa xăm thế nào? | Theo diện tích hình xăm (cm²) và màu mực, nhân với số buổi. Bác sĩ đo và báo tổng chi phí dự kiến khi thăm khám.\nCần bao nhiêu buổi? | Mực đen thường ít buổi hơn mực màu; trung bình 4–10 buổi, cách nhau 6–8 tuần.\nXóa xăm có để lại sẹo không? | Nguy cơ thấp nếu dùng đúng thông số và chăm sóc đúng; không cạy mài sau điều trị.",
		),

		/* ---------------- Chăm sóc da ---------------- */

		'tre-hoa-da-cong-nghe-cao'        => array(
			'price_from' => 2500000,
			'price_unit' => '/buổi',
			'sessions'   => '3–5 buổi',
			'duration'   => '60–90 phút',
			'prices'     => "Se khít lỗ chân lông, trẻ hóa da với RF Needle | 2500000 | 60 phút/buổi\nSe khít lỗ chân lông, trẻ hóa da bằng Fractional CO2 Laser | 6000000 | Liệu trình 3 buổi · 90 phút/buổi\nTrẻ hóa vùng mặt Jalupro Super Hydro | 8500000 | 60 phút/lần\nTrẻ hóa vùng mặt Profhilo | 11000000 | 60 phút/lần",
			'downtime'   => 'Đỏ nhẹ 1–3 ngày',
			'suits'      => "Da lão hóa sớm, nếp nhăn nhỏ, kém đàn hồi\nLỗ chân lông to, bề mặt da sần\nDa xỉn màu, thiếu ẩm",
			'not_suits'  => "Đang mang thai, cho con bú\nĐang có mụn viêm, nhiễm trùng da\nCơ địa sẹo lồi, rối loạn đông máu",
			'technology' => 'RF Needle (vi kim kết hợp sóng cao tần) và Fractional CO2 Laser kích thích tăng sinh collagen, se khít lỗ chân lông; Jalupro Super Hydro và Profhilo (tiêm HA/amino acid sinh học – BAP) cấp ẩm sâu và tái cấu trúc da từ bên trong.',
			'faq'        => "Nên chọn RF Needle, CO2 hay tiêm Profhilo? | RF Needle và CO2 phù hợp lỗ chân lông to, sẹo nhỏ, bề mặt sần; Profhilo, Jalupro phù hợp da mỏng, khô, chảy nhẹ. Bác sĩ thường phối hợp theo tình trạng da.\nBao lâu thấy da thay đổi? | Da căng mịn hơn sau 1–2 tuần; collagen mới hình thành rõ sau 1–3 tháng.\nProfhilo cần mấy lần? | Thường 2 lần cách nhau khoảng 4 tuần, duy trì mỗi 6 tháng.",
		),

		'phuc-hoi-da'                     => array(
			'price_from' => 3000000,
			'price_unit' => '/lần',
			'sessions'   => '3–4 lần',
			'duration'   => '80 phút',
			'prices'     => "PRP phục hồi, trẻ hóa da | 3000000 | 80 phút/lần\nMesotherapy căng bóng, trắng sáng da | 3000000 | 70 phút/lần",
			'suits'      => "Da yếu, mỏng, dễ kích ứng sau dùng sai mỹ phẩm\nDa nhiễm corticoid đã ngưng thuốc theo hướng dẫn bác sĩ\nDa khô, thiếu sức sống sau điều trị laser, peel",
			'not_suits'  => "Đang mang thai\nRối loạn đông máu, bệnh lý tiểu cầu (với PRP)\nĐang nhiễm trùng da cấp",
			'technology' => 'PRP – huyết tương giàu tiểu cầu tách từ máu tự thân, chứa yếu tố tăng trưởng giúp da tự phục hồi; Mesotherapy đưa dưỡng chất trực tiếp vào lớp bì.',
		),

		'triet-long'                      => array(
			'price_from' => 0,
			'price_unit' => '',
			'prices'     => '',
		),

		'xoa-not-ruoi'                    => array(
			'price_from' => 400000,
			'price_unit' => '/buổi',
			'sessions'   => '1 buổi',
			'duration'   => '40 phút',
			'prices'     => "Điều trị nốt ruồi, mụn cóc, mụn thịt, mụn cơm – dưới 10 nốt | 400000 | 40 phút/buổi\nĐiều trị nốt ruồi, mụn cóc, mụn thịt, mụn cơm – dưới 20 nốt | 600000 | 40 phút/buổi\nĐiều trị nốt ruồi, mụn cóc, mụn thịt, mụn cơm – dưới 40 nốt | 1000000 | 40 phút/buổi",
			'suits'      => "Nốt ruồi lành tính mất thẩm mỹ\nMụn thịt, mụn cóc, mụn cơm vùng mặt, cổ, cơ thể\nNhiều nốt nhỏ muốn xử lý trong một buổi",
			'not_suits'  => "Nốt ruồi có dấu hiệu bất thường: to nhanh, đổi màu, bờ không đều, chảy máu (cần khám chuyên khoa trước)\nCơ địa sẹo lồi\nĐang mang thai",
			'technology' => 'Laser bóc tách từng lớp nốt chính xác, hạn chế tổn thương da lành. Bác sĩ kiểm tra từng nốt trước khi xử lý; giá tính theo số lượng nốt.',
			'faq'        => "Xóa nốt ruồi có để lại sẹo không? | Nốt nhỏ, nông thường lành phẳng; nốt sâu có thể để lại vết lõm nhẹ. Chăm sóc đúng giúp hạn chế sẹo.\nNốt ruồi nào không nên tự ý xóa? | Nốt to nhanh, nhiều màu, bờ nham nhở, ngứa hoặc chảy máu cần được bác sĩ khám kỹ trước.\nBao lâu thì lành? | Vết điều trị đóng mài và bong sau khoảng 5–7 ngày.",
		),

		'kiem-dau-tre-hoa-laser-picosure' => array(
			'price_from' => 2500000,
			'price_unit' => '/buổi',
			'sessions'   => '3–5 buổi',
			'duration'   => '60–90 phút',
			'prices'     => "Se khít lỗ chân lông, trẻ hóa da với RF Needle | 2500000 | 60 phút/buổi\nMesotherapy căng bóng, trắng sáng, thu nhỏ lỗ chân lông, kiềm dầu | 4000000 | 70 phút/lần\nSe khít lỗ chân lông, trẻ hóa da bằng Fractional CO2 Laser | 6000000 | Liệu trình 3 buổi · 90 phút/buổi",
			'downtime'   => 'Đỏ nhẹ 1–3 ngày',
			'suits'      => "Da dầu, bóng nhờn, lỗ chân lông to\nDa xỉn màu, không đều màu\nDa có dấu hiệu lão hóa sớm",
			'not_suits'  => "Đang có mụn viêm nhiều\nĐang mang thai\nDa đang cháy nắng, kích ứng",
			'technology' => 'Phối hợp RF Needle, Fractional CO2 Laser hoặc Mesotherapy kiềm dầu tùy tình trạng da: điều tiết bã nhờn, se khít lỗ chân lông và làm sáng da.',
		),

		/* ---------------- Nội khoa thẩm mỹ ---------------- */

		'filler'                          => array(
			'price_from' => 3000000,
			'price_unit' => '/cc',
			'sessions'   => '1 lần, duy trì 9–12 tháng',
			'duration'   => '30–60 phút',
			'prices'     => "Filler nâng mũi | 3.000.000/1cc | 30 phút · 1cc\nTạo cằm V-line | 3.000.000/1cc | 60 phút · 1–2cc\nTạo môi trái tim | 3.000.000/1cc | 60 phút · 1cc\nLàm đầy rãnh cười | 3.000.000/1cc | 60 phút · 1–2cc / 2 bên\nTạo má Baby | 3.000.000/1cc | 60 phút · 2–3cc / 1 bên\nTai tài lộc | 3.000.000/1cc | 60 phút · 1–2cc / 2 bên\nLàm đầy thái dương | 3.000.000/1cc | 60 phút · 1–2cc / 1 bên\nTrẻ hóa bàn tay | 3.000.000/1cc | 60 phút · 2–3cc / 2 bên\nTrị thâm, tái cấu trúc đa bào vùng mắt | 8000000 | 60 phút · 1cc / 2 bên\nFiller Hàn Quốc | 3.000.000/1cc | Giá theo dòng sản phẩm\nFiller Châu Âu | 5–8 triệu/1cc | Giá theo dòng sản phẩm",
			'downtime'   => 'Sưng nhẹ 1–2 ngày',
			'suits'      => "Rãnh cười sâu, má hóp, thái dương lõm\nCằm ngắn, môi mỏng, sống mũi thấp\nNgười muốn cải thiện nhanh, không phẫu thuật",
			'not_suits'  => "Phụ nữ mang thai, cho con bú\nĐang viêm nhiễm vùng cần tiêm\nTiền sử dị ứng thành phần filler, bệnh tự miễn đang hoạt động",
			'technology' => 'Filler HA (hyaluronic acid) chính hãng, có tem nhãn kiểm tra trước khi tiêm; có thể hòa tan bằng enzyme hyaluronidase khi cần. Filler Hàn Quốc 3 triệu/cc, Filler Châu Âu 5–8 triệu/cc.',
			'faq'        => "Một vùng cần bao nhiêu cc? | Tùy vùng và cấu trúc khuôn mặt: môi, mũi thường 1cc; má Baby 2–3cc mỗi bên. Bác sĩ đo và tư vấn lượng tối thiểu cần thiết.\nFiller Hàn Quốc và Châu Âu khác gì? | Khác về độ đàn hồi, độ bền và giá. Filler Châu Âu thường duy trì lâu hơn; bác sĩ chọn theo vùng tiêm và nhu cầu.\nTiêm filler có nguy hiểm không? | Filler an toàn khi do bác sĩ có chứng chỉ thực hiện với sản phẩm chính hãng; phòng khám luôn có sẵn thuốc hòa tan và quy trình xử lý biến chứng.",
		),

		'botox'                           => array(
			'price_from' => 3000000,
			'price_unit' => '/lần',
			'sessions'   => '1 lần, duy trì 4–6 tháng',
			'duration'   => '60 phút',
			'prices'     => "Thon gọn hàm V-line | 3000000 | Botulax 100 · 60 phút\nXóa nhăn vùng khóe mắt | 3000000 | Botulax 100 · 60 phút\nXóa nhăn vùng trán và cau mày | 3000000 | Botulax 100 · 60 phút\nĐiều trị cười hở lợi | 3000000 | Botulax 100 · 60 phút\nTriệt tuyến mồ hôi nách | 6000000 | Botulax 200 · 60 phút\nTriệt tuyến mồ hôi lòng bàn tay | 6000000 | Botulax 200 · 60 phút\nTriệt tuyến mồ hôi lòng bàn chân | 6000000 | Botulax 200 · 60 phút\nGiảm cơ bắp tay | 6000000 | Botulax 200 · 60 phút\nGiảm cơ bắp chân | 6000000 | Botulax 200 · 60 phút",
			'downtime'   => 'Không cần nghỉ dưỡng',
			'suits'      => "Nếp nhăn động: trán, cau mày, đuôi mắt\nCơ hàm to, muốn mặt thon gọn\nCười hở lợi\nĐổ mồ hôi nhiều ở nách, bàn tay, bàn chân\nBắp tay, bắp chân to do cơ",
			'not_suits'  => "Phụ nữ mang thai, cho con bú\nBệnh lý thần kinh – cơ (nhược cơ…)\nĐang dùng kháng sinh nhóm aminoglycoside\nĐang viêm nhiễm vùng tiêm",
			'technology' => 'Botulinum toxin type A Botulax (Hàn Quốc), liều 100 hoặc 200 đơn vị theo vùng điều trị, do bác sĩ tính liều và tiêm trực tiếp.',
			'aftercare'  => "Không nằm, cúi người hay xoa bóp vùng tiêm 4 giờ đầu.\nTránh xông hơi, tập luyện nặng, rượu bia 24 giờ.\nHiệu quả bắt đầu sau 3–7 ngày, rõ nhất sau 2 tuần; hẹn tái khám để đánh giá.",
			'faq'        => "Botox bao lâu thì có hiệu quả? | Bắt đầu thấy sau 3–7 ngày, đạt tối đa khoảng 2 tuần.\nHiệu quả kéo dài bao lâu? | Xóa nhăn, gọn hàm thường 4–6 tháng; triệt mồ hôi thường kéo dài 4–9 tháng, lần tiêm sau thường bền hơn.\nTiêm botox có bị đơ mặt không? | Không, nếu tính đúng liều và đúng điểm tiêm. Bác sĩ giữ biểu cảm tự nhiên cho khuôn mặt.",
		),

		'cang-chi'                        => array(
			'price_from' => 10000000,
			'price_unit' => '/lần',
			'sessions'   => '1 lần, duy trì 12–18 tháng',
			'duration'   => '60 phút',
			'prices'     => "Nâng cao trụ, sống mũi | 15000000 | Chỉ Medi White (Hiko – Misko)\nLàm đầy rãnh cười, xóa nhăn thái dương | 10.000.000/vùng | Chỉ Medi White (Hiko – Misko)\nNâng cơ mặt | 15000000 | Chỉ Cog\nXiết nọng cằm, mặt V-line | 15000000 | Chỉ Cog – Mono – Screw\nTạo má Baby bằng chỉ | 10000000 | Chỉ Mono\nTrẻ hóa tăng sinh Collagen Full Face | 10000000 | Chỉ Mono",
			'downtime'   => 'Sưng, bầm nhẹ 3–5 ngày',
			'suits'      => "Da chảy xệ nhẹ đến vừa vùng má, góc hàm, nọng cằm\nRãnh cười, nếp nhăn thái dương\nSống mũi thấp muốn nâng không phẫu thuật",
			'not_suits'  => "Phụ nữ mang thai, cho con bú\nRối loạn đông máu, đang dùng thuốc chống đông\nĐang viêm nhiễm vùng cấy chỉ\nDa chảy xệ nặng (cần tư vấn phẫu thuật)",
			'technology' => 'Chỉ PDO tự tiêu: Cog (có gai, nâng kéo), Mono (sợi trơn, tăng sinh collagen), Screw (xoắn, tạo thể tích); chỉ Medi White (Hiko – Misko) cho nâng mũi và làm đầy.',
			'aftercare'  => "Chườm mát 24–48 giờ đầu để giảm sưng.\nHạn chế há miệng to, nhai đồ cứng và biểu cảm mạnh 1–2 tuần.\nKhông massage, xông hơi vùng mặt 2 tuần.\nNằm ngửa, kê cao gối vài đêm đầu.",
			'faq'        => "Căng chỉ duy trì được bao lâu? | Chỉ tự tiêu sau 6–12 tháng, hiệu quả nâng và collagen mới thường duy trì 12–18 tháng.\nCăng chỉ có đau không? | Vùng điều trị được gây tê; sau làm có thể căng tức, sưng nhẹ vài ngày.\nNâng mũi bằng chỉ khác filler thế nào? | Chỉ tạo khung nâng trụ, sống mũi; filler làm đầy thể tích. Bác sĩ có thể phối hợp tùy dáng mũi.",
		),

		'meso'                            => array(
			'price_from' => 3000000,
			'price_unit' => '/lần',
			'sessions'   => '3–5 lần',
			'duration'   => '70–80 phút',
			'prices'     => "Mesotherapy căng bóng, trắng sáng da | 3000000 | 70 phút/lần\nMesotherapy điều trị nám | 3000000 | 70 phút/lần\nMesotherapy điều trị mụn viêm trung bình – nặng | 3000000 | 70 phút/lần\nPRP phục hồi, trẻ hóa da | 3000000 | 80 phút/lần\nMesotherapy căng bóng, trắng sáng, thu nhỏ lỗ chân lông, kiềm dầu | 4000000 | 70 phút/lần",
			'downtime'   => 'Đỏ nhẹ, nốt kim li ti vài giờ đến 1 ngày',
			'suits'      => "Da khô, xỉn màu, thiếu sức sống\nDa dầu, lỗ chân lông to\nNám, mụn viêm cần hỗ trợ điều trị từ bên trong",
			'not_suits'  => "Phụ nữ mang thai, cho con bú\nĐang nhiễm trùng da, mụn mủ lan rộng vùng tiêm\nRối loạn đông máu",
			'technology' => 'Mesotherapy đưa dưỡng chất (HA, vitamin, peptide, hoạt chất đặc trị) vào lớp bì bằng kim siêu nhỏ hoặc máy tiêm; PRP dùng huyết tương giàu tiểu cầu tự thân.',
			'aftercare'  => "Không trang điểm 12–24 giờ.\nTránh xông hơi, bơi lội 2 ngày.\nChống nắng và dưỡng ẩm theo hướng dẫn.",
			'faq'        => "Meso cần làm mấy lần? | Thường 3–5 lần, cách nhau 2–4 tuần, sau đó duy trì 1–3 tháng/lần.\nMeso khác PRP thế nào? | Meso dùng dưỡng chất pha sẵn theo mục tiêu (sáng da, kiềm dầu, trị nám); PRP dùng yếu tố tăng trưởng từ máu của chính bạn để phục hồi, trẻ hóa.",
		),
	),

	/*
	 * Trang /dich-vu-khac/: nhóm dịch vụ chưa có trang riêng.
	 * tables: [ tiêu đề bảng (có thể rỗng) => dòng `Tên | Giá | Ghi chú` ].
	 */
	'other'    => array(
		'intro'  => 'Bảng giá tiểu phẫu thẩm mỹ, peel, trẻ hóa da BAP, laser công nghệ cao, cấy HA và PRP tại Qbe+ Quang Đăng. Mọi dịch vụ đều có bác sĩ thăm khám, tư vấn trước khi thực hiện.',
		'groups' => array(
			'tieu-phau-mat'      => array(
				'title'  => 'Tiểu phẫu mắt',
				'intro'  => 'Cắt mí, lấy mỡ, treo cung mày và mở góc mắt giúp đôi mắt to, sáng và trẻ hơn. Thủ thuật gây tê tại chỗ, bác sĩ đánh giá cấu trúc mí, lượng da thừa và bọng mỡ trước khi chỉ định.',
				'tables' => array(
					'Mí trên'        => "Cắt mí tạo 2 nếp mí | 6000000 |\nCắt da trùng qua đường mí | 6000000 |\nDàn mỡ | 3000000 |\nThiết kế đuôi phượng | 3000000 |\nLấy mỡ | 3000000 |\nCấy mỡ | 4000000 |",
					'Mí dưới'        => "Cắt da thừa | 3000000 |\nDàn mỡ rãnh lệ | 6000000 |\nLấy mỡ | 3000000 |\nSửa lật mi | 6000000 |",
					'Cung mày'       => "Treo cung mày | 6000000 |\nĐính màng xương trán | 3000000 |",
					'Góc mắt & sửa mí' => "Mở góc mắt trong | 3000000 |\nSửa mí – mở size mí | 9000000 |",
				),
				'faq'    => "Cắt mí bao lâu thì lành? | Sưng, bầm rõ nhất 2–3 ngày đầu; cắt chỉ sau khoảng 5–7 ngày; đa số sinh hoạt bình thường sau 1–2 tuần và mí ổn định hoàn toàn sau 1–3 tháng.\nTiểu phẫu mắt có rủi ro gì? | Có thể gặp sưng bầm, khô mắt, mí chưa đều hoặc sẹo rõ (hiếm). Bác sĩ khám kỹ, chọn đường mổ phù hợp và hẹn tái khám để theo dõi.\nAi không nên làm? | Người đang viêm nhiễm vùng mắt, khô mắt nặng, rối loạn đông máu, tăng huyết áp, đái tháo đường chưa kiểm soát hoặc đang mang thai.",
			),
			'tieu-phau-mui'      => array(
				'title'  => 'Tiểu phẫu mũi',
				'intro'  => 'Nâng mũi kín qua đường lỗ mũi, không để lại sẹo bên ngoài; cấy trung bì bọc đầu mũi giúp đầu mũi mềm mại, hạn chế lộ sóng.',
				'tables' => array(
					'' => "Nâng mũi kín đường lỗ mũi | 9000000 |\nCấy trung bì, bọc đầu mũi | 6000000 |",
				),
				'faq'    => "Nâng mũi kín có để lại sẹo không? | Đường mổ nằm trong lỗ mũi nên không thấy sẹo bên ngoài.\nBao lâu thì mũi vào form? | Sưng giảm nhiều sau 1–2 tuần, dáng mũi tự nhiên dần sau 1–3 tháng.",
			),
			'tieu-phau-vung-kin' => array(
				'title'  => 'Tiểu phẫu vùng kín',
				'intro'  => 'Tạo hình thẩm mỹ vùng kín giúp cải thiện hình dáng, sự tự tin và chất lượng đời sống. Thăm khám, tư vấn riêng tư; bác sĩ đánh giá cấu trúc và nhu cầu trước khi chỉ định.',
				'tables' => array(
					'Mũ âm vật'          => 'Tạo hình mũ âm vật (da thừa) | 6000000 |',
					'Môi bé'             => "Tạo hình môi bé (da thừa môi bé) | 6000000 |\nChuyển vạt làm hồng | 3000000 |",
					'Tầng sinh môn'      => "Tạo hình tầng sinh môn | 4000000 |\nSửa sẹo | 3000000 |",
					'Se khít – Môi lớn' => "Thu ống âm đạo (ống âm đạo rộng) | 9000000 |\nChất làm đầy môi lớn (Filler) | Báo giá khi khám |",
				),
				'faq'    => "Tiểu phẫu vùng kín có đau không? | Thủ thuật được gây tê tại chỗ; sau làm có thể đau tức nhẹ vài ngày, bác sĩ kê thuốc giảm đau.\nBao lâu thì sinh hoạt vợ chồng lại được? | Thường sau 4–6 tuần, khi bác sĩ tái khám xác nhận vết thương đã lành.\nThông tin có được bảo mật không? | Có. Hồ sơ và hình ảnh được bảo mật tuyệt đối, chỉ dùng khi bạn đồng ý bằng văn bản.",
			),
			'peel'               => array(
				'title'  => 'Peel da – Công nghệ Châu Âu',
				'intro'  => 'Peel hóa học dùng acid nồng độ y khoa loại bỏ lớp sừng già, thông thoáng lỗ chân lông, làm sáng và đều màu da. Peel nông hầu như không cần nghỉ dưỡng, da có thể đỏ và bong nhẹ 3–7 ngày; thường cần 3–5 buổi, cách nhau 2–4 tuần.',
				'tables' => array(
					'' => "Peel mụn ẩn, mụn đầu đen, mụn viêm nhẹ | 600000 | 60 phút/buổi\nPeel mụn viêm trung bình – nặng | 1200000 | 60 phút/buổi\nPeel sáng da vùng kín (nách, mông, bikini) | 1200000 | 60 phút/buổi\nPeel nám, tàn nhang, trắng sáng đều màu da | 2000000 | 60 phút/buổi\nPeel vùng lưng làm sáng, giảm thâm, giảm mụn | 2500000 | 60 phút/buổi\nGói điều trị thâm, trắng da vùng lưng | 2500000 | Loại bỏ sắc tố kết hợp CNC · 90 phút/buổi",
				),
				'faq'    => "Peel có làm mỏng da không? | Không, khi đúng nồng độ, đúng chỉ định và có khoảng nghỉ giữa các buổi. Peel còn kích thích da tái tạo.\nSau peel cần chăm sóc gì? | Rửa mặt nước mát, dưỡng ẩm, chống nắng SPF 30+ mỗi ngày; không cạy lớp da bong; tạm ngưng retinoid, AHA/BHA theo dặn dò.\nAi chưa nên peel? | Người đang dùng isotretinoin, đang cháy nắng, có vết thương hở hoặc đang mang thai.",
			),
			'bap'                => array(
				'title'  => 'Trẻ hóa BAP – Profhilo, Jalupro',
				'intro'  => 'Tiêm sinh học (BAP) dùng HA tinh khiết hoặc HA kết hợp amino acid để cấp ẩm sâu, kích thích collagen – elastin, giúp da căng mướt và đàn hồi. Profhilo thường tiêm 2 lần cách nhau khoảng 4 tuần, duy trì mỗi 6 tháng.',
				'tables' => array(
					'' => "Trẻ hóa vùng mặt Jalupro Super Hydro | 8500000 | 60 phút/lần\nTrẻ hóa vùng mặt Profhilo | 11000000 | 60 phút/lần\nTrẻ hóa vùng cổ | 10000000 | 60 phút/lần\nTrẻ hóa bàn tay | 10000000 | 60 phút/lần",
				),
				'faq'    => "BAP khác filler thế nào? | Filler tạo thể tích, làm đầy vùng lõm; BAP lan tỏa dưới da để cấp ẩm và cải thiện chất lượng da, không làm thay đổi đường nét.\nSau tiêm có sưng không? | Có thể nổi vài nốt sẩn nhỏ ở điểm tiêm, tự hết sau vài giờ đến 1 ngày.",
			),
			'laser-cong-nghe'    => array(
				'title'  => 'Laser & công nghệ cao',
				'intro'  => 'Fractional CO2 Laser và RF Needle tạo vi tổn thương có kiểm soát để kích thích collagen, se khít lỗ chân lông, cải thiện sẹo lõm. Sau CO2 da thường đỏ, đóng mài mỏng 5–7 ngày; RF Needle đỏ nhẹ 1–3 ngày.',
				'tables' => array(
					'' => "Se khít lỗ chân lông, sẹo, trẻ hóa da với RF Needle | 2500000 | 60 phút/buổi\nSe khít lỗ chân lông, trẻ hóa da bằng Fractional CO2 Laser | 6000000 | Liệu trình 3 buổi · 90 phút/buổi\nĐiều trị sẹo lõm toàn mặt bằng Fractional CO2 Laser kết hợp PRP | 15000000 | Liệu trình 5 buổi · 90 phút/buổi",
				),
				'faq'    => "Bao lâu thấy kết quả? | Da mịn hơn sau 1–2 tuần; collagen mới hình thành dần, rõ nhất khoảng 3 tháng sau buổi cuối.\nAi chưa nên làm? | Người đang có mụn viêm nhiều, đang hoặc vừa ngưng isotretinoin dưới 6 tháng, cơ địa sẹo lồi, đang mang thai.",
			),
			'cay-ha-prp'         => array(
				'title'  => 'Cấy HA & PRP',
				'intro'  => 'Cấy HA cấp ẩm và làm sáng vùng da mỏng dưới mắt; PRP dùng huyết tương giàu tiểu cầu từ máu tự thân giúp da phục hồi, trẻ hóa an toàn.',
				'tables' => array(
					'' => "Cấy HA điều trị mờ thâm quầng mắt | 5000000 | 60 phút/lần\nPRP phục hồi, trẻ hóa da | 3000000 | 80 phút/lần",
				),
				'faq'    => "PRP có an toàn không? | PRP lấy từ máu của chính bạn nên hầu như không gây dị ứng; không áp dụng cho người rối loạn đông máu, bệnh lý tiểu cầu.\nCấy HA cần mấy lần? | Thường 1–3 lần cách nhau 3–4 tuần tùy mức độ thâm.",
			),
		),
	),

	/*
	 * md5 của nội dung MẪU cũ (dev/seed-data/services.php, xuống dòng chuẩn hóa \n, trim).
	 * Field nội dung có giá trị trùng một mã ở đây được coi là "chưa sửa tay" và sẽ được thay.
	 */
	'sample_hashes' => array(
		'26f7396c695a19722559542d968a573a',
		'1835567a0f84f467b3995c632262678b',
		'b5675c1e67393b309094f15af09cdcfa',
		'ba0015b39fcffab3e0cf9360f30bcfa7',
		'924806c639259d22da14adeeb497123c',
		'747f64281f42ecd6eb3186a75f7a57e5',
		'd3d5cd487074dae5884a884d54338ff0',
		'1e691cfc24fdf53c9258c776a50e1277',
		'1691209656ae358ad8aa70b0ee6b5f42',
		'3612477fa5707d66e73d9e7b44431fd8',
		'87ed3bf6b14f60395fb7a2b0df5ba861',
		'f081eaacf338af8f4fa77139ab76b15d',
		'241e8a0e5fc0d6f4cb4ca8fcd331481c',
		'f509650a56cfb7cbeb5e91036969145d',
		'8906826bc695f0ecdae6487c07e5e065',
		'835288df8ee1913574ee7ff76ba202f4',
		'c8bc10b4e82be0d74e72a83e2e280bd6',
		'3adb48435d7c37503e9e269cdceeba4b',
		'6dda1d6f1713cf8729148accc4f7dc33',
		'f1b8de5e4382e2d46a81cadbcc921bc0',
		'162bb1d542d6cb98c1005d843fba5f62',
		'9f596dbff6389ebc8487d2b338312181',
		'f009093235ce1e3d2b0b9e359ca0c60e',
		'3e6e70e94849e437661954b80fd2b9fe',
		'ff377298bca64ad9b8c5d418be4d1914',
		'6498ba93503ac94acc5ef49c0b61ef36',
		'cffca9264171940a51502f43f170653e',
		'eff8f198f0ac6008790ef013bb1465d5',
		'5689245127f5ba13cdabbdb6f61599db',
		'460abdb93c39f965407b3cde272b3869',
		'0a3e526b566a480434a81036509bbc94',
		'ece47380db5d0a33927debdb1a4c1eac',
		'78dcd0e0a8a8af8b7dd9584038608596',
		'7f0b5ba879a13d1ef00d8a79681a17c6',
		'0d319b7f173a91288ca9db271d55351d',
		'34cdf9ac5056000d122ea5bbe5dcb52c',
		'30e21bf13540bc054cb5722c6cd72970',
		'ac02496d1cdd95dbba873271e26f73d4',
		'1d7e8fee14efa041edfdbd7053ee0eed',
		'032f97cbf22e94eb4d34b0636102bc01',
		'0b7519983ec3bbabc884a9b243caf9b0',
		'35b614081e59e1b0a71b9ef8aaf4403b',
		'43088f085e99b4998ec062a39c586d7e',
	),
);
