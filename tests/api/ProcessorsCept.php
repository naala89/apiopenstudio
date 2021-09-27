<?php
/** @var Scenario $scenario */

use Codeception\Scenario;

$I = new ApiTester($scenario);

$I->wantTo('Test /processors is only available to developers.');
$uri = $I->getCoreBaseUri() . '/processors';
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(401);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Unauthorized for this call.',
            'id' => 'processors_security',
        ]
    ]
);
$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(401);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Unauthorized for this call.',
            'id' => 'processors_security',
        ]
    ]
);
$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(401);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Unauthorized for this call.',
            'id' => 'processors_security',
        ]
    ]
);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(401);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Unauthorized for this call.',
            'id' => 'processors_security',
        ]
    ]
);
$I->performLogin(getenv('TESTER_DEVELOPER_NAME'), getenv('TESTER_DEVELOPER_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseMatchesJsonType(
    [
        'name' => 'string',
        'machineName' => 'string',
        'description' => 'string',
        'menu' => 'string',
        'input' => 'array',
    ]
);

$I->wantTo('Test that /processors/all, /processors and /processors/ will return all processors.');
$I->sendGet($uri);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
if (!is_array($json) || !count($json) > 1) {
    assert('Invalid JSON response');
}
$I->seeResponseMatchesJsonType(
    [
        'name' => 'string',
        'machineName' => 'string',
        'description' => 'string',
        'menu' => 'string',
        'input' => 'array',
    ]
);
$I->sendGet($uri . '/');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
if (!is_array($json) || !count($json) > 1) {
    assert('Invalid JSON response');
}
$I->seeResponseMatchesJsonType(
    [
        'name' => 'string',
        'machineName' => 'string',
        'description' => 'string',
        'menu' => 'string',
        'input' => 'array',
    ]
);
$I->sendGet($uri . '/all');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$json = json_decode($I->getResponse(), true);
if (!is_array($json) || !count($json) > 1) {
    assert('Invalid JSON response');
}
$I->seeResponseMatchesJsonType(
    [
        'name' => 'string',
        'machineName' => 'string',
        'description' => 'string',
        'menu' => 'string',
        'input' => 'array',
    ]
);

$I->wantTo('Validate all necessary details are in each processor.');
foreach (\GuzzleHttp\json_decode($I->getResponse()) as $index => $processor) {
    if (empty($processor->name)) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            'the processor: ' . $index . ' is missing a name in its details in its details.'
        );
    }
    if (empty($processor->machineName)) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            'the processor: ' . $processor->name . ' is missing a machineName in its details.'
        );
    }
    if (empty($processor->description)) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            'the processor: ' . $processor->name . ' is missing a description in its details.'
        );
    }
    if (empty($processor->menu)) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            'the processor: ' . $processor->name . ' is missing a menu in its details.'
        );
    }
    if (empty($processor->menu)) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            'the processor: ' . $processor->menu . ' is missing an application in its details.'
        );
    }
    if (!isset($processor->input)) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            'the processor: ' . $processor->name . ' is missing an input in its details.'
        );
    }
    foreach ($processor->input as $key => $val) {
        if (is_numeric($key)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " index must be a textual name for input ($key)"
            );
        }
        if (empty($val->description)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " needs a description for input input ($key)"
            );
        }
        if (!isset($val->cardinality)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " is missing cardinality on its input: $key"
            );
        }
        if (!is_array($val->cardinality)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " must have cardinality of type array on its input: $key"
            );
        }
        if (!isset($val->cardinality[0])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " is missing min cardinality on its input: $key"
            );
        }
        if (!is_integer($val->cardinality[0] + 0)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " min cardinality must be an integer on its input: $key"
            );
        }
        if ($val->cardinality[0] < 0) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " min cardinality must be a positive value on its input: $key"
            );
        }
        if (!isset($val->cardinality[1])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " is missing max cardinality on its input: $key"
            );
        }
        if ($val->cardinality[1] != '*' && !is_integer($val->cardinality[1] + 0)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " max cardinality must be an integer or " * " on its input: $key"
            );
        }
        if (is_integer($val->cardinality[1]) && $val->cardinality[1] < 0) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " max cardinality must be a positive value on its input: $key"
            );
        }
        if (!isset($val->literalAllowed)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " needs a literalAllowed  on its input: $key"
            );
        }
        if (!is_bool($val->literalAllowed)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " literalAllowed must be a boolean on its input: $key"
            );
        }
        if (!isset($val->limitProcessors)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " needs a limitProcessors  on its input: $key"
            );
        }
        if (!isset($val->limitTypes)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " needs a limitTypes  on its input: $key"
            );
        }
        foreach ($val->limitTypes as $limitType) {
            $limitTypes = [
                'boolean',
                'integer',
                'float',
                'text',
                'array',
                'json',
                'xml',
                'image',
                'file',
                'empty',
            ];
            if (!in_array($limitType, $limitTypes)) {
                $message = 'the processor: ' . $processor->name . " can only have a value of ";
                $message .= implode(', ', $limitTypes) . " on its limitTypes: $key";
                PHPUnit_Framework_Assert::assertTrue(
                    false,
                    $message
                );
            }
        }
        if (!isset($val->limitValues)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " needs a limitValues on its input: $key"
            );
        }
        if (!is_array($val->limitValues)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " limitValues must be an array on its input: $key"
            );
        }
        if (!isset($val->default)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                'the processor: ' . $processor->name . " needs a default on its input: $key"
            );
        }
    }
}
