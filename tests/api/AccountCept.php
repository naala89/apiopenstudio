<?php

$I = new ApiTester($scenario);

$validCreateEditDeleteUsers = [
    ['username' => getenv('TESTER_ADMINISTRATOR_NAME'), 'password' => getenv('TESTER_ADMINISTRATOR_PASS')],
];
$invalidCreateEditDeleteUsers = [
    ['username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'), 'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS')],
    ['username' => getenv('TESTER_APPLICATION_MANAGER_NAME'), 'password' => getenv('TESTER_APPLICATION_MANAGER_PASS')],
    ['username' => getenv('TESTER_DEVELOPER_NAME'), 'password' => getenv('TESTER_DEVELOPER_PASS')],
    ['username' => getenv('TESTER_CONSUMER_NAME'), 'password' => getenv('TESTER_CONSUMER_PASS')],
];
$validReadUsers = [
    [
        'username' => getenv('TESTER_ADMINISTRATOR_NAME'),
        'password' => getenv('TESTER_ADMINISTRATOR_PASS'),
        'accounts' => [
            [
                'accid' => 1,
                'name' => 'apiopenstudio',
            ], [
                'accid' => 2,
                'name' => 'testing_acc',
            ],
        ],
    ],
    [
        'username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'),
        'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS'),
        'accounts' => [
            [
                'accid' => 2,
                'name' => 'testing_acc',
            ],
        ],
    ], [
        'username' => getenv('TESTER_APPLICATION_MANAGER_NAME'),
        'password' => getenv('TESTER_APPLICATION_MANAGER_PASS'),
        'accounts' => [
            [
                'accid' => 2,
                'name' => 'testing_acc',
            ],
        ],
    ], [
        'username' => getenv('TESTER_DEVELOPER_NAME'),
        'password' => getenv('TESTER_DEVELOPER_PASS'),
        'accounts' => [
            [
                'accid' => 2,
                'name' => 'testing_acc',
            ],
        ],
    ], [
        'username' => getenv('TESTER_CONSUMER_NAME'),
        'password' => getenv('TESTER_CONSUMER_PASS'),
        'accounts' => [
            'accid' => 2,
            'name' => 'testing_acc',
        ],
    ],
];

// Test account generation for each role
$uri = $I->getCoreBaseUri() . '/account';
$accid = 0;
foreach ($validCreateEditDeleteUsers as $user) {
    $I->performLogin($user['username'], $user['password']);
    $I->sendPost($uri, ['name' => 'new_account1']);
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseJsonMatchesJsonPath('$.accid');
    $response = json_decode($I->getResponse(), true);
    $accid = $response['accid'];
    $I->seeResponseContainsJson([
        'accid' => $accid,
        'name' => 'new_account1'
    ]);

    $I->sendPut("$uri/$accid/edited_name");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'accid' => $accid,
        'name' => 'edited_name'
    ]);

    $I->sendDelete("$uri/$accid");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContains('true');
}

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($uri, ['name' => 'new_account1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.accid');
$response = json_decode($I->getResponse(), true);
$accid = $response['accid'];
$I->seeResponseContainsJson([
    'accid' => $accid,
    'name' => 'new_account1'
]);

foreach ($invalidCreateEditDeleteUsers as $user) {
    $I->performLogin($user['username'], $user['password']);
    $I->sendPost($uri, ['name' => 'new_account2']);
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(['error' => [
        'code' => 4,
        'id' => 'create_account_security',
        'message' => 'Permission denied.',
    ]]);

    $I->sendPut("$uri/$accid/edited_name");
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(['error' => [
        'code' => 4,
        'id' => 'account_update_security',
        'message' => 'Permission denied.',
    ]]);

    $I->sendDelete("$uri/$accid");
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(['error' => [
        'code' => 4,
        'id' => 'delete_account_security',
        'message' => 'Permission denied.',
    ]]);
}

// Test all account read for all users.
foreach ($validReadUsers as $user) {
    $I->performLogin($user['username'], $user['password']);
    $I->sendGet("$uri");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson($user['accounts']);
}

// Test individual account read for a user
foreach ($validReadUsers as $user) {
    if ($user['username'] != getenv('TESTER_ADMINISTRATOR_NAME')) {
        $I->performLogin($user['username'], $user['password']);
        $I->sendGet("$uri/1");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([]);

        $I->sendGet("$uri/2");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'accid' => 2,
                'name' => 'testing_acc',
            ],
        ]);

        $I->sendGet("$uri/$accid");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([]);
    } else {
        $I->performLogin($user['username'], $user['password']);
        $I->sendGet("$uri/1");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'accid' => 1,
                'name' => 'apiopenstudio',
            ],
        ]);

        $I->sendGet("$uri/2");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'accid' => 2,
                'name' => 'testing_acc',
            ],
        ]);

        $I->sendGet("$uri/$accid");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'accid' => $accid,
                'name' => 'new_account1',
            ],
        ]);
    }
}

// Clean up
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete("$uri/$accid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
