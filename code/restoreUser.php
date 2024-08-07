<?php 

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
?>