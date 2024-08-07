<?php  
//Hàm tạo table user
function create_user_data_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_form';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        Id int NOT NULL AUTO_INCREMENT,
        Name tinytext NOT NULL,
        Phone text NOT NULL,
        Email text NOT NULL,
		CertificateId int,
		isCertified boolean DEFAULT false,
		isDeleted boolean DEFAULT false,
        submittedAt datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (Id),
		CONSTRAINT fk_certificate FOREIGN KEY (CertificateId) REFERENCES {$wpdb->prefix}certificate(Id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

//Hàm tạo table certificate
function create_certificate_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'certificate';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        Id int NOT NULL AUTO_INCREMENT,
       	Name text NOT NULL,
        TemplateSVG longtext NOT NULL,
		isDeleted boolean DEFAULT false,
        PRIMARY KEY (Id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

//Hàm tạo table certificate đã cấp
function create_certificated_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'certificated';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        Id int NOT NULL AUTO_INCREMENT,
        TemplateSVG longtext NOT NULL,
		isDeleted boolean DEFAULT false,
        createdAt datetime DEFAULT CURRENT_TIMESTAMP,
        userId int,
        PRIMARY KEY (Id),
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    //Thêm khóa ngoại
    $foreign_key_sql = "ALTER TABLE $table_name
                        ADD CONSTRAINT fk_user FOREIGN KEY (userId) REFERENCES {$wpdb->prefix}user_form(Id);";
    
    $wpdb->query($foreign_key_sql);
}

// Kết hợp các hàm tạo bảng vào một hàm
function create_all_custom_tables() {
	create_certificate_table();
    create_user_data_table();
    create_certificated_table();
}
add_action('admin_init', 'create_all_custom_tables');
?>