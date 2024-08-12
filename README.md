Hướng dẫn cài đặt:

Bước 1: Tạo form trên giao diện

Bước 2: Đặt tên form là "user_register_form" và gán biến:

![image](https://github.com/user-attachments/assets/0aeaa4e3-8350-4406-9261-d01db29a4238)

Bước 3: Gán biến cho từng ô select

![image](https://github.com/user-attachments/assets/55f0d090-31fe-43f7-8125-f3eff3ff9a1b)

Bước 4: Đặt thư mục "components" vào giao diện đang dùng trong Wordpress

![image](https://github.com/user-attachments/assets/634eb161-4999-40ec-8690-e1f97f4844ef)

Bước 5: Chạy file "cayxanh.sql" trên phần mềm quản lý cơ sở dữ liệu để thêm 3 bảng: wp_certificate, wp_user_form, wp_certificated

Bước 6: Thêm các dòng này vào file "functions.php" để gọi các file từ trong thư mục "components":

require_once get_template_directory() . "/components/create_menu_admin.php";

require_once get_template_directory() . "/components/dashboard_users.php";

require_once get_template_directory() . "/components/dashboard_cerificate.php";

require_once get_template_directory() . "/components/dashboard_certificated.php";

require_once get_template_directory() . "/components/submitForm.php";

require_once get_template_directory() . "/components/search_certificate.php";

require_once get_template_directory() . "/components/manager_users.php";

require_once get_template_directory() . "/components/manager_certificate.php";

Bước 7 thêm hàm sau trong file "functions.php" để làm mất cái footer trong admin dashboard.

function remove_footer_admin() {

    remove_filter('update_footer', 'core_update_footer'); // Xóa phiên bản WordPress
    
    add_filter('admin_footer_text', '__return_empty_string'); // Xóa văn bản mặc định
    
}

add_action('admin_menu', 'remove_footer_admin');
