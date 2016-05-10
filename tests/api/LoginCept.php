<?php

$I = new ApiTester($scenario);
$I->wantTo('perform login and see result');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/4/user/login', ['username' => 'john', 'password' => 'jeweller']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType(array('token' => 'string'));
