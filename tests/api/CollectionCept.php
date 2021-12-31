<?php

$I = new ApiTester($scenario);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$I->wantTo('create a collection of strings and see result');

$yaml = 'collectionStrings.yaml';
$uri = $I->getMyBaseUri() . '/collection/strings';
$I->createResourceFromYaml($yaml);

$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    "attr1",
    "attr2",
    "attr3",
    "attr4",
]);

$I->tearDownTestFromYaml($yaml);

$I->wantTo('create a collection of numbers and see result');

$yaml = 'collectionNumbers.yaml';
$uri = $I->getMyBaseUri() . '/collection/numbers';
$I->createResourceFromYaml($yaml);

$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    2,
    9999999999,
    -1,
    0,
    -3.345987345433624537,
]);

$I->tearDownTestFromYaml($yaml);

$I->wantTo('create a collection of fields and see result');

$yaml = 'collectionFields.yaml';
$uri = $I->getMyBaseUri() . '/collection/fields';
$I->createResourceFromYaml($yaml);

$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    "key1" => [
        "key2" => "val2",
    ],
]);

$I->tearDownTestFromYaml($yaml);

$I->wantTo('create a collection of mixed entities and see result');

$yaml = 'collectionMixed.yaml';
$uri = $I->getMyBaseUri() . '/collection/mixed';
$I->createResourceFromYaml($yaml);

$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
  null,
  1234567890,
  -1234567890,
  'val1',
  ['key2' => 'val2']
]);
$I->tearDownTestFromYaml($yaml);
