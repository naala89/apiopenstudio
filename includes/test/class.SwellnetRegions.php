<?php

class SwellnetRegions
{
  /**
   * swellnet dev regions
   *
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":3,"sources":[{"type":"varStore","meta":{"id":4,"operation":"fetch","var":"drupalUrl"}},"api\/anon\/location\/region\/",{"type":"varUri","meta":{"id":5,"index":0}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}
   */
  public function get()
  {
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
       'type' => 'inputUrl',
       'meta' => array(
          'standardError' => true,
          'method' => 'get',
          'source' => array(
             'type' => 'concatenate',
             'meta' => array(
                'id' => 3,
                'sources' => array(
                   array(
                      'type' => 'varStore',
                      'meta' => array(
                         'id' => 4,
                         'operation' => 'fetch',
                         'var' => 'drupalUrl',
                      ),
                   ),
                   'api/anon/location/region/',
                   array(
                      'type' => 'varUri',
                      'meta' => array(
                         'id' => 5,
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
    );
  }
}