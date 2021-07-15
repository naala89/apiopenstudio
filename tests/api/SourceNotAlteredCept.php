<?php

$I = new ApiTester($scenario);

$uri = $I->getMyBaseUri() . '/url/';
$yamlFilename = 'url.yaml';
$xml_path = 'https://www.w3schools.com/xml/cd_catalog.xml';
$json_path = 'https://jsonplaceholder.typicode.com/users';
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('populate a Url with sample xml and Accept:application/xml in header see the result.');
curl_setopt_array($curl, array(
    CURLOPT_URL => $xml_path,
));
$comparison = curl_exec($curl);
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendGet(
    $uri,
    [
        'method' => 'get',
        'url' => $xml_path,
        'source_type' => 'xml',
        'report_error' => true,
        'connect_timeout' => 10,
        'timeout' => 30
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$I->seeResponseContains($comparison);

$I->wantTo('populate a Url with sample json and Accept:application/json in the header and see the result.');
curl_setopt_array($curl, array(
    CURLOPT_URL => $json_path,
));
$comparison = curl_exec($curl);
$I->haveHttpHeader('Accept', 'application/json');
$I->sendGet(
    $uri,
    [
        'method' => 'get',
        'url' => $json_path,
        'source_type' => 'json',
        'report_error' => true,
        'connect_timeout' => 10,
        'timeout' => 30
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains($comparison);

$I->deleteHeader('Authorization');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');
