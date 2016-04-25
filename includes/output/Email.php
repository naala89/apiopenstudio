<?php

namespace Datagator\Output;

class Email extends Output
{
  public $details = array(
    'name' => 'Email',
    'description' => 'Output in email format.',
    'menu' => 'Output',
    'application' => 'All',
    'input' => array(),
  );

  protected function getData()
  {}
}
