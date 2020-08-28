<?php

$I = new ApiTester($scenario);
$I->wantTo('create a Field processor of literals and vars and see result');
$I->doTestFromYaml('fieldTest.yaml');
$I->seeResponseContainsJson(['myTestVar' => 'MyTestVal']);
$I->tearDownTestFromYaml();
