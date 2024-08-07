<?php 
// Shortcode để tạo form nhập mã chứng chỉ
function certificate_input_form_shortcode() {
    ob_start();
    ?>
    <div class="certificate-input-form" style="text-align: center; width: 50%">
        <form action="<?php echo home_url('/certificate'); ?>" method="get">
            <label for="certificate_code">Nhập mã chứng chỉ:</label>
            <input style="width: 50%"type="text" id="certificate_code" name="id" required>
            <button type="submit">Xem chứng chỉ</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('certificate_input_form', 'certificate_input_form_shortcode');
?>