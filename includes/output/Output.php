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
      return $data;
    }
  }

  public function setHeader()
  {
    if (Config::$debugInterface == 'LOG' || (Config::$debug < 1 && Config::$debugDb < 1)) {
      header($this->header);
    }
  }

  public function setStatus()
  {
    http_response_code($this->status);
  }

  /**
   * This function returns the payload that will be sent on the response.
   * It must convert the data into the expected format (e.g. XML or JSON strings).
   *
   * @return mixed
   */
  abstract protected function getData();
}
