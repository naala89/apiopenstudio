<?php

$I = new ApiTester($scenario);
$yamlFilename = 'jsonPath.yaml';
$url = $I->getMyBaseUri() . '/jsonpath';
$data = json_encode([
    "store" => [
        "book" => [
            [
                "category" => "reference",
                "author" => "Nigel Rees",
                "title" => "Sayings of the Century",
                "price" => 8.95,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "Evelyn Waugh",
                "title" => "Sword of Honour",
                "price" => 12.99,
                "available" => false,
            ], [
                "category" => "fiction",
                "author" => "Herman Melville",
                "title" => "Moby Dick",
                "isbn" => "0-553-21311-3",
                "price" => 8.99,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "J. R. R. Tolkien",
                "title" => "The Lord of the Rings",
                "isbn" => "0-395-19395-8",
                "price" => 22.99,
                "available" => false,
            ],
        ],
        "bicycle" => [
            "color" => "red",
            "price" => 19.95,
            "available" => true,
        ],
    ],
    "authors" => [
        "Nigel Rees",
        "Evelyn Waugh",
        "Herman Melville",
        "J. R. R. Tolkien",
    ]
]);

$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->createResourceFromYaml($yamlFilename);
$I->deleteHeader('Authorization');

$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));

$I->wantTo('Test JsonPath get all authors');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '$.authors',
        'operation' => 'get',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseEquals('["Nigel Rees","Evelyn Waugh","Herman Melville","J. R. R. Tolkien"]');

$I->wantTo('Test JsonPath get books where price less than 10');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '$.store.book[?(@.price<10)]',
        'operation' => 'get',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseEquals(json_encode([
    [
        "category" => "reference",
        "author" => "Nigel Rees",
        "title" => "Sayings of the Century",
        "price" => 8.95,
        "available" => true,
    ], [
        "category" => "fiction",
        "author" => "Herman Melville",
        "title" => "Moby Dick",
        "isbn" => "0-553-21311-3",
        "price" => 8.99,
        "available" => true,
    ],
]));

$I->wantTo('Test JsonPath change the price of Sayings of the Century: Nigel Rees');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '$.store.book[?(@.title == "Sayings of the Century")].price',
        'operation' => 'set',
        'value' => 0.99,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->canSeeResponseContainsJson([
    "store" => [
        "book" => [
            [
                "category" => "reference",
                "author" => "Nigel Rees",
                "title" => "Sayings of the Century",
                "price" => 0.99,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "Evelyn Waugh",
                "title" => "Sword of Honour",
                "price" => 12.99,
                "available" => false,
            ], [
                "category" => "fiction",
                "author" => "Herman Melville",
                "title" => "Moby Dick",
                "isbn" => "0-553-21311-3",
                "price" => 8.99,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "J. R. R. Tolkien",
                "title" => "The Lord of the Rings",
                "isbn" => "0-395-19395-8",
                "price" => 22.99,
                "available" => false,
            ],
        ],
        "bicycle" => [
            "color" => "red",
            "price" => 19.95,
            "available" => true,
        ],
    ],
    "authors" => [
        "Nigel Rees",
        "Evelyn Waugh",
        "Herman Melville",
        "J. R. R. Tolkien",
    ]
]);

$I->wantTo('Test JsonPath add a book');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '$.store.book',
        'operation' => 'add',
        'value' => json_encode([
            "category" => "reference",
            "author" => "H.R. Geiger",
            "title" => "Alien",
            "price" => 100.99,
            "available" => true,
        ]),
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->canSeeResponseContainsJson([
    "store" => [
        "book" => [
            [
                "category" => "reference",
                "author" => "Nigel Rees",
                "title" => "Sayings of the Century",
                "price" => 8.95,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "Evelyn Waugh",
                "title" => "Sword of Honour",
                "price" => 12.99,
                "available" => false,
            ], [
                "category" => "fiction",
                "author" => "Herman Melville",
                "title" => "Moby Dick",
                "isbn" => "0-553-21311-3",
                "price" => 8.99,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "J. R. R. Tolkien",
                "title" => "The Lord of the Rings",
                "isbn" => "0-395-19395-8",
                "price" => 22.99,
                "available" => false,
            ], [
                "category" => "reference",
                "author" => "H.R. Geiger",
                "title" => "Alien",
                "price" => 100.99,
                "available" => true,
            ],
        ],
        "bicycle" => [
            "color" => "red",
            "price" => 19.95,
            "available" => true,
        ],
    ],
    "authors" => [
        "Nigel Rees",
        "Evelyn Waugh",
        "Herman Melville",
        "J. R. R. Tolkien",
    ]
]);

