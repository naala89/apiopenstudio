<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('varBool.yaml');

$I->wantTo('populate a VarBool with 1 and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => '1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => '0']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with 1 and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with yes and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 'yes']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with no and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 'no']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with true and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarBool with 0 and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 'false']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('false');

$I->wantTo('populate a VarBool with 6 and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => '6']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 5, "message" => "Invalid boolean.", "id" => 3]]);

$I->wantTo('populate a VarBool with 6 and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 6]);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 5, "message" => "Invalid boolean.", "id" => 3]]);

$I->wantTo('populate a VarBool with fales and see the result.');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['value' => 'fales']);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 5, "message" => "Invalid boolean.", "id" => 3]]);

$I->tearDownTestFromYaml();