<?php

$I = new ApiTester($scenario);
$uri = $I->getCoreBaseUri() . '/var_store';

// Test role access to create var_store.

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test a consumer cannot create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a consumer cannot create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a consumer cannot create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a consumer cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$I->wantTo('Test a developer cannot create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a developer can create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey1', 'val' => 'varval1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey1~)',
        'val' => 'string:regex(~varval1~)',
    ],
]);

$I->wantTo('Test a developer cannot create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey2', 'val' => 'varval2']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a developer cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey2', 'val' => 'varval2']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));

$I->wantTo('Test an application manager cannot create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey2', 'val' => 'varval2']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test an application manager can create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey2', 'val' => 'varval2']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey2~)',
        'val' => 'string:regex(~varval2~)',
    ],
]);

$I->wantTo('Test an application manager cannot create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey3', 'val' => 'varval3']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test an application manager cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey3', 'val' => 'varval3']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));

$I->wantTo('Test an account manager can create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey3', 'val' => 'varval3']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>1:<3',
        'appid' => 'null',
        'key' => 'string:regex(~varkey3~)',
        'val' => 'string:regex(~varval3~)',
    ],
]);

$I->wantTo('Test an account manager can create a var for an application belonging to the account they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey4', 'val' => 'varval4']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey4~)',
        'val' => 'string:regex(~varval4~)',
    ],
]);

$I->wantTo('Test an account manager cannot create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey5', 'val' => 'varval5']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test an account manager cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey5', 'val' => 'varval5']);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store create process',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

$I->wantTo('Test an administrator can create a var for the test account');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey6', 'val' => 'varval6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>1:<3',
        'appid' => 'null',
        'key' => 'string:regex(~varkey6~)',
        'val' => 'string:regex(~varval6~)',
    ],
]);

$I->wantTo('Test an administrator can create a var for the test application');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey8', 'val' => 'varval8']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey8~)',
        'val' => 'string:regex(~varval8~)',
    ],
]);

$I->wantTo('Test an administrator can create a var for the apiopenstudio account');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey5', 'val' => 'varval5']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>0:<2',
        'appid' => 'null',
        'key' => 'string:regex(~varkey5~)',
        'val' => 'string:regex(~varval5~)',
    ],
]);

$I->wantTo('Test an administrator can create a var for the core application');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey7', 'val' => 'varval7']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>0:<2',
        'key' => 'string:regex(~varkey7~)',
        'val' => 'string:regex(~varval7~)',
    ],
]);

// Test create var_store with validate_access => false.

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$yaml = 'varStoreCreateWithoutValidation.yaml';
$uri = $I->getMyBaseUri() . '/var_store/no_validation';
$I->createResourceFromYaml($yaml);

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test a consumer can create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey9', 'val' => 'varval9']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>1:<3',
        'appid' => 'null',
        'key' => 'string:regex(~varkey9~)',
        'val' => 'string:regex(~varval9~)',
    ],
]);

$I->wantTo('Test a consumer can create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey10', 'val' => 'varval10']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey10~)',
        'val' => 'string:regex(~varval10~)',
    ],
]);

$I->wantTo('Test a consumer can create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey11', 'val' => 'varval11']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>0:<2',
        'appid' => 'null',
        'key' => 'string:regex(~varkey11~)',
        'val' => 'string:regex(~varval11~)',
    ],
]);

$I->wantTo('Test a consumer can create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey12', 'val' => 'varval12']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>0:<2',
        'key' => 'string:regex(~varkey12~)',
        'val' => 'string:regex(~varval12~)',
    ],
]);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$I->wantTo('Test a developer can create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey13', 'val' => 'varval13']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>1:<3',
        'appid' => 'null',
        'key' => 'string:regex(~varkey13~)',
        'val' => 'string:regex(~varval13~)',
    ],
]);

$I->wantTo('Test a developer can create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey14', 'val' => 'varval14']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey14~)',
        'val' => 'string:regex(~varval14~)',
    ],
]);

