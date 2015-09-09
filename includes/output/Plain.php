<?php

namespace Datagator\Output;

class Plain extends Text
{
  protected $header = 'Content-Type:text/plain';
  protected $details = array(
    'name' => 'Plain',
    'description' => 'Output in plain-text format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(
      'destination' => array(
        'description' => 'List of URLs to send to (other than response).',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );
}