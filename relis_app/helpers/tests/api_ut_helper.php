<?php

// TEST ELEMENT CONTROLLER
class ApiUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "api";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->projectProtocol();
        $this->projectReport();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
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
     * Test 1
     * Action : protocol
     * Description : generate a protocol for a project providing information about the project's details, participants, research questions, papers, screening, QA, and data extraction.
     * Expected result : check if the data generated about the project are correct
     */
    private function projectProtocol()
    {
        $action = "protocol";
        $test_name = "generate a protocol for a project providing information about the project's details, participants, research questions, papers, screening, QA, and data extraction";
        $test_aspect = "Project protocol";
        $expected_value = '{"message":"OK","project_id":"demoTestProject","project_name":"Demo Test Project","project_description":"Demo Test Project","participant":{"user_id":"1","user_name":"Admin","user_role":"Reviewer"},"research_question":[],"papers_search":{"search_strategy":[],"papers_source":[{"value":"Google Scholar"},{"value":"Chocolate DB"}]},"screening":{"exclusion_criteria":[{"criteria":"EC1: Too short "},{"criteria":"EC2: Not abour chocolate"}],"inclusion_criteria":[],"0":{"phase_title":"Title","phase_description":"Screen by title","kappa":1,"result":{"Total":5,"Included":4,"Excluded":1,"In Review":0,"Pending":0}}},"qa":{"questions":[{"question":"Is the paper about chocolate?"},{"question":"Is the chocolate brand available?"},{"question":"Is there a validation of the methodology?"}],"responses":[{"response":"Yes","score":"3"},{"response":"Bearly","score":"1.5"},{"response":"No","score":"0"}],"papers":4},"data_extraction":{"papers":"2","schema":{"has_choco":{"title":"Has chocolate","multi_value":"No","category":"Boolean"},"temperature":{"title":"Temperature","multi_value":"No","category":"Number"},"start":{"title":"Start date","multi_value":"No","category":"Date"},"code":{"title":"Code","multi_value":"No","category":"Text"},"brand":{"title":"Brand","multi_value":"No","values":["Ferrero","Ghirardelli","Godiva","Hersheys","Leonidas","Lindt","Nestle"],"category":"List"},"cocoa_origin":{"title":"Cocoa origin","multi_value":"No","values":{"Cote dIvoire":"Cote dIvoire","Indonesia":"Indonesia","Ghana":"Ghana","Nigeria":"Nigeria","Cameroon":"Cameroon","Brazil":"Brazil","Ecuador":"Ecuador","Mexico":"Mexico","Dominican Republic":"Dominican Republic","Peru":"Peru"},"category":"List"},"cocoa_level":{"title":"Cocoa level","multi_value":"No","values":{"35%":"35%","40%":"40%","45%":"45%","50%":"50%","55%":"55%","60%":"60%","65%":"65%","70%":"70%","75%":"75%","80%":"80%","90%":"90%","95%":"95%","100%":"100%"},"category":"List"},"types":{"title":"Types","multi_value":"No","values":{"Raw":"Raw","Dark":"Dark","Milk":"Milk","White":"White","Baking":"Baking","Modeling":"Modeling","Organic":"Organic","Compound":"Compound","Couverture":"Couverture","Ruby":"Ruby"},"category":"List"},"variety":{"title":"Variety","multi_value":"Yes","category":"Subcategory","sub_categories":{"variety":{"title":"Variety","multi_value":"No","values":["Bitter","Bittersweet","Semi-sweet","Sweet"],"category":"List"},"level1":{"title":"Level 1","multi_value":"No","values":"cocoa_level","category":"ListDependant"},"level2":{"title":"Level 2","multi_value":"No","values":"cocoa_level","category":"ListDependant"}}},"venue":{"title":"Venue","multi_value":"No","category":"Text"},"year":{"title":"Year","multi_value":"No","category":"Number"},"citation":{"title":"Number of citations","multi_value":"No","category":"Number"},"note":{"title":"Note","multi_value":"No","category":"Text"}}}}';

        $json_data = file_get_contents('http://host.docker.internal:8083/' . $this->controller . '/' . $action . '/' . getProjectShortName());
        $actual_value = $json_data;

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 2
     * Action : report
     * Description : generate a report for a project, providing information about the project's schema and data
     * Expected result : check if the data generated about the project are correct
     */
    private function projectReport()
    {
        $action = "report";
        $test_name = "generate a report for a project, providing information about the project's schema and data";
        $test_aspect = "Project report";
        $expected_value = '{"schema":{"has_choco":{"title":"Has chocolate","multi_value":"No","category":"Boolean"},"temperature":{"title":"Temperature","multi_value":"No","category":"Number"},"start":{"title":"Start date","multi_value":"No","category":"Date"},"code":{"title":"Code","multi_value":"No","category":"Text"},"brand":{"title":"Brand","multi_value":"No","values":["Ferrero","Ghirardelli","Godiva","Hersheys","Leonidas","Lindt","Nestle"],"category":"List"},"cocoa_origin":{"title":"Cocoa origin","multi_value":"No","values":{"Cote dIvoire":"Cote dIvoire","Indonesia":"Indonesia","Ghana":"Ghana","Nigeria":"Nigeria","Cameroon":"Cameroon","Brazil":"Brazil","Ecuador":"Ecuador","Mexico":"Mexico","Dominican Republic":"Dominican Republic","Peru":"Peru"},"category":"List"},"cocoa_level":{"title":"Cocoa level","multi_value":"No","values":{"35%":"35%","40%":"40%","45%":"45%","50%":"50%","55%":"55%","60%":"60%","65%":"65%","70%":"70%","75%":"75%","80%":"80%","90%":"90%","95%":"95%","100%":"100%"},"category":"List"},"types":{"title":"Types","multi_value":"No","values":{"Raw":"Raw","Dark":"Dark","Milk":"Milk","White":"White","Baking":"Baking","Modeling":"Modeling","Organic":"Organic","Compound":"Compound","Couverture":"Couverture","Ruby":"Ruby"},"category":"List"},"variety":{"title":"Variety","multi_value":"Yes","category":"Subcategory","sub_categories":{"variety":{"title":"Variety","multi_value":"No","values":["Bitter","Bittersweet","Semi-sweet","Sweet"],"category":"List"},"level1":{"title":"Level 1","multi_value":"No","values":"cocoa_level","category":"ListDependant"},"level2":{"title":"Level 2","multi_value":"No","values":"cocoa_level","category":"ListDependant"}}},"venue":{"title":"Venue","multi_value":"No","category":"Text"},"year":{"title":"Year","multi_value":"No","category":"Number"},"citation":{"title":"Number of citations","multi_value":"No","category":"Number"},"note":{"title":"Note","multi_value":"No","category":"Text"}},"data":[["#","Paper","Has chocolate","Temperature","Start date","Code","Brand","Cocoa origin","Cocoa level","Types","Variety","Venue","Year","Number of citations","Note"]';

        $classificationData = $this->ci->db->query("SELECT * from relis_dev_correct_" . getProjectShortName() . ".classification")->result_array();

        foreach ($classificationData as $class) {
            $paper = $this->ci->db->query("SELECT bibtexKey, title from relis_dev_correct_" . getProjectShortName() . ".paper WHERE id =" . $class['class_paper_id'])->row_array();
            $expected_value = $expected_value . ',{"class_id":' . $class['class_id'] . ',"class_paper_id":"' . $paper['bibtexKey'] . ' - '. $paper['title'] . '","has_choco":"Yes","temperature":"","start":"2023-11-04","code":"AB9","brand":"Godiva","cocoa_origin":"Cote dIvoire","cocoa_level":"45%","types":"Milk","variety":[],"venue":"udem","year":"2017","citation":"7","note":"good"}';
        }

        $expected_value = $expected_value . ']}';

        $json_data = file_get_contents('http://host.docker.internal:8083/' . $this->controller . '/' . $action . '/' . getProjectShortName());
        $actual_value = $json_data;

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}
 


