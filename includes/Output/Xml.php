<?php
/**
 * Class Xml.
 *
 * @package    ApiOpenStudio
 * @subpackage Output
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Output;

use ApiOpenStudio\Core;

/**
 * Class Xml
 *
 * Outputs the results as XML.
 */
class Xml extends Output
{
    /**
     * {@inheritDoc}
     *
     * @var string The string to contain the content type header value.
     */
    protected string $header = 'Content-Type:application/xml';

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Xml',
        'machineName' => 'xml',
        'description' => 'Output in the results of the resource in XML format to a remote server.',
        'menu' => 'Output',
        'input' => [
            'destination' => [
                'description' => 'Destination URLs for the output.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'method' => [
                'description' => 'HTTP delivery method when sending output. Only used in the output section.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'post', 'push', 'delete', 'put'],
                'default' => '',
            ],
            'options' => [
                // phpcs:ignore
                'description' => 'Extra Curl options to be applied when sent to the destination (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => ['field'],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     */
    public function process(): Core\DataContainer
    {
        $this->logger->info('Output: ' . $this->details()['machineName']);
        return new Core\DataContainer(parent::process(), 'xml');
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $data Boolean data.
     *
     * @return string XML string.
     */
    protected function fromBoolean(bool &$data): string
    {
        return '<?xml version="1.0"?><apiOpenStudioWrapper>' . $data ? 'true' : 'false' . '</apiOpenStudioWrapper>';
    }

    /**
     * {@inheritDoc}
     *
     * @param integer $data Integer data.
     *
     * @return string XML string.
     */
    protected function fromInteger(int &$data): string
    {
        return '<?xml version="1.0"?><apiOpenStudioWrapper>' . $data . '</apiOpenStudioWrapper>';
    }

    /**
     * {@inheritDoc}
     *
     * @param float $data Float data.
     *
     * @return string XML string.
     */
    protected function fromFloat(float &$data): string
    {
        return '<?xml version="1.0"?><apiOpenStudioWrapper>' . $data . '</apiOpenStudioWrapper>';
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data XML data.
     *
     * @return string XML string.
     */
    protected function fromXml(string &$data): string
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($data);
        if (!$doc) {
            libxml_clear_errors();
            return '<?xml version="1.0"?><apiOpenStudioWrapper>' . $data . '</apiOpenStudioWrapper>';
        } else {
            return $data;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data HTML data.
     *
     * @return string XML string.
     */
    protected function fromHtml(string &$data): string
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Text data.
     *
     * @return string XML string.
     */
    protected function fromText(string &$data)
    {
        return '<?xml version="1.0"?><apiOpenStudioWrapper>' . $data . '</apiOpenStudioWrapper>';
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data Array data.
     *
     * @return string XML string.
     */
    protected function fromArray(array &$data)
    {
        $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><apiOpenStudioWrapper></apiOpenStudioWrapper>');
        $this->array2xml($data, $xml_data);
        return $xml_data->asXML();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data Json data.
     *
     * @return string XML string.
     */
    protected function fromJson(string &$data)
    {
        $data = json_decode($data, true);
        return $this->fromArray($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data Image data.
     *
     * @return string XML string.
     */
    protected function fromImage(&$data)
    {
        return $this->fromText($data);
    }

    /**
     * Recursive method to convert an array into XML format.
     *
     * @param array $array Input array.
     * @param \SimpleXMLElement $xml A SimpleXMLElement element.
     *
     * @return \SimpleXMLElement A populated SimpleXMLElement.
     */
    private function array2xml(array $array, \SimpleXMLElement $xml)
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
