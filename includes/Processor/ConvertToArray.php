<?php

/**
 * Convert any data container data type to an array data container.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Output\Output;

class ConvertToArray extends Output
{
    /**
     * @var mixed The output data.
     */
    protected $data;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * {@inheritDoc}
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
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * ConvertToArray constructor.
     *
     * @param array $meta
     *   The processor metadata.
     * @param Request $request
     *   Request object.
     * @param ADODB_mysqli $db
     *   Database object.
     * @param \Monolog\Logger $logger
     *   Logger object.
     */
    public function __construct($meta, &$request, $db, $logger)
    {
        Core\ProcessorEntity::__construct($meta, $request, $db, $logger);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);
        $this->data = $this->val('source');
        return new Core\DataContainer($this->getData(), 'array');
    }

    /**
     * {@inheritDoc}
     */
    protected function fromBoolean(&$data)
    {
        return [$data ? true : false];
    }

    /**
     * {@inheritDoc}
     */
    protected function fromInteger(&$data)
    {
        return [$data];
    }

    /**
     * {@inheritDoc}
     */
    protected function fromFloat(&$data)
    {
        return [$data];
    }

    /**
     * {@inheritDoc}
     */
    protected function fromXml(&$data)
    {
        $xml = simplexml_load_string($data);
        $json = $this->_xml2json($xml);
        return json_decode($json, true);
    }

    /**
     * {@inheritDoc}
     */
    protected function fromHtml(&$data)
    {
        return $this->fromXml($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function fromText(&$data)
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
     */
    protected function fromArray(&$data)
    {
        return $data;
    }

    protected function fromImage(&$data)
    {
        return $this->fromText($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function fromJson(&$data)
    {
        return json_decode($data, true);
    }

    /**
     * Convert an XML doc to json string.
     *
     * @param SimpleXMLElement $xml
     * @return array|false|string
     */
    private function _xml2json(SimpleXMLElement &$xml)
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
                array_push($jsnode[$childname], $this->_xml2json($childxmlnode, true));
            }
            return $jsnode;
        } else {
            $nodename = $xml->getName();
            $jsnode[$nodename] = [];
            array_push($jsnode[$nodename], $this->_xml2json($xml, true));
            return json_encode($jsnode);
        }
    }
}
