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
use ApiOpenStudio\Core\OutputResponse;

/**
 * Class Xml
 *
 * Outputs the results as XML.
 */
class Xml extends OutputResponse
{
    use ConvertToXmlTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Xml',
        'machineName' => 'xml',
        // phpcs:ignore
        'description' => 'Output the results of the resource in XML format in the response. This does not need to be added to the resource - it will be automatically detected by the Accept header.',
        'menu' => 'Output',
        'input' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = 'Content-Type:application/xml';

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
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
