<?php

/**
 * @see http://stackoverflow.com/a/5965940/1113356
 * @see http://pastebin.com/pYuXQWee
 */

namespace Datagator\Outputs;

class Xml extends Output
{
  public function process()
  {
    parent::process();
    header('Content-Type:text/html');
    $data = $this->data;

    if (is_object($data)) {
      $data = get_object_vars($data);
    }
    if (is_array($data)) {
      $xml = new SimpleXMLElement('<?xml version="1.0"?><wrapper></wrapper>');
      $this->_arrayToXml($data, $xml);
      $xml = $xml->asXML();
    } else {
      $xml = "<?xml version=\"1.0\"?><wrapper>$data</wrapper>";
    }

    return $xml;
  }

  protected function _arrayToXml($array, &$xml)
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
