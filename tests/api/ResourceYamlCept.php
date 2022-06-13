<?php

use function PHPUnit\Framework\assertEquals;

$I = new ApiTester($scenario);

$yamlGoodFilename = 'resourceGood.yaml';
$badIdentities = [
    [getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS')],
    [getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS')],
    [getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS')],
    [getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS')],
];
$goodIdentities = [
    [getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS')]];

foreach ($badIdentities as $badIdentity) {
    $I->wantTo('Test resource create for ' . $badIdentity[0]);
    $I->performLogin($badIdentity[0], $badIdentity[1]);
    $I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource',
        [
            'name' => 'not allowed to post a resource',
            'description' => 'test not allowed to post a resource',
            'url' => 'test/not/allowed',
            'method' => 'post',
            'appid' => 2,
            'ttl' => 0,
            'meta' => "security:
  function: token_role
  id: test_varuri_processor_security
  token:
    function: var_get
    id: test_varuri_processor_security_token
    name: token
  role: Consumer
process:
  function: var_uri
  id: test varuri processor process
  index:
    function: var_get
    id: test varuri processor process varget
    name: index"
        ]
    );
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'resource_create_security',
        ],
    ]);

    $I->wantTo('Test resource delete for ' . $badIdentity[0]);
    $I->sendDelete(
        $I->getCoreBaseUri() . '/resource/0',
    );
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'resource_delete_security',
        ],
    ]);

    $I->wantTo('Test resource export for ' . $badIdentity[0]);
    $I->sendGet(
        $I->getCoreBaseUri() . '/resource/export/yaml/14',
    );
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'resource_export_security',
        ],
    ]);

    $I->wantTo('Test resource import for ' . $badIdentity[0]);
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlGoodFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlGoodFilename)),
                'tmp_name' => codecept_data_dir($yamlGoodFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'resource_import_security',
        ],
    ]);

    $I->wantTo('Test resource read for ' . $badIdentity[0]);
    $I->sendGet(
        $I->getCoreBaseUri() . '/resource',
        ['resid' => 33]
    );
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'resource_read_security',
        ],
    ]);
}

