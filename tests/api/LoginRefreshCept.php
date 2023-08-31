<?php

$I = new ApiTester($scenario);

$uri = $I->getCoreBaseUri() . '/auth/token';
$I->haveHttpHeader('Accept', 'application/json');

$I->wantTo('perform a successful login as account manager and see result');
$data = $I->sendPOST(
    $uri,
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
$data = json_decode($data, true);
$acc_man_token = $data['data']['token'];
$acc_man_refresh_token = $data['data']['refresh_token'];

$I->wantTo('perform a successful login as developer and see result');
$data = $I->sendPOST(
    $uri,
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
$data = json_decode($data, true);
$dev_token = $data['data']['token'];
$dev_refresh_token = $data['data']['refresh_token'];

$uri = $I->getCoreBaseUri() . '/auth/token/refresh';

$I->wantTo('refresh tokens without a token');
$I->sendPOST(
    $uri,
    [
        'refresh_token' => $dev_refresh_token,
    ]
);
$I->seeResponseCodeIs(401);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 4,
        'message' => 'Invalid token.',
        'id' => 'refresh_token_process',
    ],
]);

$I->wantTo('refresh tokens with a mismatching token and refresh token');
$I->haveHttpHeader('Authorization', 'bearer ' . $acc_man_token);
$I->sendPOST(
    $uri,
    [
        'refresh_token' => $dev_refresh_token,
    ]
);
$I->seeResponseCodeIs(401);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 4,
        'message' => 'Invalid refresh token.',
        'id' => 'refresh_token_process',
    ],
]);

$I->wantTo('refresh tokens with a matching token and refresh token');
$I->haveHttpHeader('Authorization', 'bearer ' . $dev_token);
$I->sendPOST(
    $uri,
    [
        'refresh_token' => $dev_refresh_token,
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
