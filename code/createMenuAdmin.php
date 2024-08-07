<?php 

// Thêm menu trang quản lý người dùng vào Admin Dashboard
function my_custom_user_management_page() {
    add_menu_page(
        'QUẢN LÝ NGƯỜI DÙNG',   // Tiêu đề trang
        'Quản lý người dùng',   // Tiêu đề menu
        'manage_options',       // Khả năng cần thiết để truy cập trang này
        'custom-user-management', // Slug của menu
        'my_custom_user_management_page_html', // Hàm hiển thị nội dung của trang
        'dashicons-admin-users', // Biểu tượng menu
        6                       // Vị trí của menu
    );
}
// Thêm menu trang quản lý chứng chỉ vào Admin Dashboard
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

// Thêm menu trang tra cứu thông tin vào Admin Dashboard
// function my_custom_search_management_page() {
//     add_menu_page(
//         'TRA CỨU',   
//         'Tra cứu',  
//         'manage_options',
//         'custom-search-management',
//         'my_custom_search_management_page_html',
//         'dashicons-search',
//         8
//     );
// }

// Đăng ký các trang quản lý với WordPress
function my_register_custom_menus() {
    my_custom_user_management_page();
    my_custom_certificate_management_page();
    //my_custom_search_management_page();
}
add_action('admin_menu', 'my_register_custom_menus');

?>