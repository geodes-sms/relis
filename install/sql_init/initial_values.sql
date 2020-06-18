-- TABLES
DROP TABLE IF EXISTS `admin_config`$$
CREATE TABLE IF NOT EXISTS `admin_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_label` varchar(100) NOT NULL,
  `config_value` varchar(100) NOT NULL,
  `config_description` varchar(500) DEFAULT NULL,
  `config_user` int(11) NOT NULL DEFAULT '0',
  `config_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;$$

DROP TABLE IF EXISTS `config`$$
CREATE TABLE IF NOT EXISTS `config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_type` varchar(15) NOT NULL DEFAULT 'default',
  `project_title` varchar(500) DEFAULT NULL,
  `project_description` text,
  `default_lang` varchar(15) NOT NULL DEFAULT 'en',
  `creator` int(11) NOT NULL DEFAULT '1',
  `run_setup` int(1) NOT NULL DEFAULT '0',
  `rec_per_page` int(4) NOT NULL DEFAULT '30',
  `config_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1$$


INSERT INTO `config` (`config_id`, `config_type`, `project_title`, `project_description`, `default_lang`, `creator`, `run_setup`, `rec_per_page`, `config_active`) VALUES
(1, 'default', 'Admin', 'Admin project', 'en', 1, 0, 30, 1)$$

DROP TABLE IF EXISTS `config_admin`$$
CREATE TABLE IF NOT EXISTS `config_admin` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_type` varchar(100) NOT NULL,
  `editor_url` varchar(100) NOT NULL,
  `editor_generated_path` varchar(100) NOT NULL,
  `track_comment_on` int(2) NOT NULL DEFAULT '0',
  `list_trim_nbr` int(3) NOT NULL DEFAULT '80',
  `first_connect` int(2) NOT NULL DEFAULT '1',
  `config_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1$$

INSERT INTO `config_admin` (`config_id`, `config_type`, `editor_url`, `editor_generated_path`, `track_comment_on`, `config_active`) VALUES
(1, '', 'http://127.0.0.1:8080/relis/texteditor', 'C:\\dslforge_workspace', 0, 1)$$



DROP TABLE IF EXISTS `log`$$
CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type` varchar(20) NOT NULL,
  `log_event` text NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_ip_address` varchar(50) DEFAULT NULL,
  `log_user_id` int(11) DEFAULT NULL,
  `log_poste_id` int(11) DEFAULT NULL,
  `log_user_agent` varchar(150) DEFAULT NULL,
  `log_publish` int(1) NOT NULL DEFAULT '1',
  `log_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 $$



DROP TABLE IF EXISTS `projects`$$
CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_label` varchar(100) NOT NULL,
  `project_title` varchar(250) NOT NULL,
  `project_description` varchar(1000) DEFAULT NULL,
  `project_creator` int(11) NOT NULL DEFAULT '1',
  `project_icon` longblob,
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_public` int(1) NOT NULL DEFAULT '0',
  `project_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 $$



