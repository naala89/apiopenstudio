<?php

$I = new ApiTester($scenario);
$yamlFilename = 'varPost.yaml';
$uri = $I->getMyBaseUri() . '/varpost';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('populate a varPost with text and see the result.');
$I->sendPOST($uri, ['value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'text',
]);

$I->wantTo('populate a varPost with true and see the result.');
$I->sendPOST($uri, ['value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'true',
]);

$I->wantTo('populate a varPost with 1.6 and see the result.');
$I->sendPOST($uri, ['value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 1.6,
]);

$I->wantTo('populate a varPost with 1.6 and see the result.');
$I->sendPOST($uri, ['value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 1.6,
]);

$I->wantTo('populate a varPost with 1 and see the result.');
$I->sendPOST($uri, ['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 1,
]);

$I->wantTo('populate a varPost with 1.0 and see the result.');
$I->sendPOST($uri, ['value' => 1.0]);
$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 1,
]);

$I->wantTo('populate a varPost with -11 and see the result.');
$I->sendPOST($uri, ['value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => -11,
]);

$I->wantTo('populate a varPost with -11.0 and see the result.');
$I->sendPOST($uri, ['value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => -11,
]);

$I->wantTo('populate a varPost with 0 and see the result.');
$I->sendPOST($uri, ['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 0,
]);

$I->wantTo('populate a varPost with 0.0 and see the result.');
$I->sendPOST($uri, ['value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 0,
]);

$I->wantTo('populate a varPost with wrong varname and see the result.');
$I->sendPOST($uri, ['values' => 'test']);
$I->seeResponseCodeIs(200);
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => null,
]);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
