<?php

/**
 * TODO: Define outputEmail
 */

namespace Datagator\Output;

class Email extends Output
{
  protected $required = array();
  public $details = array(
    'name' => 'User Login',
    'description' => 'Login a user for token-based API access.',
    'menu' => 'Validator',
    'application' => 'All',
    'input' => array(
      'username' => array(
        'description' => 'Users username.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'password' => array(
        'description' => 'Users password.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  protected function getData()
  {}
}
