<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('varNum.yaml');
$I->createResourceFromYaml();

$I->wantTo('populate a VarInt with text and see the result.');
$I->callResourceFromYaml(['value' => 'text']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Invalid number: text.", "id" => 3]]);

$I->wantTo('populate a VarInt with true and see the result.');
$I->callResourceFromYaml(['value' => 'true']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Invalid number: true.", "id" => 3]]);

$I->wantTo('populate a VarInt with 1.6 and see the result.');
$I->callResourceFromYaml(['value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarInt with 1.6 and see the result.');
$I->callResourceFromYaml(['value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

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