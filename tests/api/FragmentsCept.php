<?php

$I = new ApiTester($scenario);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$yaml = 'fragment.yaml';
$url = $I->getMyBaseUri() . '/fragments';
$I->createResourceFromYaml($yaml);
$response = json_decode($I->getResponse(), true);
$resid = $response['data']['resid'];

$I->wantTo('Test that concatenate pre-calculated integers from fragments.');
$I->sendGet($url, [
    'val1' => 1,
    'val2' => 2,
    'val3' => 3,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '123145',
]);

$I->wantTo('Test that concatenate pre-calculated strings from fragments.');
$I->sendGet($url, [
    'val1' => 'one.',
    'val2' => 'two.',
    'val3' => 'three.',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'one.two.three.one.45',
]);

$I->sendDelete($I->getCoreBaseUri() . "/resource/$resid");
