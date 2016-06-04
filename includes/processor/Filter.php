<?php

/**
 * Perform filter
 */

namespace Datagator\Processor;
use Datagator\Core;

class Filter extends ProcessorEntity
{
  protected $details = array(
    'name' => 'Filter',
    'description' => 'Filter values from a data-set.',
    'menu' => 'Operation',
    'application' => 'Common',
    'input' => array(
      'values' => array(
        'description' => 'The data-set to filter.',
        'cardinality' => array(0, '*'),
        'accepts' => array('function', 'literal')
      ),
      'filter' => array(
        'description' => 'The values to filter out. These are keys if values contains Fields, otherwise values.',
        'cardinality' => array(0, '*'),
        'accepts' => array('function', 'literal')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'processor Filter', 4);

    if (empty($this->meta->values)) {
      return [];
    }
    $values = $this->val($this->meta->values);
    if (empty($this->meta->filter)) {
      return $values;
    }
    $filter = $this->val($this->meta->filter);

    $filter = is_array($filter) ? $filter : array($filter);
    $values = is_array($values) ? $values : array($values);

    if (Core\Utilities::is_assoc($values)) {
      foreach ($filter as $key) {
        if (isset($values[$key])) {
          unset ($values[$key]);
        }
      }
    } else {
      foreach ($filter as $val) {
        if(($key = array_search($val, $values)) !== false) {
          unset($values[$key]);
        }
      }
    }

    return $values;
  }
}
