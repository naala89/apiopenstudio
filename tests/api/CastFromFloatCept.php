<?php

$I = new ApiTester($scenario);

$I->comment('Testing with float literal -3.141');
$uri = $I->getMyBaseUri() . '/cast/float';
$yamlFilename = 'castFloat-3.141.yaml';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);

$I->wantTo('Test cast -3.141 to array.');
$I->sendGet($uri, ['data_type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [-3.141],
]);

$I->wantTo('Test cast -3.141 to boolean.');
$I->sendGet($uri, ['data_type' => 'boolean']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast float cast',
        'message' => 'Cannot cast float to boolean.',
    ],
]);

$I->wantTo('Test cast -3.141 to empty.');
$I->sendGet($uri, ['data_type' => 'undefined']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => null,
]);

$I->wantTo('Test cast -3.141 to float.');
$I->sendGet($uri, ['data_type' => 'float']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => -3.141,
]);

$I->wantTo('Test cast -3.141 to html.');
$I->sendGet($uri, ['data_type' => 'html']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'html' => [
            ['_lang' => 'en-us'],
            [
                'head' => [
                    [
                        'meta' => [
                            ['_charset' => 'utf-8'],
                        ],
                    ], [
                        'title' => [
                            ['#text' => 'HTML generated by ApiOpenStudio'],
                        ],
                    ],
                ],
            ], [
                "body" => [
                    [
                        "div" => [
                            ["#text" => "-3.141"],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

$I->wantTo('Test cast -3.141 to image.');
$I->sendGet($uri, ['data_type' => 'image']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast float cast',
        'message' => 'Cannot cast float to image.',
    ],
]);

$I->wantTo('Test cast -3.141 to integer.');
$I->sendGet($uri, ['data_type' => 'integer']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast float cast',
        'message' => 'Cannot cast float to integer.',
    ],
]);

$I->wantTo('Test cast -3.141 to json.');
$I->sendGet($uri, ['data_type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => -3.141,
]);

$I->wantTo('Test cast -3.141 to text.');
$I->sendGet($uri, ['data_type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '-3.141',
]);

$I->wantTo('Test cast -3.141 to xml.');
$I->sendGet($uri, ['data_type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'item' => -3.141,
        ],
    ],
]);

$I->tearDownTestFromYaml($yamlFilename);

$I->comment('Testing with float literal 0');
$yamlFilename = 'castFloat0.yaml';
$uri = $I->getMyBaseUri() . '/cast/float';
$I->createResourceFromYaml($yamlFilename);

$I->wantTo('Test cast 0.0 to array.');
$I->sendGet($uri, ['data_type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [0],
]);

$I->wantTo('Test cast 0.0 to boolean.');
$I->sendGet($uri, ['data_type' => 'boolean']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => false,
]);

$I->wantTo('Test cast 0.0 to empty.');
$I->sendGet($uri, ['data_type' => 'undefined']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => null,
]);

$I->wantTo('Test cast 0.0 to float.');
$I->sendGet($uri, ['data_type' => 'float']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 0.0,
]);

$I->wantTo('Test cast 0.0 to html.');
$I->sendGet($uri, ['data_type' => 'html']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'html' => [
            ['_lang' => 'en-us'],
            [
                'head' => [
                    [
                        'meta' => [
                            ['_charset' => 'utf-8'],
                        ],
                    ], [
                        'title' => [
                            ['#text' => 'HTML generated by ApiOpenStudio'],
                        ],
                    ],
                ],
            ], [
                "body" => [
                    [
                        "div" => [
                            ["#text" => "0"],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

$I->wantTo('Test cast 0.0 to image.');
$I->sendGet($uri, ['data_type' => 'image']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast float cast',
        'message' => 'Cannot cast float to image.',
    ],
]);

$I->wantTo('Test cast 0.0 to integer.');
$I->sendGet($uri, ['data_type' => 'integer']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 0
]);

$I->wantTo('Test cast 0.0 to json.');
$I->sendGet($uri, ['data_type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 0,
]);

$I->wantTo('Test cast 0.0 to text.');
$I->sendGet($uri, ['data_type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '0.0',
]);

$I->wantTo('Test cast 0.0 to xml.');
$I->sendGet($uri, ['data_type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'item' => 0,
        ],
    ],
]);

$I->tearDownTestFromYaml($yamlFilename);

$I->comment('Testing with float literal 1');
$yamlFilename = 'castFloat1.yaml';
$uri = $I->getMyBaseUri() . '/cast/float';
$I->createResourceFromYaml($yamlFilename);

$I->wantTo('Test cast 1.0 to array.');
$I->sendGet($uri, ['data_type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [1],
]);

$I->wantTo('Test cast 1.0 to boolean.');
$I->sendGet($uri, ['data_type' => 'boolean']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => true,
]);

