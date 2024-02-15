<?php

// TEST REPORTING CONTROLLER
class ReportingUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "reporting";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->resultGraph();
        $this->resultExport();
        $this->download();
        $this->result_export_classification();
        $this->result_export_excluded_class();
        $this->result_export_papers();
        $this->result_export_papers_bib();
        $this->result_export_papers_bib_included();
        $this->result_export_papers_bib_excluded();
        $this->result_export_excluded_screen();
        $this->r_export_configurations();
        $this->result_r_config_file();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
        //delete generated reporting files
        deleteReportingFiles();
        //delete created test user
        deleteCreatedTestUser();
        //delete created demoProject
        deleteCreatedTestProject();
        //create test user
        addTestUser();
        //Login as admin
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        //create demoProject
        createDemoProject();
        //add users to test Project
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(), "Reviewer");
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening with 4 included papers
        assignPapers_and_performScreening([getAdminUserId(), getTestUserId()], 'Title', -1, 4);
        // //perform QA with 2 low quality papers (1 for each user)
        assignPapers_and_performQA([getAdminUserId(), getTestUserId()], -1, 1);
        // //Exclude low quality papers
        qaExcludeLowQuality();
        //perform classification
        assignPapersForClassification([getAdminUserId(), getTestUserId()]);
        performClassification();
    }

    /*
     * Action : result_export
     * Description : display the export options to download the result data
     * Expected HTTP Response Code : 200 OK
     */
    private function resultExport()
    {
        $action = "result_export";
        $test_name = "Display the export options to download the result data";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Action : download
     * Description : enable the user to download the specified file
     * Expected HTTP Response Code : 200 OK
     */
    private function download()
    {
        $action = "download";
        $test_name = "Enable the user to download the specified file";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/index.html");

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Action : result_export_excluded_class
     * Description : exporting the excluded papers with their classification information to a CSV file
     * Expected generated reporting file: check if the reproting file is generated
     */
    private function result_export_excluded_class()
    {
        $action = "result_export_excluded_class";
        $test_name = "exporting the excluded papers with their classification information to a CSV file";
        $test_generated_file = "Generated file";
        $expected_generated_file = "relis_paper_excluded_class_demoTestProject.csv";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_generated_file = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (file_get_contents('cside/export_r/relis_paper_excluded_class_demoTestProject.csv') == file_get_contents('relis_app/helpers/tests/testFiles/reporting/get_relis_paper_excluded_class.csv')) {
                $actual_generated_file = "relis_paper_excluded_class_demoTestProject.csv";
            } else {
                $actual_generated_file = "File not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_file, $expected_generated_file, $actual_generated_file);
    }

    /*
     * Action : result_export_papers
     * Description : exporting the necessary data about the papers to a CSV file
     * Expected generated reporting file: check if the reproting file is generated
     */
    private function result_export_papers()
    {
        $action = "result_export_papers";
        $test_name = "Exporting the necessary data about the papers to a CSV file";
        $test_generated_file = "Generated file";
        $expected_generated_file = "relis_paper_demoTestProject.csv";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_generated_file = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (file_get_contents('cside/export_r/relis_paper_demoTestProject.csv') == file_get_contents('relis_app/helpers/tests/testFiles/reporting/get_relis_paper.csv')) {
                $actual_generated_file = "relis_paper_demoTestProject.csv";
            } else {
                $actual_generated_file = "File not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_file, $expected_generated_file, $actual_generated_file);
    }

    /*
     * Action : result_export_papers_bib
     * Description : exporting the BibTeX information of papers.
     * Expected generated reporting file: check if the reproting file is generated
     */
    private function result_export_papers_bib()
    {
        $action = "result_export_papers_bib";
        $test_name = "Exporting the BibTeX information of papers";
        $test_generated_file = "Generated file";
        $expected_generated_file = "relis_paper_bibtex_demoTestProject.bib";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_generated_file = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (file_get_contents('cside/export_r/relis_paper_bibtex_demoTestProject.bib') == file_get_contents('relis_app/helpers/tests/testFiles/reporting/get_relis_paper_bibtex.bib')) {
                $actual_generated_file = "relis_paper_bibtex_demoTestProject.bib";
            } else {
                $actual_generated_file = "File not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_file, $expected_generated_file, $actual_generated_file);
    }

    /*
     * Action : result_export_included_papers_bib
     * Description : exporting the BibTeX information of included papers.
     * Expected generated reporting file: check if the reproting file is generated
     */
    private function result_export_papers_bib_included()
    {
        $action = "result_export_included_papers_bib";
        $test_name = "Exporting the BibTeX information of included papers";
        $test_generated_file = "Generated file";
        $expected_generated_file = "relis_paper_bibtex_Included_demoTestProject.bib";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_generated_file = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (file_exists("cside/export_r/relis_paper_bibtex_Included_demoTestProject.bib")) {
                $actual_generated_file = "relis_paper_bibtex_Included_demoTestProject.bib";
            } else {
                $actual_generated_file = "File not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_file, $expected_generated_file, $actual_generated_file);
    }

    /*
     * Action : result_export_excluded_papers_bib
     * Description : exporting the BibTeX information of excluded papers
     * Expected generated reporting file: check if the reproting file is generated
     */
    private function result_export_papers_bib_excluded()
    {
        $action = "result_export_excluded_papers_bib";
        $test_name = "Exporting the BibTeX information of excluded papers";
        $test_generated_file = "Generated file";
        $expected_generated_file = "relis_paper_bibtex_Excluded_demoTestProject.bib";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_generated_file = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (file_get_contents('cside/export_r/relis_paper_bibtex_Excluded_demoTestProject.bib') == file_get_contents('relis_app/helpers/tests/testFiles/reporting/get_relis_paper_bibtex_Excluded.bib')) {
                $actual_generated_file = "relis_paper_bibtex_Excluded_demoTestProject.bib";
            } else {
                $actual_generated_file = "File not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_file, $expected_generated_file, $actual_generated_file);
    }

    /*
     * Action : result_export_excluded_screen
     * Description : exports the information of papers that have been excluded during the screening process.
     * Expected generated reporting file: check if the reproting file is generated
     */
    private function result_export_excluded_screen()
    {
        $action = "result_export_excluded_screen";
        $test_name = "Exports the information of papers that have been excluded during the screening process";
        $test_generated_file = "Generated file";
        $expected_generated_file = "relis_paper_excluded_screen_demoTestProject.csv";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_generated_file = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (file_get_contents('cside/export_r/relis_paper_excluded_screen_demoTestProject.csv') == file_get_contents('relis_app/helpers/tests/testFiles/reporting/get_relis_paper_excluded_screen.csv')) {
                $actual_generated_file = "relis_paper_excluded_screen_demoTestProject.csv";
            } else {
                $actual_generated_file = "File not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_file, $expected_generated_file, $actual_generated_file);
    }

    /*
     * Action : r_export_configurations
     * Description : Display the form to export r configurations
     * Expected result : check if all categories are presents
     */
    private function r_export_configurations()
    {
        $action = "r_export_configurations";
        $test_name = "Display the form to export r configurations";
        $test_aspect = "All categories present?";
        $expected_result = "yes";
        $actual_result = "no";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                strstr($response['content'], "Has chocolate") != false &&
                strstr($response['content'], "Temperature") != false &&
                strstr($response['content'], "Start date") != false &&
                strstr($response['content'], "Code") != false &&
                strstr($response['content'], "Brand") != false &&
                strstr($response['content'], "Cocoa origin") != false &&
                strstr($response['content'], "Cocoa level") != false &&
                strstr($response['content'], "Types") != false &&
                strstr($response['content'], "Variety") != false &&
                strstr($response['content'], "Venue") != false &&
                strstr($response['content'], "Year") != false &&
                strstr($response['content'], "Number of citations") != false &&
                strstr($response['content'], "Note") != false
            ) {
                $actual_result = "yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_result, $actual_result);
    }

    /*
     * Action : result_r_config_file
     * Description : Export r configuration files
     * Expected generated reporting files: check if the reproting files are generated
     */
    private function result_r_config_file()
    {
        $action = "result_r_config_file";
        $test_name = "Export r configuration files";
        $test_generated_files = "Generated files";
        $expected_generated_files = "relis_r_config_demoTestProject.R, relis_r_lib_demoTestProject.R";

        $postData = [
            'Has chocolate' => 'Nominal',
            'Temperature' => 'Continuous',
            'Start date' => 'Text',
            'Code' => 'Text',
            'Brand' => 'Nominal',
            'Cocoa origin' => 'Nominal',
            'Cocoa level' => 'Nominal',
            'Types' => 'Nominal',
            'Variety' => 'Nominal',
            'Venue' => 'Text',
            'Year' => 'Continuous',
            'Number of citations' => 'Continuous',
            'Note' => 'Text'
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_generated_files = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                file_get_contents('cside/export_r/relis_r_config_demoTestProject.R') == file_get_contents('relis_app/helpers/tests/testFiles/reporting/get_relis_r_config.R') &&
                file_get_contents('cside/export_r/relis_r_lib_demoTestProject.R') == file_get_contents('relis_app/helpers/tests/testFiles/reporting/get_relis_r_lib.R')
            ) {
                $actual_generated_files = "relis_r_config_demoTestProject.R, relis_r_lib_demoTestProject.R";
            } else {
                $actual_generated_files = "Files not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_files, $expected_generated_files, $actual_generated_files);
    }

    /*
     * Action : result_graph
     * Description : Generate a graph displaying the result of a paper classification.
     * Expected HTTP Response Code : 200 OK
     */
    private function resultGraph()
    {
        $action = "result_graph";
        $test_name = "Generate a graph displaying the result of a paper classification";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Action : result_export_classification
     * Description : Retrieves the classification data and export them to a csv file
     * Expected generated reporting file: check if the reproting file is generated
     */
    private function result_export_classification()
    {
        $action = "result_export_classification";
        $test_name = "Retrieves the classification data and export them to a csv file";
        $test_generated_file = "Generated file";
        $expected_generated_file = "relis_classification_demoTestProject.csv";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_generated_file = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (file_exists("cside/export_r/relis_classification_demoTestProject.csv")) {
                $actual_generated_file = "relis_classification_demoTestProject.csv";
            } else {
                $actual_generated_file = "File not generated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_generated_file, $expected_generated_file, $actual_generated_file);
    }
}

