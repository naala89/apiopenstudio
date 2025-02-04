<?php

$I = new ApiTester($scenario);

$appid = 0;
$coreOpenApi = [
    'openapi' => '3.0.3',
    'info' => [
        'title' => 'core',
        'description' => 'These are the resources that belong to the core application.',
        'termsOfService' => 'https://www.apiopenstudio.com/license/',
        'contact' => [
            'name' => 'API Support',
            'email' => 'contact@api.apiopenstudio.local',
        ],
        'license' => [
            'name' => 'ApiOpenStudio Public License based on Mozilla Public License 2.0',
            'url' => 'https://www.apiopenstudio.com/license/',
        ],
        'version' => '1.0.0',
    ],
    'servers' => [
        ['url' => 'http://localhost/apiopenstudio/core'],
    ],
    'paths' => [],
    'components' => [
        'schemas' => [
            'AccountObject' => [
                'type' => 'object',
                'properties' => [
                    'accid' => [
                        'description' => 'The account ID.',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'name' => [
                        'description' => 'The account name.',
                        'type' => 'string',
                    ],
                ],
            ],
            'AccountObjects' => [
                'type' => 'array',
                'items' => ['$ref' => '#/components/schemas/AccountObject'],
            ],
            'ApplicationObject' => [
                'type' => 'object',
                'properties' => [
                    'accid' => [
                        'description' => 'The account ID.',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'appid' => [
                        'description' => 'The application ID.',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'name' => [
                        'description' => 'The application name.',
                        'type' => 'string',
                    ],
                ],
            ],
            'ApplicationObjects' => [
                'type' => 'array',
                'items' => ['$ref' => '#/components/schemas/ApplicationObject'],
            ],
            'GeneralError' => [
                'type' => 'object',
                'properties' => [
                    'error' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                                'format' => 'int32',
                            ],
                            'code' => [
                                'type' => 'integer',
                                'format' => 'int32',
                            ],
                            'message' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
            'InviteObject' => [
                'type' => 'object',
                'properties' => [
                    'iid' => [
                        'description' => 'The invite ID',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'created' => [
                        'description' => 'The date/time that the invite was created',
                        'type' => 'string',
                    ],
                    'email' => [
                        'description' => 'The invited users email',
                        'type' => 'string',
                    ],
                    'token' => [
                        'description' => 'The invited users invite token',
                        'type' => 'string',
                    ],
                ],
            ],
            'InviteObjects' => [
                'type' => 'array',
                'items' => ['$ref' => '#/components/schemas/InviteObject'],
            ],
            'ModuleDetailObject' => [
                'type' => 'object',
                'properties' => [
                    'details' => ['$ref' => '#/components/schemas/ModuleDetailSummaryObject'],
                    'path' => [
                        'description' => 'The path the module class definition.',
                        'type' => 'string',
                    ],
                    'installed' => [
                        'description' => 'Has the module been installed in the database.',
                        'type' => 'boolean',
                    ],
                    'installable' => [
                        'description' => 'Does the module have an install() function.',
                        'type' => 'boolean',
                    ],
                    'update_function' => [
                        'description' => 'Pending update functions to run.',
                        'type' => 'array',
                        'items' => [
                            'description' => 'Fully namespaced function name.',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'ModuleDetailSummaryObject' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'description' => "The module's human readable name.",
                        'type' => 'string',
                    ],
                    'machineName' => [
                        'description' => "The module's machine_name.",
                        'type' => 'string',
                    ],
                    'description' => [
                        'description' => "The module's description.",
                        'type' => 'string',
                    ],
                    'menu' => [
                        'description' => "The module's parent menu.",
                        'type' => 'string',
                    ],
                    'input' => [
                        'description' => "The module's input.",
                        'type' => 'array',
                        'items' => ['$ref' => '#/components/schemas/ProcessorInputObject'],
                    ],
                ],
            ],
            'ProcessorInputObject' => [
                'type' => 'object',
                'properties' => [
                    'description' => [
                        'description' => 'The input description.',
                        'type' => 'string',
                    ],
                    'cardinality' => [
                        'description' => "The input's description.",
                        'type' => 'array',
                        'minItems' => 2, 'maxItems' => 2, 'items' => [
                            'oneOf' => [
                                [
                                    'type' => 'integer',
                                ], [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                    ],
                    'literalAllowed' => [
                        'description' => 'The input can be a literal.',
                        'type' => 'boolean',
                    ],
                    'limitProcessors' => [
                        'description' => 'Limit the input to specific processors.',
                        'type' => 'array',
                        'items' => [
                            'type' => 'string',
                        ],
                    ],
                    'limitTypes' => [
                        'description' => 'Limit the input to specific content types.',
                        'type' => 'array',
                        'items' => [
                            'type' => 'string',
                        ],
                    ],
                    'limitValues' => [
                        'description' => 'Limit the input to specific values.',
                        'type' => 'array',
                        'items' => [],
                    ],
                    'default' => [
                        'description' => 'The default value if no input recieved',
                        'oneOf' => [
                            [
                                'type' => 'number',
                            ], [
                                'type' => 'string',
                            ], [
                                'type' => 'boolean',
                            ], [
                                'type' => 'object',
                            ],
                        ],
                    ],
                ],
            ],
            'ProcessorObject' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'description' => 'The processor name.',
                        'type' => 'string',
                    ],
                    'machineName' => [
                        'description' => 'The processor machine name.',
                        'type' => 'string',
                    ],
                    'description' => [
                        'description' => 'The processor description.',
                        'type' => 'string',
                    ],
                    'menu' => [
                        'description' => "The processor's parent menu.",
                        'type' => 'string',
                    ],
                    'input' => [
                        'description' => "The processor's input items.",
                        'type' => 'object',
                        'additionalProperties' => ['$ref' => '#/components/schemas/ProcessorInputObject'],
                    ],
                ],
            ],
            'ProcessorObjects' => [
                'type' => 'array',
                'items' => ['$ref' => '#/components/schemas/ProcessorObject'],
            ],
            'TokenObject' => [
                'type' => 'object',
                'properties' => [
                    'uid' => [
                        'description' => 'The user ID',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'token' => [
                        'description' => 'The JWT auth token',
                        'type' => 'string',
                    ],
                    'token_expiry' => [
                        'description' => 'The JWT auth token expiry date',
                        'type' => 'string',
                    ],
                    'refresh_token' => [
                        'description' => 'The JWT refresh token',
                        'type' => 'string',
                    ],
                    'refresh_expiry' => [
                        'description' => 'The JWT refresh token expiry date',
                        'type' => 'string',
                    ],
                ],
            ],
            'ResourceId' => [
                'type' => 'object',
                'properties' => [
                    'resid' => [
                        'description' => 'The resource ID.',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                ],
            ],
            'ResourceObject' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'description' => 'The resource name.',
                        'type' => 'string',
                    ],
                    'description' => [
                        'description' => 'The resource description.',
                        'type' => 'string',
                    ],
                    'appid' => [
                        'description' => 'The application ID the resource belongs to.',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'method' => [
                        'description' => 'The resource request method.',
                        'type' => 'string',
                    ],
                    'uri' => [
                        'description' => 'The resource URI.',
                        'type' => 'string',
                    ],
                    'ttl' => [
                        'description' => 'The resource cache time (in seconds).',
                        'type' => 'integer',
                    ],
                    'meta' => [
                        'description' => 'The resource cache time.',
                        'type' => 'object',
                        'additionalProperties' => [],
                    ],
                    'openapi' => [
                        'description' => 'The resources OpenApi documentation fragment for paths.method.',
                        'type' => 'object',
                        'additionalProperties' => [],
                    ],
                ],
            ],
            'ResourceObjects' => [
                'type' => 'array',
                'items' => [
                    'allOf' => [
                        ['$ref' => '#/components/schemas/ResourceId'],
                        ['$ref' => '#/components/schemas/ResourceObject'],
                    ],
                ],
            ],
            'RoleObject' => [
                'type' => 'object',
                'properties' => [
                    'rid' => [
                        'description' => 'The role ID.',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'name' => [
                        'description' => 'The role name.',
                        'type' => 'string',
                    ],
                ],
            ],
            'RoleObjects' => [
                'type' => 'array',
                'items' => ['$ref' => '#/components/schemas/RoleObject'],
            ],
            'UserId' => [
                'type' => 'object',
                'properties' => [
                    'uid' => [
                        'description' => 'The users ID',
                        'type' => 'integer',
                        'minimum' => 1,
                    ]
                ],
            ],
            'UserObject' => [
                'type' => 'object',
                'properties' => [
                    'active' => [
                        'description' => 'The active/disabkled status of the user',
                        'type' => 'integer',
                        'minimum' => 0, 'maximum' => 1,
                    ],
                    'username' => [
                        'description' => 'The users username',
                        'type' => 'string',
                    ],
                    'hash' => [
                        'description' => 'The users password hash',
                        'type' => 'string',
                    ],
                    'email' => [
                        'description' => 'The users email address',
                        'type' => 'string',
                    ],
                    'honorific' => [
                        'description' => 'The users honorific',
                        'type' => 'string',
                    ],
                    'nameFirst' => [
                        'description' => 'The users first name',
                        'type' => 'string',
                    ],
                    'nameLast' => [
                        'description' => 'The users last name',
                        'type' => 'string',
                    ],
                    'company' => [
                        'description' => 'The users company',
                        'type' => 'string',
                    ],
                    'website' => [
                        'description' => 'The users website',
                        'type' => 'string',
                    ],
                    'addressStreet' => [
                        'description' => 'The users address street',
                        'type' => 'string',
                    ],
                    'addressSuburb' => [
                        'description' => 'The users address suburb',
                        'type' => 'string',
                    ],
                    'addressCity' => [
                        'description' => 'The users address city',
                        'type' => 'string',
                    ],
                    'addressState' => [
                        'description' => 'The users address state',
                        'type' => 'string',
                    ],
                    'addressCountry' => [
                        'description' => 'The users address country',
                        'type' => 'string',
                    ],
                    'addressPostcode' => [
                        'description' => 'The users address postcode',
                        'type' => 'string',
                    ],
                    'phoneMobile' => [
                        'description' => 'The users mobile phone number',
                        'type' => 'string',
                    ],
                    'phoneWork' => [
                        'description' => 'The users work phone number',
                        'type' => 'string',
                    ],
                    'passwordReset' => [
                        'description' => 'The users password reset hash',
                        'type' => 'string',
                    ],
                    'passwordResetTtl' => [
                        'description' => 'The users password reset expiry date',
                        'type' => 'string',
                    ],
                ],
            ],
            'UserObjects' => [
                'type' => 'array',
                'items' => [
                    'allOf' => [
                        ['$ref' => '#/components/schemas/UserId'],
                        ['$ref' => '#/components/schemas/UserObject'],
                    ],
                ],
            ],
            'UserRoleObject' => [
                'type' => 'object',
                'properties' => [
                    'urid' => [
                        'description' => 'The user/role ID',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'uid' => [
                        'description' => 'The user ID',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'accid' => [
                        'description' => 'The account ID for the user/role',
                        'type' => 'integer',
                    ],
                    'appid' => [
                        'description' => 'The application ID for the user/role',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'rid' => [
                        'description' => 'The role ID for the user/role',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                ],
            ],
            'UserRoleObjects' => [
                'type' => 'array',
                'items' => ['$ref' => '#/components/schemas/UserRoleObject'],
            ],
            'VarStoreObject' => [
                'type' => 'object',
                'properties' => [
                    'vid' => [
                        'description' => 'The var store ID',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'appid' => [
                        'description' => 'The parent application ID',
                        'type' => 'integer',
                        'minimum' => 1,
                    ],
                    'key' => [
                        'description' => 'The var store key',
                        'type' => 'string',
                    ],
                    'val' => [
                        'description' => 'The var store value',
                        'type' => 'string',
                    ],
                ],
            ],
            'VarStoreObjects' => [
                'type' => 'array',
                'items' => ['$ref' => '#/components/schemas/VarStoreObject'],
            ],
        ],
        'responses' => [
            'GeneralError' => [
                'description' => 'General Error',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/GeneralError'],
                        'example' => [
                            'error' => [
                                'id' => '<my_processor_id>',
                                'code' => 6, 'message' => 'Oops, something went wrong.',
                            ],
                        ],
                    ],
                ],
            ],
            'Unauthorised' => [
                'description' => 'Unauthorised',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/GeneralError'],
                        'example' => [
                            'error' => [
                                'id' => '<my_processor_id>',
                                'code' => 4, 'message' => 'Invalid token.',
                            ],
                        ],
                    ],
                ],
            ],
            'Forbidden' => [
                'description' => 'Forbidden',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/GeneralError'],
                        'example' => [
                            'error' => [
                                'id' => '<my_processor_id>',
                                'code' => 6, 'message' => 'Permission denied.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'securitySchemes' => [
            'bearer_token' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ],
        ],
    ],
    'security' => [],
    'externalDocs' => [
        'description' => 'Find out more about ApiOpenStudio',
        'url' => 'https://www.apiopenstudio.com',
    ]
];
$testingAppOpenApi = [
    'openapi' => '3.0.3',
    'info' => [
        'title' => 'testing_app',
        'description' => 'These are the resources that belong to the testing_app application.',
        'termsOfService' => 'https://www.apiopenstudio.com/license/',
        'contact' => [
            'name' => 'API Support',
            'email' => 'contact@api.apiopenstudio.local',
        ],
        'license' => [
            'name' => 'ApiOpenStudio Public License based on Mozilla Public License 2.0',
            'url' => 'https://www.apiopenstudio.com/license/',
        ],
        'version' => '1.0.0',
    ],
    'servers' => [
        ['url' => 'https://api.apiopenstudio.local/testing_acc/testing_app']
    ],
    'paths' => [],
    'components' => [
        'schemas' => [
            'GeneralError' => [
                'properties' => [
                    'data' => [
                        'properties' => [
                            'code' => [
                                'format' => 'int32',
                                'type' => 'integer',
                            ],
                            'id' => [
                                'format' => 'int32',
                                'type' => 'integer',
                            ],
                            'message' => [
                                'type' => 'string',
                            ],
                        ],
                        'type' => 'object',
                    ],
                    'result' => [
                        'type' => 'string',
                    ]
                ],
                'type' => 'object',
            ],
        ],
        'responses' => [
            'GeneralError' => [
                'description' => 'General Error',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/GeneralError'],
                        'example' => [
                            'result' => 'error',
                            'data' => [
                                'id' => '<my_processor_id>',
                                'code' => 6,
                                'message' => 'Oops, something went wrong.',
                            ],
                        ],
                    ],
                ],
            ],
            'Unauthorised' => [
                'description' => 'Unauthorised',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/GeneralError'],
                        'example' => [
                            'result' => 'error',
                            'data' => [
                                'id' => '<my_processor_id>',
                                'code' => 4,
                                'message' => 'Invalid token.',
                            ],
                        ],
                    ],
                ],
            ],
            'Forbidden' => [
                'description' => 'Forbidden',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/GeneralError'],
                        'example' => [
                            'result' => 'error',
                            'data' => [
                                'id' => '<my_processor_id>',
                                'code' => 6,
                                'message' => 'Permission denied.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'securitySchemes' => [
            'bearer_token' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ],
        ],
    ],
    'security' => [],
    'externalDocs' => [
        'description' => 'Find out more about ApiOpenStudio',
        'url' => 'https://www.apiopenstudio.com',
    ],
];
$newApplicationOpenApi = [
    'openapi' => '3.0.3',
    'info' => [
        'title' => 'new_application1',
        'description' => 'These are the resources that belong to the new_application1 application.',
        'termsOfService' => 'https://www.apiopenstudio.com/license/',
        'contact' => [
            'name' => 'API Support',
            'email' => 'contact@localhost',
        ],
        'license' => [
            'name' => 'ApiOpenStudio Public License based on Mozilla Public License 2.0',
            'url' => 'https://www.apiopenstudio.com/license/',
        ],
        'version' => '1.0.0',
    ],
    'servers' => [
        [
            'url' => 'http://localhost/testing_acc/new_application1',
        ],
    ],
    'paths' => [],
    'components' => [
        'schemas' => [
            'GeneralError' => [
                'type' => 'object',
                'properties' => [
                    'result' => [
                        'type' => 'string',
                    ],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                                'format' => 'int32',
                            ],
                            'code' => [
                                'type' => 'integer',
                                'format' => 'int32',
                            ],
                            'message' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'responses' => [
            'GeneralError' => [
                'description' => 'General Error',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/GeneralError',
                        ],
                        'example' => [
                            'result' => 'error',
                            'data' => [
                                'id' => '<my_processor_id>',
                                'code' => 6,
                                'message' => 'Oops, something went wrong.',
                            ],
                        ],
                    ],
                ],
            ],
            'Unauthorised' => [
                'description' => 'Unauthorised',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/GeneralError',
                        ],
                        'example' => [
                            'result' => 'error',
                            'data' => [
                                'id' => '<my_processor_id>',
                                'code' => 4,
                                'message' => 'Invalid token.',
                            ],
                        ],
                    ],
                ],
            ],
            'Forbidden' => [
                'description' => 'Forbidden',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/GeneralError',
                        ],
                        'example' => [
                            'result' => 'error',
                            'data' => [
                                'id' => '<my_processor_id>',
                                'code' => 6,
                                'message' => 'Permission denied.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'securitySchemes' => [
            'bearer_token' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ],
        ],
    ],
    'security' => [],
    'externalDocs' => [
        'description' => 'Find out more about ApiOpenStudio',
        'url' => 'https://www.apiopenstudio.com',
    ],
];

$validCreateEditDeleteUsers = [
    [
        'username' => getenv('TESTER_ADMINISTRATOR_NAME'),
        'password' => getenv('TESTER_ADMINISTRATOR_PASS'),
    ],
    [
        'username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'),
        'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS'),
    ],
];
$invalidCreateEditDeleteUsers = [
    [
        'username' => getenv('TESTER_APPLICATION_MANAGER_NAME'),
        'password' => getenv('TESTER_APPLICATION_MANAGER_PASS'),
    ],
    [
        'username' => getenv('TESTER_DEVELOPER_NAME'),
        'password' => getenv('TESTER_DEVELOPER_PASS'),
    ],
    [
        'username' => getenv('TESTER_CONSUMER_NAME'),
        'password' => getenv('TESTER_CONSUMER_PASS'),
    ],
];
$validReadUsers = [
    [
        'username' => getenv('TESTER_ADMINISTRATOR_NAME'),
        'password' => getenv('TESTER_ADMINISTRATOR_PASS'),
        'applications' => [
            1 => [
                'accid' => 1,
                'appid' => 1,
                'name' => 'core',
                'openapi' => $coreOpenApi,
            ],
            2 => [
                'accid' => 2,
                'appid' => 2,
                'name' => 'testing_app',
                'openapi' => $testingAppOpenApi,
            ],
        ],
    ],
    [
        'username' => getenv('TESTER_ACCOUNT_MANAGER_NAME'),
        'password' => getenv('TESTER_ACCOUNT_MANAGER_PASS'),
        'applications' => [
            2 => [
                'accid' => 2,
                'appid' => 2,
                'name' => 'testing_app',
                'openapi' => $testingAppOpenApi,
            ],
        ],
    ], [
        'username' => getenv('TESTER_APPLICATION_MANAGER_NAME'),
        'password' => getenv('TESTER_APPLICATION_MANAGER_PASS'),
        'applications' => [
            2 => [
                'accid' => 2,
                'appid' => 2,
                'name' => 'testing_app',
                'openapi' => $testingAppOpenApi,
            ],
        ],
    ], [
        'username' => getenv('TESTER_DEVELOPER_NAME'),
        'password' => getenv('TESTER_DEVELOPER_PASS'),
        'applications' => [
            2 => [
                'accid' => 2,
                'appid' => 2,
                'name' => 'testing_app',
                'openapi' => $testingAppOpenApi,
            ],
        ],
    ], [
        'username' => getenv('TESTER_CONSUMER_NAME'),
        'password' => getenv('TESTER_CONSUMER_PASS'),
        'applications' => [
            2 => [
                'accid' => 2,
                'appid' => 2,
                'name' => 'testing_app',
                'openapi' => $testingAppOpenApi,
            ],
        ],
    ],
];

// Test application create/read/delete for each valid role
$uri = $I->getCoreBaseUri() . '/application';

foreach ($validCreateEditDeleteUsers as $user) {
    $I->performLogin($user['username'], $user['password']);
    $I->wantTo('Test creating an application with a valid user: ' . $user['username']);
    $I->sendPost($uri, ['accid' => 2, 'name' => 'new_application1']);
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $response = json_decode($I->getResponse(), true);
    $appid = $response['data']['appid'];
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => [
            'appid' => $appid,
            'accid' => 2,
            'name' => 'new_application1',
            'openapi' => $newApplicationOpenApi,
        ],
    ]);

    $I->wantTo('Test updating an application with a valid user: ' . $user['username']);
    $I->sendPut("$uri/$appid/2/edited_name");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $editedNewApplicationOpenApi = $newApplicationOpenApi;
    $editedNewApplicationOpenApi['info']['title'] = 'edited_name';
    $editedNewApplicationOpenApi['info']['description'] =
        'These are the resources that belong to the edited_name application.';
    $editedNewApplicationOpenApi['servers'] = [
        ['url' => 'http://localhost/testing_acc/edited_name'],
    ];
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => [
            'appid' => $appid,
            'accid' => 2,
            'name' => 'edited_name',
            'openapi' => $editedNewApplicationOpenApi,
        ],
    ]);

    $I->wantTo('Test deleting an application with a valid user: ' . $user['username']);
    $I->sendDelete("$uri/$appid");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => true,
    ]);
}

