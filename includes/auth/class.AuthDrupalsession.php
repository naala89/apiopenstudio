<?php

/**
 * Provide username/ Password authentication
 *
 * this class is to be used by ProcessorInput.
 * The map parameters allow user/pass var names to be mapped to different get vars and is optional
 *
 * Meta:
 *    {
 *      "authType": "drupalsession",
 *      "map": {
 *        "sessionName": <string>,
 *        "sessionId": <string>
 *      }
 *    }
 */
class AuthDrupalsession
{
  private $sessionName = '';
  private $sessionId = '';
  private $map = '';

  public function AuthDrupalsession($meta)
  {
    $get = $_GET;
    $sessionName = 'sessionName';
    $sessionId = 'sessionId';
    if (isset($meta['map'])) {
      $sessionName = $meta['map']['sessionName'];
      $sessionId = $meta['map']['sessionId'];
    }

    if (isset($get[$sessionName])) {
      $this->sessionName = $get[$sessionName];
    }
    if (isset($get[$sessionId])) {
      $this->sessionId = $get[$sessionId];
    }
  }

  public function process()
  {
    return array(
      CURLOPT_COOKIE => ($this->sessionName . '=' . $this->sessionId)
    );
  }
}