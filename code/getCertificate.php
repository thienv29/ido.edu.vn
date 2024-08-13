<?php 
function handle_get_certificate() {
    $certificateId = isset($_POST['id']) ? intval($_POST['id']) : 0;

    global $wpdb;
    $certificate_table = $wpdb->prefix . 'CERTIFICATE';

    $certificate = $wpdb->get_row($wpdb->prepare("SELECT Name, TemplateSVG FROM $certificate_table WHERE Id = %d", $certificateId));
   
    if ($certificate) {
        wp_send_json_success($certificate);
    } else {
        wp_send_json_error('Không tìm thấy chứng chỉ.');
    }

    wp_die();
}
?>