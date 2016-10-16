<?php
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Accept', 'application/json');

/**
 * filter by key
 */

$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, inverse, non-recursive');
$I->setYamlFilename('filterFieldsKeyNonregexInverseNonrecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([2 => ['key3' => 'val3'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();
/*
$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, inverse, recursive');
$I->setYamlFilename('filterFieldsKeyNonregexInverseRecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResult();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 3 => ['key4' => 'val4'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();

$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, non-inverse, non-recursive');
$I->setYamlFilename('filterFieldsKeyNonregexNoninverseNonrecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResult();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 3 => ['key4' => 'val4'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();

$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, non-inverse, recursive');
$I->setYamlFilename('filterFieldsKeyNonregexNoninverseRecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResult();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 3 => ['key4' => 'val4'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();

$I->wantTo('Test Filter by filtering by key on an array of fields with settings regex, inverse, non-recursive');
$I->setYamlFilename('filterFieldsKeyRegexInverseNonrecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResult();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 3 => ['key4' => 'val4'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();

$I->wantTo('Test Filter by filtering by key on an array of fields with settings regex, inverse, recursive');
$I->setYamlFilename('filterFieldsKeyRegexInverseRecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResult();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 3 => ['key4' => 'val4'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();

$I->wantTo('Test Filter by filtering by key on an array of fields with settings regex, non-inverse, non-recursive');
$I->setYamlFilename('filterFieldsKeyRegexNoninverseNonrecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResult();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 3 => ['key4' => 'val4'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();

$I->wantTo('Test Filter by filtering by key on an array of fields with settings regex, non-inverse, recursive');
$I->setYamlFilename('filterFieldsKeyRegexNoninverseRecursive.yaml');
$I->createResourceFromYaml();
$I->callResourceFromYaml();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResult();
$I->seeResponseContainsJson([0 => ['key1' => 'val1'], 1 => ['key2' => 'val2'], 2 => ['key3' => 'val3'], 3 => ['key4' => 'val4'], 4 => ['key5' => 'val5']]);
//$I->tearDownTestFromYaml();
*/