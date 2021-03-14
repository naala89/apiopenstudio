<?php

$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('url.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/url';

$I->wantTo('populate a Url with correct inputs (no auth) and see the result.');
$I->sendGet(
    $uri,
    [
        'token' => $I->getMyStoredToken(),
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
// phpcs:ignore
$I->seeResponseContainsJson([
    "userId" => 1,
    "id" => 1,
    "title" => "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
        // phpcs:ignore
    "body" => "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"
    ]
);

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('url.yaml');
$I->deleteHeader('Authorization');
