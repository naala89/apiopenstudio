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

    $payload = $this->dataToXml();

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
}
