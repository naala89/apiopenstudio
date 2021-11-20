<?php

/**
 * Trait ConvertToArrayTrait.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
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
 * Trait ConvertToArrayTrait.
 *
 * Trait to cast an input value to array.
 */
trait ConvertToArrayTrait
{
    /**
     * Convert empty to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromEmptyToArray($data): array
    {
        return [];
    }

    /**
     * Convert boolean to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromBooleanToArray($data): array
    {
        return [$data];
    }

    /**
     * Convert integer to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromIntegerToArray($data): array
    {
        return [$data];
    }

    /**
     * Convert float to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromFloatToArray($data): array
    {
        return [$data];
    }

    /**
     * Convert text to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromTextToArray($data): array
    {
        return [$data];
    }

    /**
     * Convert array to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromArrayToArray($data): array
    {
        return $data;
    }

    /**
     * Convert JSON to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromJsonToArray($data): array
    {
        return json_decode($data, true);
    }

    /**
     * Convert XML to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromXmlToArray($data): array
    {
        $json = json_encode($this->xml2json($data));
        return json_decode($json, true);
    }

    /**
     * Convert HTML to array.
     *
     * @param $data
     *
     * @return array
     */
    public function fromHtmlToArray($data): array
    {
        $json = json_encode($this->xml2json($data));
        return json_decode($json, true);
    }

    /**
     * Convert image to array.
     *
     * @param $data
     *
     * @return array
     *
     * @throws ApiException
     */
    public function fromImageToArray($data): array
    {
        throw new ApiException('Cannot cast image to array');
    }

    /**
     * Convert file to array.
     *
     * @param $data
     *
     * @return array
     *
     * @throws ApiException
     */
    public function fromFileToArray($data): array
    {
        throw new ApiException('Cannot cast file to array');
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
        $root = !(func_num_args() > 1);
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
            $result = json_encode($jsnode);
            return !is_array($result) ? [$result] : $result;
        }
    }
}
