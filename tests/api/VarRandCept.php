<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('varRand.yaml');
$I->createResourceFromYaml();

$I->wantTo('test a varRand with no settings and see the result.');
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeReponseHasLength(8);

$I->wantTo('test a varRand with length settings and see the result.');
$I->callResourceFromYaml(['length' => 25]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeReponseHasLength(25);

$I->tearDownTestFromYaml();
