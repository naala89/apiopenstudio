<?php

$I = new ApiTester($scenario);
$I->wantTo('perform a successful login and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/2/user/login', ['username' => $I->getMyUsername(), 'password' => $I->getMyPassword()]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType(array('token' => 'string'));
$I->storeMyToken();

$I = new ApiTester($scenario);
$I->wantTo('perform a login with bad password see 401 with error object');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/2/user/login', ['username' => $I->getMyUsername(), 'password' => 'badpassword']);
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
$I->wantTo('perform a login with bad username see 401 with error object');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/2/user/login', ['username' => 'badusername', 'password' => $I->getMyPassword()]);
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
$I->sendPOST('/2/user/login', ['username' => $I->getMyUsername(), 'password' => $I->getMyPassword()]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType(array('token' => 'string'));
$I->storeMyToken();
$I->sendPOST('/2/user/login', ['username' => $I->getMyUsername(), 'password' => $I->getMyPassword()]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeTokenIsSameAsStoredToken();
