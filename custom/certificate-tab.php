<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<style>
  .search-input {
    max-height: 28px;
    line-height: 1.5;
    padding: 5px 10px;
    font-size: 14px;
  }

  .container {
    width: 100%;
    height: auto;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    object-fit: cover;
  }

  .container svg {
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
  }
</style>

<?php
require_once plugin_dir_path(__FILE__) . 'send-certificate.php';
require_once plugin_dir_path(__FILE__) . 'cancel-certificate.php';
require_once plugin_dir_path(__FILE__) . 'delete-user.php';

global $wpdb;
$table_name = $wpdb->prefix . 'user_submisstion';
$cer_table_name = $wpdb->prefix . 'certificate';

$current_url = add_query_arg(NULL, NULL);
$search_value = isset($_GET['s']) ? esc_attr($_GET['s']) : '';
$filter_value = isset($_GET['f']) ? esc_attr($_GET['f']) : '';

$record_per_page = 10;
$page = isset($_GET['p']) ? $_GET['p'] : '1';
$int_page = intval($page);
$skip_value = ($int_page - 1) * $record_per_page;
$base_path = 'http://localhost/wordpress/wp-admin/admin.php?page=chung-chi&s=' . $search_value . '&f=' . $filter_value;


$search_sql = "";
if (!empty($search_value)) {
  $search = '%' . trim($search_value) . '%';
  $search_sql .= " AND (u.name LIKE '$search' OR u.email LIKE '$search' OR u.phone LIKE '$search')";
}

$filter_sql = "";
if (!empty($filter_value)) {
  $filter_sql = " AND certificated LIKE $filter_value";
}


