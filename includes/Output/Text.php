<?php
/**
 * Class Text.
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

use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Text
 *
 * Outputs the results as a text.
 */
class Text extends Output
{
    /**
     * {@inheritDoc}
     *
     * @var string The string to contain the content type header value.
     */
    protected $header = 'Content-Type:text/text';

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Text',
        'machineName' => 'text',
        'description' => 'Output in the results of the resource in text format to a remote server.',
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
                'limitValues' => ['get', 'post', 'push', 'delete', 'put'],
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
     * @param boolean $data Boolean data.
     *
     * @return mixed
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
     * @return mixed
     */
    protected function fromInteger(int &$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param float $data Float data.
     *
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return string
     */
    protected function fromArray(array &$data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Json data.
     *
     * @return mixed
     */
    protected function fromJson(string &$data)
    {
        return $data;
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
