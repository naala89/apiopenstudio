<?php

namespace Datagator\Outputs;
use Datagator;

class Json extends Output
{
  public function process()
  {
    parent::process();
    if (Datagator\Config::$debugInterface == 'LOG' || (Datagator\Config::$debug < 1 && Datagator\Config::$debugDb < 1)) {
      header('Content-Type: application/json');
    }

    $payload = $this->toJson();

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
