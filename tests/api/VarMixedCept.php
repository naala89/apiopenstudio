<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('varMixed.yaml');
$I->createResourceFromYaml();

$I->wantTo('populate a varMixed with text and see the result.');
$I->callResourceFromYaml(['value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('text');

$I->wantTo('populate a varMixed with true and see the result.');
$I->callResourceFromYaml(['value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a varMixed with 1.6 and see the result.');
$I->callResourceFromYaml(['value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varMixed with 1.6 and see the result.');
$I->callResourceFromYaml(['value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varMixed with 1 and see the result.');
$I->callResourceFromYaml(['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varMixed with 1.0 and see the result.');
$I->callResourceFromYaml(['value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varMixed with -11 and see the result.');
$I->callResourceFromYaml(['value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a varMixed with -11.0 and see the result.');
$I->callResourceFromYaml(['value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a varMixed with 0 and see the result.');
$I->callResourceFromYaml(['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a varMixed with 0.0 and see the result.');
$I->callResourceFromYaml(['value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->tearDownTestFromYaml();
