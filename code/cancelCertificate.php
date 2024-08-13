<?php 
function handle_cancel_certificate() {
    $userId = isset($_POST['id']) ? intval($_POST['id']) : 0;

    global $wpdb;
    $table_users = $wpdb->prefix . 'user_form';
    $table_certificated = $wpdb->prefix . 'certificated';

    $update_result_user = $wpdb->update(
        $table_users,
        ['isCertified' => 0],
        ['Id' => $userId],
        ['%d'], 
        ['%d']
    );

     if ($update_result_user === false) {
        error_log('Error updating user data: ' . $wpdb->last_error);
        wp_send_json_error('Có lỗi xảy ra khi hủy chứng chỉ.');
    }

    $update_result_certificated = $wpdb->update(
        $table_certificated,
        ['isDeleted' => 1],
        ['userId' => $userId],
        ['%d'],
        ['%d']
    );

    if ($update_result_certificated === false) {
        error_log('Error updating user data: ' . $wpdb->last_error);
        wp_send_json_error('Có lỗi xảy ra khi hủy chứng chỉ.');
    }

    wp_send_json_success('Đã hủy chứng chỉ thành công.');

    wp_die();
}
?>