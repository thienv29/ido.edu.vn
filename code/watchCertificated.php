<?php 
function display_certificate_by_id_shortcode($atts) {
    // Lấy ID chứng chỉ từ query string
    $certificate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    ob_start();

    if ($certificate_id <= 0) {
        echo 'ID chứng chỉ không hợp lệ.';
        return ob_get_clean();
    }

    global $wpdb;
    $table_certificated = $wpdb->prefix . 'certificated';

    // Lấy chứng chỉ từ bảng dựa trên ID
    $certificate = $wpdb->get_row($wpdb->prepare("
        SELECT TemplateSVG, createdAt
        FROM $table_certificated
        WHERE Id = %d AND isDeleted = 0
    ", $certificate_id));

    if ($certificate) {
        ?>
        <div class="certificate-view" style="width: 50%; text-align: center">
            <?php echo $certificate->TemplateSVG; ?>
        </div>
        <?php
    } else {
        echo 'Không tìm thấy chứng chỉ! Vui lòng kiểm tra lại.';
    }

    return ob_get_clean();
}
add_shortcode('display_certificate_by_id', 'display_certificate_by_id_shortcode');

?>