<?php

// TODO

//$I = new ApiTester($scenario);
//$I->performLogin();
//
//$I->wantTo('create a collection of strings and see result');
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->createResourceFromYaml('collectionStrings.yaml');
//$I->deleteHeader('Authorization');
//$I->sendGet($I->getMyBaseUri() . '/collection/strings', ['token' => $I->getMyStoredToken()]);
//$I->seeResponseContainsJson([
//    "attr1",
//    "attr2",
//    "attr3",
//    "attr4",
//]);
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->tearDownTestFromYaml('collectionStrings.yaml');
//$I->deleteHeader('Authorization');
//
//$I->wantTo('create a collection of numbers and see result');
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->createResourceFromYaml('collectionNumbers.yaml');
//$I->deleteHeader('Authorization');
//$I->sendGet($I->getMyBaseUri() . '/collection/numbers', ['token' => $I->getMyStoredToken()]);
//$I->seeResponseContainsJson([
//    2,
//    9999999999,
//    -1,
//    0,
//    -3.345987345433624537,
//]);
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->tearDownTestFromYaml('collectionNumbers.yaml');
//$I->deleteHeader('Authorization');
//
//$I->wantTo('create a collection of fields and see result');
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->createResourceFromYaml('collectionFields.yaml');
//$I->deleteHeader('Authorization');
//$I->sendGet($I->getMyBaseUri() . '/collection/fields', ['token' => $I->getMyStoredToken()]);
//$I->seeResponseContainsJson([
//    "key1" => [
//        "key2" => "val2",
//    ],
//]);
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->tearDownTestFromYaml('collectionFields.yaml');
//$I->deleteHeader('Authorization');
//
//$I->doTestFromYaml('collectionMixed.yaml');
//$I->seeResponseContainsJson([
//  null,
//  1234567890,
//  -1234567890,
//  'val1',
//  ['key2' => 'val2']
//]);
//$I->tearDownTestFromYaml();
