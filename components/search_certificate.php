<?php 
function display_certificate_by_id_shortcode($atts) {
    // Lấy ID chứng chỉ từ query string
    $certificate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    ob_start();
    ?>
    <style>
        .certificate-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }

        .certificate-form label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
        }

        .certificate-form input[type="number"],
        .certificate-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .certificate-form input[type="number"] {
            background-color: #fff;
            transition: border-color 0.3s ease;
        }

        .certificate-form input[type="number"]:focus {
            border-color: #0073aa;
            outline: none;
        }

        .certificate-form input[type="submit"] {
            background-color: #0073aa;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .certificate-form input[type="submit"]:hover {
            background-color: #005f8d;
        }

        .certificate-view {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .certificate-not-found {
            color: #ff0000;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
    </style>

    <!-- Form nhập ID chứng chỉ -->
    <form method="GET" class="certificate-form">
        <label for="certificate-id">Nhập ID chứng chỉ:</label>
        <input type="number" id="certificate-id" name="id" value="<?php echo $certificate_id > 0 ? $certificate_id : ''; ?>" required>
        <input type="submit" value="Tra cứu">
    </form>

    <?php
    if ($certificate_id > 0) {
        global $wpdb;
        $table_certificated = $wpdb->prefix . 'certificated';

        // Lấy chứng chỉ từ bảng dựa trên ID
        $certificate = $wpdb->get_row($wpdb->prepare("
            SELECT TemplateSVG, createdAt
            FROM $table_certificated
            WHERE Id = %d AND isDeleted = 0
        ", $certificate_id));

        if ($certificate) {
            ?>
            <div class="certificate-view">
                <?php echo $certificate->TemplateSVG; ?>
            </div>
            <?php
        } else {
            echo '<p class="certificate-not-found">Không tìm thấy chứng chỉ! Vui lòng kiểm tra lại.</p>';
        }
    }

    return ob_get_clean();
}
add_shortcode('display_certificate_by_id', 'display_certificate_by_id_shortcode');


?>
