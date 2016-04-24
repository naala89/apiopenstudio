<?php

namespace Datagator\Output;

class Plain extends Text
{
  protected $header = 'Content-Type:text/plain';
  public $details = array(
    'name' => 'Plain',
    'description' => 'Output in plain-text format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(
      'destination' => array(),
    ),
  );
}