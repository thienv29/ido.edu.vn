<?php 
function my_custom_search_management_page_html() {
	// Kiểm tra quyền truy cập
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <div class="wrap">
        <h1>Tra cứu thông tin</h1>
        <div style="display: flex; align-items: center; gap: 50px">
            <div style="display: flex; align-items: center; gap: 12px">
                <label style="font-size: 20px">Tìm kiếm người dùng:</label>
                <input style="width: 400px" type="text" id="txtUsername" placeholder="Nhập tên người dùng...">
                <button style="color: #fff; background-color: #007bff; border: none; padding: 6px 12px; cursor: pointer; border-radius: .25rem;" id="search">Tìm</button>
            </div>
            <div style="display: flex; align-items: center; gap: 12px">
                <label style="font-size: 20px">Lọc người dùng:</label>
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
                <button id="btnFilter" style="color: #fff; background-color: #007bff; border: none; padding: 6px 12px; cursor: pointer; border-radius: .25rem;">Lọc</button>
            </div>
        </div>
        
        <div id="result"></div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('#search').click(function() {
                const name = $('#txtUsername').val()
                if (!name) {
                    alert('Vui lòng nhập tên người dùng!')
                    return
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_users_by_name',
                        name
                    },
                    success: function(response) {
                        $('#txtUsername').val('')
                        const users = response.data;
                        console.log(users)
                        if (users.length !== 0) {
                            let tableHtml = '<table class="wp-list-table widefat fixed striped users" style="margin-top: 10px"><tr><th>STT</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Chứng chỉ</th><th>Ngày gửi</th><th>Trạng thái</th><th>Hoạt động</th></tr>';
                            users.forEach(user => {
                                tableHtml += `<tr>
                                                <td>${user.rownum}</td>
                                                <td>${user.Name}</td>
                                                <td>${user.Email}</td>
                                                <td>${user.Phone}</td>
                                                <td>${user.certificate_name}</td>
                                                <td>${formatDate(user.submittedAt)}</td>
                                                <td>${user.isCertified === "0" ? "Chưa cấp" : "Đã cấp"}</td>
                                                <td>${user.isDeleted === "0"? "Đang hoạt động" : "Đã xóa"}</td>
                                            </tr>`;
                            });

                            tableHtml += '</table>';
                            $('#result').html(tableHtml);
                        } else {
                            $('#result').html('<p>Không tồn tại người dùng với tên này</p>');
                        }
                        

                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                    }
                });
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
                    $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_users_by_date_submit',
                        date: date
                    },
                    success: function(response) {
                        const users = response.data;
                        if (users.length !== 0) {
                            let tableHtml = '<table class="wp-list-table widefat fixed striped users" style="margin-top: 10px"><tr><th>STT</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Chứng chỉ</th><th>Ngày gửi</th><th>Trạng thái</th><th>Hoạt động</th></tr>';
                            users.forEach(user => {
                                tableHtml += `<tr>
                                                <td>${user.rownum}</td>
                                                <td>${user.Name}</td>
                                                <td>${user.Email}</td>
                                                <td>${user.Phone}</td>
                                                <td>${user.certificate_name}</td>
                                                <td>${formatDate(user.submittedAt)}</td>
                                                <td>${user.isCertified === "0" ? "Chưa cấp" : "Đã cấp"}</td>
                                                <td>${user.isDeleted === "0"? "Đang hoạt động" : "Đã xóa"}</td>
                                            </tr>`;
                            });

                            tableHtml += '</table>';
                            $('#result').html(tableHtml);
                        } else {
                            $('#result').html('<p>Không có người dùng nộp yêu cầu trong ngày này.</p>');
                        }
                        

                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                    }
                });}

                if (method === "date" && date) {
                    $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_users_by_date',
                        date: date
                    },
                    success: function(response) {
                        const users = response.data;
                        if (users.length !== 0) {
                            let tableHtml = '<table class="wp-list-table widefat fixed striped users" style="margin-top: 10px"><tr><th>STT</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Chứng chỉ</th><th>Ngày nộp</th><th>Trạng thái</th><th>Ngày cấp</th><th>Hoạt động</th></tr>';
                            users.forEach(user => {
                                tableHtml += `<tr>
                                                <td>${user.rownum}</td>
                                                <td>${user.Name}</td>
                                                <td>${user.Email}</td>
                                                <td>${user.Phone}</td>
                                                <td>${user.certificate_name}</td>
                                                <td>${formatDate(user.submittedAt)}</td>
                                                <td>${user.isCertified === "0" ? "Chưa cấp" : "Đã cấp"}</td>
                                                <td>${formatDate(user.createdAt)}</td>
                                                <td>${user.isDeleted === "0"? "Đang hoạt động" : "Đã xóa"}</td>
                                            </tr>`;
                            });

                            tableHtml += '</table>';
                            $('#result').html(tableHtml);
                        } else {
                            $('#result').html('<p>Không có người dùng được cấp chứng chỉ trong ngày này.</p>');
                        }
                        

                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                    }
                });
                }

                if (method === "certificate" && certificateType) {
                    $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_users_by_certificate',
                        idCertificate: certificateType
                    },
                    success: function(response) {
                        const users = response.data;
                        if (users.length !== 0) {
                            let tableHtml = '<table class="wp-list-table widefat fixed striped users" style="margin-top: 10px"><tr><th>STT</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Chứng chỉ</th><th>Ngày gửi</th><th>Trạng thái</th><th>Hoạt động</th></tr>';
                            users.forEach(user => {
                                tableHtml += `<tr>
                                                <td>${user.rownum}</td>
                                                <td>${user.Name}</td>
                                                <td>${user.Email}</td>
                                                <td>${user.Phone}</td>
                                                <td>${user.certificate_name}</td>
                                                <td>${formatDate(user.submittedAt)}</td>
                                                <td>${user.isCertified === "0" ? "Chưa cấp" : "Đã cấp"}</td>
                                                <td>${user.isDeleted === "0"? "Đang hoạt động" : "Đã xóa"}</td>
                                            </tr>`;
                            });

                            tableHtml += '</table>';
                            $('#result').html(tableHtml);
                        } else {
                            $('#result').html('<p>Không tồn tại người dùng với chứng chỉ này</p>');
                        }
                        

                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi lấy danh sách người dùng.');
                    }
                });
            }
            })
        })

        function formatDate(dateString) {
            if (dateString === "0000-00-00 00:00:00") return "-"
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    </script>
    <?php
} 
//Thêm action để xử lý yêu cầu AJAX
add_action('wp_ajax_get_users_by_name', 'handle_get_users_by_name');
add_action('wp_ajax_get_users_by_certificate', 'handle_get_users_by_certificate');
add_action('wp_ajax_get_users_by_date', 'handle_get_users_by_date');
add_action('wp_ajax_get_users_by_date_submit', 'handle_get_users_by_date_submit');
?>