<?php 
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
?>