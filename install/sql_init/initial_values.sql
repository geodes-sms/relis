INSERT INTO `usergroup` (`usergroup_id`, `usergroup_name`, `usergroup_description`, `usergroup_active`) VALUES
(1, 'Administrator', 'Administrator', 1),
(2, 'Project Manager', 'Project Manager', 1),
(3, 'Standard', 'Standard', 1)$$

INSERT INTO `config` (`config_id`, `config_type`, `project_title`, `project_description`, `default_lang`, `creator`, `run_setup`, `rec_per_page`, `config_active`) VALUES
(1, 'default', 'Admin', 'Admin project', 'en', 1, 0, 30, 1)$$

INSERT INTO `config_admin` (`config_id`, `config_type`, `editor_url`, `editor_generated_path`, `track_comment_on`, `config_active`) VALUES
(1, '', 'http://127.0.0.1:8080/relis/texteditor', 'C:\\dslforge_workspace', 0, 1)$$


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