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
use ApiOpenStudio\Core\OutputResponse;

/**
 * Class File
 *
 * Outputs the results as a file in the response.
 */
class File extends OutputResponse
{
    use ConvertToFileTrait;
    use DetectTypeTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'File',
        'machineName' => 'file',
        // phpcs:ignore
        'description' => 'Output the results of the resource in file format in the response. This does not need to be added to the resource - it will be automatically detected by the Accept header.',
        'menu' => 'Output',
        'input' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = 'Content-Type: application/octet-stream';

    /**
     * Set the response headers.
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
