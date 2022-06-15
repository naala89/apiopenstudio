<?php

$I = new ApiTester($scenario);

$I->comment('Testing cast from html');
$uri = $I->getMyBaseUri() . '/cast/html';
$yamlFilename = 'castHtml.yaml';
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);

$I->wantTo('Test cast html to array.');
$I->sendGet($uri, ['data_type' => 'array']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'html' => [
            ['_itemscope' => ''],
            ['_itemtype' => 'http://schema.org/WebPage'],
            ['_lang' => 'en-AU'],
            [
                'head' => [
                    [
                        'meta' => [
                            ['_charset' => 'UTF-8'],
                        ],
                    ], [
                        'meta' => [
                            ['_content' => 'origin'],
                            ['_name' => 'referrer'],
                        ],
                    ], [
                        'meta' => [
                            ['_content' => '/images/branding/googleg/1x/googleg_standard_color_128dp.png'],
                            ['_itemprop' => 'image'],
                        ],
                    ], [
                        'link' => [
                            ['_href' => '/manifest?pwa=webhp'],
                            ['_crossorigin' => 'use-credentials'],
                            ['_rel' => 'manifest'],
                        ],
                    ], [
                        'title' => [
                            ['#text' => 'Google'],
                        ],
                    ], [
                        'script' => [
                            ['_nonce' => 'K6yMFw3t_j87HL4HOZMfxg'],
                            ['#text' => 'var f={};'],
                        ],
                    ], [
                        'script' => [
                            ['_nonce' => 'K6yMFw3t_j87HL4HOZMfxg'],
                            ['#text' => 'var g=[];'],
                        ],
                    ], [
                        'style' => [
                            ['#text' => 'h1,ol,ul,li,button{margin:0;padding:0}'],
                        ],
                    ],
                ],
            ], [
                'body' => [
                    [
                        'div' => [
                            ['_class' => 'o3j99 n1xJcf Ne6nSd'],
                            [
                                'a' => [
                                    ['_class' => 'MV3Tnb'],
                                    ['_href' => 'https://about.google'],
                                    ['#text' => 'About'],
                                ],
                            ], [
                                'a' => [
                                    ['_class' => 'MV3Tnb'],
                                    ['_href' => 'https://store.google.com'],
                                    ['#text' => 'Store'],
                                ],
                            ], [
                                'div' => [
                                    ['_class' => 'LX3sZb'],
                                    [
                                        'div' => [
                                            ['_class' => 'gb_e gb_f'],
                                            [
                                                'a' => [
                                                    ['_class' => 'gb_d'],
                                                    ['_data-pid' => '23'],
                                                    ['_href' => 'https://mail.google.com'],
                                                    ['_target' => '_top'],
                                                    ['#text' => 'Gmail'],
                                                ],
                                            ],
                                        ],
                                    ], [
                                        'div' => [
                                            ['_class' => 'gb_e gb_f'],
                                            [
                                                'a' => [
                                                    ['_class' => 'gb_d'],
                                                    ['_data-pid' => '2'],
                                                    ['_href' => 'https://www.google.com.au'],
                                                    ['_target' => '_top'],
                                                    ['#text' => 'Images'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

$I->wantTo('Test cast html to boolean.');
$I->sendGet($uri, ['data_type' => 'boolean']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast html cast',
        'message' => 'Cannot cast HTML to boolean.',
    ],
]);

$I->wantTo('Test cast html to empty.');
$I->sendGet($uri, ['data_type' => 'undefined']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => null,
]);

$I->wantTo('Test cast html to float.');
$I->sendGet($uri, ['data_type' => 'float']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast html cast',
        'message' => 'Cannot cast HTML to float.',
    ],
]);

$I->wantTo('Test cast html to html.');
$I->sendGet($uri, ['data_type' => 'html']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        'html' => [
            ['_itemscope' => ''],
            ['_itemtype' => 'http://schema.org/WebPage'],
            ['_lang' => 'en-AU'],
            [
                'head' => [
                    [
                        'meta' => [
                            ['_charset' => 'UTF-8'],
                        ],
                    ], [
                        'meta' => [
                            ['_content' => 'origin'],
                            ['_name' => 'referrer'],
                        ],
                    ], [
                        'meta' => [
                            ['_content' => '/images/branding/googleg/1x/googleg_standard_color_128dp.png'],
                            ['_itemprop' => 'image'],
                        ],
                    ], [
                        'link' => [
                            ['_href' => '/manifest?pwa=webhp'],
                            ['_crossorigin' => 'use-credentials'],
                            ['_rel' => 'manifest'],
                        ],
                    ], [
                        'title' => [
                            ['#text' => 'Google'],
                        ],
                    ], [
                        'script' => [
                            ['_nonce' => 'K6yMFw3t_j87HL4HOZMfxg'],
                            ['#text' => 'var f={};'],
                        ],
                    ], [
                        'script' => [
                            ['_nonce' => 'K6yMFw3t_j87HL4HOZMfxg'],
                            ['#text' => 'var g=[];'],
                        ],
                    ], [
                        'style' => [
                            ['#text' => 'h1,ol,ul,li,button{margin:0;padding:0}'],
                        ],
                    ],
                ],
            ], [
                'body' => [
                    [
                        'div' => [
                            ['_class' => 'o3j99 n1xJcf Ne6nSd'],
                            [
                                'a' => [
                                    ['_class' => 'MV3Tnb'],
                                    ['_href' => 'https://about.google'],
                                    ['#text' => 'About'],
                                ],
                            ], [
                                'a' => [
                                    ['_class' => 'MV3Tnb'],
                                    ['_href' => 'https://store.google.com'],
                                    ['#text' => 'Store'],
                                ],
                            ], [
                                'div' => [
                                    ['_class' => 'LX3sZb'],
                                    [
                                        'div' => [
                                            ['_class' => 'gb_e gb_f'],
                                            [
                                                'a' => [
                                                    ['_class' => 'gb_d'],
                                                    ['_data-pid' => '23'],
                                                    ['_href' => 'https://mail.google.com'],
                                                    ['_target' => '_top'],
                                                    ['#text' => 'Gmail'],
                                                ],
                                            ],
                                        ],
                                    ], [
                                        'div' => [
                                            ['_class' => 'gb_e gb_f'],
                                            [
                                                'a' => [
                                                    ['_class' => 'gb_d'],
                                                    ['_data-pid' => '2'],
                                                    ['_href' => 'https://www.google.com.au'],
                                                    ['_target' => '_top'],
                                                    ['#text' => 'Images'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

$I->wantTo('Test cast html to image.');
$I->sendGet($uri, ['data_type' => 'image']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast html cast',
        'message' => 'Cannot cast HTML to image.',
    ],
]);

$I->wantTo('Test cast html to integer.');
$I->sendGet($uri, ['data_type' => 'integer']);
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'error',
    'data' => [
        'code' => 6,
        'id' => 'test cast html cast',
        'message' => 'Cannot cast HTML to integer.',
    ],
]);

$I->tearDownTestFromYaml($yamlFilename);
