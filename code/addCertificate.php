<?php 
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
?>