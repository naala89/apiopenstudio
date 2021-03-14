<?php

$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varPost.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/varpost';

$I->wantTo('populate a varPost with text and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains("text");

$I->wantTo('populate a varPost with true and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains("true");

$I->wantTo('populate a varPost with 1.6 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varPost with 1.6 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a varPost with 1 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varPost with 1.0 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a varPost with -11 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => -11]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a varPost with -11.0 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => -11.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('-11');

$I->wantTo('populate a varPost with 0 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a varPost with 0.0 and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a varPost with wrong varname and see the result.');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken(), 'values' => 'test']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseEquals('""');

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varPost.yaml');
$I->deleteHeader('Authorization');
