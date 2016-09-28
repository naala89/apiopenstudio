<?php

/**
 * @see http://stackoverflow.com/a/5965940/1113356
 * @see http://pastebin.com/pYuXQWee
 */

namespace Datagator\Output;
use Datagator\Core;

class Xml extends Output {
  protected $header = 'Content-Type:application/xml';
  protected $details = array(
    'name' => 'Xml',
    'machineName' => 'xml',
    'description' => 'Output in XML format.',
    'menu' => 'Output',
    'application' => 'Common',
    'input' => array(
      'destination' => array(
        'description' => 'Destination URLs for the output.',
        'cardinality' => array(0, '*'),
        'literalAllowed' => TRUE,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'method' => array(
        'description' => 'HTTP delivery method when sending output. Only used in the output section.',
        'cardinality' => array(0, '1'),
        'literalAllowed' => TRUE,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('get', 'post'),
        'default' => ''
      ),
      'options' => array(
        'description' => 'Extra Curl options to be applied when sent to the destination  (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'literalAllowed' => TRUE,
        'limitFunctions' => array('field'),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  /**
   * @param $data
   * @return string
   */
  protected function fromBoolean(& $data)
  {
    return '<?xml version="1.0"?><datagatorWrapper>' . $data ? 'true' : 'false' . '</datagatorWrapper>';
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromInteger(& $data)
  {
    return '<?xml version="1.0"?><datagatorWrapper>' . $data . '</datagatorWrapper>';
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromFloat(& $data)
  {
    return '<?xml version="1.0"?><datagatorWrapper>' . $data . '</datagatorWrapper>';
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromXml(& $data) {
    libxml_use_internal_errors(TRUE);
    $doc = simplexml_load_string($data);
    if (!$doc) {
      libxml_clear_errors();
      return "<?xml version=\"1.0\"?><datagatorWrapper>$data</datagatorWrapper>";
    }
    else {
      return $data;
    }
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromHtml(& $data) {
    return $data;
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromText(& $data) {
    return $data;
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromArray(& $data) {
    $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><datagatorWrapper></datagatorWrapper>');
    $this->_array2xml($data, $xml_data);
    return $xml_data->asXML();
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromJson(& $data) {
    $data = json_decode($data, TRUE);
    return $this->fromArray($data);
  }

  /**
   * @param $array
   * @param $xml
   * @return mixed
   */
  private function _array2xml($array, $xml)
  {
    foreach($array as $key => $value) {
      if (is_numeric($key)) {
        $key = "item$key";
      }
      if(is_array($value)) {
        $this->_array2xml($value, $xml->addChild($key));
      } else {
        $xml->addchild($key, htmlentities($value));
      }
    }
    return $xml->asXML();
  }
}
