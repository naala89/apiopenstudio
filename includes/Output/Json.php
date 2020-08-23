<?php

namespace Gaterdata\Output;

use Gaterdata\Core;

class Json extends Output
{
    /**
     * {@inheritDoc}
     */
    protected $header = 'Content-Type: application/json';

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Json',
        'machineName' => 'output_json',
        'description' => 'Output in the results of the resource in JSON format to a remote server.',
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
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Output: ' . $this->details()['machineName']);
        return parent::process();
    }

    /**
     * {@inheritDoc}
     */
    protected function fromBoolean(&$data)
    {
        return $data ? 'true' : 'false';
    }

    /**
     * {@inheritDoc}
     */
    protected function fromInteger(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromFloat(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromXml(&$data)
    {
        $xml = simplexml_load_string($data);
        return $this->_xml2json($xml);
    }

    /**
     * {@inheritDoc}
     */
    protected function fromHtml(&$data)
    {
        return $this->_xml2json($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function fromText(&$data)
    {
        if ($data == '') {
            // Empty string should be returned as double quotes so that it is not returned as null.
            return '""';
        }
        // Wrap in double quotes if not already present.
        if (substr($data, 0, 1) != '"' && substr($data, 0, 6) != '&quot;') {
            $data = '"' . $data;
        }
        if (substr($data, -1, 1) != '"' && substr($data, -6, 6) != '&quot;') {
            $data = $data . '"';
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromArray(&$data)
    {
        return \json_encode($data);
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
        return is_string($data) ? $data : \json_encode($data);
    }

    private function _xml2json(&$xml)
    {
        $root = (func_num_args() > 1 ? false : true);
        $jsnode = array();

        if (!$root) {
            if (count($xml->attributes()) > 0) {
                $jsnode["$"] = array();
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
                    $jsnode[$childname] = array();
                }
                array_push($jsnode[$childname], $this->_xml2json($childxmlnode, true));
            }
            return $jsnode;
        } else {
            $nodename = $xml->getName();
            $jsnode[$nodename] = array();
            array_push($jsnode[$nodename], $this->_xml2json($xml, true));
            return json_encode($jsnode);
        }
    }
}
