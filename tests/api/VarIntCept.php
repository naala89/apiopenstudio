<?php

$I = new ApiTester($scenario);
$yamlFilename = 'varInt.yaml';
$uri = $I->getMyBaseUri() . '/varint';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('populate a VarInt with text and see the result.');
$I->sendGet($uri, ['value' => 'text']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "error" => [
            "code" => 6,
            "message" => "Invalid type (text), only 'integer' allowed in input 'value'.",
            "id" => 'test var_int process',
        ],
    ]
);

$I->wantTo('populate a VarInt with true bool and see the result.');
$I->sendGet($uri, ['value' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with true string and see the result.');
$I->sendGet($uri, ['value' => 'true']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "error" => [
            "code" => 6,
            "message" => "Invalid type (boolean), only 'integer' allowed in input 'value'.",
            "id" => 'test var_int process',
        ],
    ]
);

$I->wantTo('populate a VarInt with 1.6 and see the result.');
$I->sendGet($uri, ['value' => 1.6]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "error" => [
            "code" => 6,
            "message" => "Invalid type (float), only 'integer' allowed in input 'value'.",
            "id" =>  'test var_int process',
        ],
    ]
);

$I->wantTo('populate a VarInt with 1 and see the result.');
$I->sendGet($uri, ['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with 1.0 and see the result.');
$I->sendGet($uri, ['value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with -11 and see the result.');
$I->sendGet($uri, ['value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarInt with -11.0 and see the result.');
$I->sendGet($uri, ['value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarInt with 0 and see the result.');
$I->sendGet($uri, ['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarInt with 0.0 and see the result.');
$I->sendGet($uri, ['value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
