<?php

namespace Datagator\Output;

class Html extends Xml
{
  protected $header = 'Content-Type:application/xml';
  protected $details = array(
    'name' => 'Html',
    'description' => 'Output in HTML format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(
      'destination' => array(
        'description' => 'List of URLs to send to.',
        'cardinality' => array(1, '*'),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );
}