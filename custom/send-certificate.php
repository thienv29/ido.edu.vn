
<?php

function sendEmail($recieverEmail, $id, $url)
{
  $subject = 'Chứng chỉ từ Vườn vì Việt Nam xanh';
  $message = '<h2>
                Bạn đã nhận được 1 chứng chỉ từ Vườn vì Việt Nam xanh.
              </h2>
              <h3>
                Mã chứng chỉ của bạn là: ' . $id . '
              </h3>
              <h3>
                Hoặc có thể bấm vào link này để xem chứng chỉ: ' . $url . '
              </h3>';
  $headers = array('Content-Type: text/html; charset=UTF-8');

  // Gửi email
  $mail_sent = wp_mail($recieverEmail, $subject, $message, $headers);

  if ($mail_sent) {
    echo "<script>alert('Gửi chứng chỉ thành công!');</script>";
  } else {
    echo "<script>alert('Gửi chứng chỉ thất bại!');</script>";
  }
}

if (isset($_POST["send-certificate"])) {
  global $wpdb;
  $id = $_POST["id"];
  $email = $_POST["email"];
  $website_url = home_url() . '/xem-chung-chi?ma-chung-chi=' . $id;

  $wpdb->update(
    "wp_user_submisstion",
    array(
      "isCertified" => "certified",
    ),
    array(
      "id" => $id,
    )
  );

  sendEmail($email, $id, $website_url);

}
