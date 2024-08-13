<?php 

function handle_save_certificate() {

    $templateSVG = isset($_POST['certificate']) ? stripslashes($_POST['certificate']) : '';
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;

    if (empty($templateSVG) || $userId <= 0) {
        wp_send_json_error('Dữ liệu không hợp lệ.');
    }

    global $wpdb;
    $table_certificated = $wpdb->prefix . 'certificated';
    $table_users = $wpdb->prefix . 'user_form';

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
        ['isCertified' => 1],
        ['Id' => $userId],
        ['%d'],
        ['%d']
    );

    if ($update_result === false) {
        error_log('Error updating user data: ' . $wpdb->last_error);
        wp_send_json_error('Có lỗi xảy ra khi cập nhật thông tin người dùng.');
    }


    $user = $wpdb->get_row($wpdb->prepare("
        SELECT Name, Email
        FROM $table_users
        WHERE Id = %d
    ", $userId));


    if ($user) {
        $subject = 'Chứng chỉ của bạn đã được cấp';
        $certificate_id = $wpdb->insert_id;
        $certificate_url = add_query_arg('id', $certificate_id, home_url('/certificate'));

        $message = "Chào " . esc_html($user->Name) . ",\n\n";
        $message .= "Chứng chỉ của bạn đã được cấp. Bạn có thể xem chứng chỉ của mình tại đường link sau:\n";
        $message .= esc_url($certificate_url) . "\n\n";
        $message .= "Trân trọng,\nĐội ngũ của chúng tôi";

        wp_mail($user->Email, $subject, $message);
    }

    wp_send_json_success('Chứng chỉ đã được lưu thành công.');

    if ($wpdb->last_error) {
        error_log('Error inserting data: ' . $wpdb->last_error);
    }

    wp_die();
}
?>