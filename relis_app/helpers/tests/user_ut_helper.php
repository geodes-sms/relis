<?php

// TEST USER CONTROLLER
class UserUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "user";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->descriptionPage();
        $this->helpPage();
        $this->helpDetPage();
        $this->newUserPage_userNotConnected();
        $this->newUserPage_userAlreadyConnected();
        $this->newUserFormWithEmptyFields();
        $this->newUserFormWithInvalidEmail();
        $this->newUserFormWithUnmatchedPasswordAndConfirmPassword();
        $this->newUserFormWithRecaptchaNotChecked();
        $this->newUserFormWithUsedUsername();
        $this->newUserFormWithcorrectFieldData();
        $this->accountValidationPage();
        $this->accountValidationFormWithEmptyValidationCode();
        $this->accountValidationFormWithWrongValidationCode();
        $this->accountValidationFormWithCorrectValidationCode();
        $this->loginPage_userNotConnected();
        $this->loginPageURL_userAlreadyConnected();
        $this->submitLogin_allFieldsEmpty();
        $this->submitLogin_EmptyUsername();
        $this->submitLogin_EmptyPassword();
        $this->submitLogin_wrongPassword();
        $this->submitLogin_wrongUsername();
        $this->submitLogin_nonValidatedAccount();
        $this->submitLogin_correctUsernameAndPassword();
        $this->loginAsDemoUser();
        $this->logout();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
        //delete created test user
        deleteCreatedTestUser();
    }

    /*
     * Test 1
     * Controller : User
     * Action : index
     * Description : This test verifies the behavior of the 'index' action.
     * Scenario : When the user navigate to "user/index" url they should be taken to the ReLiS description page.
     */
    private function descriptionPage()
    {
        $action = "index";
        $test_name = "Test description page";
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
     * Controller : User
     * Action : user_help
     * Scenario : When the user navigate to "user/user_help" url they should be taken to the ReLiS help page (Getting Started page).
     */
    private function helpPage()
    {
        $action = "user_help";
        $test_name = "Test user help page";
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
     * Test 3
     * Controller : User
     * Action : help_det
     * Scenario : When the user navigate to "user/help_det/[number]" url they should be taken to the ReLiS tutorial page for a specific relis phase.
     */
    private function helpDetPage()
    {
        $action = "help_det";
        $test_name = "Test tutorial page";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . '/3');

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Controller : User
     * Action : new_user
     * Description : This test verifies the behavior of the 'new_user' action (Navigate to new user form page) when a user is not already logged in (there is no open user session).
     * Scenario : When the user is not logged in and clicks the 'create user' link, the user should be directed to the 'new_user' HTML form to create a new user.
     */
    private function newUserPage_userNotConnected()
    {
        $this->http_client->unsetCookie('relis_session');
        $action = "new_user";
        $test_name = "Go to new user form page when user is not already logged in";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
        $this->http_client->unsetCookie('relis_session');
    }

    /*
     * Test 5
     * Controller : User
     * Action : new_user
     * Description : This test verifies the behavior of the 'new_user' action (Navigate to new user form page) when a user is already logged in (there is an open user session).
     * Scenario : When the user is already logged in and clicks the 'create user' link (Navigate to new user form page), the user should be automatically logged in.
     */
    private function newUserPage_userAlreadyConnected()
    {
        $this->http_client->unsetCookie('relis_session');
        $action = "new_user";
        $test_name = "Go to new user form page when user is already logged in";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];

        //Login first
        $this->http_client->response($this->controller, "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        //Navigate to new user form page
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
        $this->http_client->unsetCookie('relis_session');
    }

    /*
     * Test 6
     * Controller : User
     * Action : check_create_user
     * Description : Submit new user form while all the form fields are empty.
     * Scenario : When the user submit the new user form, No data shoud be inserted into the users table
     * Expected users table last ID: the users table last user ID should be the same before and after the test
     */
    private function newUserFormWithEmptyFields()
    {
        $action = "check_create_user";
        $test_name = "Submit new user form while all the form fields are empty";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];

        $response = $this->http_client->response($this->controller, $action, ['user_name' => '', 'user_mail' => '', 'user_username' => '', 'user_password' => '', 'user_password_validate' => ''], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 7
     * Controller : User
     * Action : check_create_user
     * Description : Submit new user form while all the form fields are correctly filed but the email field is not valid.
     * Scenario : When the user submit the new user form, No data shoud be inserted into the users table
     * Expected users table last ID: the users table last user ID should be the same before and after the test
     */
    private function newUserFormWithInvalidEmail()
    {
        $action = "check_create_user";
        $test_name = "Submit new user form while all the form fields are correctly filed but the email field is not valid";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];

        $response = $this->http_client->response($this->controller, $action, ['user_name' => 'christian', 'user_mail' => '123gmai.com', 'user_username' => 'Malakani', 'user_password' => '123', 'user_password_validate' => '123'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 8
     * Controller : User
     * Action : check_create_user
     * Description : Submit new user form while all the form fields are correctly filed but the validate password field doesn't match the password field.
     * Scenario : When the user submit the new user form, No data shoud be inserted into the users table
     * Expected users table last id: the users table last user ID should be the same before and after the test
     */
    private function newUserFormWithUnmatchedPasswordAndConfirmPassword()
    {
        $action = "check_create_user";
        $test_name = "Submit new user form while all the form fields are correctly filed but the validate password field doesn't match the password field";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];

        $response = $this->http_client->response($this->controller, $action, ['user_name' => 'christian', 'user_mail' => '123@gmai.com', 'user_username' => 'Malakani', 'user_password' => '123', 'user_password_validate' => '12'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 9
     * Controller : User
     * Action : check_create_user
     * Description : Submit new user form while all the form fields are correctly filed but the reCAPTCHA is not checked.
     * Scenario : When the user submit the new user form, No data shoud be inserted into the users table
     * Expected users table last id: the users table last user ID should be the same before and after the test
     */
    private function newUserFormWithRecaptchaNotChecked()
    {
        $action = "check_create_user";
        $test_name = "Submit new user form while all the form fields are correctly filed but the reCAPTCHA is not checked";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];
        
        $response = $this->http_client->response($this->controller, $action, ['user_name' => 'christian', 'user_mail' => '123@gmai.com', 'user_username' => 'Malakani', 'user_password' => '123', 'user_password_validate' => '123'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 10
     * Controller : User
     * Action : check_create_user
     * Description : Submit new user form while all the form fields are correctly filed, the reCAPTCHA is checked, but the Username is already used.
     * Scenario : When the user submit the new user form, No data shoud be inserted into the users table
     * Expected users table last id: the users table last user ID should be the same before and after the test
     */
    private function newUserFormWithUsedUsername()
    {
        $action = "check_create_user";
        $test_name = "Submit new user form while all the form fields are correctly filed, the reCAPTCHA is checked, but the Username is already used";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];
        $response = $this->http_client->response($this->controller, $action, ['user_name' => 'chris', 'user_mail' => '123@gmai.com', 'user_username' => 'admin', 'user_password' => '123', 'user_password_validate' => '123', 'g-recaptcha-response' => 'relis_test'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1")->row_array()['user_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 11
     * Controller : User
     * Action : check_create_user
     * Description : Submit new user form while all the form fields are correctly filed and the reCAPTCHA is checked.
     * Scenario : When the user submit the new user form, a new user is created in the users table"
     * Expected created user: the user that should be inserted in the users table
     * Expected created user confirmation: the user confirmation data that should be inserted in the user_creation table
     */
    private function newUserFormWithcorrectFieldData()
    {
        $action = "check_create_user";
        $test_name = "Submit new user form while all the form fields are correctly filled";

        $test_createdUser = "Created user";
        $test_createdUserConfirmation = "Created user confirmation";

        $user_name = "christian";
        $user_mail = "123@gmai.com";
        $user_username = getUser_username();
        $user_usergroup = "2";
        $created_by = "1";
        $user_state = "0";
        $user_active = "1";
        $user = ["user_name" => $user_name, "user_mail" => $user_mail, "user_username" => $user_username, "user_usergroup" => $user_usergroup, "created_by" => $created_by, "user_state" => $user_state, "user_active" => $user_active];

        $response = $this->http_client->response($this->controller, $action, ['user_name' => $user_name, 'user_mail' => $user_mail, 'user_username' => $user_username, 'user_password' => '123', 'user_password_validate' => '123', 'g-recaptcha-response' => 'relis_test'], "POST");
        $user_data = $this->ci->db->query("SELECT user_name, user_mail, user_username, user_usergroup, created_by, user_state, user_active FROM users WHERE user_username = '" . $user['user_username'] . "'")->row_array();
        $user_confirmation_data = $this->ci->db->query("SELECT creation_user_id, confirmation_code, confirmation_try, user_creation_active FROM user_creation WHERE creation_user_id = " . getTestUserId() . "")->row_array();

        $expected_createdUser = json_encode($user);
        $expected_createdUserConfirmation = json_encode(array("creation_user_id" => getTestUserId(), "confirmation_code" => getTestUserConfirmationCode(), "confirmation_try" => "0", "user_creation_active" => "1"));

        if ($response['status_code'] >= 400) {
            $actual_createdUser = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_createdUserConfirmation = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_createdUser = json_encode($user_data);
            $actual_createdUserConfirmation = json_encode($user_confirmation_data);
        }

        run_test($this->controller, $action, $test_name, $test_createdUser, $expected_createdUser, $actual_createdUser);
        run_test($this->controller, $action, $test_name, $test_createdUserConfirmation, $expected_createdUserConfirmation, $actual_createdUserConfirmation);
    }

    /*
     * Test 12
     * Controller : User
     * Action : validate_user
     * Description : validate_user action displays the Form for account validation after signing up.
     */
    private function accountValidationPage()
    {
        $action = "validate_user";
        $test_name = "Check the displaying of the Form for account validation after signing up";
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
     * Test 13
     * Controller : User
     * Action : check_validation
     * Description : Submit the account validation form with an empty validation_code field.
     * Scenario : When a user submit the account validation form with an empty validation_code field :
     *      - the user_sate field in the users table should remain 0
     *      - the user_creation_active field in the user_creation table should remain 1
     * Expected user_sate field : 0
     * Expected user_creation_active field : 1
     */
    private function accountValidationFormWithEmptyValidationCode()
    {
        $action = "check_validation";
        $test_name = "Submit the account validation form with an empty validation_code field";

        $test_user_sate = "user_state field in users table";
        $test_user_creation_active = "user_creation_active field in user_creation table";

        $expected_user_sate = '0';
        $expected_user_creation_active = '1';

        $response = $this->http_client->response($this->controller, $action, ['user_id' => getTestUserId(), 'validation_code' => ''], "POST");

        if ($response['status_code'] >= 400) {
            $actual_user_sate = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_user_creation_active = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_user_sate = $this->ci->db->query("SELECT user_state FROM users WHERE user_id = '" . getTestUserId() . "'")->row_array()['user_state'];
            $actual_user_creation_active = $this->ci->db->query("SELECT user_creation_active FROM user_creation WHERE creation_user_id = " . getTestUserId() . "")->row_array()['user_creation_active'];
        }

        run_test($this->controller, $action, $test_name, $test_user_sate, $expected_user_sate, $actual_user_sate);
        run_test($this->controller, $action, $test_name, $test_user_creation_active, $expected_user_creation_active, $actual_user_creation_active);
    }

    /*
     * Test 14
     * Controller : User
     * Action : check_validation
     * Description : Submit the account validation form with a wrong validation code.
     * Scenario : When a user submit the account validation form with an wrong validation_code field :
     *      - the user_sate field in the users table should remain 0
     *      - the user_creation_active field in the user_creation table should remain 1
     * Expected user_sate field : 0
     * Expected user_creation_active field : 1
     */
    private function accountValidationFormWithWrongValidationCode()
    {
        $action = "check_validation";
        $test_name = "Submit the account validation form with a wrong validation code";

        $test_user_sate = "user_state field in users table";
        $test_user_creation_active = "user_creation_active field in user_creation table";

        $expected_user_sate = '0';
        $expected_user_creation_active = '1';

        $response = $this->http_client->response($this->controller, $action, ['user_id' => getTestUserId(), 'validation_code' => '1234'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_user_sate = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_user_creation_active = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_user_sate = $this->ci->db->query("SELECT user_state FROM users WHERE user_id = '" . getTestUserId() . "'")->row_array()['user_state'];
            $actual_user_creation_active = $this->ci->db->query("SELECT user_creation_active FROM user_creation WHERE creation_user_id = " . getTestUserId() . "")->row_array()['user_creation_active'];
        }

        run_test($this->controller, $action, $test_name, $test_user_sate, $expected_user_sate, $actual_user_sate);
        run_test($this->controller, $action, $test_name, $test_user_creation_active, $expected_user_creation_active, $actual_user_creation_active);
    }

    /*
     * Test 15
     * Controller : User
     * Action : check_validation
     * Description : Submit the account validation form with a correct validation code.
     * Scenario : When a user submit the account validation form with an correct validation_code field, the account should be validated :
     *      - the user_sate field in the users table should become 1
     *      - the user_creation_active field in the user_creation table should become 0
     * Expected user_sate field : 1
     * Expected user_creation_active field : 0
     */
    private function accountValidationFormWithCorrectValidationCode()
    {
        $action = "check_validation";
        $test_name = "Submit the account validation form with a correct validation code";

        $test_user_sate = "user_state field in users table";
        $test_user_creation_active = "user_creation_active field in user_creation table";

        $expected_user_sate = '1';
        $expected_user_creation_active = '0';

        $response = $this->http_client->response($this->controller, $action, ['user_id' => getTestUserId(), 'validation_code' => getTestUserConfirmationCode()], "POST");

        if ($response['status_code'] >= 400) {
            $actual_user_sate = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_user_creation_active = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_user_sate = $this->ci->db->query("SELECT user_state FROM users WHERE user_id = '" . getTestUserId() . "'")->row_array()['user_state'];
            $actual_user_creation_active = $this->ci->db->query("SELECT user_creation_active FROM user_creation WHERE creation_user_id = " . getTestUserId() . "")->row_array()['user_creation_active'];
        }

        run_test($this->controller, $action, $test_name, $test_user_sate, $expected_user_sate, $actual_user_sate);
        run_test($this->controller, $action, $test_name, $test_user_creation_active, $expected_user_creation_active, $actual_user_creation_active);
    }

    /*
     * Test 16
     * Controller : User
     * Action : login
     * Description : This test verifies the behavior of the 'login' action (go to login form page) when a user is not already logged in (there is no open user session).
     * Scenario : When a user is not logged in and clicks the 'Go to ReLiS' link (go to login form page), the user should be directed to the 'login' HTML form page.
     */
    private function loginPage_userNotConnected()
    {
        $this->http_client->unsetCookie('relis_session');
        $action = "login";
        $test_name = "Go to login form page when user is not already logged in";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

        $this->http_client->unsetCookie('relis_session');
    }

    /*
     * Test 17
     * Controller : User
     * Action : login
     * Description : This test verifies the behavior of the 'login' action (go to login form page) when a user is already logged in (there is an open user session).
     * Scenario : When a user is already logged in and clicks the 'Go to ReLiS' link (go to login form page), the user should be redirected to the 'home/index' url to be automatically logged in.
     */
    private function loginPageURL_userAlreadyConnected()
    {
        $this->http_client->unsetCookie('relis_session');
        $action = "login";
        $test_name = "Go to login form page when user is already logged in";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];

        // Login
        $this->http_client->response($this->controller, "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        // Navigate to login form page
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

        $this->http_client->unsetCookie('relis_session');
    }

    /*
     * Test 18
     * Controller : User
     * Action : check_form
     * Description : Submit login form while all the form fields are empty.
     * Scenario : When the user submit the login form, the user should not be logged in
     * Expected userdata(user_id) session: Null (the authentication session must remain null before and after login attempt)
     */
    private function submitLogin_allFieldsEmpty()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "check_form";
        $test_name = "Submit login form while all the form fields are empty";
        $test_aspect = "User session data";
        $expected_value = $this->http_client->readUserdata('user_id');

        $response = $this->http_client->response($this->controller, $action, ['user_username' => '', 'user_password' => ''], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 19 
     * Controller : User
     * Action : check_form
     * Description : Submit login form with empty username field and filled password field.
     * Scenario : When the user submit the login form, the user should not be logged in
     * Expected userdata(user_id) session: Null (the authentication session must remain null before and after login attempt)
     */
    private function submitLogin_EmptyUsername()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "check_form";
        $test_name = "Submit login form with empty username field and filled password field";
        $test_aspect = "User session data";
        $expected_value = $this->http_client->readUserdata('user_id');

        $response = $this->http_client->response($this->controller, $action, ['user_username' => '', 'user_password' => '123'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 20
     * Controller : User
     * Action : check_form
     * Description : Submit login form with filled username field and empty password field.
     * Scenario : When the user submit the login form, the user should not be logged in
     * Expected userdata(user_id) session: Null (the authentication session must remain null before and after login attempt)
     */
    private function submitLogin_EmptyPassword()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "check_form";
        $test_name = "Submit login form with filled username field and empty password field";
        $test_aspect = "User session data";
        $expected_value = $this->http_client->readUserdata('user_id');

        $response = $this->http_client->response($this->controller, $action, ['user_username' => 'admin', 'user_password' => ''], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 21
     * Controller : User
     * Action : check_form
     * Description : Submit login form with correct username and wrong password.
     * Scenario : When the user submit the login form, the user should not be logged in
     * Expected userdata(user_id) session: Null (the authentication session must remain null before and after login attempt)
     */
    private function submitLogin_wrongPassword()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "check_form";
        $test_name = "Submit login form with correct username and wrong password";
        $test_aspect = "User session data";
        $expected_value = $this->http_client->readUserdata('user_id');

        $response = $this->http_client->response($this->controller, $action, ['user_username' => 'admin', 'user_password' => '111'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 22
     * Controller : User
     * Action : check_form
     * Description : Submit login form with wrong username and correct password.
     * Scenario : When the user submit the login form, the user should not be logged in
     * Expected userdata(user_id) session: Null (the authentication session must remain null before and after login attempt)
     */
    private function submitLogin_wrongUsername()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "check_form";
        $test_name = "Submit login form with wrong username and correct password";
        $test_aspect = "User session data";
        $expected_value = $this->http_client->readUserdata('user_id');

        $response = $this->http_client->response($this->controller, $action, ['user_username' => 'aaa', 'user_password' => '123'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 23
     * Controller : User
     * Action : check_form
     * Description : Submit login form with a non validated account.
     * Scenario : When the user submit the login form, the user should not be logged in
     * Expected userdata(user_id) session: Null (the authentication session must remain null before and after login attempt)
     */
    private function submitLogin_nonValidatedAccount()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "check_form";
        $test_name = "Submit login form with a non validated account";
        $test_aspect = "User session data";
        $expected_value = $this->http_client->readUserdata('user_id');

        //Create new user
        $username = "jackson";
        $this->http_client->response($this->controller, "check_create_user", ['user_name' => "john", 'user_mail' => "abc@gmail.com", 'user_username' => $username, 'user_password' => '123', 'user_password_validate' => '123', 'g-recaptcha-response' => 'relis_test'], "POST");

        $response = $this->http_client->response($this->controller, $action, ['user_username' => $username, 'user_password' => '123'], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 24
     * Controller : User
     * Action : check_form
     * Description : Submit login form with correct username and correct password.
     * Scenario : When the user submit the login form, the user should not be logged in
     * Expected userdata(user_id) session: user ID of the login user
     */
    private function submitLogin_correctUsernameAndPassword()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "check_form";
        $test_name = "Submit login form with correct username and correct password";
        $test_aspect = "User ID session data";

        $username = getUser_username();
        $password = "123";
        $expected_value = $this->ci->db->query("SELECT user_id FROM users WHERE user_username = '$username'")->row_array()['user_id'];

        $response = $this->http_client->response($this->controller, $action, ['user_username' => $username, 'user_password' => $password], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 25
     * Controller : User
     * Action : demo_user
     * Description : Login as demo user
     * Scenario : When the user click on "demo user" link the user should login as demo user
     * Expected userdata(user_id) session: 2 (user_id of demo user)
     */
    private function loginAsDemoUser()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "demo_user";
        $test_name = "Login as demo user";
        $test_aspect = "User ID session data";
        $username = "demo";
        $expected_value = $this->ci->db->query("SELECT user_id FROM users WHERE user_username = '$username'")->row_array()['user_id'];

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 26
     * Controller : User
     * Action : discon
     * Description : logout
     * Expected userdata(user_id) session: Null
     */
    private function logout()
    {
        //unset authentication cookie
        $this->http_client->unsetCookie('relis_session');
        $action = "discon";
        $test_name = "Logout";
        $test_aspect = "User ID session data";
        $expected_value = 'Null';

        //Login first
        $this->http_client->response($this->controller, "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        //Logout
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('user_id');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}