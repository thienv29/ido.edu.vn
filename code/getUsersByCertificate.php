<?php 

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
?>