<?php

class GetImages {
  /**
   * get images
   *
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\/anon\/image\/",{"type":"varUri","meta":{"id":6,"index":0}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}
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
        'type' => 'inputUrl',
        'meta' => array(
          'id' => 3,
          'standardError' => TRUE,
          'method' => 'get',
          'source' => array(
            'type' => 'concatenate',
            'meta' => array(
              'id' => 4,
              'sources' => array(
                array(
                  'type' => 'varStore',
                  'meta' => array(
                    'id' => 5,
                    'operation' => 'fetch',
                    'var' => 'drupalUrl',
                  ),
                ),
                'api/anon/image/',
                array(
                  'type' => 'varUri',
                  'meta' => array(
                    'id' => 6,
                    'index' => 0,
                  ),
                ),
              ),
            ),
          ),
          'curlOpts' => array(
            'CURLOPT_SSL_VERIFYPEER' => 0,
            'CURLOPT_FOLLOWLOCATION' => 1,
          ),
        ),
      ),
    );
  }
}