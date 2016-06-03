<?php

/**
 * Sort logic gate.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Sort extends ProcessorBase
{
  protected $details = array(
    'name' => 'Sort',
    'description' => 'Sort an input of multiple values. The values can be singular items or name/value pairs (sorted by key or value). Singular items cannot be mixed with name/value pairs.',
    'menu' => 'Logic',
    'application' => 'Common',
    'input' => array(
      'values' => array(
        'description' => 'The values to sort.',
        'cardinality' => array(0, '*'),
        'accepts' => array('function', 'literal'),
      ),
      'direction' => array(
        'description' => 'Sort ascending or descending.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', '"asc"', '"desc"'),
      ),
      'sortByValue' => array(
        'description' => 'If set to true, sort by the value, otherwise sort by key (only used if the sources are of type Field, and the sortable key or value cannot be another key/value pair). Default is false.',
        'cardinality' => array(0, 1),
        'accepts' => array('function', '"true"', '"false"'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Sort', 4);

    $values = $this->val($this->meta->values);
    if (!is_array($values) || empty($values)) {
      return $values;
    }

    $asc = ($this->val($this->meta->direction) == 'asc');
    $sortByValue = isset($this->meta->sortByValue) ? $this->val($this->meta->sortByValue) == 'true' : false;

    if (!$sortByValue) {
      if ($asc) {
        ksort($values);
      } else {
        krsort($values);
      }
    } else {
      if ($asc) {
        asort($values);
      } else {
        arsort($values);
      }
    }

    return $values;
  }
}
