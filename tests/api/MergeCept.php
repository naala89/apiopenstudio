<?php 
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('merge.yaml');
$I->createResourceFromYaml();
$I->haveHttpHeader('Accept', 'application/json');

$I->wantTo('perform a merge of type union without unique set and see result');
$I->callResourceFromYaml(['mergeType' => 'union']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["val1","val2","val3","val4","val1","val5","val6","val7","val8"]);

$I->wantTo('perform a merge of type union with unique set and see result');
$I->callResourceFromYaml(['mergeType' => 'union', 'unique' => 'true']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["0" => "val1","1" => "val2","2" => "val3","3" => "val4","5" => "val5","6" => "val6","7" => "val7","8" => "val8"]);

$I->wantTo('perform a merge of type intersect without unique set and see result');
$I->callResourceFromYaml(['mergeType' => 'intersect']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["val1"]);

$I->wantTo('perform a merge of type difference without unique set and see result');
$I->callResourceFromYaml(['mergeType' => 'difference']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["val2","val3","val4","val5","val6","val7","val8"]);

$I->tearDownTestFromYaml();