$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendPost($uri, ['accid' => 2, 'name' => 'new_application1']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$response = json_decode($I->getResponse(), true);
$appid = $response['data']['appid'];

foreach ($invalidCreateEditDeleteUsers as $user) {
    $I->performLogin($user['username'], $user['password']);
    $I->wantTo('Test creating an application with an invalid user: ' . $user['username']);
    $I->sendPost($uri, ['accid' => 2, 'name' => 'new_application2']);
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'id' => 'application_create_security',
            'message' => 'Permission denied.',
        ]
    ]);

    $I->wantTo('Test updating an application with an invalid user: ' . $user['username']);
    $I->sendPut("$uri/$appid/2/edited_name");
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'id' => 'application_update_security',
            'message' => 'Permission denied.',
        ]
    ]);

    $I->wantTo('Test deleting an application with an invalid user: ' . $user['username']);
    $I->sendDelete("$uri/$appid");
    $I->seeResponseCodeIs(403);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'id' => 'application_delete_security',
            'message' => 'Permission denied.',
        ]
    ]);
}

// Test individual account read for a user
foreach ($validReadUsers as $user) {
    $I->performLogin($user['username'], $user['password']);
    if ($user['username'] == getenv('TESTER_ADMINISTRATOR_NAME')) {
        $I->wantTo('Test reading the core application with user: ' . $user['username']);
        $I->sendGet($uri, ['application_id' => 1]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'result' => 'ok',
            'data' => [
                1 => [
                    'accid' => 1,
                    'appid' => 1,
                    'name' => 'core',
                    'openapi' => $coreOpenApi,
                ],
            ]
        ]);

        $I->wantTo('Test reading the testing_application application with user: ' . $user['username']);
        $I->sendGet($uri, ['application_id' => 2]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'result' => 'ok',
            'data' => [
                2 => [
                    'accid' => 2,
                    'appid' => 2,
                    'name' => 'testing_app',
                    'openapi' => [],
                ],
            ]
        ]);

        $I->wantTo('Test reading the new application with user: ' . $user['username']);
        $I->sendGet($uri, ['application_id' => $appid]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'result' => 'ok',
            'data' => [
                $appid => [
                    'accid' => 2,
                    'appid' => $appid,
                    'name' => 'new_application1',
                    'openapi' => $newApplicationOpenApi,
                ]
            ]
        ]);
    } else {
        $I->wantTo('Test reading the core application with user: ' . $user['username']);
        $I->sendGet($uri, ['application_id' => 1]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([]);

        $I->wantTo('Test reading the testing_application application with user: ' . $user['username']);
        $I->sendGet($uri, ['application_id' => 2]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'result' => 'ok',
            'data' => [
                2 => [
                    'accid' => 2,
                    'appid' => 2,
                    'name' => 'testing_app',
                    'openapi' => [],
                ],
            ]
        ]);

        $I->wantTo('Test reading the new application with user: ' . $user['username']);
        $I->sendGet($uri, ['application_id' => $appid]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([]);
    }
}

//// Test all account read for all users.
//foreach ($validReadUsers as $user) {
//    $I->performLogin($user['username'], $user['password']);
//    $I->wantTo('Test reading an application with a valid user: ' . $user['username']);
//    $I->sendGet($uri);
//    $I->seeResponseCodeIs(200);
//    $I->seeResponseIsJson();
//    $response = [
//        'result' => 'ok',
//        'data' => $user['applications'],
//    ];
//    $response[$appid] = [
//        'accid' => 2,
//        'appid' => $appid,
//        'name' => 'new_application1',
//        'openapi' => $newApplicationOpenApi,
//    ];
//    if ($user['username'] == getenv('TESTER_ADMINISTRATOR_NAME')) {
//        $response[$appid] = [
//            'accid' => 2,
//            'appid' => $appid,
//            'name' => 'new_application1',
//            'openapi' => $newApplicationOpenApi,
//        ];
//    } else {
//        $I->seeResponseContainsJson($response);
//    }
//}

// Clean up
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->sendDelete("$uri/$appid");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