foreach ($goodIdentities as $goodIdentity) {
    $I->wantTo('Test resource create for ' . $goodIdentity[0]);
    $I->performLogin($goodIdentity[0], $goodIdentity[1]);
    $I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource',
        [
            'name' => 'Test allowed to create a resource',
            'description' => 'test allowed to create a resource',
            'uri' => 'test/resource_create/allowed',
            'method' => 'post',
            'appid' => 2,
            'ttl' => 0,
            "metadata" => [
                "security" => [
                    "function" => "validate_token_roles",
                    "id" => "test_security",
                    "roles" => ["Consumer"]
                ],
                "process" => [
                    "processor" => "var_int",
                    "id" => "test allowed to create process",
                    "value" => 32
                ]
            ]
        ]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseMatchesJsonType([
        'result' => 'string',
        'data' => [
            'resid' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'appid' => 'integer',
            'method' => 'string',
            'uri' => 'string',
            'ttl' => 'integer',
            'meta' => 'array',
            'openapi' => 'array',
        ],
    ]);

    $I->wantTo('Test resource read for ' . $goodIdentity[0]);
    $I->sendGet(
        $I->getCoreBaseUri() . '/resource',
        ['keyword' => 'allowed to create a resource']
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseMatchesJsonType([
        'result' => 'string',
        'data' => [
            [
                'resid' => 'integer',
                'name' => 'string',
                'description' => 'string',
                'appid' => 'integer',
                'method' => 'string',
                'uri' => 'string',
                'ttl' => 'integer',
                'meta' => 'array',
                'openapi' => 'array',
            ],
        ],
    ]);
    $json = json_decode($I->getResponse(), true);
    $resid = $json['data'][0]['resid'];

    $I->wantTo('Test resource update for ' . $goodIdentity[0]);
    $I->sendPut(
        $I->getCoreBaseUri() . "/resource/$resid",
        json_encode([
            "name" => "Test allowed to update a resource",
            "description" => "test allowed to update a resource",
            "uri" => "test/resource_update/allowed",
            "method" => "post",
            "appid" => 2,
            "ttl" => 0,
            "metadata" => [
                "security" => [
                    "function" => "validate_token_roles",
                    "id" => "test_security",
                    "roles" => ["Consumer"]
                ],
                "process" => [
                    "processor" => "var_int",
                    "id" => "test allowed to update process",
                    "value" => 32
                ]
            ]
        ])
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseMatchesJsonType([
        'result' => 'string',
        'data' => [
            'resid' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'appid' => 'integer',
            'method' => 'string',
            'uri' => 'string',
            'ttl' => 'integer',
            'meta' => 'array',
            'openapi' => 'array',
        ],
    ]);

    $I->wantTo('Test resource JSON export for ' . $goodIdentity[0]);
    $I->deleteHeader('Accept');
    $I->haveHttpHeader('Accept', 'application/text');
    $I->sendGet(
        $I->getCoreBaseUri() . "/resource/export/json/$resid",
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseMatchesJsonType([
        'resid' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'appid' => 'integer',
        'method' => 'string',
        'uri' => 'string',
        'ttl' => 'integer',
        'security' => 'array',
        'process' => 'array',
    ]);
    $I->deleteHeader('Accept');
    $I->haveHttpHeader('Accept', 'application/json');

    $I->wantTo('Test resource YAML export for ' . $goodIdentity[0]);
    $I->deleteHeader('Accept');
    $I->haveHttpHeader('Accept', 'application/text');
    $I->sendGet(
        $I->getCoreBaseUri() . "/resource/export/yaml/$resid",
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseContains("resid:");
    $I->seeResponseContains("name: 'Test allowed to update a resource'");
    $I->seeResponseContains("description: 'test allowed to update a resource");
    $I->seeResponseContains("appid: 2");
    $I->seeResponseContains("method: post");
    $I->seeResponseContains("uri: test/resource_update/allowed");
    $I->seeResponseContains("ttl: 0");
    $I->seeResponseContains("security:");
    $I->seeResponseContains("   function: validate_token_roles");
    $I->seeResponseContains("   id: test_security");
    $I->seeResponseContains("   roles:");
    $I->seeResponseContains("       - Consumer");
    $I->seeResponseContains("process:");
    $I->seeResponseContains("   processor: var_int");
    $I->seeResponseContains("   id: 'test allowed to update process'");
    $I->seeResponseContains("   value: 32");
    $I->deleteHeader('Accept');
    $I->haveHttpHeader('Accept', 'application/json');

    $I->wantTo('Test resource delete for ' . $goodIdentity[0]);
    $I->sendDelete(
        $I->getCoreBaseUri() . "/resource/$resid",
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseContainsJson([
        'result' => 'ok',
        'data' => true
    ]);

    $I->wantTo('create a new resource from YAML missing name attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoName.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Missing name in new resource.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML missing uri attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoUri.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Missing uri in new resource.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_read_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML missing description attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoDescription.yaml';
    $I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Missing description in new resource.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML missing method attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoMethod.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Missing method in new resource.',
        ],
    ]);
    // We cannot test tearing down the resource here,
    // because we will be unable to fetch the resource due to missing method attr.

    $I->wantTo('create a new resource from YAML missing ttl attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoTtl.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Missing ttl in new resource.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML negative ttl attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceTtl-1.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Negative ttl in new resource.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML missing security attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoSecurity.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(200);
    $I->seeResponseContains('true');

    $I->wantTo('create a new resource from YAML missing process attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoProcess.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Missing process in new resource.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML missing output attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceNoOutput.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(200);
    $I->seeResponseContains('true');

    $I->wantTo('create a new resource from YAML with a string value in process attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceStaticString.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Invalid process declaration, only processors allowed.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with an integer value in process attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceStaticInt.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Invalid process declaration, only processors allowed.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with an array value in process attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceStaticArray.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Invalid process declaration, only processors allowed.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with an object value in process attr for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceStaticObj.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Invalid process declaration, only processors allowed.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with an non array output structure for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceOutputString.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Invalid output declaration. Only processor, array of processors or "response" allowed.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo(
        'create a new resource from YAML with an associative array output structure for ' . $goodIdentity[0]
    );
    $yamlFilename = 'resourceOutputAssocArr.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Invalid output declaration. Only processor, array of processors or "response" allowed.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo(
        'create a new resource from YAML with a func val missing in output structure for ' . $goodIdentity[0]
    );
    $yamlFilename = 'resourceOutputEmptyFunc.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Invalid output declaration. Only processor, array of processors or "response" allowed.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with resource only output for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceOutputResponseOnly.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(200);

    $I->wantTo('create a new resource from YAML with incorrect processor for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceRequireFuncType.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Unknown processor: Fucntions.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with less than min inputs for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceBadMin.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => "Input 'items' in processor 'test resource with bad min process' requires min 2.",
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with more than max inputs for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceBadMax.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => "Input 'value' in processor 'test resource with bad max process' requires max 1.",
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML for an account without developer access for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceAccountNoAccess.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Unauthorised: you do not have permissions for this application.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);

    $I->wantTo('create a new resource from YAML with identical Ids in processors for ' . $goodIdentity[0]);
    $yamlFilename = 'resourceIdenticalId.yaml';
    $I->sendPOST(
        $I->getCoreBaseUri() . '/resource/import',
        [],
        [
            'resource_file' => [
                'name' => $yamlFilename,
                'type' => 'file',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir($yamlFilename)),
                'tmp_name' => codecept_data_dir($yamlFilename),
            ],
        ]
    );
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_import_process',
            'code' => 1,
            'message' => 'Identical IDs in new resource: resource identical ids.',
        ],
    ]);
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson([
        'result' => 'error',
        'data' => [
            'id' => 'resource_delete_process',
            'code' => 6,
            'message' => 'No resources found or insufficient privileges.',
        ],
    ]);
}

// Tear down the ResourceYaml test resource.
$I->deleteResource(2, 'post', 'test/resource_create/allowed');
