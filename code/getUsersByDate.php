<?php 
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
