<?php

namespace Datagator\Yaml;

class ValidateToken {
  /**
   * Validate token is still active.
   *
   * {"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}}
   */
  public function get() {
    return array(
      'process' => array(
        'type' => 'validateToken',
        'meta' => array(
          'id' => 1,
          'token' => array(
            'type' => 'varGet',
            'meta' => array(
              'id' => 2,
              'var' => 'token',
            ),
          ),
        ),
      ),
    );
  }
}