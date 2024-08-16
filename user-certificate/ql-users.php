<?php

function custom_admin_menu()
{
    add_menu_page(
        __('Quản lý người dùng', 'textdomain'), // Tên của trang menu
        __('Quản lý người dùng', 'textdomain'), // Tên hiển thị trên trang dashboard
        'manage_options', // Quyền cần thiết để truy cập
        'user-management', // Slug của trang
        'user_management_page', // Function sẽ thực thi khi người dùng truy cập vào trang
        'dashicons-admin-users', // Icon của menu
        20 // Thứ tự của menu trong trang dashboard
    );
}

// Thêm action để gọi function custom_admin_menu
add_action('admin_menu', 'custom_admin_menu');


function user_management_page()
{

    echo '<style>
    .template-image svg {
        width: 300px;
        height: 300px;
    }
    .select-certificate {
      float: right;
      margin-right: 10px;
    }
    .pagination{
        font-size: 20px;
        text-align: center;
    }
      </style>';


    global $wpdb;

    echo '<h1>Quản lý người dùng</h1>';

    echo '<input type="text" id="user-search" placeholder="Nhập tên hoặc email....">';

    echo '<select id="certificate-filter" class="select-certificate">

    <option value="">Tất cả chứng chỉ</option>';
    $certificateIds = $wpdb->get_col("SELECT DISTINCT CertificateId FROM wp_user_2");
    foreach ($certificateIds as $certId) {
        echo '<option value="' . $certId . '"> chứng chỉ ' . $certId . '</option>';
    }

    echo '</select>';

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="width: 20px;">id</th>';
    echo '<th>Tên</th>';
    echo '<th>SĐT</th>';
    echo '<th>Email</th>';
    echo '<th>Chứng chỉ</th>';
    echo '<th>Hoạt động</th>';
    echo '<th>Hành động</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody id="user-list">';

    $usersPerPage = 5;
    $currentPage = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

    $totalUsers = $wpdb->get_var("SELECT COUNT(Id) FROM wp_user_2");
    $totalPages = ceil($totalUsers / $usersPerPage);

    $offset = ($currentPage - 1) * $usersPerPage;

    $users = $wpdb->get_results("SELECT Id, Name, Phone, Email, CertificateId, isCertificate, isDelete FROM wp_user_2 LIMIT $offset, $usersPerPage", ARRAY_A);


    foreach ($users as $user) {
        echo '<tr>';
        echo '<td style="width: 20px;">' . $user['Id'] . '</td>';
        echo '<td>' . $user['Name'] . '</td>';
        echo '<td>' . $user['Phone'] . '</td>';
        echo '<td>' . $user['Email'] . '</td>';
        echo '<td>' . $user['CertificateId'] . '</td>';
        echo '<td>' . ($user['isDelete'] == 1 ? 'Có' : 'Không') . '</td>';
        echo '<td>';
        echo '<button class="delete-btn" data-id="' . $user['Id'] . '">Xoá</button>';
        echo '<button class="certificate-btn" data-certificate-id="' . $user['CertificateId'] . '"
        data-id-user="' . $user['Id'] . '">Cấp chứng chỉ</button>';
        echo '<button class="delete-certificate" data-id-certificate="' . $user['Id'] . '">Huỷ chứng chỉ</button>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    echo '<div class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?page=user-management&paged=' . $i . '">' . $i . '</a> ';
    }
    echo '</div>';

    echo '<div id="certificate-svg-container" class="template-image"></div>'; // Container for the form
    echo '<div id="svg-container-2" class="template-image"></div>'; // Container for the form

}


