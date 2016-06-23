<?php
$I = new ApiTester($scenario);
$I->performLogin();
$uri = '/' . $I->getMyApplicationName() . '/processors/all';

$I->wantTo('call /processors/all and see the result.');
$I->sendGet($uri, ['token' => $I->getMyStoredToken()]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseMatchesJsonType([
  'name' => 'string',
  'description' => 'string',
  'application' => 'string',
  'input' => 'array',
]);
foreach (\GuzzleHttp\json_decode($I->getResponse()) as $processor) {
  foreach ($processor->input as $inputName => $inputDetails) {
    if (empty($inputDetails->description)) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . " is missing a description on its input: $inputName");
    }
    if (empty($inputDetails->cardinality)) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . " is missing cardinality on its input: $inputName");
    }
    if (!isset($inputDetails->cardinality[0])) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . " is missing min cardinality on its input: $inputName");
    }
    if (!is_integer($inputDetails->cardinality[0] + 0)) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . ' min cardinality must be an integer: ' . $inputDetails->cardinality[0]);
    }
    if ($inputDetails->cardinality[0] < 0) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . ' min cardinality must be a positive value: ' . $inputDetails->cardinality[0]);
    }
    if (!isset($inputDetails->cardinality[1])) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . " is missing max cardinality on its input: $inputName");
    }
    if (!is_integer($inputDetails->cardinality[1] + 0) && $inputDetails->cardinality[1] != '*') {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . ' max cardinality must be an integer or "*": ' . $inputDetails->cardinality[1]);
    }
    if (is_integer($inputDetails->cardinality[1] + 0) && $inputDetails->cardinality[1] < 0) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . ' max cardinality must be a positive value: ' . $inputDetails->cardinality[1]);
    }
    if (empty($inputDetails->accepts)) {
      \PHPUnit_Framework_Assert::assertTrue(false,  'the processor: ' . $processor->name . " is missing an accepts on its input: $inputName");
    }
  }
}
