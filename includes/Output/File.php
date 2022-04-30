<?php

/**
 * Class File.
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
use ApiOpenStudio\Core\ConvertToFileTrait;
use ApiOpenStudio\Core\DetectTypeTrait;

/**
 * Class File
 *
 * Outputs the results as a file.
 */
class File extends Output
{
    use ConvertToFileTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = 'Content-Type: application/octet-stream';

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'File',
        'machineName' => 'file',
        'description' => 'Output a file.',
        'menu' => 'Output',
        'input' => [
            'filename' => [
                'description' => 'The output suggested filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'apiopenstudio.txt',
            ],
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
     * Set the Content-Type header.
     *
     * @return void
     *
     * @throws ApiException
     */
    public function setHeader()
    {
        parent::setHeader();
        $filename = $this->val('filename', true);
        header("Content-Disposition: attachment; filename='$filename'");
    }

    /**
     * Cast the data to text.
     *
     * @throws ApiException
     *   Throw an exception if unable to convert the data.
     */
    protected function castData(): void
    {
        $currentType = $this->data->getType();
        $method = 'from' . ucfirst(strtolower($currentType)) . 'ToFile';

        try {
            $this->data->setData($this->$method($this->data->getData()));
            $this->data->setType('file');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
    }
}
