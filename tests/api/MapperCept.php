<?php
$I = new ApiTester($scenario);
$I->performLogin();
$baseUrl = $I->getBaseUrl() . 'html/sample/';

$I->wantTo('Test mapper with json and see the result');
$I->haveHttpHeader('Accept', 'application/json');
$I->setYamlFilename('mapperJson.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml([]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['key2' => 'val2', 'key3' => 'val3', 'key5' => ['key6' => 'val6', 'key7' => 'val7']]);