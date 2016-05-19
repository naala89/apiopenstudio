<?php 
$I = new ApiTester($scenario);
$I->wantTo('create an Object processor of literals and see result');

$I->doTestFromYaml('objectLiteralTest.yaml');
$I->seeResponseContainsJson(["field1","field2","field3"]);
$I->deleteResourceFromYaml();