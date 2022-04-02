<?php

use function PHPUnit\Framework\assertEquals;

$I = new ApiTester($scenario);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml('url.yaml');
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$uri = $I->getMyBaseUri() . '/url/';

$I->wantTo('populate a Url with correct inputs (no auth) and see the result.');
$I->sendGet(
    $uri,
    [
        'method' => 'get',
        'url' => 'jsonplaceholder.typicode.com/posts/1',
        'source_type' => 'json',
        'report_error' => true,
        'connect_timeout' => 10,
        'timeout' => 30
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$array = json_decode($I->getResponse(), true);
assertEquals('ok', $array['result'], 'Assert we have an ok condition.');
assertEquals(
    [
        'userId' => 1,
        'id' => 1,
        'title' => 'sunt aut facere repellat provident occaecati excepturi optio reprehenderit',
        // phpcs:ignore
        'body' => "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto",
    ],
    json_decode($array['data'], true),
    'Assert the JSON response is correct.'
);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml('url.yaml');
$I->deleteHeader('Authorization');
