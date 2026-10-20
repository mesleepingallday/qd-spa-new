# Đăng bài viết qua git

Mỗi thư mục trong `quangdang/content/posts/` là **một bài viết**. Sau khi deploy, lượt truy cập đầu tiên vào web sẽ tự tải ảnh vào Media và đăng bài; không cần mở trang quản trị. Code nằm ở `quangdang/inc/features/post-import.php`.

Sau khi đăng, bài là một bài viết WordPress bình thường. Nó hiện trong **Bài viết**, ảnh nằm trong **Media**, và sửa bằng trình soạn thảo như mọi bài khác. Git chỉ đưa bài lên **một lần**:
- Sửa trong quản trị không bị ghi đè.
- Xóa trong quản trị thì bài không bị tạo lại.
- Muốn sửa bài đã đăng thì sửa trong quản trị, không sửa file trong repo.

## Cấu trúc một bài

```
quangdang/content/posts/<slug>/      tên thư mục = đường dẫn bài
  post.json                          thông số
  content.html                       nội dung (mã khối Gutenberg)
  *.webp                             ảnh
```

`post.json`:

| Khóa | Ý nghĩa |
|---|---|
| `title` | Tiêu đề (H1) |
| `status` | `publish` (đăng ngay), `draft` (nháp để xem lại trong quản trị), `pending` hoặc `private` |
| `date` | Không bắt buộc, dạng `2026-11-01 08:00:00` (giờ của web). Ngày trong tương lai kèm `publish` thì bài được lên lịch. Bỏ trống thì đăng ngay lúc deploy. |
| `category` / `tags` | `{ "slug": "Tên" }`. Danh mục hoặc thẻ chưa có sẽ được tạo. Thẻ trùng slug nhóm dịch vụ (ví dụ `cham-soc-da`) sẽ chèn thẻ dịch vụ đó vào giữa bài. |
| `excerpt` | Tóm tắt, hiện trên thẻ bài |
| `featured_image` | Tên file ảnh đại diện. Thẻ bài và ảnh bìa cắt theo tỉ lệ 16:9. |
| `images` | `{ "file.webp": "alt" }`: mọi ảnh của bài kèm văn bản thay thế |
| `promo` | Chỉ cho bài Sự kiện – Ưu đãi: `label`, `start`, `end` (dạng `YYYY-MM-DD`) |
| `rank_math` | `title` (≤ 60 ký tự), `description`, `focus_keyword` |

Trong `content.html`, ảnh được viết là `src="{{img:file.webp}}"`. Khi đăng, chỗ này được thay bằng đường dẫn thật trong Media.

## Đăng một bài mới

1. **Tạo thư mục bài.** Chép một thư mục bài có sẵn, đổi tên thư mục thành slug mới, rồi sửa `post.json`, `content.html` và ảnh.
2. **Xem thử trên máy:** `npm run setup && npm run serve`, mở web thử. Bài đã có trên đó, vì lượt truy cập đầu tiên đã đăng nó.
3. **Commit, mở PR, merge** vào `main`, rồi deploy trong cPanel.
4. **Mở trang chủ web thật một lần** để bài được đăng, rồi vào LiteSpeed Cache → Purge All nếu thấy trang chưa đổi.

**Một slug đã có bài** (kể cả bài nháp) thì thư mục đó bị bỏ qua, và bài đang có không bị đụng tới.
