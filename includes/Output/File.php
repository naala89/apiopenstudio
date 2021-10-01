<?php

/**
 * Class File.
 *
 * @package    ApiOpenStudio
 * @subpackage Output
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Output;

use ApiOpenStudio\Core\ApiException;

/**
 * Class File
 *
 * Outputs the results as a file.
 */
class File extends Output
{
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
        $this->logger->info('api', 'Output: ' . $this->details()['machineName']);
        parent::setHeader();
        $filename = $this->val('filename', true);
        header("Content-Disposition: attachment; filename='$filename'");
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $data Boolean data.
     *
     * @return string Boolean as a string.
     */
    protected function fromBoolean(bool &$data): string
    {
        return $data ? 'true' : 'false';
    }

    /**
     * {@inheritDoc}
     *
     * @param integer $data Integer data.
     *
     * @return string Integer as a string.
     */
    protected function fromInteger(int &$data): string
    {
        return (string) $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param float $data Float data.
     *
     * @return string Float as a string.
     */
    protected function fromFloat(float &$data): string
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data XML data.
     *
     * @return string XML string.
     */
    protected function fromXml(string &$data): string
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data HTML data.
     *
     * @return string HTML string.
     */
    protected function fromHtml(string &$data): string
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Text data.
     *
     * @return string Text string.
     */
    protected function fromText(string &$data): string
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data Array data.
     *
     * @return string JSON encoded array string.
     */
    protected function fromArray(array &$data): string
    {
        return json_encode($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Json data.
     *
     * @return string JSON string.
     */
    protected function fromJson(string &$data): string
    {
        return is_string($data) ? $data : json_encode($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data Image data.
     *
     * @return string Image string.
     */
    protected function fromImage(&$data): string
    {
        return $data;
    }
}