DROP TABLE IF EXISTS `str_management`$$
CREATE TABLE IF NOT EXISTS `str_management` (
  `str_id` int(11) NOT NULL AUTO_INCREMENT,
  `str_label` varchar(500) NOT NULL,
  `str_text` varchar(1000) NOT NULL,
  `str_category` varchar(20) NOT NULL DEFAULT 'default',
  `str_lang` varchar(3) NOT NULL DEFAULT 'en',
  `str_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`str_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 $$



DROP TABLE IF EXISTS `usergroup`$$
CREATE TABLE IF NOT EXISTS `usergroup` (
  `usergroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup_name` varchar(100) NOT NULL,
  `usergroup_description` varchar(100) DEFAULT NULL,
  `usergroup_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`usergroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 $$


INSERT INTO `usergroup` (`usergroup_id`, `usergroup_name`, `usergroup_description`, `usergroup_active`) VALUES
(1, 'Administrator', 'Administrator', 1),
(2, 'Project Manager', 'Project Manager', 1),
(3, 'Standard', 'Standard', 1)$$


DROP TABLE IF EXISTS `userproject`$$
CREATE TABLE IF NOT EXISTS `userproject` (
  `userproject_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL ,
  `project_id` int(11) NOT NULL,
  `user_role` enum('Reviewer','Validator','Project admin','Guest') NOT NULL DEFAULT 'Reviewer',
  `added_by` int(11) NOT NULL DEFAULT '1',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userproject_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 $$


DROP TABLE IF EXISTS `users`$$
CREATE TABLE IF NOT EXISTS `users` (
 `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_username` varchar(20) NOT NULL,
  `user_mail` varchar(100) DEFAULT NULL,
  `user_usergroup` int(11) NOT NULL,
  `user_password` varchar(35) DEFAULT NULL,
  `user_picture` longblob,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_state` int(2) NOT NULL DEFAULT '1',
  `user_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_username` (`user_username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 $$

DROP TABLE IF EXISTS `info`$$
CREATE TABLE IF NOT EXISTS `info` (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `info_title` varchar(500) NOT NULL,
  `info_desc` varchar(1000) DEFAULT NULL,
  `info_link` varchar(500) DEFAULT NULL,
  `info_type` enum('Home','Features','Help','Reference') NOT NULL DEFAULT 'Help',
  `info_order` int(2) NOT NULL DEFAULT '1',
  `info_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 $$

DROP TABLE IF EXISTS `user_creation`$$
CREATE TABLE IF NOT EXISTS `user_creation` (
  `user_creation_id` int(11) NOT NULL AUTO_INCREMENT,
  `creation_user_id` int(11) NOT NULL,
  `confirmation_code` varchar(50) NOT NULL,
  `confirmation_expiration` int(10) NOT NULL,
  `confirmation_try` int(10) NOT NULL,
  `user_creation_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_creation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 $$

-- stored procedures

DROP PROCEDURE IF EXISTS `add_config`$$
CREATE  PROCEDURE `add_config`(_config_id INT , _project_title  VARCHAR(405) , _project_description  VARCHAR(1005) , _default_lang  VARCHAR(16) , _creator INT , _run_setup  VARCHAR(6))
BEGIN
START TRANSACTION;
INSERT INTO config (project_title , project_description , default_lang , creator , run_setup) VALUES (_project_title , _project_description , _default_lang , _creator , _run_setup);
SELECT config_id AS id_value FROM config WHERE config_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `add_logs`$$
CREATE  PROCEDURE `add_logs`(_log_id INT , _log_type  VARCHAR(55) , _log_user_id INT , _log_event  VARCHAR(205) , _log_ip_address  VARCHAR(205))
BEGIN
START TRANSACTION;
INSERT INTO log (log_type , log_user_id , log_event , log_ip_address) VALUES (_log_type , _log_user_id , _log_event , _log_ip_address);
SELECT log_id AS id_value FROM log WHERE log_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `add_project`$$
CREATE  PROCEDURE `add_project`(_project_id INT , _project_creator INT , _project_label  VARCHAR(105) , _project_title  VARCHAR(255) , _project_description  VARCHAR(1005) , _project_icon  LONGBLOB )
BEGIN
START TRANSACTION;
INSERT INTO projects (project_creator , project_label , project_title , project_description , project_icon) VALUES (_project_creator , _project_label , _project_title , _project_description , _project_icon);
SELECT project_id AS id_value FROM projects WHERE project_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `add_str_mng`$$
CREATE  PROCEDURE `add_str_mng`(_str_id INT , _str_label  VARCHAR(405) , _str_text  VARCHAR(805) , _str_lang  VARCHAR(8) , _str_category  VARCHAR(23))
BEGIN
START TRANSACTION;
INSERT INTO str_management (str_label , str_text , str_lang , str_category) VALUES (_str_label , _str_text , _str_lang , _str_category);
SELECT str_id AS id_value FROM str_management WHERE str_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `add_users`$$
CREATE  PROCEDURE `add_users`(_user_id INT , _user_state INT , _user_name  VARCHAR(55) , _user_username  VARCHAR(25) , _user_mail  VARCHAR(105) , _user_usergroup INT , _user_password  VARCHAR(40) , _user_picture  LONGBLOB  , _created_by INT)
BEGIN
START TRANSACTION;
INSERT INTO users (user_state , user_name , user_username , user_mail , user_usergroup , user_password , user_picture  , created_by) VALUES (_user_state , _user_name , _user_username , _user_mail , _user_usergroup , _user_password , _user_picture  , _created_by);
SELECT user_id AS id_value FROM users WHERE user_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `add_users_project`$$
CREATE  PROCEDURE `add_users_project`(_userproject_id INT , _user_id INT , _project_id INT , _user_role  VARCHAR(25) , _added_by INT)
BEGIN
START TRANSACTION;
INSERT INTO userproject (user_id , project_id , user_role , added_by) VALUES (_user_id , _project_id , _user_role , _added_by);
SELECT userproject_id AS id_value FROM userproject WHERE userproject_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `check_login`$$
CREATE   PROCEDURE `check_login`(IN _login VARCHAR(20))
BEGIN
START TRANSACTION;
SELECT COUNT(user_id)  AS number 
FROM users
WHERE user_active=1 AND user_username = _login  ;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `check_user_credentials`$$
CREATE   PROCEDURE `check_user_credentials`(_username VARCHAR(100), _password VARCHAR(100))
BEGIN
START TRANSACTION;
	SELECT * FROM users 
	WHERE (users.user_username = _username)
    AND (users.user_password = _password);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `count_list`$$
CREATE   PROCEDURE `count_list`(IN source varchar(200),IN condition_stat VARCHAR(1000))
BEGIN
SET @query = CONCAT('Select count(*) as nombre from  ',source ,'   WHERE 1=1  ', condition_stat );
PREPARE stmt FROM @query;
EXECUTE stmt; -- execute statement
DEALLOCATE PREPARE stmt; -- release the statement memory.
END$$

DROP PROCEDURE IF EXISTS `get_detail_config`$$
CREATE   PROCEDURE `get_detail_config`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM config
WHERE config_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_logs`$$
CREATE   PROCEDURE `get_detail_logs`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM log
WHERE log_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_project`$$
CREATE  PROCEDURE `get_detail_project`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM projects
WHERE project_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_str_mng`$$
CREATE   PROCEDURE `get_detail_str_mng`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM str_management
WHERE str_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_usergroup`$$
CREATE   PROCEDURE `get_detail_usergroup`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM usergroup
WHERE usergroup_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_users`$$
CREATE   PROCEDURE `get_detail_users`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM users
WHERE user_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_user_project`$$
CREATE   PROCEDURE `get_detail_user_project`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM userproject
WHERE userproject_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list`$$
CREATE   PROCEDURE `get_list`(IN _source varchar(100),IN _fields varchar(1000),IN _condition_stat VARCHAR(1000))
BEGIN
SET @query = CONCAT('Select ',_fields,' from  ',_source ,'   WHERE 1=1   ', _condition_stat );
 PREPARE stmt FROM @query;
 EXECUTE stmt; -- execute statement
 DEALLOCATE PREPARE stmt; -- release the statement memory.
END$$

DROP PROCEDURE IF EXISTS `get_list_config`$$
CREATE   PROCEDURE `get_list_config`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
IF _range < 1 THEN
SELECT  * FROM config
WHERE config_active=1   ORDER BY config_id ASC;
ELSE
SELECT  * FROM config
WHERE config_active=1   ORDER BY config_id ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_logs`$$
CREATE   PROCEDURE `get_list_logs`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
SET @search_log_type := CONCAT('%',TRIM(_search),'%') ;  SET @search_log_event := CONCAT('%',TRIM(_search),'%') ;  
IF _range < 1 THEN
SELECT  * FROM log
WHERE log_active=1   AND (  (log_type LIKE  @search_log_type)  OR (log_event LIKE  @search_log_event)   )  ORDER BY log_id DESC ;
ELSE
SELECT  * FROM log
WHERE log_active=1   AND (   (log_type LIKE  @search_log_type)  OR (log_event LIKE  @search_log_event)   )  ORDER BY log_id DESC  LIMIT _start_by , _range;
END IF;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_project`$$
CREATE  PROCEDURE `get_list_project`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
SET @search_project_title := CONCAT('%',TRIM(_search),'%') ;  
IF _range < 1 THEN
SELECT  * FROM projects
WHERE project_active=1   AND (  (project_title LIKE  @search_project_title) )  ORDER BY project_id ASC;
ELSE
SELECT  * FROM projects
WHERE project_active=1   AND (  (project_title LIKE  @search_project_title)  )  ORDER BY project_id ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_str_mng`$$
CREATE   PROCEDURE `get_list_str_mng`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500) ,IN _str_lang VARCHAR(3))
BEGIN
START TRANSACTION;
SET @search_str_label := CONCAT('%',TRIM(_search),'%') ;  SET @search_str_text := CONCAT('%',TRIM(_search),'%') ;  
IF _range < 1 THEN
SELECT  * FROM str_management
WHERE str_active=1  AND _str_lang = _str_lang   AND ( (str_label LIKE  @search_str_label)  OR (str_text LIKE  @search_str_text)  )  ORDER BY str_text ASC ;
ELSE
SELECT  * FROM str_management
WHERE str_active=1  AND _str_lang = _str_lang   AND (  (str_label LIKE  @search_str_label)  OR (str_text LIKE  @search_str_text)  )  ORDER BY str_text ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_usergroup`$$
CREATE   PROCEDURE `get_list_usergroup`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
SET @search_usergroup_name := CONCAT('%',TRIM(_search),'%') ; 
IF _range < 1 THEN
SELECT  * FROM usergroup
WHERE usergroup_active=1   AND (  (usergroup_name LIKE  @search_usergroup_name)  )  ORDER BY usergroup_name ASC;
ELSE
SELECT  * FROM usergroup
WHERE usergroup_active=1   AND (  (usergroup_name LIKE  @search_usergroup_name)  )  ORDER BY usergroup_name ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_users`$$
CREATE   PROCEDURE `get_list_users`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
SET @search_user_name := CONCAT('%',TRIM(_search),'%') ; 
IF _range < 1 THEN
SELECT  * FROM users
WHERE user_active=1   AND (  (user_name LIKE  @search_user_name)  )  ORDER BY user_name ASC ;
ELSE
SELECT  * FROM users
WHERE user_active=1   AND (  (user_name LIKE  @search_user_name)  )  ORDER BY user_name ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_users_all`$$
CREATE   PROCEDURE `get_list_users_all`()
BEGIN
START TRANSACTION;
SELECT  U.*,G.usergroup_name FROM users U
INNER JOIN usergroup G ON (U.user_usergroup  = G.usergroup_id)
WHERE U.user_active = 1 AND G.usergroup_active;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_user_project`$$
CREATE   PROCEDURE `get_list_user_project`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN 
START TRANSACTION;
IF _range < 1 THEN 
SELECT * FROM userproject WHERE userproject_active=1 ORDER BY user_id ASC; 
ELSE
SELECT * FROM userproject WHERE userproject_active=1 ORDER BY user_id ASC LIMIT _start_by , _range; 
END IF; 
COMMIT; 
END$$

DROP PROCEDURE IF EXISTS  get_reference_value $$
CREATE   PROCEDURE  get_reference_value  (IN  _table  VARCHAR(100), IN  _id  VARCHAR(100), IN  _field  VARCHAR(100), IN  _table_id  VARCHAR(100))  BEGIN
SET @query = CONCAT('Select ',_field,' from  ',_table ,'   WHERE ', _table_id, ' = ', _id );
PREPARE stmt FROM @query;
EXECUTE stmt; -- execute statement
DEALLOCATE PREPARE stmt; -- release the statement memory.
END$$

DROP PROCEDURE IF EXISTS `get_row`$$
CREATE   PROCEDURE `get_row`(IN source varchar(100),IN source_id VARCHAR(100),IN id_value VARCHAR(100))
BEGIN
SET @query = CONCAT("Select * from  ",source ,"  WHERE ", source_id ," = '",id_value,"'");
 PREPARE stmt FROM @query;
 EXECUTE stmt; -- execute statement
 DEALLOCATE PREPARE stmt; -- release the statement memory.
END$$

DROP PROCEDURE IF EXISTS `add_string`$$
CREATE  PROCEDURE `add_string`(_str_id INT , _str_label  VARCHAR(405) , _str_text  VARCHAR(805) , _str_lang  VARCHAR(8) , _str_category  VARCHAR(23))
BEGIN
START TRANSACTION;
INSERT INTO str_management (str_label , str_text , str_lang , str_category) VALUES (_str_label , _str_text , _str_lang , _str_category);
SELECT str_id AS id_value FROM str_management WHERE str_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_string`$$
CREATE   PROCEDURE `get_string`(IN _text VARCHAR(500),IN _category VARCHAR(30),IN _lang VARCHAR(3))
BEGIN
START TRANSACTION;
SELECT str_id, str_text 
FROM str_management
WHERE str_active=1 AND str_label = _text AND str_category = _category AND str_lang = _lang ;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_userproject_detail`$$
CREATE  PROCEDURE `get_userproject_detail`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM userproject
WHERE userproject_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_user_detail`$$
CREATE  PROCEDURE `get_user_detail`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM users
WHERE user_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `remove_config`$$
CREATE   PROCEDURE `remove_config`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE config SET config_active=0
WHERE config_id= _element_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `remove_logs`$$
CREATE   PROCEDURE `remove_logs`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE log SET log_active=0
WHERE log_id= _element_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `remove_project`$$
CREATE  PROCEDURE `remove_project`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE projects SET project_active=0
WHERE project_id= _element_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `remove_str_mng`$$
CREATE   PROCEDURE `remove_str_mng`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE str_management SET str_active=0
WHERE str_id= _element_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `remove_users`$$
CREATE   PROCEDURE `remove_users`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE users SET user_active=0
WHERE user_id= _element_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `remove_user_project`$$
CREATE   PROCEDURE `remove_user_project`(IN _element_id INT)
BEGIN START TRANSACTION; UPDATE userproject SET userproject_active=0 WHERE userproject_id= _element_id; COMMIT; END$$

DROP PROCEDURE IF EXISTS `update_config`$$
CREATE   PROCEDURE `update_config`(_element_id INT , _config_id INT , _project_description  VARCHAR(1005) , _default_lang  VARCHAR(16) , _creator INT , _run_setup  VARCHAR(6))
BEGIN
START TRANSACTION;
UPDATE  config SET config_id = _config_id , project_description = _project_description , default_lang = _default_lang , creator = _creator , run_setup = _run_setup
WHERE (config_id = _element_id);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `update_logs`$$
CREATE   PROCEDURE `update_logs`(_element_id INT , _log_id INT , _log_user_id INT , _log_time  VARCHAR(205))
BEGIN
START TRANSACTION;
UPDATE  log SET log_id = _log_id , log_user_id = _log_user_id , log_time = _log_time
WHERE (log_id = _element_id);
COMMIT;
END$$


DROP PROCEDURE IF EXISTS `update_project`$$
CREATE   PROCEDURE `update_project`(_element_id INT , _project_id INT , _project_title  VARCHAR(255) , _project_description  VARCHAR(1005) , _project_icon  LONGBLOB )
BEGIN
START TRANSACTION;
UPDATE  projects SET project_id = _project_id , project_title = _project_title , project_description = _project_description , project_icon = _project_icon
WHERE (project_id = _element_id);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `update_str_mng`$$
CREATE   PROCEDURE `update_str_mng`(_element_id INT , _str_id INT , _str_text  VARCHAR(805))
BEGIN
START TRANSACTION;
UPDATE  str_management SET str_id = _str_id , str_text = _str_text
WHERE (str_id = _element_id);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `update_users`$$
CREATE   PROCEDURE `update_users`(_element_id INT , _user_id INT , _user_name  VARCHAR(55) , _user_mail  VARCHAR(105) , _user_usergroup INT , _user_password  VARCHAR(40) , _user_picture  LONGBLOB )
BEGIN
START TRANSACTION;
UPDATE  users SET user_id = _user_id , user_name = _user_name , user_mail = _user_mail , user_usergroup = _user_usergroup , user_password = _user_password , user_picture = _user_picture
WHERE (user_id = _element_id);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `update_user_project`$$
CREATE   PROCEDURE `update_user_project`(_element_id INT , _userproject_id INT , _project_id INT , _user_role  VARCHAR(25))
BEGIN
START TRANSACTION;
UPDATE  userproject SET userproject_id = _userproject_id , project_id = _project_id , user_role = _user_role
WHERE (userproject_id = _element_id);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_config_admin`$$
CREATE PROCEDURE `get_detail_config_admin`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM config_admin
WHERE config_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `update_config_admin`$$
CREATE PROCEDURE `update_config_admin`(_element_id INT , _config_id INT , _editor_url  VARCHAR(105) , _editor_generated_path  VARCHAR(105) , _track_comment_on INT)
BEGIN
START TRANSACTION;
UPDATE  config_admin SET config_id = _config_id , editor_url = _editor_url , editor_generated_path = _editor_generated_path , track_comment_on = _track_comment_on
WHERE (config_id = _element_id);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `add_info`$$
CREATE PROCEDURE `add_info`(_info_id INT , _info_title  VARCHAR(505) , _info_desc  VARCHAR(1005) , _info_link  VARCHAR(505) , _info_type  VARCHAR(25) , _info_order INT)
BEGIN
START TRANSACTION;
INSERT INTO info (info_title , info_desc , info_link , info_type , info_order) VALUES (_info_title , _info_desc , _info_link , _info_type , _info_order);
SELECT info_id AS id_value FROM info WHERE info_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_info`$$
CREATE PROCEDURE `get_detail_info`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM info
WHERE info_id= _row_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_list_info`$$
CREATE  PROCEDURE `get_list_info`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;
					 SET @search_info_title := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM info
WHERE info_active=1   AND (  (info_title LIKE  @search_info_title)  )    ORDER BY info_title ASC ;
ELSE
SELECT * FROM info
WHERE info_active=1   AND (  (info_title LIKE  @search_info_title)  )    ORDER BY info_title ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `remove_info`$$
CREATE PROCEDURE `remove_info`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE info SET info_active=0
WHERE info_id= _element_id;
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `update_info`$$
CREATE PROCEDURE `update_info`(_element_id INT , _info_id INT , _info_title  VARCHAR(505) , _info_desc  VARCHAR(1005) , _info_link  VARCHAR(505) , _info_type  VARCHAR(25) , _info_order INT)
BEGIN
START TRANSACTION;
UPDATE  info SET info_id = _info_id , info_title = _info_title , info_desc = _info_desc , info_link = _info_link , info_type = _info_type , info_order = _info_order
WHERE (info_id = _element_id);
COMMIT;
END$$

DROP TABLE IF EXISTS `info`$$
CREATE TABLE IF NOT EXISTS `info` (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `info_title` varchar(500) NOT NULL,
  `info_desc` varchar(1000) DEFAULT NULL,
  `info_link` varchar(500) DEFAULT NULL,
  `info_type` enum('Home','Features','Help','Reference') NOT NULL DEFAULT 'Help',
  `info_order` int(2) NOT NULL DEFAULT '1',
  `info_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 $$

DROP PROCEDURE IF EXISTS `add_user_creation`$$
CREATE PROCEDURE `add_user_creation`(_user_creation_id INT , _creation_user_id INT , _confirmation_code  VARCHAR(55) , _confirmation_try INT , _confirmation_expiration INT)
BEGIN
START TRANSACTION;
INSERT INTO user_creation (creation_user_id , confirmation_code , confirmation_try , confirmation_expiration) VALUES (_creation_user_id , _confirmation_code , _confirmation_try , _confirmation_expiration);
SELECT user_creation_id AS id_value FROM user_creation WHERE user_creation_id = LAST_INSERT_ID();
COMMIT;
END$$

DROP PROCEDURE IF EXISTS `get_detail_user_creation`$$
CREATE PROCEDURE `get_detail_user_creation`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM user_creation
WHERE user_creation_id= _row_id;
COMMIT;
END$$


INSERT INTO `info` (`info_id`, `info_title`, `info_desc`, `info_link`, `info_type`, `info_order`, `info_active`) VALUES
(1, 'ReLiS : a tool for conducting Systematic Review', 'Systematic Review (SR) is a technique used to search for evidence in scientific literature that is conducted in a formal manner, applying well-defined steps, according to a previously elaborated protocol. As the SR has many steps and activities, its execution is laborious and repetitive. Therefore, the support of a computational tool is essential to improve the quality of its application. ReLiS is a tool to help in  planning, conducting and reporting the review.<br/>\r\n<i>ReLiS stands for <b>Revue Litteraire Systématique</b> which is French for <b> Systematic Literature Reviews</b>  Relis literally translates to “reread”.</i>\r\n', '', 'Home', 1, 1),
(2, 'Plan the review', 'ReLiS features a domain specific language to define a protocol that will guide the process of conducting the review. That protocol will help to generate a project tailored to the needs of the review.', '', 'Features', 1, 1),
(3, 'Import papers', 'ReLiS allow to add papers manually or import a list of them from CSV, BibTeX or EndNote files', '', 'Features', 2, 1),
(4, 'Screen papers', 'Each paper can be assigned automatically or manually to a number of reviewers and a reviewer  can start screening the corpus and decide which paper to include and which one to exclude.', '', 'Features', 3, 1),
(5, 'Create user account', '', 'create_account.mp4', 'Help', 1, 1),
(6, 'Add reviewers to project', '', 'add_user_to_project.mp4', 'Help', 2, 1),
(7, 'Import papers', 'ReLiS allow to add papers manually or import a list of them from CSV, BibTeX or EndNote files', 'add_papers.mp4', 'Help', 4, 1),
(8, 'Learn more about the tool in:', '<p>B. Bigendako. and E. Syriani. Modeling a Tool for Conducting Systematic Reviews Iteratively. <i>Proceedings of the 6th International Conference on Model-Driven Engineering and Software Development</i>. pp. 552–559. (2018).</p>\r\n<p><center></center></p>', '', 'Reference', 1, 1),
(9, 'Assess quality', 'Researchers can assess the quality of selected studies by using forms customised to the review.', '', 'Features', 4, 1),
(10, 'Do data extraction', 'Researchers extracts the relevant data from each included paper according to the categories of a classification scheme he predefined for the study.', '', 'Features', 6, 1),
(11, 'Export results', 'Extracted data are automatically synthesized in tables and charts and can be exported for further analysis.', '', 'Features', 7, 1),
(12, 'Add a project', '', 'new_project.mp4', 'Help', 2, 1),
(13, 'Data extraction  (or classification)', '', 'data_extraction.mp4', 'Help', 10, 1),
(14, 'Screening', '', 'screening.mp4', 'Help', 6, 1)$$
