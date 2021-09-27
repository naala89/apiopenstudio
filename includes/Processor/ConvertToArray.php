<?php
/**
 * Class ConvertToArray.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core;
use ApiOpenStudio\Output\Output;
use Monolog\Logger;
use SimpleXMLElement;

/**
 * Class ConvertToArray
 *
 * Processor class to convert data to array.
 */
class ConvertToArray extends Output
{
    /**
     * {@inheritDoc}
     *
     * @var mixed The output data.
     */
    protected $data;

    /**
     * Logging class.
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Convert to array',
        'machineName' => 'convert_to_array',
        'description' => 'Convert an input data into an array data type (i.e. JSON, XML or object) into an array.',
        'menu' => 'Data operation',
        'input' => [
            'source' => [
                'description' => 'The source data.',
                'cardinality' => [1, 1],
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

        $this->data = $this->val('source');

        return new Core\DataContainer($this->getData(), 'array');
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $data The data to convert.
     *
     * @return bool[]
     */
    protected function fromBoolean(bool &$data): array
    {
        return [(bool) $data];
    }

    /**
     * {@inheritDoc}
     *
     * @param integer $data The data to convert.
     *
     * @return int[]
     */
    protected function fromInteger(int &$data): array
    {
        return [$data];
    }

    /**
     * {@inheritDoc}
     *
     * @param float $data The data to convert.
     *
     * @return float[]
     */
    protected function fromFloat(float &$data): array
    {
        return [$data];
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return array
     */
    protected function fromXml(string &$data): array
    {
        $xml = simplexml_load_string($data);
        $json = $this->xml2json($xml);
        $result = json_decode($json, true);

        return !is_array($result) ? [$result] : $result;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return array
     */
    protected function fromHtml(string &$data): array
    {
        return $this->fromXml($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return array|string[]
     */
    protected function fromText(string &$data): array
    {
        if (empty($data)) {
            return [];
        }
        // Wrap in double quotes if not already present.
        if (substr($data, 0, 1) != '"' && substr($data, 0, 6) != '&quot;') {
            $data = '"' . $data;
        }
        if (substr($data, -1, 1) != '"' && substr($data, -6, 6) != '&quot;') {
            $data = $data . '"';
        }
        return [$data];
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data The data to convert.
     *
     * @return array
     */
    protected function fromArray(array &$data): array
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data The data to convert.
     *
     * @return string[]
     */
    protected function fromImage(&$data): array
    {
        return $this->fromText($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data The data to convert.
     *
     * @return array
     */
    protected function fromJson(string &$data): array
    {
        $result = json_decode($data, true);

        return !is_array($result) ? [$result] : $result;
    }

    /**
     * Convert an XML doc to json string.
     *
     * @param SimpleXMLElement $xml XML element.
     *
     * @return array
     */
    private function xml2json(SimpleXMLElement &$xml): array
    {
        $root = (func_num_args() > 1 ? false : true);
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
