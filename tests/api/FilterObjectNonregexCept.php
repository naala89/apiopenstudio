<?php

// TODO

//$I = new ApiTester($scenario);
//$I->performLogin();
//$I->haveHttpHeader('Accept', 'application/json');
//
//$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, non-inverse, non-recursive');
//$I->setYamlFilename('filterObjectNonRegex.yaml');
//$I->createResourceFromYaml();
//$I->callResourceFromYaml(['keyOrValue' => 'key', 'regex' => false, 'inverse' => false, 'recursive' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(['key2' => 'val2', 'key3' => 'val3', 'key5' => ['key6' => 'val6', 'key7' => 'val7']]);
//
//$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, non-inverse, recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'key', 'regex' => false, 'inverse' => false, 'recursive' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(['key2' => 'val2', 'key3' => 'val3', 'key5' => ['key7' => 'val7']]);
//
//$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, inverse, non-recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'key', 'regex' => false, 'inverse' => true, 'recursive' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(['key1' => 'val1', 'key4' => 'val4']);
//
//$I->wantTo('Test Filter by filtering by key on an array of fields with settings non-regex, inverse, recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'key', 'regex' => false, 'inverse' => true, 'recursive' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(['key1' => 'val1', 'key4' => 'val4']);
//
//$I->wantTo('Test Filter by filtering by key on an array of fields with settings regex, non-inverse, non-recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'key', 'regex' => true, 'inverse' => false, 'recursive' => false]);
//$I->seeResponseCodeIs(417);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(["error" => [
//    "id" => 3,
//    "code" => 0,
//    "message" => "Cannot have an array of regexes as a filter."
//]]);
//
//// phpcs:ignore
//$I->wantTo('Test Filter by filtering by value on an array of fields with settings non-regex, non-inverse, non-recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'value', 'regex' => false, 'inverse' => false, 'recursive' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([
//    'key1' => 'val1',
//    'key2' => 'val2',
//    'key4' => 'val4',
//    'key5' => [
//        'key6' => 'val6',
//        'key7' => 'val7'
//    ]
//]);
//
//$I->wantTo('Test Filter by filtering by value on an array of fields with settings non-regex, non-inverse, recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'value', 'regex' => false, 'inverse' => false, 'recursive' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(['key1' => 'val1', 'key2' => 'val2', 'key4' => 'val4', 'key5' => ['key6' => 'val6']]);
//
//$I->wantTo('Test Filter by filtering by value on an array of fields with settings non-regex, inverse, non-recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'value', 'regex' => false, 'inverse' => true, 'recursive' => false]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(['key3' => 'val3']);
//
//$I->wantTo('Test Filter by filtering by value on an array of fields with settings non-regex, inverse, recursive');
//$I->callResourceFromYaml(['keyOrValue' => 'value', 'regex' => false, 'inverse' => true, 'recursive' => true]);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(['key3' => 'val3']);
//
//$I->tearDownTestFromYaml();
