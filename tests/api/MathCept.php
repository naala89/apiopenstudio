<?php

$I = new ApiTester($scenario);
$uri = $I->getMyBaseUri() . '/math';
$yaml = 'math.yaml';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yaml);
$I->deleteHeader('Authorization');

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$nanData = [
    'sqrt(log(0)) + 1',
    'sqrt(-1.0)',
    '5^500 - 5^500',
    '5^500 / 5^500',
];
$infData = [
    ['sqrt(5^500)', 'Infinity'],
    ['10 + log(0)', '-Infinity'],
    ['-(5)^500+5', '-Infinity'],
    ['(-5)^500+5', 'Infinity'],
    ['abs(-5^500)/pi', 'Infinity'],
    ['-abs(-5^500+1)', '-Infinity'],
    ['log(0)', '-Infinity'],
    ['-log(0)', 'Infinity'],
];
$varData = [
    ['3*x^2 - 4*y + 3/y', 16.375, ['x' => -4, 'y' => 8]],
    ['5/-x', 2.5, ['x' => -2]],
    ['sqrt(x^y/pi)', 9.027033336763804, ['x' => -2, 'y' => 8]],
    ['abs(x-y^3)', 25, ['x' => 2, 'y' => 3]],
    ['x-tan(-4)^3', 1.4521174611477041, ['x' => -.1]],
    ['(y)^x', 16, ['x' => 4, 'y' => -2]],
];
$errorData = [
    ['5*/7', '*/.'],
    ['^', 'Unidentified error.'],
    ['()', 'Stack must be empty.'],
    [') (', 'Unidentified error.'],
    ['(1+1)5', 'Stack must be empty.'],
    ['pi e', 'Stack must be empty.'],
    ['1*E1', 'E1.'],
    ['3 * x6', 'X6.', ['x' => '1']],
    ['  ', 'Stack must be empty.'],
    ['_', '_.'],
    ['X', 'X.', ['x' => '4']],
    ['(x))', 'Unidentified error.'],
];

$I->wantTo('Test formulas that will return NAN.');
foreach ($nanData as $formula) {
    $I->wantTo("Test formula $formula.");
    $I->sendGet($uri, ['formula' => $formula]);
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => 'NAN',
    ]);
}

$I->wantTo('Test formulas that will return INF values.');
foreach ($infData as $formula) {
    $I->wantTo("Test formula {$formula[0]}.");
    $I->sendGet($uri, ['formula' => $formula[0]]);
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => $formula[1],
    ]);
}

$I->wantTo('Test formulas with variables.');
foreach ($varData as $formula) {
    $I->wantTo("Test formula {$formula[0]}.");
    $query = ['formula' => $formula[0]];
    $query = array_merge($query, $formula[2]);
    $I->sendGet($uri, $query);
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => $formula[1],
    ]);
}

$I->wantTo('Test formulas with errors.');
foreach ($errorData as $formula) {
    $I->wantTo("Test formula {$formula[0]}.");
    $query = ['formula' => $formula[0]];
    if (isset($formula[2])) {
        $query = array_merge($query, $formula[2]);
    }
    $I->sendGet($uri, $query);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'math_processor',
            'code' => 6,
            'message' => $formula[1],
        ],
    ]);
}

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yaml);
$I->deleteHeader('Authorization');
