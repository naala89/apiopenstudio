<?php

$I = new ApiTester($scenario);

$I->performLogin();

$uri = $I->getMyBaseUri() . '/field/';

$I->wantTo('create a VarField processor of literals and vars and see result');
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varField.yaml');
$I->deleteHeader('Authorization');
$I->sendGet($uri, ['token' => $I->getMyStoredToken()]);
$I->seeResponseContainsJson(
    [
    'my_test_var' => 'my_test_val',
    ]
);

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varField.yaml');
$I->deleteHeader('Authorization');
