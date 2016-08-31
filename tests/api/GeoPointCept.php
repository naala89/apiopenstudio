<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->setYamlFilename('geoPoint.yaml');
$I->createResourceFromYaml();

$I = new ApiTester($scenario);
$I->wantTo('Create a GeoPoint processor of 2 floats and see result');
$I->callResourceFromYaml(['lat' => 1.34, 'lon' => 3.141]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['lat' => 1.34, 'lon' => 3.141]);

$I = new ApiTester($scenario);
$I->wantTo('Create a GeoPoint processor of lat as a float and see result');
$I->callResourceFromYaml(['lat' => 1.34]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['lat' => 1.34, 'lon' => 0]);

$I = new ApiTester($scenario);
$I->wantTo('Create a GeoPoint processor of lon as a float and see result');
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['lat' => 0, 'lon' => 0]);

$I = new ApiTester($scenario);
$I->wantTo('Create a GeoPoint processor of a word and a float and see result');
$I->callResourceFromYaml(['lat' => 'three', 'lon' => 3.141]);
$I->seeResponseCodeIs(417);
$I->seeResponseIsJson();
$I->seeResponseContains("Invalid value (three), only 'float', 'integer' allowed.");


$I->tearDownTestFromYaml();
