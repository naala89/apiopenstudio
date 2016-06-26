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
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing name in new resource.', 'id' => 3]]);
$I->setYamlFilename('resourceNoName.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing uri attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoUri.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing uri in new resource.', 'id' => 3]]);

$I->wantTo('create a new resource from YAML missing description attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoDescription.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing description in new resource.', 'id' => 3]]);
$I->setYamlFilename('resourceNoDescription.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing method attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoMethod.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing method in new resource.', 'id' => 3]]);

$I->wantTo('create a new resource from YAML missing ttl attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoTtl.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing or negative ttl in new resource.', 'id' => 3]]);
$I->setYamlFilename('resourceNoTtl.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML negative ttl attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceTtl-1.yaml']);
$I->seeResponseCodeIs(406);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing or negative ttl in new resource.', 'id' => 3]]);
$I->setYamlFilename('resourceTtl-1.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

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
$I->seeResponseContainsJson(['error' => ['code' => 6, 'message' => 'Missing process in new resource.', 'id' => 3]]);
$I->setYamlFilename('resourceNoProcess.yaml');
$I->tearDownTestFromYaml(400, ['error' => ['code' => 2,'message' => 'Could not delete resource, not found.', 'id' => -1]]);

$I->wantTo('create a new resource from YAML missing output attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoOutput.yaml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->setYamlFilename('resourceNoOutput.yaml');
$I->tearDownTestFromYaml();

$I->wantTo('create a new resource from YAML missing output attr and see the result');
$I->sendPOST($uri, ['token' => $I->getMyStoredToken()], ['resource' => 'tests/_data/resourceNoOutput.yaml']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->setYamlFilename('resourceNoOutput.yaml');
$I->tearDownTestFromYaml();