$I->wantTo('Test JsonPath add ISBN number to Sword of Honour: Evelyn Waugh');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '$.store.book[?(@.title == "Sword of Honour")]',
        'operation' => 'add',
        'value' => '0-553-21311-3',
        'field_name' => 'isbn',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->canSeeResponseContainsJson([
    "store" => [
        "book" => [
            [
                "category" => "reference",
                "author" => "Nigel Rees",
                "title" => "Sayings of the Century",
                "price" => 8.95,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "Evelyn Waugh",
                "title" => "Sword of Honour",
                "price" => 12.99,
                "available" => false,
                "isbn" => "0-553-21311-3",
            ], [
                "category" => "fiction",
                "author" => "Herman Melville",
                "title" => "Moby Dick",
                "isbn" => "0-553-21311-3",
                "price" => 8.99,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "J. R. R. Tolkien",
                "title" => "The Lord of the Rings",
                "isbn" => "0-395-19395-8",
                "price" => 22.99,
                "available" => false,
            ],
        ],
        "bicycle" => [
            "color" => "red",
            "price" => 19.95,
            "available" => true,
        ],
    ],
    "authors" => [
        "Nigel Rees",
        "Evelyn Waugh",
        "Herman Melville",
        "J. R. R. Tolkien",
    ]
]);

$I->wantTo('Test JsonPath remove the 3rd book');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '$.store.book[3]',
        'operation' => 'remove',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->canSeeResponseContainsJson([
    "store" => [
        "book" => [
            [
                "category" => "reference",
                "author" => "Nigel Rees",
                "title" => "Sayings of the Century",
                "price" => 8.95,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "Evelyn Waugh",
                "title" => "Sword of Honour",
                "price" => 12.99,
                "available" => false,
            ], [
                "category" => "fiction",
                "author" => "J. R. R. Tolkien",
                "title" => "The Lord of the Rings",
                "isbn" => "0-395-19395-8",
                "price" => 22.99,
                "available" => false,
            ],
        ],
        "bicycle" => [
            "color" => "red",
            "price" => 19.95,
            "available" => true,
        ],
    ],
    "authors" => [
        "Nigel Rees",
        "Evelyn Waugh",
        "Herman Melville",
        "J. R. R. Tolkien",
    ]
]);

$I->wantTo('Test JsonPath remove ISBN from all books');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST(
    $url,
    [
        'data' => $data,
        'expression' => '$.store.book',
        'operation' => 'remove',
        'field_name' => 'isbn',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->canSeeResponseContainsJson([
    "store" => [
        "book" => [
            [
                "category" => "reference",
                "author" => "Nigel Rees",
                "title" => "Sayings of the Century",
                "price" => 8.95,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "Evelyn Waugh",
                "title" => "Sword of Honour",
                "price" => 12.99,
                "available" => false,
            ], [
                "category" => "fiction",
                "author" => "Herman Melville",
                "title" => "Moby Dick",
                "price" => 8.99,
                "available" => true,
            ], [
                "category" => "fiction",
                "author" => "J. R. R. Tolkien",
                "title" => "The Lord of the Rings",
                "price" => 22.99,
                "available" => false,
            ],
        ],
        "bicycle" => [
            "color" => "red",
            "price" => 19.95,
            "available" => true,
        ],
    ],
    "authors" => [
        "Nigel Rees",
        "Evelyn Waugh",
        "Herman Melville",
        "J. R. R. Tolkien",
    ]
]);
