<?php

/**
 * Class Xml.
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
use ApiOpenStudio\Core\ConvertToXmlTrait;
use ApiOpenStudio\Core\DetectTypeTrait;

/**
 * Class Xml
 *
 * Outputs the results as XML.
 */
class Xml extends Output
{
    use ConvertToXmlTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = 'Content-Type:application/xml';

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Xml',
        'machineName' => 'xml',
        'description' => 'Output in the results of the resource in XML format to a remote server.',
        'menu' => 'Output',
        'input' => [
            'destination' => [
                'description' => 'Destination URLs for the output.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'method' => [
                'description' => 'HTTP delivery method when sending output. Only used in the output section.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'push', 'delete', 'put'],
                'default' => '',
            ],
            'options' => [
                // phpcs:ignore
                'description' => 'Extra Curl options to be applied when sent to the destination (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => ['field'],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * Cast the data to XML.
     *
     * @throws ApiException
     *   Throw an exception if unable to convert the data.
     */
    protected function castData(): void
    {
        $currentType = $this->data->getType();
        if ($currentType == 'xml') {
            return;
        }

        $method = 'from' . ucfirst(strtolower($currentType)) . 'ToXml';

        try {
            $this->data->setData($this->$method($this->data->getData()));
            $this->data->setType('xml');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), 6, 'output: XML', 400);
        }
    }
}
