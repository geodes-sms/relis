<?php

// TEST PAPER CONTROLLER
class PaperUnitTest
{
    private $controller;
    private $http_client;
    private $ci;
    private $csvFilePath;
    private $bibFilePath;

    function __construct()
    {
        $this->controller = "paper";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
        $this->csvFilePath = 'relis_app/helpers/tests/testFiles/paper/5_csvPapers.xls';
        $this->bibFilePath = 'relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib';
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->importPapers();
        $this->importEndNote();
        $this->importBibtext();
        $this->importPapersLoad_0CSV();
        $this->importPapersLoad_1CSV();
        $this->importPapersLoad_5CSV();
        $this->importPapersSave_0CSV();
        $this->importPapersSave_1CSV();
        $this->importPapersSave_5CSV();
        $this->importPapersLoadBibtext_0paper();
        $this->importPapersLoadBibtext_1paper();
        $this->importPapersLoadBibtext_5papers();
        $this->importPapersSaveBibtext_0papers();
        $this->importPapersSaveBibtext_1paper();
        $this->importPapersSaveBibtext_5papers();
        $this->importPapersSaveBibtext_withExistingPapers();
        $this->addPaperBibtex();
        $this->saveBibtexPaper_noTitle();
        $this->saveBibtexPaper();
        $this->saveBibtexPaper_existingPaper();
        $this->clearPapersValidation();
        $this->clearPapersTemp();
        $this->cancelClearPapers();
        $this->biblerAddPaper();
        $this->listPaper();
        $this->viewPaper();
        $this->displayPaperMin();
        $this->clearPapers();
    }

    private function TestInitialize()
    {
        //delete created test session files
        deleteSessionFiles();
        //delete created test project
        deleteCreatedTestProject();
        //Login
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        //create demoProject
        createDemoProject();
    }

    /*
     * Test 1
     * Action : import_papers
     * Description : display form for importing papers from a CSV file.
     */
    private function importPapers()
    {
        $action = "import_papers";
        $test_name = "Display form for importing papers from a CSV file";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 2
     * Action : import_bibtext
     * Description : display form for importing bibliographic data from EndNote files
     */
    private function importEndNote()
    {
        $action = "import_bibtext";
        $test_name = "Display form for importing bibliographic data from EndNote files";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/endnote");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
     * Action : import_bibtext
     * Description : display form for importing bibliographic data from bibtex files
     */
    private function importBibtext()
    {
        $action = "import_bibtext";
        $test_name = "Display form for importing bibliographic data from bibtex files";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/bibtex");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Action : import_papers_load_csv
     * Description : Test the loading and processing of 0 CSV file for importing papers
     */
    private function importPapersLoad_0CSV()
    {
        $action = "import_papers_load_csv";
        $test_name = "Test the loading and processing of 0 paper from CSV file";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $csvFilePath = "relis_app/helpers/tests/testFiles/paper/0_csvPaper.xls";
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'paper_file', 'filePath' => $csvFilePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_value);
    }

    /*
     * Test 5
     * Action : import_papers_load_csv
     * Description : Test the loading and processing of 1 CSV file for importing papers
     */
    private function importPapersLoad_1CSV()
    {
        $action = "import_papers_load_csv";
        $test_name = "Test the loading and processing of 1 paper from CSV file";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $csvFilePath = "relis_app/helpers/tests/testFiles/paper/1_csvPaper.xls";
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'paper_file', 'filePath' => $csvFilePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_value);
    }

    /*
     * Test 6
     * Action : import_papers_load_csv
     * Description : Test the loading and processing of 5 CSV papers
     */
    private function importPapersLoad_5CSV()
    {
        $action = "import_papers_load_csv";
        $test_name = "Test the loading and processing of 5 papers from CSV file";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'paper_file', 'filePath' => $this->csvFilePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_value);
    }

