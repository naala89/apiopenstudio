<?php

class GetVersion {
  /**
   * Validate version
   *
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"varStore","meta":{"id":3,"operation":"fetch","var":"version"}}
   */
  public function get() {
    return array(
      'validation' => array(
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
      'process' => array(
        'type' => 'varStore',
        'meta' => array(
          'id' => 3,
          'operation' => 'fetch',
          'var' => 'version',
        ),
      ),
    );
  }
}