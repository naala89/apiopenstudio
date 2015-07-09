<?php

class LatestImage {
  /**
   * Get latest image from dir
   *
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetImage","meta":{"id":3,"path":{"type":"varStore","meta":{"id":4,"operation":"fetch","var":"dropbox"}}}}
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
        'type' => 'swellnetImage',
        'meta' => array(
          'id' => 3,
          'path' => array(
            'type' => 'varStore',
            'meta' => array(
              'id' => 4,
              'operation' => 'fetch',
              'var' => 'dropbox',
            ),
          ),
        ),
      ),
    );
  }
}