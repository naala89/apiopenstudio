<?php

/**
 * Class ConvertToArray.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Output\Output;
use Monolog\Logger;

/**
 * Class ConvertToArray
 *
 * Processor class to convert data to array.
 */
class ConvertToArray extends Output
{
    /**
     * {@inheritDoc}
     *
     * @var mixed The output data.
     */
    protected $data;

    /**
     * Logging class.
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Convert to array',
        'machineName' => 'convert_to_array',
        'description' => 'Convert an input data into an array data type (i.e. JSON, XML or object) into an array.',
        'menu' => 'Data operation',
        'input' => [
            'source' => [
                'description' => 'The source data.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ConvertToArray constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        Core\ProcessorEntity::__construct($meta, $request, $db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);
        $this->data = $this->val('source');
        return new Core\DataContainer($this->getData(), 'array');
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $data The data to convert.
     *
     * @return mixed
     */
    protected function fromBoolean(bool &$data)
    {
        return [$data ? true : false];
    }

    /**
     * {@inheritDoc}
     *
     * @param integer $data The data to convert.
     *
     * @return mixed
     */
    protected function fromInteger(int &$data)
    {
        return [$data];
    }

    /**
     * {@inheritDoc}
     *
     * @param float $data The data to convert.
     *
     * @return mixed
     */
    protected function fromFloat(float &$data)
    {
        return [$data];
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    protected function fromXml(string &$data)
    {
        $xml = simplexml_load_string($data);
        $json = $this->xml2json($xml);
        return json_decode($json, true);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    protected function fromHtml(string &$data)
    {
        return $this->fromXml($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    protected function fromText(string &$data)
    {
        if (empty($data)) {
            return [];
        }
        // Wrap in double quotes if not already present.
        if (substr($data, 0, 1) != '"' && substr($data, 0, 6) != '&quot;') {
            $data = '"' . $data;
        }
        if (substr($data, -1, 1) != '"' && substr($data, -6, 6) != '&quot;') {
            $data = $data . '"';
        }
        return [$data];
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data The data to convert.
     *
     * @return mixed
     */
    protected function fromArray(array &$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data The data to convert.
     *
     * @return mixed
     */
    protected function fromImage(&$data)
    {
        return $this->fromText($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    protected function fromJson(string &$data)
    {
        return json_decode($data, true);
    }

    /**
     * Convert an XML doc to json string.
     *
     * @param \SimpleXMLElement $xml XML element.
     *
     * @return array|false|string
     */
    private function xml2json(SimpleXMLElement &$xml)
    {
        $root = (func_num_args() > 1 ? false : true);
        $jsnode = [];

        if (!$root) {
            if (count($xml->attributes()) > 0) {
                $jsnode["$"] = [];
                foreach ($xml->attributes() as $key => $value) {
                    $jsnode["$"][$key] = (string)$value;
                }
            }

            $textcontent = trim((string)$xml);
            if (count($textcontent) > 0) {
                $jsnode["_"] = $textcontent;
            }

            foreach ($xml->children() as $childxmlnode) {
                $childname = $childxmlnode->getName();
                if (!array_key_exists($childname, $jsnode)) {
                    $jsnode[$childname] = [];
                }
                array_push($jsnode[$childname], $this->xml2json($childxmlnode, true));
            }
            return $jsnode;
        } else {
            $nodename = $xml->getName();
            $jsnode[$nodename] = [];
            array_push($jsnode[$nodename], $this->xml2json($xml, true));
            return json_encode($jsnode);
        }
    }
}