$I->wantTo('Test a developer can create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey15', 'val' => 'varval15']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>0:<2',
        'appid' => 'null',
        'key' => 'string:regex(~varkey15~)',
        'val' => 'string:regex(~varval15~)',
    ],
]);

$I->wantTo('Test a developer can create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey16', 'val' => 'varval16']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>0:<2',
        'key' => 'string:regex(~varkey16~)',
        'val' => 'string:regex(~varval16~)',
    ],
]);

$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));

$I->wantTo('Test an application manager can create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey17', 'val' => 'varval17']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>1:<3',
        'appid' => 'null',
        'key' => 'string:regex(~varkey17~)',
        'val' => 'string:regex(~varval17~)',
    ],
]);

$I->wantTo('Test an application manager can create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey18', 'val' => 'varval18']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey18~)',
        'val' => 'string:regex(~varval18~)',
    ],
]);

$I->wantTo('Test an application manager can create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey19', 'val' => 'varval19']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>0:<2',
        'appid' => 'null',
        'key' => 'string:regex(~varkey19~)',
        'val' => 'string:regex(~varval19~)',
    ],
]);

$I->wantTo('Test an application manager can create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey20', 'val' => 'varval20']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>0:<2',
        'key' => 'string:regex(~varkey20~)',
        'val' => 'string:regex(~varval20~)',
    ],
]);

$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));

$I->wantTo('Test an account manager can create a var for an account they are assigned to');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey21', 'val' => 'varval21']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>1:<3',
        'appid' => 'null',
        'key' => 'string:regex(~varkey21~)',
        'val' => 'string:regex(~varval21~)',
    ],
]);

$I->wantTo('Test an account manager can create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey22', 'val' => 'varval22']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey22~)',
        'val' => 'string:regex(~varval22~)',
    ],
]);

$I->wantTo('Test an account manager cannot create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey23', 'val' => 'varval23']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>0:<2',
        'appid' => 'null',
        'key' => 'string:regex(~varkey23~)',
        'val' => 'string:regex(~varval23~)',
    ],
]);

$I->wantTo('Test an account manager can create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey24', 'val' => 'varval24']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey24~)',
        'val' => 'string:regex(~varval24~)',
    ],
]);

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

$I->wantTo('Test an administrator can create a var for the test account.');
$I->sendPost($uri, ['accid' => 2, 'key' => 'varkey25', 'val' => 'varval25']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>1:<3',
        'appid' => 'null',
        'key' => 'string:regex(~varkey25~)',
        'val' => 'string:regex(~varval25~)',
    ],
]);

$I->wantTo('Test an administrator can create a var for for the test application.');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey26', 'val' => 'varval26']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>1:<3',
        'key' => 'string:regex(~varkey26~)',
        'val' => 'string:regex(~varval26~)',
    ],
]);

$I->wantTo('Test an administrator can create a var for for the apiopenstudio account.');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey27', 'val' => 'varval27']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'integer:>0:<2',
        'appid' => 'null',
        'key' => 'string:regex(~varkey27~)',
        'val' => 'string:regex(~varval27~)',
    ],
]);

$I->wantTo('Test an administrator can create a var for for the core application.');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey28', 'val' => 'varval28']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>0:<2',
        'key' => 'string:regex(~varkey28~)',
        'val' => 'string:regex(~varval28~)',
    ],
]);

// Test role access to read var_store.