//Bắt sự kiện submit form
add_action('elementor_pro/forms/new_record', function ($record, $handler) {
    $form_name = $record->get_form_settings('form_name');
    if ('form_user' !== $form_name) {
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
    $certificateId = intval($fields['certificate']);

    // Lưu dữ liệu vào cơ sở dữ liệu
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_2';
    $wpdb->insert(
        $table_name,
        [
            'Name' => $name,
            'Phone' => $phone,
            'Email' => $email,
            'CertificateId' => $certificateId,
            'isCertificate' => 1,
            'isDelete' => 1
        ]
    );
}, 10, 2);

//----------------------Xoá mềm user---------------------------------
echo '<script>
    document.addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("delete-btn")) {
            if (confirm("Bạn muốn xoá người dùng này?")) {
                let userId = e.target.getAttribute("data-id");
                // AJAX call to update database
                jQuery.post(
                    ajaxurl,
                    {
                        action: "update_user_delete_status",
                        user_id: userId
                    },
                    function(response) {
                        // Handle response if needed
                        console.log(response);
                    }
                );
            }
        }
    });
</script>';

add_action('wp_ajax_update_user_delete_status', 'update_user_delete_status');
function update_user_delete_status()
{
    $userId = $_POST['user_id'];
    global $wpdb;
    $wpdb->update(
        'wp_user_2',
        array('isDelete' => 0),
        array('Id' => $userId)
    );
    wp_die(); // Always use wp_die at the end of your AJAX callback function
}

//----------------------Xoá mềm chứng chỉ-------------------------------
echo '<script>
    document.addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("delete-certificate")) {
            if (confirm("Bạn muốn xoá chứng chỉ này?")) {
                let userId = e.target.getAttribute("data-id-certificate");
                // AJAX call to update database
                jQuery.post(
                    ajaxurl,
                    {
                        action: "update_certificate_status",
                        user_id: userId
                    },
                    function(response) {
                        // Handle response if needed
                        console.log(response);
                    }
                );
            }
        }
    });
</script>';

add_action('wp_ajax_update_certificate_status', 'update_certificate_status');
function update_certificate_status()
{
    $userId = $_POST['user_id'];
    global $wpdb;

    $wpdb->update(
        'wp_user_2',
        array(
            'isCertificate' => 0,
            'isDelete' => 0
        ),
        array('Id' => $userId)
    );

    // Cập nhật bảng wp_certificate_user
    $wpdb->update(
        'wp_certificate_user',
        array(
            'isDelete' => 0
        ),
        array('UserId' => $userId)
    );


    wp_die();
}


//----------------Cấp chứng chỉ-----------------------------
echo '<script>
document.addEventListener("click", function(e) {
    if (e.target && e.target.classList.contains("certificate-btn")) {
        let certificateId = e.target.getAttribute("data-certificate-id");
        let userId = e.target.getAttribute("data-id-user");
        
        jQuery.post(
            ajaxurl,
            {
                action: "get_certificate_template_svg",
                certificate_id: certificateId,
                user_Id: userId
            },
            function(response) {          
                document.getElementById("svg-container-2").innerHTML = response;

            }
        );
    }
        

});

</script>';

add_action('wp_ajax_get_certificate_template_svg', 'get_certificate_template_svg');
add_action('wp_ajax_nopriv_get_certificate_template_svg', 'get_certificate_template_svg');

function get_certificate_template_svg()
{
    $certificateId = $_POST['certificate_id'];
    $userId = $_POST['user_Id'];
    global $wpdb;

    $templateSvg = $wpdb->get_var("SELECT TemplateSvg FROM certificates WHERE Id = $certificateId");

    $data = $wpdb->get_row("
        SELECT wp_user_2.Name AS user_name, certificates.Name AS certificate_name
        FROM wp_user_2
        JOIN certificates ON wp_user_2.CertificateId = certificates.Id
        WHERE wp_user_2.Id = $userId
    ", ARRAY_A);

    if ($data) {
        $name = $data['user_name'];
        $role = $data['certificate_name'];

        $templateSvg = str_replace('name', $name, $templateSvg);
        $templateSvg = str_replace('role', $role, $templateSvg);

        echo $templateSvg;
        // Save the modified SVG into the database
        $wpdb->insert('wp_certificate_user', array(
            'UserId' => $userId,
            'TemplateSvg' => $templateSvg,
            'isDelete' => 1
        ));

        wp_die();
    } else {
        echo "Data not found for the given ID.";
    }
}

add_action('wp_ajax_save_certificate_data', 'save_certificate_data');

function save_certificate_data()
{
    global $wpdb;

    $userId = $_POST['user_id'];
    $templateSvg = $_POST['template_svg'];

    $wpdb->insert('wp_certificate_user', array(
        'UserId' => $userId,
        'TemplateSvg' => $templateSvg,
        'isDelete' => 1
    ));

    wp_die();
}
//---------------------tìm kiếm----------------------------------

echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("user-search");

    searchInput.addEventListener("input", function() {
        const searchTerm = this.value;

        jQuery.post(
            ajaxurl,
            {
                action: "user_search",
                search_query: searchTerm
            },
            function(response) {
                document.getElementById("user-list").innerHTML = response;
            }
        );
    });
});


