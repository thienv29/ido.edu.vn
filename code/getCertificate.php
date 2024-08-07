<?php 
function handle_get_certificate() {
    // Lấy ID chứng chỉ từ yêu cầu AJAX
    $certificateId = isset($_POST['id']) ? intval($_POST['id']) : 0;

    global $wpdb;
    $certificate_table = $wpdb->prefix . 'CERTIFICATE';

    // Lấy chứng chỉ từ cơ sở dữ liệu
    $certificate = $wpdb->get_row($wpdb->prepare("SELECT Name, TemplateSVG FROM $certificate_table WHERE Id = %d", $certificateId));
   
    if ($certificate) {
        wp_send_json_success($certificate); // Trả về chứng chỉ SVG
    } else {
        wp_send_json_error('Không tìm thấy chứng chỉ.'); // Trả về lỗi nếu không tìm thấy chứng chỉ
    }

    wp_die(); // Kết thúc AJAX request
}
?>