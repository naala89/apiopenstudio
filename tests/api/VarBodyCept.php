<?php

$I = new ApiTester($scenario);
$yamlFilename = 'varBody.yaml';
$uri = $I->getMyBaseUri() . '/var_body';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test VarBody with an empty body, expected type text, nullable false. Header text');
$I->haveHttpHeader('Accept', 'text/plain');
$I->sendPOST("$uri?expected_type=json&nullable=false", '');
$I->seeResponseCodeIs(400);
$I->seeResponseContains('Error: Body is empty');
$I->deleteHeader('Accept');

$I->wantTo('Test VarBody with an empty body, expected type text, nullable false. Header text');
$I->haveHttpHeader('Accept', 'text/plain');
// phpcs:ignore
$loremIpsum = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
$I->sendPOST("$uri?expected_type=json&nullable=false", $loremIpsum);
$I->seeResponseCodeIs(200);
$I->seeResponseContains($loremIpsum);
$I->deleteHeader('Accept');

$I->wantTo('Test VarBody with a raw JSON body, expected type json, nullable false.');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST("$uri?expected_type=json&nullable=false", '{"test":true}');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'test' => true,
    ]
]);
$I->deleteHeader('Accept');

$I->wantTo('Test VarBody with a raw JSON body, expected type json, nullable false.');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST("$uri?expected_type=json&nullable=false", [
    "test" => true
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'test=1',
]);
$I->deleteHeader('Accept');

$I->wantTo('Test VarBody with a raw JSON body, expected type json, nullable false. Header text');
$I->haveHttpHeader('Accept', 'text/plain');
$I->sendPOST("$uri?expected_type=json&nullable=false", '{"test":true}');
$I->seeResponseCodeIs(200);
$I->seeResponseContains('{"test":true}');
$I->deleteHeader('Accept');

$I->wantTo('Test VarBody with post variables, expected type json, nullable false.');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST("$uri?expected_type=json&nullable=false", '{"test":true}');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'test' => true,
    ]
]);
$I->deleteHeader('Accept');
