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
 * :Author: Brice Michel Bigendako
 * --------------------------------------------------------------------------
 * Functions used to access review data via API in JSON
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Apiquery extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index($sql = "", $targetDb = 'default')
    {
    }

    /**
     * provide a convenient way to run custom SQL queries and retrieve the results, while handling any errors that may occur during the execution process.
     */
    public function run($sql = "", $targetDb = 'default')
    {
        $sql = $sql ?: "select * from users";
        $pre_select_sql = " select* from ( ";
        $post_select_sql = " ) as T ";
        $sql = $pre_select_sql . $sql . $post_select_sql;
        $res = $this->manage_mdl->run_query($sql, true);
        if ($res['code'] == 0) {
            $result = json_encode($res['message']);
            print_r($result);
        } else {
            $result = "Error: " . $res['message'];
            print_r($result);
        }
    }
}