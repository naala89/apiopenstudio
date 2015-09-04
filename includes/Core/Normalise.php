<?php

/**
 *
 */

namespace Datagator\Core;

class Normalise
{
  private $data;
  private $format;
  public $normaliseFunc = 'toArray';

  /**
   * @param $data
   * @param bool|FALSE $format
   */
  public function __construct($data, $format=false)
  {
    $this->data = $data;
    $this->format = $format;
  }

  public function normalise()
  {
    return $this->{$this->normaliseFunc}();
  }

  /**
   * @return array
   */
  public function toArray()
  {
    $format = !$this->format ? $this->_calcFormat() : $this->_getFormat();
    switch ($format) {
      case 'xml':
        $data = $this->_xmlToArray();
        break;
      case 'json':
        $data = $this->_jsonToArray();
        break;
      case 'text':
      default:
        $data = array('data' => $this->data);
        break;
    }
    return $data;
  }

  /**
   * @return \stdClass
   */
  public function toStdClass()
  {
    $format = !$this->format ? $this->_calcFormat() : $this->_getFormat();
    switch ($format) {
      case 'xml':
        $data = $this->_xmlToStdClass();
        break;
      case 'json':
        $data = $this->_jsonToStdClass();
        break;
      case 'text':
      default:
        $data = new \stdClass();
        $data->data = $this->data;
        break;
    }
    return $data;
  }

  /**
   * We can pass in the full header array or header[CURLINFO_CONTENT_TYPE].
   *
   * @return string
   */
  private function _getFormat()
  {
    $format = $this->format;
    if (is_array($format)) {
      if (!empty($format[CURLINFO_CONTENT_TYPE])) {
        return $this->_parseContentType($format[CURLINFO_CONTENT_TYPE]);
      }
      return $this->_calcFormat();
    }
    return $this->_parseContentType($format);
  }

  private function _parseContentType($str)
  {
    $result = '';
    if (preg_match('/text\/|application\//', $str) == 1) {
      return preg_replace('/text\/|application\//', '', $str);
    }
    if (preg_match('/multipart\//', $str) == 1) {
      throw new ApiException('invalid response from remote url, cannot disseminate content-type: multipart');
    }
    if (preg_match('/message\//', $str) == 1) {
      throw new ApiException('invalid response from remote url, cannot disseminate content-type: message');
    }
    if (preg_match('/image\//', $str) == 1) {
      return 'image';
    }
    if (preg_match('/audio\//', $str) == 1) {
      return 'audio';
    }
    if (preg_match('/video\//', $str) == 1) {
      return 'video';
    }
    return $result;
  }

  /**
   * @return string
   */
  private function _calcFormat()
  {
    $data = $this->data;
    // test for XML
    if (simplexml_load_string($data) !== false) {
      return 'xml';
    }
    // test for JSON
    json_decode($data);
    if (json_last_error() == JSON_ERROR_NONE) {
      return 'json';
    }
    return 'text';
  }

  /**
   * @return array
   */
  private function _xmlToArray()
  {
    $obj = simplexml_load_string($this->data); // Parse XML
    return json_decode(json_encode($obj), true); // Convert to array
  }


  /**
   * @return array
   */
  private function _jsonToArray()
  {
    return json_decode($this->data, true); // Convert to array
  }

  /**
   * @return \stdClass
   */
  private function _xmlToStdClass()
  {
    $obj = simplexml_load_string($this->data); // Parse XML
    return json_decode(json_encode($obj)); // Convert to stdclass
  }


  /**
   * @return \stdClass
   */
  private function _jsonToStdClass()
  {
    return json_decode($this->data); // Convert to stdclass
  }
}
