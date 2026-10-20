# Bảng giá dịch vụ (menu Qbe+ Quang Đăng)

Giá và nội dung dịch vụ nằm ở `quangdang/inc/data/price-menu.php`. Git pull chỉ cập nhật file theme, không cập nhật database, nên giá của từng dịch vụ được đưa vào bằng một nút bấm trong wp-admin.

## Cập nhật lên website (cPanel)

1. Pull theme mới trên cPanel.
2. Vào wp-admin, mở **Công cụ › Cập nhật dữ liệu dịch vụ** (khi có phiên bản mới, wp-admin cũng hiện thông báo kèm đường dẫn).
3. Xem bảng xem trước rồi bấm **Áp dụng**.

Quy tắc ghi:

| Trường | Cách ghi |
|---|---|
| Giá từ, Đơn vị giá, Bảng giá, Số buổi, Thời gian | Luôn theo menu (ghi đè) |
| Nghỉ dưỡng, Phù hợp với, Chưa phù hợp, Công nghệ, Quy trình, Lưu ý, Hỏi đáp | Chỉ điền khi đang trống hoặc còn là nội dung mẫu; mục đã sửa tay được giữ nguyên |

Lần áp dụng đầu tiên cũng tạo trang **Dịch vụ khác** (`/dich-vu-khac/`). Trang này đọc thẳng từ file dữ liệu, nên các lần sửa sau chỉ cần pull, không cần bấm nút.

**Menu:** nếu website đã gán menu trong Giao diện › Menu, hãy tự thêm trang "Dịch vụ khác" vào nhánh Dịch vụ. Menu mặc định của theme đã có sẵn mục này.

## Sửa giá về sau

1. Sửa số trong `price-menu.php`. Bảng giá ghi mỗi dòng theo dạng `Tên | Giá | Ghi chú`. Giá ghi bằng số sẽ tự định dạng (`3000000` → 3.000.000đ); giá ghi bằng chữ (`3.000.000/1cc`, `Báo giá khi khám`) được hiển thị nguyên văn.
2. Tăng `version`.
3. Commit, pull lên cPanel, rồi bấm Áp dụng.

`price_from` = 0 cùng bảng giá trống thì trang hiện "Báo giá khi khám". Hiện áp dụng cho Triệt lông và Xóa xăm mí mắt, vì menu không có giá của hai dịch vụ này.

## Ghép menu với trang dịch vụ

- Dòng menu nào khớp một trang dịch vụ có sẵn thì được đưa vào bảng giá của trang đó. Ví dụ: Peel vùng kín → Trị thâm nách / bẹn – mông / vùng kín; Cấy HA → Trị thâm mắt; RF Needle, CO2 Fractional → Trị sẹo lõm, Trẻ hóa da, Kiềm dầu; các gói Mesotherapy và PRP → Meso.
- Tiểu phẫu (mắt, mũi, vùng kín), Peel, BAP, Laser công nghệ cao, Cấy HA và PRP được gom vào trang **Dịch vụ khác**.
- Menu in khối "Nâng mũi" hai lần với cùng giá, nên trang chỉ đưa vào một lần.

## Nguồn tham khảo cho nội dung

Nội dung được viết theo hướng thận trọng về y khoa và không cam kết kết quả. Bác sĩ phòng khám nên đọc lại trước khi đăng.

- American Academy of Dermatology: Chemical peels FAQs, https://www.aad.org/public/cosmetic/younger-looking/chemical-peels-faqs
- Mayo Clinic: Blepharoplasty, https://www.mayoclinic.org/tests-procedures/blepharoplasty/about/pac-20385174
- Radiofrequency trong điều trị sẹo mụn (tổng quan), https://pubmed.ncbi.nlm.nih.gov/25540589/
- RF microneedling cho sẹo mụn (nghiên cứu tiến cứu 2024), https://pmc.ncbi.nlm.nih.gov/articles/PMC11619162/
- Fractional CO2 điều trị sẹo mụn, https://ijl.uobaghdad.edu.iq/index.php/IJL/article/view/44
- Botulinum toxin cho tăng tiết mồ hôi nách (thử nghiệm pha III), https://clinicaltrials.gov/study/NCT01128738
- Liệu trình Profhilo (2 lần cách nhau khoảng 4 tuần): tờ thông tin sản phẩm, https://rozinaali.com/wp-content/uploads/2024/12/Profhilo-Information-Sheet.pdf
