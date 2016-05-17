<?php

$I = new ApiTester($scenario);
$I->wantTo('perform a successful login and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/1/user/login', ['username' => 'tester', 'password' => 'tester_pass']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType(array('token' => 'string'));
$I->storeMyToken();

$I = new ApiTester($scenario);
$I->wantTo('perform an invalid login and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/1/user/login', ['username' => 'tester', 'password' => 'wrong_passs']);
$I->seeResponseCodeIs(401);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array(
  'error' => array(
    'code' => 4,
    'message' => 'Invalid username or password.',
    'id' => -1
  )
));

$I = new ApiTester($scenario);
$I->wantTo('validate that token is not recreated before ttl expires');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/1/user/login', ['username' => 'tester', 'password' => 'tester_pass']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType(array('token' => 'string'));
$I->storeMyToken();
$I->sendPOST('/1/user/login', ['username' => 'tester', 'password' => 'tester_pass']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeTokenIsSameAsStoredToken();
