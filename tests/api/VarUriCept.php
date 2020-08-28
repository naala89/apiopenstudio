<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('varUri.yaml');
$I->createResourceFromYaml();
$uri = '/' . $I->getMyApplicationName() . '/varuri';

$I->wantTo('populate a varUri with text and see the result.');
$I->sendGet($uri . '/text', ['token' => $I->getMyStoredToken(), 'index' => 'text']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => [
    'code' => 5,
    'message' => "Invalid value (text), only 'integer' allowed.",
    'id' => 3
]]);

$I->wantTo('populate a varUri with true and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'index' => 'true']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => [
    'code' => 5,
    'message' => "Invalid value (true), only 'integer' allowed.",
    'id' => 3
]]);

$I->wantTo('populate a varUri with 1.6 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'index' => '1.6']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => [
    'code' => 5,
    'message' => "Invalid value (1.6), only 'integer' allowed.",
    'id' => 3
]]);

$I->wantTo('populate a varUri with 1.6 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'index' => 1.6]);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => [
    'code' => 5,
    'message' => "Invalid value (1.6), only 'integer' allowed.",
    'id' => 3
]]);

$I->wantTo('populate a varUri with 0 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'index' => '0']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('index1');

$I->wantTo('populate a varUri with 1 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'index' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('index2');

$I->wantTo('populate a varUri with 1 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'index' => '3']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'URI index "3" does not exist.', 'id' => 3]]);

$I->wantTo('populate a varUri with 1 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'index' => 3]);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'URI index "3" does not exist.', 'id' => 3]]);

$I->tearDownTestFromYaml();
