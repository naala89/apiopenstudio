<?php

$I = new ApiTester($scenario);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$yamlFilename = 'arrayIndexedNested.yaml';
$uri = $I->getMyBaseUri() . '/array/indexed/nested';
$I->createResourceFromYaml($yamlFilename);

// json - application/json
$I->wantTo('Test a nested indexed array with accept: application/json.');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => [
        [
            'this',
            'is',
            'one',
        ], [
            'this',
            'is',
            'two',
        ],
    ],
]);
$I->deleteHeader('Accept');

// xml - application/xml
$xml = "<?xml version=\"1.0\"?>\n";
$xml .= '<apiOpenStudioWrapper>';
$xml .= '<item><item>this</item><item>is</item><item>one</item></item>';
$xml .= '<item><item>this</item><item>is</item><item>two</item></item>';
$xml .= '</apiOpenStudioWrapper>';
$I->wantTo('Test a nested indexed array with accept: application/xml.');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($xml);
$I->deleteHeader('Accept');

// xml - text/xml
$I->wantTo('Test a nested indexed array with accept: text/xml.');
$I->haveHttpHeader('Accept', 'text/xml');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($xml);
$I->deleteHeader('Accept');

// text = text/plain
$I->wantTo('Test a nested indexed array with accept: text/plain.');
$I->haveHttpHeader('Accept', 'text/plain');
$I->sendGet($uri);
$I->seeResponseCodeIs(400);
$I->seeResponseContains('Error: Cannot cast array to text');
$I->deleteHeader('Accept');

// html = text/html
$html = "<!DOCTYPE html>\n";
$html .= '<html lang="en-us"><head><meta charset="utf-8" /><title>HTML generated by ApiOpenStudio</title></head>';
$html .= '<body><dl>';
$html .= '<dt>0</dt>';
$html .= '<dd>';
$html .= '<dl>';
$html .= '<dt>0</dt><dd>this</dd>';
$html .= '<dt>1</dt><dd>is</dd>';
$html .= '<dt>2</dt><dd>one</dd>';
$html .= '</dl>';
$html .= '</dd>';
$html .= '<dt>1</dt>';
$html .= '<dd>';
$html .= '<dl>';
$html .= '<dt>0</dt><dd>this</dd>';
$html .= '<dt>1</dt><dd>is</dd>';
$html .= '<dt>2</dt><dd>two</dd>';
$html .= '</dl>';
$html .= '</dd>';
$html .= '</dl>';
$html .= '</body></html>';
$I->wantTo('Test a nested indexed array with accept: text/html.');
$I->haveHttpHeader('Accept', 'text/html');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($html);
$I->deleteHeader('Accept');

$I->haveHttpHeader('Accept', 'application/json');
$I->tearDownTestFromYaml($yamlFilename);