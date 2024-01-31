<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------------------------
 *
 *  :Author: Brice Michel Bigendako
 */
class Manage_stored_procedure_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();



	}

	/*
	 * Creation of the stored procedure to display a list of elements
	 * $config: Represents the configuration for creating the stored procedure.
	 * $run_query: A boolean parameter indicating whether to execute the created query or not. It is set to TRUE by default.
	 * $target_db: Represents the target database where the stored procedure will be created. It is set to 'current' by default.
	 */
	public function create_stored_procedure_get($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{



		$target_db = ($target_db == 'current') ? project_db() : $target_db;

		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);

		//calls the get_table_config function to retrieve the configuration of the specified table ($config) in the target database
		$table_config = get_table_config($config, $target_db);


		$sql_append = "";
		$sql_vars = "";
		if (!empty($table_config['search_by'])) {

			$fields_search = explode(",", $table_config['search_by']);

			$i = 0;
			$sql_append .= " AND ( ";
			foreach ($fields_search as $field_name) {
				$sql_vars .= " SET @search_" . $field_name . " := CONCAT('%',TRIM(_search),'%') ; ";
				if ($i == 0) {

					$sql_append .= " ($field_name LIKE  @search_" . $field_name . ") ";
				} else {
					$sql_append .= " OR ($field_name LIKE  @search_" . $field_name . ") ";
				}
				$i = 1;
			}

			$sql_append .= " ) ";


		}

		$procedure = "
				DROP PROCEDURE IF EXISTS get_list_" . $config . ";
				";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose) {
			echo "<p>$procedure</p>";
			print_test($res);
		}

		$extra_parametters = "";
		$extra_condition = "";

		if ($config == "str_mng") {
			$extra_parametters = " ,IN _lang VARCHAR(3)";
			$extra_condition = " AND str_lang = _lang ";
		}


		if ($config == "papers") {
			$procedure = "CREATE PROCEDURE get_list_" . $config . "(IN _start_by INT,IN _range INT, IN _search VARCHAR(500),IN _excluded VARCHAR(2))
BEGIN
START TRANSACTION;
$sql_vars
IF _range < 1 THEN
SELECT * FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_active_field'] . "=1  AND (paper_excluded = _excluded OR _excluded ='_' )   $sql_append ORDER BY " . $table_config['order_by'] . ";
ELSE
SELECT * FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_active_field'] . "=1  AND (paper_excluded = _excluded OR _excluded ='_' )   $sql_append ORDER BY " . $table_config['order_by'] . " LIMIT _start_by , _range;
END IF;
COMMIT;
END";

			//papers_for_classification

			$procedure_c = "
				DROP PROCEDURE IF EXISTS get_list_papers_class
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_c);

			if ($verbose) {
				echo "<p>$procedure_c</p>";
				print_test($res);
			}

			$procedure_c = "CREATE PROCEDURE get_list_papers_class(IN _start_by INT,IN _range INT, IN _search VARCHAR(500),IN _excluded VARCHAR(2))
	BEGIN
	START TRANSACTION;
	$sql_vars
	IF _range < 1 THEN
	SELECT * FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_active_field'] . "=1 AND classification_status <> 'Waiting'  AND (paper_excluded = _excluded OR _excluded ='_' )   $sql_append ORDER BY " . $table_config['order_by'] . ";
ELSE
SELECT * FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_active_field'] . "=1 AND classification_status <> 'Waiting' AND (paper_excluded = _excluded OR _excluded ='_' )   $sql_append ORDER BY " . $table_config['order_by'] . " LIMIT _start_by , _range;
END IF;
COMMIT;
END";

			if ($run_query)
				$res = $this->CI->db2->query($procedure_c);

			if ($verbose) {
				echo "<p>$procedure_c</p>";
				print_test($res);
			}

			//papers assigned to a user


			$procedure_p = "
				DROP PROCEDURE IF EXISTS get_list_papers_assigned
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}

			$procedure_p = "CREATE PROCEDURE get_list_papers_assigned(IN  _user_id  INT,IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
$sql_vars
IF _range < 1 THEN
SELECT * FROM view_paper_assigned
WHERE " . $table_config['table_active_field'] . "=1  AND classification_status <> 'Waiting' AND (assigned_user_id = _user_id)   $sql_append ORDER BY " . $table_config['order_by'] . ";
ELSE
SELECT * FROM view_paper_assigned
WHERE " . $table_config['table_active_field'] . "=1  AND classification_status <> 'Waiting' AND  (assigned_user_id = _user_id)     $sql_append ORDER BY " . $table_config['order_by'] . " LIMIT _start_by , _range;
END IF;
COMMIT;
END";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}




			//papers pending


			$procedure_p = "
				DROP PROCEDURE IF EXISTS get_list_papers_pending
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}

			$procedure_p = "CREATE PROCEDURE get_list_papers_pending(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
