<?php

$I = new ApiTester($scenario);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$yamlFilename = 'floatINF.yaml';
$uri = $I->getMyBaseUri() . '/float/inf';
$I->createResourceFromYaml($yamlFilename);

// json - application/json
$I->wantTo('Test float INF with accept: application/json.');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([
    'result' => 'ok',
    'data' => 'Infinity',
]);
$I->deleteHeader('Accept');

// xml - application/xml
$I->wantTo('Test float INF with accept: application/xml.');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<?xml version="1.0" encoding="utf-8"?>
<apiOpenStudioWrapper><item>INF</item></apiOpenStudioWrapper>');
$I->deleteHeader('Accept');

// xml - text/xml
$I->wantTo('Test float INF with accept: text/xml.');
$I->haveHttpHeader('Accept', 'text/xml');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains('<?xml version="1.0" encoding="utf-8"?>
<apiOpenStudioWrapper><item>INF</item></apiOpenStudioWrapper>');
$I->deleteHeader('Accept');

// text = text/plain
$I->wantTo('Test float INF with accept: text/plain.');
$I->haveHttpHeader('Accept', 'text/plain');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseContains('INF');
$I->deleteHeader('Accept');

// html = text/html
$I->wantTo('Test float INF with accept: text/html.');
$html = "<!DOCTYPE html>\n";
$html .= '<html lang="en-us"><head><meta charset="utf-8" /><title>HTML generated by ApiOpenStudio</title></head>';
$html .= '<body><div>INF</div></body></html>';
$I->haveHttpHeader('Accept', 'text/html');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($html);
$I->deleteHeader('Accept');

$I->haveHttpHeader('Accept', 'application/json');
$I->tearDownTestFromYaml($yamlFilename);