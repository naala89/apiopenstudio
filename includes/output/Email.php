<?php

/**
 * TODO: Define outputEmail
 */

namespace Datagator\Output;

class Email extends Output
{
  protected $required = array('destination', 'format');
  protected $details = array(
    'name' => 'Email',
    'description' => 'Output in email format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(
      'destination' => array(
        'description' => 'List of URLs to send to (other than response).',
        'cardinality' => array(1, '*'),
        'accepts' => array('processor', 'literal'),
      ),
      'format' => array(
        'description' => 'How to format the data when its placed in the email body.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"xml"', '"html"', '"json"','"text"', '"plain"', '"image"'),
      ),
    ),
  );

  protected function getData()
  {}
}
