<?php

/**
 * Class XmlPath.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use DOMDocument;
use DOMXPath;
use SimpleXMLElement;

/**
 * Class XmlPath
 *
 * Processor class to fetch/set values in XML or HTML string.
 */
class XmlPath extends Core\ProcessorEntity
{
    use Core\ConvertToXmlTrait;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'XML Path',
        'machineName' => 'xml_path',
        'description' => <<<DESCRIPTION
Get, update, add or remove values in an XML or HTML string.

Examples:

    Source XML:
        <?xml version="1.0" encoding="ISO-8859-1" ?>
        <bookstore>
	        <book category="COOKING">
                <title lang="en">Everyday Italian</title>
                <author>Giada De Laurentiis</author>
                <year>2005</year>
                <price>30.00</price>
            </book>
            <book category="CHILDREN">
                <title lang="en">Harry Potter</title>
                <author>J K. Rowling</author>
                <year>2005</year>
                <price>29.99</price>
            </book>
            <book category="WEB">
                <title lang="en">XQuery Kick Start</title>
                <author>James McGovern</author>
                <author>Per Bothner</author>
                <author>Kurt Cagle</author>
                <author>James Linn</author>
                <author>Vaidyanathan Nagarajan</author>
                <year>2003</year>
                <price>49.99</price>
            </book>
            <book category="WEB">
                <title lang="en">Learning XML</title>
                <author>Erik T. Ray</author>
                <year>2003</year>
                <price>39.95</price>
            </book>
        </bookstore>

    Get operation:
    
        processor: xml_path
        id: get_example
        data: "<xml><string /></xml>"
        operation: get
        expression: "//bookstore/book[1]/price"
        
        Result:
            ["<price>30.00</price>"]
    
    Set operation:
    
        processor: xml_path
        id: set_example
        data: "<xml><string></string></xml>"
        operation: set
        expression: "//bookstore/book[1]/price"
        value: 100000000.95
        
        Result:
            <?xml version="1.0" encoding="ISO-8859-1" ?>
            <bookstore>
                <book category="COOKING">
                    <title lang="en">Everyday Italian</title>
                    <author>Giada De Laurentiis</author>
                    <year>2005</year>
                    <price>100000000.95</price>
                </book>
                <book category="CHILDREN">
                    <title lang="en">Harry Potter</title>
                    <author>J K. Rowling</author>
                    <year>2005</year>
                    <price>29.99</price>
                </book>
                <book category="WEB">
                    <title lang="en">XQuery Kick Start</title>
                    <author>James McGovern</author>
                    <author>Per Bothner</author>
                    <author>Kurt Cagle</author>
                    <author>James Linn</author>
                    <author>Vaidyanathan Nagarajan</author>
                    <year>2003</year>
                    <price>49.99</price>
                </book>
                <book category="WEB">
                    <title lang="en">Learning XML</title>
                    <author>Erik T. Ray</author>
                    <year>2003</year>
                    <price>39.95</price>
                </book>
            </bookstore>
    
    Add operation:
    
        processor: json_path
        id: add_example
        data: "<xml><string></string></xml>"
        operation: add
        expression: "$.store.book"
        value: "<book category="DRAMA">
                  <title lang="en">Killing Heidi</title>
                  <author>Jane Austen</author>
                  <year>2035</year>
                  <price>0.95</price>
                </book>"
        
        Result:
            <?xml version="1.0" encoding="ISO-8859-1" ?>
            <bookstore>
                <book category="COOKING">
                    <title lang="en">Everyday Italian</title>
                    <author>Giada De Laurentiis</author>
                    <year>2005</year>
                    <price>100000000.95</price>
                </book>
                <book category="CHILDREN">
                    <title lang="en">Harry Potter</title>
                    <author>J K. Rowling</author>
                    <year>2005</year>
                    <price>29.99</price>
                </book>
                <book category="WEB">
                    <title lang="en">XQuery Kick Start</title>
                    <author>James McGovern</author>
                    <author>Per Bothner</author>
                    <author>Kurt Cagle</author>
                    <author>James Linn</author>
                    <author>Vaidyanathan Nagarajan</author>
                    <year>2003</year>
                    <price>49.99</price>
                </book>
                <book category="WEB">
                    <title lang="en">Learning XML</title>
                    <author>Erik T. Ray</author>
                    <year>2003</year>
                    <price>39.95</price>
                </book>
                <book category="DRAMA">
                    <title lang="en">Killing Heidi</title>
                    <author>Jane Austen</author>
                    <year>2035</year>
                    <price>0.95</price>
                </book>
            </bookstore>
    
    Remove operation:
    
        processor: json_path
        id: remove_example
        data: "<xml><string></string></xml>"
        operation: remove
        expression: "$.store.book[0]"
        