$I->wantTo('Test cast 1.0 to empty.');
$I->sendGet($uri, ['data_type' => 'undefined']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => null,
]);

$I->wantTo('Test cast 1.0 to float.');
$I->sendGet($uri, ['data_type' => 'float']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 1.0,
]);

$I->wantTo('Test cast 1.0 to html.');
$I->sendGet($uri, ['data_type' => 'html']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'html' => [
            ['_lang' => 'en-us'],
            [
                'head' => [
                    [
                        'meta' => [
                            ['_charset' => 'utf-8'],
                        ],
                    ], [
                        'title' => [
                            ['#text' => 'HTML generated by ApiOpenStudio'],
                        ],
                    ],
                ],
            ], [
                "body" => [
                    [
                        "div" => [
                            ["#text" => "1"],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

$I->wantTo('Test cast 1.0 to image.');
$I->sendGet($uri, ['data_type' => 'image']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast float cast',
        'message' => 'Cannot cast float to image.',
    ],
]);

$I->wantTo('Test cast 1.0 to integer.');
$I->sendGet($uri, ['data_type' => 'integer']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 1
]);

$I->wantTo('Test cast 1.0 to text.');
$I->sendGet($uri, ['data_type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '1.0',
]);

$I->wantTo('Test cast 1.0 to json.');
$I->sendGet($uri, ['data_type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 1,
]);

$I->wantTo('Test cast 1.0 to xml.');
$I->sendGet($uri, ['data_type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'item' => 1,
        ],
    ],
]);

$I->tearDownTestFromYaml($yamlFilename);

$I->comment('Testing with float literal 3.141');
$yamlFilename = 'castFloat3.141.yaml';
$uri = $I->getMyBaseUri() . '/cast/float';
$I->createResourceFromYaml($yamlFilename);

$I->wantTo('Test cast 3.141 to array.');
$I->sendGet($uri, ['data_type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [3.141],
]);

$I->wantTo('Test cast 3.141 to boolean.');
$I->sendGet($uri, ['data_type' => 'boolean']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'id' => 'test cast float cast',
        'code' => 6,
        'message' => 'Cannot cast float to boolean.'
    ],
]);

$I->wantTo('Test cast 3.141 to empty.');
$I->sendGet($uri, ['data_type' => 'undefined']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => null,
]);

$I->wantTo('Test cast 3.141 to float.');
$I->sendGet($uri, ['data_type' => 'float']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 3.141,
]);

$I->wantTo('Test cast 3.141 to html.');
$I->sendGet($uri, ['data_type' => 'html']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'html' => [
            ['_lang' => 'en-us'],
            [
                'head' => [
                    [
                        'meta' => [
                            ['_charset' => 'utf-8'],
                        ],
                    ], [
                        'title' => [
                            ['#text' => 'HTML generated by ApiOpenStudio'],
                        ],
                    ],
                ],
            ], [
                "body" => [
                    [
                        "div" => [
                            ["#text" => "3.141"],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

$I->wantTo('Test cast 3.141 to image.');
$I->sendGet($uri, ['data_type' => 'image']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast float cast',
        'message' => 'Cannot cast float to image.',
    ],
]);

$I->wantTo('Test cast 3.141 to integer.');
$I->sendGet($uri, ['data_type' => 'integer']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast float cast',
        'message' => 'Cannot cast float to integer.',
    ],
]);

$I->wantTo('Test cast 3.141 to text.');
$I->sendGet($uri, ['data_type' => 'text']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => '3.141',
]);

$I->wantTo('Test cast 3.141 to json.');
$I->sendGet($uri, ['data_type' => 'json']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 3.141,
]);

$I->wantTo('Test cast 3.141 to xml.');
$I->sendGet($uri, ['data_type' => 'xml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'apiOpenStudioWrapper' => [
            'item' => 3.141,
        ],
    ],
]);

$I->tearDownTestFromYaml($yamlFilename);