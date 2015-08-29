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

    if (!empty($this->destination)) {
      foreach ($this->destination as $destination) {
        $curl = new Curl();
        $curl->post($destination, array(
          'CURLOPT_POSTFIELDS' => $payload
        ));
        $this->sendToUrl($destination, $payload, $this->status);
      }
    }

    return $payload;
  }
}