    /*
     * Test 7
     * Action : import_papers_save_csv
     * Description : inserting 0 loaded csv paper into the database
     * Expected papers inserted in DB : 0
     */
    private function importPapersSave_0CSV()
    {
        $action = "import_papers_save_csv";
        $test_name = "Inserting 0 loaded csv papers into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count']);

        $paperData = getCSVdata('relis_app/helpers/tests/testFiles/paper/0_csvPaper.xls');
        $response = $this->http_client->response($this->controller, $action, [
            "paper_title" => "4",
            "bibtexKey" => "1",
            "paper_link" => "3",
            "year" => "",
            "paper_abstract" => "",
            "bibtex" => "",
            "paper_key" => "",
            "paper_author" => "",
            "data_array" => $paperData,
            "paper_start_from" => "2",
            "papers_sources" => ""
        ], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 8
     * Action : import_papers_save_csv
     * Description : inserting 1 loaded csv paper into the database
     * Expected papers inserted in DB : 1
     */
    private function importPapersSave_1CSV()
    {
        $action = "import_papers_save_csv";
        $test_name = "Inserting 1 loaded csv papers into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'] + 1);

        $paperData = getCSVdata('relis_app/helpers/tests/testFiles/paper/1_csvPaper.xls');
        $response = $this->http_client->response($this->controller, $action, [
            "paper_title" => "4",
            "bibtexKey" => "1",
            "paper_link" => "3",
            "year" => "",
            "paper_abstract" => "",
            "bibtex" => "",
            "paper_key" => "",
            "paper_author" => "",
            "data_array" => $paperData,
            "paper_start_from" => "2",
            "papers_sources" => ""
        ], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 9
     * Action : import_papers_save_csv
     * Description : inserting the 5 loaded csv papers into the database
     * Expected papers inserted in DB : 5
     */
    private function importPapersSave_5CSV()
    {
        $action = "import_papers_save_csv";
        $test_name = "Inserting the 5 loaded csv papers into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'] + 5);

        $paperData = getCSVdata($this->csvFilePath);
        $response = $this->http_client->response($this->controller, $action, [
            "paper_title" => "4",
            "bibtexKey" => "1",
            "paper_link" => "3",
            "year" => "",
            "paper_abstract" => "",
            "bibtex" => "",
            "paper_key" => "",
            "paper_author" => "",
            "data_array" => $paperData,
            "paper_start_from" => "2",
            "papers_sources" => ""
        ], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 10
     * Action : import_papers_load_bibtext
     * Description : Test the loading and processing of bibtext files with 0 paper
     */
    private function importPapersLoadBibtext_0paper()
    {
        $action = "import_papers_load_bibtext";
        $test_name = "Test the loading and processing of bibtext files with 0 paper";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $bibFilePath = 'relis_app/helpers/tests/testFiles/paper/0_bibPaper.bib';
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'paper_file', 'filePath' => $bibFilePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_value);
    }

    /*
     * Test 11
     * Action : import_papers_load_bibtext
     * Description : Test the loading and processing of bibtext files with 1 paper
     */
    private function importPapersLoadBibtext_1paper()
    {
        $action = "import_papers_load_bibtext";
        $test_name = "Test the loading and processing of bibtext files with 1 paper";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $bibFilePath = 'relis_app/helpers/tests/testFiles/paper/1_bibPaper.bib';
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'paper_file', 'filePath' => $bibFilePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_value);
    }

    /*
     * Test 12
     * Action : import_papers_load_bibtext
     * Description : Test the loading and processing of bibtext files with 5 papers
     */
    private function importPapersLoadBibtext_5papers()
    {
        $action = "import_papers_load_bibtext";
        $test_name = "Test the loading and processing of bibtext files with 5 papers";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'paper_file', 'filePath' => $this->bibFilePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_value);
    }

    /*
     * Test 13
     * Action : import_papers_save_bibtext
     * Description : Inserting the loaded bibtext papers with 0 paper into the database
     * Expected papers inserted in DB : 0
     */
    private function importPapersSaveBibtext_0papers()
    {
        $action = "import_papers_save_bibtext";
        $test_name = "Inserting the loaded bibtext papers with 0 paper into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count']);

        $bibFilePath = 'relis_app/helpers/tests/testFiles/paper/0_bibPaper.bib';
        $paperData = getBibtextData($bibFilePath);
        $response = $this->http_client->response($this->controller, $action, ["data_array" => $paperData, "papers_sources" => ""], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 14
     * Action : import_papers_save_bibtext
     * Description : Inserting the loaded bibtext papers with 1 paper into the database
     * Expected papers inserted in DB : 1
     */
    private function importPapersSaveBibtext_1paper()
    {
        $action = "import_papers_save_bibtext";
        $test_name = "Inserting the loaded bibtext papers with 1 paper into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'] + 1);

        $paperData = getBibtextData('relis_app/helpers/tests/testFiles/paper/1_bibPaper.bib');
        $response = $this->http_client->response($this->controller, $action, ["data_array" => $paperData, "papers_sources" => ""], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 15
     * Action : import_papers_save_bibtext
     * Description : inserting the loaded bibtext papers into the database
     * Expected papers inserted in DB : 5
     */
    private function importPapersSaveBibtext_5papers()
    {
        $this->TestInitialize();

        $action = "import_papers_save_bibtext";
        $test_name = "Inserting the loaded bibtext with 5 papers into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'] + 5);

        $paperData = getBibtextData($this->bibFilePath);
        $response = $this->http_client->response($this->controller, $action, ["data_array" => $paperData, "papers_sources" => ""], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 16
     * Action : import_papers_save_bibtext
     * Description : inserting existing bibtext papers into the database
     * Expected papers inserted in DB : No papers should be inserted into DB
     */
    private function importPapersSaveBibtext_withExistingPapers()
    {
        $action = "import_papers_save_bibtext";
        $test_name = "Inserting existing bibtext papers into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];

        $paperData = getBibtextData($this->bibFilePath);
        $response = $this->http_client->response($this->controller, $action, ["data_array" => $paperData, "papers_sources" => ""], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 17
     * Action : add_paper_bibtex
     * Description : display page for adding a bibtext paper.
     */
    private function addPaperBibtex()
    {
        $action = "add_paper_bibtex";
        $test_name = "Display page for adding a bibtext paper";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 18
     * Action : save_bibtex_paper
     * Description : handles the saving of a paper from a BibTeX entry without title
     * Expected paper inserted in DB : No papers should be inserted into DB
     */
    private function saveBibtexPaper_noTitle()
    {
        $action = "save_bibtex_paper";
        $test_name = "Handles the saving of a paper from a BibTeX entry without title";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];

        $bibtextKey = "Barbierato2016";
        $autour = "Barbierato, Enrico and Gribaudo, Marco and Iacono, Mauro";
        $journal = "Electronic Notes in Theoretical Computer Science";
        $title = "";
        $year = "2016";
        $pages = "5--25";
        $volume = "327";
        $abstract = "Hybrid systems (HS) have been proven a valid formalism to study and analyze specific issues in a variety of fields. However, most of the analysis techniques for HS are based on low-level description, where single states of the systems have to be defined and enumerated by the modeler. Some high level modeling formalisms, such as Fluid Stochastic Petri Nets, have been introduced to overcome such difficulties, but simple procedures allowing the definitions of domain specific languages for HS could simplify the analysis of such systems. This paper presents a stochastic HS language consisting of a subset of piecewise deterministic Markov processes, and shows how SIMTHESys \endash a compositional, metamodeling based framework describing and extending formalisms \endash can be used to convert into this paradigm a wide number of high-level HS description languages. A simple example applying the technique to solve a model of the energy consumption of a data-center specified using Queuing Network and Hybrid Petri Nets is presented to show the effectiveness of the proposal.";
        $doi = "10.1016/j.entcs.2016.09.021";
        $paper = "https://dx.doi.org/10.1016/j.entcs.2016.09.021";

        $bibtextData = "@ARTICLE{" . $bibtextKey . ",
            author = {" . $autour . "},
            journal = {" . $journal . "},
            title = {" . $title . "},
            year = {" . $year . "},
            pages = {" . $pages . "},
            volume = {" . $volume . "},
            abstract = {" . $abstract . "},
            doi = {" . $doi . "},
            paper = {" . $paper . "}
          }";

        $response = $this->http_client->response($this->controller, $action, ["bibtext" => $bibtextData], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 19
     * Action : save_bibtex_paper
     * Description : handles the saving of a paper from a BibTeX entry
     * Expected paper inserted in DB
     */
    private function saveBibtexPaper()
    {
        $action = "save_bibtex_paper";
        $test_name = "Handles the saving of a paper from a BibTeX entry";
        $test_aspect_papersInDB = "Paper inserted in DB";

        $bibtextKey = "Barbierato2016";
        $autour = "Barbierato, Enrico and Gribaudo, Marco and Iacono, Mauro";
        $journal = "Electronic Notes in Theoretical Computer Science";
        $title = "Modeling Hybrid Systems in SIMTHESys";
        $year = "2016";
        $pages = "5--25";
        $volume = "327";
        $abstract = "Hybrid systems (HS) have been proven a valid formalism to study and analyze specific issues in a variety of fields. However, most of the analysis techniques for HS are based on low-level description, where single states of the systems have to be defined and enumerated by the modeler. Some high level modeling formalisms, such as Fluid Stochastic Petri Nets, have been introduced to overcome such difficulties, but simple procedures allowing the definitions of domain specific languages for HS could simplify the analysis of such systems. This paper presents a stochastic HS language consisting of a subset of piecewise deterministic Markov processes, and shows how SIMTHESys \endash a compositional, metamodeling based framework describing and extending formalisms \endash can be used to convert into this paradigm a wide number of high-level HS description languages. A simple example applying the technique to solve a model of the energy consumption of a data-center specified using Queuing Network and Hybrid Petri Nets is presented to show the effectiveness of the proposal.";
        $doi = "10.1016/j.entcs.2016.09.021";
        $paper = "https://dx.doi.org/10.1016/j.entcs.2016.09.021";

        $bibtextData = "@ARTICLE{" . $bibtextKey . ",
            author = {" . $autour . "},
            journal = {" . $journal . "},
            title = {" . $title . "},
            year = {" . $year . "},
            pages = {" . $pages . "},
            volume = {" . $volume . "},
            abstract = {" . $abstract . "},
            doi = {" . $doi . "},
            paper = {" . $paper . "}
          }";

        $paper = ["bibtexKey" => $bibtextKey, "title" => $title];
        $expected_PaperInDB = json_encode($paper);

        $response = $this->http_client->response($this->controller, $action, ["bibtext" => $bibtextData], "POST");

        if ($response['status_code'] >= 400) {
            $actual_PaperInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $paper_data = $this->ci->db->query("SELECT bibtexKey, title FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE bibtexKey = '" . $bibtextKey . "'")->row_array();
            $actual_PaperInDB = json_encode($paper_data);
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_PaperInDB, $actual_PaperInDB);
    }

    /*
     * Test 20
     * Action : save_bibtex_paper
     * Description : handles the saving of a existing paper from a BibTeX entry
     * Expected paper inserted in DB : No papers should be inserted into DB
     */
    private function saveBibtexPaper_existingPaper()
    {
        $action = "save_bibtex_paper";
        $test_name = "Handles the saving of a existing paper from a BibTeX entry";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];

        $bibtextKey = "Barbierato2016";
        $author = "Barbierato, Enrico and Gribaudo, Marco and Iacono, Mauro";
        $journal = "Electronic Notes in Theoretical Computer Science";
        $title = "Modeling Hybrid Systems in SIMTHESys";
        $year = "2016";
        $pages = "5--25";
        $volume = "327";
        $abstract = "Hybrid systems (HS) have been proven a valid formalism to study and analyze specific issues in a variety of fields. However, most of the analysis techniques for HS are based on low-level description, where single states of the systems have to be defined and enumerated by the modeler. Some high level modeling formalisms, such as Fluid Stochastic Petri Nets, have been introduced to overcome such difficulties, but simple procedures allowing the definitions of domain specific languages for HS could simplify the analysis of such systems. This paper presents a stochastic HS language consisting of a subset of piecewise deterministic Markov processes, and shows how SIMTHESys \endash a compositional, metamodeling based framework describing and extending formalisms \endash can be used to convert into this paradigm a wide number of high-level HS description languages. A simple example applying the technique to solve a model of the energy consumption of a data-center specified using Queuing Network and Hybrid Petri Nets is presented to show the effectiveness of the proposal.";
        $doi = "10.1016/j.entcs.2016.09.021";
        $paper = "https://dx.doi.org/10.1016/j.entcs.2016.09.021";

        $bibtextData = "@ARTICLE{" . $bibtextKey . ",
            author = {" . $author . "},
            journal = {" . $journal . "},
            title = {" . $title . "},
            year = {" . $year . "},
            pages = {" . $pages . "},
            volume = {" . $volume . "},
            abstract = {" . $abstract . "},
            doi = {" . $doi . "},
            paper = {" . $paper . "}
          }";

        $response = $this->http_client->response($this->controller, $action, ["bibtext" => $bibtextData], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 21
     * Action : clear_papers_validation
     * Description : Confirmation page to delete all papers.
     */
    private function clearPapersValidation()
    {
        $action = "clear_papers_validation";
        $test_name = "Confirmation page to delete all papers";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 22
     * Action : clear_papers_temp
     * Description : Marking all papers as inactive rather than permanently deleting them from the database.
     * Expected DB Update
     */
    private function clearPapersTemp()
    {
        $action = "clear_papers_temp";
        $test_name = "Marking all papers as inactive rather than permanently deleting them from the database";
        $test_aspect_update = "DB Update";
        $expected_update = "DB updated";
        $actual_update = "DB not updated";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $sql_paper = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE paper_active=1")->row_array();
            $sql_paperAuthor = $this->ci->db->query("SELECT paperauthor_id FROM relis_dev_correct_" . getProjectShortName() . ".paperauthor WHERE paperauthor_active=1")->row_array();

            if (empty($sql_paper) && empty($sql_paperAuthor)) {
                $actual_update = "DB updated";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect_update, $expected_update, $actual_update);
    }

    /*
     * Test 23
     * Action : cancel_clear_papers
     * Description : Restore the previously marked inactive records.
     * Expected DB Restored
     */
    private function cancelClearPapers()
    {
        $action = "cancel_clear_papers";
        $test_name = "Restore the previously marked inactive records";
        $test_aspect_restore = "DB Restored";
        $expected_restore = "DB restored";
        $actual_restore = "DB not restored";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $sql_paper = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE paper_active=3")->row_array();
            $sql_paperAuthor = $this->ci->db->query("SELECT paperauthor_id FROM relis_dev_correct_" . getProjectShortName() . ".paperauthor WHERE paperauthor_active=3")->row_array();

            if (empty($sql_paper) && empty($sql_paperAuthor)) {
                $actual_restore = "DB restored";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect_restore, $expected_restore, $actual_restore);
    }

    /*
     * Test 24
     * Action : bibler_add_paper
     * Description : Rendering the form for adding or editing a bibtex paper
     */
    private function biblerAddPaper()
    {
        $action = "bibler_add_paper";
        $test_name = "Rendering the form for adding or editing a bibtex paper";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 25
     * Action : list_paper
     * Description : Display the list of papers
     */
    private function listPaper()
    {
        $action = "list_paper";
        $test_name = "Display the list of papers";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        //$paperID = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper ORDER BY id DESC LIMIT 1")->row_array()['id'];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 26
     * Action : view_paper
     * Description : Displaying the paper details
     */
    private function viewPaper()
    {
        $action = "view_paper";
        $test_name = "Displaying the paper details";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[307];

        $paperID = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper ORDER BY id DESC LIMIT 1")->row_array()['id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $paperID);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 27
     * Action : display_paper_min
     * Description : Displaying a minimal version of the paper details
     */
    private function displayPaperMin()
    {
        $action = "display_paper_min";
        $test_name = "Displaying a minimal version of the paper details";
        $test_aspect_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $paperID = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper ORDER BY id DESC LIMIT 1")->row_array()['id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $paperID);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 28
     * Action : clear_papers
     * Description : Permanently deleting papers and related data from the database.
     * Expected papers Deletion
     */
    private function clearPapers()
    {
        $action = "clear_papers";
        $test_name = "Permanently deleting papers and related data from the database";
        $test_aspect_deletion = "Papers Deletion";
        $expected_deletion = "Papers deleted";
        $actual_deletion = "Papers not deleted";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_deletion = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $nbrOfPaper = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
            $nbrOfAuthor = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".author")->row_array()['row_count'];
            $nbrOfVenue = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".venue")->row_array()['row_count'];
            $nbrOfPaperauthor = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paperauthor")->row_array()['row_count'];
            $nbrOfAssigned = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned")->row_array()['row_count'];
            $nbrOfClassification = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".classification")->row_array()['row_count'];
            $nbrOfExclusion = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".exclusion")->row_array()['row_count'];
            $nbrOfQa_assignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment")->row_array()['row_count'];
            $nbrOfQa_result = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_result")->row_array()['row_count'];
            $nbrOfQa_validation_assignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment")->row_array()['row_count'];
            $nbrOfScreening_paper = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper")->row_array()['row_count'];
            $nbrOfScreen_decison = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screen_decison")->row_array()['row_count'];

            if ($nbrOfPaper == 0 && $nbrOfAuthor == 0 && $nbrOfVenue == 0 && $nbrOfPaperauthor == 0 && $nbrOfAssigned == 0 && $nbrOfClassification == 0 && $nbrOfExclusion == 0 && $nbrOfQa_assignment == 0 && $nbrOfQa_result == 0 && $nbrOfQa_validation_assignment == 0 && $nbrOfScreening_paper == 0 && $nbrOfScreen_decison == 0) {
                $actual_deletion = "Papers deleted";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect_deletion, $expected_deletion, $actual_deletion);
    }
}