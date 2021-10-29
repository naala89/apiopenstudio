<?php

$I = new ApiTester($scenario);
$uri = $I->getMyBaseUri() . '/sort/collection/';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml('sortCollection.yaml');
$I->deleteHeader('Authorization');

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Sort a Collection by value in ascending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'asc',
        'sort_by' => 'value',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "field1",
        "field2",
        "field3",
        "field4",
        1,
        5,
    ]
);

$I->wantTo('Sort a Collection by value in descending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'desc',
        'sort_by' => 'value',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        5,
        1,
        "field4",
        "field3",
        "field2",
        "field1",
    ]
);

$I->wantTo('Sort a Collection by key in ascending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'asc',
        'sort_by' => 'key',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "field1",
        "field4",
        "field2",
        "field3",
        1,
        5,
    ]
);

$I->wantTo('Sort a Collection by key in descending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'desc',
        'sort_by' => 'key',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        5,
        1,
        "field3",
        "field2",
        "field4",
        "field1",
    ]
);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml('sortCollection.yaml');
$I->createResourceFromYaml('sortArray.yaml');
$I->deleteHeader('Authorization');

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$uri = $I->getMyBaseUri() . '/sort/array/';

$I->wantTo('Sort multiple values by value in ascending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'asc',
        'sort_by' => 'value',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "field1",
        "field2",
        "field3",
        "field4",
        1,
        5,
    ]
);

$I->wantTo('Sort multiple values by value in descending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'desc',
        'sort_by' => 'value',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        5,
        1,
        "field4",
        "field3",
        "field2",
        "field1",
    ]
);

$I->wantTo('Sort multiple values by key in ascending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'asc',
        'sort_by' => 'key',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "field1",
        "field4",
        "field2",
        "field3",
        1,
        5,
    ]
);

$I->wantTo('Sort multiple values by key in descending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'desc',
        'sort_by' => 'key',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        5,
        1,
        "field3",
        "field2",
        "field4",
        "field1",
    ]
);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml('sortArray.yaml');
$I->createResourceFromYaml('sortObject.yaml');
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$uri = $I->getMyBaseUri() . '/sort/object/';

$I->wantTo('Sort an Object by value in ascending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'asc',
        'sort_by' => 'value',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'key999' => 'val1',
        'key4' => 'val2',
        'key3' => 'val3',
        'key2' => 'val5',
        'key1' => 'val99',
    ]
);

$I->wantTo('Sort an Object by value in descending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'desc',
        'sort_by' => 'value',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'key1' => 'val99',
        'key2' => 'val5',
        'key3' => 'val3',
        'key4' => 'val2',
        'key999' => 'val1',
    ]
);

$I->wantTo('Sort an Object by key in asscending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'asc',
        'sort_by' => 'key',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'key1' => 'val99',
        'key2' => 'val5',
        'key3' => 'val3',
        'key4' => 'val2',
        'key999' => 'val1',
    ]
);

$I->wantTo('Sort an Object by key in descending order and see the result.');
$I->sendGet(
    $uri,
    [
        'direction' => 'desc',
        'sort_by' => 'key',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        'key999' => 'val1',
        'key4' => 'val2',
        'key3' => 'val3',
        'key2' => 'val5',
        'key1' => 'val99',
    ]
);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml('sortObject.yaml');
$I->deleteHeader('Authorization');
