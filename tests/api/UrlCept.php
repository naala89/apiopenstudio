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
$I->seeResponseContains('true');

$I->tearDownTestFromYaml();
