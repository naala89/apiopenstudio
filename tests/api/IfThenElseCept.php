<?php

$I = new ApiTester($scenario);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));

$yaml = 'ifThenElseSimple.yaml';
$uri = $I->getMyBaseUri() . '/if_then_else/simple';
$I->createResourceFromYaml($yaml);

$I->wantTo('Test If Then Else processor with equals operator.');

$I->sendGet(
    $uri,
    [
        'lhs' => 0,
        'rhs' => 10,
        'operator' => '==',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 0,
        'operator' => '==',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 10,
        'operator' => '==',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);

$I->wantTo('Test If Then Else processor with not equals operator.');

$I->sendGet(
    $uri,
    [
        'lhs' => 0,
        'rhs' => 10,
        'operator' => '!=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 0,
        'operator' => '!=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 10,
        'operator' => '!=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);

$I->wantTo('Test If Then Else processor with less than operator.');

$I->sendGet(
    $uri,
    [
        'lhs' => 0,
        'rhs' => 10,
        'operator' => '<',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 0,
        'operator' => '<',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 10,
        'operator' => '<',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);

$I->wantTo('Test If Then Else processor with less than or equals operator.');

$I->sendGet(
    $uri,
    [
        'lhs' => 0,
        'rhs' => 10,
        'operator' => '<=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 0,
        'operator' => '<=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 10,
        'operator' => '<=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);

$I->wantTo('Test If Then Else processor with greater than operator.');

$I->sendGet(
    $uri,
    [
        'lhs' => 0,
        'rhs' => 10,
        'operator' => '>',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 0,
        'operator' => '>',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 10,
        'operator' => '>',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);

$I->wantTo('Test If Then Else processor with greater than or equals operator.');

$I->sendGet(
    $uri,
    [
        'lhs' => 0,
        'rhs' => 10,
        'operator' => '>=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is false"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 0,
        'operator' => '>=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);
$I->sendGet(
    $uri,
    [
        'lhs' => 10,
        'rhs' => 10,
        'operator' => '>=',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["This is true"]);

$I->wantTo('Test If Then Else processor with an invalid operator.');

$I->sendGet(
    $uri,
    [
        'lhs' => 0,
        'rhs' => 10,
        'operator' => '-ne',
    ]
);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'error' => [
        'id' => 'Simple if then else',
        'code' => 6,
        'message' => "Invalid value (-ne). Only '==', '!=', '>', '>=', '<', '<=' allowed in input 'operator'.",
    ]
]);

$I->tearDownTestFromYaml($yaml);
$yaml = 'ifThenElseComplex.yaml';
$uri = $I->getMyBaseUri() . '/if_then_else/complex';
$I->createResourceFromYaml($yaml);

$I->wantTo('Test If Then Else processor with complex nesting with 1st logic branch');

$I->sendGet(
    $uri,
    [
        'lhs_key' => 'lhs_real_key',
        'rhs_key' => 'rhs_real_key',
        'lhs_real_key' => 34,
        'rhs_real_key' => 35,
        'operator' => '<',
        'key1' => 100,
        'key2' => 1000,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    34,
    42,
    "key1 < key2",
    "key2 > key1",
    "Hello"
]);

$I->sendGet(
    $uri,
    [
        'lhs_key' => 'lhs_real_key',
        'rhs_key' => 'rhs_real_key',
        'lhs_real_key' => 34,
        'rhs_real_key' => 35,
        'operator' => '>',
        'key1' => 100,
        'key2' => 1000,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'this',
    'is',
    'else',
]);

$I->wantTo('Test If Then Else processor with complex nesting with 1st nested if_then_else');

$I->sendGet(
    $uri,
    [
        'lhs_key' => 'lhs_real_key',
        'rhs_key' => 'rhs_real_key',
        'lhs_real_key' => 34,
        'rhs_real_key' => 35,
        'operator' => '<',
        'key1' => 2000,
        'key2' => 1000,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    34,
    42,
    "key1 > key2",
    "key2 < key1",
    "Hello"
]);

$I->sendGet(
    $uri,
    [
        'lhs_key' => 'lhs_real_key',
        'rhs_key' => 'rhs_real_key',
        'lhs_real_key' => 34,
        'rhs_real_key' => 35,
        'operator' => '>',
        'key1' => 2000,
        'key2' => 1000,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'this',
    'is',
    'else',
]);

$I->wantTo('Test If Then Else processor with complex nesting with 2nd nested if_then_else');

$I->sendGet(
    $uri,
    [
        'lhs_key' => 'lhs_real_key',
        'rhs_key' => 'rhs_real_key',
        'lhs_real_key' => 34,
        'rhs_real_key' => 35,
        'operator' => '<',
        'key1' => 500,
        'key2' => 1000,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    34,
    42,
    "key1 < key2",
    "key2 > key1",
    "Hello"
]);

$I->sendGet(
    $uri,
    [
        'lhs_key' => 'lhs_real_key',
        'rhs_key' => 'rhs_real_key',
        'lhs_real_key' => 34,
        'rhs_real_key' => 35,
        'operator' => '>',
        'key1' => 2000,
        'key2' => 1000,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'this',
    'is',
    'else',
]);

$I->tearDownTestFromYaml($yaml);
