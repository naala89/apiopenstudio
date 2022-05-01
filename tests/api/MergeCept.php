<?php

$I = new ApiTester($scenario);
$yamlFilename = 'merge.yaml';
$uri = $I->getMyBaseUri() . '/merge';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('perform a merge of type union with unique set to false and see result');
$I->sendGet($uri, [
    'merge_type' => 'union',
    'unique' => 'false'
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'val1',
        'val1',
        'val2',
        'val3',
        'val4',
        'val5',
        'val6',
        'val7',
        'val8',
    ],
]);

$I->wantTo('perform a merge of type union without unique set and see result');
$I->sendGet($uri, [
    'merge_type' => 'union'
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'val1',
        'val1',
        'val2',
        'val3',
        'val4',
        'val5',
        'val6',
        'val7',
        'val8',
    ],
]);

$I->wantTo('perform a merge of type union with unique set to true and default reset_keys and see result');
$I->sendGet($uri, [
    'merge_type' => 'union',
    'unique' => true,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        0 => 'val1',
        1 => 'val2',
        2 => 'val3',
        3 => 'val4',
        5 => 'val5',
        6 => 'val6',
        7 => 'val7',
        8 => 'val8',
    ],
]);

$I->wantTo('perform a merge of type union with unique set to true and reset_keys set to false and see result');
$I->sendGet($uri, [
    'merge_type' => 'union',
    'unique' => true,
    'reset_keys' => false,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        0 => 'val1',
        1 => 'val2',
        2 => 'val3',
        3 => 'val4',
        5 => 'val5',
        6 => 'val6',
        7 => 'val7',
        8 => 'val8',
    ],
]);

$I->wantTo('perform a merge of type union with unique set to true and reset_keys set to true and see result');
$I->sendGet($uri, [
    'merge_type' => 'union',
    'unique' => true,
    'reset_keys' => true,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        0 => 'val1',
        1 => 'val2',
        2 => 'val3',
        3 => 'val4',
        4 => 'val5',
        5 => 'val6',
        6 => 'val7',
        7 => 'val8',
    ],
]);

$I->wantTo('perform a merge of type intersect without unique set and see result');
$I->sendGet($uri, [
    'merge_type' => 'intersect',
    'reset_keys' => true,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'val1',
    ],
]);

$I->wantTo('perform a merge of type difference without unique set and see result');
$I->sendGet($uri, [
    'merge_type' => 'difference',
    'reset_keys' => true,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'val2',
        'val3',
        'val4',
        'val5',
        'val6',
        'val7',
        'val8',
    ],
]);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