$sql_vars
IF _range < 1 THEN
SELECT * FROM view_paper_pending
WHERE " . $table_config['table_active_field'] . "=1  AND classification_status <> 'Waiting'   $sql_append ORDER BY " . $table_config['order_by'] . ";
ELSE
SELECT * FROM view_paper_pending
WHERE " . $table_config['table_active_field'] . "=1 AND classification_status <> 'Waiting'     $sql_append ORDER BY " . $table_config['order_by'] . " LIMIT _start_by , _range;
END IF;
COMMIT;
END";

			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}


			//papers processed


			$procedure_p = "
				DROP PROCEDURE IF EXISTS get_list_papers_processed
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}

			$procedure_p = "CREATE PROCEDURE get_list_papers_processed(IN _start_by INT,IN _range INT, IN _search VARCHAR(500))
BEGIN
START TRANSACTION;
$sql_vars
IF _range < 1 THEN
SELECT * FROM view_paper_processed
WHERE " . $table_config['table_active_field'] . "=1 AND classification_status <> 'Waiting'    $sql_append ORDER BY " . $table_config['order_by'] . ";
ELSE
SELECT * FROM view_paper_processed
WHERE " . $table_config['table_active_field'] . "=1 AND classification_status <> 'Waiting'     $sql_append ORDER BY " . $table_config['order_by'] . " LIMIT _start_by , _range;
END IF;
COMMIT;
END";

			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}





		} else {

			$procedure = "CREATE PROCEDURE get_list_" . $config . "(IN _start_by INT,IN _range INT, IN _search VARCHAR(500)" . $extra_parametters . ")
BEGIN
START TRANSACTION;
$sql_vars
IF _range < 1 THEN
SELECT * FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_active_field'] . "=1 " . $extra_condition . " $sql_append ORDER BY " . $table_config['order_by'] . ";
ELSE
SELECT * FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_active_field'] . "=1 " . $extra_condition . " $sql_append ORDER BY " . $table_config['order_by'] . " LIMIT _start_by , _range;
END IF;
COMMIT;
END";
		}

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			print_test($res);



	}


	/*
	 * Creating the stored procedure to count the number of elements.
	 */
	public function create_stored_procedure_count($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		$table_config = get_table_config($config, $target_db);

		$sql_append = "";
		$sql_vars = "";
		if (!empty($table_config['search_by'])) {

			$fields_search = explode(",", $table_config['search_by']);

			$i = 0;
			$sql_append .= " AND ( ";
			foreach ($fields_search as $field_name) {
				$sql_vars .= " SET @search_" . $field_name . " := CONCAT('%',TRIM(_search),'%') ; ";
				if ($i == 0) {

					$sql_append .= " ($field_name LIKE  @search_" . $field_name . ") ";
				} else {
					$sql_append .= " OR ($field_name LIKE  @search_" . $field_name . ") ";
				}
				$i = 1;
			}

			$sql_append .= " ) ";


		}

		$procedure = "
				DROP PROCEDURE IF EXISTS count_" . $config . ";
				";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			echo "<p>$procedure</p>";


		if ($config == "papers") {
			$procedure = "CREATE PROCEDURE count_" . $config . "(IN _search VARCHAR(100) ,IN _excluded VARCHAR(2))
BEGIN
START TRANSACTION;
$sql_vars
SELECT count(*) as nbr FROM " . $table_config['table_name'] . " WHERE " . $table_config['table_active_field'] . "=1   AND (paper_excluded = _excluded OR _excluded ='_'  )  $sql_append;
COMMIT;
END";
			//papers for classification

			$procedure_c = "
				DROP PROCEDURE IF EXISTS count_papers_class
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_c);

			if ($verbose) {
				echo "<p>$procedure_c</p>";
				print_test($res);
			}


			$procedure_c = "CREATE PROCEDURE count_papers_class(IN _search VARCHAR(100) ,IN _excluded VARCHAR(2))
	BEGIN
	START TRANSACTION;
	$sql_vars
	SELECT count(*) as nbr FROM " . $table_config['table_name'] . " WHERE " . $table_config['table_active_field'] . "=1 AND classification_status <> 'Waiting'  AND (paper_excluded = _excluded OR _excluded ='_'  )  $sql_append;
	COMMIT;
	END";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_c);

			if ($verbose) {
				echo "<p>$procedure_c</p>";
				print_test($res);
			}
			//papers assigned to a user

			$procedure_p = "
				DROP PROCEDURE IF EXISTS count_papers_assigned
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}


			$procedure_p = "CREATE PROCEDURE count_papers_assigned(IN  _user_id  INT,IN _search VARCHAR(100))
