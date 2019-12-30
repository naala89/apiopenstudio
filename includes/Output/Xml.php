<?php

/**
 * @see http://stackoverflow.com/a/5965940/1113356
 * @see http://pastebin.com/pYuXQWee
 */

namespace Gaterdata\Output;

use Gaterdata\Core;

class Xml extends Output
{
    /**
     * {@inheritDoc}
     */
    protected $header = 'Content-Type:application/xml';

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Xml',
        'machineName' => 'xml',
        'description' => 'Output in the results of the resource in XML format to a remote server.',
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
    protected function fromBoolean(&$data)
    {
        return '<?xml version="1.0"?><datagatorWrapper>' . $data ? 'true' : 'false' . '</datagatorWrapper>';
    }

    /**
     * {@inheritDoc}
     */
    protected function fromInteger(&$data)
    {
        return '<?xml version="1.0"?><datagatorWrapper>' . $data . '</datagatorWrapper>';
    }

    /**
     * {@inheritDoc}
     */
    protected function fromFloat(&$data)
    {
        return '<?xml version="1.0"?><datagatorWrapper>' . $data . '</datagatorWrapper>';
    }

    /**
     * {@inheritDoc}
     */
    protected function fromXml(&$data)
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($data);
        if (!$doc) {
            libxml_clear_errors();
            return '<?xml version="1.0"?><datagatorWrapper>' . $data . '</datagatorWrapper>';
        } else {
            return $data;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function fromHtml(&$data)
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function fromText(&$data)
    {
        return '<?xml version="1.0"?><datagatorWrapper>' . $data . '</datagatorWrapper>';
    }

    /**
     * {@inheritDoc}
     */
    protected function fromArray(&$data)
    {
        $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><datagatorWrapper></datagatorWrapper>');
        $this->_array2xml($data, $xml_data);
        return $xml_data->asXML();
    }

    /**
     * {@inheritDoc}
     */
    protected function fromJson(&$data)
    {
        $data = json_decode($data, true);
        return $this->fromArray($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function fromImage(&$data)
    {
        return this.$this->fromText($data);
    }

    /**
     * Recursive method to convert an array into XML format.
     * @param $array
     * @param $xml
     * @return mixed
     */
    private function _array2xml($array, $xml)
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $key = "item$key";
            }
            if (is_array($value)) {
                $this->_array2xml($value, $xml->addChild($key));
            } else {
                $xml->addchild($key, htmlentities($value));
            }
        }
        return $xml->asXML();
    }
}
