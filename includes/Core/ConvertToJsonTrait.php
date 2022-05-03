<?php

/**
 * Trait ConvertToJsonTrait.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use SimpleXMLElement;

/**
 * Trait ConvertToJsonTrait.
 *
 * Class to cast an input value to JSON.
 */
trait ConvertToJsonTrait
{
    /**
     * Convert empty to JSON.
     *
     * @param $data
     *
     * @return null
     */
    public function fromEmptyToJson($data)
    {
        return null;
    }

    /**
     * Convert boolean to JSON string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromBooleanToJson($data): string
    {
        return json_encode($data);
    }

    /**
     * Convert integer to JSON string.
     *
     * @param $data
     *
     * @return string|null
     */
    public function fromIntegerToJson($data): ?string
    {
        if (is_infinite($data)) {
            $data = $data < 0 ? '-Infinity' : 'Infinity';
        } elseif (is_nan($data)) {
            $data = 'NaN';
        }
        return json_encode($data);
    }

    /**
     * Convert float to JSON string.
     *
     * @param $data
     *
     * @return string|null
     */
    public function fromFloatToJson($data): ?string
    {
        if (is_infinite($data)) {
            $data = $data < 0 ? '-Infinity' : 'Infinity';
        } elseif (is_nan($data)) {
            $data = 'NaN';
        }
        return json_encode($data);
    }

    /**
     * Convert text to JSON string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromTextToJson($data): string
    {
        if ($data == '') {
            // Empty string should be returned as double quotes so that it is not returned as null.
            return '""';
        }
        // Wrap in double quotes if not already present.
        if (substr($data, 0, 1) != '"') {
            $data = '"' . $data;
        }
        if (substr($data, -1, 1) != '"') {
            $data = $data . '"';
        }
        return $data;
    }

    /**
     * Convert array to JSON string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromArrayToJson($data): string
    {
        return json_encode($data, true);
    }

    /**
     * Convert JSON string to JSON string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromJsonToJson($data): string
    {
        return $data;
    }

    /**
     * Convert XML string to JSON string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromXmlToJson($data): string
    {
        $xml = simplexml_load_string($data);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        return  json_encode($array);
    }

    /**
     * Convert an HTML string to JSON string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromHtmlToJson($data): string
    {
        return $this->fromXmlToJson($data);
    }

    /**
     * Convert an image string to JSON string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromImageToJson($data): string
    {
        return $this->fromTextToJson($data);
    }

    /**
     * Convert file to JSON string.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromFileToJson($data): string
    {
        throw new ApiException('Cannot cast file to JSON', 6, -1, 400);
    }

    /**
     * Convert an XML doc to JSON string.
     *
     * @param SimpleXMLElement $xml XML element.
     *
     * @return array
     */
    public function xml2json(SimpleXMLElement &$xml): array
    {
        $root = (!(func_num_args() > 1));
        $jsnode = [];

        if (!$root) {
            if (count($xml->attributes()) > 0) {
                $jsnode["$"] = [];
                foreach ($xml->attributes() as $key => $value) {
                    $jsnode["$"][$key] = (string)$value;
                }
            }

            $textcontent = trim((string)$xml);
            if (!empty($textcontent)) {
                $jsnode["_"] = $textcontent;
            }

            foreach ($xml->children() as $childxmlnode) {
                $childname = $childxmlnode->getName();
                if (!array_key_exists($childname, $jsnode)) {
                    $jsnode[$childname] = [];
                }
                array_push($jsnode[$childname], $this->xml2json($childxmlnode));
            }
            return $jsnode;
        } else {
            $nodename = $xml->getName();
            $jsnode[$nodename] = [];
            array_push($jsnode[$nodename], $this->xml2json($xml));
            $result = json_encode($jsnode);
            return !is_array($result) ? [$result] : $result;
        }
    }
}
