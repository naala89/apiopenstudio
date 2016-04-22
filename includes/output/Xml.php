<?php

/**
 * @see http://stackoverflow.com/a/5965940/1113356
 * @see http://pastebin.com/pYuXQWee
 */

namespace Datagator\Output;

class Xml extends Output
{
  protected $header = 'Content-Type:text/html';
  protected $details = array(
    'name' => 'Xml',
    'description' => 'Output in XML format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(),
  );

  /**
   * @return \Datagator\Output\SimpleXMLElement|string
   */
  protected function getData()
  {
    return $this->toXml();
  }

  /**
   * @param null $data
   * @return \Datagator\Output\SimpleXMLElement|string
   */
  protected function toXml($data = null) {
    $data = empty($data) ? $this->data : $data;
    if (is_object($data)) {
      $data = get_object_vars($data);
    }
    if (is_array($data)) {
      $xml = new \SimpleXMLElement('<?xml version="1.0"?><wrapper></wrapper>');
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
