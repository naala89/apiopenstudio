<?php

$I = new ApiTester($scenario);
$I->wantTo('create an Object processor of fields and literals and see result');
$I->doTestFromYaml('objectFieldsTest.yaml');
$I->seeResponseContainsJson(["field1" => "field1", "field2" => "field2", "field3" => "field3", "field4" => "field4"]);
$I->tearDownTestFromYaml();
