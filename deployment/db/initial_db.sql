-- MySQL dump 10.16  Distrib 10.1.44-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: relis_db
-- ------------------------------------------------------
-- Server version	10.1.44-MariaDB-0+deb9u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `relis_db`
--

/*!40000 DROP DATABASE IF EXISTS `relis_db`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `relis_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `relis_db`;

--
-- Table structure for table `admin_config`
--

DROP TABLE IF EXISTS `admin_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_label` varchar(100) NOT NULL,
  `config_value` varchar(100) NOT NULL,
  `config_description` varchar(500) DEFAULT NULL,
  `config_user` int(11) NOT NULL DEFAULT '0',
  `config_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_config`
--

LOCK TABLES `admin_config` WRITE;
/*!40000 ALTER TABLE `admin_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'default','Admin','Admin project','en',1,0,30,1);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_admin`
--

DROP TABLE IF EXISTS `config_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_admin` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_type` varchar(100) NOT NULL,
  `editor_url` varchar(100) NOT NULL,
  `editor_generated_path` varchar(100) NOT NULL,
  `track_comment_on` int(2) NOT NULL DEFAULT '0',
  `list_trim_nbr` int(3) NOT NULL DEFAULT '80',
  `first_connect` int(2) NOT NULL DEFAULT '1',
  `config_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_admin`
--

LOCK TABLES `config_admin` WRITE;
/*!40000 ALTER TABLE `config_admin` DISABLE KEYS */;
INSERT INTO `config_admin` VALUES (1,'','http://localhost:8088/relis/texteditor','/u/relis/public_html/workspace/dslforge_workspace',0,80,1,1);
/*!40000 ALTER TABLE `config_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info` (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `info_title` varchar(500) NOT NULL,
  `info_desc` varchar(15000) DEFAULT NULL,
  `info_link` varchar(500) DEFAULT NULL,
  `info_type` enum('Home','Features','Help','Reference') NOT NULL DEFAULT 'Help',
  `info_order` int(2) NOT NULL DEFAULT '1',
  `info_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info`
--

LOCK TABLES `info` WRITE;
/*!40000 ALTER TABLE `info` DISABLE KEYS */;
INSERT INTO `info` VALUES (1,'ReLiS : a tool for conducting Systematic Review','<div class="container mt-5">
  <div class="announcement-box" style="background-color: #d4edda; border: 1px solid #e2e2e2; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin: 20px 0;">
    <div class="announcement-title" style="font-weight: bold; font-size: 1.5em; margin-bottom: 10px;">New Release Announcement!</div>
    <div class="announcement-message" style="font-size: 1.1em;">
      We are excited to announce the latest release of our project. You can use your <strong>existing credentials</strong> from previous releases to access the new features. Please note that projects from previous releases are <strong>not imported</strong> but can be accessed at 
      <a href="https://relis-archive.iro.umontreal.ca" target="_blank"><strong>relis-archive.iro.umontreal.ca</strong></a>.
    </div>
  </div>
</div>

Systematic review is a technique used to search for evidence in scientific literature that is conducted in a formal manner, following a well-defined process, according to
a previously elaborated protocol. Conducting a systematic reviews involves many steps over a long period of time, and is often laborious and repetitive. This is why we have created ReLiS which provides essential software support to reviewers in conducting
high quality systematic reviews. With ReLiS, you can planning, conducting, and reporting your review.\r\n
<p>\r\n<i>ReLiS stands for Revue Litteraire SystÃ©matique which is French for Systematic Literature Reviews Relis literally translates to â€œrereadâ€.</i>\r\n</p>\r\n','','Home',1,1),(2,'Plan the review','ReLiS features a domain-specific language to define
a protocol that will guide the process of conducting the review. That protocol will help to generate a project tailored to the needs of the review. ReLiS supports an iterative process: you can revise the protocol and add more articles at any time.','','Features',1,1),(3,'Import
papers','ReLiS allows you to add references manually or import a list of them from CSV, BibTeX or EndNote files.\r\n
<hr>\r\n
<h3>Import BibTeX references</h3>\r\n
<p>\r\nMake sure the file is <b>encoded in ANSI or Western Windows</b>. No funny characters should be present.\r\nAlso, make sure each reference is well-formatted using the format:\r\n</p>\r\n<pre>\r\n@type{key, field={value} }\r\n</pre>\r\n
<ul>\r\n
    <li><i>type</i> can be any valid BibTeX entry type, such as <code>article</code>, <code>inproceedings</code>, <code>inbook</code>, etc. </li>\r\n
    <li><i>key</i> uniquely identifies the reference. It is optional, but the comma after the curly bracket is mandatory. So <code>@article{, field={value}}</code> is also valid. If the key is not provided, ReLiS will generate a unique one automatically based
        on the last name of the first author and the year of publication.</li>\r\n
    <li><i>field</i> can be a standard BibTeX field or not. Make sure you have the <code>abstract</code> field (to be displayed when screening). You may also wish to have the <code>doi</code> or <code>paper</code> fields to display the link to the online
        article. </li>\r\n
    <li><i>value</i> can be the enclosed in curly brackets <code>{  }</code> or double quotes <code>\"  \"</code>.</li>\r\n
    <li>Note that any white space or new lines between these keywords will be ignored. </li>\r\n</ul>\r\n
<p>\r\n<a href=\ "https://www.openoffice.org/bibliographic/bibtex-defs.html\" style=\ "text-decoration: underline;\">Here is the complete specification of the BibTeX format</a>.\r\n</p>\r\n
<hr>\r\n
<h3>Import EndNote references</h3>\r\n
<a name=\ "bibler\"></a>\r\n
<p>\r\nTo import references from EndNote, you need to use a special export style in EndNote: <b>BiBler Export</b>.\r\nThis export style ships with the tool <a href=\ "https://github.com/geodes-sms/bibler\" style=\ "text-decoration: underline;\">BiBler</a>.
    You can either <a href=\ "https://github.com/geodes-sms/bibler/releases\" style=\ "text-decoration: underline;\">install it</a> (Windows only) or simply download the export style <a href=\
        "https://github.com/geodes-sms/bibler/blob/master/src/bibler/external/BiBler%20Export.ens\" style=\ "text-decoration: underline;\">directly from here</a>.\r\n</p>\r\n
<p>\r\nWith EndNote already installed on your computer, simply open <i>BiBler Export.ens</i> and save. It will then permanently be installed in your EndNote application.\r\n</p>\r\n
<p>\r\nDepending on which digital library you downloaded the references from, you may need to ensure that the exported file is a well-formatted BibTeX file. See above for more information.\r\n</p>\r\n\r\n
<h4><b>Import references from EndNote to ReLiS on Windows or Mac</b></h4>\r\n\r\n
<p>These steps assume all your references are in EndNote and that you have removed duplicates. We assume your library is called <i>MyReferences.enl</i>.\r\n</p>\r\n
<ol>\r\n
    <li>In your EndNote library, export all the references using the <a href=\ "#bibler\" style=\ "text-decoration: underline;\">BiBler Export style</a>. This creates a file <i>MyReferences.txt</i></li>\r\n
    <li>Open <i>MyReferences.txt</i> in <a href=\ "https://notepad-plus-plus.org/\" style=\ "text-decoration: underline;\">Notepad++</a> or <a href=\ "https://support.apple.com/en-ca/guide/textedit/welcome/mac\" style=\ "text-decoration: underline;\">TextEdit</a></li>\r\n
    <li>Replace all <code>@article{</code> by <code>@article{,</code></li>\r\n
    <li>Replace all <code>%</code> by <code>\\%</code></li>\r\n
    <li>Replace any funny looking characters by their regular counterparts</li>\r\n **The following assumes you are running under Windows. It may be done on Mac, you should check how.**\r\n
    <li>Open <i>MyReferences.txt</i> in <a href=\ "https://notepad-plus-plus.org/\" style=\ "text-decoration: underline;\">Notepad++</a></li>\r\n
    <li>Click Encoding > Convert to UTF-8</li>\r\n
    <li>Save the file as <i>MyReferences.bib</i></li>\r\n
    <li>Open <a href=\ "#bibler\" style=\ "text-decoration: underline;\">BiBler</a></li>\r\n
    <li>Click File > Open, choose <i>MyReferences.bib</i>, choose the option EndNote file</li>\r\n
    <li>Save</li>\r\n **Now you can go back to your Mac**\r\n
    <li>In Chrome (not Safari), go to your project in ReLiS</li>\r\n
    <li>In the menu, click Import Papers > Import BibTeX</li>\r\n
    <li>Choose the file <i>MyReferences.bib</i></li>\r\n
    <li>Upload</li>\r\n</ol>\r\n\r\n
<p>This may take a while depending on the number of references you are importing. When the import is complete, make sure the right number of references was imported. If not, then this means that there are still some funny characters left in <i>MyReferences.bib</i>.
    Check what the last imported reference is in ReLiS and go back to <i>MyReferences.bib</i> to correct the funny characters. You can continue at step 12.\r\n</p>\r\n
<hr>\r\n
<h3>Authors and venues</h3>\r\n
<p>When you import references in your project, ReLiS automatically indexes all the authors of every reference. This may be useful if you want to retrieve some statistics on the authors. In the home page of your project, go to the Authors menu. You can find
    the list of all the authors or only the first authors of each reference.</p>\r\n\r\n
<p>ReLiS also automatically indexes the venue of every reference. This corresponds to the conference or the journal where each reference is published as well as the publication year. This may be useful if you want to retrieve some statistics about the publication
    venues or publication year of the references. In the home page of your project, go to the Venues menu. More specifically, here is the field of the venue indexed for each <a href=\ "https://www.openoffice.org/bibliographic/bibtex-defs.html\" style=\
        "text-decoration: underline;\">BibTeX entry type</a>:</p>\r\n
<ul>\r\n
    <li><code>article</code>: <code>journal</code> (if the reference is an article published in a journal)</li>\r\n
    <li><code>inbook</code>: <code>title</code> (if the reference is an chapter published in a book)</li>\r\n
    <li><code>incollection</code>, <code>inproceedings</code>: <code>booktitle</code> (if the reference is an article published in a conference proceedings)</li>\r\n</ul>\r\n','pGOWnVqGByQ','Help',3,1),(4,'Screen articles','Each article can be assigned automatically
or manually to a number of reviewers. Reviewers screen the corpus of articles by deciding which article to include and which one to exclude from the review. ReLiS brings invaluable assistance to conflicts between reviewers decisions, resolution of conflict,
and many statistics on the screening process.','','Features',3,1),(5,'Create user account','
<p>To get started with ReLiS, you must create a user. Follow the steps in the video above.\r\n</p>\r\n
<p>\r\nIf you are only looking to explore ReLiS, a read-only access is available, you can click on the Demo user button in the menu.\r\n</p>\r\n
<p>Now you are ready to <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/12\" style=\ "text-decoration: underline;\">create a project</a>.\r\n</p>','-xpOz2Gf__w','Help',1,1),(6,'Add reviewers to project','
<p>The video above describes how to add users to your ReLiS project. The following lists the different user roles.\r\n</p>\r\n
<hr>\r\n
<h3>Administrator</h3>\r\n
<p>\r\nThe administrator has all read and write access to all projects. A user with adminitrator role is automatically part of all projects in ReLiS. This user will however not show up in the list of users on the home page of the project. The administrator
    can also query the databases directly from the Query Database menu. He also has write access to the database, therefore it should be used with caution. Only very advanced users with programming skills should be administrators.\r\n</p>\r\n\r\n
<h3>Project Manager</h3>\r\n
<p>\r\nThe project manager has all the administrating privileges but only for his project. He can modify the settings and configuration of the project, manage users, and query the database. He can also assign articles to users and import articles.\r\n</p>\r\n\r\n
<h3>Standard user</h3>\r\n
<p>\r\nThe following are considered standard users. They have access to the project but cannot manage it.\r\n</p>\r\n
<h4><b>Reviewer</b></h4>\r\n
<p>\r\nThe reviewer can conduct the screening, perform the quality assurance, and perform data extraction from articles. He has read-access to other information, such as statistics, validation, and list of articles. He can modify exclusion criteria. He does
    not \r\n</p>\r\n
<h4><b>Validator</b></h4>\r\n
<p>\r\nThe validator has the same rights as the reviewer, but can also perform validations.\r\n</p>\r\n
<h4><b>Guest</b></h4>\r\n
<p>\r\nHas very limited access in read-only to the project. This is equivalent to the Demo user, but for a specific project only.\r\n</p>\r\n
<hr>\r\n
<h3>Access rights per user role</h3>\r\n
<style type=\ "text/css\">
    \r\n.tg {
        border-collapse: collapse;
        border-spacing: 0;
    }
    
    \r\n.tg td {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        \r\n overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }
    
    \r\n.tg th {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        \r\n font-weight: normal;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }
    
    \r\n.tg .tg-9wq8 {
        border-color: inherit;
        text-align: center;
        vertical-align: middle
    }
    
    \r\n.tg .tg-uzvj {
        border-color: inherit;
        font-weight: bold;
        text-align: center;
        vertical-align: middle
    }
    
    \r\n.tg .tg-g7sd {
        border-color: inherit;
        font-weight: bold;
        text-align: left;
        vertical-align: middle
    }
    
    \r\n.tg .tg-yla0 {
        font-weight: bold;
        text-align: left;
        vertical-align: middle
    }
    
    \r\n.tg .tg-nrix {
        text-align: center;
        vertical-align: middle
    }
    
    \r\n
</style>\r\n
<table class=\ "tg\">\r\n
    <thead>\r\n
        <tr>\r\n
            <th class=\ "tg-uzvj\" rowspan=\ "2\">Feature</th>\r\n
            <th class=\ "tg-uzvj\" rowspan=\ "2\">Administrator</th>\r\n
            <th class=\ "tg-uzvj\" rowspan=\ "2\">Project Manager</th>\r\n
            <th class=\ "tg-uzvj\" colspan=\ "3\">Standard user</th>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-uzvj\">Validator</td>\r\n
            <td class=\ "tg-uzvj\">Reviewer</td>\r\n
            <td class=\ "tg-uzvj\">Guest</td>\r\n </tr>\r\n</thead>\r\n
    <tbody>\r\n
        <tr>\r\n
            <td class=\ "tg-g7sd\">Create and modify project</td>\r\n
            <td class=\ "tg-9wq8\">Yes*</td>\r\n
            <td class=\ "tg-9wq8\">W</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-g7sd\">Project configuration</td>\r\n
            <td class=\ "tg-9wq8\">W*</td>\r\n
            <td class=\ "tg-9wq8\">W</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-g7sd\">Project settings</td>\r\n
            <td class=\ "tg-9wq8\">W*</td>\r\n
            <td class=\ "tg-9wq8\">W</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-g7sd\">Import articles</td>\r\n
            <td class=\ "tg-9wq8\">Yes*</td>\r\n
            <td class=\ "tg-9wq8\">Yes</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n
            <td class=\ "tg-9wq8\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Import settings</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Articles</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Authors</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Venues</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Screening</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Screening settings</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Assign reviews</td>\r\n
            <td class=\ "tg-nrix\">Yes*</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\"></td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Exclusion criteria</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Screening statistics</td>\r\n
            <td class=\ "tg-nrix\">Yes*</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">QA</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Validation</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Assign validation</td>\r\n
            <td class=\ "tg-nrix\">Yes*</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Validation settings</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Data extraction/Classification</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Reference categories</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Reporting</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n
            <td class=\ "tg-nrix\">R</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Export results</td>\r\n
            <td class=\ "tg-nrix\">Yes*</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Query database</td>\r\n
            <td class=\ "tg-nrix\">W*</td>\r\n
            <td class=\ "tg-nrix\">W</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n </tr>\r\n
        <tr>\r\n
            <td class=\ "tg-yla0\">Manage users</td>\r\n
            <td class=\ "tg-nrix\">Yes*</td>\r\n
            <td class=\ "tg-nrix\">Yes</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n
            <td class=\ "tg-nrix\">-</td>\r\n </tr>\r\n</tbody>\r\n</table>\r\n
<p>\r\n* means for all projects in ReLiS.<br>\r\nW means write access. Thus, the user can add, remove, or modify any information.<br>\r\nR means read access, thus the user can only see the information without modifying it.\r\n</p>','hWbHN0rJVEw','Help',3,1),(7,'Import
articles','ReLiS allows you to manually add references or import a list from CSV, BibTeX, or EndNote files. The content of the article is available if the URL or DOI is provided.','','Features',2,1),(8,'More about ReLiS','
<p>Read this article about ReLiS, which you can cite as: </p>\r\n
<p><a href=\ "https://goo.gl/jZoWMg\" style=\ "text-decoration: underline;\">B. Bigendako. and E. Syriani. Modeling a Tool for Conducting Systematic Reviews Iteratively. <i>Proceedings of the 6th International Conference on Model-Driven Engineering and Software Development</i>. SCITEPRESS, pp. 552&#8211;559. (2018)</a></p>\r\n
<p>For more detailed information on ReLiS, you can read <a href=\ "https://goo.gl/33RRK4\" style=\ "text-decoration: underline;\">the thesis of Brice Bigendako</a> (in French).</p>\r\n
<p></p>\r\n
<h2 class=\ "lead\">The team</h2>\r\n
<p>ReLiS is developed and maintained by the software engineering lab <a href=\ "http://geodes.iro.umontreal.ca/\" style=\ "text-decoration: underline;\">GEODES</a> in the <a href=\ "http://diro.umontreal.ca/\" style=\
        "text-decoration: underline;\">department of computer science and operations research</a> at the University of Montreal. Under the supervision of <a href=\ "www.iro.umontreal.ca/~syriani\" style=\ "text-decoration: underline;\">Prof. Eugene Syriani</a>, the development is mainly
    supported by the students of the lab.</p>\r\n
<p>The open source code is available on <a href=\ "https://github.com/geodes-sms/relis\" style=\ "text-decoration: underline;\">GitHub</a>.</p>\r\n
<p>If you have any question, you can reach us at <a href="mailto:relis@iro.umontreal.ca" style="text-decoration: underline;">relis@iro.umontreal.ca</a>.</p>','','Reference',1,1),(9,'Assess quality','Reviewers can assess the quality of the selected articles in the corpus using
    a questionnaire customized for the review. There are also several ways of assessing the quality of the review itself.','','Features',4,1),(10,'Extract relevant data','Reviewers can extract the relevant data from each article according to categories
    and a classification scheme customized for each review. The data extraction form can be modified at any time incrementally.','','Features',5,1),(11,'Export results','All extracted data are automatically synthesized in tables and charts, ready to be
    exported in your favorite tool for further analysis.','','Features',6,1),(12,'Create a project','As the project manager of your systematic review, you create your project in ReLiS as shown in the video.\r\n
    <hr>\r\n
    <h3>Initial project configuration</h3>\r\n
    <p>\r\nTo create the project, you must create an initial configuration.\r\nAll projects are accessible to all users of ReLiS. This is one way to crowdsource help and guidance on how to write your configuration file. However, they are accessible only
        in read-only.\r\n</p>\r\n
    <p>\r\nTo create the configuration, you must:\r\n
        <p>\r\n
            <ul>\r\n
                <li>Create a folder with a short name of your project. The name must be <b>without spaces</b>, underscores(_) can be used.</li>\r\n
                <li>Inside the folder, create a model with the name of your project</li>\r\n</ul>\r\n\r\n
            <p>\r\nYou can either write the complete configuration of your project right away or just write the minimum necessary to create an initial version of your project. Then you can come back and enhance it with more features.\r\nAt minimum, you need
                to write:\r\n</p>\r\n<pre>\r\n<b>PROJECT</b> short_name \"Long name\"\r\n<b>CLASSIFICATION</b>\r\n<b>note</b>\r\n</pre>\r\n
            <p>\r\n<code>short_name</code> is name of your project and must be unique with no spaces. it is a variable name so it must start with a letter followed by letters, numbers or underscore.\r\nLong name is the full name of your project and has no
                restrictions if it is enclosed with quotation marks.\r\nThe <code>SCREENING</code> keyword is optional since you can run ReLiS projects without a screening phase if needed.\r\nHowever, the data extraction form is required and described
                after the <code>CLASSIFICATION</code> keyword.\r\nYou need at least one data extraction category. The last line in the code above creates a built-in category for adding a note.\r\n</p>\r\n
            <p>\r\nOnce you have completed your configuration file, right-click on the file and choose Generate. Go back to the Update project page and select the generated installation file. It will be called <code><b>classification_install_</b>short_name<b>.php</b></code>,
                where <code>short_name</code> is as defined above.\r\n</p>\r\n
            <hr>\r\n
            <h3>Next steps</h3>\r\n
            <p>\r\nYou can already start <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/3\" style=\ "text-decoration: underline;\">importing articles</a> and <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/6\" style=\
                    "text-decoration: underline;\">add reviewers</a> in your project.\r\n</p>\r\n
            <p>\r\nYou can find more information on how to configure <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/14\" style=\ "text-decoration: underline;\">the screening</a>, <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/13\" style=\
                    "text-decoration: underline;\">the classification</a>, and <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/18\" style=\ "text-decoration: underline;\">the reporting</a> in the Help pages.\r\n</p>','-KAtbgTbrSM','Help',2,1),(13,'Data extraction','
            <p>The video above describes how to perform the data extraction from the articles in your review.\r\n</p>\r\n
            <hr>\r\n
            <h3>Example</h3>\r\n<pre>\r\n<b>PROJECT</b> chocoholics \"Chocoholics in the North America\"\r\n<b>CLASSIFICATION</b>\r\n<b>Simple</b> year \"Year published\" * [1] : int(4) <span style=\"color:cadetblue\">// this is an example</span>\r\n<b>List</b> country \"Country\" [1] = [\"Canada\", \"Mexico\", \"U.S.A.\"]\r\n<b>DynamicList</b> cocoa \"Cocoa level\" [-1] = [\"Bitter\", \"Bittersweet\", \"SemiSweet\", \"MilkChocolate\"]\r\n</pre>\r\n
            <p>\r\nIn this example, the data extraction form consists of three categories. In the first one, we collect the publication year of each article we are analyzing.\r\nThe first keyword is the type of category. Here, <code>Simple</code> means it
                is a free-form field.\r\nThe name of the category is <code>year</code> and must be unique.\r\nWe want to make it mandatory, so we add a <code>*</code>. This means we must collect the year of publication of every article.\r\n<code>[1]</code>                indicates that only one value is allowed, since there is only one year of publication for each article.\r\nFinally, we specify the type of data that is allowed in the category.\r\nHere it is an integer number with at most four digits.\r\n<code>//</code>                are used for comments and are not processed.\r\n</p>\r\n
            <p>\r\nThe second category collects the country of the study in the article.\r\nSince there are only three countries in North America, we can use a <code>List</code> with the three countries. This cannot be modified later on.\r\n</p>\r\n
            <p>\r\nThe third category collects the cocoa level.\r\n<code>[-1]</code> indicates that multiple values are allowed.\r\nSince there may be more values added later, we make this category a <code>Dynamic List</code>, with some predefined values.
                These can be changed later on.\r\n</p>\r\n
            <p>\r\nYou can then specify a <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/18\" style=\ "text-decoration: underline;\">reporting</a> section in the configuration to create charts about the extracted data.\r\n</p>\r\n
            <hr>\r\n
            <h3>Category types</h3>\r\n
            <p>Here is the list of category types and their options:</p>\r\n
            <h4><b>Simple</b></h4>\r\n
            <p>This is for free-form categories where any text can be entered. The format is:</p>\r\n<pre>\r\n<b>Simple</b> category_name <b>\"</b>Label to display<b>\" * : </b>type<b>(</b>max<b>) style(\"</b>pattern<b>\") = [\"</b>default_value<b>\"]</b>\r\n</pre>\r\n
            <dl>\r\n<dt><code>category_name</code></dt>\r\n
                <dd>Variable that uniquely identifies this category.</dd>\r\n<dt><code>Label to display</code></dt>\r\n
                <dd>Text to display for this category in the data extraction form.</dd>\r\n<dt><code>*</code></dt>\r\n
                <dd>If a * is present, then the category is mandatory. The data extraction form cannot be saved unless a value is entered for this category. The * is optional, if omitted, then the category is optional.</dd>\r\n<dt><code>type</code></dt>\r\n
                <dd>Can be any of the following:
                    <ul>\r\n
                        <li><code>bool</code> for a check box: can be either true or false (1 or 0).</li>\r\n
                        <li><code>int</code> for a number which can be negative.</li>\r\n
                        <li><code>real</code> for decimal numbers.</li>\r\n
                        <li><code>string</code> is a one-line text.</li>\r\n
                        <li><code>text</code> for multi-line text.</li>\r\n
                        <li><code>date</code> for a date displayed as a calendar.</li>\r\n </ul>\r\n </dd>\r\n<dt><code>max</code></dt>\r\n
                <dd>The maximum number of characters the value can have. Default value is 20 characters. Mandatory if the characters of the value exceed the default value.</dd>\r\n<dt><code>pattern</code></dt>\r\n
                <dd>A regular expression to limit the possible values allowed. <a href=\ "https://www.w3schools.com/jsref/jsref_obj_regexp.asp\" style=\ "text-decoration: underline;\">Click here for more information</a>. The <code>style(\"pattern\")</code>                    is optional.</dd>\r\n<dt><code>default_value</code></dt>\r\n
                <dd>A default value that will appear in the initial data extraction form. It must be between square brackets and quotation marks. It must also be in the correct format depending on <code>type</code>, <code>max</code> and <code>pattern</code>.
                    The <code>[\"default_value\"]</code> is optional.</dd>\r\n</dl>\r\n
            <p>For a <code>Simple</code> category, only one value can be entered. If you want to specify multiple values, you can come up with your own notation, such as separating the values with commas. For some categories, like <code>date</code>, this
                is not possible.</p>\r\n
            <h4><b>List</b></h4>\r\n
            <p>This is a category with a predefined list of values. Once the values are defined, <b>it will no longer be possible to change, add, or remove them</b> during the systematic review process. The format is:</p>\r\n<pre>\r\n<b>List</b> category_name <b>\"</b>Label to display<b>\" * [</b>n<b>] = [\"</b>value 1<b>\", \"</b>value 2<b>\"]</b>\r\n</pre>\r\n
            <dl>\r\n<dt><code>category_name</code>, <code>Label to display</code>, <code>*</code></dt>\r\n
                <dd>Same as the <code>Simple</code> category.</dd>\r\n<dt><code>n</code></dt>\r\n
                <dd>The maximum number of values for this category. 0 or 1 means only one value can be selected from the list, 2 means up to two values can be selected, etc. To allow an unlimited number of values, use -1. The number must be between square
                    brackets.
                </dd>\r\n<dt><code>[\"value 1\", \"value 2\"]</code></dt>\r\n
                <dd>You must enumerate all the possible values of the list here. You can have any number of values in this list. Since this cannot be changed later on, the list of values is mandatory. The list must be between square brackets, each value must
                    be between quotation marks and separated by a comma.</dd>\r\n</dl>\r\n
            <h4><b>Dynamic List</b></h4>\r\n
            <p>This is like a <code>List</code>, but the values can be modified during the systematic review process. This is especially useful if you want to share the values in this category between articles in your corpus. It is more flexible than a <code>List</code>                and imposes a stronger normalization of the data than a <code>Simple</code> category. <b>This is the most used type of category</b>. The format is:</p>\r\n<pre>\r\n<b>DynamicList</b> category_name <b>\"</b>Label to display<b>\" * [</b>n<b>] \"</b>Reference name<b>\" = [\"</b>value 1<b>\", \"</b>value 2<b>\"]</b>\r\n</pre>\r\n
            <dl>\r\n<dt><code>category_name</code>, <code>Label to display</code>, <code>*</code>, <code>n</code></dt>\r\n
                <dd>Same as the <code>List</code> category.</dd>\r\n<dt><code>Reference name</code></dt>\r\n
                <dd>Recall that it is possible to change the list values during the systematic review process. To do so, a button called Reference Categories will appear in the menu when you are in the classification mode in ReLiS. <code>Reference name</code>                    is the name of the button that will be displayed under the menu. You can than add, change or remove the possible values there. This is mandatory. Often, this can be the same as the Label to display.</dd>\r\n<dt><code>[\"value 1\", \"value 2\"]</code></dt>\r\n
                <dd>As opposed to a <code>List</code>, the preliminary list of possible values is optional.</dd>\r\n</dl> \r\n
            <h4><b>Dependent Dynamic List</b></h4>\r\n
            <p>This is similar to a <code>Dynamic List</code>, but the values come from another category. This is useful if you want to restrict the possible values, but these values come from another <code>DynamicList</code> category. The format is:</p>\r\n<pre>\r\n<b>DynamicList</b> category_name <b>\"</b>Label to display<b>\" * [</b>n<b>]</b> <b>depends_on</b> dependent_category\r\n</pre>\r\n
            <dl>\r\n<dt><code>category_name</code>, <code>Label to display</code>, <code>*</code>, <code>n</code></dt>\r\n
                <dd>Same as the <code>Dynamic List</code> category.</dd>\r\n<dt><code>dependent_category</code></dt>\r\n
                <dd>The <code>category_name</code> of another <code>Dynamic List</code> from which the values are populated.</dd>\r\n</dl>\r\n
            <h4><b>Sub-categories</b></h4>\r\n
            <p>Any of the above categories can contain sub-categories. A category containing sub-categories is called a super-category. On the data extraction form, adding a value to a super-category will pop-up a secondary form. The format is:</p>\r\n<pre>\r\nSUPER CATEGORY DEFINITION <b>{</b>\r\n  SUB CATEGORY DEFINITION\r\n  SUB CATEGORY DEFINITION\r\n<b>}</b>\r\n</pre>\r\n
            <p>Here, <code>SUPER</code> and <code>SUB CATEGORY DEFINITION</code> are the same as any category type above. All you need is to enclose the sub-categories between curly brackets.\r\n</p>\r\n
            <h4><b>Built-in categories</b></h4>\r\n
            <p>The following are built-in categories:</p>\r\n
            <dl>\r\n<dt><code>note</code></dt>\r\n
                <dd>A text for leaving notes or comments. It is equivalent to <pre>Simple note \"Note\" [1] : text(500)</pre></dd>\r\n</dl>\r\n\r\n
            <p>You can <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/19\" style=\ "text-decoration: underline;\">regenerate the configuration file</a> at any point in time, but beware that some data may be lost if not modified properly.\r\n</p>','trB7B28kCF4','Help',6,1),(14,'Screening','
            <p>\r\nThe screening step follows the protocol you have specified in the configuration file. You may have multiple <a href=\ "#screen\" style=\ "text-decoration: underline;\">screening phases</a>, a validation phase, and a <a href=\ "#qa\" style=\
                    "text-decoration: underline;\">quality assurance phase</a>. The video on how to <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/12\" style=\ "text-decoration: underline;\">create a project</a> illustrates how to configure these steps.\r\n</p>\r\n
            <p>\r\nThe video above demonstrates how screening is performed in ReLiS.\r\n</p>\r\n
            <hr>\r\n
            <h3>
                <a name=\ "screen\"></a>Adding a screening process</h3>\r\n
            <p>If your systematic review includes a screening process (which is in most cases), the format is:\r\n</p>\r\n<pre>\r\n<b>PROJECT</b> short_name \"Long name\"\r\n\r\n<b>SCREENING</b>\r\n<b>Reviews</b> n\r\n<b>Conflict on</b> conflict_type <b>resolved_by</b> conlfict_resolution\r\n<b>Criteria = [\"</b>EC1<b>\", \"</b>EC2<b>\"]</b> <span style=\"color:cadetblue\">// optional</span>\r\n<b>Sources = [\"</b>Source 1<b>\", \"</b>Source 2\"]</b> <span style=\"color:cadetblue\">// optional</span>\r\n<b>Strategies = [\"</b>Database search<b>\", \"</b>Manual entry<b>\", \"</b>Snowballing\"]</b> <span style=\"color:cadetblue\">// optional</span>\r\n<b>Validation</b> x<b>%</b> validation_type <span style=\"color:cadetblue\">// optional</span>\r\n<b>Phases</b> \"Title\" \"Description\"  <b>Fields(</b>field1, field2<b>)</b> <span style=\"color:cadetblue\">// can be repeated multiple times</span>\r\n\r\n<b>CLASSIFICATION</b>\r\n...\r\n</pre>\r\n
            <dl>\r\n<dt><code>Reviews</code></dt>\r\n
                <dd><code>n</code> specifies the number of <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/6\" style=\ "text-decoration: underline;\">reviewers</a> to assign to a single article to decide on its inclusion/exclusion. For example, 2 means
                    every article will be screened by two reviewers.</dd>\r\n<dt><code>conflict_type</code></dt>\r\n
                <dd>In case multiple <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/6\" style=\ "text-decoration: underline;\">reviewers</a> are assigned to each article (so <code>n > 1</code> on the previous line), there may be conflicts in their
                    decision when screening the same article. <code>conflict_type</code> can be one of the following values. <code>Decision</code> means that if one reviewer included the article, but another one excluded it. <code>Criteria</code> is like
                    <code>Decision</code>, but also if both excluded it but not form the same exclusion criteria.</dd>\r\n<dt><code>resolved_by</code></dt>\r\n
                <dd>This indicates how conflicts between reviews (see above) shall be resolved. <code>resolved_by</code> can be one of the following values. <code>Unanimity</code> means that the article will be marked as <i>in conflict</i> until all reviewers
                    assigned to the article agree on the decision of inclusion/exclusion or exclusion criteria of the article. <code>Majority</code> means that the decision of the article is based on the decision of the majority of the reviewers.</dd>\r\n<dt><code>Criteria</code></dt>\r\n
                <dd>Enumerates the exclusion criteria to choose from when excluding an article during the screening phase. This is optional and can be modified later through the menu. In the example, <i>\"EC1\"</i> is one of the exclusion criteria. You can
                    write any text and enclose it between quotation marks.\r\n<dt><code>Sources</code></dt>\r\n
                    <dd>Enumerates the data sources of where the articles are coming from. This is set when <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/3\" style=\ "text-decoration: underline;\">importing articles</a>. For example, the name of
                        a digital library. This is optional and can be modified later through the menu. In the example, <i>\"Source 1\"</i> is one of the data source. You can write any data source name (e.g., <i>\"Web of Science\"</i>) and enclose it
                        between quotation marks.</dd>\r\n<dt><code>Strategies</code></dt>\r\n
                    <dd>Enumerates the search strategies of how the articles were collected. This is set when <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/3\" style=\ "text-decoration: underline;\">importing articles</a>. This is optional and can
                        be modified later through the menu. In the example, <i>\"Database search\"</i> is one of the search strategies. You can write any text and enclose it between quotation marks.</dd>\r\n<dt><code>Validation</code></dt>\r\n
                    <dd>The validation phase is optional. You can decide to have a sanity check phase where <code>x</code> percent of the excluded articles will be assigned to a <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/6\" style=\
                            "text-decoration: underline;\">validator</a>. The selection of the articles is decided at random following a uniform distribution among all excluded articles. Note that only articles resolved and marked as excluded are considered in the validation. Those still
                        marked in conflict are not considered.</dd>\r\n<dt><code>validation_type</code></dt>\r\n
                    <dd>In case the <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/6\" style=\ "text-decoration: underline;\">validator</a> decides to include an excluded article, we need to decide how this will be handled. <code>validation_type</code>                        can be one of the following values. <code>Info</code> means it will only be flagged in the statistics but does not change the status (inclusion/exclusion) of the article. <code>Normal</code> means his decision counts like any other
                        <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/6\" style=\ "text-decoration: underline;\">reviewers</a>. In this case, the <code>resolved_by</code> option in <code>Conflict</code> applies. For example, suppose <code>Unanimity</code>                        was selected and the two reviewers had decided to exclude the article. If the validator decides to include the article, then the article is marked in conflict. <code>Veto</code> means that the decision of the validator overrides
                        those of the reviewers.</dd>\r\n<dt><code>Phases</code></dt>\r\n
                    <dd>There can be as many screening phases as desired, but at least one is mandatory. Giving a title and short description is useful when there is more than one phase. Otherwise, you may want to simply call it <i>\"Screening\"</i>. To add
                        more phases, you can add another line starting with <code>Phases</code>. In case of multiple screening phases, note that only articles marked as included in a previous phase are available for screening in the following phase. The
                        <code>Field</code> lists the meta-information that will displayed when screening each article. The values can be any combination of:\r\n
                        <ul>\r\n
                            <li><code>Title</code> to show the title of the article</li>\r\n
                            <li><code>Abstract</code> to show the abstract of the article (if available)</li>\r\n
                            <li><code>Preview</code> to show all the meta-information of the article, including authors, year of publication, venue of publication, etc.</li>\r\n
                            <li><code>Bibtex</code> to show all the meta-information in BibTeX format</li>\r\n
                            <li><code>Link</code> to show the link to the actual article online and have access to the full text (if available)</li>\r\n </ul>\r\n </dd>\r\n</dl>\r\n
            <p>\r\nOnce the articles have been screened, they become available for <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/13\" style=\ "text-decoration: underline;\">classification and data extraction</a>.\r\n</p>\r\n
            <hr>\r\n
            <h3>
                <a name=\ "qa\"></a>Adding a quality assurance step</h3>\r\n
            <p>If desired, you may add a quality assurance (QA) step after all <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/14#screen\" style=\ "text-decoration: underline;\">the screening phases</a> and before the <a href=\
                    "http://relis.iro.umontreal.ca/auth/help_det/13\" style=\ "text-decoration: underline;\">the data extraction</a> step. Note that only articles marked as included in the final screening phase are considered for quality assurance. In this step, reviewers can fill in a checklist for each
                article. This section of the configuration file describes the questions and answers of the checklist. During the QA step, reviewers are expected to have access to the full text of the articles. The format of the QA configuration is:\r\n</p>\r\n<pre>\r\n<b>PROJECT</b>\r\n...\r\n<b>SCREENING</b>\r\n...\r\n<b>QA</b> <span style=\"color:cadetblue\">// optional section</span>\r\n<b>Questions = [</b>\"Quality question 1\", \"Quality question 2\"<b>]</b>\r\n<b>Response = [</b>\"Answer1\": w1, \"Answer2\": w2<b>]</b>\r\n<b>Min_score</b> threshold\r\n</pre>\r\n
            <dl>\r\n<dt><code>Questions</code></dt>\r\n
                <dd>Lists all the questions in the order as they appear</dd>\r\n<dt><code>Response</code></dt>\r\n
                <dd>The possible answers for all questions. They are the same for all questions. Typically, you would have <code>Yes, No, Partially</code>. Each answer is assigned a weight like: <code>[\"Yes\":3, \"Partially\":1, \"No\":0]</code>.</dd>\r\n<dt><code>threshold</code></dt>\r\n
                <dd>Once a checklist is filled in for an article, ReLiS calculates the score of this article by summing the weights of the answers selected. The <code>threshold</code> defines the minimum score that each article should have to be considered
                    for <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/13\" style=\ "text-decoration: underline;\">data extraction</a>. Otherwise, the article will be excluded.</dd>\r\n</dl>\r\n
            <p>\r\n<b>Note that you can change the configuration of the screening and QA at any point in time</b> through the menu. However, if you <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/19\" style=\ "text-decoration: underline;\">regenerate the configuration file</a>,
                those changes would be lost unless you have manually updated the configuration file.\r\n</p>','mL6z2pnQTww','Help',5,1),(15,'Export result','','','Help',7,0),(16,'Overview','
            <p>In the video above, we show you a brief overview of how to conduct a systematic review with ReLiS.</p>\r\n
            <p>To get started with ReLiS, first <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/5\" style=\ "text-decoration: underline;\">create an account</a> or use the Demo user from the menu to explore an example.</p>','96TdodQr5UY','Help',0,1),(17,'Import
            papers from digital libraries','\r\n\r\n
            <h3>Import BibTeX references</h3>\r\n\r\n \r\n\r\nMake sure the file is <b>encoded in ANSI or Western Windows</b>.\r\n\r\nAlso, make sure each reference is well-formatted using the format:\r\n\r\n<pre>\r\n\r\n@type{key, field={value} }\r\n\r\n</pre>\r\n\r\n
            <ul>\r\n\r\n
                <li><i>type</i> can be any valid BibTeX entry type, such as article, inproceedings, etc. </li>\r\n\r\n
                <li><i>key</i> uniquely identifies the reference. It is optional, but the comma after the curly bracket is mandatory. So @article{, field={value}} is also valid. </li>\r\n\r\n
                <li><i>field</i> can be a standard BibTeX field or not. Make sure you have the abstract field (to be displayed when screening). You may also wish to have the doi or paper fields to display the link to the online article. </li>\r\n\r\n
                <li><i>value</i> can be the enclosed in curly brackets { } or double quotes \" \".</li>\r\n\r\n
                <li>Note that any white space or new lines between these keywords will be ignored. </li>\r\n\r\n</ul>\r\n\r\n
            <p>\r\n\r\n<a href=\ "https://www.openoffice.org/bibliographic/bibtex-defs.html\">Here is the complete specification of the BibTeX format</a>.\r\n\r\n</p>\r\n\r\n \r\n\r\n
            <h3>Import EndNote references</h3>\r\n\r\n \r\n\r\n
            <p>\r\n\r\nTo import references from EndNote, you need to use a special export style in EndNote: <b>BiBler Export</b>.\r\n\r\nThis export style ships with the tool <a href=\ "https://github.com/geodes-sms/bibler\">BiBler</a>. You can either
                <a href=\ "https://github.com/geodes-sms/bibler/releases\">install it</a> (Windows only) or simply download the export style <a href=\ "https://github.com/geodes-sms/bibler/blob/master/src/bibler/external/BiBler%20Export.ens\">directly from here</a>.\r\n\r\n</p>\r\n\r\n
            <p>\r\n\r\nWith EndNote already installed on your computer, simply open <i>BiBler Export.ens</i> and save. It will then permanently be installed in your EndNote application.\r\n\r\n</p>\r\n\r\n
            <p>\r\n\r\nDepending on which digital library you downloaded the references from, you may need to ensure that the exported file is a well-formatted BibTeX file. See above for more information.\r\n\r\n</p>','','Help',2,0),(18,'Reporting','
            <p>ReLiS reports basic statistics for each step of the systematic review process.\r\nThe data can be exported for further analysis with dedicated statistical tools, such as Excel, SPSS, or R.\r\nAll reports are accessible at any point in time,
                even when the project is not yet complete.\r\n</p>\r\n
            <hr>\r\n
            <h3>Reporting the screening</h3>\r\n
            <p>\r\nIt is straightforward to track the progress of each step with progress bars and gauges showing the percentage of completion for each screening phase, validation, QA, and data extraction.\r\n</p>\r\n
            <p>\r\nFurthermore, you can consult various statistics of each screening phase reporting the articles included, excluded, and in conflict.\r\nReLiS also displays the <a href=\ "https://en.wikipedia.org/wiki/Cohen%27s_kappa\" style=\
                    "text-decoration: underline;\">inter-rater agreement</a> Kappa. Kappa is relevant only if there are more than one reviewer assigned per article. It is a value between 0 and 1, where:\r\n</p>\r\n
            <ul>\r\n
                <li>0 means there is no agreement</li>\r\n
                <li>0.1 - 0.2 means there is a slight agreement</li>\r\n
                <li>0.21 - 0.4 means there is a fair agreement</li>\r\n
                <li>0.43 - 0.6 means there is a moderate agreement</li>\r\n
                <li>0.61 - 0.8 means there is a substantial agreement</li>\r\n
                <li>0.8 - 0.99 means there is a near perfect agreement</li>\r\n
                <li>1 means there is perfect agreement</li>\r\n</ul>\r\n
            <p>\r\nKappa is update continuously as articles are screened during the phase. Therefore, after all conflicts are resolved Kappa will be 1. If your protocol requires to have intermediate checks on the degree of agreement, we recommend you take
                snapshots of the value at the appropriate times. For example, this can happen if you are starting with a sample of articles to make sure all reviewers are on the same page and then you continue with the remaining articles.\r\n</p>\r\n
            <hr>\r\n
            <h3>Reporting the QA</h3>\r\n
            <p>\r\nWhen articles are processed for quality assurance, the results display the score of each article. You can then decide to exclude the articles below the predefined threshold or exclude articles manually.\r\n</p>\r\n
            <h3>Reporting the classification</h3>\r\n
            <p>\r\nWhile the classification/data extraction is ongoing, you can visualize the results in the form of a table, charts or export to a file.\r\nYou can export all the classification data in a CSV file. You can export all articles, only those
                included, or those excluded at different steps to BibTeX or CSV.\r\n</p>\r\n
            <p>\r\nIn the configuration file, you can specify the charts to report in ReLiS. You can visualize data along one or two dimensions. Here is the sample code you can write at the end of the configuration file:\r\n</p>\r\n<pre>\r\n<b>REPORT</b>\r\n<b>Simple</b> chart_name <b>\"</b>Name of chart<b>\" on</b> category_name <b>charts(</b>chart_type1, chart_type2<b>)</b>\r\n<b>Compare</b> chart_name <b>\"</b>Name of chart<b>\" on</b> category1_name <b>with</b> category2_name <b>charts(</b>chart_type1, chart_type2<b>)</b>\r\n</pre>\r\n
            <h4><b>One-dimension chart</b></h4>\r\n
            <p>\r\n<code>Simple</code> indicates that we want to visualize along one category of the extraction form.\r\nThe <code>chart_name</code> is a unique variable to identify the chart.\r\nYou may optionally give it a more user-friendly name between
                quotation marks.\r\nThe <code>category_name</code> is the category along which we will report the frequencies.\r\nThe <code>chart_type</code> can be <code>line</code> (for scattered plots with a continuous line), <code>bar</code> (for
                a bar chart), or <code>pie</code> (for a pie chart). You can specify one or more chart type. They will all be displayed.\r\n</p>\r\n
            <p>\r\nFor example, lets assume we created the <i>Chocoholics</i> project (see the Data extraction help page). Now we add the following lines in the report section:\r\n<pre>\r\n<b>Simple</b> year <b>\"</b>Year published<b>\" on</b> year <b>charts(</b>line<b>)</b>\r\n<b>Simple</b> country <b>on</b> country <b>charts(</b>pie, bar<b>)</b>\r\n</pre>\r\nHere
                we will display three charts.\r\nThe first one is a graph of the number of articles per year.\r\nThe second one is a pie chart showing the percentage of articles per country.\r\nThe third one is showing the same information in the form
                of a bar chart.\r\n</p>\r\n
            <h4><b>Two-dimension chart</b></h4>\r\n
            <p>\r\nOne-dimension charts assume that the y-axis is the number of papers for each category value.\r\nTwo-dimension charts allow you to customize the x and y-axis to any dimension collected in a category.\r\n<code>Compare</code> indicates that
                we want to visualize along two categories of the extraction form.\r\nThe <code>category1_name</code> is the category that will be used for the y-axis.\r\nThe <code>category2_name</code> is the category that will be used for the x-axis.\r\n</p>\r\n
            <p>\r\nFor example, lets go back to the <i>Chocoholics</i> project. The following line will display a graph showing the number countries per year.\r\n<pre>\r\n<b>Compare</b> country_year <b>\"</b>Country per year<b>\" on</b> country <b>with</b> year <b>charts(</b>line<b>)</b>\r\n</pre>\r\n</p>\r\n
            <hr>\r\n
            <h3>Querying the database</h3>\r\n
            <p>The automated analytics in ReLiS are limited. Nevertheless, if you wish to perform more analytics on all aspects of your systematic review (process, data extracted), you can directly query the database of your project. Note that you need to
                have the appropriate <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/6\" style=\ "text-decoration: underline;\">user role</a> to have access to this option.\r\n</p>\r\n
            <p>To query the database, go to the home page of your project and click on the Query Database menu. You can write any arbitrary query using <a href=\ "https://www3.ntu.edu.sg/home/ehchua/programming/sql/MySQL_Beginner.html\">MySQL syntax</a>.
                Note that you should avoid modifying the data directly in the tables (<code>INSERT</code>, <code>UPDATE</code>, <code>DELETE</code>). You should certainly <b>not</b> modify the database or the tables (<code>DROP</code>, <code>ALTER</code>)
                as this may corrupt your project. Read-only queries using <code>SELECT</code> statements are what you should be considering.\r\n</p>\r\n
            <p>You can find the list of all the tables and views in the database of your project by executing the query:</p>\r\n<pre>\r\nSHOW TABLES\r\n</pre>\r\n
            <h4><b>Examples of queries</b></h4>\r\n
            <p>You can get the number of papers per year that have been included after screening as follows:</p>\r\n<pre>SELECT year, COUNT(*)\r\nFROM paper\r\nWHERE screening_status=\'Included\'\r\nGROUP BY year</pre>\r\n
            <p>You can get the list of papers that were excluded for a specific exclusion criteria (here EC1 is an example of the name of an exclusion criterion) as follows:</p>\r\n<pre>SELECT paper_id, bibtexKey\r\nFROM screening_paper\r\nINNER JOIN ref_exclusioncrieria ON ref_exclusioncrieria.ref_id = screening_paper.exclusion_criteria\r\nINNER JOIN paper ON paper.id = screening_paper.paper_id\r\nWHERE ref_value=\'EC1\' AND ref_active=1</pre>','','Help',7,1),(19,'Reconfiguration','
            <h3>Reconfigurations</h3>\r\n
            <p>\r\nYou can change the configuration of your project at any point in time. Some configurations can be changed directly in the settings. In the home page of your project, go to the Administration section and click under the Planning menu. You
                can then change the settings of the different steps (like screening, validation, QA). However, these <b>changes will not be reflected</b> in the configuration file. On the other hand, if you change the configuration file, then these settings
                will be updated accordingly.\r\n</p>\r\n
            <p>\r\nYou can only change the classification/data extraction form and the reporting steps from the configuration file. By default, if you change the configuration of the data extraction form, all classifications already performed will be lost.
                If you want to retain all the data of the steps of the screening, you must add the <code>keep_screening</code> keyword under <code>SCREENING</code>. If you want to retain all the data of the steps of the QA, you must add the <code>keep_qa</code>                keyword under <code>QA</code>. If you want to also reset these data (e.g., restart the screening), then you must add the <code>override</code> keyword under <code>CLASSIFICATION</code>. Here is an example where the screening data will
                be retained but not the classification data.\r\n</p>\r\n<pre>\r\n<b>PROJECT</b>\r\n...\r\n<b>SCREENING</b>\r\n<b style=\"color: red;\">keep_screening</b>\r\n...\r\n<b>QA</b>\r\n<b style=\"color: red;\">keep_qa</b>\r\n...\r\n<b>CLASSIFICATION</b>\r\n<b style=\"color: red;\">override</b>\r\n...\r\n<b>REPORTING</b>\r\n...\r\n</pre>\r\n
            <p><code>keep_screening</code> allows you to regenerate the classification and reporting steps, without impacting the screening process or the references that were already screened.</p>\r\n
            <p>Similarly, <code>keep_qa</code> allows you to regenerate the classification and reporting steps, without impacting the QA process.</p>\r\n
            <p><code>override</code> allows you to regenerate all the classification and reporting steps by resetting all the data in these steps only. Ommitting the keyword will only modify the data extraction form if it does not create a conflict with
                the existing data. If the modifications produce a conflict, it will also erase the data.</p>\r\n\r\n\r\n
            <h3>Removing references</h3>\r\n
            <p>ReLiS allows you to perform many iterations over your systematic review. Therefore, there are cases where you may need to remove the references that are loaded in your project. This is usually the case when you are starting the review and
                would like to test out the different steps (<a href=\ "http://relis.iro.umontreal.ca/auth/help_det/14\" style=\ "text-decoration: underline;\">the screening</a>, <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/14#qa\" style=\
                    "text-decoration: underline;\">the quality assurance</a>, <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/13\" style=\ "text-decoration: underline;\">the data extraction</a>, or <a href=\ "http://relis.iro.umontreal.ca/auth/help_det/18\" style=\
                    "text-decoration: underline;\">the reporting</a>). It may also be needed if you want to start a calibration phase to make sure that reviewers are in agreement with the inclusion/exclusion decisions of a small sample of your corpus of references.</p>\r\n
            <p>To remove all the references from your project, go to the home page of your project and click in the menu Papers > All. Then press the Delete all button and confirm.</p>\r\n
            <p style=\ "font-weight: bold;\">WARNING: this will erase all references in your project and it cannot be undone. All associated data, like the screening results and data extraction, will be cleared as well.</p>','','Help',8,1),(20,'Exporting','<p>
ReLiS allows you to export different information from the project.
To export the information, open the Classification phase of your project. Under the Result menu, select Export.
Click on corresponding <code>Update file</code> to generate the latest version of the file you wish to export.
</p>
<a name="classification"><h4><b>Classification</b></h4></a>
<p>This is a CSV file of the classification table with all the data extracted. Each row correponds to a reference. The columns correspond to the categories listed in the <a href="/user/help_det/13" style="text-decoration: underline;">classification form</a>. Categories, for which multiple values are specified, are separated with a <code>|</code>.</p>

<h4><b>Papers</b></h4>
<p>This is a CSV file of all the references imported in the project. The columns are: the reference ID, the BibTeX key, the title, the URL of the reference, the preview of the reference in HTML format, the abstract, the year published, and the search strategy.</p>

<h4><b>Papers (BibTeX)</b></h4>
<p>This is a BibTeX file of all the references imported in the project.</p>

<h4><b>Papers included (BibTeX)</b></h4>
<p>This is a BibTeX file of all the references included after the last screening phase.</p>

<h4><b>Papers excluded (BibTeX)</b></h4>
<p>This is a BibTeX file of all the references excluded after the last screening phase.</p>

<h4><b>Papers Excluded Screening(CSV)</b></h4>
<p>This is a CSV file of all the references excluded after the last screening phase. The columns are: the reference ID, the BibTeX key, the title, the preview of the reference in HTML format, the search strategy, the user who excluded the reference, the exclusion criteria selected, and the note left by the user as a comment.</p>

<h4><b>Papers Excluded Classification (CSV)</b></h4>
<p>This is a CSV file of all the references excluded only during the classification phase. The columns are: the reference ID, the BibTeX key, the title, the preview of the reference in HTML format, the user who excluded the reference, the exclusion criteria selected, the note left by the user as a comment, and the search strategy.</p>

<a name="r_config"><h4><b>R configuration script</b></h4></a>
<p>This redirects you to the R export configuration page. See the <a href="#export_r" style="text-decoration: underline;">Export to R</a> section below for more information on that page. Clicking on Generate R redirects you to the Exports page. From there, you can download the R scripts which you can run using R to compute different statistics on the results of the classification.</p>
<p>This page allows you to specify the type of variable scale for each category. We support the following scales:</p>
<ul>
  <li><code>Continuous</code>: For variables that are numeric and continuous in nature. This is typically for <code>int</code> and <code>real</code> simple categories.</li>
  <li><code>Nominal</code>: For categorical variables. This is typically for <code>lists</code> and <code>dynamic lists</code>, including if they have multiple values. But it can also be used for other categories, such as <code>simple bool</code>.</li>
  <li><code>Text</code>: For textual fields. If this scale type is assigned to a category, it will be excluded from all of the statistical tests and plots.</li>
</ul>

<a name="r_lib"><h4><b>R Library</b></h4></a>
<p>This is an R script file that is required for the R configuration script.</p>

<hr>
<a name="export_r"><h3>Export to R</h3></a>

<p>You can export the results of the classification to R and run statistical analysis on them. To this end, you must download the following files exported from your project: <a href="#classification" style="text-decoration: underline;">Classification</a>, <a href="#r_config" style="text-decoration: underline;">R configuration script</a>, and <a href="#r_lib" style="text-decoration: underline;">R Library</a>.</p>

<p>The file to execute in R is the R configuration script. It contains a list of commented lines which consist of all possible statistical tests and plots available in the R library. Uncomment the lines plots/tests you wish to consider then run the script.</p>
<p><strong>Note:</strong> You may need to change the path to the R library file based on your download locations. On line 1 of the R configuration script, make sure it states the proper name name and path to the R Library file you downloaded:
<pre>source(\"relis_r_lib_demo1_relis.R\") <span style=\"color: green;\"># Replace this with the name of your imported library file</span></pre>
On line 7 of the R Library file, make sure it states the proper name and path to the classification file you downloaded:
<pre>relis_data <- read.csv(\"relis_classification_demo1_relis.csv\", header = TRUE) <span style=\"color: green;\"># Replace this with the name of your imported data file</span></pre></p>

<p>The R library supports the following plots and statistical tests. They are accessible by using the correpsonding variable. For example, <code>statistics_vector[[''Temperature'']]</code> outputs the descriptive statistics of the <code>Temperature</code> variable (this variable corresponds to a numerical category in the data extraction form.</p>
<h4><b>Descriptive statistics</b></h4>
<ul>
    <li>Descriptive frequency tables (for all nominal variables): use the <code>desc_distr_vector</code> variable</li>
    <li>Bar plots (for all nominal variables): use the <code>bar_plot_vector</code> variable</li>
    <li>Statistics (for all continuous variables): use the <code>statistics_vector</code> variable</li>
    <li>Box plots (for all continuous variables): use the <code>box_plot_vector</code> variable</li>
    <li>Violin plots (for all continuous variables): use the <code>violin_plot_vector</code> variable</li>
</ul>
<h4><b>Evolution statistics</b></h4>
<ul>
    <li>Evolution frequency tables (for each nominal variables, with respect to the publication years of the references): use the <code>evo_distr_vector</code> variable</li>
    <li>Evolution plots (for all nominal variables, with respect to the publication years of the references): use the <code>evolution_plot_vector</code> variable</li>
</ul>
<h4><b>Comparative statistics</b></h4>
<ul>
    <li>Comparative frequency tables (for all nominal variables): use the <code>comp_distr_vector</code> variable</li>
    <li>Stacked bar plots (for all nominal variables): use the <code>stacked_bar_plot_vector</code> variable</li>
    <li>Grouped bar plots (for all nominal variables): use the <code>grouped_bar_plot_vector</code> variable</li>
    <li>Bubble charts (for all nominal variables): use the <code>bubble_chart_vector</code> variable</li>
    <li>Fisher''s exact test (for all nominal variables): use the <code>fisher_exact_test_vector</code> variable</li>
    <li>Shapiro Wilk''s test (for all continuous variables): use the <code>shapiro_wilk_test_vector</code> variable</li>
    <li>Pearson''s correlation test (for all continuous variables): use the <code>pearson_cor_test_vector</code> variable</li>
    <li>Spearman''s correlation test (for all continuous variables): use the <code>spearman_cor_test_vector</code> variable</li>
</ul>
<hr>
<a name="export_python"><h3>Export to Python</h3></a>
 
<p>You can export the results of the classification to Python and run statistical analysis on them. When you generate (button "update file") the environment, you will then be able to download the files required to run the statistical analysis on your local machine.</p>
 
<p>The file to execute in R is the R configuration script. It contains a list of commented lines which consist of all possible statistical tests and plots available in the R library. Uncomment the lines plots/tests you wish to consider then run the script.</p>
<p><strong>Note:</strong> Every file you need will be downloaded inside the ZIP file. You should have a .csv, containing all your data, and 2 python scripts, the kernel and the playground, that are needed to generate a proper statistical analysis environment. Make sure every file are inside the same folder when the scripts are executed.</p>
 
<p>The Python statistical environment supports the following plots and statistical tests. They are accessible by using the correpsonding variable. For example, <code>evo_plots([Variables.cocoa_origin], True)</code> outputs the evolutive statistics of the <code>cocoa_origin</code> variable (this variable corresponds to a nominal category in the data extraction form).</p>
<h4><b>Descriptive statistics</b></h4>
<ul>
    <li>Descriptive frequency tables (for all nominal variables): use the <code>desc_frequency_table</code> variable</li>
    <li>Bar plots (for all nominal variables): use the <code>desc_bar_plot</code> variable</li>
    <li>Statistics (for all continuous variables): use the <code>desc_statistics</code> variable</li>
    <li>Box plots (for all continuous variables): use the <code>desc_box_plot</code> variable</li>
    <li>Violin plots (for all continuous variables): use the <code>desc_violin_plot</code> variable</li>
</ul>
<h4><b>Evolution statistics</b></h4>
<ul>
    <li>Evolution frequency tables (for each nominal variables, with respect to the publication years of the references): use the <code>evo_frequency_table</code> variable</li>
    <li>Evolution plots (for all nominal variables, with respect to the publication years of the references): use the <code>evo_plot</code> variable</li>
</ul>
<h4><b>Comparative statistics</b></h4>
<ul>
    <li>Comparative frequency tables (for all nominal variables): use the <code>comp_frequency_table</code> variable</li>
    <li>Stacked bar plots (for all nominal variables): use the <code>comp_stacked_bar_plot</code> variable</li>
    <li>Grouped bar plots (for all nominal variables): use the <code>comp_grouped_bar_plot</code> variable</li>
    <li>Bubble charts (for all nominal variables): use the <code>comp_bubble_chart</code> variable</li>
    <li>Chi Squared test (for all nominal variables): use the <code>comp_chi_squared_test</code> variable</li>
<p><strong>Note:</strong> Originally this was "Fisher\'s exact test", but this test had some problems, so we replaced it with chi_squared for an alternative solution.</p>
    <li>Shapiro Wilk\'s test (for all continuous variables): use the <code>comp_shapiro_wilk_test</code> variable</li>
    <li>Pearson\'s correlation test (for all continuous variables): use the <code>comp_pearson_cor_test</code> variable</li>
    <li>Spearman\'s correlation test (for all continuous variables): use the <code>comp_spearman_cor_test</code> variable</li>
</ul>','','Help',9,1);
/*!40000 ALTER TABLE `info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1, 'demo_relis', 'Demo ReLiS', 'Demo ReLiS', 1, NULL, current_timestamp(), 0, 1);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `str_management`
--

DROP TABLE IF EXISTS `str_management`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `str_management` (
  `str_id` int(11) NOT NULL AUTO_INCREMENT,
  `str_label` varchar(500) NOT NULL,
  `str_text` varchar(1000) NOT NULL,
  `str_category` varchar(20) NOT NULL DEFAULT 'default',
  `str_lang` varchar(3) NOT NULL DEFAULT 'en',
  `str_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`str_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `str_management`
--

LOCK TABLES `str_management` WRITE;
/*!40000 ALTER TABLE `str_management` DISABLE KEYS */;
INSERT INTO `str_management` VALUES (1,'Add new project','Add new project','default','en',1),(2,'Installed projects','Installed projects','default','en',1),(3,'General','General','default','en',1),(4,'Projects','Projects','default','en',1),(5,'Users','Users','default','en',1),(6,'Query Database','Query Database','default','en',1),(7,'Administration','Administration','default','en',1),(8,'Logs','Logs','default','en',1),(9,'Label Mangement','Label Mangement','default','en',1),(10,'Settings','Settings','default','en',1),(11,'Home page settings','Home page settings','default','en',1),(12,'Configuration_managment','Configuration_managment','default','en',1),(13,'Main','Main','default','en',1),(14,'UP','UP','default','en',1),(15,'Dashboard','Dashboard','default','en',1),(16,'Log Out','Log Out','default','en',1),(17,'Profile','Profile','default','en',1),(18,'No project available!','No project available!','default','en',1),(19,'Select','Select','default','en',1),(20,'Select multi','Select multi','default','en',1),(21,'Delete','Delete','default','en',1),(22,'Action','Action','default','en',1),(23,'Add a new user','Add a new user','default','en',1),(24,'Close','Close','default','en',1);
/*!40000 ALTER TABLE `str_management` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_creation`
--

DROP TABLE IF EXISTS `user_creation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_creation` (
  `user_creation_id` int(11) NOT NULL AUTO_INCREMENT,
  `creation_user_id` int(11) NOT NULL,
  `confirmation_code` varchar(50) NOT NULL,
  `confirmation_expiration` int(10) NOT NULL,
  `confirmation_try` int(10) NOT NULL,
  `user_creation_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_creation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_creation`
--

LOCK TABLES `user_creation` WRITE;
/*!40000 ALTER TABLE `user_creation` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_creation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usergroup`
--

DROP TABLE IF EXISTS `usergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usergroup` (
  `usergroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup_name` varchar(100) NOT NULL,
  `usergroup_description` varchar(100) DEFAULT NULL,
  `usergroup_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`usergroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usergroup`
--

LOCK TABLES `usergroup` WRITE;
/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
INSERT INTO `usergroup` VALUES (1,'Administrator','Administrator',1),(2,'Project Manager','Project Manager',1),(3,'Standard','Standard',1);
/*!40000 ALTER TABLE `usergroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userproject`
--

DROP TABLE IF EXISTS `userproject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userproject` (
  `userproject_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_role` enum('Reviewer','Validator','Project admin','Guest') NOT NULL DEFAULT 'Reviewer',
  `added_by` int(11) NOT NULL DEFAULT '1',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userproject_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userproject`
--

LOCK TABLES `userproject` WRITE;
/*!40000 ALTER TABLE `userproject` DISABLE KEYS */;
INSERT INTO `userproject` VALUES (1, 2, 1,'Reviewer', 2, current_timestamp(), 1);
/*!40000 ALTER TABLE `userproject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin','info@relis.ca',1,'202cb962ac59075b964b07152d234b70',NULL,1,'2020-06-22 19:49:48',1,1),('2', 'Demo', 'demo', 'demo@relis.iro.umontreal.ca', '3', NULL, NULL, '1', current_timestamp(), '1', '1');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'relis_db'
--
/*!50003 DROP PROCEDURE IF EXISTS `add_config` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_config`(_config_id INT , _project_title  VARCHAR(405) , _project_description  VARCHAR(1005) , _default_lang  VARCHAR(16) , _creator INT , _run_setup  VARCHAR(6))
BEGIN
START TRANSACTION;
INSERT INTO config (project_title , project_description , default_lang , creator , run_setup) VALUES (_project_title , _project_description , _default_lang , _creator , _run_setup);
SELECT config_id AS id_value FROM config WHERE config_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_debug` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_debug`(_debug_id INT , _status  VARCHAR(20) , _created_by INT , _page_code  VARCHAR(205) , _page_url  VARCHAR(205) , _title  VARCHAR(1005) , _comment  VARCHAR(1005) , _debug_picture  LONGBLOB )
BEGIN
START TRANSACTION;
INSERT INTO debug (status , created_by , page_code , page_url , title , comment , debug_picture) VALUES (_status , _created_by , _page_code , _page_url , _title , _comment , _debug_picture);
SELECT debug_id AS id_value FROM debug WHERE debug_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_info` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_info`(_info_id INT , _info_title  VARCHAR(505) , _info_desc  VARCHAR(2005) , _info_link  VARCHAR(505) , _info_type  VARCHAR(25) , _info_order INT)
BEGIN
START TRANSACTION;
INSERT INTO info (info_title , info_desc , info_link , info_type , info_order) VALUES (_info_title , _info_desc , _info_link , _info_type , _info_order);
SELECT info_id AS id_value FROM info WHERE info_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_logs` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_logs`(_log_id INT , _log_type  VARCHAR(55) , _log_user_id INT , _log_event  VARCHAR(205) , _log_ip_address  VARCHAR(205) , _log_publish INT)
BEGIN
START TRANSACTION;
INSERT INTO log (log_type , log_user_id , log_event , log_ip_address , log_publish) VALUES (_log_type , _log_user_id , _log_event , _log_ip_address , _log_publish);
SELECT log_id AS id_value FROM log WHERE log_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_project`(_project_id INT , _project_creator INT , _project_label  VARCHAR(105) , _project_title  VARCHAR(255) , _project_description  VARCHAR(1005) , _project_icon  LONGBLOB )
BEGIN
START TRANSACTION;
INSERT INTO projects (project_creator , project_label , project_title , project_description , project_icon) VALUES (_project_creator , _project_label , _project_title , _project_description , _project_icon);
SELECT project_id AS id_value FROM projects WHERE project_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_string` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_string`(_str_id INT , _str_label  VARCHAR(405) , _str_text  VARCHAR(805) , _str_lang  VARCHAR(8) , _str_category  VARCHAR(23))
BEGIN
START TRANSACTION;
INSERT INTO str_management (str_label , str_text , str_lang , str_category) VALUES (_str_label , _str_text , _str_lang , _str_category);
SELECT str_id AS id_value FROM str_management WHERE str_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_str_mng` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_str_mng`(_str_id INT , _str_label  VARCHAR(405) , _str_text  VARCHAR(805) , _str_lang  VARCHAR(8) , _str_category  VARCHAR(23))
BEGIN
START TRANSACTION;
INSERT INTO str_management (str_label , str_text , str_lang , str_category) VALUES (_str_label , _str_text , _str_lang , _str_category);
SELECT str_id AS id_value FROM str_management WHERE str_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_users` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_users`(_user_id INT , _user_state INT , _user_name  VARCHAR(55) , _user_username  VARCHAR(25) , _user_mail  VARCHAR(105) , _user_usergroup INT , _user_password  VARCHAR(40) , _user_picture  LONGBLOB  , _created_by INT)
BEGIN
START TRANSACTION;
INSERT INTO users (user_state , user_name , user_username , user_mail , user_usergroup , user_password , user_picture , created_by) VALUES (_user_state , _user_name , _user_username , _user_mail , _user_usergroup , _user_password , _user_picture , _created_by);
SELECT user_id AS id_value FROM users WHERE user_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_users_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_users_project`(_userproject_id INT , _user_id INT , _project_id INT , _user_role  VARCHAR(25) , _added_by INT)
BEGIN
START TRANSACTION;
INSERT INTO userproject (user_id , project_id , user_role , added_by) VALUES (_user_id , _project_id , _user_role , _added_by);
SELECT userproject_id AS id_value FROM userproject WHERE userproject_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `add_user_creation` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_user_creation`(_user_creation_id INT , _creation_user_id INT , _confirmation_code  VARCHAR(55) , _confirmation_try INT , _confirmation_expiration INT)
BEGIN
START TRANSACTION;
INSERT INTO user_creation (creation_user_id , confirmation_code , confirmation_try , confirmation_expiration) VALUES (_creation_user_id , _confirmation_code , _confirmation_try , _confirmation_expiration);
SELECT user_creation_id AS id_value FROM user_creation WHERE user_creation_id = LAST_INSERT_ID();
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `check_login` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_login`(IN _login VARCHAR(20))
BEGIN
START TRANSACTION;
SELECT COUNT(user_id)  AS number
FROM users
WHERE user_active=1 AND user_username = _login  ;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `check_user_credentials` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_user_credentials`(_username VARCHAR(100), _password VARCHAR(100))
BEGIN
START TRANSACTION;
	SELECT * FROM users
	WHERE (users.user_username = _username)
    AND (users.user_password = _password);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `count_list` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `count_list`(IN source varchar(200),IN condition_stat VARCHAR(1000))
BEGIN
SET @query = CONCAT('Select count(*) as nombre from  ',source ,'   WHERE 1=1  ', condition_stat );
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_debug_detail` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_debug_detail`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM debug
WHERE debug_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_config` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_config`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM config
WHERE config_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_config_admin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_config_admin`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM config_admin
WHERE config_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_info` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_info`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM info
WHERE info_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_logs` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_logs`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM log
WHERE log_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_project`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM projects
WHERE project_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_str_mng` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_str_mng`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM str_management
WHERE str_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_usergroup` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_usergroup`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM usergroup
WHERE usergroup_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_users` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_users`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM users
WHERE user_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_user_creation` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_user_creation`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM user_creation
WHERE user_creation_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_detail_user_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_detail_user_project`(IN _row_id INT)
BEGIN
START TRANSACTION;
SELECT * FROM userproject
WHERE userproject_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list`(IN _source varchar(100),IN _fields varchar(1000),IN _condition_stat VARCHAR(1000))
BEGIN
SET @query = CONCAT('Select ',_fields,' from  ',_source ,'   WHERE 1=1   ', _condition_stat );
 PREPARE stmt FROM @query;
 EXECUTE stmt;
 DEALLOCATE PREPARE stmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_config` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_config`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_debug` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_debug`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;
					 SET @search_title := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM debug
WHERE debug_active=1   AND (  (title LIKE  @search_title)  )    ORDER BY debug_id DESC ;
ELSE
SELECT * FROM debug
WHERE debug_active=1   AND (  (title LIKE  @search_title)  )    ORDER BY debug_id DESC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_info` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_info`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;
					 SET @search_info_title := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM info
WHERE info_active=1   AND (  (info_title LIKE  @search_info_title)  )    ORDER BY info_type ASC ;
ELSE
SELECT * FROM info
WHERE info_active=1   AND (  (info_title LIKE  @search_info_title)  )    ORDER BY info_type ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_logs` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_logs`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;
					 SET @search_log_type := CONCAT('%',TRIM(_search),'%') ;  SET @search_log_event := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM log
WHERE log_active=1  AND log_publish =  '1'    AND (  (log_type LIKE  @search_log_type)  OR (log_event LIKE  @search_log_event)  )    ORDER BY log_id DESC ;
ELSE
SELECT * FROM log
WHERE log_active=1  AND log_publish =  '1'    AND (  (log_type LIKE  @search_log_type)  OR (log_event LIKE  @search_log_event)  )    ORDER BY log_id DESC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_project`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;
					 SET @search_project_title := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM projects
WHERE project_active=1   AND (  (project_title LIKE  @search_project_title)  )    ORDER BY project_title ASC ;
ELSE
SELECT * FROM projects
WHERE project_active=1   AND (  (project_title LIKE  @search_project_title)  )    ORDER BY project_title ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_str_mng` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_str_mng`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500) ,IN  _str_lang   VARCHAR(3))
BEGIN
					START TRANSACTION;
					 SET @search_str_label := CONCAT('%',TRIM(_search),'%') ;  SET @search_str_text := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM str_management
WHERE str_active=1  AND str_lang = _str_lang   AND (  (str_label LIKE  @search_str_label)  OR (str_text LIKE  @search_str_text)  )    ORDER BY str_label DESC ;
ELSE
SELECT * FROM str_management
WHERE str_active=1  AND str_lang = _str_lang   AND (  (str_label LIKE  @search_str_label)  OR (str_text LIKE  @search_str_text)  )    ORDER BY str_label DESC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_usergroup` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_usergroup`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;
					 SET @search_usergroup_name := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM usergroup
WHERE usergroup_active=1   AND (  (usergroup_name LIKE  @search_usergroup_name)  )    ORDER BY usergroup_name ASC ;
ELSE
SELECT * FROM usergroup
WHERE usergroup_active=1   AND (  (usergroup_name LIKE  @search_usergroup_name)  )    ORDER BY usergroup_name ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_users` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_users`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;
					 SET @search_user_name := CONCAT('%',TRIM(_search),'%') ;
					IF _range < 1 THEN
					SELECT * FROM users
WHERE user_active=1   AND (  (user_name LIKE  @search_user_name)  )    ORDER BY user_name ASC ;
ELSE
SELECT * FROM users
WHERE user_active=1   AND (  (user_name LIKE  @search_user_name)  )    ORDER BY user_name ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_users_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_users_all`()
BEGIN
START TRANSACTION;
SELECT  U.*,G.usergroup_name FROM users U
INNER JOIN usergroup G ON (U.user_usergroup  = G.usergroup_id)
WHERE U.user_active = 1 AND G.usergroup_active;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_user_current_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_user_current_project`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500) ,IN  _project_id   VARCHAR(4))
BEGIN
					START TRANSACTION;

					IF _range < 1 THEN
					SELECT * FROM userproject
WHERE userproject_active=1  AND project_id = _project_id     ORDER BY user_id ASC ;
ELSE
SELECT * FROM userproject
WHERE userproject_active=1  AND project_id = _project_id     ORDER BY user_id ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_list_user_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_list_user_project`(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
					START TRANSACTION;

					IF _range < 1 THEN
					SELECT * FROM userproject
WHERE userproject_active=1     ORDER BY user_id ASC ;
ELSE
SELECT * FROM userproject
WHERE userproject_active=1     ORDER BY user_id ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_reference_value` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_reference_value`(IN  _table  VARCHAR(100), IN  _id  VARCHAR(100), IN  _field  VARCHAR(100), IN  _table_id  VARCHAR(100))
BEGIN
SET @query = CONCAT('Select ',_field,' from  ',_table ,'   WHERE ', _table_id, ' = ', _id );
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_row` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_row`(IN source varchar(100),IN source_id VARCHAR(100),IN id_value VARCHAR(100))
BEGIN
SET @query = CONCAT("Select * from  ",source ,"  WHERE ", source_id ," = '",id_value,"'");
 PREPARE stmt FROM @query;
 EXECUTE stmt;
 DEALLOCATE PREPARE stmt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_string` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_string`(IN _text VARCHAR(500),IN _category VARCHAR(30),IN _lang VARCHAR(3))
BEGIN
START TRANSACTION;
SELECT str_id, str_text
FROM str_management
WHERE str_active=1 AND str_label = _text AND str_category = _category AND str_lang = _lang ;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_userproject_detail` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_userproject_detail`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM userproject
WHERE userproject_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_user_detail` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_detail`(IN _row_id INT)
BEGIN
					START TRANSACTION;
					SELECT * FROM users
WHERE user_id= _row_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_config` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_config`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE config SET config_active=0
WHERE config_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_debug` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_debug`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE debug SET debug_active=0
WHERE debug_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_info` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_info`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE info SET info_active=0
WHERE info_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_logs` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_logs`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE log SET log_active=0
WHERE log_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_project`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE projects SET project_active=0
WHERE project_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_str_mng` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_str_mng`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE str_management SET str_active=0
WHERE str_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_users` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_users`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE users SET user_active=0
WHERE user_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `remove_user_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_user_project`(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE userproject SET userproject_active=0
WHERE userproject_id= _element_id;
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_config` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_config`(_element_id INT , _config_id INT , _project_description  VARCHAR(1005) , _default_lang  VARCHAR(16) , _creator INT , _run_setup  VARCHAR(6))
BEGIN
START TRANSACTION;
UPDATE  config SET config_id = _config_id , project_description = _project_description , default_lang = _default_lang , creator = _creator , run_setup = _run_setup
WHERE (config_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_config_admin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_config_admin`(_element_id INT , _config_id INT , _editor_url  VARCHAR(105) , _editor_generated_path  VARCHAR(105) , _track_comment_on INT)
BEGIN
START TRANSACTION;
UPDATE  config_admin SET config_id = _config_id , editor_url = _editor_url , editor_generated_path = _editor_generated_path , track_comment_on = _track_comment_on
WHERE (config_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_debug` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_debug`(_element_id INT , _debug_id INT , _title  VARCHAR(1005) , _comment  VARCHAR(1005) , _debug_picture  LONGBLOB  , _status  VARCHAR(20))
BEGIN
START TRANSACTION;
UPDATE  debug SET debug_id = _debug_id , title = _title , comment = _comment , debug_picture = _debug_picture , status = _status
WHERE (debug_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_info` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_info`(_element_id INT , _info_id INT , _info_title  VARCHAR(505) , _info_desc  VARCHAR(2005) , _info_link  VARCHAR(505) , _info_type  VARCHAR(25) , _info_order INT)
BEGIN
START TRANSACTION;
UPDATE  info SET info_id = _info_id , info_title = _info_title , info_desc = _info_desc , info_link = _info_link , info_type = _info_type , info_order = _info_order
WHERE (info_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_logs` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_logs`(_element_id INT , _log_id INT , _log_user_id INT , _log_time  VARCHAR(205))
BEGIN
START TRANSACTION;
UPDATE  log SET log_id = _log_id , log_user_id = _log_user_id , log_time = _log_time
WHERE (log_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_project`(_element_id INT , _project_id INT , _project_title  VARCHAR(255) , _project_description  VARCHAR(1005) , _project_icon  LONGBLOB )
BEGIN
START TRANSACTION;
UPDATE  projects SET project_id = _project_id , project_title = _project_title , project_description = _project_description , project_icon = _project_icon
WHERE (project_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_str_mng` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_str_mng`(_element_id INT , _str_id INT , _str_label  VARCHAR(405) , _str_text  VARCHAR(805) , _str_lang  VARCHAR(8))
BEGIN
START TRANSACTION;
UPDATE  str_management SET str_id = _str_id , str_label = _str_label , str_text = _str_text , str_lang = _str_lang
WHERE (str_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_users` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_users`(_element_id INT , _user_id INT , _user_name  VARCHAR(55) , _user_mail  VARCHAR(105) , _user_usergroup INT , _user_password  VARCHAR(40) , _user_picture  LONGBLOB )
BEGIN
START TRANSACTION;
UPDATE  users SET user_id = _user_id , user_name = _user_name , user_mail = _user_mail , user_usergroup = _user_usergroup , user_password = _user_password , user_picture = _user_picture
WHERE (user_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_users_mine` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_users_mine`(_element_id INT , _user_id INT , _user_mail  VARCHAR(105) , _user_password  VARCHAR(40) , _user_picture  LONGBLOB )
BEGIN
START TRANSACTION;
UPDATE  users SET user_id = _user_id , user_mail = _user_mail , user_password = _user_password , user_picture = _user_picture
WHERE (user_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_user_project` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_user_project`(_element_id INT , _userproject_id INT , _project_id INT , _user_role  VARCHAR(25))
BEGIN
START TRANSACTION;
UPDATE  userproject SET userproject_id = _userproject_id , project_id = _project_id , user_role = _user_role
WHERE (userproject_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_user_project_2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_user_project_2`(_element_id INT , _userproject_id INT , _user_role  VARCHAR(25))
BEGIN
START TRANSACTION;
UPDATE  userproject SET userproject_id = _userproject_id , user_role = _user_role
WHERE (userproject_id = _element_id);
COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-06-22 21:10:50
