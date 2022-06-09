<?php

/**
 * Class Json.
 *
 * @package    ApiOpenStudio\Output
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Output;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\ConvertToJsonTrait;
use ApiOpenStudio\Core\DetectTypeTrait;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\OutputResponse;
use ApiOpenStudio\Core\Request;

/**
 * Class Json
 *
 * Outputs the results as a JSON string.
 */
class Json extends OutputResponse
{
    use ConvertToJsonTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Json',
        'machineName' => 'json',
        // phpcs:ignore
        'description' => 'Output the results of the resource in JSON format in the response. This does not need to be added to the resource - it will be automatically detected by the Accept header.',
        'menu' => 'Output',
        'input' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = 'Content-Type: application/json';

    /**
     * Config object.
     *
     * @var Config
     */
    protected Config $settings;

    /**
     * JSON output constructor.
     *
     * @param mixed|null $meta
     *   Output meta.
     * @param Request $request
     *   The full request object.
     * @param MonologWrapper $logger
     *   Logger.
     * @param mixed $data
     *   Output data.
     * @param integer $status
     *   HTTP output status.
     */
    public function __construct($meta, Request &$request, MonologWrapper $logger, $data, int $status)
    {
        parent::__construct($meta, $request, $logger, $data, $status);
        $this->settings = new Config();
    }

    /**
     * Cast the data to JSON.
     *
     * @throws ApiException
     *   Throw an exception if unable to convert the data.
     */
    protected function castData(): void
    {
        if ($this->data->getType() != 'json') {
            $method = 'from' . ucfirst(strtolower($this->data->getType())) . 'ToJson';
            $resultData = $this->$method($this->data->getData());
        } else {
            $resultData = $this->data->getData();
        }


        if ($this->settings->__get(['api', 'wrap_json_in_response_object'])) {
            // Wrap JSON in the wrapper object if required by the settings.
            if (in_array($this->data->getType(), ['json', 'array', 'xml', 'html']) && !is_bool($resultData)) {
                $decoded = json_decode($resultData, true);
                $resultData = is_null($decoded) ? $resultData : $decoded;
            }
            if (
                !is_array($resultData)
                || sizeof($resultData) != 2
                || !isset($resultData['result'])
                || !isset($resultData['data'])
            ) {
                $resultData = [
                    'result' => 'ok',
                    'data' => $resultData,
                ];
            }
            $resultData = json_encode($resultData);
        } elseif ($this->data->getType() == 'text') {
            // Wrap text values in double quotes so that they are parseable as valid JSON.
            $resultData = '"' . $resultData . '"';
        }

        try {
            $this->data->setData($resultData);
            $this->data->setType('json');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
