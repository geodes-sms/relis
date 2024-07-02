<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 
 * --------------------------------------------------------------------------
 *  :Author: Brice Michel Bigendako
 * --------------------------------------------------------------------------
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Unit_test extends CI_Controller
{
    private $userUnitTest;
    private $projectUnitTest;
    private $paperUnitTest;
    private $screeningUnitTest;
    private $quality_assessmentUnitTest;
    private $data_extractionUnitTest;
    private $reportingUnitTest;
    private $elementUnitTest;
    private $homeUnitTest;
    private $installUnitTest;
    private $apiUnitTest;
    private $apiQueryUnitTest;
    private $opUnitTest;
    private $adminUnitTest;
    private $configUnitTest;
    private $manageUnitTest;
    private $managerUnitTest;
    private $relisManagerUnitTest;

    function __construct()
    {
        parent::__construct();

        require_once('relis_app/helpers/tests/testFiles/project/classification_install_demoTestProject.php');

        $this->load->helper('tests/helpers/curl');
        $this->load->helper('tests/helpers/tests');
        $this->load->helper('tests/helpers/functions');
        $this->load->helper('tests/user_ut');
        $this->load->helper('tests/project_ut');
        $this->load->helper('tests/paper_ut');
        $this->load->helper('tests/screening_ut');
        $this->load->helper('tests/quality_assessment_ut');
        $this->load->helper('tests/data_extraction_ut');
        $this->load->helper('tests/reporting_ut');
        $this->load->helper('tests/element_ut');
        $this->load->helper('tests/home_ut');
        $this->load->helper('tests/install_ut');
        $this->load->helper('tests/admin_ut');
        $this->load->helper('tests/config_ut');
        $this->load->helper('tests/manage_ut');
        $this->load->helper('tests/manager_ut');
        $this->load->helper('tests/relismanager_ut');
        $this->load->helper('tests/api_ut');
        $this->load->helper('tests/apiquery_ut');
        $this->load->helper('tests/op_ut');
        $this->load->library('unit_test');

        $this->unit->use_strict(TRUE);
        $this->unit->set_test_items(array('test_controller', 'test_action', 'test_name', 'test_aspect', 'res_value', 'test_value', 'result'));

        $this->userUnitTest = new UserUnitTest();
        $this->projectUnitTest = new ProjectUnitTest();
        $this->paperUnitTest = new PaperUnitTest();
        $this->screeningUnitTest = new ScreeningUnitTest();
        $this->quality_assessmentUnitTest = new Quality_assessmentUnitTest();
        $this->data_extractionUnitTest = new Data_extractionUnitTest();
        $this->reportingUnitTest = new ReportingUnitTest();
        $this->elementUnitTest = new ElementUnitTest();
        $this->homeUnitTest = new HomeUnitTest();
        $this->installUnitTest = new InstallUnitTest();
        $this->adminUnitTest = new AdminUnitTest();
        $this->configUnitTest = new ConfigUnitTest();
        $this->manageUnitTest = new ManageUnitTest();
        $this->managerUnitTest = new ManagerUnitTest();
        $this->relisManagerUnitTest = new RelisManagerUnitTest();
        $this->apiUnitTest = new ApiUnitTest();
        $this->apiQueryUnitTest = new ApiQueryUnitTest();
        $this->opUnitTest = new OpUnitTest();
    }

    public function relis_unit_test($result = "html_report")
    {
        // Record the start time of the tests
        $startTime = microtime(true);

        $this->userUnitTest->run_tests();
        $this->projectUnitTest->run_tests();
        $this->paperUnitTest->run_tests();
        $this->screeningUnitTest->run_tests();
        $this->quality_assessmentUnitTest->run_tests();
        $this->data_extractionUnitTest->run_tests();
        $this->reportingUnitTest->run_tests();
        $this->elementUnitTest->run_tests(); 
        $this->homeUnitTest->run_tests();
        $this->installUnitTest->run_tests();
        $this->adminUnitTest->run_tests();
        $this->configUnitTest->run_tests();
        $this->manageUnitTest->run_tests();
        $this->managerUnitTest->run_tests();
        $this->relisManagerUnitTest->run_tests();
        $this->apiUnitTest->run_tests();
        $this->apiQueryUnitTest->run_tests();
        $this->opUnitTest->run_tests();

        // Record the end time of the tests
        $endTime = microtime(true);

        // Tests execution time in minutes
        $executionTime = $endTime - $startTime;

        if ($executionTime >= 60) {
            $executionTime = round($executionTime / 60, 2) . " min";
        } else {
            $executionTime = round($executionTime, 2) . " sec";
        }

        if ($result == "html_report") {
            echo $this->unit->report(array(), $executionTime);
        } elseif ($result == "raw_data") {
            print_r($this->unit->result());
        } elseif ($result == "last_result") {
            print_r($this->unit->last_result());
        }
    }
}



