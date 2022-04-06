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
    'sqrt(-INF)',
    'sin(INF)',
    '0/0 + 1',
    '5^500 - 5^500',
    '5^500 / 5^500',
    '0 * INF',
    'Inf / Inf',
    'Inf / -Inf',
    'Inf - Inf',
    'NaN',
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
    ['INF + 1', 'Infinity'],
    ['Inf * Inf', 'Infinity'],
    ['Inf * -Inf', '-Infinity'],
];
$varData = [
    ['3*x^2 - 4*y + 3/y', 16.375, ['x' => -4, 'y' => 8]],
    ['5/-x', 2.5, ['x' => -2]],
    ['+-z', 10, ['z' => -10]],
    ['sqrt(x^y/pi)', 9.027, ['x' => -2, 'y' => 8]],
    ['abs(x-y^3)', 25, ['x' => 2, 'y' => 3]],
    ['x-tan(-4)^3', 1.4521, ['x' => -.1]],
    ['(y)^x', 16, ['x' => 4, 'y' => -2]],
];
$errorData = [
    ['5*/7', 'Syntax error.'],
    ['^', 'Syntax error.'],
    ['.', 'Syntax error.'],
    ['()', 'Syntax error.'],
    [') (', 'Syntax error.'],
    ['(1+1)5', 'Syntax error.'],
    ['pi e', 'Syntax error.'],
    ['1*E1', 'Syntax error.'],
    ['1.(23)', 'Syntax error.'],
    ['1.2.3', 'Syntax error.'],
    ['.y', 'Syntax error.', ['y' => '4']],
    ['y', 'Variable error.', []],
    ['3 * x6', 'Syntax error.', ['x' => '1']],
    ['3 * 6x', 'Syntax error.', ['x' => '1']],
    ['  ', 'Empty string.'],
    ['_', 'Invalid character.'],
    ['X', 'Invalid character.', ['x' => '4']],
    ['(x))', 'Mismatched parentheses.'],
    ['x+y', 'Variable error.', ['x' => '1', 'y' => '']],
];

$I->wantTo('Test formulas that will return NaN.');
foreach ($nanData as $formula) {
    $I->wantTo("Test formula $formula.");
    $I->sendGet(
        $uri,
        ['formula' => $formula]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => 'NaN',
    ]);
}

$I->wantTo('Test formulas that will return INF values.');
foreach ($infData as $formula) {
    $I->wantTo("Test formula {$formula[0]}.");
    $I->sendGet(
        $uri,
        ['formula' => $formula[0]]
    );
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
