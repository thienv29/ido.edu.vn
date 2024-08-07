<?php 

function handle_save_certificate() {
    // Lấy dữ liệu từ yêu cầu AJAX
    $templateSVG = isset($_POST['certificate']) ? stripslashes($_POST['certificate']) : '';
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;

    // Kiểm tra dữ liệu
    if (empty($templateSVG) || $userId <= 0) {
        wp_send_json_error('Dữ liệu không hợp lệ.');
    }

    global $wpdb;
    $table_certificated = $wpdb->prefix . 'certificated';
    $table_users = $wpdb->prefix . 'user_form';

    // Thực hiện chèn dữ liệu vào cơ sở dữ liệu
    $result = $wpdb->insert(
        $table_certificated,
        [
            'TemplateSVG' => $templateSVG,
            'userId' => $userId,
        ]
    );

    if ($result === false) {
        error_log('Error inserting data: ' . $wpdb->last_error);
        wp_send_json_error('Có lỗi xảy ra khi lưu chứng chỉ.');
    }

    $update_result = $wpdb->update(
        $table_users,
        ['isCertified' => 1], // Cập nhật giá trị cột
        ['Id' => $userId], // Điều kiện để cập nhật
        ['%d'], // Định dạng dữ liệu
        ['%d'] // Định dạng điều kiện
    );

    // Kiểm tra lỗi khi cập nhật dữ liệu
    if ($update_result === false) {
        error_log('Error updating user data: ' . $wpdb->last_error);
        wp_send_json_error('Có lỗi xảy ra khi cập nhật thông tin người dùng.');
    }

    // Lấy thông tin người dùng
    $user = $wpdb->get_row($wpdb->prepare("
        SELECT Name, Email
        FROM $table_users
        WHERE Id = %d
    ", $userId));

    // Gửi email cho người dùng
    if ($user) {
        $subject = 'Chứng chỉ của bạn đã được cấp';
        $certificate_id = $wpdb->insert_id; // Lấy ID của chứng chỉ vừa được chèn
        $certificate_url = "http://localhost/myblog/certificate?id=$certificate_id";

        $message = "Chào " . esc_html($user->Name) . ",\n\n";
        $message .= "Chứng chỉ của bạn đã được cấp. Bạn có thể xem chứng chỉ của mình tại đường link sau:\n";
        $message .= esc_url($certificate_url) . "\n\n";
        $message .= "Trân trọng,\nĐội ngũ của chúng tôi";

        wp_mail($user->Email, $subject, $message);
    }

    wp_send_json_success('Chứng chỉ đã được lưu thành công.');

    // Kiểm tra lỗi
    if ($wpdb->last_error) {
        error_log('Error inserting data: ' . $wpdb->last_error);
    }

    wp_die(); // Kết thúc AJAX request
}
?>