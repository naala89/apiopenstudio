<?php

/**
 * Trait ConvertToXmlTrait.
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
use SoapBox\Formatter\Formatter;

/**
 * Trait ConvertToXmlTrait.
 *
 * Class to cast an input value to XML.
 */
trait ConvertToXmlTrait
{
    /**
     * Convert array to XML string.
     *
     * @param array $array
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromArrayToXml(array $array): string
    {
        $xml = $this->getBaseXmlWrapper();
        return $this->array2xml($array, $xml);
    }

    /**
     * Convert boolean to XML string.
     *
     * @param ?bool $boolean
     *
     * @return string
     */
    public function fromBooleanToXml(?bool $boolean): string
    {
        if (is_null($boolean)) {
            $boolean = null;
        } else {
            $boolean = $boolean ? 'true' : 'false';
        }
        $formatter = Formatter::make(['item' => $boolean], Formatter::ARR);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert empty to XML.
     *
     * @param $data
     *
     * @return string
     */
    public function fromEmptyToXml($data): string
    {
        $formatter = Formatter::make('{"item":""}', Formatter::JSON);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert file to XML string.
     *
     * @param $file
     *
     * @return string
     */
    public function fromFileToXml($file): string
    {
        $formatter = Formatter::make($file, Formatter::JSON);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert float to XML string.
     *
     * @param float|null $float
     *
     * @return string
     */
    public function fromFloatToXml(?float $float): string
    {
        if (is_infinite($float)) {
            $float = $float < 0 ? '-INF' : 'INF';
        } elseif (is_nan($float)) {
            $float = 'NAN';
        }
        $formatter = Formatter::make(['item' => $float], Formatter::ARR);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert an HTML string to XML string.
     *
     * @param string $html
     *
     * @return string
     */
    public function fromHtmlToXml(string $html): string
    {
        $convertHtml = new ConvertHtml();
        return $convertHtml->htmlToXml($html);
    }

    /**
     * Convert an image string to XML string.
     *
     * @param $image
     *
     * @return string
     */
    public function fromImageToXml($image): string
    {
        $formatter = Formatter::make($image, Formatter::JSON);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert integer to XML string.
     *
     * @param int|float|null $integer
     *
     * @return string
     */
    public function fromIntegerToXml($integer): string
    {
        if (is_infinite($integer)) {
            $integer = $integer < 0 ? '-INF' : 'INF';
        } elseif (is_nan($integer)) {
            $integer = 'NAN';
        }
        $formatter = Formatter::make(['item' => $integer], Formatter::ARR);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert JSON string to XML string.
     *
     * @param string $json
     *
     * @return string
     */
    public function fromJsonToXml(string $json): string
    {
        $testObject = json_decode($json, true);
        if (!is_array($testObject)) {
            $json = json_encode(['item' => $testObject]);
        }
        $formatter = Formatter::make($json, Formatter::JSON);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert text to XML string.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromTextToXml(string $text): string
    {
        $testObject = json_decode($text, true);
        if (!is_array($testObject) || $text == '[]') {
            $text = json_encode(['item' => $text]);
        }
        $formatter = Formatter::make($text, Formatter::JSON);
        return $formatter->toXml('apiOpenStudioWrapper');
    }

    /**
     * Convert XML string to XML string.
     *
     * @param string $xml
     *
     * @return string
     */
    public function fromXmlToXml(string $xml): string
    {
        return $xml;
    }

    /**
     * Recursive method to convert an array into XML format.
     *
     * @param array $array Input array.
     * @param SimpleXMLElement $xml A SimpleXMLElement element.
     *
     * @return string A populated SimpleXMLElement.
     */
    protected function array2xml(array $array, SimpleXMLElement $xml): string
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $key = "item";
            }
            if (is_array($value)) {
                $this->array2xml($value, $xml->addChild($key));
            } else {
                $xml->addchild($key, htmlentities($value));
            }
        }
        return $xml->asXML();
    }

    /**
     * Get the base SimpleXMLElement wrapper for XML for converting non XML inputs to XML output.
     *
     * @param string $baseTag
     * @return SimpleXMLElement
     *
     * @throws ApiException
     */
    protected function getBaseXmlWrapper(string $baseTag = 'apiOpenStudioWrapper'): SimpleXMLElement
    {
        try {
            $xml = new SimpleXMLElement("<$baseTag/>");
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), 0, -1, 500);
        }
        return $xml;
    }
}
