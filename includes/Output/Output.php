<?php

namespace Gaterdata\Output;

use Gaterdata\Core;
use Gaterdata\Config;

abstract class Output extends Core\ProcessorEntity
{
    /**
     * @var Core\Config
     */
    protected $settings;

    /**
     * @var mixed The output data.
     */
    protected $data;

    /**
     * @var mixed the output meta.
     */
    protected $meta;

    /**
     * @var string The string to contain the content type header value.
     */
    protected $header = '';

    /**
     * @var mixed The output status.
     */
    public $status;

    /**
     * Output constructor.
     * @param $data
     *     Output data.
     * @param $status
     *     Output status.
     * @param null $meta
     *     Output meta.
     */
    public function __construct($data, $status, $meta = null)
    {
        $this->settings = new Core\Config();
        $this->status = $status;
        $this->data = $data;
        $this->meta = $meta;
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->setStatus();
        $data = $this->getData();
        if (!empty($this->meta)) {
            if (empty($this->meta->destination)) {
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
                    throw new Core\ApiException($message, 5, $this->id, $curl->httpStatus);
                }
                if ($curl->httpStatus != 200) {
                    throw new Core\ApiException(json_encode($result), 5, $this->id, $curl->httpStatus);
                }
            }
        } else {
            $this->setHeader();
            return $data;
        }
    }

    /**
     * Set the Content-Type header .
     */
    public function setHeader()
    {
        if (
            $this->settings->__get(['debug', 'debugInterface']) != 'HTML'
            || (
                $this->settings->__get(['debug', 'debug']) < 1
                && $this->settings->__get(['debug', 'debugDb']) < 1
            )
        ) {
            header($this->header);
        }
    }

    /**
     * Set the response status code.
     */
    public function setStatus()
    {
        http_response_code($this->status);
    }

    /**
     * Calculate the Content-Type.
     * @return string
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
     * @return mixed
     * @throws Core\ApiException
     */
    protected function getData()
    {

        if (!$this->isDataContainer($this->data)) {
            $type = $this->calcType();
        } else {
            $type = $this->data->getType();
        }
        $this->_dataContainer2value($this->data);

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
                throw new Core\ApiException("unknown output type: '$type'. Cannot convert.");
                break;
        }
    }

    /**
     * Convert a data container to a value.
     * @param $data
     * @return mixed
     */
    private function _dataContainer2value(&$data)
    {
        if (is_array($data)) {
            foreach ($data as $key => & $value) {
                $value = $this->_dataContainer2value($value);
            }
        } elseif ($this->isDataContainer($data)) {
            $type = $data->getType();
            $data = $data->getData();
            if ($type == 'array') {
                foreach ($data as & $item) {
                    $item = $this->_dataContainer2value($item);
                }
            } elseif ($this->isDataContainer($data)) {
                $data = $this->_dataContainer2value($data);
            }
        }
        return $data;
    }

    /**
     * Convert a data item to boolean.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromBoolean(&$data);

    /**
     * Convert a data item to integer.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromInteger(&$data);

    /**
     * Convert a data item to float.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromFloat(&$data);

    /**
     * Convert a data item to text.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromText(&$data);

    /**
     * Convert a data item to array.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromArray(&$data);

    /**
     * Convert a data item to json.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromJson(&$data);

    /**
     * Convert a data item to xml.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromXml(&$data);

    /**
     * Convert a data item to html.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromHtml(&$data);

    /**
     * Convert a data item to image.
     * This is specific to each output processor.
     * @param $data
     * @return mixed
     */
    abstract protected function fromImage(&$data);
}