$uri = $I->getCoreBaseUri() . '/var_store';

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test a consumer cannot read any vars');
$I->sendGet($uri);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store read security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$I->wantTo('Test a developer can only see vars from an application they are assigned to or its account');
$I->sendGet($uri, ['order_by' => 'vid']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'vid' => 5,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey1',
            'val' => 'varval1',
        ], [
            'vid' => 6,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey2',
            'val' => 'varval2',
        ], [
            'vid' => 7,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey3',
            'val' => 'varval3',
        ], [
            'vid' => 8,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey4',
            'val' => 'varval4',
        ], [
            'vid' => 9,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey6',
            'val' => 'varval6',
        ], [
            'vid' => 10,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey8',
            'val' => 'varval8',
        ], [
            'vid' => 13,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey9',
            'val' => 'varval9',
        ], [
            'vid' => 14,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey10',
            'val' => 'varval10',
        ], [
            'vid' => 17,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey13',
            'val' => 'varval13',
        ], [
            'vid' => 18,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey14',
            'val' => 'varval14',
        ], [
            'vid' => 21,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey17',
            'val' => 'varval17',
        ], [
            'vid' => 22,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey18',
            'val' => 'varval18',
        ], [
            'vid' => 25,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey21',
            'val' => 'varval21',
        ], [
            'vid' => 26,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey22',
            'val' => 'varval22',
        ], [
            'vid' => 28,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey24',
            'val' => 'varval24',
        ], [
            'vid' => 29,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey25',
            'val' => 'varval25',
        ], [
            'vid' => 30,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey26',
            'val' => 'varval26',
        ],
    ],
]);

$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));

$I->wantTo('Test an application manager can only see vars from an application they are assigned to or its account');
$I->sendGet($uri, ['order_by' => 'vid']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'vid' => 5,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey1',
            'val' => 'varval1',
        ], [
            'vid' => 6,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey2',
            'val' => 'varval2',
        ], [
            'vid' => 7,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey3',
            'val' => 'varval3',
        ], [
            'vid' => 8,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey4',
            'val' => 'varval4',
        ], [
            'vid' => 9,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey6',
            'val' => 'varval6',
        ], [
            'vid' => 10,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey8',
            'val' => 'varval8',
        ], [
            'vid' => 13,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey9',
            'val' => 'varval9',
        ], [
            'vid' => 14,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey10',
            'val' => 'varval10',
        ], [
            'vid' => 17,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey13',
            'val' => 'varval13',
        ], [
            'vid' => 18,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey14',
            'val' => 'varval14',
        ], [
            'vid' => 21,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey17',
            'val' => 'varval17',
        ], [
            'vid' => 22,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey18',
            'val' => 'varval18',
        ], [
            'vid' => 25,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey21',
            'val' => 'varval21',
        ], [
            'vid' => 26,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey22',
            'val' => 'varval22',
        ], [
            'vid' => 28,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey24',
            'val' => 'varval24',
        ], [
            'vid' => 29,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey25',
            'val' => 'varval25',
        ], [
            'vid' => 30,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey26',
            'val' => 'varval26',
        ],
    ],
]);

$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));

$I->wantTo('Test an account manager can only see vars from an account they are assigned to or its applications');
$I->sendGet($uri, ['order_by' => 'vid']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'vid' => 5,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey1',
            'val' => 'varval1',
        ], [
            'vid' => 6,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey2',
            'val' => 'varval2',
        ], [
            'vid' => 7,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey3',
            'val' => 'varval3',
        ], [
            'vid' => 8,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey4',
            'val' => 'varval4',
        ], [
            'vid' => 9,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey6',
            'val' => 'varval6',
        ], [
            'vid' => 10,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey8',
            'val' => 'varval8',
        ], [
            'vid' => 13,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey9',
            'val' => 'varval9',
        ], [
            'vid' => 14,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey10',
            'val' => 'varval10',
        ], [
            'vid' => 17,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey13',
            'val' => 'varval13',
        ], [
            'vid' => 18,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey14',
            'val' => 'varval14',
        ], [
            'vid' => 21,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey17',
            'val' => 'varval17',
        ], [
            'vid' => 22,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey18',
            'val' => 'varval18',
        ], [
            'vid' => 25,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey21',
            'val' => 'varval21',
        ], [
            'vid' => 26,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey22',
            'val' => 'varval22',
        ], [
            'vid' => 28,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey24',
            'val' => 'varval24',
        ], [
            'vid' => 29,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey25',
            'val' => 'varval25',
        ], [
            'vid' => 30,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey26',
            'val' => 'varval26',
        ],
    ],
]);

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

