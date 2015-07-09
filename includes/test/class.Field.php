<?php

class Field {
  /**
   * create a field
   *
   *
   */
  public function get() {
    return array(
      'process' => array(
        'type' => 'field',
        'meta' => array(
          'id' => 1,
          'name' => array(
            'type' => 'varStore',
            'meta' => array(
              'id' => 2,
              'operation' => 'fetch',
              'var' => 'drupalUrl',
            ),
          ),
          'value' => 'value',
        ),
      )
    );
  }
}