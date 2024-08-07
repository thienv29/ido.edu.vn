<?php 

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
?>