$I->wantTo('Test an administrator can read all vars.');
$I->sendGet($uri, ['accid' => 2, 'order_by' => 'vid']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'vid' => 1,
            'accid' => null,
            'appid' => 1,
            'key' => 'user_invite_subject',
            'val' => 'ApiOpenStudio invite',
        ], [
            'vid' => 2,
            'accid' => null,
            'appid' => 1,
            'key' => 'user_invite_message',
            // phpcs:ignore
            'val' => '<!doctype html><html><head><meta name="viewport" content="width=device-width" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>ApiOpenStudio invite</title><style>img{border:none;-ms-interpolation-mode:bicubic;max-width:100%}body{background-color:#f6f6f6;font-family:sans-serif;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}table{border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%}table td{font-family:sans-serif;font-size:14px;vertical-align:top}.body{background-color:#f6f6f6;width:100%}.container{display:block;margin:0 auto !important;max-width:580px;padding:10px;width:580px}.content{box-sizing:border-box;display:block;margin:0 auto;max-width:580px;padding:10px}.main{background:#fff;border-radius:3px;width:100%}.wrapper{box-sizing:border-box;padding:20px}.content-block{padding-bottom:10px;padding-top:10px}.footer{clear:both;margin-top:10px;text-align:center;width:100%}.footer td, .footer p, .footer span, .footer a{color:#999;font-size:12px;text-align:center}h1,h2,h3,h4{color:#000;font-family:sans-serif;font-weight:400;line-height:1.4;margin:0;margin-bottom:30px}h1{font-size:35px;font-weight:300;text-align:center;text-transform:capitalize}p,ul,ol{font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;margin-bottom:15px}p li, ul li, ol li{list-style-position:inside;margin-left:5px}a{color:#3498db;text-decoration:underline}.btn{box-sizing:border-box;width:100%}.btn>tbody>tr>td{padding-bottom:15px}.btn table{width:auto}.btn table td{background-color:#fff;border-radius:5px;text-align:center}.btn a{background-color:#fff;border:solid 1px #3498db;border-radius:5px;box-sizing:border-box;color:#3498db;cursor:pointer;display:inline-block;font-size:14px;font-weight:bold;margin:0;padding:12px 25px;text-decoration:none;text-transform:capitalize}.btn-primary table td{background-color:#3498db}.btn-primary a{background-color:#3498db;border-color:#3498db;color:#fff}.last{margin-bottom:0}.first{margin-top:0}.align-center{text-align:center}.align-right{text-align:right}.align-left{text-align:left}.clear{clear:both}.mt0{margin-top:0}.mb0{margin-bottom:0}.preheader{color:transparent;display:none;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;visibility:hidden;width:0}.powered-by a{text-decoration:none}hr{border:0;border-bottom:1px solid #f6f6f6;margin:20px 0}@media only screen and (max-width: 620px){table[class=body] h1{font-size:28px !important;margin-bottom:10px !important}table[class=body] p, table[class=body] ul, table[class=body] ol, table[class=body] td, table[class=body] span, table[class=body] a{font-size:16px !important}table[class=body] .wrapper, table[class=body] .article{padding:10px !important}table[class=body] .content{padding:0 !important}table[class=body] .container{padding:0 !important;width:100% !important}table[class=body] .main{border-left-width:0 !important;border-radius:0 !important;border-right-width:0 !important}table[class=body] .btn table{width:100% !important}table[class=body] .btn a{width:100% !important}table[class=body] .img-responsive{height:auto !important;max-width:100% !important;width:auto !important}}@media all{.ExternalClass{width:100%}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%}.apple-link a{color:inherit !important;font-family:inherit !important;font-size:inherit !important;font-weight:inherit !important;line-height:inherit !important;text-decoration:none !important}#MessageViewBody a{color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit}.btn-primary table td:hover{background-color:#34495e !important}.btn-primary a:hover{background-color:#34495e !important;border-color:#34495e !important}}</style></head><body class=""> <span class="preheader">Invite to ApiOpenStudio.</span><table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body"><tr><td>&nbsp;</td><td class="container"><div class="content"><table role="presentation" class="main"><tr><td class="wrapper"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td><p>Hi there,</p><p>We have invited you to our API.</p><p>Clicking on the link below will grant you access to the app and the resources</p><p>When you have been granted access to ApiOpenStudio, you will need to set your password. Go to the login page and click on the reset password link and reset the password using your email.</p><p>To edit you profile and set a username, login using your email as a username and view/edit your profile.</p><table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary"><tbody><tr><td align="left"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td> <a href="https://[domain]/invite/accept/[token]" target="_blank">https://[domain]/invite/accept/[token]</a></td></tr></tbody></table></td></tr></tbody></table></td></tr></table></td></tr></table><div class="footer"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="content-block"> <span class="apple-link">ApiOpenStudio</span></td></tr><tr><td class="content-block powered-by"> Powered by <a href="https://www.apiopenstudio.com">ApiOpenStudio</a>.</td></tr></table></div></div></td><td>&nbsp;</td></tr></table></body></html>',
        ], [
            'vid' => 3,
            'accid' => null,
            'appid' => 1,
            'key' => 'password_reset_subject',
            'val' => 'ApiOpenStudio password reset',
        ], [
            'vid' => 4,
            'accid' => null,
            'appid' => 1,
            'key' => 'password_reset_message',
            // phpcs:ignore
            'val' => '<!doctype html><html><head><meta name="viewport" content="width=device-width" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>ApiOpenStudio invite</title><style>img{border:none;-ms-interpolation-mode:bicubic;max-width:100%}body{background-color:#f6f6f6;font-family:sans-serif;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}table{border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%}table td{font-family:sans-serif;font-size:14px;vertical-align:top}.body{background-color:#f6f6f6;width:100%}.container{display:block;margin:0 auto !important;max-width:580px;padding:10px;width:580px}.content{box-sizing:border-box;display:block;margin:0 auto;max-width:580px;padding:10px}.main{background:#fff;border-radius:3px;width:100%}.wrapper{box-sizing:border-box;padding:20px}.content-block{padding-bottom:10px;padding-top:10px}.footer{clear:both;margin-top:10px;text-align:center;width:100%}.footer td, .footer p, .footer span, .footer a{color:#999;font-size:12px;text-align:center}h1,h2,h3,h4{color:#000;font-family:sans-serif;font-weight:400;line-height:1.4;margin:0;margin-bottom:30px}h1{font-size:35px;font-weight:300;text-align:center;text-transform:capitalize}p,ul,ol{font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;margin-bottom:15px}p li, ul li, ol li{list-style-position:inside;margin-left:5px}a{color:#3498db;text-decoration:underline}.btn{box-sizing:border-box;width:100%}.btn>tbody>tr>td{padding-bottom:15px}.btn table{width:auto}.btn table td{background-color:#fff;border-radius:5px;text-align:center}.btn a{background-color:#fff;border:solid 1px #3498db;border-radius:5px;box-sizing:border-box;color:#3498db;cursor:pointer;display:inline-block;font-size:14px;font-weight:bold;margin:0;padding:12px 25px;text-decoration:none;text-transform:capitalize}.btn-primary table td{background-color:#3498db}.btn-primary a{background-color:#3498db;border-color:#3498db;color:#fff}.last{margin-bottom:0}.first{margin-top:0}.align-center{text-align:center}.align-right{text-align:right}.align-left{text-align:left}.clear{clear:both}.mt0{margin-top:0}.mb0{margin-bottom:0}.preheader{color:transparent;display:none;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;visibility:hidden;width:0}.powered-by a{text-decoration:none}hr{border:0;border-bottom:1px solid #f6f6f6;margin:20px 0}@media only screen and (max-width: 620px){table[class=body] h1{font-size:28px !important;margin-bottom:10px !important}table[class=body] p, table[class=body] ul, table[class=body] ol, table[class=body] td, table[class=body] span, table[class=body] a{font-size:16px !important}table[class=body] .wrapper, table[class=body] .article{padding:10px !important}table[class=body] .content{padding:0 !important}table[class=body] .container{padding:0 !important;width:100% !important}table[class=body] .main{border-left-width:0 !important;border-radius:0 !important;border-right-width:0 !important}table[class=body] .btn table{width:100% !important}table[class=body] .btn a{width:100% !important}table[class=body] .img-responsive{height:auto !important;max-width:100% !important;width:auto !important}}@media all{.ExternalClass{width:100%}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%}.apple-link a{color:inherit !important;font-family:inherit !important;font-size:inherit !important;font-weight:inherit !important;line-height:inherit !important;text-decoration:none !important}#MessageViewBody a{color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit}.btn-primary table td:hover{background-color:#34495e !important}.btn-primary a:hover{background-color:#34495e !important;border-color:#34495e !important}}</style></head><body class=""> <span class="preheader">ApiOpenStudio password reset.</span><table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body"><tr><td>&nbsp;</td><td class="container"><div class="content"><table role="presentation" class="main"><tr><td class="wrapper"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td><p>Hi there,</p><p>We have received a request to reset your password at ApiOpenStudio.</p><p>If you believe this was sent in error, please contact your administrator.</p><p>To reset your password, please click on the foloowing link.</p><table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary"><tbody><tr><td align="left"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td> <a href="https://[domain]/password/set/[token]" target="_blank">https://[domain]/password/set/[token]</a></td></tr></tbody></table></td></tr></tbody></table></td></tr></table></td></tr></table><div class="footer"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="content-block"> <span class="apple-link">ApiOpenStudio</span></td></tr><tr><td class="content-block powered-by"> Powered by <a href="https://www.apiopenstudio.com">ApiOpenStudio</a>.</td></tr></table></div></div></td><td>&nbsp;</td></tr></table></body></html>',
        ], [
            'vid' => 5,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey1',
            'val' => 'varval1',
        ], [
            'vid' => 6,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey2',
            'val' => 'varval2',
        ], [
            'vid' => 7,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey3',
            'val' => 'varval3',
        ], [
            'vid' => 8,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey4',
            'val' => 'varval4',
        ], [
            'vid' => 9,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey6',
            'val' => 'varval6',
        ], [
            'vid' => 10,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey8',
            'val' => 'varval8',
        ], [
            'vid' => 11,
            'accid' => 1,
            'appid' => null,
            'key' => 'varkey5',
            'val' => 'varval5',
        ], [
            'vid' => 12,
            'accid' => null,
            'appid' => 1,
            'key' => 'varkey7',
            'val' => 'varval7',
        ], [
            'vid' => 13,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey9',
            'val' => 'varval9',
        ], [
            'vid' => 14,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey10',
            'val' => 'varval10',
        ], [
            'vid' => 15,
            'accid' => 1,
            'appid' => null,
            'key' => 'varkey11',
            'val' => 'varval11',
        ], [
            'vid' => 16,
            'accid' => null,
            'appid' => 1,
            'key' => 'varkey12',
            'val' => 'varval12',
        ], [
            'vid' => 17,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey13',
            'val' => 'varval13',
        ], [
            'vid' => 18,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey14',
            'val' => 'varval14',
        ], [
            'vid' => 19,
            'accid' => 1,
            'appid' => null,
            'key' => 'varkey15',
            'val' => 'varval15',
        ], [
            'vid' => 20,
            'accid' => null,
            'appid' => 1,
            'key' => 'varkey16',
            'val' => 'varval16',
        ], [
            'vid' => 21,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey17',
            'val' => 'varval17',
        ], [
            'vid' => 22,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey18',
            'val' => 'varval18',
        ], [
            'vid' => 23,
            'accid' => 1,
            'appid' => null,
            'key' => 'varkey19',
            'val' => 'varval19',
        ], [
            'vid' => 24,
            'accid' => null,
            'appid' => 1,
            'key' => 'varkey20',
            'val' => 'varval20',
        ], [
            'vid' => 25,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey21',
            'val' => 'varval21',
        ], [
            'vid' => 26,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey22',
            'val' => 'varval22',
        ], [
            'vid' => 27,
            'accid' => 1,
            'appid' => null,
            'key' => 'varkey23',
            'val' => 'varval23',
        ], [
            'vid' => 28,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey24',
            'val' => 'varval24',
        ], [
            'vid' => 29,
            'accid' => 2,
            'appid' => null,
            'key' => 'varkey25',
            'val' => 'varval25',
        ], [
            'vid' => 30,
            'accid' => null,
            'appid' => 2,
            'key' => 'varkey26',
            'val' => 'varval26',
        ], [
            'vid' => 31,
            'accid' => 1,
            'appid' => null,
            'key' => 'varkey27',
            'val' => 'varval27',
        ], [
            'vid' => 32,
            'accid' => null,
            'appid' => 1,
            'key' => 'varkey28',
            'val' => 'varval28',
        ],
    ],
]);

