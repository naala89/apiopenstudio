<?php

class YamlImport {
  /**
   * get articles
   *
   * {"process":{"type":"yaml","meta":{"id":1,"func":"import","yaml":{"type":"varPost","meta":{"id":2,"var":"yaml"}}}}}
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
