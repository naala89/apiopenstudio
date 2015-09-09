<?php

namespace Datagator\Output;

class Html extends Xml
{
  protected $details = array(
    'name' => 'Html',
    'description' => 'Output in HTML format.',
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