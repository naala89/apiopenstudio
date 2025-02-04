<?php

$I = new ApiTester($scenario);
$I->wantTo('perform a successful login as administrator and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $I->getCoreBaseUri() . '/auth/token',
    [
        'username' => getenv('TESTER_ADMINISTRATOR_NAME'),
        'password' => getenv('TESTER_ADMINISTRATOR_PASS'),
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string',
    'data' => [
        'uid' => 'integer',
        'token' => 'string',
        'token_expiry' => 'string',
        'refresh_token' => 'string',
        'refresh_expiry' => 'string',
    ],
]);

$I->wantTo('perform a successful login as account manager and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $I->getCoreBaseUri() . '/auth/token',
    [
        'username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'),
        'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS'),
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string',
    'data' => [
        'uid' => 'integer',
        'token' => 'string',
        'token_expiry' => 'string',
        'refresh_token' => 'string',
        'refresh_expiry' => 'string',
    ],
]);

$I->wantTo('perform a successful login as application manager and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $I->getCoreBaseUri() . '/auth/token',
    [
        'username' => getenv('TESTER_APPLICATION_MANAGER_NAME'),
        'password' => getenv('TESTER_APPLICATION_MANAGER_PASS'),
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string',
    'data' => [
        'uid' => 'integer',
        'token' => 'string',
        'token_expiry' => 'string',
        'refresh_token' => 'string',
        'refresh_expiry' => 'string',
    ],
]);

$I->wantTo('perform a successful login as developer and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $I->getCoreBaseUri() . '/auth/token',
    [
        'username' => getenv('TESTER_DEVELOPER_NAME'),
        'password' => getenv('TESTER_DEVELOPER_PASS'),
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string',
    'data' => [
        'uid' => 'integer',
        'token' => 'string',
        'token_expiry' => 'string',
        'refresh_token' => 'string',
        'refresh_expiry' => 'string',
    ],
]);

$I->wantTo('perform a successful login as consumer and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $I->getCoreBaseUri() . '/auth/token',
    [
        'username' => getenv('TESTER_CONSUMER_NAME'),
        'password' => getenv('TESTER_CONSUMER_PASS'),
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string',
    'data' => [
        'uid' => 'integer',
        'token' => 'string',
        'token_expiry' => 'string',
        'refresh_token' => 'string',
        'refresh_expiry' => 'string',
    ],
]);

$I = new ApiTester($scenario);
$I->wantTo('perform a login with bad password see 401 with error object');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $I->getCoreBaseUri() . '/auth/token',
    [
        'username' => getenv('TESTER_ADMINISTRATOR_NAME'),
        'password' => 'badpassword',
    ]
);
$I->seeResponseCodeIs(401);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 4,
        'message' => 'Invalid username or password.',
        'id' => 'generate_token_process',
    ],
]);

$I = new ApiTester($scenario);
$I->wantTo('perform a login with bad username see 401 with error object');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $I->getCoreBaseUri() . '/auth/token',
    [
        'username' => 'badusername',
        'password' => getenv('TESTER_ADMINISTRATOR_PASS'),
    ]
);
$I->seeResponseCodeIs(401);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 4,
        'message' => 'Invalid username or password.',
        'id' => 'generate_token_process',
    ],
]);
