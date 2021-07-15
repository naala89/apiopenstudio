<?php

$I = new ApiTester($scenario);
$yamlFilename = 'varRand.yaml';
$uri = $I->getMyBaseUri() . '/varrand';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('test a varRand with no settings and see the result.');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeReponseHasLength(8);

$I->wantTo('test a varRand with length settings and see the result.');
$I->sendGet($uri, ['length' => 25, 'special' => true]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeReponseHasLength(25);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
