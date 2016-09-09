<?php 
$I = new ApiTester($scenario);
$I->wantTo('concatenate strings and see result');
$I->doTestFromYaml('concatenateStringsTest.yaml');
$I->seeResponseContains('field1field2field3');
/*
$I->tearDownTestFromYaml();

$I->wantTo('concatenate strings and numbers and see result');
$I->doTestFromYaml('concatenateStringsNumbersTest.yaml');
$I->seeResponseContains('field1field2field345.6');
$I->tearDownTestFromYaml();
*/