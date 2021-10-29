<?php

$I = new ApiTester($scenario);
$yamlFilename = 'varGet.yaml';
$uri = $I->getMyBaseUri() . '/varget';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$uri = $I->getMyBaseUri() . '/varget/';

$I->wantTo('populate a VarGet with text and see the result.');
$I->sendGet($uri, ['value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('text');

$I->wantTo('populate a VarGet with true and see the result.');
$I->sendGet($uri, ['value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarGet with 1.6 and see the result.');
$I->sendGet($uri, ['value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarGet with 1.6 and see the result.');
$I->sendGet($uri, ['value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarGet with 1 and see the result.');
$I->sendGet($uri, ['value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarGet with 1.0 and see the result.');
$I->sendGet($uri, ['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarGet with 0 and see the result.');
$I->sendGet($uri, ['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarGet with 0.0 and see the result.');
$I->sendGet($uri, ['value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarGet with wrong varname and nullable true and see the result.');
$I->sendGet($uri, ['values' => 'test', 'nullable' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseEquals('');

$I->wantTo('populate a VarGet with wrong varname and nullable false and see the result.');
$I->sendGet($uri, ['values' => 'test', 'nullable' => false]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 6,
            'message' => "GET variable (value) does not exist or is empty.",
            'id' => 'test var_get process',
        ],
    ]
);

$I->wantTo('populate a VarGet with wrong varname and nullable not set and see the result.');
$I->sendGet($uri, ['values' => 'test']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 6,
            'message' => "GET variable (value) does not exist or is empty.",
            'id' => 'test var_get process',
        ],
    ]
);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
