<?php

namespace Datagator\Output;

class Html extends Xml
{
  protected $header = 'Content-Type:text/html';
  protected $details = array(
    'name' => 'Html',
    'machineName' => 'html',
    'description' => 'Output in HTML format.',
    'menu' => 'Output',
    'application' => 'Common',
    'input' => array(
      'destination' => array(
        'description' => 'A single or array of URLs to send the results to.',
        'cardinality' => array(1, '*'),
        'accepts' => array('function', 'literal'),
      ),
      'method' => array(
        'description' => 'HTTP delivery method when sending output. Only used in the output section.',
        'cardinality' => array(0, '1'),
        'accepts' => array('function', '"get"', '"post"'),
      ),
      'options' => array(
        'description' => 'Extra Curl options to be applied when sent to the destination  (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'accepts' => array('function field'),
      ),
    ),
  );
}