<?php

$I = new ApiTester($scenario);

$badIdentities = [
    [getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS')],
    [getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS')],
];
$goodIdentities = [
    [getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS')],
    [getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS')],
    [getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS')],
];
$newRoles = [];

foreach ($goodIdentities as $goodIdentity) {
    $I->performLogin($goodIdentity[0], $goodIdentity[1]);

    $I->wantTo('Test role create for ' . $goodIdentity[0]);
    $I->sendPOST(
        $I->getCoreBaseUri() . '/role',
        ['name' => 'Test role create ' . $goodIdentity[0]]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseJsonMatchesJsonPath('$.rid');
    $I->seeResponseJsonMatchesJsonPath('$.name');
    $response = json_decode($I->getResponse(), true);
    $rid = $response['rid'];
    $newRoles[$rid] = $response['name'];

    $I->wantTo('Test role update for ' . $goodIdentity[0]);
    $I->sendPut(
        $I->getCoreBaseUri() . '/role',
        json_encode([
            'rid' => $rid,
            'name' => 'Test role update for ' . $goodIdentity[0],
        ])
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseJsonMatchesJsonPath('$.rid');
    $I->seeResponseJsonMatchesJsonPath('$.name');
    $response = json_decode($I->getResponse(), true);
    $rid = $response['rid'];
    $newRoles[$rid] = $response['name'];
}

foreach ($badIdentities as $badIdentity) {
    $I->wantTo('Test role create for ' . $badIdentity[0]);
    $I->performLogin($badIdentity[0], $badIdentity[1]);

    $I->sendPOST(
        $I->getCoreBaseUri() . '/role',
        ['name' => 'Test role create ' . $badIdentity[0]]
    );
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Permission denied.',
                'id' => 'role_create_security',
            ]
        ]
    );

    $I->wantTo('Test role update for ' . $badIdentity[0]);
    $I->sendPut(
        $I->getCoreBaseUri() . '/role',
        json_encode([
            'rid' => $rid,
            'name' => 'Test role update for ' . $badIdentity[0],
        ])
    );
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Permission denied.',
                'id' => 'role_update_security',
            ]
        ]
    );

    $I->wantTo('Test role delete for ' . $badIdentity[0]);
    $I->sendDelete($I->getCoreBaseUri() . '/role/' . $rid);
    $I->seeResponseCodeIs(403);
    $I->seeResponseContainsJson(
        [
            'error' => [
                'code' => 4,
                'message' => 'Permission denied.',
                'id' => 'role_delete_security',
            ]
        ]
    );
}

$rids = array_keys($newRoles);
foreach ($goodIdentities as $goodIdentity) {
    $I->performLogin($goodIdentity[0], $goodIdentity[1]);

    $I->wantTo('Test role delete for ' . $goodIdentity[0]);
    $rid = array_pop($rids);
    $I->sendDelete($I->getCoreBaseUri() . '/role/' . $rid);
    $I->seeResponseCodeIs(200);
    $I->seeResponseContains('true');
}
