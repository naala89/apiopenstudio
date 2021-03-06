<?php

$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varInt.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/varint';

$I->wantTo('populate a VarInt with text and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'text']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => "Invalid type (text), only 'integer' allowed in input 'value'.",
    "id" => 'test var_int process',
    ]]
);

$I->wantTo('populate a VarInt with true bool and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with true string and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'true']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => "Invalid type (boolean), only 'integer' allowed in input 'value'.",
    "id" => 'test var_int process',
    ]]
);

$I->wantTo('populate a VarInt with 1.6 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.6]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => "Invalid type (float), only 'integer' allowed in input 'value'.",
    "id" =>  'test var_int process'
    ]]
);

$I->wantTo('populate a VarInt with 1 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with 1.0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with -11 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarInt with -11.0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarInt with 0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarInt with 0.0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varInt.yaml');
$I->deleteHeader('Authorization');
