<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Accept', 'application/json');

$I->wantTo('concatenate values from fragments and see the result');
$I->setYamlFilename('fragment.yaml');
$I->createResourceFromYaml();
$uri = '/' . $I->getMyApplicationName() . '/test/fragments';
$I->sendGet($uri . '/test/fragments', ['token' => $I->getMyStoredToken(), 'val1' => 1, 'val2' => 2, 'val3' => 3]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->canSeeResponseContains('123145');
$I->tearDownTestFromYaml();
