<?php

/** @var Scenario $scenario */

use Codeception\Scenario;

$I = new ApiTester($scenario);

$I->wantTo('Test /processors is only available to developers.');
$uri = $I->getCoreBaseUri() . '/processors';
$I->performLogin(getenv('TESTER_ADMINISTRATOR_NAME'), getenv('TESTER_ADMINISTRATOR_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(403);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'processors_security',
        ]
    ]
);
$I->performLogin(getenv('TESTER_APPLICATION_MANAGER_NAME'), getenv('TESTER_APPLICATION_MANAGER_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(403);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'processors_security',
        ]
    ]
);
$I->performLogin(getenv('TESTER_ACCOUNT_MANAGER_NAME'), getenv('TESTER_ACCOUNT_MANAGER_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(403);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Permission denied.',
            'id' => 'processors_security',
        ]
    ]
);
$I->performLogin(getenv('TESTER_CONSUMER_NAME'), getenv('TESTER_CONSUMER_PASS'));
$I->haveHttpHeader('Authorization', 'Bearer ' . $I->getMyStoredToken());
$I->sendGet($uri);
$I->seeResponseCodeIs(403);
$I->seeResponseContainsJson(
    [
        'error' => [
            'code' => 4,
            'message' => 'Permission denied.',
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

$I->wantTo('Validate all necessary details are in each processor.');
foreach ($json as $index => $processor) {
    if (empty($processor['name'])) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            "the processor: $index is missing a name in its details."
        );
    }
    $processorName = $processor['name'];
    if (empty($processor['machineName'])) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            "The processor: $processorName is missing a machineName in its details."
        );
    }
    if (empty($processor['description'])) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            "The processor: $processorName is missing a description in its details."
        );
    }
    if (empty($processor['menu'])) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            "The processor: $processorName is missing a menu in its details."
        );
    }
    if (!isset($processor['input'])) {
        PHPUnit_Framework_Assert::assertTrue(
            false,
            "The processor: $processorName is missing an input in its details."
        );
    }
    foreach ($processor['input'] as $inputKey => $input) {
        $inputKeys = array_keys($input);
        if (!in_array('default', $inputKeys)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName needs a default on its input: $inputKey"
            );
        }
        if (is_numeric($inputKey)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName must have a textual key for input ($inputKey)"
            );
        }
        if (empty($input['description'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName is missing description for input ($inputKey)"
            );
        }
        if (!isset($input['cardinality'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName is missing cardinality on its input: $inputKey"
            );
        }
        if (!is_array($input['cardinality'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName must have cardinality of type array on its input: $inputKey"
            );
        }
        if (sizeof($input['cardinality']) != 2) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName must have input cardinality of [min, max]: $inputKey"
            );
        }
        if (!is_integer($input['cardinality'][0] + 0)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName min cardinality must be an integer on its input: $inputKey"
            );
        }
        if ($input['cardinality'][0] < 0) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName min cardinality must be a positive value on its input: $inputKey"
            );
        }
        if ($input['cardinality'][1] != '*' && !is_integer($input['cardinality'][1] + 0)) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName max cardinality must be an integer or '*' on its input: $inputKey"
            );
        }
        if (is_integer($input['cardinality'][1]) && $input['cardinality'][1] < 0) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName max cardinality must be a positive value on its input: $inputKey"
            );
        }
        if (!isset($input['literalAllowed'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName needs a literalAllowed  on its input: $inputKey"
            );
        }
        if (!is_bool($input['literalAllowed'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName literalAllowed must be a boolean on its input: $inputKey"
            );
        }
        if (!isset($input['limitProcessors'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName needs a limitProcessors  on its input: $inputKey"
            );
        }
        if (!isset($input['limitTypes'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName needs a limitTypes  on its input: $inputKey"
            );
        }
        foreach ($input['limitTypes'] as $limitType) {
            $limitTypes = [
                'boolean',
                'integer',
                'float',
                'text',
                'array',
                'json',
                'html',
                'xml',
                'image',
                'file',
                'empty',
            ];
            if (!in_array($limitType, $limitTypes)) {
                $message = "The processor: $processorName can only have a value of ";
                $message .= implode(', ', $limitTypes) . " on its limitTypes: $inputKey";
                PHPUnit_Framework_Assert::assertTrue(
                    false,
                    $message
                );
            }
        }
        if (!isset($input['limitValues'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName needs a limitValues on its input: $inputKey"
            );
        }
        if (!is_array($input['limitValues'])) {
            PHPUnit_Framework_Assert::assertTrue(
                false,
                "The processor: $processorName limitValues must be an array on its input: $inputKey"
            );
        }
    }
}
