<?php

namespace Datagator\Outputs;

abstract class Output
{
  public $status;
  protected $data;
  protected $meta;

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

  public function process()
  {
    $this->setStatus();
    $this->setError();
  }

  protected function setStatus()
  {
    http_response_code($this->status);
  }

  protected function setError()
  {
    if ($this->isError()) {
      $this->data = $this->data->process();
    }
  }

  /**
   * @return bool
   */
  protected function isError()
  {
    return (is_object($this->data) && get_class($this->data) == 'Error');
  }

  /**
   * @param $string
   * @return bool
   */
  protected function isJson($string)
  {
    if (!is_string($string)) {
      return FALSE;
    }
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }

  /**
   * @param null $data
   * @return array|null|string
   */
  protected function toJson($data=null)
  {
    $data = empty($data) ? $this->data : $data;
    if (is_object($data)) {
      $data = (array) $data;
    }
    if (!$this->isJson($data)) {
      $data = json_encode($data);
    }
    return $data;
  }

  /**
   * @param null $data
   * @return \Datagator\Outputs\SimpleXMLElement|string
   */
  protected function toXml($data = null) {
    $data = empty($data) ? $this->data : $data;
    if (is_object($data)) {
      $data = get_object_vars($data);
    }
    if (is_array($data)) {
      $xml = new SimpleXMLElement('<?xml version="1.0"?><wrapper></wrapper>');
      $this->_arrayToXml($data, $xml);
      $xml = $xml->asXML();
    }
    else {
      $xml = "<?xml version=\"1.0\"?><wrapper>$data</wrapper>";
    }
    return $xml;
  }

  /**
   * @param $array
   * @param $xml
   */
  private function _arrayToXml($array, &$xml)
  {
    foreach($array as $key => $value) {
      if(is_array($value) || is_object($value)) {
        $key = is_numeric($key) ? "item$key" : $key;
        $subnode = $xml->addChild("$key");
        $this->_arrayToXml($value, $subnode);
      } else {
        $key = is_numeric($key) ? "item$key" : $key;
        $xml->addChild("$key","$value");
      }
    }
  }
}
