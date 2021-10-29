<?php

$I = new ApiTester($scenario);

$yamlGoodFilename = 'resourceGood.yaml';
$badIdentities = [
    [getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS')],
    [getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS')],
    [getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS')],
    [getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS')],
];
$goodIdentities = [
    [getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS')]]
;

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
    $I->seeResponseCodeIs(401);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Unauthorized for this call.',
                'id' => 'resource_create_security',
            ]
        ]
    );

    $I->wantTo('Test resource delete for ' . $badIdentity[0]);
    $I->sendDelete(
        $I->getCoreBaseUri() . '/resource/0',
    );
    $I->seeResponseCodeIs(401);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Unauthorized for this call.',
                'id' => 'resource_delete_security',
            ]
        ]
    );

    $I->wantTo('Test resource export for ' . $badIdentity[0]);
    $I->sendGet(
        $I->getCoreBaseUri() . '/resource/export/yaml/14',
    );
    $I->seeResponseCodeIs(401);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Unauthorized for this call.',
                'id' => 'resource_export_security',
            ]
        ]
    );

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
    $I->seeResponseCodeIs(401);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Unauthorized for this call.',
                'id' => 'resource_import_security',
            ]
        ]
    );

    $I->wantTo('Test resource read for ' . $badIdentity[0]);
    $I->sendGet(
        $I->getCoreBaseUri() . '/resource',
        ['resid' => 33]
    );
    $I->seeResponseCodeIs(401);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Unauthorized for this call.',
                'id' => 'resource_read_security',
            ]
        ]
    );
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
            'format' => 'yaml',
            'meta' => 'security:
  function: validate_token_roles
  id: test_security
  roles:
    - Consumer

process:
  processor: var_int
  id: test allowed to create process
  value: 32'
        ]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseContainsJson(["true"]);

    $I->wantTo('Test resource read for ' . $goodIdentity[0]);
    $I->sendGet(
        $I->getCoreBaseUri() . '/resource',
        ['keyword' => 'allowed to create a resource']
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseMatchesJsonType(
        [
            'resid' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'appid' => 'integer',
            'method' => 'string',
            'uri' => 'string',
            'ttl' => 'string',
            'meta' => 'string',
        ]
    );

    $json = json_decode($I->getResponse(), true);
    $resid = $json[0]['resid'];

    $I->wantTo('Test resource export for ' . $goodIdentity[0]);
    $I->sendGet(
        $I->getCoreBaseUri() . "/resource/export/json/$resid",
    );
    $I->seeResponseCodeIs(200);
//    $I->seeResponseIsJson();

    $I->wantTo('Test resource delete for ' . $goodIdentity[0]);
    $I->sendDelete(
        $I->getCoreBaseUri() . "/resource/$resid",
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseContainsJson(['true']);

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
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'Missing name in new resource.',
                'id' => 'resource_import_process',
            ]
        ]
    );

    $I->wantTo('create a new resource from YAML missing uri attr and for ' . $goodIdentity[0]);
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
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'Missing uri in new resource.',
                'id' => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'Missing description in new resource.',
                'id' => 'resource_import_process',
            ]
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'Missing method in new resource.',
                'id' => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'Missing ttl in new resource.',
                'id' => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ],
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'Negative ttl in new resource.',
                'id' => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ],
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'Missing process in new resource.',
                'id' => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ],
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => "Invalid process declaration, only processors allowed.",
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => "Invalid process declaration, only processors allowed.",
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => "Invalid process declaration, only processors allowed.",
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => "Invalid process declaration, only processors allowed.",
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => 'Invalid output declaration. Only processor, array of processors or "response" allowed.',
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

    $I->wantTo('create a new resource from YAML with an associative array output structure for ' . $goodIdentity[0]);
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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => 'Invalid output declaration. Only processor, array of processors or "response" allowed.',
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

    $I->wantTo('create a new resource from YAML with a func val missing in output structure for ' . $goodIdentity[0]);
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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => 'Invalid output declaration. Only processor, array of processors or "response" allowed.',
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $message = 'Processor test resource required func type xml options ';
    $message .= 'bad is an invalid processor type (only "field" allowed).';
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => $message,
                "id" => 'resource_import_process',
            ]
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => "Input 'sources' in processor 'test resource with bad min process' requires min 2.",
                "id" => 'resource_import_process'
            ]
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => "Input 'value' in processor 'test resource with bad max process' requires max 1.",
                "id" => 'resource_import_process',
            ]
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => 'Unauthorised: you do not have permissions for this application.',
                "id" => 'resource_import_process',
            ]
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );

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
    $I->seeResponseContainsJson(
        [
            "error" => [
                "code" => 6,
                "message" => 'Identical IDs in new resource: resource identical ids.',
                "id" => 'resource_import_process',
            ],
        ]
    );
    $I->tearDownTestFromYaml($yamlFilename);
    $I->seeResponseCodeIs(400);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 6,
                'message' => 'No resources found or insufficient privileges.',
                'id' => 'resource_read_process',
            ]
        ]
    );
}

