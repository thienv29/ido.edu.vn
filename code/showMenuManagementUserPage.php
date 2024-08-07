<?php 
// Hàm hiển thị nội dung của trang quản lý người dùng
function my_custom_user_management_page_html() {
    // Kiểm tra quyền truy cập
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <div class="wrap">
        <h1>Quản lý người dùng</h1>
        <div style="margin: 20px 0;">
            <div class="d-flex" style="gap: 50px">
                <div class="d-flex" style="gap: 12px">
                    <label style="font-size: 18px">Tìm kiếm người dùng:</label>
                    <input style="width: 300px" type="text" id="txtUsername" placeholder="Nhập tên người dùng...">
                    <button class="btnSearch" id="search">Tìm</button>
                </div>
                <div style="display: flex; align-items: center; gap: 12px">
                    <label style="font-size: 18px">Lọc người dùng:</label>
                    <select id="filter">
                        <option value="">Lựa chọn phương thức</option>
                        <option value="date">Lọc theo ngày cấp chứng chỉ</option>
                        <option value="dateSubmit">Lọc theo ngày gửi</option>
                        <option value="certificate">Lọc theo chứng chỉ</option>
                    </select>
                    <div id="formSelectDate" style="display: none">
                        <label>Chọn ngày</label>
                        <input type="date" id="datePicker">
                    </div>
                    <div id="formSelectCertificate" style="display: none">
                        <label>Chọn chứng chỉ</label>
                        <select id="certificateType">
                            <option value="">Chọn</option>
                            <option value="1">Tình nguyện viên</option>
                            <option value="2">Nhà tài trợ</option>
                            <option value="3">Thành viên</option>
                        </select>
                    </div>
                    <button id="btnFilter" class="btnFilter">Lọc</button>
                </div>
            </div>
        </div>
        <div id="loading-container">
            <img src="images/loading.gif" alt="Loading..." class="loading-gif"/>
        </div>
        <div id="result"></div>
        <div id="pagination"></div>

        <div id="certificationPopup" style="display:none;">
            <h2>Thông tin chứng chỉ</h2>
            <div id="certificateContent" style="width: 50%"></div>
            <button class="btnClose" id="closePopup">Đóng</button>
            <button class="btnSave" id="saveCertificate">Lưu</button>
        </div>

        <style>

            .d-flex {
                display: flex; 
                align-items: center;
            }

            #loading-container {
                width: 100%;
                height: 70vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #f0f0f0;
            }

            .loading-gif {
                width: 25px;
                height: 25px;
            }

            #restore {
                width: 123px;
                height: 30px
            }

            .btnClose {
                color: #212529; 
                background-color: #f8f9fa; 
                padding: 6px 12px; 
                border: none; 
                cursor: pointer; 
                border-radius: .25rem
            }

            .btnSave {
                color: #fff; 
                background-color: #007bff; 
                border: none; 
                padding: 6px 12px; 
                cursor: pointer; 
                border-radius: .25rem; 
                margin-left: 6px
            }

            .btnFilter, .btnSearch {
                color: #fff; 
                background-color: #007bff; 
                border: none; 
                padding: 6px 12px; 
                cursor: pointer; 
                border-radius: .25rem;
            }

            select {
                width: 130px;
            }

            #pagination {
                margin-top: 14px;
                text-align: center;
            }

            #pagination button {
                margin: 0 5px;
            }

            .page-btn {
                padding: 10px;
                margin: 2px;
                border: 1px solid #ccc;
                background-color: #fff;
                cursor: pointer;
                border-radius: 4px;
            }

            .page-btn.active {
                background-color: #007bff;
                color: #fff;
                border-color: #007bff;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                window.onload = function() {
                   getListUsers();
                };

                const listFunctions = {
                    'getListUsers': getListUsers,
                    'getListUsersByName': getListUsersByName,
                    'getListUsersByCertificate': getListUsersByCertificate,
                    'getListUsersByDateSubmit': getListUsersByDateSubmit,
                    'getListUsersByDateCertified': getListUsersByDateCertified,
                }

                function getListUsers(ref, page = 1) {
                    showLoading()
                    $.ajax({
                        url: ajaxurl, 
                        type: 'POST',
                        data: {
                            action: 'get_list_users', 
                            page: page,
                        },
                        success: function(response) {
                            const users = response.data.users;
                            const totalPages = response.data.total_pages;
                            if (users.length !== 0) {
                                showResult(users, page, totalPages, "getListUsers", 'list')
                            }
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                        },
                        complete: function() {
                            hideLoading();
                        }
                    });
                }

                function getListUsersByName(name, page = 1) {
                    showLoading()
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_users_by_name',
                            name,
                            page
                        },
                        success: function(response) {
                            $('#txtUsername').val('')
                            const users = response.data.users;
                            const totalPages = response.data.total_pages;
                            if (users.length !== 0) {
                                showResult(users, page, totalPages, "getListUsersByName", name)
                            } else {
                                $('#result').html('<p>Không tồn tại người dùng với tên này</p>');
                                $('#pagination').html('')
                            }
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                        },
                        complete: function() {
                            hideLoading()
                        }
                    });
                }

                function getListUsersByCertificate(id, page = 1) {
                    showLoading()
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_users_by_certificate',
                            idCertificate: id,
                            page
                        },
                        success: function(response) {
                            const users = response.data.users;
                            const totalPages = response.data.total_pages;
                            if (users.length !== 0) {
                                showResult(users, page, totalPages, "getListUsersByCertificate", id)
                            } else {
                                $('#result').html('<p>Không tồn tại người dùng với chứng chỉ này</p>');
                                $('#pagination').html('')
                            }
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                        },
                        complete: function() {
                            hideLoading()
                        }
                    });
                }

                function getListUsersByDateSubmit(date, page = 1) {
                    showLoading()
                        $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_users_by_date_submit',
                            date: date,
                            page
                        },
                        success: function(response) {
                            const users = response.data.users;
                            const totalPages = response.data.total_pages;
                            if (users.length !== 0) {
                                showResult(users, page, totalPages, "getListUsersByDateSubmit", date)
                            } else {
                                $('#result').html('<p>Không có người dùng nộp yêu cầu trong ngày này.</p>');
                                $('#pagination').html('')
                            }

                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                        },
                        complete: function() {
                            hideLoading()
                        }
                    });
                }

                function getListUsersByDateCertified(date, page = 1) {
                    showLoading()
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_users_by_date',
                            date: date,
                            page
                        },
                        success: function(response) {
                            const users = response.data.users;
                            const totalPages = response.data.total_pages;
                            if (users.length !== 0) {
                                showResult(users, page, totalPages, "getListUsersByDateCertified", date)
                            } else {
                                $('#result').html('<p>Không có người dùng được cấp chứng chỉ trong ngày này.</p>');
                                $('#pagination').html('')
                            }
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                        },
                        complete: function() {
                            hideLoading()
                        }
                    });
                }

                function showResult(users, page, totalPages, funcName, ref) {
                    let tableHtml = '<table class="wp-list-table widefat fixed striped users" style="margin-top: 10px"><tr><th style="width: 30px">STT</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Chứng chỉ</th><th>Ngày gửi</th><th>Trạng thái</th><th>Ngày cấp</th><th>Hoạt động</th><th>Hành động</th></tr>';
                    users.forEach((user, index) => {
                        const stt = (page - 1) * 5 + index + 1; // Calculate STT
                        tableHtml += `<tr>
                                        <td style="width: 30px">${stt}</td>
                                        <td>${user.Name}</td>
                                        <td>${user.Email}</td>
                                        <td>${user.Phone}</td>
                                        <td>${user.certificate_name}</td>
                                        <td>${formatDate(user.submittedAt)}</td>
                                        <td>
                                            ${user.isCertified === "0" && user.certificate_status === "0" ? "Chưa cấp" :
                                                user.isCertified === "0" && user.certificate_status === "1" ? "Đã hủy" :
                                                user.isCertified === "1" && user.certificate_status === "0" ? "Đã cấp" :
                                                "Không xác định"
                                            }
                                        </td>
                                        <td>${user.createdAt ? formatDate(user.createdAt) : "-"}</td>
                                        <td>${user.isDeleted === "0" ? "Đang hoạt động" : "Đã xóa"}</td>
                                        <td>
                                            ${user.isDeleted === "0" ? `
                                                <select name="action" class="user-action" data-id="${user.CertificateId}" data-user_id="${user.Id}" data-user_name="${user.Name}">
                                                    <option>Tùy chọn</option>
                                                    <option value="delete">Xóa</option>
                                                    ${user.isCertified === "0" && user.certificate_status === "0" ? `<option value="certification">Cấp chứng chỉ</option>` :
                                                        user.isCertified === "0" && user.certificate_status === "1" ?  `<option value="restoreCertificate">Khôi phục chứng chỉ</option>` : 
                                                        user.isCertified === "1" && user.certificate_status === "0" ?  `<option value="cancelCertificate">Hủy chứng chỉ</option>` : "Không xác định"}
                                                </select>` : 
                                                `<button id="restore" data-user_id="${user.Id}">Khôi phục</button>`
                                            }
                                        </td>
                                    </tr>`;
                    });

                    tableHtml += '</table>';
                    $('#result').html(tableHtml);
                    
                    $('#pagination').data('funcName', funcName);
                    $('#pagination').data('ref', ref);
                    $('#pagination').html('');
                    for (var i = 1; i <= totalPages; i++) {
                        var buttonClass = i === page ? 'page-btn active' : 'page-btn';
                        $('#pagination').append('<button class="' + buttonClass + '" data-page="' + i + '">' + i + '</button> ');
                    }
                }

                $('#pagination').on('click', '.page-btn', function() {
                    $('#result').html('')
                    let functionName = $('#pagination').data('funcName')
                    let page = $(this).data('page')
                    let ref = $('#pagination').data('ref')
                    let func = listFunctions[functionName];

                    if (typeof func === 'function') {
                        func(ref, page); 
                    } else {
                        console.error('Hàm không tồn tại:', functionName);
                    }
                })
            

                //Tra cứu thông tin
                $('#search').click(function() {
                    const name = $('#txtUsername').val()
                    if (!name) {
                        alert('Vui lòng nhập tên người dùng!')
                        return
                    }
                    getListUsersByName(name)
                })

                $('#filter').change(function() {
                    const action = $(this).val();
                    if (action === "date" || action === "dateSubmit") {
                        $("#formSelectDate").css('display', 'block');
                    } else {
                        $("#formSelectDate").css('display', 'none');
                    }

                    if (action === "certificate") {
                        $('#formSelectCertificate').css('display', 'block');
                    } else {
                        $('#formSelectCertificate').css('display', 'none');
                    }
                })

                $("#btnFilter").click(function() {
                    const method = $("#filter").val()
                    const date = $("#datePicker").val()
                    const certificateType = $('#certificateType').val()

                    if (!method) {
                        alert("Vui lòng chọn phương thức cần lọc!")
                        return
                    }

                    if (method === "date" && !date) {
                        alert("Vui lòng chọn ngày!")
                        return
                    }

                    if (method === "certificate" && !certificateType) {
                        alert("Vui lòng chọn loại chứng chỉ!")
                        return
                    }

                    if (method === "dateSubmit" && !date) {
                        alert("Vui lòng chọn ngày gửi!")
                        return
                    }

                    if (method === "dateSubmit" && date) {
                        getListUsersByDateSubmit(date)
                    }

                    if (method === "date" && date) {
                        getListUsersByDateCertified(date)
                    }

                    if (method === "certificate" && certificateType) {
                        getListUsersByCertificate(certificateType)
                    }
                })
                
                let currentUserId = null;
                $('#result').on('change', '.user-action', function() {
                    const action = $(this).val();
                    const certificateId = $(this).data('id');
                    currentUserId = $(this).data('user_id');
                    const userName = $(this).data('user_name')
                    const date = new Date().toLocaleDateString('en-GB')

                    if (action === 'certification') {
                        $.ajax({
                            url: ajaxurl, // URL cho yêu cầu AJAX
                            type: 'POST',
                            data: {
                                action: 'get_certificate', // Tên của action PHP để xử lý yêu cầu
                                id: certificateId // ID Chứng chỉ
                            },
                            success: function(response) {
                                
                                const replacedName = response.data.TemplateSVG.replace("{name}", userName);
                                const newResult = replacedName.replace("{date}", date)
                               
                                $('#certificateContent').html(newResult); // Hiển thị chứng chỉ trong popup
                                $('#certificationPopup').css('display', 'block'); // Hiển thị popup
                            },
                            error: function() {
                                alert('Có lỗi xảy ra khi lấy chứng chỉ.');
                            }
                        });
                    }

                    if (action === 'delete') {
                        if (confirm("Bạn có chắc chắn muốn xóa người dùng này không?")) {
                            $.ajax({
                            url: ajaxurl, // URL cho yêu cầu AJAX, WordPress cung cấp ajaxurl sẵn
                            type: 'POST',
                            data: {
                                action: 'delete_user', // Tên của action PHP để xử lý yêu cầu
                                id: currentUserId // ID Chứng chỉ
                            },
                            success: function(response) {
                                alert("Đã xóa thành công.")
                                location.reload();
                            },
                            error: function() {
                                alert('Có lỗi xảy ra khi xóa người dùng.');
                            }
                        });
                        } 
                    }

                    if (action === 'cancelCertificate') {
                        if (confirm("Bạn có chắc chắn muốn hủy chứng chỉ của người dùng này không?")) {
                            $.ajax({
                            url: ajaxurl, // URL cho yêu cầu AJAX, WordPress cung cấp ajaxurl sẵn
                            type: 'POST',
                            data: {
                                action: 'cancel_certificate',
                                id: currentUserId
                            },
                            success: function(response) {
                                alert("Đã hủy chứng chỉ thành công.")
                                location.reload();
                            },
                            error: function() {
                                alert('Có lỗi xảy ra khi hủy chứng chỉ.');
                            }
                        });
                        }
                    }

                    if (action === 'restoreCertificate') {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'restore_certificate',
                                id: currentUserId // ID Chứng chỉ
                            },
                            success: function(response) {
                                alert("Đã khôi phục chứng chỉ thành công.")
                                location.reload();
                            },
                            error: function() {
                                alert('Có lỗi xảy ra khi khôi phục chứng chỉ.');
                            }
                        });
                    }
                });

                $('#result').on('click', '#restore', function() {
                    const userId = $(this).data('user_id');
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'restore_user',
                            userId
                        },
                        success: function(response) {
                            alert('Đã khôi phục người dùng thành công.')
                            location.reload();
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi khôi phục người dùng.');
                        }
                    });
                })

                $('#saveCertificate').click(function() {
                    const certificate = $('#certificateContent').html()

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'save_certificate',
                            certificate: certificate,
                            userId: currentUserId
                        },
                        success: function(response) {
                            alert('Đã lưu chứng chỉ thành công.')
                            location.reload();
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi lấy chứng chỉ.');
                        }
                    });
                })

                $('#closePopup').click(function() {
                    $('#certificationPopup').css('display', 'none'); // Ẩn chứng chỉ
                });

                function showLoading() {
                    $('#result').hide()
                    $('#pagination').hide()
                    $('#loading-container').show();
                }

                function hideLoading() {
                    $('#loading-container').hide();
                    $('#result').show()
                    $('#pagination').show()
                }
            });

            function formatDate(dateString) {
                if (dateString === "0000-00-00 00:00:00") return "-"
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }
            
        </script>
    </div>
    <?php
}

// Thêm action để xử lý yêu cầu AJAX
//Quản lý người dùng
add_action('wp_ajax_get_list_users', 'handle_get_list_users');
add_action('wp_ajax_get_certificate', 'handle_get_certificate');
add_action('wp_ajax_save_certificate', 'handle_save_certificate');
add_action('wp_ajax_delete_user', 'handle_delete_user');
add_action('wp_ajax_restore_user', 'handle_restore_user');
add_action('wp_ajax_cancel_certificate', 'handle_cancel_certificate');
add_action('wp_ajax_restore_certificate', 'handle_restore_certificate');
//Tra cứu
add_action('wp_ajax_get_users_by_name', 'handle_get_users_by_name');
add_action('wp_ajax_get_users_by_certificate', 'handle_get_users_by_certificate');
add_action('wp_ajax_get_users_by_date', 'handle_get_users_by_date');
add_action('wp_ajax_get_users_by_date_submit', 'handle_get_users_by_date_submit');
?>