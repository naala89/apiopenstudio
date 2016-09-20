<?php

namespace Datagator\Output;
use Datagator\Core;
use Datagator\Processor;
use Datagator\Config;

abstract class Output extends Core\ProcessorEntity
{
  protected $data;
  protected $meta;
  protected $header = '';
  public $status;

  /**
   * @param $data
   * @param $status
   * @param null $meta
   */
  public function __construct($data, $status, $meta=null)
  {
    $this->status = $status;
    $this->data = $data;
    $this->meta = $meta;
  }

  /**
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  public function process()
  {
    Core\Debug::variable('Processor Output');
    $this->setStatus();
    $data = $this->getData();
    if (!empty($this->meta)) {
      if (empty($this->meta->destination)) {
        throw new Core\ApiException('no destinations defined for output', 1,$this->id);
      }
      $method = $this->val('method');
      foreach ($this->meta->destination as $destination) {
        $url = $this->val($destination);
        $data = array('data' => $data);
        $curlOpts = array();
        if ($method == 'post') {
          $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($data);
        } elseif ($method == 'get') {
          $url .= http_build_query($data, '?', '&');
        }

        $curl = new Core\Curl();
        $result = $curl->{$method}($url, $curlOpts);
        if ($result === false) {
          throw new Core\ApiException('could not get response from remote server: ' . $curl->errorMsg, 5, $this->id, $curl->httpStatus);
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

  public function setHeader()
  {
    if (Config::$debugInterface != 'HTML' || (Config::$debug < 1 && Config::$debugDb < 1)) {
      header($this->header);
    }
  }

  public function setStatus()
  {
    http_response_code($this->status);
  }

  protected function calcType()
  {
    if (is_array($this->data)) {
      $result = 'array';
    } else {
      $test = json_decode($this->data);
      if (json_last_error() == JSON_ERROR_NONE) {
        $result = 'json';
      } elseif(substr(trim($this->data), 0, 5) == "<?xml") {
        $result = 'xml';
      } elseif(substr(trim($this->data), 0, 5) == "<head") {
        $result = 'html';
      } else {
        $result = 'text';
      }
    }
    Core\Debug::variable($result);
    return $result;
  }

  /**
   * @return \SimpleXMLElement|string
   * @throws \Datagator\Core\ApiException
   */
  protected function getData() {
    Core\Debug::variable($this->data);
    if (!$this->isDataEntity($this->data)) {
      $type = $this->calcType();
      $data = $this->data;
    }
    else {
      $data = $this->data->getData();
      $type = $this->data->getType();
    }
    Core\Debug::variable($type);
    switch ($type) {
      case 'xml':
        return $this->fromXml($data);
        break;
      case 'html':
        return $this->fromHtml($data);
        break;
      case 'json':
        return $this->fromJson($data);
        break;
      case 'array':
        return $this->fromArray($data);
        break;
      case 'text':
      case 'plain':
        return $this->fromText($data);
        break;
      default:
        throw new Core\ApiException("unknown output type: '$type'. Cannot convert to XML");
        break;
    }
  }

  abstract protected function fromXml(& $data);

  abstract protected function fromJson(& $data);

  abstract protected function fromHtml(& $data);

  abstract protected function fromText(& $data);

  abstract protected function fromArray(& $data);
}
