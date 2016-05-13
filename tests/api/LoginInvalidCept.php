<?php

$I = new ApiTester($scenario);
$I->wantTo('perform an invalid login and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/4/user/login', ['username' => 'tester', 'password' => 'wrong_passs']);
$I->seeResponseCodeIs(401);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array(
  'error' => array(
    'code' => 4,
    'message' => 'Invalid username or password.',
    'id' => -1
  )
));