$users = $wpdb->get_results("SELECT u.id, u.name as uname, u.phone, u.email, u.certificated, u.isCertified , c.name as cname
                              FROM $table_name as u
                              LEFT JOIN $cer_table_name as c ON u.certificated = c.id 
                              WHERE u.isDeleted LIKE 0 $search_sql $filter_sql
                              ORDER BY u.id DESC  
                              LIMIT $record_per_page OFFSET $skip_value");

$certificates = $wpdb->get_results("SELECT id,name FROM $cer_table_name");


$total_record = $wpdb->get_var("SELECT COUNT(*) 
                                FROM $table_name as u
                                WHERE u.isDeleted LIKE 0 $search_sql $filter_sql");
$total_page = ceil($total_record / $record_per_page);
?>

<body>
  <div class="wrap">
    <h1>Cấp chứng chỉ</h1>
    <div class="container-fluid">
      <div class="d-flex justify-content-between mb-1">
        <form action="" method="GET">
          <?php
          foreach ($_GET as $key => $value) {
            if ($key !== 'f' && $key !== 'p') { // Bỏ qua 'f' và 'p'
              echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
            }
          }
          ?>
          <select name="f" value="<?php echo $filter_value; ?>">
            <option value="">Chọn loại chứng chỉ</option>
            <?php
            foreach ($certificates as $cer) {
              $selected = ($cer->id == $filter_value) ? 'selected' : '';
              echo '<option value="' . $cer->id . '" ' . $selected . '>' . $cer->name . '</option>';
            }
            ?>
          </select>
          <input type="submit" value="Lọc" class="button button-primary" />
        </form>

        <form method="GET" action="">
          <?php
          // Giữ lại tất cả các parameter hiện có
          foreach ($_GET as $key => $value) {
            if ($key !== 's' && $key !== 'p') { // Bỏ qua 's' và 'p'
              echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
            }
          }
          ?>
          <span>
            <input class="search-input" type="text" name="s" placeholder="Tìm kiếm..." value="<?php echo $search_value; ?>" />
            <input type="submit" value="Tìm kiếm" class="button button-primary" />
          </span>
        </form>
      </div>
      <?php

      if ($users) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>Tên</th>';
        echo '<th>SĐT</th>';
        echo '<th>Email</th>';
        echo '<th>Chứng chỉ</th>';
        echo '<th>Trạng thái</th>';
        echo '<th>Hành động</th>';

        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($users as $user) {
          echo '<tr>';
          echo '<td>' . esc_html($user->uname) . '</td>';
          echo '<td>' . esc_html($user->phone) . '</td>';
          echo '<td>' . esc_html($user->email) . '</td>';
          echo '<td>' . esc_html($user->cname) . '</td>';
          echo '<td>' . esc_html($user->isCertified) . '</td>';
          echo
          '<td>
            <select data-email =' . esc_attr($user->email) . '  data-id=' . esc_attr($user->id) . ' onchange="handleSelectChange(event)">
              <option value="">Chọn hành động</option>
              <option value="cap-chung-chi">Cấp chứng chỉ</option>
              <option value="huy-chung-chi">Hủy chứng chỉ</option>
              <option value="delete-user">Xóa</option>
            </select>
          </td>';
          echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
      } else {
        echo '<p>Không có dữ liệu user.</p>';
      }
      ?>
      <ul class="pagination justify-content-center mt-1">
        <li class="page-item <?= $page == '1' ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= $base_path . '&p=' . ($int_page - 1) ?>" tabindex="-1" aria-disabled="true">&lsaquo;</a>
        </li>
        <?php

        $max_pages_show = 3; // Số lượng trang tối đa hiển thị

        // Tính toán khoảng trang cần hiển thị
        $start_page = max(1, $page - floor($max_pages_show / 2));
        $end_page = min($total_page, $start_page + $max_pages_show - 1);

        // Hiển thị '...' nếu không bắt đầu từ trang 1
        if ($start_page > 1) {
          $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Hiển thị các trang
        for ($i = $start_page; $i <= $end_page; $i++) {
          if ($i == $page) {
            $pagination .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
          } else {
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_path . '&p=' . $i . '">' . $i . '</a></li>';
          }
        }

        // Hiển thị '...' nếu không kết thúc ở trang cuối cùng
        if ($end_page < $total_page) {
          $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        echo $pagination;
        ?>
        <li class="page-item <?= $page == $total_page || $total_page == 0 ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= $base_path . '&p=' . ($int_page + 1) ?>">&raquo;</a>
        </li>
      </ul>
    </div>
  </div>
  <div class="modal" id="sendCertificateModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title">Cấp chứng chỉ</h4>
          <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="container" id="send-modal-container"></div>
          <form method="post" action="" class="d-flex justify-content-center">
            <input type="hidden" name="id" id="idToSend">
            <input type="hidden" name="email" id="emailToSend">
            <button name="send-certificate" type="submit" class="btn btn-success">Cấp chứng chỉ</button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <div class="modal" id="cancelCertificateModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title text-danger">Bạn chắc chắn muốn hủy chứng chỉ?</h4>
          <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="" class="d-flex justify-content-center">
            <input type="hidden" name="id" id="fidtoCancel">
            <button name="cancel-certificate" type="submit" class="btn btn-danger">Xác nhận hủy</button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <div class="modal" id="deleteUserModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title text-danger">Bạn chắc chắn muốn xóa người dùng?</h4>
          <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="" class="d-flex justify-content-center">
            <input type="hidden" name="id" id="fidToDel">
            <button name="delete-user" type="submit" class="btn btn-danger">Xác nhận xóa</button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script>
    function handleSelectChange(event) {
      var select = event.target;
      var value = select.value;
      if (value === "cap-chung-chi") {
        capChungChi(select);
      } else if (value === "huy-chung-chi") {
        huyChungChi(select);
      } else if (value === "delete-user") {
        deleteUser(select);
      }
      // Đặt lại giá trị của select về mặc định để có thể chọn lại hành động
      select.value = "";
    }

    function capChungChi(select) {
      let modal = new bootstrap.Modal(document.getElementById('sendCertificateModal'), {});
      let idToSend = select.getAttribute('data-id');
      let emailToSend = select.getAttribute('data-email');
      document.getElementById("idToSend").value = idToSend;
      document.getElementById("emailToSend").value = emailToSend;

      getUserCertificate(idToSend, function(userCertificate) {

        const svgTemplate = userCertificate?.templateSvg;

        const svgContent = svgTemplate
          .replace(/{name}/g, userCertificate?.username)
          .replace(/{certificate}/g, userCertificate?.cername);


        document.getElementById("send-modal-container").innerHTML =
          "<div class=\"container m-2 mb-3\">" + svgContent + "</div>";
      });

      modal.show();
    }

    function huyChungChi(select) {
      let cancelModal = new bootstrap.Modal(document.getElementById('cancelCertificateModal'), {});
      idToCancel = select.getAttribute('data-id');
      document.getElementById("fidtoCancel").value = idToCancel;
      cancelModal.show();
    }

    function deleteUser(select) {
      let delModal = new bootstrap.Modal(document.getElementById('deleteUserModal'), {});
      idToDel = select.getAttribute('data-id');
      document.getElementById("fidToDel").value = idToDel;
      delModal.show();
    }

    function getUserCertificate(id, callback) {
      jQuery(document).ready(function($) {
        var data = {
          action: 'get_user_certificate', // Tên action để xử lý trong PHP
          id: id // Dữ liệu cần gửi đi
        };

        $.ajax({
          url: '<?php echo admin_url('admin-ajax.php'); ?>', // Gửi đến admin-ajax.php
          type: 'POST',
          data: data,
          success: function(response) {
            if (callback) {
              callback(response.data); // Gọi callback với dữ liệu
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
          }
        });
      });
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>