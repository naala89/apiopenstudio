<?php

/**
 * Class Output.
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
use ApiOpenStudio\Config;
use Monolog\Logger;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Output
 *
 * Outputs base class.
 */
abstract class Output extends Core\ProcessorEntity
{
    /**
     * Config object.
     *
     * @var Core\Config
     */
    protected $settings;

    /**
     * The output data.
     *
     * @var mixed The output data.
     */
    protected $data;

    /**
     * The output metadata.
     *
     * @var mixed the output meta.
     */
    protected $meta;

    /**
     * Content-type header value.
     *
     * @var string The string to contain the content type header value.
     */
    protected $header = '';

    /**
     * The HTTP output status.
     *
     * @var mixed The output status.
     */
    public $status;

    /**
     * Output constructor.
     *
     * @param mixed $data Output data.
     * @param integer $status Output status.
     * @param \Monolog\Logger $logger Output status.
     * @param mixed|null $meta Output meta.
     */
    public function __construct($data, int $status, Logger $logger, $meta = null)
    {
        $this->settings = new Core\Config();
        $this->status = $status;
        $this->logger = $logger;
        $this->data = $data;
        $this->meta = $meta;
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Throw an exception if unable to precess the output.
     */
    public function process()
    {
        $this->setStatus();
        $data = $this->getData();

        if (!empty($this->meta)) {
            if (empty($this->meta->destination)) {
                $this->logger->alert('no destinations defined for output');
                throw new Core\ApiException('no destinations defined for output', 1, $this->id);
            }
            $method = $this->val('method');
            foreach ($this->meta->destination as $destination) {
                $url = $this->val($destination);
                $data = ['data' => $data];
                $curlOpts = [];
                if ($method == 'post') {
                    $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($data);
                } elseif ($method == 'get') {
                    $url .= http_build_query($data, '?', '&');
                }

                $curl = new Core\Curl();
                $result = $curl->{$method}($url, $curlOpts);
                if ($result === false) {
                    $message = 'could not get response from remote server: ' . $curl->errorMsg;
                    $this->logger->alert($message);
                    throw new Core\ApiException($message, 5, $this->id, $curl->httpStatus);
                }
                if ($curl->httpStatus != 200) {
                    $this->logger->alert('Failed to send data: ' . json_encode($result));
                    throw new Core\ApiException(json_encode($result), 5, $this->id, $curl->httpStatus);
                }
            }
        } else {
            $this->setHeader();
            return $data;
        }
    }

    /**
     * Set the Content-Type header.
     *
     * @return void
     */
    public function setHeader()
    {
        header($this->header);
    }

    /**
     * Set the response status code.
     *
     * @return void
     */
    public function setStatus()
    {
        http_response_code($this->status);
    }

    /**
     * Calculate the Content-Type.
     *
     * @return string The data type.
     */
    protected function calcType()
    {
        if (is_array($this->data)) {
            $result = 'array';
        } else {
            $test = json_decode($this->data);
            if (json_last_error() == JSON_ERROR_NONE) {
                $result = 'json';
            } elseif (substr(trim($this->data), 0, 5) == "<?xml") {
                $result = 'xml';
            } elseif (substr(trim($this->data), 0, 5) == "<head") {
                $result = 'html';
            } else {
                $result = 'text';
            }
        }
        return $result;
    }

    /**
     * Get the data.
     *
     * @return mixed
     *
     * @throws Core\ApiException Throw an exception if unable to convert the data.
     */
    protected function getData()
    {
        if (!$this->isDataContainer($this->data)) {
            $this->data = new Core\DataContainer($this->data);
        }
        $type = $this->data->getType();
        $this->dataContainer2value($this->data);

        switch ($type) {
            case 'boolean':
                return $this->fromBoolean($this->data);
                break;
            case 'integer':
                return $this->fromInteger($this->data);
                break;
            case 'float':
                return $this->fromFloat($this->data);
                break;
            case 'text':
                return $this->fromText($this->data);
                break;
            case 'array':
                return $this->fromArray($this->data);
                break;
            case 'json':
                return $this->fromJson($this->data);
                break;
            case 'xml':
                return $this->fromXml($this->data);
                break;
            case 'html':
                return $this->fromHtml($this->data);
                break;
            case 'image':
                return $this->fromImage($this->data);
                break;
            default:
                throw new Core\ApiException("unknown output type: '$type'. Cannot convert");
                break;
        }
    }

    /**
     * Convert incoming to a final value.
     *
     * This includes a DataContainer and array of DataContainer.
     *
     * @param mixed $data Incoming data.
     *
     * @return mixed outgoing data.
     */
    private function dataContainer2value(&$data)
    {
        if (is_array($data)) {
            foreach ($data as $key => & $value) {
                $value = $this->dataContainer2value($value);
            }
        } elseif ($this->isDataContainer($data)) {
            $type = $data->getType();
            $data = $data->getData();
            if ($type == 'array') {
                foreach ($data as & $item) {
                    $item = $this->dataContainer2value($item);
                }
            } elseif ($this->isDataContainer($data)) {
                $data = $this->dataContainer2value($data);
            }
        }
        return $data;
    }

    /**
     * Convert a data item to boolean.
     * This is specific to each output processor.
     *
     * @param boolean $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromBoolean(bool &$data);

    /**
     * Convert a data item to integer.
     * This is specific to each output processor.
     *
     * @param integer $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromInteger(int &$data);

    /**
     * Convert a data item to float.
     * This is specific to each output processor.
     *
     * @param float $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromFloat(float &$data);

    /**
     * Convert a data item to text.
     * This is specific to each output processor.
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromText(string &$data);

    /**
     * Convert a data item to array.
     * This is specific to each output processor.
     *
     * @param array $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromArray(array &$data);

    /**
     * Convert a data item to json.
     * This is specific to each output processor.
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromJson(string &$data);

    /**
     * Convert a data item to xml.
     * This is specific to each output processor.
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromXml(string &$data);

    /**
     * Convert a data item to html.
     * This is specific to each output processor.
     *
     * @param string $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromHtml(string &$data);

    /**
     * Convert a data item to image.
     * This is specific to each output processor.
     *
     * @param mixed $data The data to convert.
     *
     * @return mixed
     */
    abstract protected function fromImage(&$data);
}
