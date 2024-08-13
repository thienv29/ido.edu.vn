<?php
global $wpdb;
$table_name = $wpdb->prefix . 'user_submisstion';

if (isset($_POST["cancel-certificate"])) {
  $idToCancel = $_POST["id"];

  echo $idToCancel;

  $wpdb->update(
    "wp_user_submisstion",
    array(
      "isCertified" => "rejected",
    ),
    array(
      "id" => $idToCancel,
    )
  );

  echo "<script>alert('hủy chứng chỉ thành công!');</script>";
}