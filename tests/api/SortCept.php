<?php
$I = new ApiTester($scenario);
$I->performLogin();

$I->wantTo('Sort an array of single values in ascending order and see the result.');
$I->setYamlFilename('sortArrayOfSingular.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'asc']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  "field1",
  "field2",
  "field3",
  "field4",
  1,
  5]);
$I->tearDownTestFromYaml();

$I->wantTo('Sort an array of mixed values of single and Field in ascending order and see the result.');
$I->setYamlFilename('sortArrayOfSingularWithField.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'asc']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  'field1',
  'field2',
  'field3',
  'field4',
  ['key' => 'value'],
  5]);
$I->tearDownTestFromYaml();

$I->wantTo('Sort an array of mixed values of single and processors in ascending order and see the result.');
$I->setYamlFilename('sortArrayOfSingularAndProcessors.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'asc']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  "field1",
  "field2",
  "field3",
  "testStr",
  1,
  3.4562599999999999,
  5
]);

$I->tearDownTestFromYaml();

$I->wantTo('Sort an Object of Fields by key in ascending order and see the result.');
$I->setYamlFilename('sortObjectOfFields.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'asc', 'sortByValue' => 'false']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  'key1' => 'val99',
  'key2' => 'val5',
  'key3' => 'val3',
  'key4' => 'val2',
  'key999' => 'val1'
]);

$I->wantTo('Sort an Object of Fields by key in descending order and see the result.');
$I->callResourceFromYaml(['direction' => 'desc', 'sortByValue' => 'false']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  'key999' => 'val1',
  'key4' => 'val2',
  'key3' => 'val3',
  'key2' => 'val5',
  'key1' => 'val99',
]);

$I->wantTo('Sort an Object of Fields by value in descending order and see the result.');
$I->callResourceFromYaml(['direction' => 'desc', 'sortByValue' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  "key1" => "val99",
  "key2" => "val5",
  "key3" => "val3",
  "key4" => "val2",
  "key999" => "val1"
]);

$I->tearDownTestFromYaml();
