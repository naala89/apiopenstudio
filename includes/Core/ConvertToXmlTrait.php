<?php

/**
 * Trait ConvertToXmlTrait.
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
 * Trait ConvertToXmlTrait.
 *
 * Class to cast an input value to XML.
 */
trait ConvertToXmlTrait
{
    /**
     * Convert empty to XML.
     *
     * @param $data
     *
     * @return null
     */
    public function fromEmptyToXml($data)
    {
        return null;
    }

    /**
     * Convert boolean to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromBooleanToXml($data): string
    {
        return $this->wrapDataXmlFormat($data ? 'true' : 'false');
    }

    /**
     * Convert integer to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromIntegerToXml($data): string
    {
        return $this->wrapDataXmlFormat($data);
    }

    /**
     * Convert float to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromFloatToXml($data): string
    {
        return $this->wrapDataXmlFormat($data);
    }

    /**
     * Convert text to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromTextToXml($data): string
    {
        return $this->wrapDataXmlFormat($data);
    }

    /**
     * Convert array to XML string.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromArrayToXml($data): string
    {
        try {
            $xml_data = new SimpleXMLElement($this->wrapDataXmlFormat(''));
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
        $this->array2xml($data, $xml_data);
        return $xml_data->asXML();
    }

    /**
     * Convert JSON string to XML string.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromJsonToXml($data): string
    {
        $data = json_decode($data, true);
        return $this->fromArrayToXml($data);
    }

    /**
     * Convert XML string to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromXmlToXml($data): string
    {
        return $data;
    }

    /**
     * Convert an HTML string to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromHtmlToXml($data): string
    {
        return $data;
    }

    /**
     * Convert an image string to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromImageToXml($data): string
    {
        return $this->wrapDataXmlFormat($data);
    }

    /**
     * Convert file to XML string.
     *
     * @param $data
     *
     * @return string
     */
    public function fromFileToXml($data): string
    {
        return $this->wrapDataXmlFormat($data);
    }

    /**
     * Wrap Data in MML string wrapper.
     *
     * @param $data
     *
     * @return string
     */
    protected function wrapDataXmlFormat($data): string
    {
        return '<?xml version="1.0"?><apiOpenStudioWrapper>' . $data . '</apiOpenStudioWrapper>';
    }

    /**
     * Recursive method to convert an array into XML format.
     *
     * @param array $array Input array.
     * @param SimpleXMLElement $xml A SimpleXMLElement element.
     *
     * @return string A populated SimpleXMLElement.
     */
    public function array2xml(array $array, SimpleXMLElement $xml): string
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $key = "item$key";
            }
            if (is_array($value)) {
                $this->array2xml($value, $xml->addChild($key));
            } else {
                $xml->addchild($key, htmlentities($value));
            }
        }
        return $xml->asXML();
    }
}
