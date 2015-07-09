<?php

class SystemCache {
  /**
   * system cache
   *
   * local & dev
   * {"type":"systemCache","meta":{"id":1,"operation":{"type":"varUri","meta":{"id":2,"index":"0"}}}}
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
        'type' => 'systemCache',
        'meta' => array(
          'id' => 3,
          'operation' => array(
            'type' => 'varUri',
            'meta' => array(
              'id' => 4,
              'index' => '0',
            ),
          ),
        ),
      ),
    );
  }
}