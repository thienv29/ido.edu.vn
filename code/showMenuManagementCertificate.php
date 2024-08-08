<?php 
//Hàm hiển thị nối dung trang quản lý chứng chỉ
function my_custom_certificate_management_page_html() {
	// Kiểm tra quyền truy cập
    if (!current_user_can('manage_options')) {
        return;
    }

    // Lấy danh sách chứng chỉ từ database
    global $wpdb;
	$certificate_table = $wpdb->prefix . 'CERTIFICATE';
	$certificates = $wpdb->get_results("
        SELECT @rownum := @rownum + 1 AS rownum, c.*
        FROM $certificate_table c
        CROSS JOIN (SELECT @rownum := 0) AS r
        WHERE isDeleted != 1
    ");

    // Hiển thị danh sách chứng chỉ
    ?>
    <div class="wrap">
        <h1 id="title" style="cursor: pointer">Quản lý Chứng chỉ</h1>
        <button id="add" style="color: #fff; background-color: #007bff; border: none; padding: 6px 12px;margin-top: 15px; cursor: pointer; border-radius: .25rem; margin-bottom: 8px">Thêm chứng chỉ</button>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên chứng chỉ</th>
                    <th>Template</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certificates as $certificate) { ?>
                <tr>
                    <td><?php echo esc_html($certificate->rownum); ?></td>
                    <td><?php echo esc_html($certificate->Name); ?></td>
                    <td style="display: block; width: 200px; height: 200px"><?php echo $certificate->TemplateSVG; ?></td>	
                    <td>
                        <select name="action" class="certificate-action" data-id="<?php echo esc_html($certificate->Id); ?>">
                            <option>Tùy chọn</option>
                            <option value="delete">Xóa</option>
                            <option value="update">Sửa</option>
                        </select>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div id="certificationPopupUpdate" style="display:none;">
            <h1>Cập nhật chứng chỉ</h1>
            <div>
                <h2>Tên chứng chỉ: <input id="certificateName"/></h2>
                <h2>Nội dung chứng chỉ</h2>
                <div style="display: flex; gap: 10px; align-items: center">
                    <textarea id="certificateContentUpdate" style="width: 60%; height: 500px"></textarea>
                    <div id="reviewTemplateUpdate" style="width: 40%; height: 500px;"></div>
                </div>  
            </div>
            <button style="color: #212529; background-color: #f8f9fa; padding: 6px 12px; border: none; cursor: pointer; border-radius: .25rem" id="closePopupUpdate">Đóng</button>
            <button style="color: #fff; background-color: #007bff; border: none; padding: 6px 12px; cursor: pointer; border-radius: .25rem; margin-left: 6px" id="updateCertificate">Cập nhật</button>
        </div>
        <div id="certificationPopupAdd" style="display:none;">
            <h1>Thêm chứng chỉ</h1>
            <div style="margin-bottom: 20px">
                <h2>Tên chứng chỉ: <input id="certificateNameAdd"/></h2>
                <select id="selectType">
                    <option value="">Chọn kiểu</option>
                    <option value="pasteContent">Dán nội dung</option>
                    <option value="uploadFile">Tải file</option>
                </select>
                <div style="display: flex; gap: 10px">
                    <div style="width: 60%">
                        <div id="pasteContent" style="display: none">
                            <h2>Nội dung chứng chỉ (Dán nội dung chứng chỉ vào ô bên dưới)</h2>
                            <textarea id="certificateContentAdd" style="width: 100%; height: 500px"></textarea>
                        </div>
                        <div id="uploadFile" style="display: none">
                            <label>Tải file (Yêu cầu phải có biến {name} và {date} trong file)</label>
                            <input type="file" id="fileTemplate" accept=".svg">
                        </div>
                    </div>
                    <div id="reviewTemplateAdd" style="width: 40%; padding-top: 50px; display: none"></div>
                </div>
            </div>
            <button style="color: #212529; background-color: #f8f9fa; padding: 6px 12px; border: none; cursor: pointer; border-radius: .25rem" id="closePopupAdd">Đóng</button>
            <button style="color: #fff; background-color: #007bff; border: none; padding: 6px 12px; cursor: pointer; border-radius: .25rem; margin-left: 6px" id="addCertificate">Thêm</button>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {

            $('#title').click(function() {
                location.reload()
            })

            let currentCertificateId = null;

            $('.certificate-action').change(function() {
                const action = $(this).val();
                currentCertificateId = $(this).data('id');
                
                if (action === "delete") {
                    if (confirm("Bạn có chắc chắn muốn xóa chứng chỉ này không?")) {
                        $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'delete_certificate',
                            id: currentCertificateId
                        },
                        success: function(response) {
                            alert("Đã xóa chứng chỉ thành công.")
                            location.reload()
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi xóa chứng chỉ.');
                        }
                    });
                    }  
                }

                if (action === "update") {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_certificate',
                            id: currentCertificateId
                        },
                        success: function(response) {
                            $('#certificateName').val(response.data.Name)
                            $('#certificateContentUpdate').val(response.data.TemplateSVG);
                            $('#reviewTemplateUpdate').html($('#certificateContentUpdate').val());
                            $('#certificationPopupUpdate').css('display', 'block');
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi cập nhật chứng chỉ.');
                        }
                    });
                }
            })

            $('#certificateContentUpdate').on('input', function() {
                $('#reviewTemplateUpdate').html($(this).val());
            })

            $('#fileTemplate').change(function() {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                       $('#reviewTemplateAdd').html(e.target.result)
                    };
                    reader.readAsText(file); // Hoặc readAsDataURL(file) nếu cần đọc như URL dữ liệu
                }
            })

            $('#updateCertificate').click(function() {
                // Lấy giá trị từ textarea và input
                const textareaValue = $('#certificateContentUpdate').val();
                const certificateName = $('#certificateName').val();

                $.ajax({
                    url: ajaxurl, 
                    type: 'POST',
                    data: {
                        action: 'update_certificate', 
                        id: currentCertificateId, 
                        name: certificateName, 
                        content: textareaValue 
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Cập nhật chứng chỉ thành công.');
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + response.data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ', textStatus, errorThrown);
                        alert('Có lỗi xảy ra khi cập nhật chứng chỉ.');
                    }
                });
            });

            $('#add').click(function() {
                $('#certificationPopupAdd').css('display', 'block');
            })

            $('#certificateContentAdd').on('input', function() {
                $('#reviewTemplateAdd').html($(this).val())
            })

            $('#addCertificate').click(function() {
                // Lấy giá trị từ textarea và input
                const certificateName = $('#certificateNameAdd').val();
                const textareaValue = $('#certificateContentAdd').val();
                const fileInput = $('#fileTemplate')[0]
                const type = $('#selectType').val()
                
                if (!certificateName) {
                    alert('Vui lòng nhập tên chứng chỉ!')
                    return
                }

                if (type === 'pasteContent') {
                    if (!textareaValue) {
                        alert('Vui lòng nhập nội dung!')
                        return
                    }
                    saveCertificate(certificateName, textareaValue)
                }

                if (type === 'uploadFile') {
                    const fileTemplate = fileInput.files[0]
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        saveCertificate(certificateName, e.target.result)
                    };
                    reader.readAsText(fileTemplate);
                }

                function saveCertificate(name, content) {
                    $.ajax({
                    url: ajaxurl, 
                    type: 'POST',
                    data: {
                        action: 'add_certificate', 
                        name, 
                        content,
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Thêm chứng chỉ thành công.');
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + response.data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ', textStatus, errorThrown);
                        alert('Có lỗi xảy ra khi thêm chứng chỉ.');
                    }
                    });
                }

            })

            $("#selectType").change(function() {
                const type = $(this).val()
                if (type === "pasteContent") {
                    $("#pasteContent").css('display', 'block')
                    $("#uploadFile").css('display', 'none')
                    $('#reviewTemplateAdd').css('display', 'block')
                } else if (type === "uploadFile") {
                    $("#uploadFile").css('display', 'block')
                    $("#pasteContent").css('display', 'none')
                    $('#reviewTemplateAdd').css('display', 'block')
                } else {
                    $("#pasteContent").css('display', 'none')
                    $("#uploadFile").css('display', 'none')
                    $('#reviewTemplateAdd').css('display', 'none')
                }

            })

            $('#closePopupUpdate').click(function() {
                $('#certificationPopupUpdate').css('display', 'none');
            });

            $('#closePopupAdd').click(function() {
                $('#certificationPopupAdd').css('display', 'none');
            });
        })
    </script>
    <?php
} 

// Thêm action để xử lý yêu cầu AJAX
add_action('wp_ajax_delete_certificate', 'handle_delete_certificate');
add_action('wp_ajax_update_certificate', 'handle_update_certificate');
add_action('wp_ajax_add_certificate', 'handle_add_certificate');
?>