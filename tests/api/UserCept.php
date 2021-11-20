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
$newUsers = [];
$count = 0;

foreach ($goodIdentities as $goodIdentity) {
    $I->performLogin($goodIdentity[0], $goodIdentity[1]);
    $count++;

    $I->wantTo('Test user create for ' . $goodIdentity[0]);
    $I->sendPOST(
        $I->getCoreBaseUri() . '/user',
        [
            'username' => "username$count",
            'email' => "email$count@foobar.com",
        ]
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseMatchesJsonType([
        'uid' => 'integer',
        'username' => 'string',
        'hash' => 'string',
        'passwordReset' => 'string',
        'passwordResetTtl' => 'string',
        'active' => 'integer',
        'honorific' => 'string',
        'nameFirst' => 'string',
        'nameLast' => 'string',
        'email' => 'string',
        'company' => 'string',
        'website' => 'string',
        'addressStreet' => 'string',
        'addressSuburb' => 'string',
        'addressCity' => 'string',
        'addressState' => 'string',
        'addressCountry' => 'string',
        'addressPostcode' => 'string',
        'phoneMobile' => 'string',
        'phoneWork' => 'string',
    ]);
    $response = json_decode($I->getResponse(), true);
    $uid = $response['uid'];
    $newUsers[$uid] = $response;

    $I->wantTo('Test user update for ' . $goodIdentity[0]);
    $I->sendPut(
        $I->getCoreBaseUri() . "/user/$uid",
        json_encode([
            'username' => "username$count",
            'password' => 'password',
            'active' => true,
            'honorific' => 'Mr',
            'name_first' => 'John',
            'name_last' => 'Doe',
            'email' => "john$count@foobar.com",
            'company' => 'Foobar',
            'website' => 'www.foobar.com',
            'street_address' => '1 street address',
            'address_suburb' => 'testerton',
            'address_city' => 'tester',
            'address_state' => 'yale',
            'address_country' => 'USA',
            'address_postcode' => '7654',
            'phone_mobile' => '1234567',
            'phone_work' => '2345678',
        ])
    );
    $I->seeResponseCodeIs(200);
    $I->seeResponseMatchesJsonType([
        'uid' => 'integer',
        'username' => 'string',
        'hash' => 'string',
        'passwordReset' => 'string',
        'passwordResetTtl' => 'string',
        'active' => 'integer',
        'honorific' => 'string',
        'nameFirst' => 'string',
        'nameLast' => 'string',
        'email' => 'string',
        'company' => 'string',
        'website' => 'string',
        'addressStreet' => 'string',
        'addressSuburb' => 'string',
        'addressCity' => 'string',
        'addressState' => 'string',
        'addressCountry' => 'string',
        'addressPostcode' => 'string',
        'phoneMobile' => 'string',
        'phoneWork' => 'string',
    ]);
    $response = json_decode($I->getResponse(), true);
    $uid = $response['uid'];
    $newUsers[$uid] = $response;
}

foreach ($badIdentities as $badIdentity) {
    $I->wantTo('Test user create for ' . $badIdentity[0]);
    $I->performLogin($badIdentity[0], $badIdentity[1]);
    $count++;

    $I->sendPOST(
        $I->getCoreBaseUri() . '/user',
        [
            'username' => "username$count",
            'email' => "email$count@foobar.com",
        ]
    );
    $I->seeResponseCodeIs(401);

    $I->wantTo('Test user update for ' . $badIdentity[0]);
    $I->sendPut(
        $I->getCoreBaseUri() . "/user/$uid",
        json_encode([
            'username' => "username$count",
            'password' => 'password',
            'active' => 0,
            'honorific' => 'Mr',
            'name_first' => 'John',
            'name_last' => 'Doe',
            'email' => "john@foobar.com",
            'company' => 'Foobar',
            'website' => 'www.foobar.com',
            'street_address' => '1 street address',
            'address_suburb' => 'testerton',
            'address_city' => 'tester',
            'address_state' => 'yale',
            'address_country' => 'USA',
            'address_postcode' => '7654',
            'phone_mobile' => '1234567',
            'phone_work' => '2345678',
        ])
    );
    $I->seeResponseCodeIs(401);

    $I->wantTo('Test user delete for ' . $badIdentity[0]);
    $I->sendDelete($I->getCoreBaseUri() . '/user/' . $uid);
    $I->seeResponseCodeIs(401);
}

$uids = array_keys($newUsers);
foreach ($goodIdentities as $goodIdentity) {
    $I->performLogin($goodIdentity[0], $goodIdentity[1]);

    $I->wantTo('Test user delete for ' . $goodIdentity[0]);
    $uid = array_pop($uids);
    $I->sendDelete($I->getCoreBaseUri() . '/user/' . $uid);
    $I->seeResponseCodeIs(200);
    $I->seeResponseContains('true');
}
