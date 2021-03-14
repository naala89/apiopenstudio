<?php

$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varRand.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/varrand';

$I->wantTo('test a varRand with no settings and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken()]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeReponseHasLength(8);

$I->wantTo('test a varRand with length settings and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'length' => 25, 'special' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeReponseHasLength(25);

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varRand.yaml');
$I->deleteHeader('Authorization');
