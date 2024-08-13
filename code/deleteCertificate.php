<?php 
function handle_delete_certificate() {
    $certificateId = isset($_POST['id']) ? intval($_POST['id']) : 0;

    global $wpdb;
    $table_certificate = $wpdb->prefix . 'certificate';

    $update_result = $wpdb->update(
        $table_certificate,
        ['isDeleted' => 1],
        ['Id' => $certificateId],
        ['%d'],
        ['%d']
    );

    if ($update_result === false) {
        error_log('Error: ' . $wpdb->last_error);
        wp_send_json_error('Có lỗi xảy ra khi xóa chứng chỉ.');
    }

    wp_send_json_success('Chứng chỉ đã được xóa thành công.');

    wp_die();
}
?>