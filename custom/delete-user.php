<?php
global $wpdb;
$table_name = $wpdb->prefix . 'user_submisstion';

if (isset($_POST["delete-user"])) {
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

  echo "<script>alert('xóa người dùng thành công!');</script>";
}