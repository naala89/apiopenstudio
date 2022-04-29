<?php

use function PHPUnit\Framework\assertEquals;

$I = new ApiTester($scenario);

$I->wantTo('Test that max_loops will end an do...while loop prematurely.');
$yaml = 'doWhileMaxLoops.yaml';
$uri = '/do_while/max_loops';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yaml);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendGet($I->getMyBaseUri() . $uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$array = json_decode($I->getResponse(), true);
assertEquals('ok', $array['result'], 'validate result is ok.');
assertEquals('50', $array['data'][0]['val'], 'validate data value is 50.');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yaml);

$I->wantTo('Test that do...while will correctly run n amount of times.');
$yaml = 'doWhileComparison.yaml';
$uri = '/do_while/counter_loops';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yaml);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->sendGet($I->getMyBaseUri() . $uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$array = json_decode($I->getResponse(), true);
assertEquals('ok', $array['result'], 'validate result is ok.');
assertEquals('70', $array['data'][0]['val'], 'validate data value is 70.');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yaml);

// Clean up
$uri = $I->getCoreBaseUri() . '/var_store';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->sendGet($uri, ['appid' => 2]);
$varStores = json_decode($I->getResponse(), true);
foreach ($varStores['data'] as $varStore) {
    $I->sendDelete($uri . '/' . $varStore['vid']);
}
