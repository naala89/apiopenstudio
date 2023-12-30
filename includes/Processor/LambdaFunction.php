<?php

/**
 * Class LambdaFunction.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use Aws\Lambda\LambdaClient;
use Psr\Http\Message\StreamInterface;

/**
 * Class LambdaFunction
 *
 * Processor class to call an external processor in a Lambda.
 */
class LambdaFunction extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Lambda Function',
        'machineName' => 'lambda_function',
        'description' => 'Call an external processor in a Lambda.',
        'menu' => 'Data operation',
        'input' => [
            'data' => [
                'description' => 'Data input to be processed by the remote lambda. Values will be referenced in the remote Lambda, based on the field key.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => ['VarField'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
            ],
            'function_name' => [
                'description' => 'The function name to call. This can be an ARN or just the function name.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'iam_key' => [
                'description' => 'If the lambda is secured, enter the IAM key here.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'iam_secret' => [
                'description' => 'If the lambda is secured, enter the IAM secret here.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'aws_region' => [
                'description' => 'If the lambda is in a separate region, enter it here.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'report_error' => [
                'description' => 'Stop processing if the Lambda returns non 200 response.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $data = $this->val('data', true);
        $iamKey = $this->val('iam_key', true);
        $iamSecret = $this->val('iam_secret', true);
        $awsRegion = $this->val('aws_region', true);
        $functionName = $this->val('function_name', true);
        $reportError = $this->val('report_error', true);

        $args = [
            'version' => 'latest',
        ];
        if (!empty($awsRegion)) {
            $args['region'] = $awsRegion;
        }
        if (!empty($iamKey) && !empty($iamSecret)) {
            $args['credentials'] = [
                'key' => $iamKey,
                'secret' => $iamSecret,
            ];
        }
        $client = new LambdaClient($args);

        try {
            $response = $client->invoke([
                'FunctionName' => $functionName,
                'Payload' => json_encode($data),
                'Logtype' => 'None',
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 5, $this->id);
        }

        if ($reportError && $response->get('StatusCode') !== 200) {
            throw new ApiException('Lambda error', 5, $this->id, $response->get('StatusCode'));
        }

        $payload = $response->get('Payload');

        $result = $payload instanceof StreamInterface ? $payload->__toString() : $payload;

        return new DataContainer($result);
    }
}
