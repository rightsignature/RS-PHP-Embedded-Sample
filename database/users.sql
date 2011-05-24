create TABLE Users (
	id INT(11) NOT NULL AUTO_INCREMENT,
	email VARCHAR(256),
	name VARCHAR(256),
  	PRIMARY KEY (`id`),
		UNIQUE KEY index_users_email (email)
)