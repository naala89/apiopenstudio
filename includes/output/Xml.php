<?php

/**
 * @see http://stackoverflow.com/a/5965940/1113356
 * @see http://pastebin.com/pYuXQWee
 */

namespace Datagator\Output;

use Datagator\Core\Debug;

class Xml extends Output
{
  protected $header = 'Content-Type:application/xml';
  protected $details = array(
    'name' => 'Xml',
    'description' => 'Output in XML format.',
    'menu' => 'Output',
    'application' => 'Common',
    'input' => array(
      'destination' => array(
        'description' => 'A single or array of URLs to send the results to.',
        'cardinality' => array(1, '*'),
        'accepts' => array('function', 'literal'),
      ),
      'method' => array(
        'description' => 'HTTP delivery method when sending output. Only used in the output section.',
        'cardinality' => array(0, '1'),
        'accepts' => array('function', '"get"', '"post"'),
      ),
      'options' => array(
        'description' => 'Extra Curl options to be applied when sent to the destination  (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor field'),
      ),
    ),
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
      $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><wrapper></wrapper>');
      $this->_arrayToXml($data, $xml_data);
      $xml = $xml_data->asXML();
    }
    else {
      $xml = "<?xml version=\"1.0\"?><wrapper>$data</wrapper>";
    }
    return $xml;
  }

  /**
   * @param $data
   * @param $xml_data
   * @see http://stackoverflow.com/questions/1397036/how-to-convert-array-to-simplexml
   */
  private function _arrayToXml($data, &$xml_data ) {
    foreach($data as $key => $value) {

      if(is_array($value)) {
        if (substr($key, 0, 1) == '@') {
          if ($key != 'header' && $key != 'item' && $key == '@attributes') {
            foreach ($value as $k => $v) {
              $xml_data->addAttribute($k, $v);
            }
          }
        }
        if(is_numeric($key)) {
          $key = 'item'; //dealing with <0/>..<n/> issues
        }
        $subnode = $xml_data->addChild($key);
        $this->_arrayToXml($value, $subnode);
      } else {
        if (substr($key, 0, 1) == '@') {
          if ($key != 'header' && $key != 'item') {
            $xml_data->addAttribute(substr($key, 1), $value);
          }
        }
        $xml_data->addChild("$key",htmlspecialchars("$value"));
      }
    }
  }
}
