<?php

$I = new ApiTester($scenario);

$I->wantTo('create an Object with a simple array.');
$yamlFilename = 'varObjectArrayTest1.yaml';
$uri = '/object/array/1';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendGet($I->getMyBaseUri() . $uri);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test object array process',
        'message' => 'Cannot add attribute at index: 0. The attribute must be an array.',
    ],
]);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);

$I->wantTo('create an Object with a simple array with indexes.');
$yamlFilename = 'varObjectArrayTest2.yaml';
$uri = '/object/array/2';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendGet($I->getMyBaseUri() . $uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'field1',
        'field2',
        'field3',
        'field4',
    ],
]);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);

//$I->wantTo('create an Object with a simple array with indexes.');
//$yamlFilename = 'varObjectArrayTest3.yaml';
//$uri = '/object/array/3';
//$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
//$I->createResourceFromYaml($yamlFilename);
//$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
//$I->sendGet($I->getMyBaseUri() . $uri);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([
//    'result' => 'ok',
//    'data' => [
//        0 => 'field1',
//        5 => 'field2',
//        2 => 'field3',
//        3 => 'field4',
//    ],
//]);
//$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
//$I->tearDownTestFromYaml($yamlFilename);

//$I->wantTo('create an Object processor of complex fields with literals and see result');
//$yaml = 'objectFieldsComplexTest.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->createResourceFromYaml($yaml);
//$I->deleteHeader('Authorization');
//$uri = $I->getMyBaseUri() . '/object/fields/complex';
//$I->sendGet($uri, ['token' => $I->getMyStoredToken()]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([
//    "key1" => [
//        "key1_1" => "value1_1",
//    ],
//    "key2" => "field2",
//    "key3" => "field3",
//    "key4" => "field4"
//]);
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->tearDownTestFromYaml($yaml);
//$I->deleteHeader('Authorization');
//
//$I->wantTo('create an Object processor of fields with literals and see result');
//$yaml = 'objectArrayTest.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->createResourceFromYaml($yaml);
//$I->deleteHeader('Authorization');
//$uri = $I->getMyBaseUri() . '/object/array';
//$I->sendGet($uri, ['token' => $I->getMyStoredToken()]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([
//    "key1" => "field1",
//    "key2" => "field2",
//    "key3" => "field3",
//    "key4" => "field4"
//]);
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->tearDownTestFromYaml($yaml);
//$I->deleteHeader('Authorization');
//
//$I->wantTo('create an Object processor of complex arrays with literals and see result');
//$yaml = 'objectArrayComplexTest.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->createResourceFromYaml($yaml);
//$I->deleteHeader('Authorization');
//$uri = $I->getMyBaseUri() . '/object/array/complex';
//$I->sendGet($uri, ['token' => $I->getMyStoredToken()]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([
//    "key1" => [
//        "key1_1" => "val1_1",
//    ],
//    "key2" => "field2",
//    "key3" => "field3",
//    "key4" => "field4"
//]);
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->tearDownTestFromYaml($yaml);
//$I->deleteHeader('Authorization');
