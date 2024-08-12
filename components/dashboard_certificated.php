<?php
/**
 * Plugin Name: Certificates Admin Page
 * Description: Adds an admin page to display issued certificates with pagination and date filtering.
 * Version: 1.0
 * Author: Your Name
 */

// Add the admin menu
function add_certificates_menu() {
    add_menu_page(
        'QUẢN LÝ CHỨNG CHỈ ĐÃ CẤP',   // Tiêu đề của trang
        'Quản lý chứng chỉ đã cấp',               // Tiêu đề của menu
        'manage_options',             // Quyền truy cập
        'certificates',               // Slug của trang
        'display_certificates_page',  // Hàm hiển thị nội dung
        'dashicons-awards',           // Icon cho menu
        6                             // Vị trí của menu
    );
}
add_action('admin_menu', 'add_certificates_menu');

// Hàm để hiển thị nội dung của trang
function display_certificates_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'certificated';

    // Handle pagination
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 5;  // Number of certificates per page
    $offset = ($paged - 1) * $per_page;

    // Query certificate data from the database
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT Id, TemplateSVG, createdAt FROM $table_name ORDER BY createdAt DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    // Total number of certificates
    $total_certificates = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_certificates / $per_page);

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Danh Sách Chứng Chỉ Đã Cấp</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Chứng chỉ</th>
                    <th scope="col">Ngày cấp</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results) : ?>
                    <?php foreach ($results as $row) : ?>
                        <tr>
                            <td><?php echo esc_html($row->Id); ?></td>
                            <td>
                                <?php
                                // Display SVG
                                echo '<div class="svg-container">';
                                echo $row->TemplateSVG;  // Insert the SVG directly from the database
                                echo '</div>';
                                ?>
                            </td>
                            <td><?php echo esc_html(date('d-m-Y', strtotime($row->createdAt))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3">Không có chứng chỉ nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="tablenav bottom">
            <div class="pagination-links">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'total' => $total_pages,
                    'current' => $paged,
                    'before_page_number' => '<span class="page-number">',
                    'after_page_number' => '</span>',
                    'prev_text' => __('« Trang trước'),
                    'next_text' => __('Trang sau »'),
                    'end_size' => 1,  // Display first and last pages
                    'mid_size' => 2,   // Display number of pages around current page
                    'type' => 'plain', // Format of pagination links
                ));
                ?>
            </div>
            <br class="clear">
        </div>
    </div>

    <style>
    /* CSS for pagination */
    .tablenav.bottom {
        text-align: center; /* Center pagination */
    }

    .pagination-links {
        display: inline-block; /* Center pagination links */
    }

    .pagination-links .page-number {
        display: inline-block;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 50%;
        background-color: #0073aa; /* Blue background */
        color: #fff;
        text-decoration: none;
        text-align: center;
        font-size: 14px;
    }

    .pagination-links .page-number:hover {
        background-color: #005177; /* Darker blue on hover */
    }

    .pagination-links .page-number.current {
        background-color: #005177; /* Darker blue for current page */
    }
    </style>
    <?php
}

