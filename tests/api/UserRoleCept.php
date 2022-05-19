<?php

$I = new ApiTester($scenario);

$coreBaseUri = $I->getCoreBaseUri();
$myBaseUri = $I->getMyBaseUri();
$userUri = "$coreBaseUri/user";
$roleUri = "$coreBaseUri/role";
$userRoleUri = "$coreBaseUri/user/role";
$applicationUri = "$coreBaseUri/application";

$testUserName = 'test_user_name';
$testUserPass = 'test_user_pass';
$testUserEmail = 'test_user@apiopenstudio.com';
$testUser = [];

$testRoleName = 'test_role';
$allRoles = [];
$testRole = [];

$testAccName = 'test_acc';
$testAccId = 2;
$testAppName = 'test_app';
$testAppId = 2;

$testAppName = 'temp_app';
$testApp = [];

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

// Setup for tests.

$I->comment('Setting up the test application for testing');
$I->sendPost($applicationUri, [
    'accid' => $testAccId,
    'name' => $testAppName,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
$testApp = $json['data'];

$I->comment('Get all roles.');
$I->sendGet($roleUri, []);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
$allRoles = $json['data'];

$I->comment('Setting up the test role for testing');
$I->sendPost($roleUri, [
    'name' => $testRoleName,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
$testRole = $json['data'];

$I->comment('Setting up the user for testing');
$I->sendPost($userUri, [
    'email' => $testUserEmail,
    'username' => $testUserName,
    'password' => $testUserPass,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
$testUser = $json['data'];

// Test user role endpoints.

$I->wantTo('Ensure the new user does not have any roles at creation.');
$I->sendGet($userRoleUri, [
    'uid' => $testUser['uid'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [],
]);

$I->wantTo('Assign the new role to the new app to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $testUser['uid'],
    'accid' => $testAccId,
    'appid' => $testApp['appid'],
    'rid' => $testRole['rid'],

]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'accid' => $testAccId,
        'appid' => $testApp['appid'],
        'uid' => $testUser['uid'],
        'rid' => $testRole['rid'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$urid = $json['data']['urid'];

$I->wantTo('Ensure the new user now has the new user/role.');
$I->sendGet($userRoleUri, [
    'uid' => $testUser['uid'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'urid' => $urid,
            'accid' => $testAccId,
            'appid' => $testApp['appid'],
            'uid' => $testUser['uid'],
            'rid' => $testRole['rid'],
        ],
    ],
]);

$I->wantTo('Delete the new user role.');
$I->sendDelete("$userRoleUri/$urid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Ensure the new user does not have any roles.');
$I->sendGet($userRoleUri, [
    'uid' => $testUser['uid'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [],
]);

$I->wantTo('Test delete a user role fails gracefully if not found.');
$I->sendDelete("$userRoleUri/$urid");
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_process',
        'code' => 6,
        'message' => 'Invalid user role.',
    ],
]);
