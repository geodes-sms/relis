-- -- tables
DROP TABLE IF EXISTS `installation_info`;;;;
CREATE TABLE IF NOT EXISTS `installation_info` (
  `install_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_tables` text NOT NULL,
  `generated_tables` text NOT NULL,
  `foreign_key_constraint` text,
  `install_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`install_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;;;;


DROP TABLE IF EXISTS `assigned`;;;;
CREATE TABLE IF NOT EXISTS `assigned` (
  `assigned_id` int(11) NOT NULL AUTO_INCREMENT,
  `assigned_paper_id` int(11) NOT NULL,
  `assigned_user_id` int(11) NOT NULL,
  `assigned_note` text,
  `assigned_by` int(11) NOT NULL,
  `assigned_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `assigned_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`assigned_id`),
  KEY `assigned_paper_id_idx` (`assigned_paper_id`),
  KEY `assigned_user_id_idx` (`assigned_user_id`),
  KEY `assigned_by_idx` (`assigned_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;;;;


DROP TABLE IF EXISTS `author`;;;;
CREATE TABLE IF NOT EXISTS `author` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(200) NOT NULL,
  `author_desc` text,
  `author_picture` varchar(300) DEFAULT NULL,
  `author_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;;;;


DROP TABLE IF EXISTS `classification`;;;;
CREATE TABLE IF NOT EXISTS `classification` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_paper_id` int(11) DEFAULT '0',
  `note` varchar(500) DEFAULT NULL,
  class_active  int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;;;;


DROP TABLE IF EXISTS `config`;;;;
CREATE TABLE IF NOT EXISTS `config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_type` varchar(11) NOT NULL DEFAULT 'default',
  `csv_field_separator` varchar(2) NOT NULL DEFAULT ';',
  `csv_field_separator_export` varchar(2) NOT NULL DEFAULT ',',
  `editor_url` varchar(100) DEFAULT NULL,
  `editor_generated_path` varchar(100) DEFAULT NULL,
  `config_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;;;;

INSERT INTO `config` (`config_id`, `config_type`, `csv_field_separator`, `csv_field_separator_export`, `editor_url`, `editor_generated_path`, `config_active`) VALUES
(1, 'default', ';', ',', 'http://127.0.0.1:8080/relis/texteditor', 'C:/relis_workspace', 1);;;;

DROP TABLE IF EXISTS `exclusion`;;;;
CREATE TABLE IF NOT EXISTS `exclusion` (
  `exclusion_id` int(11) NOT NULL AUTO_INCREMENT,
  `exclusion_paper_id` int(11) NOT NULL,
  `exclusion_criteria` int(11) NOT NULL,
  `exclusion_note` text,
  `exclusion_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `exclusion_by` int(11) NOT NULL,
  `exclusion_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`exclusion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;;;;


DROP TABLE IF EXISTS `paper`;;;;
CREATE TABLE IF NOT EXISTS `paper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bibtexKey` varchar(100) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `doi` varchar(1000) DEFAULT NULL,
  `review` varchar(1000) DEFAULT NULL,
  `venueId` int(11) DEFAULT NULL,
  `bibtex` longtext NOT NULL,
  `preview` longtext,
  `paper_excluded` int(1) NOT NULL DEFAULT '0',
  `paper_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_Paper_bibtexKey` (`bibtexKey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;;;;



DROP TABLE IF EXISTS `paperauthor`;;;;
CREATE TABLE IF NOT EXISTS `paperauthor` (
  `paperauthor_id` int(11) NOT NULL AUTO_INCREMENT,
  `paperId` int(11) NOT NULL,
  `authorId` int(11) NOT NULL,
  `paperauthor_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`paperauthor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;;;;


DROP TABLE IF EXISTS `ref_tables`;;;;
CREATE TABLE IF NOT EXISTS `ref_tables` (
  `reftab_id` int(11) NOT NULL AUTO_INCREMENT,
  `reftab_label` varchar(50) NOT NULL,
  `reftab_table` varchar(50) NOT NULL,
  `reftab_desc` varchar(200) NOT NULL,
  `reftab_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`reftab_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;;;;

INSERT INTO `ref_tables` VALUES (1,'ref_exclusioncrieria','zref_exclusioncrieria','Exclusion criteria',1);;;;

DROP TABLE IF EXISTS `str_management`;;;;
CREATE TABLE IF NOT EXISTS `str_management` (
  `str_id` int(11) NOT NULL AUTO_INCREMENT,
  `str_label` varchar(500) NOT NULL,
  `str_text` varchar(1000) NOT NULL,
  `str_category` varchar(20) NOT NULL DEFAULT 'default',
  `str_lang` varchar(3) NOT NULL DEFAULT 'en',
  `str_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`str_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;;;;



DROP TABLE IF EXISTS `venue`;;;;
CREATE TABLE IF NOT EXISTS `venue` (
  `venue_id` int(11) NOT NULL AUTO_INCREMENT,
  `venue_abbreviation` varchar(20) NOT NULL,
  `venue_fullName` longtext,
  `venue_year` smallint(6) NOT NULL,
  `venue_volume` smallint(6) DEFAULT NULL,
  `venue_totalNumPapers` smallint(6) DEFAULT NULL,
  `venue_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`venue_id`),
  UNIQUE KEY `IX_Venue` (`venue_abbreviation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;;;;


DROP TABLE IF EXISTS `zref_exclusioncrieria`;;;;
CREATE TABLE IF NOT EXISTS `zref_exclusioncrieria` (
  `ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_value` varchar(50) NOT NULL,
  `ref_desc` varchar(250) DEFAULT NULL,
  `ref_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ref_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;;;;

-- VIEWS

DROP VIEW IF EXISTS `view_paper_assigned`;;;;

CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW `view_paper_assigned` AS SELECT DISTINCT(P.id) as Pid , P.*, A.assigned_user_id 	 FROM  	paper P INNER JOIN  assigned A ON (P.id = A.assigned_paper_id) WHERE assigned_active=1 AND paper_active=1 AND paper_excluded=0;;;;

DROP VIEW IF EXISTS `view_paper_pending`;;;;
CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW `view_paper_pending` AS SELECT * FROM  	paper WHERE id NOT IN(  SELECT DISTINCT(P.id) as id FROM paper P INNER JOIN  classification C ON (P.id = C.class_paper_id) WHERE P.paper_active=1 AND C.class_active=1 ) AND paper_active=1 AND paper_excluded=0;;;;

DROP VIEW IF EXISTS `view_paper_processed`;;;;

CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW `view_paper_processed` AS SELECT DISTINCT(P.id) as Pid , P.* FROM paper P INNER JOIN  classification C ON (P.id = C.class_paper_id) WHERE paper_active=1 AND paper_excluded=0 AND class_active=1;;;;

-- --STORED PROCEDURES

DROP PROCEDURE IF EXISTS  count_list;;;;
CREATE   PROCEDURE  count_list  (IN  source  VARCHAR(200), IN  condition_stat  VARCHAR(1000))  BEGIN
SET @query = CONCAT('Select count(*) as nombre from  ',source ,'   WHERE 1=1  ', condition_stat );
PREPARE stmt FROM @query;
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;
END;;;;

DROP PROCEDURE IF EXISTS  get_list ;;;;
CREATE   PROCEDURE  get_list  (IN  _source  VARCHAR(100), IN  _fields  VARCHAR(1000), IN  _condition_stat  VARCHAR(1000))  BEGIN
SET @query = CONCAT('Select ',_fields,' from  ',_source ,'   WHERE 1=1   ', _condition_stat );
 PREPARE stmt FROM @query;
 EXECUTE stmt;
 DEALLOCATE PREPARE stmt; 
END;;;;

DROP PROCEDURE IF EXISTS `get_assignations`;;;;
CREATE  PROCEDURE `get_assignations`(_paperId INT)
BEGIN
START TRANSACTION;
SELECT * FROM assigned WHERE (assigned_paper_id = _paperId AND assigned_active=1);
COMMIT; 
END;;;;

DROP PROCEDURE IF EXISTS  get_author ;;;;
CREATE   PROCEDURE  get_author  (IN  _start_by  INT, IN  _range  INT, IN  _search  VARCHAR(100))  BEGIN
START TRANSACTION;
SET @search_author_name := CONCAT('%',_search,'%') ;  SET @search_author_desc := CONCAT('%',_search,'%') ; 
SELECT  author_id , author_name , author_desc FROM author
WHERE author_active=1  AND (  (author_name LIKE  @search_author_name)  OR (author_desc LIKE  @search_author_desc)  )  ORDER BY  author_name ASC LIMIT _start_by , _range;	
COMMIT;
END;;;;


DROP PROCEDURE IF EXISTS  get_paper_exclusion_info ;;;;
CREATE   PROCEDURE  get_paper_exclusion_info  ( _paperId  INT)  BEGIN
START TRANSACTION;
SELECT * FROM exclusion WHERE (exclusion_paper_id = _paperId AND exclusion_active=1);
COMMIT;
END;;;;

DROP PROCEDURE IF EXISTS  get_reference_table ;;;;
CREATE   PROCEDURE  get_reference_table  ( _configId  VARCHAR(100))  BEGIN
START TRANSACTION;
SELECT * FROM ref_tables WHERE (reftab_label = _configId AND reftab_active=1);
COMMIT;
END;;;;

DROP PROCEDURE IF EXISTS  get_reference_tables_list ;;;;
CREATE   PROCEDURE  get_reference_tables_list  ()  BEGIN
START TRANSACTION;
SELECT * FROM ref_tables WHERE reftab_active=1 order by  reftab_desc;
COMMIT;
END;;;;

DROP PROCEDURE IF EXISTS  get_reference_value ;;;;
CREATE   PROCEDURE  get_reference_value  (IN  _table  VARCHAR(100), IN  _id  VARCHAR(100), IN  _field  VARCHAR(100), IN  _table_id  VARCHAR(100))  BEGIN
SET @query = CONCAT('Select ',_field,' from  ',_table ,'   WHERE ', _table_id, ' = ', _id );
PREPARE stmt FROM @query;
EXECUTE stmt;  
DEALLOCATE PREPARE stmt;  
END;;;;

DROP PROCEDURE IF EXISTS  get_result_count ;;;;
CREATE   PROCEDURE  get_result_count  (IN  _fields  VARCHAR(100))  BEGIN
SET @query = CONCAT('SELECT ',_fields,' AS field,count(*) AS nombre from classification,paper WHERE class_paper_id=id  AND paper_excluded = 0	 AND  paper_active=1 AND class_active=1 group by  ',_fields );
PREPARE stmt FROM @query;
EXECUTE stmt;  
DEALLOCATE PREPARE stmt;  
END;;;;

DROP PROCEDURE IF EXISTS  get_row ;;;;
CREATE   PROCEDURE  get_row  (IN  source  VARCHAR(100), IN  source_id  VARCHAR(100), IN  id_value  VARCHAR(100))  BEGIN
SET @query = CONCAT("Select * from  ",source ,"  WHERE ", source_id ," = '",id_value,"'");
PREPARE stmt FROM @query;
EXECUTE stmt;  
DEALLOCATE PREPARE stmt;  
END;;;;

DROP PROCEDURE IF EXISTS  get_string ;;;;
CREATE   PROCEDURE  get_string  (IN  _text  VARCHAR(500), IN  _category  VARCHAR(30), IN  _lang  VARCHAR(3))  BEGIN
START TRANSACTION;
SELECT str_id, str_text FROM str_management WHERE str_active=1 AND str_label = _text AND str_category = _category AND str_lang = _lang ;
COMMIT;
END;;;;

DROP PROCEDURE IF EXISTS  include_paper ;;;;
CREATE   PROCEDURE  include_paper  (IN  _paper_id  INT)  BEGIN
START TRANSACTION;
UPDATE paper SET  paper_excluded = 0  WHERE id = _paper_id ;
COMMIT;
END;;;;



DROP PROCEDURE IF EXISTS  get_classifications ;;;;
CREATE   PROCEDURE  get_classifications  ( _paperId  INT)  BEGIN
START TRANSACTION;
SELECT * 
FROM classification 
WHERE (class_paper_id = _paperId AND class_active=1);
COMMIT;
END;;;;

DROP PROCEDURE IF EXISTS  get_classification_paper ;;;;
CREATE   PROCEDURE  get_classification_paper  ( _classificationId  INT)  BEGIN
START TRANSACTION;
SELECT class_paper_id
FROM classification 
WHERE (class_id = _classificationId);
COMMIT;
END;;;;


DROP PROCEDURE IF EXISTS  exclude_paper ;;;;
CREATE   PROCEDURE  exclude_paper  (IN  _paper_id  INT)  BEGIN
START TRANSACTION;
UPDATE paper SET  paper_excluded = 1  WHERE id = _paper_id ;
  COMMIT;
 END;;;;
 
 
DROP PROCEDURE IF EXISTS  count_papers_assigned ;;;;
CREATE   PROCEDURE  count_papers_assigned  (IN  _user_id  INT, IN  _search  VARCHAR(500))  BEGIN 
 START TRANSACTION;
 SET @search_bibtexKey := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_title := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_preview := CONCAT('%',TRIM(_search),'%') ; 
 SELECT count(*) as nbr FROM view_paper_assigned
 WHERE paper_active=1 AND assigned_user_id = _user_id AND( (bibtexKey LIKE @search_bibtexKey) OR (title LIKE @search_title) OR (preview LIKE @search_preview) ); 
 COMMIT;
 END;;;;

DROP PROCEDURE IF EXISTS  count_papers_pending ;;;;
CREATE   PROCEDURE  count_papers_pending  (IN  _search  VARCHAR(100))  BEGIN START TRANSACTION; SET @search_bibtexKey := CONCAT('%',TRIM(_search),'%') ; SET @search_title := CONCAT('%',TRIM(_search),'%') ; SET @search_preview := CONCAT('%',TRIM(_search),'%') ; SELECT count(*) as nbr FROM view_paper_pending WHERE paper_active=1 AND ( (bibtexKey LIKE @search_bibtexKey) OR (title LIKE @search_title) OR (preview LIKE @search_preview) ) ; COMMIT; END ;;;;

DROP PROCEDURE IF EXISTS  count_papers_processed ;;;;
CREATE   PROCEDURE  count_papers_processed  (IN  _search  VARCHAR(100))  BEGIN START TRANSACTION; SET @search_bibtexKey := CONCAT('%',TRIM(_search),'%') ; SET @search_title := CONCAT('%',TRIM(_search),'%') ; SET @search_preview := CONCAT('%',TRIM(_search),'%') ; SELECT count(*) as nbr FROM view_paper_processed WHERE paper_active=1 AND ( (bibtexKey LIKE @search_bibtexKey) OR (title LIKE @search_title) OR (preview LIKE @search_preview) ) ; COMMIT; END;;;;




DROP PROCEDURE IF EXISTS  get_list_papers_assigned ;;;;
CREATE   PROCEDURE  get_list_papers_assigned  (IN  _user_id  INT, IN  _start_by  INT, IN  _range  INT, IN  _search  VARCHAR(500))  BEGIN 
 START TRANSACTION;
 SET @search_bibtexKey := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_title := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_preview := CONCAT('%',TRIM(_search),'%') ; 
 SELECT id , bibtexKey , title , preview FROM view_paper_assigned
 WHERE paper_active=1 AND assigned_user_id = _user_id AND( (bibtexKey LIKE @search_bibtexKey) OR (title LIKE @search_title) OR (preview LIKE @search_preview) ) ORDER BY id ASC LIMIT _start_by , _range; 
 COMMIT;
 END;;;;

DROP PROCEDURE IF EXISTS  get_list_papers_pending ;;;;
CREATE   PROCEDURE  get_list_papers_pending  (IN  _start_by  INT, IN  _range  INT, IN  _search  VARCHAR(500))  BEGIN 
 START TRANSACTION;
 SET @search_bibtexKey := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_title := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_preview := CONCAT('%',TRIM(_search),'%') ; 
 SELECT id , bibtexKey , title , preview FROM view_paper_pending
 WHERE paper_active=1 AND ( (bibtexKey LIKE @search_bibtexKey) OR (title LIKE @search_title) OR (preview LIKE @search_preview) ) ORDER BY id ASC LIMIT _start_by , _range; 
 COMMIT;
 END;;;;

DROP PROCEDURE IF EXISTS  get_list_papers_processed ;;;;
CREATE   PROCEDURE  get_list_papers_processed  (IN  _start_by  INT, IN  _range  INT, IN  _search  VARCHAR(500))  BEGIN 
 START TRANSACTION;
 SET @search_bibtexKey := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_title := CONCAT('%',TRIM(_search),'%') ; 
 SET @search_preview := CONCAT('%',TRIM(_search),'%') ; 
 SELECT id , bibtexKey , title , preview FROM view_paper_processed
 WHERE paper_active=1 AND ( (bibtexKey LIKE @search_bibtexKey) OR (title LIKE @search_title) OR (preview LIKE @search_preview) ) ORDER BY id ASC LIMIT _start_by , _range; 
 COMMIT;
 END;;;;