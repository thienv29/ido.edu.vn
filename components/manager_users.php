<?php 

    //Lấy danh sách Users
    function handle_get_list_users() {

        global $wpdb;
        $user_table = $wpdb->prefix . 'USER_FORM';
        $certificate_table = $wpdb->prefix . 'CERTIFICATE';
        $certificated_table = $wpdb->prefix . 'CERTIFICATED';

        //Pagination
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 5;
        $offset = ($page - 1) * $per_page;
        $total_record = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}user_form");

        $query = $wpdb->prepare("
            SELECT u.Id, u.Name, u.Phone, u.Email, u.CertificateId, u.isCertified, u.isDeleted, u.submittedAt, c.Name as certificate_name,
                CASE 
                WHEN d.isDeleted = 1 THEN '1' 
                ELSE '0'
                END as certificate_status,
                CASE 
                WHEN d.isDeleted = 1 THEN NULL 
                ELSE d.createdAt 
                END as createdAt
            FROM $user_table u
            LEFT JOIN $certificate_table c ON u.CertificateId = c.Id
            LEFT JOIN $certificated_table d ON u.Id = d.userId 
            LIMIT %d OFFSET %d", $per_page, $offset
        );

        $results = $wpdb->get_results($query);
        $total_pages = ceil($total_record / $per_page);

        if ($results === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi lấy danh sách người dùng.');
        }

        wp_send_json_success([
            'users' => $results,
            'total_pages' => $total_pages,
            'page' => $page,
        ]);

        wp_die();
    }

    //Xóa người dùng (xóa mềm)
    function handle_delete_user() {
        $userId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        global $wpdb;
        $table_users = $wpdb->prefix . 'user_form';
    
        $update_result = $wpdb->update(
            $table_users,
            ['isDeleted' => 1], // Cập nhật giá trị cột
            ['Id' => $userId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
        // Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result === false) {
            error_log('Error updating user data: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi xóa thông tin người dùng.');
        }
    
        wp_send_json_success('Xóa người dùng thành công.');
    
        wp_die(); // Kết thúc AJAX request
    }

    //Khôi phục Users
    function handle_restore_user() {
        $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    
        global $wpdb;
        $table_users = $wpdb->prefix . 'user_form';
    
        $update_result = $wpdb->update(
            $table_users,
            ['isDeleted' => 0], // Cập nhật giá trị cột
            ['Id' => $userId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
        // Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result === false) {
            error_log('Error updating user data: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi khôi phục.');
        }
    
        wp_send_json_success('Khôi phục người dùng thành công.');
    
        wp_die(); // Kết thúc AJAX request
    }

    function aggregate_certificate_data() {
        global $wpdb;
        $user_table = $wpdb->prefix . 'user_form'; // Đảm bảo tên bảng đúng
        $certificate_table = $wpdb->prefix . 'certificate'; // Đảm bảo tên bảng đúng
    
        // Lấy dữ liệu chứng chỉ và số lượng tương ứng
        $query = "
            SELECT c.Name AS certificate_name, COUNT(*) AS count
            FROM $user_table u
            LEFT JOIN $certificate_table c ON u.CertificateId = c.Id
            WHERE u.isDeleted = 0 AND u.isCertified = 1
            GROUP BY c.Name
        ";
    
        $results = $wpdb->get_results($query, ARRAY_A); // Trả về dưới dạng mảng liên kết
    
        if ($results === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi tổng hợp dữ liệu chứng chỉ.');
        }
    
        wp_send_json_success($results); // Trả về dữ liệu trực tiếp
        wp_die();
    }

    function get_notifications() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_form'; // Thay đổi tên bảng nếu cần
    
        // Lấy dữ liệu từ bảng wp_user_form
        $results = $wpdb->get_results("
            SELECT name, submittedAt
            FROM $table_name
            WHERE submittedAt IS NOT NULL
            ORDER BY submittedAt DESC
            LIMIT 12
        ");
    
        if ($results === false) {
            wp_send_json_error('Có lỗi xảy ra khi lấy dữ liệu.');
        }
    
        wp_send_json_success($results);
    }
    function handle_get_users_by_date_submit() {
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
    
        global $wpdb;
        $table_user = $wpdb->prefix . 'user_form';
        $table_certificate = $wpdb->prefix . 'certificate';
        $table_certificated = $wpdb->prefix . 'certificated';
    
        //Pagination
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 5;
        $offset = ($page - 1) * $per_page;
        $total_record = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM $table_user u
                 INNER JOIN $table_certificate a ON u.CertificateId = a.Id
                 LEFT JOIN $table_certificated d ON u.Id = d.userId
                 WHERE DATE(u.submittedAt) = %s",
                $date
            )
        );
    
        $query = $wpdb->prepare(
            "SELECT u.Id, u.Name, u.Phone, u.Email, u.CertificateId, u.isCertified, u.isDeleted, u.submittedAt, a.Name as certificate_name, 
                    CASE 
                    WHEN d.isDeleted = 1 THEN '1' 
                    ELSE '0'
                    END as certificate_status,
                    CASE 
                    WHEN d.isDeleted = 1 THEN NULL 
                    ELSE d.createdAt 
                    END as createdAt
            FROM $table_user u
            INNER JOIN $table_certificate a ON u.CertificateId = a.Id
            LEFT JOIN $table_certificated d ON u.Id = d.userId
            WHERE DATE(u.submittedAt) = %s",
            $date
        );
    
        $results = $wpdb->get_results($query);
        $total_pages = ceil($total_record / $per_page);
    
        if ($results === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi lấy danh sách người dùng.');
        }
    
        wp_send_json_success([
            'users' => $results,
            'total_pages' => $total_pages,
            'page' => $page,
        ]);
    
        wp_die();
    }
?>