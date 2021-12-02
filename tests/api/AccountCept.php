<?php

$I = new ApiTester($scenario);

$validCreateEditDeleteRoles = [
    ['username' => getenv('TESTER_ADMINISTRATOR_NAME'), 'password' => getenv('TESTER_ADMINISTRATOR_NAME')],
];
$invalidCreateEditDeleteRoles = [
    ['username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'), 'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS')],
    ['username' => getenv('TESTER_APPLICATION_MANAGER_NAME'), 'password' => getenv('TESTER_APPLICATION_MANAGER_PASS')],
    ['username' => getenv('TESTER_DEVELOPER_NAME'), 'password' => getenv('TESTER_DEVELOPER_PASS')],
    ['username' => getenv('TESTER_CONSUMER_NAME'), 'password' => getenv('TESTER_CONSUMER_PASS')],
];
$validReadRoles = [
    ['username' => getenv('TESTER_ADMINISTRATOR_NAME'), 'password' => getenv('TESTER_ADMINISTRATOR_NAME')],
    ['username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'), 'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS')],
    ['username' => getenv('TESTER_APPLICATION_MANAGER_NAME'), 'password' => getenv('TESTER_APPLICATION_MANAGER_PASS')],
    ['username' => getenv('TESTER_DEVELOPER_NAME'), 'password' => getenv('TESTER_DEVELOPER_PASS')],
    ['username' => getenv('TESTER_CONSUMER_NAME'), 'password' => getenv('TESTER_CONSUMER_PASS')],
];

// Test account generation for each role
$uri = $I->getMyBaseUri() . '/account';
foreach ($validCreateEditDeleteRoles as $validCreateEditDeleteRole) {
    $I->performLogin($validCreateEditDeleteRole['username'], $validCreateEditDeleteRole['password']);
    $I->sendPost($uri, ['new_account1']);
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseJsonMatchesJsonPath('$.accid');
    $I->seeResponseJsonMatchesJsonPath('$.name');
}
foreach ($invalidCreateEditDeleteRoles as $invalidCreateEditDeleteRole) {
    $I->performLogin($invalidCreateEditDeleteRole['username'], $invalidCreateEditDeleteRole['password']);
    $I->sendPost($uri, ['new_account1']);
    $I->seeResponseCodeIs(401);
    $I->seeResponseIsJson();
    $I->seeResponseJsonMatchesJsonPath('$.error');
    $I->seeResponseJsonMatchesJsonPath('$.error.code');
    $I->seeResponseJsonMatchesJsonPath('$.error.id');
    $I->seeResponseJsonMatchesJsonPath('$.error.message');
}
