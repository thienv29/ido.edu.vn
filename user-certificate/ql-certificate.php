<?php

function custom_certification_menu()
{
    add_menu_page(
        __('Quản lý chứng chỉ', 'textdomain'), // Tên của trang menu
        __('Quản lý chứng chỉ', 'textdomain'), // Tên hiển thị trên trang dashboard
        'manage_options', // Quyền cần thiết để truy cập
        'certification-management', // Slug của trang
        'certification_management_page', // Function sẽ thực thi khi người dùng truy cập vào trang
        'dashicons-awards', // Icon của menu
        21 // Thứ tự của menu trong trang dashboard, thay đổi 25 thành số khác nếu cần thiết
    );
}

// Thêm action để gọi function custom_certification_menu
add_action('admin_menu', 'custom_certification_menu');


function certification_management_page()
{
    global $wpdb;

    echo '<style>
            .template-image svg {
                width: 100px;
                height: 100px;
            }
          </style>';
    echo '<style>
        .form-insert {
            margin-left: 250px;
        }
        </style>';

    echo '<h1>Quản lý chứng chỉ</h1>';
    echo '<div style="margin-bottom: 20px;"><button id="add-certification-btn">Thêm chứng chỉ mới</button></div>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="width: 20px;">Id</th>';
    echo '<th>Tên chứng chỉ</th>';
    echo '<th>Template</th>';
    echo '<th>Trạng thái</th>';
    echo '<th>Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $certifications = $wpdb->get_results("SELECT Id, Name, TemplateSvg, isDeleted FROM certificates", ARRAY_A);

    foreach ($certifications as $certification) {
        echo '<tr>';
        echo '<td style="width: 20px;">' . $certification['Id'] . '</td>';
        echo '<td>' . $certification['Name'] . '</td>';
        echo '<td class="template-image">' . $certification['TemplateSvg'] . '</td>';
        echo '<td>' . ($certification['isDeleted'] == 1 ? 'Có' : 'Không') . '</td>';
        echo '<td>';
        echo '<button id="edit-certification-btn" data-id-2="' . $certification['Id'] . '">Edit</button>'; // Nút Sửa chứng chỉ
        echo '<button class="delete-btn" data-id="' . $certification['Id'] . '">Delete</button>'; // Nút Xoá chứng chỉ
        echo '</td>';
        echo '</tr>';
    }


    echo '</tbody>';
    echo '</table>';

    echo '<div id="certification-form-container" class="form-insert"></div>'; // Container for the form

    //------------------button Xoá-------------------------
    // JavaScript for handling delete functionality
    echo '<script>
     document.addEventListener("click", function(e) {
         if (e.target && e.target.classList.contains("delete-btn")) {
             if (confirm("Bạn muốn xoá chứng chỉ này?")) {
                 let certificationId = e.target.getAttribute("data-id");
                 // AJAX call to update database
                 jQuery.post(
                     ajaxurl,
                     {
                         action: "update_certification_status",
                         certification_id: certificationId
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
}

// Add AJAX action for updating certification status
add_action('wp_ajax_update_certification_status', 'update_certification_status');
function update_certification_status()
{
    global $wpdb;

    $certificationId = $_POST['certification_id'];

    // Update the database field isDeleted from 1 to 0 for the given certification ID
    $wpdb->update('certificates', array('isDeleted' => 0), array('Id' => $certificationId));

    // You can add validations or return a response as needed
    wp_die();
}
//-----------------------------Thêm chứng chỉ mới----------------------------------
echo '<script>
    document.addEventListener("click", function(e) {
        if (e.target && e.target.id === "add-certification-btn") {
            let form = `
                <form id="add-certification-form">
                    <label for="certification-name">Tên chứng chỉ:</label><br>
                    <input type="text" id="certification-name" name="certification-name"><br><br>

                     <label for="svg-upload">Chọn file SVG:</label><br>
                    <input type="file" id="svg-upload" name="svg-upload"><br><br>

                    <label for="template-svg-content">Template (Paste content của file SVG):</label><br>
                    <textarea id="template-svg-content" name="template-svg-content" style="height: 200px; width: 200px"></textarea><br><br>

                    <label for="status">Trạng thái:</label><br>
                    <select id="status" name="status">
                        <option value="1">Có</option>
                        <option value="0">Không</option>
                    </select><br><br>

                    <button type="submit">Thêm chứng chỉ</button>
                </form>
            `;
            document.getElementById("certification-form-container").innerHTML = form;
        }
        
    });
</script>';


///---hàm bắt sự kiện khi nhấn nút thêm-----------
echo '<script>
document.addEventListener("submit", function(e) {
    if (e.target && e.target.id === "add-certification-form") {
        e.preventDefault(); // Prevent the default form submission

        let certificationName = document.getElementById("certification-name").value;
        let svgFile = document.getElementById("svg-upload").files[0];

        let reader = new FileReader();
        reader.onload = function(e) {
            let templateSvgContent = e.target.result;

            let status = document.getElementById("status").value;

            // AJAX call to send data to the server
            jQuery.post(
                ajaxurl,
                {
                    action: "add_new_certification",
                    certification_name: certificationName,
                    template_svg_content: templateSvgContent,
                    status: status
                },
                function(response) {
                    // Handle response as needed
                }
            );
        };

        reader.readAsText(svgFile);
    }
});

</script>';

add_action('wp_ajax_add_new_certification', 'add_new_certification');

function add_new_certification()
{
    global $wpdb;

    $certificationName = $_POST['certification_name'];
    $templateSvgContent = stripslashes($_POST['template_svg_content']); // Remove backslashes from the SVG content
    $status = $_POST['status'];

    // Insert new certification data into the database
    $wpdb->insert('certificates', array(
        'Name' => $certificationName,
        'TemplateSvg' => $templateSvgContent,
        'isDeleted' => $status
    ));

    wp_die();
}


//------------------update chứng chỉ-------------------------

add_action('wp_ajax_get_certification_details', 'get_certification_details');

function get_certification_details()
{
    global $wpdb;

    $certificationId = $_POST['certification_id'];

    // Fetch certification details from the database based on certification ID
    $certification = $wpdb->get_row($wpdb->prepare("SELECT * FROM certificates WHERE Id = %d", $certificationId), ARRAY_A);

    // Return the certification details as a response
    if ($certification) {
        wp_send_json_success($certification); // Return the SVG certificate
    } else {
        wp_send_json_error('Không tìm thấy chứng chỉ.'); // Return an error if certification is not found
    }

    wp_die(); // End the AJAX request
}


echo '<script>
    document.addEventListener("click", function(e) {
        if (e.target && e.target.id === "edit-certification-btn") {
            let certificationId = e.target.getAttribute("data-id-2");

            // AJAX call to fetch certification details
            jQuery.post(
                ajaxurl,
                {
                    action: "get_certification_details",
                    certification_id: certificationId
                },
                function(response) {
                    // Populate form fields with fetched data
                    document.getElementById("certification-id").value = response.Id;
                    document.getElementById("certification-name").value = response.Name;
                    document.getElementById("template-svg-content").value = response.TemplateSvg;
                    document.getElementById("status").value = response.isDeleted;
                    console.log(response);
                }
            );

            let form = `
                <form id="edit-certification-form">
                    <input type="hidden" id="certification-id" name="certification-id">
                    <label for="certification-name">Tên chứng chỉ:</label><br>
                    <input type="text" id="certification-name" name="certification-name"><br><br>
                    <label for="template-svg-content">Template (Paste content của file SVG):</label><br>
                    <textarea id="template-svg-content" name="template-svg-content" style="height: 200px; width: 200px"></textarea><br><br>
                    <label for="status">Trạng thái:</label><br>
                    <select id="status" name="status">
                        <option value="1">Có</option>
                        <option value="0">Không</option>
                    </select><br><br>
                    <button type="submit">Cập nhật</button>
                </form>
            `;

            document.getElementById("certification-form-container").innerHTML = form;
        }
    });
</script>';


// Add AJAX action for updating certification data
add_action('wp_ajax_update_certification', 'update_certification');

function update_certification()
{
    global $wpdb;

    $certificationId = $_POST['certification-id'];
    $certificationName = $_POST['certification-name'];
    $templateSvgContent = stripslashes($_POST['template-svg-content']);
    $status = $_POST['status'];

    // Update the certification data in the database
    $wpdb->update(
        'certificates',
        array(
            'Name' => $certificationName,
            'TemplateSvg' => $templateSvgContent,
            'isDeleted' => $status
        ),
        array('Id' => $certificationId)
    );

    wp_die();
}
