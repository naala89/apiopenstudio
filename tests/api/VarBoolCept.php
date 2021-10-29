<?php

$yamlFilename = 'varBool.yaml';

$I = new ApiTester($scenario);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$uri = $I->getMyBaseUri() . '/varbool/';

$I->wantTo('populate a VarBool with 1 and see the result.');
$I->sendGet($uri, ['value' => '1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->sendGet($uri, ['value' => '0']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with 1 and see the result.');
$I->sendGet($uri, ['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->sendGet($uri, ['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with yes and see the result.');
$I->sendGet($uri, ['value' => 'yes']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with no and see the result.');
$I->sendGet($uri, ['value' => 'no']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with true and see the result.');
$I->sendGet($uri, ['value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->sendGet($uri, ['value' => 'false']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with 6 and see the result.');
$I->sendGet($uri, ['value' => 6]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('error.message');
$I->seeResponseJsonMatchesJsonPath('error.id');

$I->wantTo('populate a VarBool with fales and see the result.');
$I->sendGet($uri, ['value' => 'fales']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('error.message');
$I->seeResponseJsonMatchesJsonPath('error.id');

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
