<?php

$I = new ApiTester($scenario);
$yamlFilename = 'xmlPath.yaml';
$url = $I->getMyBaseUri() . '/xmlpath';
$data = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<bookstore>
    <book category="COOKING">
        <title lang="en">Everyday Italian</title>
        <author>Giada De Laurentiis</author>
        <year>2005</year>
        <price>30.00</price>
    </book>
    <book category="CHILDREN">
        <title lang="en">Harry Potter</title>
        <author>J K. Rowling</author>
        <year>2005</year>
        <price>29.99</price>
    </book>
    <book category="WEB">
        <title lang="en">XQuery Kick Start</title>
        <author>James McGovern</author>
        <author>Per Bothner</author>
        <author>Kurt Cagle</author>
        <author>James Linn</author>
        <author>Vaidyanathan Nagarajan</author>
        <year>2003</year>
        <price>49.99</price>
    </book>
    <book category="WEB">
        <title lang="en">Learning XML</title>
        <author>Erik T. Ray</author>
        <year>2003</year>
        <price>39.95</price>
    </book>
</bookstore>';

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->haveHttpHeader('Accept', 'application/xml');

$I->wantTo('Test XmlPath get all authors of books');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '//bookstore/book/author',
        'operation' => 'get',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$xml = '<?xml version="1.0"?>
';
// phpcs:ignore
$xml .= '<apiOpenStudioWrapper><author>Giada De Laurentiis</author><author>J K. Rowling</author><author>James McGovern</author><author>Per Bothner</author><author>Kurt Cagle</author><author>James Linn</author><author>Vaidyanathan Nagarajan</author><author>Erik T. Ray</author></apiOpenStudioWrapper>
';
$I->seeResponseEquals($xml);

$I->wantTo('Test XmlPath get books where price more than 30');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '//bookstore/book[price>30.00]',
        'operation' => 'get',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$xml = '<?xml version="1.0"?>
';
// phpcs:ignore
$xml .= '<apiOpenStudioWrapper><book category="WEB"> <title lang="en">XQuery Kick Start</title> <author>James McGovern</author> <author>Per Bothner</author> <author>Kurt Cagle</author> <author>James Linn</author> <author>Vaidyanathan Nagarajan</author> <year>2003</year> <price>49.99</price> </book><book category="WEB"> <title lang="en">Learning XML</title> <author>Erik T. Ray</author> <year>2003</year> <price>39.95</price> </book></apiOpenStudioWrapper>
';
$I->seeResponseEquals($xml);

$I->wantTo('Test XmlPath change the price of the first book');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '//bookstore/book[1]/price',
        'operation' => 'set',
        'value' => 0.99,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
';
// phpcs:ignore
$xml .= '<bookstore><book category="COOKING"><title lang="en">Everyday Italian</title><author>Giada De Laurentiis</author><year>2005</year><price>0.99</price></book><book category="CHILDREN"><title lang="en">Harry Potter</title><author>J K. Rowling</author><year>2005</year><price>29.99</price></book><book category="WEB"><title lang="en">XQuery Kick Start</title><author>James McGovern</author><author>Per Bothner</author><author>Kurt Cagle</author><author>James Linn</author><author>Vaidyanathan Nagarajan</author><year>2003</year><price>49.99</price></book><book category="WEB"><title lang="en">Learning XML</title><author>Erik T. Ray</author><year>2003</year><price>39.95</price></book></bookstore>
';
$I->canSeeResponseEquals($xml);

$I->wantTo('Test JsonPath add a book');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '//bookstore',
        'operation' => 'add',
        'value' => '    <book category="HORROR">
        <title lang="en">How I fell in love with an alien</title>
        <author>H. R. Geiger</author>
        <year>1999</year>
        <price>130.00</price>
    </book>
',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
';
// phpcs:ignore
$xml .= '<bookstore><book category="COOKING"><title lang="en">Everyday Italian</title><author>Giada De Laurentiis</author><year>2005</year><price>30.00</price></book><book category="CHILDREN"><title lang="en">Harry Potter</title><author>J K. Rowling</author><year>2005</year><price>29.99</price></book><book category="WEB"><title lang="en">XQuery Kick Start</title><author>James McGovern</author><author>Per Bothner</author><author>Kurt Cagle</author><author>James Linn</author><author>Vaidyanathan Nagarajan</author><year>2003</year><price>49.99</price></book><book category="WEB"><title lang="en">Learning XML</title><author>Erik T. Ray</author><year>2003</year><price>39.95</price></book><book category="HORROR"> <title lang="en">How I fell in love with an alien</title> <author>H. R. Geiger</author> <year>1999</year> <price>130.00</price> </book></bookstore>
';
$I->canSeeResponseEquals($xml);

$I->wantTo('Test XmlPath add ISBN number to Everyday Italian');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '//bookstore/book[title[contains(., "Everyday Italian")]]',
        'operation' => 'add',
        'value' => '<isbn>0-553-21311-3</isbn>',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
';
// phpcs:ignore
$xml .= '<bookstore><book category="COOKING"><title lang="en">Everyday Italian</title><author>Giada De Laurentiis</author><year>2005</year><price>30.00</price><isbn>0-553-21311-3</isbn></book><book category="CHILDREN"><title lang="en">Harry Potter</title><author>J K. Rowling</author><year>2005</year><price>29.99</price></book><book category="WEB"><title lang="en">XQuery Kick Start</title><author>James McGovern</author><author>Per Bothner</author><author>Kurt Cagle</author><author>James Linn</author><author>Vaidyanathan Nagarajan</author><year>2003</year><price>49.99</price></book><book category="WEB"><title lang="en">Learning XML</title><author>Erik T. Ray</author><year>2003</year><price>39.95</price></book></bookstore>
';
$I->canSeeResponseEquals($xml);

$I->wantTo('Test XmlPath remove the 3rd book');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '//bookstore/book[3]',
        'operation' => 'remove',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
';
// phpcs:ignore
$xml .= '<bookstore><book category="COOKING"><title lang="en">Everyday Italian</title><author>Giada De Laurentiis</author><year>2005</year><price>30.00</price></book><book category="CHILDREN"><title lang="en">Harry Potter</title><author>J K. Rowling</author><year>2005</year><price>29.99</price></book><book category="WEB"><title lang="en">Learning XML</title><author>Erik T. Ray</author><year>2003</year><price>39.95</price></book></bookstore>
';
$I->canSeeResponseEquals($xml);

$I->wantTo('Test XmlPath remove year from all books');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '//bookstore/book/year',
        'operation' => 'remove',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsXml();
$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
';
// phpcs:ignore
$xml .= '<bookstore><book category="COOKING"><title lang="en">Everyday Italian</title><author>Giada De Laurentiis</author><price>30.00</price></book><book category="CHILDREN"><title lang="en">Harry Potter</title><author>J K. Rowling</author><price>29.99</price></book><book category="WEB"><title lang="en">XQuery Kick Start</title><author>James McGovern</author><author>Per Bothner</author><author>Kurt Cagle</author><author>James Linn</author><author>Vaidyanathan Nagarajan</author><price>49.99</price></book><book category="WEB"><title lang="en">Learning XML</title><author>Erik T. Ray</author><price>39.95</price></book></bookstore>
';
$I->canSeeResponseEquals($xml);

// Tear down the XmlPath test resource.
$I->haveHttpHeader('Accept', 'application/json');
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->tearDownTestFromYaml($yamlFilename);
