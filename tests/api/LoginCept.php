<?php

$I = new ApiTester($scenario);
$I->wantTo('perform a successful login and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/4/user/login', ['username' => 'tester', 'password' => 'tester_pass']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType(array('token' => 'string'));
