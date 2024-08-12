<?php 
//Bắt sự kiện submit form
add_action('elementor_pro/forms/new_record', function($record, $handler) {
    $form_name = $record->get_form_settings('form_name');
    if ('user_register_form' !== $form_name) {
        return;
    }

    // Lấy dữ liệu từ form
    $raw_fields = $record->get('fields');
    $fields = [];
    foreach ($raw_fields as $id => $field) {
        $fields[$id] = $field['value'];
    }

    $name = sanitize_text_field($fields['name']);
    $phone = sanitize_text_field($fields['phone']);
    $email = sanitize_email($fields['email']);
    $projectId = intval($fields['certifcated']);

    // Lưu dữ liệu vào cơ sở dữ liệu
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_form';
    $wpdb->insert(
        $table_name,
        [
            'Name' => $name,
            'Phone' => $phone,
            'Email' => $email,
            'CertificateId' => $projectId,
            'isCertified' => 0,
            'isDeleted' => 0
        ]
    );

    // Kiểm tra lỗi
    if ($wpdb->last_error) {
        error_log('Error inserting data: ' . $wpdb->last_error);
    }
}, 10, 2);
?>