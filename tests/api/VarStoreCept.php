<?php

$I = new ApiTester($scenario);
$uri = $I->getCoreBaseUri() . '/var_store';

// Test role access to create var_store.

$I->wantTo('Test a consumer cannot create a var for an application they are assigned to');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    "error" => [
        "id" => "var_store_create_process",
        "code" => 6,
        "message" => "Permission denied."
    ]
]);

$I->wantTo('Test a consumer cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    "error" => [
        "id" => "var_store_create_process",
        "code" => 6,
        "message" => "Permission denied."
    ]
]);

$I->wantTo('Test a developer can create a var for an application they are assigned to');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.key');
$I->seeResponseJsonMatchesJsonPath('$.val');

$I->wantTo('Test a developer cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey2', 'val' => 'varval2']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    "error" => [
        "id" => "var_store_create_process",
        "code" => 6,
        "message" => "Permission denied.",
    ]
]);

$I->wantTo('Test an application manager can create a var for an application they are assigned to');
$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey2', 'val' => 'varval2']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.key');
$I->seeResponseJsonMatchesJsonPath('$.val');

$I->wantTo('Test an application manager cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey3', 'val' => 'varval3']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    "error" => [
        "id" => "var_store_create_process",
        "code" => 6,
        "message" => "Permission denied.",
    ]
]);

$I->wantTo('Test an account manager can create a var for an application in an account they are assigned to');
$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey3', 'val' => 'varval3']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.key');
$I->seeResponseJsonMatchesJsonPath('$.val');

$I->wantTo('Test an account manager cannot create a var for an application in an account they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey4', 'val' => 'varval4']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    "error" => [
        "id" => "var_store_create_process",
        "code" => 6,
        "message" => "Permission denied.",
    ]
]);

$I->wantTo('Test an administrator can create a var for any application');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey4', 'val' => 'varval4']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.key');
$I->seeResponseJsonMatchesJsonPath('$.val');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey5', 'val' => 'varval5']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.key');
$I->seeResponseJsonMatchesJsonPath('$.val');

// Test role access to read var_store.

$yamlFilename = 'varStoreRead.yaml';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/testing_var_store';

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

// phpcs:ignore
$I->wantTo('Test a consumer cannot read a var within an application they are assigned to with validate_access set to true, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => true]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([]);

// phpcs:ignore
$I->wantTo('Test a consumer can read a var within an application they are assigned to with validate_access set to false, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test a consumer cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
$I->sendGet($uri, ['vid' => 9]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([]);

// phpcs:ignore
$I->wantTo('Test a consumer can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
$I->sendGet($uri, ['vid' => 9, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

// phpcs:ignore
$I->wantTo('Test a developer can read a var within an application they are assigned to with validate_access set to true, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test a developer can read a var within an application they are assigned to with validate_access set to false, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test a developer cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
$I->sendGet($uri, ['vid' => 9]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([]);

// phpcs:ignore
$I->wantTo('Test a developer can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
$I->sendGet($uri, ['vid' => 9, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));

// phpcs:ignore
$I->wantTo('Test an application manager can read a var within an application they are assigned to with validate_access set to true, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test an application manager can read a var within an application they are assigned to with validate_access set to false, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test an application manager cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
$I->sendGet($uri, ['vid' => 9]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([]);

// phpcs:ignore
$I->wantTo('Test an application manager can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
$I->sendGet($uri, ['vid' => 9, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));

// phpcs:ignore
$I->wantTo('Test an account manager can read a var within an application they are assigned to with validate_access set to true, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test an account manager can read a var within an application they are assigned to with validate_access set to false, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test an account manager cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
$I->sendGet($uri, ['vid' => 9]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([]);

// phpcs:ignore
$I->wantTo('Test an account manager can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
$I->sendGet($uri, ['vid' => 9, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

// phpcs:ignore
$I->wantTo('Test an administrator can read a var within an application they are assigned to with validate_access set to true, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test an administrator can read a var within an application they are assigned to with validate_access set to false, by vid');
$I->sendGet($uri, ['vid' => 5, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test an administrator cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
$I->sendGet($uri, ['vid' => 9]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// phpcs:ignore
$I->wantTo('Test an administrator can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
$I->sendGet($uri, ['vid' => 9, 'validate_access' => false]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$..key');
$I->seeResponseJsonMatchesJsonPath('$..val');

// Clean up
$uri = $I->getCoreBaseUri() . '/var_store';

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete($uri, ['vid' => 9]);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->sendDelete($uri, ['vid' => 5]);
$I->sendDelete($uri, ['vid' => 6]);
$I->sendDelete($uri, ['vid' => 7]);
$I->sendDelete($uri, ['vid' => 8]);
