<?php
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
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

//This class is responsible for interacting with a remote BibTeX management system using HTTP requests
class Biblerproxy_lib
{
	private $instance;

	/*
		It holds the base URL of the remote BibTeX management system. The default value is set to 'http://relis_bibler:80/', 
		but it can be changed using the setURL() method.
	*/

	private  $url='http://bibler:8000/';

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	//private function __construct()	{}

	//method allows changing the base URL of the remote BibTeX management system.
	public function setURL($uri)
	{
		$this->url = $uri;
	}

	public static function getInstance()
	{
		if (!$instance) {
			$instance = new BiBlerProxy();
		}
		return $instance;
	}

	//sends an HTTP POST request to the specified URL with the provided data
	private function httpPost($url, $data)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		$payload = json_encode(array("bibtex" => utf8_encode($data)));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		// var_dump(json_last_error());
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		return $response;
	}

	//Adds an entry to the BibTeX database
	public function addEntry($data)
	{
		return $this->httpPost($this->url . "addentry/", $data);
	}

	//Retrieves a BibTeX entry from the database
	public function getBibtex($data)
	{
		return $this->httpPost($this->url . "getbibtex/", $data);
	}

	//Formats a BibTeX entry
	public function formatBibtex($data)
	{
		return $this->httpPost($this->url . "formatbibtex/", $data);
	}

	//Previews a BibTeX entry
	public function previewEntry($data)
	{
		return $this->httpPost($this->url . "previewentry/", $data);
	}

	//Validates a BibTeX entry
	public function validateEntry($data)
	{
		return $this->httpPost($this->url . "validateentry/", $data);
	}

	//Converts BibTeX to BibTeX
	public function bibtextobibtex($data)
	{
		return $this->httpPost($this->url . "bibtextobibtex/", $data);
	}

	//Converts BibTeX to SQL
	public function bibtextosql($data)
	{
		return $this->httpPost($this->url . "bibtextosql/", $data);
	}

	//Converts BibTeX to CSV
	public function bibtextocsv($data)
	{
		return $this->httpPost($this->url . "bibtextocsv/", $data);
	}

	//Converts BibTeX to HTML
	public function bibtextohtml($data)
	{
		return $this->httpPost($this->url . "bibtextohtml/", $data);
	}

	//Creates an entry for the "reliS" system.
	public function createentryforreliS($data)
	{
		return $this->httpPost($this->url . "createentryforrelis/", $data);
	}

	//Imports a BibTeX string for the "reliS" system.
	public function importbibtexstringforrelis($data)
	{
		return $this->httpPost($this->url . "importbibtexstringforrelis/", $data);
	}

	//Imports an EndNote string for the "reliS" system.
	public function importendnotestringforrelis($data)
	{
		return $this->httpPost($this->url . "importendnotestringforrelis/", $data);
	}

	//Generates a report using the provided data.
	public function generatereport($data)
	{
		return $this->httpPost($this->url . "generateReport/", $data);
	}
	
	public function fixJSON($json)
	{
		$regex = <<<'REGEX'
~
    "[^"\\]*(?:\\.|[^"\\]*)*"
    (*SKIP)(*F)
  | '([^'\\]*(?:\\.|[^'\\]*)*)'
~x
REGEX;
		return preg_replace_callback($regex, function ($matches) {
			return '"' . preg_replace('~\\\\.(*SKIP)(*F)|"~', '\\"', $matches[1]) . '"';
		}, $json);
	}
}