//$I->wantTo('create a new resource from YAML missing an id attr and see the result');
//$yamlFilename = 'resourceFunctionNoId.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->sendPOST(
//    $uri,
//    [],
//    [
//        'resource_file' => [
//            'name' => $yamlFilename,
//            'type' => 'file',
//            'error' => UPLOAD_ERR_OK,
//            'size' => filesize(codecept_data_dir($yamlFilename)),
//            'tmp_name' => codecept_data_dir($yamlFilename),
//        ],
//    ]
//);
//$I->seeResponseCodeIs(400);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([
//        "error" => [
//        "code" => 6,
//        "message" => "Invalid processor, id attribute missing.",
//        "id" => -1,
//    ],
//]);
//$I->tearDownTestFromYaml($yamlFilename);
//$I->seeResponseCodeIs(400);
//$I->seeResponseContainsJson([
//    'error' => [
//        'code' => 6,
//        'message' => 'No resources found or insufficient privileges.',
//        'id' => 'resource_read_process',
//    ],
//]);

//$I->wantTo('create a new resource from YAML with good fragments structure and see the result');
//$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentGood.yaml']);
//$yamlFilename = 'resourceFragmentGood.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->sendPOST(
//    $uri,
//    [],
//    [
//        'resource_file' => [
//            'name' => $yamlFilename,
//            'type' => 'file',
//            'error' => UPLOAD_ERR_OK,
//            'size' => filesize(codecept_data_dir($yamlFilename)),
//            'tmp_name' => codecept_data_dir($yamlFilename),
//        ],
//    ]
//);
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseContains('true');
//$I->tearDownTestFromYaml($yamlFilename);
//$I->seeResponseCodeIs(200);

//$I->wantTo('create a new resource from YAML with string in fragments structure and see the result');
//$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentString.yaml']);
//$yamlFilename = 'resourceFragmentString.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->sendPOST(
//    $uri,
//    [],
//    [
//        'resource_file' => [
//            'name' => $yamlFilename,
//            'type' => 'file',
//            'error' => UPLOAD_ERR_OK,
//            'size' => filesize(codecept_data_dir($yamlFilename)),
//            'tmp_name' => codecept_data_dir($yamlFilename),
//        ],
//    ]
//);
//$I->seeResponseCodeIs(400);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(["error" => [
//    "code" => 6,
//    "message" => 'Invalid fragments structure in new resource.',
//    "id" => -1
//]]);
//$I->setYamlFilename('ResourceFragmentString.yaml');
//$I->tearDownTestFromYaml(400, ['error' => [
//    'code' => 2,
//    'message' => 'Could not delete resource, not found.',
//    'id' => -1
//]]);

//$I->wantTo('create a new resource from YAML with string in fragments structure and see the result');
//$yamlFilename = 'resourceFragmentString.yaml';
//$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
//$I->sendPOST(
//    $uri,
//    [],
//    [
//        'resource_file' => [
//            'name' => $yamlFilename,
//            'type' => 'file',
//            'error' => UPLOAD_ERR_OK,
//            'size' => filesize(codecept_data_dir($yamlFilename)),
//            'tmp_name' => codecept_data_dir($yamlFilename),
//        ],
//    ]
//);
//$I->seeResponseCodeIs(406);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(["error" => [
//    "code" => 6,
//    "message" => 'Invalid fragments structure in new resource.',
//    "id" => -1
//]]);
//$I->tearDownTestFromYaml($yamlFilename);
//$I->seeResponseCodeIs(400);
//$I->seeResponseContainsJson([
//    'error' => [
//        'code' => 6,
//        'message' => 'No resources found or insufficient privileges.',
//        'id' => 'resource_read_process',
//    ]
//]);

//$I->wantTo('create a new resource from YAML with normal array in fragments structure and see the result');
//$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentArray.yaml']);
//$I->seeResponseCodeIs(406);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(["error" => [
//    "code" => 6,
//    "message" => 'Invalid fragments structure in new resource.',
//    "id" => -1
//]]);
//$I->setYamlFilename('ResourceFragmentArray.yaml');
//$I->tearDownTestFromYaml(400, ['error' => [
//    'code' => 2,
//    'message' => 'Could not delete resource, not found.',
//    'id' => -1
//]]);
