<?php
/**

 * This content is released under the MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	Brice
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

//provides methods for setting and retrieving various configuration properties related to an entity.
class Entityconf_lib
{

	public $config_id = '';
	public $table_name = '';
	public $table_id = '';
	public $table_active_field = '';
	public $main_field = '';
	public $views = '';
	public $fields = '';
	public $operations = '';

	public function __construct(array $config = array())
	{

		$this->CI =& get_instance();
		$this->initialize($config);

	}

	/*
		Initializes the class properties based on the provided configuration. 
		It iterates through the $config array and sets the corresponding properties using setter methods or directly assigning the values.
	*/
	public function initialize(array $config = array())
	{
		//	$this->clear();

		foreach ($config as $key => $val) {
			if (isset($this->$key)) {
				$method = 'set_' . $key;

				if (method_exists($this, $method)) {
					$this->$method($val);
				} else {
					$this->$key = $val;
				}
			}
		}


		return $this;
		//return (array)$this;

	}

	//Returns the entity configuration as an array
	public function get_configuration()
	{
		return (array) $this;
	}

	//Sets the $config_id property with the provided string value and returns the class instance
	public function set_config_id($str)
	{
		$this->config_id = (string) $str;
		return $this;
	}

	//Sets the $table_name property with the provided string value and returns the class instance.
	public function set_table_name($str)
	{
		$this->table_name = (string) $str;
		return $this;
	}

	// Sets the $table_id property with the provided string value and returns the class instance
	public function set_table_id($str)
	{
		$this->table_id = (string) $str;
		return $this;
	}

	//Sets the $table_active_field property with the provided string value and returns the class instance
	public function set_table_active_field($str)
	{
		$this->table_active_field = (string) $str;
		return $this;
	}
}