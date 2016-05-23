<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('varPost.yaml');
$I->createResourceFromYaml();

$I->wantTo('populate a varPost with text and see the result.');
$I->callResourceFromYaml(['value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('text');

$I->wantTo('populate a varPost with true and see the result.');
$I->callResourceFromYaml(['value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a varPost with 1.6 and see the result.');
$I->callResourceFromYaml(['value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varPost with 1.6 and see the result.');
$I->callResourceFromYaml(['value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varPost with 1 and see the result.');
$I->callResourceFromYaml(['value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varPost with 1.0 and see the result.');
$I->callResourceFromYaml(['value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varPost with 0 and see the result.');
$I->callResourceFromYaml(['value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a varPost with 0.0 and see the result.');
$I->callResourceFromYaml(['value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a varPost with wrong varname and see the result.');
$I->callResourceFromYaml(['values' => 'test']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('null');

$I->tearDownTestFromYaml();