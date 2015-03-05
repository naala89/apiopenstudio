<?php

class SwellnetWamVideo {
  /**
   * get wams video
   *
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetWamVideo","meta":{"id":3,"drupalUrl":{"type":"varStore","meta":{"id":4,"var":"drupalUrl","operation":"fetch"}},"apiUrl":{"type":"varStore","meta":{"id":5,"var":"apiUrl","operation":"fetch"}},"uriPattern":{"type":"varStr","meta":{"id":6,"var":"api\/anon\/wams\/%locationId%"}},"locationId":{"type":"varUri","meta":{"id":7,"index":0}},"videoType":{"type":"varUri","meta":{"id":8,"index":1}}}}
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
      'type' => 'swellnetWamVideo',
      'meta' => array(
        'id' => 3,
        'drupalUrl' => array(
          'type' => 'varStore',
          'meta' => array(
            'id' => 4,
            'var' => 'drupalUrl',
            'operation' => 'fetch'
          ),
        ),
        'apiUrl' => array(
          'type' => 'varStore',
          'meta' => array(
            'id' => 5,
            'var' => 'apiUrl',
            'operation' => 'fetch'
          ),
        ),
        'uriPattern' => array(
          'type' => 'varStr',
          'meta' => array(
            'id' => 6,
            'var' => 'api/anon/wams/%locationId%'
          ),
        ),
        'locationId' => array(
          'type' => 'varUri',
          'meta' => array(
            'id' => 7,
            'index' => 0
          ),
        ),
        'videoType' => array(
          'type' => 'varUri',
          'meta' => array(
            'id' => 8,
            'index' => 1
          ),
        ),
      ),
    );
  }
}