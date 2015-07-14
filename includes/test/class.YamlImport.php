<?php

class YamlImport {
  /**
   * get articles
   *
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\/anon\/article\/latest\/",{"type":"varUri","meta":{"id":6,"index":0}},"\/",{"type":"varUri","meta":{"id":7,"index":1}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}
   */
  public function get() {
    return array(
      'process' => array(
        'type' => 'yaml',
        'meta' => array(
          'id' => 1,
          'func' => 'import',
          'yaml' => array(
            'type' => 'varPost',
            'meta' => array(
              'id' => 2,
              'var' => 'yaml'
            )
          )
        )
      )
    );
  }
}
