<?php

namespace Datagator\Output;
use Datagator\Core;

class Json extends Output
{
  protected $header = 'Content-Type: application/json';
  protected $details = array(
    'name' => 'Json',
    'machineName' => 'json',
    'description' => 'Output in JSON format.',
    'menu' => 'Output',
    'application' => 'Common',
    'input' => array(
      'destination' => array(
        'description' => 'Destination URLs for the output.',
        'cardinality' => array(1, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'method' => array(
        'description' => 'HTTP delivery method when sending output. Only used in the output section.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('get', 'post'),
        'default' => ''
      ),
      'options' => array(
        'description' => 'Extra Curl options to be applied when sent to the destination  (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'literalAllowed' => true,
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
    return $data ? 'true' : 'false';
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromInteger(& $data)
  {
    return $data;
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromFloat(& $data)
  {
    return $data;
  }

  /**
   * @param $data
   * @return string
   */
  protected function fromXml(& $data) {
    Core\Debug::variable($data);
    $xml = simplexml_load_string($data);
    return $this->_xml2json($xml);
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromHtml(& $data) {
    return $this->_xml2json($data);
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromText(& $data) {
    if ($data == '') {
      // Empty string should be returned as double quotes so that it is not returned as null.
      return '""';
    }
    // Wrap in double quotes if not already present.
    if (substr($data, 0, 1) != '"' && substr($data, 0, 6) != '&quot;') {
      $data = '"' . $data;
    }
    if (substr($data, -1, 1) != '"' && substr($data, -6, 6) != '&quot;') {
      $data = $data . '"';
    }
    return $data;
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromArray(& $data) {
    return \GuzzleHttp\json_encode($data);
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function fromJson(& $data) {
    return $data;
  }

  private function _xml2json(& $xml) {
    $root = (func_num_args() > 1 ? false : true);
    $jsnode = array();

    if (!$root) {
      if (count($xml->attributes()) > 0){
        $jsnode["$"] = array();
        foreach($xml->attributes() as $key => $value)
          $jsnode["$"][$key] = (string)$value;
      }

      $textcontent = trim((string)$xml);
      if (count($textcontent) > 0)
        $jsnode["_"] = $textcontent;

      foreach ($xml->children() as $childxmlnode) {
        $childname = $childxmlnode->getName();
        if (!array_key_exists($childname, $jsnode))
          $jsnode[$childname] = array();
        array_push($jsnode[$childname], $this->_xml2json($childxmlnode, true));
      }
      return $jsnode;
    } else {
      $nodename = $xml->getName();
      $jsnode[$nodename] = array();
      array_push($jsnode[$nodename], $this->_xml2json($xml, true));
      return json_encode($jsnode);
    }
  }
}