BEGIN
START TRANSACTION;
$sql_vars
SELECT count(*) as nbr FROM view_paper_assigned WHERE " . $table_config['table_active_field'] . "=1  AND classification_status <> 'Waiting' AND (assigned_user_id = _user_id  )  $sql_append;
COMMIT;
END";

			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}

			//papers pending

			$procedure_p = "
				DROP PROCEDURE IF EXISTS count_papers_pending
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}


			$procedure_p = "CREATE PROCEDURE count_papers_pending(IN _search VARCHAR(100))
		BEGIN
		START TRANSACTION;
		$sql_vars
		SELECT count(*) as nbr FROM view_paper_pending WHERE " . $table_config['table_active_field'] . "=1 AND classification_status <> 'Waiting'  $sql_append;
		COMMIT;
		END";

			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}


			//papers processes

			$procedure_p = "
				DROP PROCEDURE IF EXISTS count_papers_processed
				";
			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}


			$procedure_p = "CREATE PROCEDURE count_papers_processed(IN _search VARCHAR(100))
				BEGIN
				START TRANSACTION;
				$sql_vars
				SELECT count(*) as nbr FROM view_paper_processed WHERE " . $table_config['table_active_field'] . "=1  AND classification_status <> 'Waiting'  $sql_append;
				COMMIT;
				END";

			if ($run_query)
				$res = $this->CI->db2->query($procedure_p);

			if ($verbose) {
				echo "<p>$procedure_p</p>";
				print_test($res);
			}






		} else {
			$procedure = "CREATE PROCEDURE count_" . $config . "(IN _search VARCHAR(100))
BEGIN
START TRANSACTION;
$sql_vars
SELECT count(*) as nbr FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_active_field'] . "=1 $sql_append;
COMMIT;
END";

		}

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			print_test($res);

	}


	/*
	 * Creation of the stored procedure to delete an element
	 */
	public function create_stored_procedure_remove($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		$table_config = get_table_config($config, $target_db);

		$procedure = "
				DROP PROCEDURE IF EXISTS remove_" . $config . ";
				";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			echo "<p>$procedure</p>";


		$procedure = "CREATE PROCEDURE remove_" . $config . "(IN _element_id INT)
BEGIN
START TRANSACTION;
UPDATE " . $table_config['table_name'] . " SET " . $table_config['table_active_field'] . "=0
WHERE " . $table_config['table_id'] . "= _element_id;
COMMIT;
END";
		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			print_test($res);

	}


	/*
	 * Creation of the stored procedure to add an element
	 */
	public function create_stored_procedure_add($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		$table_config = get_table_config($config, $target_db);

		$fields_param = "";
		$fields_col = "";
		$fields_val = "";

		$i = 0;

		foreach ($table_config['fields'] as $key => $value) {
			//get the fields type
			$size = "250";
			$type = "VARCHAR";
			if ($value['field_type'] == 'number') {
				$type = "INT";

			} elseif ($value['field_type'] == 'text') {
				if (!empty($value['field_size'])) {
					$size = $value['field_size'] + 5;

				}

				$type = " VARCHAR($size)";
			}

			if (isset($value['input_type']) and $value['input_type'] == 'image') {

				$type = " LONGBLOB ";
			}

			if (($value['on_add'] != 'not_set' and $value['on_add'] != 'drill_down' and $value['on_add'] != 'disabled') and !((isset($value['multi-select']) and isset($value['multi-select']) == 'Yes')))
				if ($i == 0) {

					$fields_param .= "_" . $key . " " . $type;
				} else {
					$fields_param .= " , _" . $key . " " . $type;

					if ($i == 1) {
						$fields_val .= "_" . $key;
						$fields_col = "$key";
					} else {

						$fields_val .= " , _" . $key;
						$fields_col .= " , " . $key;
					}
				}

			$i++;
		}


		$procedure = "
				DROP PROCEDURE IF EXISTS add_" . $config . ";
				";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			echo "<p>$procedure</p>";

		$procedure = "CREATE PROCEDURE add_" . $config . "(" . $fields_param . ")
BEGIN
START TRANSACTION;
INSERT INTO " . $table_config['table_name'] . " (" . $fields_col . ") VALUES (" . $fields_val . ");
SELECT " . $table_config['table_id'] . " AS id_value FROM " . $table_config['table_name'] . " WHERE " . $table_config['table_id'] . " = LAST_INSERT_ID();
COMMIT;
END";
		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

	}

	/*
	 * Creation of the stored procedure to edit an item
	 */
	public function create_stored_procedure_update($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		$table_config = get_table_config($config, $target_db);

		$fields_param = "";

		$fields_val = "";

		$i = 0;

		foreach ($table_config['fields'] as $key => $value) {
			//get the fields type
			$size = "250";
			$type = "VARCHAR";
			if ($value['field_type'] == 'number') {
				$type = "INT";

			} elseif ($value['field_type'] == 'text') {
				if (!empty($value['field_size'])) {
					$size = $value['field_size'] + 5;

				}

				$type = " VARCHAR($size)";
			}

			if (isset($value['input_type']) and $value['input_type'] == 'image') {

				$type = " LONGBLOB ";
			}


			if (($value['on_edit'] != 'not_set' and $value['on_edit'] != 'drill_down' and $value['on_edit'] != 'disabled') and !((isset($value['multi-select']) and isset($value['multi-select']) == 'Yes'))) {
				if ($i == 0) {

					$fields_param .= "_" . $key . " " . $type;
					$fields_val .= "$key = _" . $key;


				} else {
					$fields_param .= " , _" . $key . " " . $type;
					$fields_val .= " , $key = _" . $key;

				}
				$i = 1;
			}
		}


		$procedure = "
				DROP PROCEDURE IF EXISTS update_" . $config . ";
				";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			echo "<p>$procedure</p>";

		$procedure = "CREATE PROCEDURE update_" . $config . "(_element_id INT , " . $fields_param . ")
BEGIN
START TRANSACTION;
UPDATE  " . $table_config['table_name'] . " SET " . $fields_val . "
WHERE (" . $table_config['table_id'] . " = _element_id);
COMMIT;
END";

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			print_test($res);



	}


	/*
	 * Creation of the stored procedure to display the details of an element
	 */
	public function create_stored_procedure_detail($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		$table_config = get_table_config($config, $target_db);



		$procedure = "DROP PROCEDURE IF EXISTS get_detail_" . $config . ";";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			echo "<p>$procedure</p>";

		$size = "250";
		$type = "VARCHAR";
		if ($table_config['fields'][$table_config['table_id']]['field_type'] == 'number') {
			$type = "INT";

		} elseif ($table_config['fields'][$table_config['table_id']]['field_type'] == 'text') {
			if (!empty($table_config['fields'][$table_config['table_id']]['field_size'])) {
				$size = $value['field_size'] + 5;

			}

			$type = " VARCHAR($size)";
		}

		$procedure = "CREATE PROCEDURE get_detail_" . $config . "(IN _row_id $type)
BEGIN
START TRANSACTION;
SELECT * FROM " . $table_config['table_name'] . "
WHERE " . $table_config['table_id'] . "= _row_id;
COMMIT;
END";

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			print_test($res);



	}



	/*
	 * Adding Foreign Keys
	 */
	public function add_froreign_keys_constraint($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{


		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		$table_config = get_table_config($config, $target_db);



		$sql_constraint_header = "ALTER TABLE " . $table_config['table_name'];
		$sql_constraint = "";
		$sql_constraint_drop = "";
		$i = 1;
		foreach ($table_config['fields'] as $key => $value) {
			if (!empty($value['input_type']) and !empty($value['input_select_source']) and !empty($value['input_select_values']) and ($value['input_type'] == 'select') and ($value['input_select_source'] == 'table')) {
				if (!(isset($value['category_type']) and ($value['category_type'] == 'WithSubCategories' or $value['category_type'] == 'WithMultiValues'))) { //Le schamps qui sonts dans les tables association



					//print_test($value);
					$conf = explode(";", $value['input_select_values']);
					//print_test($conf);
					$ref_table = $conf[0];
					//echo $ref_table;
					if ($ref_table != "users") {
						$constraint = $key;
						$linked_config = get_table_config($ref_table, $target_db);
						$table_linked = $linked_config['table_name'];
						$table_linked_id = $linked_config['table_id'];
						if ($i != 1) {
							$sql_constraint .= ',';
							$sql_constraint_drop .= ',';
						}
						$sql_constraint .= " ADD CONSTRAINT  " . $config . "_" . $constraint . "  FOREIGN KEY ( $constraint ) REFERENCES $table_linked ( $table_linked_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
							";
						$sql_constraint_drop .= " DROP FOREIGN KEY  " . $config . "_" . $constraint . "  ";
						$i++;
					}
				}
			}

		}
		if (!empty($sql_constraint)) {
			$sql_query = $sql_constraint_header . $sql_constraint . ";";
			///$sql_query=$sql_constraint_header.$sql_constraint_drop.";";


			if ($verbose)
				echo "<p>$sql_query</p>";


			if ($run_query)
				$res = $this->CI->db2->query($sql_query);

			if ($verbose)
				print_test($res);
		}

	}

	/*
	 * deleting Foreign Keys
	 */
	public function drop_froreign_keys_constraint($config, $run_query = TRUE, $verbose = TRUE, $target_db = 'current')
	{


		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		$table_config = get_table_config($config, $target_db);



		$sql_constraint_header = "ALTER TABLE " . $table_config['table_name'];

		$sql_constraint_drop = "";
		$i = 1;
		foreach ($table_config['fields'] as $key => $value) {
			if (!empty($value['input_type']) and !empty($value['input_select_source']) and !empty($value['input_select_values']) and ($value['input_type'] == 'select') and ($value['input_select_source'] == 'table')) {
				if (!(isset($value['category_type']) and ($value['category_type'] == 'WithSubCategories' or $value['category_type'] == 'WithMultiValues'))) { //Le schamps qui sonts dans les tables association




					$conf = explode(";", $value['input_select_values']);

					$ref_table = $conf[0];

					if ($ref_table != "users") {
						$constraint = $key;

						if ($i != 1) {

							$sql_constraint_drop .= ',';
						}

						$sql_constraint_drop .= " DROP FOREIGN KEY  " . $config . "_" . $constraint . "  ";
						$i++;
					}
				}
			}

		}
		if (!empty($sql_constraint)) {
			$sql_query = $sql_constraint_header . $sql_constraint_drop . ";";


			if ($verbose)
				echo "<p>$sql_query</p>";


			if ($run_query)
				$res = $this->CI->db2->query($sql_query);

			if ($verbose)
				print_test($res);
		}

	}

	/*
		   create a database table based on the provided configuration data ($config) in the specified database ($target_db). 
		   It handles various field types, default values, and primary key settings during the table creation process
	   */
	public function create_table_config($config, $target_db = 'current')
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		//	print_test($config);
		$table_id = $config['table_id'];
		$del_line = "DROP TABLE IF EXISTS " . $config['table_name'] . ";";
		$res_sql = $this->CI->manage_mdl->run_query($del_line, False, $target_db);

		$sql = "CREATE TABLE IF NOT EXISTS " . $config['table_name'] . " (
			$table_id int(11) NOT NULL AUTO_INCREMENT,";
		$field_default = "   ";
		$field_type = "  ";
		foreach ($config['fields'] as $key => $value) {

			if ($key != $table_id and $key != $config['table_active_field']) {
				//start with select
				if (!empty($value['input_type']) and $value['input_type'] == 'select') {
					if ($value['input_select_source'] == 'array') { //static
						$i = 1;
						$field_type = " enum(";
						foreach ($value['input_select_values'] as $k => $v) {
							if ($i == 1)
								$field_type .= "'" . $k . "'";
							else
								$field_type .= ",'" . $k . "'";

							$i++;
						}

						$field_type .= ") ";
						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";

						if (!empty($value['initial_value'])) {

							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}
					} elseif ($value['input_select_source'] == 'yes_no') {
						$field_type = " int(2) ";
						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";
						if (isset($value['initial_value']) and trim($value['initial_value']) != '') {

							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}



					} else { //dynamic
						$field_type = " int(11) ";
						//$field_default="  DEFAULT '0' ";
						$field_default = "  DEFAULT NULL ";

					}

				} elseif (!empty($value['input_type']) and $value['input_type'] == 'date') {
					$field_type = " timestamp ";
					$field_default = "  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ";
					$field_default = "  NOT NULL DEFAULT CURRENT_TIMESTAMP  ";

				} else { //Free category
					if (!empty($value['field_value']) and $value['field_value'] == '0_1') { //Yes_no

					} elseif ($value['field_type'] == 'number') {
						$field_type = " int(" . $value['field_size'] . ") ";
						$field_default = "   DEFAULT '0' ";
						if (!empty($value['initial_value'])) {

							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}

					} else {
						$field_type = " varchar(" . $value['field_size'] . ") ";
						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";
						if (!empty($value['initial_value'])) {

							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}
					}


				}

				if (!(isset($value['category_type']) and ($value['category_type'] == 'WithSubCategories' or $value['category_type'] == 'WithMultiValues'))) {
					$sql .= " " . $key . " $field_type $field_default,";
				}
			}



		}







		$sql .= " " . $config['table_active_field'] . " int(1) NOT NULL DEFAULT '1',";
		$sql .= " PRIMARY KEY ($table_id)";

		$sql .= ") ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

		$res_sql = $this->CI->manage_mdl->run_query($sql, False, $target_db);
		//echo $sql;
		//print_test($res_sql);
		return "$del_line $sql";
	}

	/*
		   create a database table based on the provided configuration data ($config) in the specified database ($target_db). 
		   It handles various field types, default values, and primary key settings during the table creation process
	   */
	public function create_table_configuration($config, $target_db = 'current')
	{
		//echo "in";
		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		//	print_test($config);
		$table_id = $config['table_id'];
		$del_line = "DROP TABLE IF EXISTS " . $config['table_name'] . ";";
		$res_sql = $this->CI->manage_mdl->run_query($del_line, False, $target_db);

		$sql = "CREATE TABLE IF NOT EXISTS " . $config['table_name'] . " (
			$table_id int(11) NOT NULL AUTO_INCREMENT,";
		$field_default = "   ";
		$field_type = "  ";
		foreach ($config['fields'] as $key => $value) {

			if ($key != $table_id and $key != $config['table_active_field'] and empty($value['not_in_db'])) {
				//start with select
				if (!empty($value['input_type']) and $value['input_type'] == 'select') {
					if ($value['input_select_source'] == 'array') { //static
						$i = 1;
						$field_type = " enum(";
						foreach ($value['input_select_values'] as $k => $v) {
							if ($i == 1)
								$field_type .= "'" . $k . "'";
							else
								$field_type .= ",'" . $k . "'";

							$i++;
						}

						$field_type .= ") ";
						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";
						//print_test($value);
						//print_test($value['default_value']);
						if (!empty($value['default_value'])) {

							$field_default = "   NOT NULL DEFAULT '" . $value['default_value'] . "' ";
						}

					} elseif ($value['input_select_source'] == 'yes_no') {
						$field_type = " int(2) ";
						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";
						if (isset($value['default_value']) and trim($value['default_value']) != '') {

							$field_default = "   NOT NULL DEFAULT '" . $value['default_value'] . "' ";
						}



					} else { //dynamic
						$field_type = " int(11) ";
						//$field_default="  DEFAULT '0' ";
						//	$field_default="  DEFAULT NULL ";

						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";

					}

				} elseif (!empty($value['field_type']) and $value['field_type'] == 'time') {
					$field_type = " timestamp ";

					$field_default = (empty($value['mandatory'])) ? " NULL  DEFAULT NULL " : "   NOT NULL ";

					if (isset($value['default_value']) and trim($value['default_value']) == 'CURRENT_TIMESTAMP') {

						$field_default = "   NOT NULL DEFAULT CURRENT_TIMESTAMP ";
					}

				} else { //Free category
					if ($value['field_type'] == 'number' || $value['field_type'] == 'int') {
						$field_type = " int(" . $value['field_size'] . ") ";

						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";

						if (!empty($value['default_value'])) {

							$field_default = "   NOT NULL DEFAULT '" . $value['default_value'] . "' ";
						}

					} elseif ($value['field_type'] == 'real') {
						$field_type = " double ";

						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";

						if (!empty($value['default_value'])) {

							$field_default = "   NOT NULL DEFAULT '" . $value['default_value'] . "' ";
						}

					} elseif ($value['field_type'] == 'image') {
						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";
						$field_type = " LONGBLOB ";
						//$field_default="   DEFAULT NULL ";


					} else {
						if ($value['field_type'] == 'longtext') {
							$field_type = " longtext ";
						} else {
							$field_type = " varchar(" . $value['field_size'] . ") ";
						}
						$field_default = (empty($value['mandatory'])) ? "   DEFAULT NULL " : "   NOT NULL ";
						if (!empty($value['initial_value'])) {

							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}
					}


				}

				if (!(isset($value['category_type']) and ($value['category_type'] == 'WithSubCategories' or $value['category_type'] == 'WithMultiValues'))) {
					$sql .= " " . $key . " $field_type $field_default,";
				}
			}



		}







		$sql .= " " . $config['table_active_field'] . " int(1) NOT NULL DEFAULT '1',";
		$sql .= " PRIMARY KEY ($table_id)";

		$sql .= ") ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

		$res_sql = $this->CI->manage_mdl->run_query($sql, False, $target_db);
		//		echo $del_line."<br/><br/>";

		//	echo $sql;
		// add initial query

		if (!empty($config['init_query'])) {
			foreach ($config['init_query'] as $key => $query) {
				if (!empty($query)) {
					$res_sql = $this->CI->manage_mdl->run_query($query, False, $target_db);
				}
			}

		}

		//	print_test($res_sql);
		return "$del_line $sql";
	}



	/*
	 * Creation of the stored procedure to display a list of elements
	 */
	public function create_view($config, $target_db = 'current', $run_query = TRUE, $verbose = TRUE)
	{
		$target_db = ($target_db == 'current') ? project_db() : $target_db;

		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);
		if ($verbose)
			print_test($config);

		$procedure = "
				DROP VIEW IF EXISTS " . $config['name'] . ";
				";
		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);

			if ($verbose)
				print_test($res);
		}


		$procedure = "
				CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW  " . $config['name'] . " AS " . $config['script'];

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);

			if ($verbose)
				print_test($res);
		}
	}
	/*
	 * Création de la procedure stocké pour afficher la liste
	 */
	public function generate_stored_procedure_list($config, $target_db = 'current', $run_query = TRUE, $verbose = TRUE)
	{

		//$run_query=false;
		//$run_query=false;

		$target_db = ($target_db == 'current') ? project_db() : $target_db;

		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		//$table_config=get_table_config($config,$target_db);


		$sql_append = "";
		$sql_vars = "";
		if (!empty($config['search_by'])) {

			$fields_search = explode(",", $config['search_by']);

			$i = 0;
			$sql_append .= " AND ( ";
			foreach ($fields_search as $field_name) {
				$sql_vars .= " SET @search_" . $field_name . " := CONCAT('%',TRIM(_search),'%') ; ";
				if ($i == 0) {

					$sql_append .= " ($field_name LIKE  @search_" . $field_name . ") ";
				} else {
					$sql_append .= " OR ($field_name LIKE  @search_" . $field_name . ") ";
				}
				$i = 1;
			}

			$sql_append .= " ) ";


		}

		$procedure = "
				DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'] . ";
				";

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);

			if ($verbose)
				print_test($res);
		}

		$extra_parametters = "";
		$extra_condition = "";

		if (!empty($config['conditions'])) {

			foreach ($config['conditions'] as $key_condition => $value_condition) {
				$operator = " = ";
				$pre_value = " '";
				$post_value = "' ";
				if (!empty($value_condition['evaluation'])) {
					switch ($value_condition['evaluation']) {


						case 'different':
							$operator = " <> ";
							break;

						case 'like':
							$operator = " LIKE ";

							break;

						case 'contain':
							$operator = " LIKE ";
							$pre_value = " '%";
							$post_value = "%'";
							break;

						case 'start_with':
							$operator = " <> ";
							$post_value = "%'";
							break;

						case 'end_with':
							$operator = " <> ";
							$pre_value = " '%";
							break;

						default:
							$operator = " = ";
							break;
					}
				}

				if ($value_condition['add_on_generation']) {
					$extra_condition .= " AND " . $value_condition['field'] . $operator . $pre_value . $value_condition['value'] . $post_value . " ";
				} else {
					//work only for equal
					$parameter_type = !empty($value_condition['parameter_type']) ? " " . $value_condition['parameter_type'] : " VARCHAR(3)";
					$extra_parametters .= " ,IN  _" . $value_condition['field'] . "  " . $parameter_type;
					$extra_condition .= " AND " . $value_condition['field'] . " = _" . $value_condition['field'] . " ";
				}
			}
		}

		/*	if($config=="str_mng"){
									 $extra_parametters= " ,IN _lang VARCHAR(3)";
									 $extra_condition= " AND str_lang = _lang ";
								 }
						 
						 */
		$order_by = (!empty($config['order_by']) ? " ORDER BY " . $config['order_by'] : " ");

		$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(IN _start_by INT,IN _range INT, IN _search VARCHAR(500)" . $extra_parametters . ")
					BEGIN
					START TRANSACTION;
					$sql_vars
					IF _range < 1 THEN
					SELECT * FROM " . $config['table_name'] . "
