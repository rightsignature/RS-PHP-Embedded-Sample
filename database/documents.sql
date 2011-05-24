create TABLE Documents (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11),
	rs_document_id VARCHAR(64),
  	PRIMARY KEY (`id`),
	KEY index_documents_on_user_id (user_id),
	KEY index_documents_on_rs_document_id (rs_document_id)
)