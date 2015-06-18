<?php

/**
 * Provide username/ Password authentication
 *
 * this class is to be used by ProcessorInput.
 * The map parameters allow user/pass var names to be mapped to different get vars
 *
 * Meta:
 *    {
 *      "type": "userPass",
 *      "meta": {
 *        "id": <integer>,
 *        "username": <obj|string>,
 *        "password": <obj|string>
 *      }
 *    }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class AuthBasicAuth extends Processor
{
  protected $required = array('username', 'password');

  public function process()
  {
    Debug::variable($this->meta, 'AuthBasicAuth', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $username = $this->getVar($this->meta->username);
    $password = $this->getVar($this->meta->password);

    return array(CURLOPT_HTTPHEADER => array("Authorization: Basic " . base64_encode("$username:$password")));
  }
}