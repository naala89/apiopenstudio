<?php

namespace Datagator\Output;

class Text extends Output
{
  protected $header = 'Content-Type:text/text';
  public $details = array(
    'name' => 'Text',
    'description' => 'Output in text format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(
      'destination' => array(
        'description' => 'List of URLs to send to (other than response).',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  /**
   * @return array|null|string
   */
  protected function getData()
  {
    $payload = $this->toText();
    if (!empty($this->meta)) {
      $options = !empty($this->meta->options) ? $this->meta->options : array();
      foreach ($this->meta->destination as $destination) {
        $curl = new Curl();
        $curl->post($destination, $options + array(
            'CURLOPT_POSTFIELDS' => $payload
          ));
      }
    }
    return $payload;
  }

  /**
   * @param null $data
   * @return array|null|string
   */
  protected function toText($data=null)
  {
    $data = empty($data) ? $this->data : $data;
    if (is_object($data)) {
      $data = (array) $data;
    }
    if (is_array($data)) {
      $data = serialize($data);
    }
    return $data;
  }
}