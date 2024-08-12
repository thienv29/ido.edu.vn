<?php 
// Hàm hiển thị nội dung của trang quản lý người dùng
function my_custom_user_management_page_html() {
    // Kiểm tra quyền truy cập
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="wrap" style="background-color: #fff">
        <h1>Quản lý người dùng</h1>
        <div style="margin: 20px 0;">
            <div class="d-flex" style="gap: 50px">
                <div class="d-flex" style="gap: 12px">
                    <label style="font-size: 18px">Tìm kiếm người dùng:</label>
                    <input style="width: 300px" type="text" id="txtUsername" placeholder="Nhập tên người dùng...">
                    <button class="btnSearch" id="searTch">Tìm</button>
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
                            <option value="1">Nhận Cây giống về Trồng và Gửi Về</option>
                            <option value="2">Đóng Góp Mua cây Giống</option>
                            <option value="3">Đóng Góp Mua cây Giống và trồng Cây</option>
                        </select>
                    </div>
                    <button id="btnFilter" class="btnFilter">Lọc</button>
                    <button id="btnRefresh" class="btnRefresh">Refresh</button> <!-- Nút Refresh -->
                </div>
            </div>
        </div>
        <div id="loading-container">
            <img src="images/loading.gif" alt="Loading..." class="loading-gif"/>
        </div>
        <div id="result" style="border-radius: 20px"></div>
        <div id="pagination"></div>
        <div style="display: flex; gap: 20px; margin-top: 20px;">
    <div style="flex: 1; max-width: 400px; height: 500px; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background-color: #fff;">
        <h2 style="text-align: center; margin-bottom: 20px;">Thống kê các chứng chỉ đã cấp</h2>
        <div style="display: flex; text-align: center; width: 400px; height: 400px">
            <canvas id="certificateChart" ></canvas>
        </div>
    </div>
    <div style="width: 500px; height: 500px; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background-color: #fff;">
        <h2 style="text-align: center; margin-bottom: 20px;">Thông báo mới nhất</h2>
        <div id="notificationsContainer" style="margin-top: 20px;">
            <!-- Bảng thông báo sẽ được chèn vào đây -->
        </div>
    </div >
    <div style="width: 300px; height: 500px; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background-color: #fff;">
        <h2 style="text-align: center; margin-bottom: 20px;">Thông tin chứng chỉ</h2>
        <div id="certificationPopup" style="display: none;">
                    <div id="certificateContent" style="width: 100%; margin-bottom: 20px;text-align: center;"></div>
                    <div style="display: flex; justify-content: center; gap: 10px;">
                        <button class="btnClose" id="closePopup" style="padding: 10px 20px; border: none; border-radius: 5px; background-color: #f44336; color: #fff; cursor: pointer;">Đóng</button>
                        <button class="btnSave" id="saveCertificate" style="padding: 10px 20px; border: none; border-radius: 5px; background-color: #4CAF50; color: #fff; cursor: pointer;">Lưu</button>
                    </div>
                </div>
    </div>
</div>

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
            .btnRefresh{
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
                border-radius: 20px;
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

                function loadCertificateChart() {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'aggregate_certificate_data' // Đây có thể là action khác bạn cần điều chỉnh trong PHP
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.success && Array.isArray(response.data)) { // Kiểm tra nếu response.data là mảng
                                const certificateCounts = {};

                                response.data.forEach(item => {
                                    if (item.certificate_name) {
                                        if (certificateCounts[item.certificate_name]) {
                                            certificateCounts[item.certificate_name]++;
                                        } else {
                                            certificateCounts[item.certificate_name] = item.count;
                                        }
                                    }
                                });

                                const labels = Object.keys(certificateCounts);
                                const data = Object.values(certificateCounts);

                                // Vẽ biểu đồ tròn
                                const ctx = document.getElementById('certificateChart').getContext('2d');
                                new Chart(ctx, {
                                    type: 'pie',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            data: data,
                                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                position: 'top',
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        let label = context.label || '';
                                                        if (label) {
                                                            label += ': ';
                                                        }
                                                        if (context.raw !== null) {
                                                            label += context.raw + ' người';
                                                        }
                                                        return label;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            } else {
                                console.error('Dữ liệu không hợp lệ:', response.data);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Có lỗi xảy ra:', status, error);
                        }
                    });
                }

                // Gọi hàm này khi trang được tải
                loadCertificateChart();

                function showResult(users, page, totalPages, funcName, ref) {
                    let tableHtml = '<table class="wp-list-table widefat fixed striped users" style="margin-top: 10px; border-radius: 10px; border: 5px solid #c2c6c1;"><tr><th style="width: 30px">STT</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Chứng chỉ</th><th>Ngày gửi</th><th>Trạng thái</th><th>Ngày cấp</th><th>Hoạt động</th><th>Hành động</th></tr>';
                    users.forEach((user, index) => {
                        const stt = (page - 1) * 5 + index + 1; // Calculate STT
                        tableHtml += `<tr style="border-radius: 10px">
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
            
                $('#btnRefresh').click(function() {
                    location.reload(); // Tải lại trang
                });
                
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
        <script>
document.addEventListener('DOMContentLoaded', function() {
    // Gửi yêu cầu AJAX để lấy dữ liệu thông báo
    fetchNotifications();

    function fetchNotifications() {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=get_notifications')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayNotifications(data.data);
                } else {
                    document.getElementById('notificationsContainer').innerHTML = '<p>Không có thông tin thông báo.</p>';
                }
            })
            .catch(error => {
                console.error('Lỗi khi lấy dữ liệu thông báo:', error);
                document.getElementById('notificationsContainer').innerHTML = '<p>Đã xảy ra lỗi khi lấy dữ liệu.</p>';
            });
    }

    function displayNotifications(notifications) {
        let tableHtml = '<table style="width:100%; border-collapse: collapse; margin-top: 20px;">';
        tableHtml += '<thead>';
        tableHtml += '<tr>';
        tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Tên</th>';
        tableHtml += '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Thời gian</th>';
        tableHtml += '</tr>';
        tableHtml += '</thead>';
        tableHtml += '<tbody>';

        notifications.forEach(item => {
            tableHtml += '<tr>';
            tableHtml += `<td style="border: 1px solid #ddd; padding: 8px;">${item.name}</td>`;
            tableHtml += `<td style="border: 1px solid #ddd; padding: 8px;">${item.submittedAt}</td>`;
            tableHtml += '</tr>';
        });

        tableHtml += '</tbody>';
        tableHtml += '</table>';

        document.getElementById('notificationsContainer').innerHTML = tableHtml;
    }
});
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
add_action('wp_ajax_aggregate_certificate_data', 'aggregate_certificate_data');
add_action('wp_ajax_nopriv_aggregate_certificate_data', 'aggregate_certificate_data');
add_action('wp_ajax_get_notifications', 'get_notifications');
add_action('wp_ajax_nopriv_get_notifications', 'get_notifications');
//Tra cứu
add_action('wp_ajax_get_users_by_name', 'handle_get_users_by_name');
add_action('wp_ajax_get_users_by_certificate', 'handle_get_users_by_certificate');
add_action('wp_ajax_get_users_by_date', 'handle_get_users_by_date');
add_action('wp_ajax_get_users_by_date_submit', 'handle_get_users_by_date_submit');

//add_action('wp_footer', 'add_certificate_chart_script');
?>