<?php

define('POST_FORM_ID', '982ec71');

function myplugin_create_table()
{
    global $wpdb;
    $table_name_user = $wpdb->prefix . 'user_submisstion';
    $table_name_certificate = $wpdb->prefix . 'certificate';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name_user (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        phone varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        certificated varchar(50) NOT NULL,
        isCertified ENUM('pending', 'certified', 'rejected') DEFAULT 'pending',
        isDeleted TINYINT(1) DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    $sql2 = "CREATE TABLE $table_name_certificate (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        templateSvg longtext NOT NULL,
        enroll Date DEFAULT CURRENT_TIMESTAMP,
        isDeleted TINYINT(1) DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    dbDelta($sql2);
}
add_action('after_switch_theme', 'myplugin_create_table');

add_action('elementor_pro/forms/new_record', 'handle_elementor_form_after_send', 10, 2);


function handle_elementor_form_after_send($record, $handler)
{
    $post_form_id = POST_FORM_ID;
    $form_id = $record->get_form_settings('id');

    if (strval($form_id) !== $post_form_id) {
        return;
    }

    $fields = $record->get('fields');

    $name = isset($fields['name']['value']) ? sanitize_text_field($fields['name']['value']) : '';
    $email = isset($fields['email']['value']) ? sanitize_email($fields['email']['value']) : '';
    $phone = isset($fields['phone']['value']) ? sanitize_text_field($fields['phone']['value']) : '';
    $certificate = isset($fields['certificate']['value']) ? sanitize_text_field($fields['certificate']['value']) : '';

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_submisstion';
    $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'certificated' => $certificate,
        )
    );
}


add_action('admin_menu', 'add_custom_admin_tab');

function add_custom_admin_tab()
{
    add_menu_page(
        'Trao chứng chỉ', // Tiêu đề trang
        'Trao chứng chỉ', // Tên hiển thị trong menu
        'manage_options', // Quyền truy cập cần thiết
        'chung-chi', // Slug của trang
        'display_custom_tab_content', // Callback function để hiển thị nội dung
        'dashicons-admin-generic', // Icon (tùy chọn)
        6 // Vị trí trong menu (tùy chọn)
    );
}

// Function để hiển thị nội dung của tab
function display_custom_tab_content()
{
    // Đường dẫn đến file nội dung
    $content_file = plugin_dir_path(__FILE__) . 'certificate-tab.php';

    // Kiểm tra xem file có tồn tại không
    if (file_exists($content_file)) {
        include $content_file;
    } else {
        echo '<div class="wrap"><p>Không tìm thấy file nội dung.</p></div>';
    }
}

// Tạo Submenu
function custom_submenu_page()
{
    add_submenu_page(
        'chung-chi',          // Slug menu cha
        'Quản lý chứng chỉ',      // Tiêu đề trang submenu
        'Quản lý chứng chỉ',            // Tiêu đề submenu
        'manage_options',            // Khả năng người dùng cần có để xem submenu này
        'quan-ly-chung-chi',       // Slug submenu
        'display_submenu_page_content' // Hàm hiển thị nội dung trang submenu
    );
}
add_action('admin_menu', 'custom_submenu_page');

// Nội dung Trang Submenu
function display_submenu_page_content()
{
    // Đường dẫn đến file nội dung
    $content_file = plugin_dir_path(__FILE__) . 'certificate-manage.php';

    // Kiểm tra xem file có tồn tại không
    if (file_exists($content_file)) {
        include $content_file;
    } else {
        echo '<div class="wrap"><p>Không tìm thấy file nội dung.</p></div>';
    }
}

function tranformSvg($svgContent)
{
    $temp = str_replace("\'", "'", $svgContent);
    return str_replace('\"', "'", $temp);
}

function handle_ajax_request()
{
    // Kiểm tra dữ liệu nhận được và xử lý logic
    if (isset($_POST['id'])) {
        $id = sanitize_text_field($_POST['id']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'user_submisstion';

        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT u.name as username, c.name as cername, templateSvg
             FROM $table_name as u
             LEFT JOIN wp_certificate as c ON u.certificated = c.id 
             WHERE u.id = %d",
            $id
        ));

        $user->templateSvg = tranformSvg($user->templateSvg);

        wp_send_json_success($user);
    } else {
        wp_send_json_error('Không có dữ liệu nào được gửi.');
    }
}
add_action('wp_ajax_get_user_certificate', 'handle_ajax_request');
add_action('wp_ajax_nopriv_get_user_certificate', 'handle_ajax_request');

function create_custom_page()
{
    // Kiểm tra xem trang đã tồn tại hay chưa
    $page_check = get_page_by_title('Xem chứng chỉ', 'OBJECT', 'page');
    if (!isset($page_check->ID)) {
        // Tạo trang mới
        wp_insert_post(array(
            'post_title'     => 'Xem chứng chỉ',
            'post_content'   => '', 
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_author'    => 1, 
            'post_slug'      => 'xem-chung-chi',
            'comment_status' => 'closed', 
        ));
    }
}
add_action('after_switch_theme', 'create_custom_page');

function load_custom_template_for_page() {
    if (is_page('xem-chung-chi')) {
        include(get_template_directory() . '/custom/show-certificate.php');
        exit();
    }
}
add_action('template_redirect', 'load_custom_template_for_page');