        Result:
        <?xml version="1.0" encoding="ISO-8859-1" ?>
        <bookstore>
            <book category="CHILDREN">
                <title lang="en">Harry Potter</title>
                <author>J K. Rowling</author>
                <year>2005</year>
                <price>29.99</price>
            </book>
            <book category="WEB">
                <title lang="en">XQuery Kick Start</title>
                <author>James McGovern</author>
                <author>Per Bothner</author>
                <author>Kurt Cagle</author>
                <author>James Linn</author>
                <author>Vaidyanathan Nagarajan</author>
                <year>2003</year>
                <price>49.99</price>
            </book>
            <book category="WEB">
                <title lang="en">Learning XML</title>
                <author>Erik T. Ray</author>
                <year>2003</year>
                <price>39.95</price>
            </book>
        </bookstore>
DESCRIPTION,
        'menu' => 'Data operation',
        'input' => [
            'data' => [
                'description' => 'The input XML string.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['xml', 'html'],
                'limitValues' => [],
                'default' => '',
            ],
            'expression' => [
                'description' => 'The JSON path expression.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '$.*',
            ],
            'operation' => [
                'description' => 'The operation ("get", "set", "add" or "remove").',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'set', 'add', 'remove'],
                'default' => 'get',
            ],
            'value' => [
                // phpcs:ignore
                'description' => 'The XML node string to insert or value to update on a node (used for "set" or "add" operations).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $data = $this->val('data', true);
        $expression = $this->val('expression', true);
        $operation = $this->val('operation', true);
        $value = $this->val('value', true);

        switch ($operation) {
            case 'get':
                $result = $this->getXpath($data, $expression);
                break;
            case 'set':
                $result = $this->setXpath($data, $expression, $value);
                break;
            case 'add':
                $result = $this->addXpath($data, $expression, $value);
                break;
            case 'remove':
                $result = $this->removeXpath($data, $expression);
                break;
            default:
                throw new Core\ApiException("invalid operation: $operation", 6, $this->id, 400);
        }

        return new Core\DataContainer($result, 'xml');
    }

    /**
     * Fetch an array of items found in an XML string with an expression.
     *
     * @param string $data
     *   XML string.
     * @param string $expression
     *   XPath expression.
     *
     * @return string
     *
     * @throws Core\ApiException
     */
    protected function getXpath(string $data, string $expression): string
    {
        $xml = simplexml_load_string($data);
        $nodes = $xml->xpath($expression);

        $result = $this->getBaseXmlWrapper();

        if (sizeof($nodes) > 0) {
            foreach ($nodes as $node) {
                $xmlWithoutWhiteSpace = trim(preg_replace('~\s+~u', ' ', $node->asXML()), ' ');
                if (!$this->simplexmlImportXml($result, $xmlWithoutWhiteSpace)) {
                    throw new Core\ApiException('failed to add node to parent XML, something went wrong');
                }
            }
        }

        if (($result = $result->asXML()) === false) {
            throw new Core\ApiException('failed to transform result document after transformations', 6, $this->id, 400);
        }

        return $result;
    }

    /**
     * Set a value in an XML string.
     *
     * @param string $data
     *   XML string.
     * @param string $expression
     *   XPath expression for nodes to set.
     * @param mixed $value
     *   Value to set on the nodes found by XPath.
     *
     * @return string
     *
     * @throws Core\ApiException
     */
    protected function setXpath(string $data, string $expression, $value): string
    {
        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        if ($document->loadXml($data) === false) {
            throw new Core\ApiException('invalid XML, unable to parse', 6, $this->id, 400);
        }
        $xpath = new DOMXPath($document);
        $nodeList = $xpath->query($expression);

        foreach ($nodeList as $node) {
            $node->nodeValue = $value;
        }

        if (($result = $document->saveXML()) === false) {
            throw new Core\ApiException('failed to transform result document after transformations', 6, $this->id, 400);
        }

        return $result;
    }

    /**
     * Add a value to an XML string.
     *
     * @param string $data
     *   XML string.
     * @param string $expression
     *   XPath expression for nodes to add to.
     * @param string $value
     *   Value to add to the nodes found by XPath.
     *
     * @return string
     *
     * @throws Core\ApiException
     */
    protected function addXpath(string $data, string $expression, string $value): string
    {
        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        if ($document->loadXml($data) === false) {
            throw new Core\ApiException('invalid XML, unable to parse', 6, $this->id, 400);
        }
        $xpath = new DOMXpath($document);

        $childNode = $document->createDocumentFragment();
        $valueWithoutWhiteSpace = trim(preg_replace('~\s+~u', ' ', $value), ' ');
        $childNode->appendXML($valueWithoutWhiteSpace);

        foreach ($xpath->evaluate($expression) as $node) {
            $node->appendChild($childNode);
        }

        if (($result = $document->saveXML()) === false) {
            throw new Core\ApiException('failed to transform result document after transformations', 6, $this->id, 400);
        }

        return $result;
    }

    /**
     * Add a value to an XML string.
     *
     * @param string $data
     *   XML string.
     * @param string $expression
     *   XPath expression for nodes to add to.
     *
     * @return string
     *
     * @throws Core\ApiException
     */
    protected function removeXpath(string $data, string $expression): string
    {
        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        if ($document->loadXml($data) === false) {
            throw new Core\ApiException('invalid XML, unable to parse', 6, $this->id, 400);
        }
        $xpath = new DOMXPath($document);

        foreach ($xpath->evaluate($expression) as $node) {
            $node->parentNode->removeChild($node);
        }

        if (($result = $document->saveXML()) === false) {
            throw new Core\ApiException('failed to transform result document after transformations', 6, $this->id, 400);
        }

        return $result;
    }

    /**
     * Append a SimpleXMLElement structure to an existing SimpleXMLElement.
     *
     * @param SimpleXMLElement $parent
     * @param string|SimpleXMLElement $xml
     * @param bool $before
     *
     * @return bool
     *
     * @see https://gist.github.com/hakre/4761677 for other useful methods.
     */
    protected function simplexmlImportXml(SimpleXMLElement $parent, $xml, bool $before = false): bool
    {
        $xml = (string) $xml;

        $node = dom_import_simplexml($parent);
        $fragment = $node->ownerDocument->createDocumentFragment();
        $fragment->appendXML($xml);

        if ($before) {
            return (bool) $node->parentNode->insertBefore($fragment, $node);
        }

        return (bool) $node->appendChild($fragment);
    }
}
