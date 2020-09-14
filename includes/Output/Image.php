<?php
/**
 * Class Image.
 *
 * @package Gaterdata
 * @subpackage Output
 * @author john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Output;

use Gaterdata\Core;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Image
 *
 * Outputs the results as an image.
 */
class Image extends Output
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Image',
        'machineName' => 'output_image',
        'description' => 'Output in the results of the resource in image format to a remote server.',
        'menu' => 'Output',
        'input' => [
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
                'cardinality' => [0, '1'],
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
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     */
    public function process()
    {
        $this->logger->info('Output: ' . $this->details()['machineName']);
        return parent::process();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Image data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if invalid input.
     */
    protected function fromXml(string &$data)
    {
        throw new Core\ApiException('Cannot convert XML to image format');
    }

    /**
     * {@inheritDoc}
     *
     * @param float $data Image data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if invalid input.
     */
    protected function fromFloat(float &$data)
    {
        throw new Core\ApiException('Cannot convert a float to image format');
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $data Image data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if invalid input.
     */
    protected function fromBoolean(bool &$data)
    {
        throw new Core\ApiException('Cannot convert a boolean to image format');
    }

    /**
     * {@inheritDoc}
     *
     * @param integer $data Image data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if invalid input.
     */
    protected function fromInteger(int &$data)
    {
        throw new Core\ApiException('Cannot convert an integer to image format');
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Image data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if invalid input.
     */
    protected function fromJson(string &$data)
    {
        throw new Core\ApiException('Cannot convert JSON to image format');
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Image data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if invalid input.
     */
    protected function fromHtml(string &$data)
    {
        throw new Core\ApiException('Cannot convert HTML to image format');
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Image data.
     *
     * @return mixed
     */
    protected function fromText(string &$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data Image data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if invalid input.
     */
    protected function fromArray(array &$data)
    {
        throw new Core\ApiException('Cannot convert an array to image format');
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data Image data.
     *
     * @return mixed
     */
    protected function fromImage(&$data)
    {
        return $data;
    }
}
