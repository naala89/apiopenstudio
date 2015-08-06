<?php

namespace Datagator\Yaml;

class GetProcessors {
  /**
   * get all public processors and ones for the client
   *
   * {"type":"processors","meta":{"id":1}}
   *
   * @return array
   */
  public function get() {
    return array(
      'process' => array(
        'type' => 'processors',
        'meta' => array(
          'id' => 1,
        ),
      ),
    );
  }
}