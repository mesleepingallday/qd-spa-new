# Demo: ba cách hiển thị ảnh phòng khám

Trang HTML/CSS tĩnh, chỉ để so sánh. Không đụng tới theme `quangdang/`.

## Cách mở
Chạy máy chủ tĩnh từ **thư mục gốc repo** (để đường dẫn font `../../quangdang/assets/fonts/qd-sans.woff2` chạy được):

    python3 -m http.server 8090
    # mở http://localhost:8090/demos/clinic-photos/

Mở bằng `file://` cũng được, nhưng lightbox và font có thể khác.

- `index.html` chọn phương án
- `option-1-bento.html` mosaic
- `option-2-tour.html` tham quan theo bước
- `option-3-filmstrip.html` dải ảnh + lightbox
- `lightbox.js` dùng chung (phương án 1 và 3), khoảng 1 KB gzip

Mỗi trang có hai phần: "Trang chủ" và "Trang Cơ sở vật chất".

## Ảnh
| File trong `photos/` | Nguồn | Trạng thái |
|---|---|---|
| phong-cham-soc-da.webp | 2.jpg | có |
| khu-triet-long.webp | 3.jpg | có |
| loi-vao.webp | 4.jpg | có |
| chi-tiet-khan.webp | 5.jpg | có |
| sanh-cho.webp | 7.jpg (sảnh chờ, sofa xanh) | **thiếu** |
| quay-don-tiep.webp | 6.jpg (tường logo, quầy) | **thiếu** |
| phong-laser.webp | 8.jpg (phòng máy laser) | **thiếu** |

Ảnh WebP 960x1280, chất lượng 78, đã xóa EXIF (vị trí). 1.jpg trùng nên bỏ.

## Ảnh còn thiếu
Ba ảnh thiếu hiện là khung "Ảnh sắp có" (cùng tỉ lệ khung, nên bố cục đúng như khi có ảnh):

    <span class="frame frame--3-4 frame--pending" role="img" aria-label="..."><strong>Chú thích</strong><small>Ảnh sắp có</small></span>

Khi có ảnh: chép `sanh-cho.webp`, `quay-don-tiep.webp`, `phong-laser.webp` vào `photos/`, rồi trong HTML thay thẻ `<span ... frame--pending>` bằng
`<a class="frame frame--3-4" href="photos/x.webp" data-lightbox="..." data-caption="..."><img src="photos/x.webp" width="960" height="1280" alt="..." loading="lazy" decoding="async"></a>` (xem các ô ảnh thật cùng trang để chép đúng). Ô "pending" không mở lightbox.

## Khác với kế hoạch gốc (vì thiếu ảnh)
- Phương án 1, trang chủ: ô lớn dùng `phong-cham-soc-da`, hai ô vuông là `khu-triet-long` và `chi-tiet-khan`. Khi có `sanh-cho` nên chuyển thành ô lớn.
- Phương án 2, bước "Điều trị" dùng `phong-cham-soc-da` thay cho `phong-laser`. Bước "Thăm khám" và "Soi da" đang là khung chờ.
- Phương án 3: dải ảnh trang chủ có đủ 7 ô (4 thật trước, 3 chờ sau). Lightbox chỉ đi qua ảnh thật ("1 / 4").
- Ô lightbox là thẻ `<a href="photos/x.webp">` được JS nâng cấp, nên không có JS thì bấm vẫn mở ảnh.
