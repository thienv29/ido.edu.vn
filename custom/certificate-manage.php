<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<style>
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
global $wpdb;
$table_name = $wpdb->prefix . 'certificate';

// function tranformSvg($svgContent)
// {
//   $temp = str_replace("\'", "'", $svgContent);
//   return str_replace('\"', "'", $temp);
// }


if (isset($_POST["add-certificate"])) {
  $cer_name = $_POST["name"];
  $cer_template_svg = $_POST["template-svg"];

  $wpdb->insert(
    $table_name,
    array(
      'name' => $cer_name,
      'templateSvg' => $cer_template_svg,
    )
  );

  echo "<script>alert('thêm thành công!');</script>";
};

if (isset($_POST["edit-certificate"])) {
  $idToEdit = $_POST["id"];
  $edited_name = $_POST["name"];
  $edited_name_template_svg = $_POST["template-svg"];

  $data = array(
    'name' => $edited_name,
    'templateSvg' => $edited_name_template_svg,
  );

  $where = array(
    'id' => $idToEdit,
  );

  $format = array(
    '%s',
    '%s',
  );

  $updated = $wpdb->update($table_name, $data, $where, $format, $where_format);

  if (false === $updated) {
    echo "<script>alert('Có lỗi xảy ra trong quá trình cập nhật.');</script>";
  } elseif (0 === $updated) {
    echo "<script>alert('Không có gì được cập nhật.');</script>";
  } else {
    echo "<script>alert('Cập nhật thành công $updated bản ghi.');</script>";
  }
};

if (isset($_POST["delete-certificate"])) {
  $idToDel = $_POST["id"];

  $wpdb->update(
    $table_name,
    array(
      "isDeleted" => 1,
    ),
    array(
      "id" => $idToDel,
    )
  );

  echo "<script>alert('xóa thành công!');</script>";
}


$certificates = $wpdb->get_results("SELECT * FROM $table_name WHERE isDeleted LIKE 0");
?>

<body>
  <div class="wrap">
    <h1>Quản lý chứng chỉ</h1>
    <div class="container-fluid">
      <div class="control-bar mb-1">
        <button class="button" data-bs-toggle="modal" data-bs-target="#addModal">Thêm mẫu chứng chỉ</button>
      </div>
      <?php
      if ($certificates) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>Số thứ tự</th>';
        echo '<th>Tên chứng chỉ</th>';
        echo '<th>Mẫu chứng chỉ</th>';
        echo '<th>Ngày tạo</th>';
        echo '<th>Hành động</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        $count = 1;
        foreach ($certificates as $certificate) {
          echo '<tr>';
          echo '<td>' . $count . '</td>';
          echo '<td >' . esc_html($certificate->name) . '</td>';
          echo
          '<td class="container">'
            . tranformSvg($certificate->templateSvg) .
            '</td>';
          echo '<td>' . esc_html($certificate->enroll) . '</td>';
          echo '<td>
          <select data-id="' . esc_attr($certificate->id) . '" 
                  data-name="' . esc_attr($certificate->name) . '"
                  data-svg="' . tranformSvg($certificate->templateSvg) . '"
                  onchange="handleSelectChange(event)">
            <option value="">Chọn hành động</option>
            <option value="edit">Sửa</option>
            <option value="delete">Xóa</option>
          </select>
          </td>';
          echo '</tr>';
          $count++;
        }

        echo '</tbody>';
        echo '</table>';
      } else {
        echo '<p>Không có dữ liệu certificate.</p>';
      }
      ?>
    </div>
  </div>
  <div class="modal" id="addModal">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Thêm chứng chỉ</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="d-flex gap-1">
            <form method="post" action="" style="min-width:50%">
              <div>
                <label>
                  Tên chứng chỉ
                </label>
                <br />
                <input class="form-control" type="text" name="name" required>
              </div>
              <div>
                <label>
                  Mẫu chứng chỉ
                </label>
                <br />
                <textarea id="svg-input" style="height: 250px; width: 100%" name="template-svg" required></textarea>
              </div>
              <div class="d-flex justify-content-center">
                <button name="add-certificate" type="submit" class="btn btn-success">Thêm</button>
              </div>
            </form>
            <div class="container p-0" id="svg-output" style="max-width:60%">
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="modal" id="editModal">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Sửa chứng chỉ</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="d-flex gap-1">
            <form method="post" action="" style="min-width:50%">
              <div>
                <input type="hidden" name="id" id="fid">
                <label>
                  Tên chứng chỉ
                </label>
                <br />
                <input class="form-control" type="text" name="name" required id="fname">
              </div>
              <div>
                <label>
                  Mẫu chứng chỉ
                </label>
                <br />
                <textarea style="height: 250px; width: 100%" name="template-svg" id="ftemplate" required></textarea>
              </div>
              <div class="d-flex justify-content-center">
                <button name="edit-certificate" type="submit" class="btn btn-primary">Sửa</button>
              </div>
            </form>
            <div class="container p-0" id="foutput" style="max-width:60%">
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="modal" id="deleteModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title text-danger">Bạn chắc chắn muốn xóa chứng chỉ?</h4>
          <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="" class="d-flex justify-content-center">
            <input type="hidden" name="id" id="fidtoDel">
            <button name="delete-certificate" type="submit" class="btn btn-danger">Xác nhận xóa</button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script>
    const displaySvg = (input, output) => {
      let templateSvg = document.getElementById(input);
      let svgOutput = document.getElementById(output);
      templateSvg.oninput = () => {
        svgOutput.innerHTML = templateSvg.value;
      }
    }

    displaySvg("svg-input", "svg-output");
    displaySvg("ftemplate", "foutput");

    function handleSelectChange(event) {
      var select = event.target;
      var value = select.value;
      if (value === "edit") {
        openEditModal(select);
      } else if (value === "delete") {
        openDeleteModal(select);
      }
      // Đặt lại giá trị của select về mặc định để có thể chọn lại hành động
      select.value = "";
    }

    function openEditModal(select) {
      let editModal = new bootstrap.Modal(document.getElementById('editModal'), {});
      let id = select.getAttribute('data-id');
      let name = select.getAttribute('data-name');
      let svg = select.getAttribute('data-svg');
      document.getElementById("fid").value = id;
      document.getElementById("fname").value = name;
      document.getElementById("ftemplate").value = svg;
      document.getElementById("foutput").innerHTML = svg;
      editModal.show();
    }

    function openDeleteModal(select) {
      let delModal = new bootstrap.Modal(document.getElementById('deleteModal'), {});
      idtoDel = select.getAttribute('data-id');
      document.getElementById("fidtoDel").value = idtoDel;
      delModal.show();
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>