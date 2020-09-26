<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varBool.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/varbool';

$I->wantTo('populate a VarBool with 1 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => '1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => '0']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with 1 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with yes and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'yes']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with no and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'no']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with true and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'false']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with 6 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 6]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => [
    "code" => 6,
    "message" => "6 is not boolean.",
    "id" => 'test var_bool process',
]]);

$I->wantTo('populate a VarBool with fales and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'fales']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => [
    "code" => 6,
    "message" => "Fales is not boolean.",
    "id" => 'test var_bool process'
]]);

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varBool.yaml');
$I->deleteHeader('Authorization');
