<?php
$I = new ApiTester($scenario);
$I->performLogin();

$I->wantTo('Sort a Collection by value in ascending order and see the result.');
$I->setYamlFilename('sortCollection.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'asc', 'sortby' => 'value']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  "field1",
  "field2",
  "field3",
  "field4",
  1,
  5]);

$I->wantTo('Sort a Collection by value in descending order and see the result.');
$I->callResourceFromYaml(['direction' => 'desc', 'sortby' => 'value']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  5,
  1,
  "field4",
  "field3",
  "field2",
  "field1"]);

$I->wantTo('Sort a Collection by key in ascending order and see the result.');
$I->callResourceFromYaml(['direction' => 'asc', 'sortby' => 'key']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  "field1",
  "field4",
  "field2",
  "field3",
  1,
  5]);

$I->wantTo('Sort a Collection by key in descending order and see the result.');
$I->callResourceFromYaml(['direction' => 'desc', 'sortby' => 'key']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  5,
  1,
  "field3",
  "field2",
  "field4",
  "field1"]);
$I->tearDownTestFromYaml();

$I->wantTo('Sort multiple values by value in ascending order and see the result.');
$I->setYamlFilename('sortMultiple.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'asc', 'sortby' => 'value']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  "field1",
  "field2",
  "field3",
  "field4",
  1,
  5]);

$I->wantTo('Sort multiple values by value in descending order and see the result.');
$I->callResourceFromYaml(['direction' => 'desc', 'sortby' => 'value']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  5,
  1,
  "field4",
  "field3",
  "field2",
  "field1"]);

$I->wantTo('Sort multiple values by key in ascending order and see the result.');
$I->callResourceFromYaml(['direction' => 'asc', 'sortby' => 'key']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  "field1",
  "field4",
  "field2",
  "field3",
  1,
  5]);

$I->wantTo('Sort multiple values by key in descending order and see the result.');
$I->callResourceFromYaml(['direction' => 'desc', 'sortby' => 'key']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  5,
  1,
  "field3",
  "field2",
  "field4",
  "field1"]);
$I->tearDownTestFromYaml();

$I->wantTo('Sort an Object by value in ascending order and see the result.');
$I->setYamlFilename('sortObject.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'asc', 'sortby' => 'value']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  'key999' => 'val1',
  'key4' => 'val2',
  'key3' => 'val3',
  'key2' => 'val5',
  'key1' => 'val99']);

$I->wantTo('Sort an Object by value in descending order and see the result.');
$I->callResourceFromYaml(['direction' => 'desc', 'sortby' => 'value']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  'key1' => 'val99',
  'key2' => 'val5',
  'key3' => 'val3',
  'key4' => 'val2',
  'key999' => 'val1']);

$I->wantTo('Sort an Object by key in asscending order and see the result.');
$I->callResourceFromYaml(['direction' => 'asc', 'sortby' => 'key']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  'key1' => 'val99',
  'key2' => 'val5',
  'key3' => 'val3',
  'key4' => 'val2',
  'key999' => 'val1']);

$I->wantTo('Sort an Object by key in descending order and see the result.');
$I->setYamlFilename('sortObject.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml(['direction' => 'desc', 'sortby' => 'key']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  'key999' => 'val1',
  'key4' => 'val2',
  'key3' => 'val3',
  'key2' => 'val5',
  'key1' => 'val99']);

$I->tearDownTestFromYaml();
