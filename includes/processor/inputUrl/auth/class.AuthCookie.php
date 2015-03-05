<?php

/**
 * Provide cookie authentication
 *
 * This class is to be used by ProcessorInput.
 *
 * Meta:
 *    {
 *      "type": "cookie",
 *      "meta": {
 *        "id": <integer>,
 *        "cookie": <processor|string>
 *      }
 *    }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class AuthCookie extends Processor
{
  protected $required = array('cookie');

  public function process()
  {
    Debug::variable($this->meta, 'AuthCookie');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $cookie = $this->getVar($this->meta->cookie);
    if ($this->status != 200) {
      return $cookie;
    }

    return array(CURLOPT_COOKIE => $cookie);
  }
}
