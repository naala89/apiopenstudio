<?php

class SwellnetSurfReport
{
  /**
   * swellnet surf report
   *
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetReport","meta":{"id":3,"url":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\/anon\/surfreport\/"]}}}}
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
       'type' => 'swellnetReport',
       'meta' => array(
          'id' => 3,
          'url' => array(
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
                  'api/anon/surfreport/',
                ),
             ),
          ),
       ),
    );
  }
}