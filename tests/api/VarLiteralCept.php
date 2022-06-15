<?php

$I = new ApiTester($scenario);

$I->comment('Testing with text literal');
$yamlFilename = 'varLiteralText.yaml';
$uri = $I->getMyBaseUri() . '/var_literal/text';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->haveHttpHeader('Accept', 'application/json');

$I->wantTo('Test without type and see the result.');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'text',
]);

$I->wantTo('Test with type text and see the result.');
$I->sendGet($uri, ['type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'text',
]);

$I->wantTo('Test with type integer and see the result.');
$I->sendGet($uri, ['type' => 'integer']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'message' => "Cannot cast text to integer.",
        'id' => 'test var_literal text var_literal',
        'code' => 6,
    ],
]);

$I->wantTo('Test with type float and see the result.');
$I->sendGet($uri, ['type' => 'float']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'message' => "Cannot cast text to float.",
        'id' => 'test var_literal text var_literal',
        'code' => 6,
    ],
]);

$I->wantTo('Test with type array and see the result.');
$I->sendGet($uri, ['type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => ['text'],
]);

$I->wantTo('Test with type json and see the result.');
// Note this test will return null, because we have invalid json = this is an unquoted text string.
$I->sendGet($uri, ['type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'text',
]);

$I->wantTo('Test with type xml & JSON output and see the result.');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'item' => 'text'
        ],
    ],
]);

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/xml');

$I->wantTo('Test with type xml & XML output and see the result.');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<?xml version="1.0" encoding="utf-8"?>
<apiOpenStudioWrapper><item>text</item></apiOpenStudioWrapper>');

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/json');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);

$I->comment('Testing with integer literal');
$yamlFilename = 'varLiteralInteger.yaml';
$uri = $I->getMyBaseUri() . '/var_literal/integer';
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test without type and see the result.');
$I->sendGet($uri, ['type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34,
]);

$I->wantTo('Test with type text and see the result.');
$I->sendGet($uri, ['type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '34',
]);

$I->wantTo('Test with type integer and see the result.');
$I->sendGet($uri, ['type' => 'integer']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34,
]);

$I->wantTo('Test with type float and see the result.');
$I->sendGet($uri, ['type' => 'float']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34.0,
]);

$I->wantTo('Test with type array and see the result.');
$I->sendGet($uri, ['type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [34],
]);

$I->wantTo('Test with type json and see the result.');
$I->sendGet($uri, ['type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34,
]);

$I->wantTo('Test with type xml & JSON output and see the result.');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'item' => 34
        ],
    ],
]);

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/xml');

$I->wantTo('Test with type xml & XML output and see the result.');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<?xml version="1.0" encoding="utf-8"?>
<apiOpenStudioWrapper><item>34</item></apiOpenStudioWrapper>');

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/json');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);

$I->comment('Testing with float literal');
$yamlFilename = 'varLiteralFloat.yaml';
$uri = $I->getMyBaseUri() . '/var_literal/float';
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test without type and see the result.');
$I->sendGet($uri, ['type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34.141,
]);

$I->wantTo('Test with type text and see the result.');
$I->sendGet($uri, ['type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '34.141',
]);

$I->wantTo('Test with type integer and see the result.');
$I->sendGet($uri, ['type' => 'integer']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test var_literal float var_literal',
        'message' => "Cannot cast float to integer."
    ],
]);

$I->wantTo('Test with type float and see the result.');
$I->sendGet($uri, ['type' => 'float']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34.141,
]);

$I->wantTo('Test with type array and see the result.');
$I->sendGet($uri, ['type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [34.141],
]);

$I->wantTo('Test with type json and see the result.');
$I->sendGet($uri, ['type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 34.141,
]);

$I->wantTo('Test with type xml & JSON output and see the result.');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'item' => 34.141,
        ],
    ],
]);

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/xml');

$I->wantTo('Test with type xml & XML output and see the result.');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<?xml version="1.0" encoding="utf-8"?>
<apiOpenStudioWrapper><item>34.141</item></apiOpenStudioWrapper>');

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/json');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);

$I->comment('Testing with array literal');
$yamlFilename = 'varLiteralArray.yaml';
$uri = $I->getMyBaseUri() . '/var_literal/array';
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test without type and see the result.');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => ['pi' => 3.141],
]);

$I->wantTo('Test with type text and see the result.');
$I->sendGet($uri, ['type' => 'text']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test var_literal array var_literal',
        'message' => "Cannot cast array to text."
    ],
]);

$I->wantTo('Test with type integer and see the result.');
$I->sendGet($uri, ['type' => 'integer']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test var_literal array var_literal',
        'message' => "Cannot cast array to integer."
    ],
]);

$I->wantTo('Test with type float and see the result.');
$I->sendGet($uri, ['type' => 'float']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test var_literal array var_literal',
        'message' => "Cannot cast array to float."
    ],
]);

$I->wantTo('Test with type array and see the result.');
$I->sendGet($uri, ['type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => ['pi' => 3.141],
]);

$I->wantTo('Test with type json and see the result.');
$I->sendGet($uri, ['type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => ['pi' => 3.141],
]);

$I->wantTo('Test with type xml & JSON output and see the result.');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'pi' => '3.141',
        ],
    ],
]);

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/xml');

$I->wantTo('Test with type xml & XML output and see the result.');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet($uri, ['type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<?xml version="1.0"?>
<apiOpenStudioWrapper><pi>3.141</pi></apiOpenStudioWrapper>');

$I->deleteHeader('Accept');
$I->haveHttpHeader('Accept', 'application/json');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
