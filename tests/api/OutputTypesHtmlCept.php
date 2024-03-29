<?php

$I = new ApiTester($scenario);

$html = "<!DOCTYPE html>\n";
$html .= "<html lang=\"en-us\">\n";
$html .= "    <head>\n";
$html .= "        <meta charset=\"utf-8\" />\n";
$html .= "        <title>HTML generated by ApiOpenStudio</title>\n";
$html .= "    </head>\n";
$html .= "    <body>\n";
$html .= "        <dl>\n";
$html .= "            <dt>one</dt>\n";
$html .= "            <dd>\n";
$html .= "                <dl>\n";
$html .= "                    <dt>one_one</dt>\n";
$html .= "                    <dd>this</dd>\n";
$html .= "                    <dt>one_two</dt>\n";
$html .= "                    <dd>is</dd>\n";
$html .= "                    <dt>one_three</dt>\n";
$html .= "                    <dd>an</dd>\n";
$html .= "                </dl>\n";
$html .= "            </dd>\n";
$html .= "            <dt>two</dt>\n";
$html .= "            <dd>\n";
$html .= "                <dl>\n";
$html .= "                    <dt>two_one</dt>\n";
$html .= "                    <dd>associative</dd>\n";
$html .= "                    <dt>two_two</dt>\n";
$html .= "                    <dd>array</dd>\n";
$html .= "                </dl>\n";
$html .= "            </dd>\n";
$html .= "        </dl>\n";
$html .= "    </body>\n";
$html .= "</html>\n";

$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
// phpcs:ignore
$xml .= '<html><item><_lang>en-us</_lang></item><item><head><item><meta><metum><_charset>utf-8</_charset></metum></meta></item><item><title><item><text>HTML generated by ApiOpenStudio</text></item></title></item></head></item><item><body><item><dl><item><dt><item><text>one</text></item></dt></item><item><dd><item><dl><item><dt><item><text>one_one</text></item></dt></item><item><dd><item><text>this</text></item></dd></item><item><dt><item><text>one_two</text></item></dt></item><item><dd><item><text>is</text></item></dd></item><item><dt><item><text>one_three</text></item></dt></item><item><dd><item><text>an</text></item></dd></item></dl></item></dd></item><item><dt><item><text>two</text></item></dt></item><item><dd><item><dl><item><dt><item><text>two_one</text></item></dt></item><item><dd><item><text>associative</text></item></dd></item><item><dt><item><text>two_two</text></item></dt></item><item><dd><item><text>array</text></item></dd></item></dl></item></dd></item></dl></item></body></item></html>';


$json = [
    "html" => [
        ["_lang" => "en-us"],
        [
            "head" => [
                [
                    "meta" => [
                        ["_charset" => "utf-8"],
                    ],
                ], [
                    "title" => [
                        ["#text" => "HTML generated by ApiOpenStudio"],
                    ],
                ],
            ],
        ],
        [
            "body" => [
                [
                    "dl" => [
                        [
                            "dt" => [
                                ["#text" => "one"],
                            ],
                        ], [
                            "dd" => [
                                [
                                    "dl" => [
                                        [
                                            "dt" => [
                                                ["#text" => "one_one"],
                                            ],
                                        ], [
                                            "dd" => [
                                                ["#text" => "this"],
                                            ],
                                        ], [
                                            "dt" => [
                                                ["#text" => "one_two"],
                                            ],
                                        ], [
                                            "dd" => [
                                                ["#text" => "is"],
                                            ],
                                        ], [
                                            "dt" => [
                                                ["#text" => "one_three"],
                                            ],
                                        ], [
                                            "dd" => [
                                                ["#text" => "an"],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ], [
                            "dt" => [
                                ["#text" => "two"],
                            ],
                        ], [
                            "dd" => [
                                [
                                    "dl" => [
                                        [
                                            "dt" => [
                                                ["#text" => "two_one"],
                                            ],
                                        ], [
                                            "dd" => [
                                                ["#text" => "associative"],
                                            ],
                                        ], [
                                            "dt" => [
                                                ["#text" => "two_two"],
                                            ],
                                        ], [
                                            "dd" => [
                                                ["#text" => "array"],
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
];

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$yamlFilename = 'html.yaml';
$uri = $I->getMyBaseUri() . '/html';
$I->createResourceFromYaml($yamlFilename);

// json - application/json
$I->wantTo('Test a html string with accept: application/json.');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => $json,
]);
$I->deleteHeader('Accept');

// xml - application/xml
$I->wantTo('Test a html string with accept: application/xml.');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($xml);
$I->deleteHeader('Accept');

// xml - text/xml
$I->wantTo('Test a html string with accept: text/xml.');
$I->haveHttpHeader('Accept', 'text/xml');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($xml);
$I->deleteHeader('Accept');

// text = text/plain
$I->wantTo('Test a html string with accept: text/plain.');
$I->haveHttpHeader('Accept', 'text/plain');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseContains($html);
$I->deleteHeader('Accept');

// html = text/html
$I->wantTo('Test a html string with accept: text/html.');
$I->haveHttpHeader('Accept', 'text/html');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($html);
$I->deleteHeader('Accept');

$I->haveHttpHeader('Accept', 'application/json');
$I->tearDownTestFromYaml($yamlFilename);
