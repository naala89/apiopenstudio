<?php

class UserLogin {
  /**
   * swellnet user login
   *
   * {"type":"loginStoreDrupal","meta":{"id":1,"source":{"type":"inputUrl","meta":{"id":2,"source":{"type":"concatenate","meta":{"id":3,"sources":[{"type":"varStore","meta":{"id":4,"var":"drupalUrl","operation":"fetch"}},"api\/anon\/user\/login"]}},"method":"post","vars":{"username":{"type":"varPost","meta":{"id":5,"var":"username"}},"password":{"type":"varPost","meta":{"id":6,"var":"password"}}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}}}
   */
  public function get() {
    return array(
      'process' => array(
        'type' => 'loginStoreDrupal',
        'meta' => array(
          'id' => 1,
          'source' => array(
            'type' => 'inputUrl',
            'meta' => array(
              'id' => 2,
              'source' => array(
                'type' => 'concatenate',
                'meta' => array(
                  'id' => 3,
                  'sources' => array(
                    array(
                      'type' => 'varStore',
                      'meta' => array(
                        'id' => 4,
                        'var' => 'drupalUrl',
                        'operation' => 'fetch',
                      ),
                    ),
                    'api/anon/user/login'
                  ),
                ),
              ),
              'method' => 'post',
              'vars' => array(
                'username' => array(
                  'type' => 'varPost',
                  'meta' => array(
                    'id' => 5,
                    'var' => 'username',
                  ),
                ),
                'password' => array(
                  'type' => 'varPost',
                  'meta' => array(
                    'id' => 6,
                    'var' => 'password',
                  ),
                ),
              ),
              'curlOpts' => array(
                'CURLOPT_SSL_VERIFYPEER' => 0,
                'CURLOPT_FOLLOWLOCATION' => 1,
              ),
            ),
          ),
        ),
      ),
    );
  }
}