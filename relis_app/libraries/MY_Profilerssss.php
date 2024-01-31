<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MY_Profilerssss extends CI_Profiler
{
	function MY_Profilerssss()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Compile memory usage
	 *
	 * Display total used memory
	 *
	 * @return	string
	 */
	protected function _compile_memory_usage()
	{
		//return " \n\n ".($usage = memory_get_usage()) != '' ? number_format($usage) : "no_value";
		return "\n\n"
			. '<fieldset name="memory"><br/>-----------<br/>MEMORY_USAGE '
			. (($usage = memory_get_usage()) != '' ? number_format($usage) . ' bytes' : $this->CI->lang->line('profiler_no_memory'))
			. '</fieldset>';
	}


	/**
	 * Show query string
	 *
	 * @return	string
	 */
	protected function _compile_uri_string()
	{
		return '<fieldset name="ci_profiler_uri_string" ><br/>-----------<br/> URI_STRING'

			. '<div >'
			. ($this->CI->uri->uri_string === '' ? $this->CI->lang->line('profiler_no_uri') : $this->CI->uri->uri_string)
			. '</div></fieldset>';
	}

	// --------------------------------------------------------------------

	/**
	 * Show the controller and function that were called
	 *
	 * @return	string
	 */
	protected function _compile_controller_info()
	{
		return '<fieldset name="ci_profiler_controller_info"><br/>-----------<br/>COMPILE_CONTROLLER'
			. '<div>' . $this->CI->router->class . '/' . $this->CI->router->method
			. '</div></fieldset>';
	}
	/**
	 * Compile $_POST Data
	 *
	 * @return	string
	 */
	protected function _compile_post()
	{
		$output = "\n\n"
			. '<fieldset name="post_data"><br/>-----------<br/>POST_DATA'
			. "\n";

		if (count($_POST) === 0 && count($_FILES) === 0) {
			$output .= '<div>' . $this->CI->lang->line('profiler_no_post') . '</div>';
		} else {
			$output .= "\n\n<table>\n";

			foreach ($_POST as $key => $val) {
				is_int($key) or $key = "'" . htmlspecialchars($key, ENT_QUOTES, config_item('charset')) . "'";
				$val = (is_array($val) or is_object($val))
					? '<pre>' . htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));

				$output .= '<tr><td >&#36;_POST['
					. $key . '] </td><td >'
					. $val . "</td></tr>\n";
			}

			foreach ($_FILES as $key => $val) {
				is_int($key) or $key = "'" . htmlspecialchars($key, ENT_QUOTES, config_item('charset')) . "'";
				$val = (is_array($val) or is_object($val))
					? '<pre>' . htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));

				$output .= '<tr><td >&#36;_FILES['
					. $key . '] </td><td >'
					. $val . "</td></tr>\n";
			}

			$output .= "</table>\n";
		}

		return $output . '</fieldset>';
	}


	protected function _compile_benchmarks()
	{
		$profile = array();
		foreach ($this->CI->benchmark->marker as $key => $val) {
			// We match the "end" marker so that the list ends
			// up in the order that it was defined
			if (
				preg_match('/(.+?)_end$/i', $key, $match)
				&& isset($this->CI->benchmark->marker[$match[1] . '_end'], $this->CI->benchmark->marker[$match[1] . '_start'])
			) {
				$profile[$match[1]] = $this->CI->benchmark->elapsed_time($match[1] . '_start', $key);
			}
		}

		// Build a table containing the profile data.
		// Note: At some point we should turn this into a template that can
		// be modified. We also might want to make this data available to be logged

		$output = "\n\n"
			. '<fieldset name="benchmarks"><br/>-----------<br/>BENCHMARKS'
			. "\n"

			. "<table >";

		foreach ($profile as $key => $val) {
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output .= '<tr><td>'
				. $key . '</td><td>'
				. $val . "</td></tr>";
		}

		return $output . "</table></fieldset>";
	}



	/**
	 * Compile $_GET Data
	 *
	 * @return	string
	 */
	protected function _compile_get()
	{
		$output = '<fieldset name="get_data"><br/>-----------<br/>GET_DATA';

		if (count($_GET) === 0) {
			$output .= '<div>' . $this->CI->lang->line('profiler_no_get') . '</div>';
		} else {
			$output .= "<table>";

			foreach ($_GET as $key => $val) {
				is_int($key) or $key = "'" . htmlspecialchars($key, ENT_QUOTES, config_item('charset')) . "'";
				$val = (is_array($val) or is_object($val))
					? '<pre>' . htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));

				$output .= '<tr><td>&#36;_GET['
					. $key . ']</td><td>'
					. $val . "</td></tr>\n";
			}

			$output .= "</table>";
		}

		return $output . '</fieldset>';
	}
	public function run()
	{
		$output = '<div id="codeigniter_profiler" >';
		$fields_displayed = 0;
		$output = "";
		foreach ($this->_available_sections as $section) {
			if ($this->_compile_ { $section} !== FALSE) {
				$func = '_compile_' . $section;
				$output .= $this->{$func}();
				$fields_displayed++;
			}
		}

		if ($fields_displayed === 0) {
			//	$output .= '<p style="border:1px solid #5a0099;padding:10px;margin:20px 0;background-color:#eee;">'
			//			.$this->CI->lang->line('profiler_no_profiles').'</p>';

			$output .= '<p>' . $this->CI->lang->line('profiler_no_profiles') . '</p>';
		}

		$this->CI =& get_instance();
		$result['server_info'] = $_SERVER;
		$result['session'] = $this->CI->session->userdata();
		$result['profiler'] = $output . '</div>';

		//	echo json_encode($result);
		//print_test($this->CI->session->userdata());
		//echo $output=$output.'</div>';
		//set_log('output_profiler', "log addeddd") ;
		save_metrics(json_encode($result));
		return "";
	}
}