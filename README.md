## Yêu cầu
Trang web phải sử dụng form từ Elementor Pro. <br/><br/>
Form phải có các trường sau:
+ name  | input:text
+ phone | input:text
+ email | input:email
+ certificate | select
## Cách cài đặt chức năng

B1: di chuyển folder custom trong repo này vào đường dẫn [your-wp-site]/wp-content/themes/[your-theme] <br/>
&emsp;&emsp; VD: wordpress-site/wp-content/themes/astra <br/><br/>
B2: vào file functions.php trong thư mục theme bạn sử dụng và nhập đoạn mã:
```php
 require_once plugin_dir_path(__FILE__) . 'custom/function-add-on.php';
```
B3: Nhập id của form Elementor vào file function-add-on.php trong thư mục custom
```php
 define('POST_FORM_ID', 'your_elementor_form_id');
```
⚠️Lưu ý: id của form đến từ trường id, không phải CSS id.
