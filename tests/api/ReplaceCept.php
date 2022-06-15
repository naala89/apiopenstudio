<?php

$I = new ApiTester($scenario);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

// Test replace on a text string

$yaml = 'replaceOnText.yaml';
$url = $I->getMyBaseUri() . '/replace/text';
$I->createResourceFromYaml($yaml);
$response = json_decode($I->getResponse(), true);
$rid = $response['data']['resid'];

$I->wantTo('Test a straight test replace with text on text with default ignore_case works.');
$I->sendGet($url, [
    'needle' => 'Lorem ipsum dolor sit amet',
    'value' => 'Start',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    // phpcs:ignore
    'data' => 'Start, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
]);

$I->wantTo('Test a straight test replace with text on text with ignore_case true works.');
$I->sendGet($url, [
    'needle' => 'lorem ipsum dolor sit amet',
    'value' => 'Start',
    'ignore_case' => true,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    // phpcs:ignore
    'data' => 'Start, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
]);

$I->wantTo('Test a straight test replace with text on text with ignore_case false fails.');
$I->sendGet($url, [
    'needle' => 'lorem ipsum dolor sit amet',
    'value' => 'Start',
    'ignore_case' => false,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    // phpcs:ignore
    'data' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
]);

$I->wantTo('Test a straight test replace with json on text with default ignore_case works.');
$I->sendGet($url, [
    'needle' => 'Lorem ipsum dolor sit amet',
    'value' => '[{"vid":10,"accid":2,"appid":null,"key":"varkey3","val":"varval3"}]',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    // phpcs:ignore
    'data' => '[{"vid":10,"accid":2,"appid":null,"key":"varkey3","val":"varval3"}], consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
]);

$I->wantTo('Test a straight test replace with xml on text with default ignore_case works.');
$I->sendGet($url, [
    'needle' => 'Lorem ipsum dolor sit amet',
    // phpcs:ignore
    'value' => "<?xml version='1.0' encoding='utf-8' ?><note><to>Tove</to><from>Jani</from><heading>Reminder</heading><body>Don't forget me this weekend!</body></note>",
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    // phpcs:ignore
    'data' => "<?xml version='1.0' encoding='utf-8' ?><note><to>Tove</to><from>Jani</from><heading>Reminder</heading><body>Don't forget me this weekend!</body></note>, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
]);

$I->wantTo('Test a straight test replace with a number on text with default ignore_case works.');
$I->sendGet($url, [
    'needle' => 'Lorem ipsum dolor sit amet',
    'value' => 10,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    // phpcs:ignore
    'data' => "10, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
]);

$I->sendDelete($I->getCoreBaseUri() . "/resource/$rid");

// Test replace on an xml string

$yaml = 'replaceOnXml.yaml';
$url = $I->getMyBaseUri() . '/replace/xml';
$I->createResourceFromYaml($yaml);
$response = json_decode($I->getResponse(), true);
$rid = $response['data']['resid'];

$I->wantTo('Test a straight test replace with text on text with default ignore_case works.');
$I->sendGet($url, [
    'needle' => '<from>Jani</from>',
    'value' => '<from>ApiOpenStudio</from>',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'note' => [
            'to' => 'Tove',
            'from' => 'ApiOpenStudio',
            'heading' => 'Reminder',
            'body' => "Don't forget me this weekend!",
        ],
    ],
]);

$I->wantTo('Test a straight test replace with text on text with ignore_case true works.');
$I->sendGet($url, [
    'needle' => '<from>Jani</from>',
    'value' => '<from>ApiOpenStudio</from>',
    'ignore_case' => true,
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'note' => [
            'to' => 'Tove',
            'from' => 'ApiOpenStudio',
            'heading' => 'Reminder',
            'body' => "Don't forget me this weekend!",
        ],
    ],
]);

$I->sendDelete($I->getCoreBaseUri() . "/resource/$rid");

// Test replace on a json string

$yaml = 'replaceOnJson.yaml';
$url = $I->getMyBaseUri() . '/replace/json';
$I->createResourceFromYaml($yaml);
$response = json_decode($I->getResponse(), true);
$rid = $response['data']['resid'];

$I->wantTo('Test a straight test replace with text on xml with default ignore_case works.');
$I->sendGet($url, [
    'needle' => 'Apple',
    'value' => 'pear',
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        "fruit" => "pear",
        "size" => "Large",
        "color" => "Red",
    ],
]);

$I->sendDelete($I->getCoreBaseUri() . "/resource/$rid");