// Test role access to delete var_store.

$uri = $I->getCoreBaseUri() . '/var_store';

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test a consumer cannot delete a var for an account they are associated to via appid');
$I->sendDelete($uri, ['accid' => 2]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a consumer cannot delete a var for an application they are assigned to');
$I->sendDelete($uri, ['appid' => 2]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a consumer cannot delete a var for an account they are not assigned to');
$I->sendDelete($uri, ['accid' => 1]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->wantTo('Test a consumer cannot delete a var for an application they are not assigned to');
$I->sendDelete($uri, ['appid' => 1]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete security',
        'code' => 4,
        'message' => 'Permission denied.'
    ]
]);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$I->wantTo('Test a developer cannot delete a var for an account they are associated to via appid');
$I->sendDelete("$uri/null/2/null/varkey3/null");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ],
]);

$I->wantTo('Test a developer can delete a var for an application they are assigned to');
$I->sendDelete("$uri/null/null/2/varkey1/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test a developer cannot delete a var for an account they are not assigned to');
$I->sendDelete("$uri/null/1/null/null/null");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ]
]);

$I->wantTo('Test a developer cannot delete a var for an application they are not assigned to');
$I->sendDelete("$uri/null/null/1/null/null");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ]
]);

$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));

$I->wantTo('Test an application manager cannot delete a var for an account they are associated with via appid');
$I->sendDelete("$uri/null/2/null/varkey6/null");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ],
]);

