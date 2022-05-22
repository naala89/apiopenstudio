<?php

$I = new ApiTester($scenario);

$coreBaseUri = $I->getCoreBaseUri();
$myBaseUri = $I->getMyBaseUri();
$userUri = "$coreBaseUri/user";
$roleUri = "$coreBaseUri/role";
$userRoleUri = "$coreBaseUri/user/role";
$applicationUri = "$coreBaseUri/application";

$testAccName = 'testing_acc';
$testAccId = 2;
$testAppName = 'testing_app';
$testAppId = 2;

$newUserName = 'test_user_name';
$newUserPass = 'test_user_pass';
$newUserEmail = 'test_user@apiopenstudio.com';

$newAppName = 'new_application';

$allRoles = [];
$allUsers = [];
$newUser = [];
$newApp = [];

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

// Setup for tests.

$I->comment('Setting up the test application for testing');
$I->sendPost($applicationUri, [
    'accid' => $testAccId,
    'name' => $newAppName,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
$newApp = $json['data'];

$I->comment('Get all roles.');
$I->sendGet($roleUri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
foreach ($json['data'] as $role) {
    $allRoles[$role['name']] = $role['rid'];
}

$I->comment('Get all users.');
$I->sendGet($userUri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
foreach ($json['data'] as $user) {
    $allUsers[$user['username']] = $user;
}

$I->comment('Set up the user for testing');
$I->sendPost($userUri, [
    'email' => $newUserEmail,
    'username' => $newUserName,
    'password' => $newUserPass,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
$newUser = $json['data'];

// Test user role endpoints.

$I->wantTo('Ensure the new user does not have any roles at creation.');
$I->sendGet($userRoleUri, [
    'uid' => $newUser['uid'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [],
]);

$I->wantTo('Assign the new role to the new app to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $newApp['appid'],
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $newApp['appid'],
        'rid' => $allRoles['Consumer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$urid = $json['data']['urid'];

$I->wantTo('Ensure the new user now has the new user/role.');
$I->sendGet($userRoleUri, [
    'uid' => $newUser['uid'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'urid' => $urid,
            'accid' => $testAccId,
            'appid' => $newApp['appid'],
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Consumer'],
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
    'uid' => $newUser['uid'],
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






// User role permissions

// Consumer

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test consumer canNOT assign the Administrator role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => null,
    'appid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT assign the Account manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT assign the Application manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT assign the Developer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Developer'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT assign the Consumer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->comment('Adding new perms to new user to test delete and read rules for consumer');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'appid' => null,
    'accid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => null,
        'appid' => null,
        'rid' => $allRoles['Administrator'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$administratorUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => null,
        'rid' => $allRoles['Account manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$accountManagerUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Application manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$applicationManagerUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Developer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Developer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$developerUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Consumer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$consumerUrid = $json['data']['urid'];

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test consumer canNOT read the roles from the new user.');
$I->sendGet($userRoleUri, ['uid' => $newUser['uid']]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [],
]);

$I->wantTo('Test consumer canNOT delete the Consumer role from the new user.');
$I->sendDelete("$userRoleUri/$consumerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT delete the Developer role from the new user.');
$I->sendDelete("$userRoleUri/$developerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT delete the Application manager role from the new user.');
$I->sendDelete("$userRoleUri/$applicationManagerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT delete the Account manager role from the new user.');
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test consumer canNOT delete the Administrator role from the new user.');
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->comment('Cleaning up new perms on the new user');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->sendDelete("$userRoleUri/$applicationManagerUrid");
$I->sendDelete("$userRoleUri/$developerUrid");
$I->sendDelete("$userRoleUri/$consumerUrid");








// Developer

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$I->wantTo('Test developer role canNOT assign the Administrator role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT assign the Account manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT assign the Application manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT assign the Developer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Developer'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT assign the Consumer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->comment('Adding new perms to new user to test delete and read rules for developer role');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'appid' => null,
    'accid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => null,
        'appid' => null,
        'rid' => $allRoles['Administrator'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$administratorUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => null,
        'rid' => $allRoles['Account manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$accountManagerUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Application manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$applicationManagerUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Developer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Developer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$developerUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Consumer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$consumerUrid = $json['data']['urid'];

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$I->wantTo('Test developer role canNOT read the roles from the new user.');
$I->sendGet($userRoleUri, ['uid' => $newUser['uid']]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [],
]);

$I->wantTo('Test developer role canNOT delete the Consumer role from the new user.');
$I->sendDelete("$userRoleUri/$consumerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT delete the Developer role from the new user.');
$I->sendDelete("$userRoleUri/$developerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT delete the Application manager role from the new user.');
$I->sendDelete("$userRoleUri/$applicationManagerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT delete the Account manager role from the new user.');
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test developer role canNOT delete the Administrator role from the new user.');
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_security',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->comment('Cleaning up new perms on the new user');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->sendDelete("$userRoleUri/$applicationManagerUrid");
$I->sendDelete("$userRoleUri/$developerUrid");
$I->sendDelete("$userRoleUri/$consumerUrid");








// Application manager

$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));

$I->wantTo('Test Application manager role canNOT assign the Administrator role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'appid' => null,
    'accid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Application manager role canNOT assign the Account manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Application manager role canNOT assign the Application manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Application manager role can assign the Developer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Developer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Developer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$developerUrid = $json['data']['urid'];

$I->wantTo('Test Application manager role can assign the Consumer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Consumer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$consumerUrid = $json['data']['urid'];

$I->comment('Adding new perms to new user to test delete and read rules for developer role');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'appid' => null,
    'accid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => null,
        'appid' => null,
        'rid' => $allRoles['Administrator'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$administratorUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => null,
        'rid' => $allRoles['Account manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$accountManagerUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Application manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$applicationManagerUrid = $json['data']['urid'];

$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));

$I->wantTo('Test Application manager role can read the roles from the new user in the new app.');
$I->sendGet($userRoleUri, ['uid' => $newUser['uid']]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'urid' => $consumerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Consumer'],
        ], [
            'urid' => $developerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Developer'],
        ], [
            'urid' => $applicationManagerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Application manager'],
        ],
    ],
]);

$I->wantTo('Test Application manager role can delete the Consumer role from the new user.');
$I->sendDelete("$userRoleUri/$consumerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Application manager role can delete the Developer role from the new user.');
$I->sendDelete("$userRoleUri/$developerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Application manager role can delete the Application manager role from the new user.');
$I->sendDelete("$userRoleUri/$applicationManagerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Application manager role canNOT delete the Account manager role from the new user.');
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Application manager role canNOT delete the Administrator role from the new user.');
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->comment('Cleaning up new perms on the new user');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->sendDelete("$userRoleUri/$applicationManagerUrid");








// Account manager

$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));

$I->wantTo('Test Account manager role canNOT assign the Administrator role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'appid' => null,
    'accid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Account manager role canNOT assign the Account manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_create_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Account manager role can assign the Application manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Application manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$applicationManagerUrid = $json['data']['urid'];

$I->wantTo('Test Account manager role can assign the Developer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Developer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Developer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$developerUrid = $json['data']['urid'];

$I->wantTo('Test Account manager role can assign the Consumer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Consumer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$consumerUrid = $json['data']['urid'];

$I->comment('Adding new perms to new user to test delete and read rules for developer role');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'appid' => null,
    'accid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => null,
        'appid' => null,
        'rid' => $allRoles['Administrator'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$administratorUrid = $json['data']['urid'];
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => null,
        'rid' => $allRoles['Account manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$accountManagerUrid = $json['data']['urid'];

$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));

$I->wantTo('Test Account manager role can read the roles from the new user in the new app.');
$I->sendGet($userRoleUri, ['uid' => $newUser['uid']]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'urid' => $applicationManagerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Application manager'],
        ], [
            'urid' => $developerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Developer'],
        ], [
            'urid' => $consumerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Consumer'],
        ], [
            'urid' => $accountManagerUrid,
            'accid' => $testAccId,
            'appid' => null,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Account manager'],
        ],
    ],
]);

$I->wantTo('Test Account manager role can delete the Consumer role from the new user.');
$I->sendDelete("$userRoleUri/$consumerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Account manager role can delete the Developer role from the new user.');
$I->sendDelete("$userRoleUri/$developerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Account manager role can delete the Application manager role from the new user.');
$I->sendDelete("$userRoleUri/$applicationManagerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Account manager role canNOT delete the Account manager role from the new user.');
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->wantTo('Test Account manager role canNOT delete the Administrator role from the new user.');
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'user_role_delete_process',
        'code' => 4,
        'message' => 'Permission denied.',
    ],
]);

$I->comment('Cleaning up new perms on the new user');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->sendDelete("$userRoleUri/$accountManagerUrid");








// Account manager

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

$I->wantTo('Test Administrator role can assign the Administrator role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'appid' => null,
    'accid' => null,
    'rid' => $allRoles['Administrator'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => null,
        'appid' => null,
        'rid' => $allRoles['Administrator'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$administratorUrid = $json['data']['urid'];

$I->wantTo('Test Administrator role can assign the Account manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => null,
    'rid' => $allRoles['Account manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => null,
        'rid' => $allRoles['Account manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$accountManagerUrid = $json['data']['urid'];

$I->wantTo('Test Administrator role can assign the Application manager role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Application manager'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Application manager'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$applicationManagerUrid = $json['data']['urid'];

$I->wantTo('Test Administrator role can assign the Developer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Developer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Developer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$developerUrid = $json['data']['urid'];

$I->wantTo('Test Administrator role can assign the Consumer role to the new user.');
$I->sendPost($userRoleUri, [
    'uid' => $newUser['uid'],
    'accid' => $testAccId,
    'appid' => $testAppId,
    'rid' => $allRoles['Consumer'],
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'uid' => $newUser['uid'],
        'accid' => $testAccId,
        'appid' => $testAppId,
        'rid' => $allRoles['Consumer'],
    ],
]);
$json = json_decode($I->getResponse(), true);
$consumerUrid = $json['data']['urid'];

$I->wantTo('Test Administrator role can read the roles from the new user in the new app.');
$I->sendGet($userRoleUri, ['uid' => $newUser['uid']]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'urid' => $applicationManagerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Application manager'],
        ], [
            'urid' => $developerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Developer'],
        ], [
            'urid' => $consumerUrid,
            'accid' => $testAccId,
            'appid' => $testAppId,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Consumer'],
        ], [
            'urid' => $accountManagerUrid,
            'accid' => $testAccId,
            'appid' => null,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Account manager'],
        ], [
            'urid' => $administratorUrid,
            'accid' => null,
            'appid' => null,
            'uid' => $newUser['uid'],
            'rid' => $allRoles['Administrator'],
        ],
    ],
]);

$I->wantTo('Test Administrator role can delete the Consumer role from the new user.');
$I->sendDelete("$userRoleUri/$consumerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Administrator role can delete the Developer role from the new user.');
$I->sendDelete("$userRoleUri/$developerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Administrator role can delete the Application manager role from the new user.');
$I->sendDelete("$userRoleUri/$applicationManagerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Administrator role can delete the Account manager role from the new user.');
$I->sendDelete("$userRoleUri/$accountManagerUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test Administrator role can delete the Administrator role from the new user.');
$I->sendDelete("$userRoleUri/$administratorUrid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);
