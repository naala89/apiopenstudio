<?php

/**
 * Class ConvertHtml.
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

use DOMDocument;
use DOMElement;
use stdClass;
use SoapBox\Formatter\Formatter;

/**
 * Class ConvertHtml
 *
 * Class to convert HTML to an array, object or JSON.
 *
 * The
 */
class ConvertHtml
{
    /**
     * @var string
     */
    protected string $attributePrefix;

    /**
     * @var string
     */
    protected string $htmlTextTag;

    /**
     * @param string $attributePrefix
     * @param string $htmlTextTag
     */
    public function __construct(string $attributePrefix = '_', string $htmlTextTag = '#text')
    {
        $this->attributePrefix = $attributePrefix;
        $this->htmlTextTag = $htmlTextTag;
    }

    /**
     * Convert a valid HTML document string into an array.
     *
     * This is an array format that maintains the element order and no data loss.
     *
     * @param string $html
     *
     * @return array
     */
    public function htmlToArray(string $html): array
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        return ['html' => $this->domElementToArray($dom->documentElement)];
    }

    /**
     * Convert a valid HTML document string into an object.
     *
     * @param string $html
     *
     * @return string
     */
    public function htmlToJson(string $html): string
    {
        return json_encode($this->htmlToArray($html));
    }

    /**
     * Convert a valid HTML document string into an xml string.
     *
     * @param string $html
     *
     * @return string
     */
    public function htmlToXml(string $html): string
    {
        $array = $this->htmlToArray($html);
        $formatter = Formatter::make($array['html'], Formatter::ARR);
        return $formatter->toXml('html');
    }

    /**
     * Convert the output array format from this class into a HTML string.
     *
     * @param array $definition
     *
     * @return string
     */
    public function arrayToHtml(array $definition): string
    {
        return $this->arrayToDomDocument($definition)->saveHTML();
    }

    /**
     * Convert a DOM element to an array of attributes and children.
     *
     * @param DOMElement $element
     *
     * @return array
     */
    protected function domElementToArray(DOMElement $element): array
    {
        $result = [];

        $attributes = $this->getAttributes($element);
        if (!empty($attributes)) {
            $result = array_merge($result, $attributes);
        }

        $children = $this->getChildren($element);
        if (!empty($children)) {
            $result = array_merge($result, $children);
        }

        $text = $this->getText($element);
        if ($text !== '') {
            $result[] = [$this->htmlTextTag => $text];
        }

        return $result;
    }

    /**
     * Return an array of attributes for a DOM element.
     *
     * @param DOMElement $element
     *
     * @return array
     */
    protected function getAttributes(DOMElement $element): array
    {
        $attributes = [];

        foreach ($element->attributes as $attribute) {
            $attributes[] = [($this->attributePrefix . $attribute->name) => $attribute->value];
        }

        return $attributes;
    }

    /**
     * Return an array of children for a DOM element.
     *
     * @param DOMElement $element
     *
     * @return array
     */
    protected function getChildren(DOMElement $element): array
    {
        $children = [];

        foreach ($element->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $children[] = [$node->tagName => $this->domElementToArray($node)];
            }
        }

        return $children;
    }

    /**
     * Return raw text for a DOM element.
     *
     * @param DOMElement $element
     *
     * @return string
     */
    protected function getText(DOMElement $element): string
    {
        $text = '';

        foreach ($element->childNodes as $node) {
            if ($node->nodeName == '#text' || $node->nodeType == XML_CDATA_SECTION_NODE) {
                $text = $node->textContent;
            }
        }

        return trim($text);
    }

    /**
     * Return a DOM Document defined in an array.
     *
     * @param array $definition
     *
     * @return DOMDocument
     */
    protected function arrayToDomDocument(array $definition): DOMDocument
    {
        $dom = new DOMDocument();
        foreach ($definition as $elemName => $children) {
            $node = $this->generateElement($dom, $elemName, $children);
            $dom->appendChild($node);
        }
        return $dom;
    }

    /**
     * Generate a DOM element with its value, attributes and all of its children.
     *
     * @param DOMDocument $dom
     * @param string $elementName
     * @param array $definition
     *
     * @return DOMElement
     */
    protected function generateElement(DOMDocument &$dom, string $elementName, array $definition): DOMElement
    {
        $node = $dom->createElement($elementName);
        foreach ($definition as $children) {
            foreach ($children as $childKey => $childValue) {
                if (strpos($childKey, $this->attributePrefix) === 0) {
                    $regex = "~^" . $this->attributePrefix . "(.*)~";
                    $node->setAttribute(preg_replace($regex, "$1", $childKey), $childValue);
                } elseif ($childKey == $this->htmlTextTag) {
                    $node->nodeValue = $childValue;
                } else {
                    $childNode = $this->generateElement($dom, $childKey, $childValue);
                    $node->appendChild($childNode);
                }
            }
        }

        return $node;
    }
}
