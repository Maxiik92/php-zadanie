CREATE DATABASE php_form;
USE php_form;
CREATE TABLE post (
id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
selected_date DATE NOT NULL,
content VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL,
color VARCHAR(255) NOT NULL );

CREATE TABLE select_option (
id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
select_option VARCHAR(255) NOT NULL);
CREATE TABLE post_select (
post_id INT NOT NULL,
select_id INT NOT NULL);

INSERT INTO select_option (select_option) VALUES ('sport'),('music'),('movies'),('other');
