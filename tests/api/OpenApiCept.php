<?php

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsString;

$schemasGeneralError = [
    "type" => "object",
    "properties" => [
        'result' => [
            'type' => 'string'
        ],
        "data" => [
            "type" => "object",
            "properties" => [
                "id" => [
                    "type" => "integer",
                    "format" => "int32",
                ],
                "code" => [
                    "type" => "integer",
                    "format" => "int32",
                ],
                "message" => [
                    "type" => "string",
                ],
            ],
        ],
    ],
];
$responsesGeneralError = [
    "description" => "General Error",
    "content" => [
        'application/json' => [
            'schema' => [
                '$ref' => '#/components/schemas/GeneralError',
            ],
            'example' => [
                'result' => 'error',
                'data'  => [
                    'id' => '<my_processor_id>',
                    'code' => 6,
                    'message' => 'Oops, something went wrong.',
                ],
            ],
        ],
    ],
];
$responsesUnauthorised = [
    "description" => "Unauthorised",
    "content" => [
        'application/json' => [
            'schema' => [
                '$ref' => '#/components/schemas/GeneralError',
            ],
            'example' => [
                'result' => 'error',
                'data'  => [
                    'id' => '<my_processor_id>',
                    'code' => 4,
                    'message' => 'Invalid token.',
                ],
            ],
        ],
    ],
];
$responsesForbidden = [
    "description" => "Forbidden",
    "content" => [
        'application/json' => [
            'schema' => [
                '$ref' => '#/components/schemas/GeneralError',
            ],
            'example' => [
                'result' => 'error',
                'data'  => [
                    'id' => '<my_processor_id>',
                    'code' => 6,
                    'message' => 'Permission denied.',
                ],
            ],
        ],
    ],
];
$securitySchemeBearerToken = [
    "type" => 'http',
    'scheme' => 'bearer',
    'bearerFormat' => 'JWT',
];

$I = new ApiTester($scenario);

$I->wantTo(
    'Test that creating a new application will create and return the base OpenApi schema for that application.'
);
$url = $I->getCoreBaseUri() . '/application';
$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));
$I->sendPost(
    $url,
    [
        'accid' => 2,
        'name' => 'test_openapi',
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$array = json_decode($I->getResponse(), true);
assertEquals('ok', $array['result'], 'Got OK as result.');
$openApi = $array['data']['openapi'];
// Swagger/OpenApi version block.
assertEquals('3.0.3', $openApi['openapi'], 'The openApi version is 3.0.3');
// info block.
assertEquals('test_openapi', $openApi['info']['title'], 'OpenApi has the application name as title.');
assertEquals(
    'These are the resources that belong to the test_openapi application.',
    $openApi['info']['description'],
    'OpenApi has the application name in the description.'
);
assertIsString($openApi['info']['termsOfService'], 'Got a string for terms of service.');
assertIsArray($openApi['info']['contact'], 'Got an array for contact details.');
assertIsString($openApi['info']['contact']['name'], 'Contact has name element.');
assertIsString($openApi['info']['contact']['email'], 'Contact has email element.');
assertIsArray($openApi['info']['license'], 'Got an array for license details.');
assertIsString($openApi['info']['license']['name'], 'license has name element.');
assertIsString($openApi['info']['license']['url'], 'license has url element.');
assertEquals('1.0.0', $openApi['info']['version'], 'OpenApi has 1.0.0 as the version tag for the new application.');
// servers block.
assertIsArray($openApi['servers']);
assertEquals(1, sizeof($openApi['servers'][0]), 'The single server listed has a single element in it.');
assertIsString($openApi['servers'][0]['url'], 'Got a string as the only server URL.');
// Paths block.
assertIsArray($openApi['paths'], 'Got a paths element.');
assertEquals(0, sizeof($openApi['paths']), 'heer are no paths defined yet.');
// Components block.
assertIsArray($openApi['components'], 'Got a components element.');
assertIsArray($openApi['components']['schemas'], 'Got a schemas element.');
assertEquals(1, sizeof($openApi['components']['schemas']), 'Got a single schemas attribute.');
assertIsArray($openApi['components']['schemas']['GeneralError'], 'Got a GeneralError schema attribute.');
assertEquals(
    $schemasGeneralError,
    $openApi['components']['schemas']['GeneralError'],
    'the GeneralError contains the expected definition'
);
assertIsArray($openApi['components']['responses'], 'Got a responses element.');
assertEquals(3, sizeof($openApi['components']['responses']), 'Got three responses attributes.');
assertIsArray($openApi['components']['responses']['GeneralError'], 'Got a GeneralError responses attribute.');
assertEquals(
    $responsesGeneralError,
    $openApi['components']['responses']['GeneralError'],
    'the GeneralError contains the expected definition'
);
assertIsArray($openApi['components']['responses']['Unauthorised'], 'Got an Unauthorised responses attribute.');
assertEquals(
    $responsesUnauthorised,
    $openApi['components']['responses']['Unauthorised'],
    'the Unauthorised contains the expected definition'
);
assertIsArray($openApi['components']['responses']['Forbidden'], 'Got a Forbidden responses attribute.');
assertEquals(
    $responsesForbidden,
    $openApi['components']['responses']['Forbidden'],
    'the Unauthorised contains the expected definition'
);
assertIsArray($openApi['components']['securitySchemes'], 'Got a securitySchemes attribute.');
assertEquals(1, sizeof($openApi['components']['securitySchemes']), 'Got a single securitySchemes attribute.');
assertIsArray(
    $openApi['components']['securitySchemes']['bearer_token'],
    'Got a bearer_token securitySchemes attribute.'
);
assertEquals(
    $securitySchemeBearerToken,
    $openApi['components']['securitySchemes']['bearer_token'],
    'the bearer_token contains the expected definition'
);
// Security block
assertIsArray($openApi['security'], 'Got a security attribute.');
assertEmpty($openApi['security'], 'the security attribute is empty.');
// ExternalDocs block
assertIsArray($openApi['externalDocs'], 'Got a externalDocs attribute.');
assertEquals(2, sizeof($openApi['externalDocs']), 'Got 2 attributes in the externalDocs.');
assertIsString($openApi['externalDocs']['description'], 'Gpt a description attribute in externalDocs.');
assertIsString($openApi['externalDocs']['url'], 'Gpt a url attribute in externalDocs.');
