<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Accept', 'application/json');

$I->wantTo('perform a non-inverse strict recursive filter by value on a list of fields');
$I->setYamlFilename('filterWithFieldsStrictValuesRecursiveNormal.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();

$I->wantTo('perform a non-inverse strict recursive filter by key on a list of fields');
$I->setYamlFilename('filterWithFieldsStrictKeyRecursiveNormal.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();
