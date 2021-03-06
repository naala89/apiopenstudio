<?php

$I = new ApiTester($scenario);
$I->performLogin();
$uri = $I->getCoreBaseUri() . '/resource/import';

$I->wantTo('create a new resource from good YAML and see the result');
$yamlFilename = 'resourceGood.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->tearDownTestFromYaml($yamlFilename);
$I->deleteHeader('Authorization');

$I->wantTo('create a new resource from YAML missing name attr and see the result');
$yamlFilename = 'resourceNoName.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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

$I->wantTo('create a new resource from YAML missing uri attr and see the result');
$yamlFilename = 'resourceNoUri.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
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

$I->wantTo('create a new resource from YAML missing description attr and see the result');
$yamlFilename = 'resourceNoDescription.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
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
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML missing method attr and see the result');
$yamlFilename = 'resourceNoMethod.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
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

$I->wantTo('create a new resource from YAML missing ttl attr and see the result');
$yamlFilename = 'resourceNoTtl.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
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
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ],
    ]
);

$I->wantTo('create a new resource from YAML negative ttl attr and see the result');
$yamlFilename = 'resourceTtl-1.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
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
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ],
    ]
);

$I->wantTo('create a new resource from YAML missing security attr and see the result');
$yamlFilename = 'resourceNoSecurity.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

$I->wantTo('create a new resource from YAML missing process attr and see the result');
$yamlFilename = 'resourceNoProcess.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'Missing process in new resource.',
        'id' => -1
    ]
    ]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ],
    ]
);

$I->wantTo('create a new resource from YAML missing output attr and see the result');
$yamlFilename = 'resourceNoOutput.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(200);
$I->seeResponseContains('true');

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
//$I->deleteHeader('Authorization');
//$I->seeResponseCodeIs(400);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson([
//        "error" => [
//        "code" => 6,
//        "message" => "Invalid function, id attribute missing.",
//        "id" => -1,
//    ],
//]);
//$I->tearDownTestFromYaml($yamlFilename);
//$I->seeResponseCodeIs(400);
//$I->seeResponseContainsJson([
//    'error' => [
//        'code' => 6,
//        'message' => 'No resources found.',
//        'id' => 'resource_read_process',
//    ],
//]);

$I->wantTo('create a new resource from YAML with a string value in process attr and see the result');
$yamlFilename = 'resourceStaticString.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
        "error" => [
        "code" => 6,
        "message" => "Invalid process declaration, only functions allowed.",
        "id" => -1,
        ],
    ]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with an integer value in process attr and see the result');
$yamlFilename = 'resourceStaticInt.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
    "error" => [
        "code" => 6,
        "message" => "Invalid process declaration, only functions allowed.",
        "id" => -1,
    ],
    ]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with an array value in process attr and see the result');
$yamlFilename = 'resourceStaticArray.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
    "error" => [
        "code" => 6,
        "message" => "Invalid process declaration, only functions allowed.",
        "id" => -1,
    ],
    ]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with an object value in process attr and see the result');
$yamlFilename = 'resourceStaticObj.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
    "error" => [
        "code" => 6,
        "message" => "Invalid process declaration, only functions allowed.",
        "id" => -1,
    ],
    ]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with an non array output structure and see the result');
$yamlFilename = 'resourceOutputString.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => 'Invalid output declaration, only functions or array of functions or "response" allowed.',
    "id" => -1
    ]]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with an associative array output structure and see the result');
$yamlFilename = 'resourceOutputAssocArr.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => 'Invalid output declaration, only functions or array of functions or "response" allowed.',
    "id" => -1
    ]]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with a func val missing in output structure and see the result');
$yamlFilename = 'resourceOutputEmptyFunc.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => 'Invalid output declaration, only functions or array of functions or "response" allowed.',
    "id" => -1
    ]]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with resource only output and see the result');
$yamlFilename = 'resourceOutputResponseOnly.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(200);

$I->wantTo('create a new resource from YAML with incorrect function and see the result');
$yamlFilename = 'ResourceRequireFuncType.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$message = 'Processor test resource required func type xml options ';
$message .= 'bad is an invalid function type (only "field" allowed).';
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => $message,
    "id" => 'test resource required func type xml',
    ]]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with less than min inputs and see the result');
$yamlFilename = 'ResourceBadMin.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => "Input 'sources' in function 'test resource with bad min process' requires min 2.",
    "id" => 'test resource with bad min process'
    ]]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with more than max inputs and see the result');
$yamlFilename = 'ResourceBadMax.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => "Input 'value' in function 'test resource with bad max process' requires max 1.",
    "id" => 'test resource with bad max process',
    ]]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML for an account without developer access and see the result');
$yamlFilename = 'resourceAccountNoAccess.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    ["error" => [
    "code" => 6,
    "message" => 'Unauthorised: you do not have permissions for this application.',
    "id" => 'resource_import_process',
    ]]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

$I->wantTo('create a new resource from YAML with identiocal Ids in processors and see the result');
$yamlFilename = 'resourceIdenticalId.yaml';
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendPOST(
    $uri,
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
$I->deleteHeader('Authorization');
$I->seeResponseCodeIs(400);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(
    [
    "error" => [
        "code" => 6,
        "message" => 'Identical IDs in new resource: resource identical ids.',
        "id" => -1,
        ]
    ]
);
$I->tearDownTestFromYaml($yamlFilename);
$I->seeResponseCodeIs(400);
$I->seeResponseContainsJson(
    [
    'error' => [
        'code' => 6,
        'message' => 'No resources found.',
        'id' => 'resource_read_process',
    ]
    ]
);

//$I->wantTo('create a new resource from YAML with good fragments structure and see the result');
//$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentGood.yaml']);
//$yamlFilename = 'ResourceFragmentGood.yaml';
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
//$I->deleteHeader('Authorization');
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
//$I->deleteHeader('Authorization');
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
//$yamlFilename = 'ResourceFragmentString.yaml';
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
//$I->deleteHeader('Authorization');
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
//        'message' => 'No resources found.',
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
