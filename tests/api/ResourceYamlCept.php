<?php 
$I = new ApiTester($scenario);
$I->performLogin();
$I->haveHttpHeader('Accept', 'application/json');
$uri = '/testing/resource/yaml';

$I->wantTo('create a new resource from good YAML and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceGood.yaml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->setYamlFilename('resourceGood.yaml');
$I->tearDownTestFromYaml();

$I->wantTo('create a new resource from YAML missing name attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoName.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing name in new resource.', 'id' => -1]]);
$I->setYamlFilename('resourceNoName.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing uri attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoUri.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing uri in new resource.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing description attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoDescription.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing description in new resource.', 'id' => -1]]);
$I->setYamlFilename('resourceNoDescription.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing method attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoMethod.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing method in new resource.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing ttl attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoTtl.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing or negative ttl in new resource.', 'id' => -1]]);
$I->setYamlFilename('resourceNoTtl.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML negative ttl attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceTtl-1.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing or negative ttl in new resource.', 'id' => -1]]);
$I->setYamlFilename('resourceTtl-1.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);
/*
$I->wantTo('create a new resource from YAML missing security attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoSecurity.yaml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->setYamlFilename('resourceNoSecurity.yaml');
$I->tearDownTestFromYaml();

$I->wantTo('create a new resource from YAML missing process attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoProcess.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing process in new resource.', 'id' => -1]]);
$I->setYamlFilename('resourceNoProcess.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing output attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoOutput.yaml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->setYamlFilename('resourceNoOutput.yaml');
$I->tearDownTestFromYaml();

$I->wantTo('create a new resource from YAML missing an id attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceFunctionNoId.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Missing ID in a function.", "id" => -1]]);
$I->setYamlFilename('resourceFunctionNoId.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with a string value in process attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceStaticString.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Missing ID in a function.", "id" => -1]]);
$I->setYamlFilename('resourceFunctionNoId.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with an integer value in process attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceStaticInt.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Missing ID in a function.", "id" => -1]]);
$I->setYamlFilename('resourceFunctionNoId.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with an array value in process attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceStaticArray.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Missing ID in a function.", "id" => -1]]);
$I->setYamlFilename('resourceFunctionNoId.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with an object value in process attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceStaticObj.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Missing ID in a function.", "id" => -1]]);
$I->setYamlFilename('resourceFunctionNoId.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with an non array output structure and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceOutputString.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => 'Invalid output structure.', "id" => -1]]);
$I->setYamlFilename('ResourceOutputString.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with an associative array output structure and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceOutputAssocArr.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Invalid output structure.", "id" => -1]]);
$I->setYamlFilename('ResourceOutputAssocArr.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with a func val missing in output structure and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceOutputEmptyFunc.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => 'Missing function at index 0.', "id" => -1]]);
$I->setYamlFilename('ResourceOutputEmptyFunc.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with resource only output and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceOutputResponseOnly.yaml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->setYamlFilename('ResourceOutputResponseOnly.yaml');
$I->tearDownTestFromYaml();

$I->wantTo('create a new resource from YAML with good fragments structure and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentGood.yaml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->setYamlFilename('ResourceFragmentGood.yaml');
$I->tearDownTestFromYaml();

$I->wantTo('create a new resource from YAML with string in fragments structure and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentString.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => 'Invalid fragments structure.', "id" => -1]]);
$I->setYamlFilename('ResourceFragmentString.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with string in fragments structure and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentString.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => 'Invalid fragments structure.', "id" => -1]]);
$I->setYamlFilename('ResourceFragmentString.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with normal array in fragments structure and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceFragmentArray.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => 'Invalid fragments structure.', "id" => -1]]);
$I->setYamlFilename('ResourceFragmentArray.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with incorrect function and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceRequireFuncType.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => 'Invalid function in options: ProcessorsAll.', "id" => 5]]);
$I->setYamlFilename('ResourceRequireFuncType.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with less than min inputs and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceBadMin.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Input 'sources' requires min 2.", "id" => 3]]);
$I->setYamlFilename('ResourceBadMin.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with more than max inputs and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceBadMax.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "Input 'value' requires max 1.", "id" => 3]]);
$I->setYamlFilename('ResourceBadMax.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML with reserved method & uri and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/ResourceReserved.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(["error" => ["code" => 6, "message" => "This resource is reserved (get + processors/all).", "id" => -1]]);
$I->setYamlFilename('ResourceReserved.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);
*/