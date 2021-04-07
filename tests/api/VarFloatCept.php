<?php

$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varFloat.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/varfloat/';

$I->wantTo('populate a VarFloat with text and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'text']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => "Invalid type (text), only 'float', 'integer' allowed in input 'value'.",
    "id" => 'test var_float process',
    ]]
);

$I->wantTo('populate a VarFloat with true and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'true']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => "Invalid type (boolean), only 'float', 'integer' allowed in input 'value'.",
    "id" => 'test var_float process',
    ]]
);

$I->wantTo('populate a VarFloat with 1.6 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarFloat with 1.6 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarFloat with 1 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarFloat with 1.0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarFloat with 0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarFloat with 0.0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varFloat.yaml');
$I->deleteHeader('Authorization');
