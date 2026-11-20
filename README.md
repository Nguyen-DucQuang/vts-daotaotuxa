# VTS — Landing page tuyển sinh

## Cấu trúc
```text
assets/
  css/style.css
  images/
  js/main.js
  vendor/
components/
  header.php
  footer.php
data/
  programs.php
  schools.php
  faq.php
index.php
README.md
```

- `index.php`: cấu hình liên hệ và các khối nội dung của landing page.
- `data/`: dữ liệu chương trình, trường liên kết và câu hỏi thường gặp.
- `components/header.php`, `components/footer.php`: phần đầu và cuối trang.
- `assets/css/style.css`: toàn bộ CSS theo thứ tự ưu tiên hiện tại.
- `assets/js/main.js`: bộ lọc, chi tiết chương trình, form và các tương tác.
- `assets/js/registration-config.local.js`: URL Google Apps Script nhận dữ liệu đăng ký.
- `assets/images/`: giữ nguyên tài nguyên ảnh; `assets/vendor/`: thư viện và font Be Vietnam Pro.

Chạy bằng PHP server. Dữ liệu đăng ký được gửi trực tiếp đến Google Apps Script và lưu trong Google Sheet; website không lưu bản sao thông tin người đăng ký trên máy chủ.

Các file cũ được sao lưu ngoài thư mục website trước khi gom cấu trúc.
