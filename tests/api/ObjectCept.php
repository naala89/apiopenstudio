<?php

// TODO

//$I = new ApiTester($scenario);
//$I->performLogin();
//
//$I->wantTo('create an Object processor of fields with literals and see result');
//$yaml = 'objectFieldsTest.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->createResourceFromYaml($yaml);
//$I->deleteHeader('Authorization');
//$uri = $I->getMyBaseUri() . '/object/fields';
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
