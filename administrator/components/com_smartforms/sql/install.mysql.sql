CREATE TABLE IF NOT EXISTS `#__smartforms` (
        form_id int AUTO_INCREMENT,
        form_name VARCHAR(200) NOT NULL,
        element_options MEDIUMTEXT NOT NULL,
        client_form_options MEDIUMTEXT NOT NULL,
        form_options MEDIUMTEXT NOT NULL,
        donation_email VARCHAR(200),
        PRIMARY KEY  (form_id)
) COLLATE utf8_general_ci;


CREATE TABLE `#__smartforms_entry` (
        entry_id int AUTO_INCREMENT,
        uniq_id VARCHAR(50),
        user_id VARCHAR(50),
        form_id int,
        date datetime NOT NULL,
        data MEDIUMTEXT NOT NULL,
        ip VARCHAR(39),
        reference_id VARCHAR(200),
        PRIMARY KEY  (entry_id)
) COLLATE utf8_general_ci;


CREATE TABLE `#__smart_forms_entry_detail` (
        entry_detail_id int AUTO_INCREMENT,
        entry_id int,
        field_id varchar(50) NOT NULL,
        json_value MEDIUMTEXT NOT NULL,
        value MEDIUMTEXT NOT NULL,
        exvalue1 MEDIUMTEXT NOT NULL,
        exvalue2 MEDIUMTEXT NOT NULL,
        exvalue3 MEDIUMTEXT NOT NULL,
        exvalue4 MEDIUMTEXT NOT NULL,
        exvalue5 MEDIUMTEXT NOT NULL,
        exvalue6 MEDIUMTEXT NOT NULL,
        datevalue DATETIME,
        PRIMARY KEY  (entry_detail_id)
) COLLATE utf8_general_ci;

CREATE TABLE `#__smart_forms_options` (
        option_id int AUTO_INCREMENT,
        option_name VARCHAR(100),
        option_value MEDIUMTEXT NOT NULL,
        PRIMARY KEY  (option_id)
) COLLATE utf8_general_ci;


CREATE TABLE `#__smart_forms_plugins` (
        plugin_id int AUTO_INCREMENT,
        plugin_name VARCHAR(200),
        plugin_root_folder VARCHAR(200),
        plugin_loader VARCHAR(200),
        PRIMARY KEY  (plugin_id)
) COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__smart_forms_uploaded_files` (
        uploaded_file_id INT AUTO_INCREMENT,
		    file_key  VARCHAR(32),
		    file_name VARCHAR(500),
		    file_mime VARCHAR(500),
		    original_name VARCHAR(500),
		    PRIMARY KEY (uploaded_file_id)
) COLLATE utf8_general_ci;