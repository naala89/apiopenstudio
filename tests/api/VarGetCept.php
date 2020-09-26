<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->createResourceFromYaml('varGet.yaml');
$I->deleteHeader('Authorization');

$uri = $I->getMyBaseUri() . '/varget';

$I->wantTo('populate a VarGet with text and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('text');

$I->wantTo('populate a VarGet with true and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');

$I->wantTo('populate a VarGet with 1.6 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => '1.6']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarGet with 1.6 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.6]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1.6');

$I->wantTo('populate a VarGet with 1 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarGet with 1.0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 1]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('1');

$I->wantTo('populate a VarGet with 0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarGet with 0.0 and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'value' => 0.0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('0');

$I->wantTo('populate a VarGet with wrong varname and nullable true and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'values' => 'test', 'nullable' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseEquals('""');

$I->wantTo('populate a VarGet with wrong varname and nullable false and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'values' => 'test', 'nullable' => false]);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => [
    'code' => 6,
    'message' => "GET variable (value) not received.",
    'id' => 'test var_get process'
]]);

$I->wantTo('populate a VarGet with wrong varname and nullable not set and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken(), 'values' => 'test']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => [
    'code' => 6,
    'message' => "GET variable (value) not received.",
    'id' => 'test var_get process'
]]);

$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml('varGet.yaml');
$I->deleteHeader('Authorization');
