create TABLE Templates (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11),
	rs_template_id VARCHAR(64),
  	PRIMARY KEY (`id`),
	KEY index_templates_on_user_id (user_id),
	KEY index_templates_on_rs_template_id (rs_template_id)
)