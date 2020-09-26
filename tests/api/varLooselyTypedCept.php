<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varLooselyTyped.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/varlooselytyped';

$I->wantTo('populate a VarLooselyTyped with text and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('text');

$I->wantTo('populate a VarLooselyTyped with true and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarLooselyTyped with 1.6 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarLooselyTyped with 1.6 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarLooselyTyped with 1 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarLooselyTyped with 1.0 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarLooselyTyped with -11 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarLooselyTyped with -11.0 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a VarLooselyTyped with 0 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarLooselyTyped with 0.0 and see the result.');
$I->sendGet($uri . '/index1/index2', ['token' => $I->getMyStoredToken(), 'value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varLooselyTyped.yaml');
$I->deleteHeader('Authorization');
