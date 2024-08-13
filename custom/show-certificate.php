<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Xem chứng chỉ</title>
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

  input {
    max-height: 38px;
  }
</style>

<body>
  
  <?php
  get_header();
  if (isset($_GET["ma-chung-chi"])) {
    $id = sanitize_text_field($_GET["ma-chung-chi"]);

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_submisstion';

    $user = $wpdb->get_row($wpdb->prepare(
      "SELECT u.name as username, c.name as cername, isCertified, templateSvg
             FROM $table_name as u
             LEFT JOIN wp_certificate as c ON u.certificated = c.id 
             WHERE u.id = %d",
      $id
    ));

    if ($user->isCertified != 'certified' || !$user) {
      echo "<div class='container container-lg'>
              <h2 class='text-danger'>Chứng chỉ không tồn tại</h2>
            </div>";
      return;
    }

    $templateSvg = tranformSvg($user->templateSvg);
    $templateSvgContent = str_replace('{name}', $user->username, $templateSvg);
    $templateSvgContent = str_replace('{certificate}', $user->cername, $templateSvgContent);

    echo "<div class='container container-lg'> $templateSvgContent </div>";
  } else {
    echo '   
      <div class="container mt-5 d-flex flex-column align-items-center">
        <h2>Nhập mã chứng chỉ để xem chứng chỉ</h2>
        <form action="" method="GET" class="d-flex align-items-center gap-3 mt-2">
          <input type="text" name="ma-chung-chi" required>
          <button   class="btn btn-success">Xem</button>
        </form>
      </div>
    ';
  }
  ?>
</body>

</html>