<?php 
function my_custom_user_management_page() {
    add_menu_page(
        'QUẢN LÝ NGƯỜI DÙNG',
        'Quản lý người dùng',
        'manage_options',
        'custom-user-management',
        'my_custom_user_management_page_html',
        'dashicons-admin-users',
        6
    );
}

function my_custom_certificate_management_page() {
    add_menu_page(
        'QUẢN LÝ CHỨNG CHỈ',
        'Quản lý chứng chỉ',
        'manage_options', 
        'custom-certificate-management', 
        'my_custom_certificate_management_page_html', 
        'dashicons-awards',
        7 
    );
}

function my_register_custom_menus() {
    my_custom_user_management_page();
    my_custom_certificate_management_page();
}
add_action('admin_menu', 'my_register_custom_menus');
?>