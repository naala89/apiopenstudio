<?php

$I = new ApiTester($scenario);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$yaml = 'fieldValue.yaml';
$url = $I->getMyBaseUri() . '/field/value';
$I->createResourceFromYaml($yaml);
$response = json_decode($I->getResponse(), true);
$resid = $response['data']['resid'];

// Test field_value fetch key

$I->wantTo('Test fetch key on a field with key text and value text.');
$I->sendGet($url, [
    'key' => 'Foo',
    'value' => 'Bar',
    'key_value' => 'key',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'Foo',
]);

$I->wantTo('Test fetch key on a field with key numerical and value numerical.');
$I->sendGet($url, [
    'key' => 10,
    'value' => 34,
    'key_value' => 'key',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 10,
]);

// Test field_value fetch value

$I->wantTo('Test fetch value on a field with key text and value text.');
$I->sendGet($url, [
    'key' => 'Foo',
    'value' => 'Bar',
    'key_value' => 'value',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'Bar',
]);

$I->wantTo('Test fetch key on a field with key numerical and value numerical.');
$I->sendGet($url, [
    'key' => 10,
    'value' => 34,
    'key_value' => 'value',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34,
]);

$I->sendDelete($I->getCoreBaseUri() . "/resource/$resid");
