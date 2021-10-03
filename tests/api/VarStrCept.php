<?php

$I = new ApiTester($scenario);
$yamlFilename = 'varStr.yaml';
$uri = $I->getMyBaseUri() . '/varstr';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('populate a varStr with text and see the result.');
$I->sendGet($uri, ['value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains("text");

$I->wantTo('populate a varStr with true and see the result.');
$I->sendGet($uri, ['value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains("true");

$I->wantTo('populate a varStr with 1.6 and see the result.');
$I->sendGet($uri, ['value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varStr with 1.6 and see the result.');
$I->sendGet($uri, ['value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varStr with 1 and see the result.');
$I->sendGet($uri, ['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varStr with 1.0 and see the result.');
$I->sendGet($uri, ['value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varStr with -11 and see the result.');
$I->sendGet($uri, ['value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a varStr with -11.0 and see the result.');
$I->sendGet($uri, ['value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a varStr with 0 and see the result.');
$I->sendGet($uri, ['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a varStr with 0.0 and see the result.');
$I->sendGet($uri, ['value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