WHERE " . $config['table_active_field'] . "=1 " . $extra_condition . " $sql_append  " . $order_by . ";
ELSE
SELECT * FROM " . $config['table_name'] . "
WHERE " . $config['table_active_field'] . "=1 " . $extra_condition . " $sql_append  " . $order_by . " LIMIT _start_by , _range;
END IF;
COMMIT;
END";


		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);

			if ($verbose)
				print_test($res);
		}


	}



	/*
	 * Création de la procedure stocké pour ajouter un élément
	 */
	public function generate_stored_procedure_add($config, $target_db = 'current', $run_query = TRUE, $verbose = TRUE)
	{
		//$run_query=false;

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		//$table_config=get_table_config($config,$target_db);

		$fields_param = "";
		$fields_col = "";
		$fields_val = "";

		$i = 0;

		foreach ($config['fields'] as $k_field => $v_type) {
			//get the fields type

			if ($i == 0) {

				$fields_param .= "_" . $k_field . " " . $v_type;
			} else {
				$fields_param .= " , _" . $k_field . " " . $v_type;

				if ($i == 1) {
					$fields_val .= "_" . $k_field;
					$fields_col = "$k_field";
				} else {

					$fields_val .= " , _" . $k_field;
					$fields_col .= " , " . $k_field;
				}
			}

			$i++;
		}


		$procedure = "
				DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'] . ";
				";
		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);
			if ($verbose)
				print_test($res);
		}

		$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(" . $fields_param . ")
