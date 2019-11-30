<?php

/**
 *
 */

namespace Gaterdata\Core;

class Normalise
{
    private $data;
    private $format;
    public $defaultNormalise = 'array';

    /**
     * Set the data to be processed.
     * @param $data
     * @param null $format
     */
    public function set($data, $format = null)
    {
        $this->data = $data;
        $this->format = $format;
    }

    /**
     * Call the default normalise function
     * @param null $into
     * @return mixed
     * @throws \Gaterdata\Core\ApiException
     */
    public function normalise($into = null)
    {
        $into = empty($into) ? $this->defaultNormalise : $into;
        $normaliseFunc = 'to' . ucfirst($into);
        if (!method_exists($this, $normaliseFunc)) {
            throw new ApiException("cannot normalise input into $into, this functionality does not exist",
                6, $this->id, 417);
        }
        return $this->{$normaliseFunc}();
    }

    /**
     * Convert data to an array.
     * @return array
     * @throws ApiException
     */
    public function toArray()
    {
        $format = empty($this->format) ? $this->_calcFormat() : $this->_getFormat();
        switch ($format) {
            case 'xml':
            case 'application/xml':
            case 'text/xml':
                $data = $this->_xmlToArray($this->data);
            break;
            case 'json':
            case 'application/json':
            case 'text/json':
                $data = $this->_jsonToArray($this->data);
            break;
            case 'array':
                $data = $this->data;
            break;
            case 'text':
            case 'application/text':
            case 'text/text':
            default:
                $data = array('data' => $this->data);
            break;
        }
        return $data;
    }

    /**
     * Convert constructs and text to stdClass.
     * @return \stdClass
     * @throws ApiException
     */
    public function toStdClass()
    {
        $format = !$this->format ? $this->_calcFormat() : $this->_getFormat();
        switch ($format) {
            case 'xml':
                $data = $this->_xmlToStdClass();
            break;
            case 'json':
                $data = $this->_jsonToStdClass();
            break;
            case 'array':
                $data = new \stdClass();
                $data->data = (object) $this->data;
            break;
            case 'text':
            default:
                $data = new \stdClass();
                $data->data = $this->data;
            break;
        }
        return $data;
    }

    /**
     * We can pass in the full header array or header[CURLINFO_CONTENT_TYPE].
     * @return string|string[]|null
     * @throws ApiException
     */
    private function _getFormat()
    {
        $format = $this->format;
        if (is_array($format)) {
            if (!empty($format[CURLINFO_CONTENT_TYPE])) {
                return $this->_parseContentType($format[CURLINFO_CONTENT_TYPE]);
            }
            return $this->_calcFormat();
        }
        return $this->_parseContentType($format);
    }

    /**
     * Validate content types.
     * @param $str
     * @return string|string[]|null
     * @throws ApiException
     */
    private function _parseContentType($str)
    {
        $result = '';
        if (preg_match('/text\/|application\//', $str) == 1) {
            return preg_replace('/text\/|application\//', '', $str);
        }
        if (preg_match('/multipart\//', $str) == 1) {
            throw new ApiException('invalid response from remote url, cannot disseminate content-type: multipart', 3);
        }
        if (preg_match('/message\//', $str) == 1) {
            throw new ApiException('invalid response from remote url, cannot disseminate content-type: message', 3);
        }
        if (preg_match('/image\//', $str) == 1) {
            return 'image';
        }
        if (preg_match('/audio\//', $str) == 1) {
            return 'audio';
        }
        if (preg_match('/video\//', $str) == 1) {
            return 'video';
        }
        return $result;
    }

    /**
     * Calculate the format type of a string.
     * @return string
     */
    private function _calcFormat()
    {
        $data = $this->data;
        // test for array
        if (is_array($data)) {
            return 'array';
        }
        // test for JSON
        json_decode($data);
        if (json_last_error() == JSON_ERROR_NONE) {
            return 'json';
        }
        // test for XML
        if (simplexml_load_string($data) !== false) {
            return 'xml';
        }
        return 'text';
    }

    /**
     * Convert XML string into an array, maintaining attributes and cdata.
     * @see https://github.com/gaarf/XML-string-to-PHP-array
     * @return array
     */
    private function _xmlToArray()
    {
        $doc = new \DOMDocument();
        $doc->loadXML($this->data);
        $root = $doc->documentElement;
        $output = $this->domNodeToArray($root);
        $output['@root'] = $root->tagName;
        return $output;
    }

    /**
     * Convert an XML node into an array attribute.
     * @param $node
     * @return array
     */
    private function domNodeToArray($node)
    {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
            break;

            case XML_ELEMENT_NODE:
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domNodeToArray($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } elseif ($v || $v === '0') {
                        $output = (string) $v;
                    }
                }
                if ($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
                    $output = array('@content'=>$output); //Change output into an array.
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                              $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v)==1 && $t!='@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
            break;
        }
        return $output;
    }

    /**
     * @return array
     */
    private function _jsonToArray()
    {
        return json_decode($this->data, true); // Convert to array
    }

    /**
     * @return \stdClass
     */
    private function _xmlToStdClass()
    {
        $obj = simplexml_load_string($this->data); // Parse XML
        return json_decode(json_encode($obj)); // Convert to stdclass
    }


    /**
     * @return \stdClass
     */
    private function _jsonToStdClass()
    {
        return json_decode($this->data); // Convert to stdclass
    }
}
