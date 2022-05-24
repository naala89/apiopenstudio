<?php

$I = new ApiTester($scenario);
$uri = $I->getCoreBaseUri() . '/var_store';
$accVarStores = $appVarStores = [];

// Test role access to create var_store.

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test a consumer cannot create a var for an account they are assigned to');
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

$I->wantTo('Test a consumer cannot create a var for an application they are assigned to');
$I->sendPost($uri, ['appid' => 2, 'key' => 'varkey1', 'val' => 'varval1']);
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

$I->wantTo('Test a consumer cannot create a var for an account they are not assigned to');
$I->sendPost($uri, ['accid' => 1, 'key' => 'varkey1', 'val' => 'varval1']);
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

$I->wantTo('Test a consumer cannot create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey1', 'val' => 'varval1']);
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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[1][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[1][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

// Test create var_store with validate_access: false.

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$yaml = 'var_store_create_without_validation.yaml';
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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

$I->wantTo('Test an account manager can create a var for an account they are not assigned to');
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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

$I->wantTo('Test an account manager can create a var for an application they are not assigned to');
$I->sendPost($uri, ['appid' => 1, 'key' => 'varkey24', 'val' => 'varval24']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string:regex(~ok~)',
    'data' => [
        'vid' => 'integer:>0',
        'accid' => 'null',
        'appid' => 'integer:>0:<2',
        'key' => 'string:regex(~varkey24~)',
        'val' => 'string:regex(~varval24~)',
    ],
]);
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));

$I->wantTo('Test an administrator can create a var for an account they are assigned to');
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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

$I->wantTo('Test an administrator can create a var for an application they are assigned to');
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
$response = json_decode($I->getResponse(), true);
$appVarStores[2][$response['data']['key']] = $response['data']['vid'];

$I->wantTo('Test an administrator can create a var for an account they are not assigned to');
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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];

$I->wantTo('Test an administrator can create a var for an application they are not assigned to');
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
$response = json_decode($I->getResponse(), true);
$accVarStores[2][$response['data']['key']] = $response['data']['vid'];




//
//
//// Test role access to read var_store.
//
//$yamlFilename = 'varStoreRead.yaml';
//
//$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
//$I->createResourceFromYaml($yamlFilename);
//$I->deleteHeader('Authorization');
//
//$uri = $I->getMyBaseUri() . '/testing_var_store';
//
//$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
//
//// phpcs:ignore
//$I->wantTo('Test a consumer can read a var within an application they are assigned to with validate_access set to true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test a consumer can read a var within an application they are assigned to with validate_access set to false, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test a consumer can read a var within an application they are NOT assigned to with validate_access as default true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey5']]);
//$I->seeResponseCodeIs(400);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([]);
//
//// phpcs:ignore
//$I->wantTo('Test a consumer can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
//$I->sendGet($uri, ['vid' => $varStores['varkey5'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>0:<2',
//            'key' => 'string:regex(~varkey5~)',
//            'val' => 'string:regex(~varval5~)',
//        ],
//    ],
//]);
//
//$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
//
//// phpcs:ignore
//$I->wantTo('Test a developer can read a var within an application they are assigned to with validate_access set to true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test a developer can read a var within an application they are assigned to with validate_access set to false, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test a developer cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey5']]);
//$I->seeResponseCodeIs(400);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([]);
//
//// phpcs:ignore
//$I->wantTo('Test a developer can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
//$I->sendGet($uri, ['vid' => $varStores['varkey5'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>0:<2',
//            'key' => 'string:regex(~varkey5~)',
//            'val' => 'string:regex(~varval5~)',
//        ],
//    ],
//]);
//
//$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));
//
//// phpcs:ignore
//$I->wantTo('Test an application manager can read a var within an application they are assigned to with validate_access set to true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test an application manager can read a var within an application they are assigned to with validate_access set to false, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test an application manager cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey5']]);
//$I->seeResponseCodeIs(400);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([]);
//
//// phpcs:ignore
//$I->wantTo('Test an application manager can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
//$I->sendGet($uri, ['vid' => $varStores['varkey5'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>0:<2',
//            'key' => 'string:regex(~varkey5~)',
//            'val' => 'string:regex(~varval5~)',
//        ],
//    ],
//]);
//
//$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));
//
//// phpcs:ignore
//$I->wantTo('Test an account manager can read a var within an application they are assigned to with validate_access set to true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test an account manager can read a var within an application they are assigned to with validate_access set to false, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test an account manager cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey5']]);
//$I->seeResponseCodeIs(400);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([]);
//
//// phpcs:ignore
//$I->wantTo('Test an account manager can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
//$I->sendGet($uri, ['vid' => $varStores['varkey5'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>0:<2',
//            'key' => 'string:regex(~varkey5~)',
//            'val' => 'string:regex(~varval5~)',
//        ],
//    ],
//]);
//
//$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
//
//// phpcs:ignore
//$I->wantTo('Test an administrator can read a var within an application they are assigned to with validate_access set to true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test an administrator can read a var within an application they are assigned to with validate_access set to false, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey1'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>1:<3',
//            'key' => 'string:regex(~varkey1~)',
//            'val' => 'string:regex(~varval1~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test an administrator cannot read a var within an application they are NOT assigned to with validate_access as default true, by vid');
//$I->sendGet($uri, ['vid' => $varStores['varkey5']]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>0:<2',
//            'key' => 'string:regex(~varkey5~)',
//            'val' => 'string:regex(~varval5~)',
//        ],
//    ],
//]);
//
//// phpcs:ignore
//$I->wantTo('Test an administrator can read a var within an application they are NOT assigned to with validate_access set to false, by vid ');
//$I->sendGet($uri, ['vid' => $varStores['varkey5'], 'validate_access' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseMatchesJsonType([
//    'result' => 'string:regex(~ok~)',
//    'data' => [
//        [
//            'vid' => 'integer:>0',
//            'appid' => 'integer:>0:<2',
//            'key' => 'string:regex(~varkey5~)',
//            'val' => 'string:regex(~varval5~)',
//        ],
//    ],
//]);
//
//// Clean up
//
//$uri = $I->getCoreBaseUri() . '/var_store';
//
//$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
//foreach ($varStores as $vid) {
//    $I->sendDelete($uri . '/' . $vid);
//}
//
//$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
//$I->tearDownTestFromYaml($yamlFilename);
