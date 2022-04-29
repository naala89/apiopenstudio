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
        'read_result' => [
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>0:<2',
                    'name' => 'string:regex(~apiopenstudio~)',
                ], [
                    'accid' => 'integer:>1:<3',
                    'name' => 'string:regex(~testing_acc~)',
                ],
            ]
        ],
    ],
    [
        'username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'),
        'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS'),
        'read_result' => [
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>1:<3',
                    'name' => 'string:regex(~testing_acc~)',
                ],
            ],
        ],
    ], [
        'username' => getenv('TESTER_APPLICATION_MANAGER_NAME'),
        'password' => getenv('TESTER_APPLICATION_MANAGER_PASS'),
        'read_result' => [
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>1:<3',
                    'name' => 'string:regex(~testing_acc~)',
                ],
            ],
        ],
    ], [
        'username' => getenv('TESTER_DEVELOPER_NAME'),
        'password' => getenv('TESTER_DEVELOPER_PASS'),
        'read_result' => [
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>1:<3',
                    'name' => 'string:regex(~testing_acc~)',
                ],
            ],
        ],
    ], [
        'username' => getenv('TESTER_CONSUMER_NAME'),
        'password' => getenv('TESTER_CONSUMER_PASS'),
        'read_result' => [
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>1:<3',
                    'name' => 'string:regex(~testing_acc~)',
                ],
            ],
        ],
    ],
];

// Test account generation for each role
$uri = $I->getCoreBaseUri() . '/account';
$accid = 0;
foreach ($validCreateEditDeleteUsers as $user) {
    $I->comment('Testing account crud with valid user: ' . $user['username']);
    $I->performLogin($user['username'], $user['password']);

    $I->wantTo('Create a new account with name: new_account1.');
    $I->sendPost($uri, ['name' => 'new_account1']);
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseJsonMatchesJsonPath('$.data.accid');
    $I->seeResponseMatchesJsonType([
        'result' => 'string:regex(~ok~)',
        'data' => [
            'accid' => 'integer:>2',
            'name' => 'string:regex(~new_account1~)',
        ],
    ]);
    $response = json_decode($I->getResponse(), true);
    $accid = $response['data']['accid'];
    $I->comment("Got accid: $accid");

    $I->wantTo("Update account $accid to edited_name.");
    $I->sendPut("$uri/$accid/edited_name");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseMatchesJsonType([
        'result' => 'string:regex(~ok~)',
        'data' => [
            'accid' => 'integer:>' . ($accid - 1) . ':<' . ($accid + 1),
            'name' => 'string:regex(~edited_name~)',
        ],
    ]);

    $I->wantTo("Delete accid $accid.");
    $I->sendDelete("$uri/$accid");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseIsJson([
        [
            'result' => 'ok',
            'data' => true,
        ],
    ]);
}

$I->wantTo('Recreate the new account with name: new_account1, using administrator.');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($uri, ['name' => 'new_account1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data.accid');
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'accid' => 'integer:>2',
        'name' => 'string:regex(~new_account1~)',
    ],
]);
$response = json_decode($I->getResponse(), true);
$accid = $response['data']['accid'];
$I->comment("Got accid: $accid");
$validReadUsers[0]['read_result']['data'][] = [
    'accid' => 'integer:>' . ($accid - 1) . ':<' . ($accid + 1),
    'name' => 'string:regex(~new_account1~)',
];

foreach ($invalidCreateEditDeleteUsers as $user) {
    $I->comment('Testing account crud with invalid user: ' . $user['username']);
    $I->performLogin($user['username'], $user['password']);
    $I->sendPost($uri, ['name' => 'new_account2']);
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseMatchesJsonType([
        'result' => 'string:regex(~error~)',
        'data' => [
            'code' => 'integer:>3:<5',
            'id' => 'string:regex(~create_account_security~)',
            'message' => 'string:regex(~Permission denied.~)',
        ]
    ]);

    $I->sendPut("$uri/$accid/edited_name");
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseMatchesJsonType([
        'result' => 'string:regex(~error~)',
        'data' => [
            'code' => 'integer:>3:<5',
            'id' => 'string:regex(~account_update_security~)',
            'message' => 'string:regex(~Permission denied.~)',
        ]
    ]);

    $I->sendDelete("$uri/$accid");
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseMatchesJsonType([
        'result' => 'string:regex(~error~)',
        'data' => [
            'code' => 'integer:>3:<5',
            'id' => 'string:regex(~delete_account_security~)',
            'message' => 'string:regex(~Permission denied.~)',
        ]
    ]);
}

// Test all account read for all users.
foreach ($validReadUsers as $user) {
    $I->wantTo('Test fetch all accounts for user: ' . $user['username']);
    $I->performLogin($user['username'], $user['password']);
    $I->sendGet("$uri");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseMatchesJsonType($user['read_result']);
}

// Test individual account read for a user
foreach ($validReadUsers as $user) {
    $I->comment('Testing read individual accounts for user' . $user['username']);
    $I->performLogin($user['username'], $user['password']);

    if ($user['username'] == getenv('TESTER_ADMINISTRATOR_NAME')) {
        $I->wantTo('Test read account 1');
        $I->sendGet("$uri", ['accid' => 1]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>0:<2',
                    'name' => 'string:regex(~apiopenstudio~)',
                ],
            ],
        ]);

        $I->wantTo('Test read account 2');
        $I->sendGet("$uri", ['accid' => 2]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>1:<3',
                    'name' => 'string:regex(~testing_acc~)',
                ],
            ],
        ]);

        $I->wantTo("Test read account $accid");
        $I->sendGet("$uri", ['accid' => $accid]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>' . ($accid - 1) . ':<' . ($accid + 1),
                    'name' => 'string:regex(~new_account1~)',
                ],
            ],
        ]);
    } else {
        $I->wantTo('Test read account 1');
        $I->sendGet("$uri", ['accid' => 1]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson([
            [
                'result' => 'ok',
                'data' => [],
            ],
        ]);

        $I->wantTo('Test read account 2');
        $I->sendGet("$uri", ['accid' => 2]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'result' => 'string:regex(~ok~)',
            'data' => [
                [
                    'accid' => 'integer:>1:<3',
                    'name' => 'string:regex(~testing_acc~)',
                ],
            ],
        ]);

        $I->wantTo("Test read account $accid");
        $I->sendGet("$uri", ['accid' => $accid]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson([
            [
                'result' => 'ok',
                'data' => [],
            ],
        ]);
    }
}

$I->wantTo('Test exception for valid user deleting invalid account.');
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete("$uri/" . ($accid + 1));
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~error~)',
    'data' => [
        'code' => 'integer:>5:<7',
        'id' => 'string:regex(~account_delete_process~)',
        'message' => 'string:regex(~Account does not exist: 5.~)',
    ]
]);

// Clean up
$I->sendDelete("$uri/$accid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
