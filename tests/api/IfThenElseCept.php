<?php
$I = new ApiTester($scenario);

$I->setYamlFilename('ifThenElse.yaml');
$I->performLogin();
$I->createResourceFromYaml();

$I->wantTo('test IfThenElse == operator when < and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 200, 'operator' => '=='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse == operator when > and see result');
$I->callResourceFromYaml(array('lhs' => 200, 'rhs' => 100, 'operator' => '=='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse == operator when > and see result');
$I->callResourceFromYaml(array('lhs' => 200, 'rhs' => 100, 'operator' => '=='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse == operator when == and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 100, 'operator' => '=='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse == operator when == but not strict and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => '100', 'operator' => '=='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse != operator when < and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 200, 'operator' => '!='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse != operator when > and see result');
$I->callResourceFromYaml(array('lhs' => 200, 'rhs' => 100, 'operator' => '!='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse != operator when == and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 100, 'operator' => '!='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse != operator when == but not strict and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => '100', 'operator' => '!='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse < operator when < and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 200, 'operator' => '<'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse < operator when > and see result');
$I->callResourceFromYaml(array('lhs' => 200, 'rhs' => 100, 'operator' => '<'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse < operator when == and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 100, 'operator' => '<'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse < operator with letters when < and see result');
$I->callResourceFromYaml(array('lhs' => 'a', 'rhs' => 'b', 'operator' => '<'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse < operator with letters when > and see result');
$I->callResourceFromYaml(array('lhs' => 'b', 'rhs' => 'a', 'operator' => '<'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse < operator with letters when == and see result');
$I->callResourceFromYaml(array('lhs' => 'a', 'rhs' => 'a', 'operator' => '<'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse > operator when < and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 200, 'operator' => '>'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse > operator when > and see result');
$I->callResourceFromYaml(array('lhs' => 200, 'rhs' => 100, 'operator' => '>'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse > operator when == and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 100, 'operator' => '>'));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse >= operator when < and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 200, 'operator' => '>='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse >= operator when > and see result');
$I->callResourceFromYaml(array('lhs' => 200, 'rhs' => 100, 'operator' => '>='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse >= operator when == and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 100, 'operator' => '>='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse <= operator when < and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 200, 'operator' => '<='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse <= operator when > and see result');
$I->callResourceFromYaml(array('lhs' => 200, 'rhs' => 100, 'operator' => '<='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('false');

$I->wantTo('test IfThenElse <= operator when == and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 100, 'operator' => '<='));
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('test IfThenElse for bad operator and see result');
$I->callResourceFromYaml(array('lhs' => 100, 'rhs' => 100, 'operator' => '>>'));
$I->seeResponseCodeIs(417);
$I->seeResponseContainsJson(["error" => ["code" => 5, "message" => "Invalid value (>>). Only '==', '!=', '>', '>=', '<', '<=' allowed.", "id" => 3]]);

$I->tearDownTestFromYaml();
