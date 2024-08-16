**I. Thêm code vào dự án**
Bước 1: Tạo thư mục mới có tên "user-certificate" vào thư mục "wp-includes"

Bước 2: Copy code vào thư mục "user-certificate"

Bước 3: Require tất cả các file vào file functions.php. Ví dụ: require_once 'user-certificate/ql-users.php';

Bước 4: Đăng nhập lại admin.

**II. Giao diện**
Bước 1: Cài đặt plugin WP Mail SMTP

Bước 2: Tạo form nhập liệu bằng Elementor Pro gồm các dòng:

- Name type: text, id: name

- Email type: email, id: email

- Phone type: number, id: phone

- Chứng chỉ type: select, id: certificate. Đặt tên cho mỗi option vs id của mỗi option: vd. Chứng chỉ tình nguyện|1

**III. Tạo trang để xem chứng chỉ**
Tạo trang mới và add shortcode [display_certificate_by_id] cho trang xem chứng chỉ.

