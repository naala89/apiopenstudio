<?php

/**
 * Class XmlRemote.
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
use ApiOpenStudio\Core\OutputRemote;

/**
 * Class XmlRemote
 *
 * Outputs the results as XML to a remote location.
 */
class XmlRemote extends OutputRemote
{
    use ConvertToXmlTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Xml remote',
        'machineName' => 'xml_remote',
        'description' => 'Output in the results of the resource in XML format to a remote server.',
        'menu' => 'Output',
        'input' => [
            'filename' => [
                'description' => 'The output filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'apiopenstudio.xml',
            ],
            'transport' => [
                'description' => 'The Transport for uploading. example: ApiOpenStudio\Plugins\TransportS3.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'parameters' => [
                // phpcs:ignore
                'description' => 'Name/Value pairs for parameters required by the transport, e.g. username, password, etc.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
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
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
