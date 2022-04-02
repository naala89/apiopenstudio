<?php

$I = new ApiTester($scenario);
$yamlFilename = 'varUri.yaml';
$uri = $I->getMyBaseUri() . '/varuri';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('populate a varUri with text and see the result.');
$I->sendGet($uri . '/text', ['index' => 'text']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'result' => 'error',
        'data' => [
            'code' => 6,
            'message' => "Failed to convert text to integer.",
            'id' => 'test varuri processor varget',
        ],
    ]
);

$I->wantTo('populate a varUri with true and see the result.');
$I->sendGet($uri . '/index1/index2', ['index' => 'true']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'result' => 'error',
        'data' => [
            'code' => 6,
            'message' => "Failed to convert text to integer.",
            'id' => 'test varuri processor varget',
        ],
    ]
);

$I->wantTo('populate a varUri with 1.6 and see the result.');
$I->sendGet($uri . '/index1/index2', ['index' => '1.6']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'result' => 'error',
        'data' => [
            'code' => 6,
            'message' => "Failed to convert text to integer.",
            'id' => 'test varuri processor varget',
        ],
    ]
);

$I->wantTo('populate a varUri with 1.6 and see the result.');
$I->sendGet($uri . '/index1/index2', ['index' => 1.6]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'result' => 'error',
        'data' => [
            'code' => 6,
            'message' => "Failed to convert text to integer.",
            'id' => 'test varuri processor varget',
        ],
    ]
);

$I->wantTo('populate a varUri with 0 and see the result.');
$I->sendGet($uri . '/index1/index2', ['index' => '0']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('index1');

$I->wantTo('populate a varUri with 1 and see the result.');
$I->sendGet($uri . '/index1/index2', ['index' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('index2');

$I->wantTo('populate a varUri with an invalid index as a string and see the result.');
$I->sendGet($uri . '/index1/index2', ['index' => '3']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'result' => 'error',
        'data' => [
            'code' => 6,
            'message' => "URI var does not exist or is empty: 3.",
            'id' => 'test varuri processor process',
        ],
    ]
);

$I->wantTo('populate a varUri with a float and see the result.');
$I->sendGet($uri . '/index1/index2', ['index' => 2.1]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'result' => 'error',
        'data' => [
            'code' => 6,
            'message' => "Failed to convert text to integer.",
            'id' => 'test varuri processor varget',
        ],
    ]
);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