</script>';



add_action('wp_ajax_user_search', 'user_search');

function user_search()
{
    $searchQuery = sanitize_text_field($_POST['search_query']);

    global $wpdb;

    $sql = "SELECT Id, Name, Phone, Email, CertificateId, isCertificate, isDelete FROM wp_user_2 WHERE Name LIKE '%%%s%%' OR Email LIKE '%%%s%%'";
    $placeholders = array($searchQuery, $searchQuery);


    $users = $wpdb->get_results($wpdb->prepare($sql, $placeholders), ARRAY_A);

    foreach ($users as $user) {
        echo '<tr>';
        echo '<td style="width: 20px;">' . $user['Id'] . '</td>';
        echo '<td>' . $user['Name'] . '</td>';
        echo '<td>' . $user['Phone'] . '</td>';
        echo '<td>' . $user['Email'] . '</td>';
        echo '<td>' . $user['CertificateId'] . '</td>';
        echo '<td>' . ($user['isDelete'] == 1 ? 'Có' : 'Không') . '</td>';
        echo '<td>';
        echo '<button class="delete-btn" data-id="' . $user['Id'] . '">Xoá</button>';
        echo '<button class="certificate-btn" data-certificate-id="' . $user['CertificateId'] . '" data-id-user="' . $user['Id'] . '">Cấp chứng chỉ</button>';
        echo '<button class="delete-certificate" data-id-certificate="' . $user['Id'] . '">Huỷ chứng chỉ</button>';
        echo '</td>';
        echo '</tr>';
    }

    wp_die();
}
//--------------lọc-------------------------
echo '<script>
document.addEventListener("change", function(e) {
    if (e.target && e.target.id === "certificate-filter") {
        let certId = e.target.value;
        jQuery.post(
            ajaxurl,
            {
                action: "user_filter_by_certificate",
                certificate_id: certId
            },
            function(response) {
                document.getElementById("user-list").innerHTML = response;
            }
        );
    }
});

</script>';

add_action('wp_ajax_user_filter_by_certificate', 'user_filter_by_certificate');

function user_filter_by_certificate()
{
    $certificateId = $_POST['certificate_id'];
    global $wpdb;
    $sql = "SELECT Id, Name, Phone, Email, CertificateId, isCertificate, isDelete FROM wp_user_2 WHERE CertificateId = %d";
    $filteredUsers = $wpdb->get_results($wpdb->prepare($sql, $certificateId), ARRAY_A);
    foreach ($filteredUsers as $user) {
        echo '<tr>';
        echo '<td style="width: 20px;">' . $user['Id'] . '</td>';
        echo '<td>' . $user['Name'] . '</td>';
        echo '<td>' . $user['Phone'] . '</td>';
        echo '<td>' . $user['Email'] . '</td>';
        echo '<td>' . $user['CertificateId'] . '</td>';
        echo '<td>' . ($user['isDelete'] == 1 ? 'Có' : 'Không') . '</td>';
        echo '<td>';
        echo '<button class="delete-btn" data-id="' . $user['Id'] . '">Xoá</button>';
        echo '<button class="certificate-btn" data-certificate-id="' . $user['CertificateId'] . '" data-id-user="' . $user['Id'] . '">Cấp chứng chỉ</button>';
        echo '<button class="delete-certificate" data-id-certificate="' . $user['Id'] . '">Huỷ chứng chỉ</button>';
        echo '</td>';
        echo '</tr>';
    }
    wp_die();
}
