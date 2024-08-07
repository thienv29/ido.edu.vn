<?php 
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
?>