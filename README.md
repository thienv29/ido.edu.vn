# **HƯỚNG DẪN**

## Các Plugin cần thiết
Elementor, Elementor Pro, Blocksy, WP Mail SMTP

## **I. Thêm code vào Project**
Bước 1: Tạo thư mục mới trong thư mục themes của bạn. Ví dụ: /themes/blocksy/your-folder.

Bước 2: Copy and paste tất cả các file code vào thư mục vừa tạo.

Bước 3: Require tất cả các file vào file functions.php. Ví dụ: require_one get_template_directory() . "/your-folder/your-file.php";.

Bước 4: Đăng nhập lại admin.

## **II. Tạo chứng chỉ (Lưu ý nên tạo chứng chỉ trước khi submit form)**
Bước 1: Vào trang quản lý chứng chỉ -> chọn tạo chứng chỉ -> nhập tên -> chọn kiểu là Dán nội dung hoặc tải file (yêu cầu phải có biến {name} và biến {date} trong nội dung của chứng chỉ). 

Ví dụ: Có 3 Chứng chỉ Tình nguyện viên Id: 1, Nhà tài trợ Id: 2 và Thành viên Id: 3.

## **III. Tạo form bằng Elementor Pro**
Bước 1: Đặt tên form là register_form và tạo các trường Name, Phone, Email, Vai trò và đặt ID cho các trường đó là name, phone, email, role.

Bước 2: Đối với trường Vai trò với mỗi option thì đặt value là ID của chứng chỉ có trong cơ sở dữ liệu.

Bước 3: Cài đặt plugin WP Mail SMTP để hỗ trợ gửi mail.

## **IV. Tạo trang để xem chứng chỉ và trang để nhập id chứng chỉ**
Bước 1: Tạo trang mới và add shortcode [display_certificate_by_id] cho trang xem chứng chỉ.

Bước 2: Tạo trang mới và add shortcode [certificate_input_form] cho trang nhập id chứng chỉ.

Bước 3: Điều chỉnh path của trang /certificate cho trang xem chứng chỉ và /certificate-view cho trang nhập id chứng chỉ.
