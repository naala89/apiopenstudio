<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('url.yaml');

$I->wantTo('populate a Url with correct inputs (no auth) and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml([
  'method' => 'get',
  'url' => 'jsonplaceholder.typicode.com/posts/1',
  'sourceType' => 'json',
  'reportError' => true,
  'connectTimeout' => 10,
  'timeout' => 30
]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
// phpcs:ignore
$I->seeResponseContains('{"userId": 1, "id": 1, "title": "sunt aut facere repellat provident occaecati excepturi optio reprehenderit", "body": "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"}');

$I->tearDownTestFromYaml();
