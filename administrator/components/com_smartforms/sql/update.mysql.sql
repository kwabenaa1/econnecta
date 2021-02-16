CREATE TABLE IF NOT EXISTS `#__smart_forms_uploaded_files` (
        uploaded_file_id INT AUTO_INCREMENT,
		    file_key  VARCHAR(32),
		    file_name VARCHAR(500),
		    file_mime VARCHAR(500),
		    original_name VARCHAR(500),
		    PRIMARY KEY (uploaded_file_id)
) COLLATE utf8_general_ci;
