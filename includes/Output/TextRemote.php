<?php

/**
 * Class Text.
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
use ApiOpenStudio\Core\ConvertToTextTrait;
use ApiOpenStudio\Core\DetectTypeTrait;
use ApiOpenStudio\Core\OutputRemote;

/**
 * Class Text
 *
 * Outputs the results as text to a remote location.
 */
class TextRemote extends OutputRemote
{
    use ConvertToTextTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Text remote',
        'machineName' => 'text_remote',
        'description' => 'Output in the results of the resource in text format to a remote server.',
        'menu' => 'Output',
        'input' => [
            'filename' => [
                'description' => 'The output filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'apiopenstudio.txt',
            ],
            'method' => [
                'description' => 'The method for uploading.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [
                    'azure_blob',
                    'ftp',
                    'google_cloud',
                    's3',
                    'sftp',
                ],
                'default' => 'sftp',
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
     * Cast the data to text.
     *
     * @throws ApiException
     *   Throw an exception if unable to convert the data.
     */
    protected function castData(): void
    {
        $currentType = $this->data->getType();
        $method = 'from' . ucfirst(strtolower($currentType)) . 'ToText';

        try {
            $this->data->setData($this->$method($this->data->getData()));
            $this->data->setType('text');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
