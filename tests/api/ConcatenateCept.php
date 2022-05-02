<?php

$I = new ApiTester($scenario);

$I->wantTo('Concatenate strings and see result');
$yamlFilename = 'concatenateStrings.yaml';
$uri = $I->getMyBaseUri() . '/concatenate/strings';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'field1field2field3',
]);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);

$I->wantTo('Concatenate strings and numbers and see result');
$yamlFilename = 'concatenateStringsNumbers.yaml';
$uri = $I->getMyBaseUri() . '/concatenate/mixed';
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'field1field2field345.6',
]);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);

$I->wantTo('Concatenate numbers and see result');
$yamlFilename = 'concatenateNumbers.yaml';
$uri = $I->getMyBaseUri() . '/concatenate/numbers';
$I->createResourceFromYaml($yamlFilename);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '30.141705',
]);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
