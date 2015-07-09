<?php

class Object {
  /**
   * create a field
   *
   *
   */
  public function get() {
    return array(
      'process' => array(
        'type' => 'object',
        'meta' => array(
          'id' => 1,
          'attributes' => array(
            array(
              'type' => 'field',
              'meta' => array(
                'id' => 2,
                'name' => 'name1',
                'value' => array(
                  'type' => 'varGet',
                  'meta' => array(
                    'id' => 2,
                    'var' => 'token',
                  ),
                ),
              ),
            ),
            array(
              'type' => 'field',
              'meta' => array(
                'id' => 3,
                'name' => 'name2',
                'value' => 'value',
              ),
            ),
            array(
              'type' => 'field',
              'meta' => array(
                'id' => 4,
                'name' => 'name3',
                'value' => 'value',
              ),
            ),
          ),
        ),
      ),
    );
  }
}