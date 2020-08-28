<?php

$I = new ApiTester($scenario);
$I->wantTo('create a collection of strings and see result');
$I->doTestFromYaml('collectionStrings.yaml');
$I->seeResponseContainsJson([
    "attr1",
    "attr2",
    "attr3",
    "attr4"]
);
$I->tearDownTestFromYaml();

$I->doTestFromYaml('collectionNumbers.yaml');
$I->seeResponseContainsJson([
    2,
    9999999999,
    -1,
    0,
    -3.345987345433624537]
);
$I->tearDownTestFromYaml();

$I->doTestFromYaml('collectionFields.yaml');
$I->seeResponseContainsJson(
  ["key1" => ["key2" => "val2"]]
);
$I->tearDownTestFromYaml();

$I->doTestFromYaml('collectionMixed.yaml');
$I->seeResponseContainsJson([
  null,
  1234567890,
  -1234567890,
  'val1',
  ['key2' => 'val2']
]);
$I->tearDownTestFromYaml();
