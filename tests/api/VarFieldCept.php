<?php

$I = new ApiTester($scenario);
$uri = $I->getMyBaseUri() . '/field/';
$yamlFilename = 'varField.yaml';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('create a VarField processor of literals and vars and see result');
$I->sendGet($uri);
$I->seeResponseContainsJson(
    [
        'my_test_var' => 'my_test_val',
    ]
);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