BEGIN
START TRANSACTION;
INSERT INTO " . $config['table_name'] . " (" . $fields_col . ") VALUES (" . $fields_val . ");
SELECT " . $config['table_id'] . " AS id_value FROM " . $config['table_name'] . " WHERE " . $config['table_id'] . " = LAST_INSERT_ID();
COMMIT;
END";
		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);
			if ($verbose)
				print_test($res);
		}

		//conflicting users display


		$procedure_p = "
	DROP PROCEDURE IF EXISTS get_list_papers_conflicting_users
	";
		if ($run_query)
			$res = $this->CI->db2->query($procedure_p);

		if ($verbose) {
			echo "<p>$procedure_p</p>";
			print_test($res);
		}

		$procedure_p = "CREATE   PROCEDURE  get_list_papers_conflicting_users  (IN  _start_by  INT, IN  _range  INT, IN  _search  VARCHAR(500), IN  _screening_phase  VARCHAR(10), IN  _screening_status  VARCHAR(20), IN  _user_id  VARCHAR(20))  BEGIN 
START TRANSACTION;
IF _range < 1 THEN
SELECT *,
GROUP_CONCAT(CASE WHEN tab.screening_decision != lis.your_decision THEN tab.users END SEPARATOR ', ') AS conflicting_users 
FROM view_conflicting_users AS tab 
LEFT JOIN (SELECT id, screening_decision AS your_decision FROM view_conflicting_users AS tab WHERE tab.user_id = _user_id) AS lis 
ON (tab.id=lis.id) 
WHERE screening_phase=_screening_phase AND paper_active=1
GROUP BY tab.id
ORDER BY tab.id ASC ;
ELSE
SELECT *,
GROUP_CONCAT(CASE WHEN tab.screening_decision != lis.your_decision THEN tab.users END SEPARATOR ', ') AS conflicting_users 
FROM view_conflicting_users AS tab 
LEFT JOIN (SELECT id, screening_decision AS your_decision FROM view_conflicting_users AS tab WHERE tab.user_id = _user_id) AS lis 
ON (tab.id=lis.id) 
WHERE screening_phase=_screening_phase AND paper_active=1 
GROUP BY tab.id
ORDER BY tab.id ASC  LIMIT _start_by , _range;
END IF;
COMMIT;
END";


		if ($run_query)
			$res = $this->CI->db2->query($procedure_p);

		if ($verbose) {
			echo "<p>$procedure_p</p>";
			print_test($res);
		}
	}




	/*
	 * Création de la procedure stocké pour modifier un élément
	 */
	public function generate_stored_procedure_update($config, $target_db = 'current', $run_query = TRUE, $verbose = TRUE)
	{
		//$run_query=false;
		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		//	$table_config=get_table_config($config,$target_db);

		$fields_param = "";

		$fields_val = "";

		$i = 0;

		foreach ($config['fields'] as $k_field => $v_type) {
			//get the fields type

			if ($i == 0) {

				$fields_param .= "_" . $k_field . " " . $v_type;
				$fields_val .= "$k_field = _" . $k_field;


			} else {
				$fields_param .= " , _" . $k_field . " " . $v_type;
				$fields_val .= " , $k_field = _" . $k_field;

			}
			$i = 1;


		}

		$procedure = "
				DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'];


		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);
			if ($verbose)
				print_test($res);
		}


		$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(_element_id INT , " . $fields_param . ")
