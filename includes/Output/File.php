<?php
/**
 * Class File.
 *
 * @package    Gaterdata
 * @subpackage Output
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Output;

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
    protected $header = 'Content-Type: application/octet-stream';

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'File',
        'machineName' => 'file',
        'description' => 'Output a file.',
        'menu' => 'Output',
        'input' => [
            'filename' => [
                'description' => 'The output suggested filename.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => 'gaterdata.txt',
            ],
            'destination' => [
                'description' => 'Destination URLs for the output.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'method' => [
                'description' => 'HTTP delivery method when sending output. Only used in the output section.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post'],
                'default' => '',
            ],
            'options' => [
                // phpcs:ignore
                'description' => 'Extra Curl options to be applied when sent to the destination (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => ['field'],
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
     */
    public function setHeader()
    {
        $this->logger->info('Output: ' . $this->details()['machineName']);
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
    protected function fromBoolean(bool &$data)
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
    protected function fromInteger(int &$data)
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
    protected function fromFloat(float &$data)
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
    protected function fromXml(string &$data)
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
    protected function fromHtml(string &$data)
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
    protected function fromText(string &$data)
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
    protected function fromArray(array &$data)
    {
        return \json_encode($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Json data.
     *
     * @return string JSON string.
     */
    protected function fromJson(string &$data)
    {
        return is_string($data) ? $data : \json_encode($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data Image data.
     *
     * @return string Image string.
     */
    protected function fromImage(&$data)
    {
        return $data;
    }
}
