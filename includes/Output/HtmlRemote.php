<?php

/**
 * Class HtmlRemote.
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
use ApiOpenStudio\Core\ConvertToHtmlTrait;
use ApiOpenStudio\Core\DetectTypeTrait;
use ApiOpenStudio\Core\OutputRemote;

/**
 * Class HtmlRemote
 *
 * Outputs the results as an HTML string to a remote location.
 */
class HtmlRemote extends OutputRemote
{
    use ConvertToHtmlTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Html remote',
        'machineName' => 'html_remote',
        'description' => 'Output in the results of the resource in HTML format to a remote server.',
        'menu' => 'Output',
        'input' => [
            'filename' => [
                'description' => 'The output filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'apiopenstudio.html',
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
                'description' => 'Name/Value pairs for parameters required by the uploader, e.g. username, password, etc.',
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
     * Cast the data to HTML.
     *
     * @throws ApiException
     *   Throw an exception if unable to convert the data.
     */
    protected function castData(): void
    {
        $currentType = $this->data->getType();
        $method = 'from' . ucfirst(strtolower($currentType)) . 'ToHtml';

        try {
            $html = $this->$method($this->data->getData());
            $this->data->setData($html);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
