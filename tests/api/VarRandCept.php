<?php

use function PHPUnit\Framework\assertEquals;

$I = new ApiTester($scenario);
$yamlFilename = 'varRand.yaml';
$uri = $I->getMyBaseUri() . '/varrand';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('test a varRand with no settings and see the result.');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string',
    'data' => 'string',
]);
$result = json_decode($I->getResponse(), true);
assertEquals(8, strlen($result['data']), 'assert default random string length is 8.');

$I->wantTo('test a varRand with length settings and see the result.');
$I->sendGet($uri, ['length' => 25, 'special' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
    'result' => 'string',
    'data' => 'string',
]);
$result = json_decode($I->getResponse(), true);
assertEquals(25, strlen($result['data']), 'assert random string with manually defined length is 25.');

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
