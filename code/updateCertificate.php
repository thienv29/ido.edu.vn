<?php 
function handle_update_certificate() {
    $certificateId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $certificateName = isset($_POST['name']) ? $_POST['name'] : '';
    $contentSVG = isset($_POST['content']) ? stripslashes($_POST['content']) : '';

    global $wpdb;
    $table_certificate = $wpdb->prefix . 'certificate';

    $update_result = $wpdb->update(
        $table_certificate,
        [
            'TemplateSVG' => $contentSVG,
            'Name' => $certificateName  
        ],
        ['Id' => $certificateId]
    );

   
    if ($update_result === false) {
        error_log('Error: ' . $wpdb->last_error);
        wp_send_json_error('Có lỗi xảy ra khi cập nhật chứng chỉ.');
    }

    wp_send_json_success('Chứng chỉ đã được cập nhật thành công.');

    wp_die();
}
?>