<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('varInt.yaml');
$I->createResourceFromYaml();

$I->wantTo('populate a VarInt with text and see the result.');
$I->callResourceFromYaml(['value' => 'text']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => [
    "code" => 5,
    "message" => "Invalid value (text), only 'integer' allowed.",
    "id" => 3
]]);

$I->wantTo('populate a VarInt with true bool and see the result.');
$I->callResourceFromYaml(['value' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with true string and see the result.');
$I->callResourceFromYaml(['value' => 'true']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => [
    "code" => 5,
    "message" => "Invalid value (true), only 'integer' allowed.",
    "id" => 3
]]);

$I->wantTo('populate a VarInt with 1.6 and see the result.');
$I->callResourceFromYaml(['value' => 1.6]);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => [
    "code" => 5,
    "message" => "Invalid value (1.6), only 'integer' allowed.",
    "id" => 3
]]);

$I->wantTo('populate a VarInt with 1 and see the result.');
$I->callResourceFromYaml(['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with 1.0 and see the result.');
$I->callResourceFromYaml(['value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarInt with -11 and see the result.');
$I->callResourceFromYaml(['value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarInt with -11.0 and see the result.');
$I->callResourceFromYaml(['value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarInt with 0 and see the result.');
$I->callResourceFromYaml(['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarInt with 0.0 and see the result.');
$I->callResourceFromYaml(['value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->tearDownTestFromYaml();
