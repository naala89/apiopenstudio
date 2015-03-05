<?php

class SwellnetAnonToken
{
  /**
   * get token for Swellnet unauthenticated user, or generate a new one
   *
   * {"type":"swellnetToken","meta":{"id":1}}
   *
   */
  public function get()
  {
    return array(
      'type' => 'SwellnetAnonToken',
      'meta' => array(
        'id' => 1,
      )
    );
  }
}