$I->wantTo('Test an application manager can delete a var for an application they are assigned to');
$I->sendDelete("$uri/null/null/2/varkey2/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test an application manager cannot delete a var for an account they are not assigned to');
$I->sendDelete("$uri/null/1/null/null/null");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ]
]);

$I->wantTo('Test an application manager cannot delete a var for an application they are not assigned to');
$I->sendDelete("$uri/null/null/1/null/null");
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ]
]);

$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));

$I->wantTo('Test an account manager can delete a var for an account they are assigned to');
$I->sendDelete("$uri/null/2/null/varkey6/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test an account manager can delete a var for an application they are associated with via accid');
$I->sendDelete("$uri/null/null/2/varkey4/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test an account manager cannot delete a var for an account they are not assigned to');
$I->sendDelete("$uri/null/1/null/null/null");
// $I->sendDelete($uri, ['accid' => 1]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ]
]);

$I->wantTo('Test an account manager cannot delete a var for an application they are not associated with');
$I->sendDelete("$uri/null/null/1/null/null");
// $I->sendDelete($uri, ['appid' => 1]);
$I->seeResponseCodeIs(403);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'var store delete process',
        'code' => 4,
        'message' => 'Permission denied. You do not have delete rights to all the variables in the result.'
    ]
]);

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

$I->wantTo('Test an administrator can delete a var from the test account.');
$I->sendDelete("$uri/null/2/null/varkey9/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test an administrator can delete a var from the test application.');
$I->sendDelete("$uri/null/null/2/varkey8/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test an administrator can delete a var from the apiopenstudio account');
$I->sendDelete("$uri/null/1/null/varkey5/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test an administrator can delete a var from the core application');
$I->sendDelete("$uri/null/1/null/varkey11/null");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

// Tidy up data
$I->sendDelete("$uri/null/null/null/null/varkey");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);