BEGIN
START TRANSACTION;
UPDATE  " . $config['table_name'] . " SET " . $fields_val . "
WHERE (" . $config['table_id'] . " = _element_id);
COMMIT;
END";

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query) {
			$res = $this->CI->db2->query($procedure);

			if ($verbose)
				print_test($res);
		}


	}

	/*
	 * Création de la procedure stocké pour afficher le détail d'un élément
	 */
	public function generate_stored_procedure_detail($config, $target_db = 'current', $run_query = TRUE, $verbose = TRUE)
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		//$table_config=get_table_config($config,$target_db);



		$procedure = "DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'] . ";";
		if ($verbose)
			echo "<p>$procedure</p>";
		if ($run_query) {
			$res = $this->CI->db2->query($procedure);
			if ($verbose)
				echo "<p>$res</p>";
		}





		$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(IN _row_id " . $config['table_id_type'] . ")
					BEGIN
					START TRANSACTION;
					SELECT * FROM " . $config['table_name'] . "
WHERE " . $config['table_id'] . "= _row_id;
COMMIT;
END";

		if ($verbose)
			echo "<p>$procedure</p>";
		if ($run_query) {
			$res = $this->CI->db2->query($procedure);
			if ($verbose)
				echo "<p>$res</p>";
		}



	}



	/*
	 * Création de la procedure stocké pour supprimer un élément
	 */
	public function generate_stored_procedure_remove($config, $target_db = 'current', $run_query = TRUE, $verbose = TRUE)
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		$this->CI->db2 = $this->CI->load->database($target_db, TRUE);


		if ($config['stored_procedure_name'] == 'remove_paper') {
			$config['table_one'] = 'screening_paper';
			$config['table_two'] = 'paperauthor';
			$config['table_one_field'] = 'paper_id';
			$config['table_two_field'] = 'paperId';
			$config['table_one_active_field'] = 'screening_active';
			$config['table_two_active_field'] = 'paperId';


			$procedure = "
				DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'] . ";
				";

			if ($run_query)
				$res = $this->CI->db2->query($procedure);

			if ($verbose)
				echo "<p>$procedure</p>";

			$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(IN _element_id INT)
            BEGIN
            START TRANSACTION;
            UPDATE " . $config['table_name'] . " SET " . $config['table_active_field'] . "=0
            WHERE " . $config['table_id'] . "= _element_id;
            UPDATE " . $config['table_one'] . " SET " . $config['table_one_active_field'] . "=0
            WHERE " . $config['table_one_field'] . "= _element_id;
            UPDATE " . $config['table_two'] . " SET " . $config['table_two_active_field'] . "=0
            WHERE " . $config['table_two_field'] . "= _element_id;
            COMMIT;
            END";
		} else if ($config['stored_procedure_name'] == 'remove_qa_result') {
			$procedure = "
				DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'] . ";
				";

			if ($run_query)
				$res = $this->CI->db2->query($procedure);

			if ($verbose)
				echo "<p>$procedure</p>";


			$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(IN _element_id INT)
            BEGIN
            START TRANSACTION;
            DELETE FROM " . $config['table_name'] . ";
            COMMIT;
            END";
		} else if ($config['stored_procedure_name'] == 'remove_qa_assignment') {
			$procedure = "
				DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'] . ";
				";

			if ($run_query)
				$res = $this->CI->db2->query($procedure);

			if ($verbose)
				echo "<p>$procedure</p>";
			$status = 'Pending';
			$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(IN _element_id INT)
            BEGIN
            START TRANSACTION;
            UPDATE " . $config['table_name'] . " SET " . $config['table_active_field'] . "=0, qa_status ='Pending';
            COMMIT;
            END";
		} else {
			$procedure = "
				DROP PROCEDURE IF EXISTS " . $config['stored_procedure_name'] . ";
				";

			if ($run_query)
				$res = $this->CI->db2->query($procedure);

			if ($verbose)
				echo "<p>$procedure</p>";


			$procedure = "CREATE PROCEDURE " . $config['stored_procedure_name'] . "(IN _element_id INT)
            BEGIN
            START TRANSACTION;
            UPDATE " . $config['table_name'] . " SET " . $config['table_active_field'] . "=0
            WHERE " . $config['table_id'] . "= _element_id;
            COMMIT;
            END";
		}

		if ($verbose)
			echo "<p>$procedure</p>";

		if ($run_query)
			$res = $this->CI->db2->query($procedure);

		if ($verbose)
			print_test($res);

	}






}