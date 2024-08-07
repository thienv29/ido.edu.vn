<?php 
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
?>