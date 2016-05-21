<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Accept', 'application/json');

$I->wantTo('perform a filter of a single value from a list of values');
$I->setYamlFilename('filterWithSingle.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([1 => "val2", 2 => "val3", 3 => "val4", 4 => "val5", 5 => "val6", 6 => "val7", 7 => "val8"]);
$I->tearDownTestFromYaml();

$I->wantTo('perform a filter of multiple values from a list of values');
$I->setYamlFilename('filterWithMultiple.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([1 => "val2", 3 => "val4", 4 => "val5", 5 => "val6", 6 => "val7", 7 => "val8"]);
$I->tearDownTestFromYaml();

$I->wantTo('perform a filter on a list of mixed fields and values');
$I->setYamlFilename('filterWithMixed.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['key2' => 'val2', 'key3' => 'val3', 0 => 'val4', 1 => 'val5']);
$I->tearDownTestFromYaml();

$I->wantTo('perform a filter of multiple keys from a list of Fields');
$I->setYamlFilename('filterWithFields.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['key2' => 'val2', 'key3' => 'val3', 'key4' => 'val4', 'key5' => 'val5',]);
$I->tearDownTestFromYaml();
