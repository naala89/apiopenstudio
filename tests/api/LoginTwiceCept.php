<?php

$I = new ApiTester($scenario);
$I->wantTo('validate that token is not recreated before ttl expires');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/4/user/login', ['username' => 'tester', 'password' => 'tester_pass']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType(array('token' => 'string'));
$I->storeToken();

$I->sendPOST('/4/user/login', ['username' => 'tester', 'password' => 'tester_pass']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->compareToken();