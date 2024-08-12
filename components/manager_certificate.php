<?php 
    function handle_get_certificate() {
        // Lấy ID chứng chỉ từ yêu cầu AJAX
        $certificateId = isset($_POST['id']) ? intval($_POST['id']) : 0;

        global $wpdb;
        $certificate_table = $wpdb->prefix . 'CERTIFICATE';

        // Lấy chứng chỉ từ cơ sở dữ liệu
        $certificate = $wpdb->get_row($wpdb->prepare("SELECT Name, TemplateSVG FROM $certificate_table WHERE Id = %d", $certificateId));
    
        if ($certificate) {
            wp_send_json_success($certificate); // Trả về chứng chỉ SVG
        } else {
            wp_send_json_error('Không tìm thấy chứng chỉ.'); // Trả về lỗi nếu không tìm thấy chứng chỉ
        }

        wp_die(); // Kết thúc AJAX request
    }

    function handle_save_certificate() {
        // Lấy dữ liệu từ yêu cầu AJAX
        $templateSVG = isset($_POST['certificate']) ? stripslashes($_POST['certificate']) : '';
        $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    
        // Kiểm tra dữ liệu
        if (empty($templateSVG) || $userId <= 0) {
            wp_send_json_error('Dữ liệu không hợp lệ.');
        }
    
        global $wpdb;
        $table_certificated = $wpdb->prefix . 'certificated';
        $table_users = $wpdb->prefix . 'user_form';
    
        // Thực hiện chèn dữ liệu vào cơ sở dữ liệu
        $result = $wpdb->insert(
            $table_certificated,
            [
                'TemplateSVG' => $templateSVG,
                'userId' => $userId,
            ]
        );
    
        if ($result === false) {
            error_log('Error inserting data: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi lưu chứng chỉ.');
        }
    
        $update_result = $wpdb->update(
            $table_users,
            ['isCertified' => 1], // Cập nhật giá trị cột
            ['Id' => $userId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
        // Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result === false) {
            error_log('Error updating user data: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi cập nhật thông tin người dùng.');
        }
    
        // Lấy thông tin người dùng
        $user = $wpdb->get_row($wpdb->prepare("
            SELECT Name, Email
            FROM $table_users
            WHERE Id = %d
        ", $userId));
    
        // Gửi email cho người dùng
        if ($user) {
            $subject = 'Chứng chỉ của bạn đã được cấp';
            $certificate_id = $wpdb->insert_id; // Lấy ID của chứng chỉ vừa được chèn
            $certificate_url = "http://localhost/cayxanh/certificate/?id=$certificate_id";
            $certificate_url_search = "http://localhost/cayxanh/certificate/";
    
            $message = "Chào bạn " . esc_html($user->Name) . ",\n\n";
            $message .= "Chứng chỉ của bạn đã được cấp. Bạn có thể xem chứng chỉ của mình tại đường link sau:\n";
            $message .= esc_url($certificate_url) . "\n\n";
            $message .= "Hoặc bạn có thể tra cứu chứng chỉ của mình qua:\n";
            $message .= "ID chứng chỉ của bạn: " . esc_attr($certificate_id) . "\n";
            $message .= "Đường link tra cứu chứng chỉ " . esc_url($certificate_url_search) . "\n\n";
            $message .= "Chân thành cảm ơn bạn đã tham gia dự án cộng đồng. Sự tham gia của bạn sẽ giúp làm lớn mạnh cộng động cây xanh ở Việt Nam\n";
            $message .= "Trân trọng,\nĐội ngũ của chúng tôi";
    
            wp_mail($user->Email, $subject, $message);
        }
    
        wp_send_json_success('Chứng chỉ đã được lưu thành công.');
    
        // Kiểm tra lỗi
        if ($wpdb->last_error) {
            error_log('Error inserting data: ' . $wpdb->last_error);
        }
    
        wp_die(); // Kết thúc AJAX request
    }

    function handle_cancel_certificate() {
        $userId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        global $wpdb;
        $table_users = $wpdb->prefix . 'user_form';
        $table_certificated = $wpdb->prefix . 'certificated';
    
        $update_result_user = $wpdb->update(
            $table_users,
            ['isCertified' => 0], // Cập nhật giá trị cột
            ['Id' => $userId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
         // Kiểm tra lỗi khi cập nhật dữ liệu
         if ($update_result_user === false) {
            error_log('Error updating user data: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi hủy chứng chỉ.');
        }
    
        $update_result_certificated = $wpdb->update(
            $table_certificated,
            ['isDeleted' => 1], // Cập nhật giá trị cột
            ['userId' => $userId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
        // Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result_certificated === false) {
            error_log('Error updating user data: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi hủy chứng chỉ.');
        }
    
        wp_send_json_success('Đã hủy chứng chỉ thành công.');
    
        wp_die(); // Kết thúc AJAX request
    }

    function handle_restore_certificate() {
        $userId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        global $wpdb;
        $table_certificated = $wpdb->prefix . 'certificated';
        $table_user = $wpdb->prefix . 'user_form';
    
        $update_result = $wpdb->update(
            $table_certificated,
            ['isDeleted' => 0], // Cập nhật giá trị cột
            ['userId' => $userId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
        // Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi khôi phục chứng chỉ.');
        }
    
        $update_result_user = $wpdb->update(
            $table_user,
            ['isCertified' => 1], // Cập nhật giá trị cột
            ['Id' => $userId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
         // Kiểm tra lỗi khi cập nhật dữ liệu
         if ($update_result_user === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi khôi phục chứng chỉ.');
        }
    
        wp_send_json_success('Chứng chỉ đã được khôi phục thành công.');
    
        wp_die(); // Kết thúc AJAX request
    }

    function handle_delete_certificate() {
        $certificateId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        global $wpdb;
        $table_certificate = $wpdb->prefix . 'certificate';
    
        $update_result = $wpdb->update(
            $table_certificate,
            ['isDeleted' => 1], // Cập nhật giá trị cột
            ['Id' => $certificateId], // Điều kiện để cập nhật
            ['%d'], // Định dạng dữ liệu
            ['%d'] // Định dạng điều kiện
        );
    
        // Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi xóa chứng chỉ.');
        }
    
        wp_send_json_success('Chứng chỉ đã được xóa thành công.');
    
        wp_die(); // Kết thúc AJAX request
    }

    function handle_update_certificate() {
        $certificateId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $certificateName = isset($_POST['name']) ? $_POST['name'] : '';
        $contentSVG = isset($_POST['content']) ? stripslashes($_POST['content']) : '';
    
        global $wpdb;
        $table_certificate = $wpdb->prefix . 'certificate';
    
        $update_result = $wpdb->update(
            $table_certificate,
            [
                'TemplateSVG' => $contentSVG, // Cập nhật TemplateSVG
                'Name' => $certificateName     // Cập nhật Name
            ],
            ['Id' => $certificateId] // Điều kiện để cập nhật
        );
    
        // Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi cập nhật chứng chỉ.');
        }
    
        wp_send_json_success('Chứng chỉ đã được cập nhật thành công.');
    
        wp_die(); // Kết thúc AJAX request
    }

    function handle_add_certificate() {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $content = isset($_POST['content']) ? stripslashes($_POST['content']) : '';
    
        global $wpdb;
        $table_certificate = $wpdb->prefix . 'certificate';
    
        $update_result = $wpdb->insert(
            $table_certificate,
            [
                'Name' => $name,
                'TemplateSVG' => $content
            ],
        );
    
        //Kiểm tra lỗi khi cập nhật dữ liệu
        if ($update_result === false) {
            error_log('Error: ' . $wpdb->last_error);
            wp_send_json_error('Có lỗi xảy ra khi thêm chứng chỉ.');
        }
    
        wp_send_json_success('Thêm chứng chỉ thành công.');
    
        wp_die(); // Kết thúc AJAX request
    }

    function handle_get_users_by_name() {
        $name = isset($_POST['name']) ? $_POST['name'] : '';

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
                LEFT JOIN $table_certificate c ON u.CertificateId = c.Id
                LEFT JOIN $table_certificated d ON u.Id = d.userId
                WHERE u.Name LIKE %s",
                '%' . $wpdb->esc_like($name) . '%'
            )
        );
        
        $query = $wpdb->prepare(
            "SELECT u.Id, u.Name, u.Phone, u.Email, u.CertificateId, u.isCertified, u.isDeleted, u.submittedAt, c.Name as certificate_name, 
                    CASE 
                    WHEN d.isDeleted = 1 THEN '1' 
                    ELSE '0'
                    END as certificate_status,
                    CASE 
                    WHEN d.isDeleted = 1 THEN NULL 
                    ELSE d.createdAt 
                    END as createdAt
            FROM $table_user u
            LEFT JOIN $table_certificate c ON u.CertificateId = c.Id
            LEFT JOIN $table_certificated d ON u.Id = d.userId
            WHERE u.Name LIKE %s
            LIMIT %d OFFSET %d",
            '%' . $wpdb->esc_like($name) . '%', // Tên tham số đầu tiên
            $per_page, // Số bản ghi mỗi trang (LIMIT)
            $offset    // Vị trí bắt đầu (OFFSET)
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

    function handle_get_users_by_certificate() {
        $id = isset($_POST['idCertificate']) ? intval($_POST['idCertificate']) : 0;
    
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
                 LEFT JOIN $table_certificate c ON u.CertificateId = c.Id
                 LEFT JOIN $table_certificated d ON u.Id = d.userId
                 WHERE u.CertificateId = %d",
                $id
            )
        );
    
        $query = $wpdb->prepare(
            "SELECT u.Id, u.Name, u.Phone, u.Email, u.CertificateId, u.isCertified, u.isDeleted, u.submittedAt, c.Name as certificate_name, 
                    CASE 
                    WHEN d.isDeleted = 1 THEN '1' 
                    ELSE '0'
                    END as certificate_status,
                    CASE 
                    WHEN d.isDeleted = 1 THEN NULL 
                    ELSE d.createdAt 
                    END as createdAt
            FROM $table_user u
            LEFT JOIN $table_certificate c ON u.CertificateId = c.ID
            LEFT JOIN $table_certificated d ON u.Id = d.userId
            WHERE u.CertificateId = %d LIMIT %d OFFSET %d", $id, $per_page, $offset   
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

    function handle_get_users_by_date() {
        // Lấy và làm sạch dữ liệu ngày từ POST
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
    
        // Kết nối với cơ sở dữ liệu
        global $wpdb;
        $table_user = $wpdb->prefix . 'user_form';
        $table_certificated = $wpdb->prefix . 'certificated';
        $table_certificate = $wpdb->prefix . 'certificate';
    
        //Pagination
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 5;
        $offset = ($page - 1) * $per_page; 
        $total_record = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM $table_user u
                 INNER JOIN $table_certificated c ON u.Id = c.userId
                 INNER JOIN $table_certificate a ON u.CertificateId = a.Id
                 WHERE DATE(c.createdAt) = %s",
                $date
            )
        );
    
        $query = $wpdb->prepare(
            "SELECT u.Id, 
                    u.Name, 
                    u.Phone, 
                    u.Email, 
                    u.CertificateId, 
                    u.isCertified, 
                    u.isDeleted, 
                    u.submittedAt, 
                    a.Name as certificate_name, 
                    c.createdAt as createdAt,
                    CASE 
                        WHEN c.isDeleted = 1 THEN '1' 
                        ELSE '0' 
                    END as certificate_status
             FROM $table_user u
             INNER JOIN $table_certificated c ON u.Id = c.userId
             INNER JOIN $table_certificate a ON u.CertificateId = a.Id
             WHERE DATE(c.createdAt) = %s LIMIT %d OFFSET %d", $date, $per_page, $offset
        );
    
        // Thực hiện truy vấn
        $results = $wpdb->get_results($query);
        $total_pages = ceil($total_record / $per_page);
    
        // Kiểm tra lỗi và trả kết quả
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