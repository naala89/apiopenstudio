<?php

/**
 * Sort logic gate.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Sort extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Sort',
    'machineName' => 'sort',
    'description' => 'Sort an input of multiple values. The values can be singular items or name/value pairs (sorted by key or value). Singular items cannot be mixed with name/value pairs.',
    'menu' => 'Logic',
    'application' => 'Common',
    'input' => array(
      'values' => array(
        'description' => 'The values to sort.',
        'cardinality' => array(0, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'direction' => array(
        'description' => 'Sort ascending or descending.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('asc', 'desc'),
        'default' => 'asc'
      ),
      'sortByValue' => array(
        'description' => 'If set to true then sort by the value. If false then sort by key (only used if all the sources are of type Field or a single Object).',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Sort', 4);

    $values = $this->val('values');
    if (!is_array($values) || empty($values)) {
      return $values;
    }

    $asc = $this->val('direction', true);
    $sortByValue = $this->val('sortByValue', true);

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
