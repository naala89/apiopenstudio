<?php

/**
 * Sort logic gate.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Sort extends ProcessorBase
{
  private $asc;
  private $sortByValue;

  protected $details = array(
    'name' => 'Sort',
    'description' => 'Sort an input of multiple values. The values can be singular items or name/value pairs (sorted by key or value). Singular items cannot be mixed with name/value pairs.',
    'menu' => 'Logic',
    'application' => 'All',
    'input' => array(
      'values' => array(
        'description' => 'The values to sort.',
        'cardinality' => array(0, '*'),
        'accepts' => array('processor', 'literal'),
      ),
      'direction' => array(
        'description' => 'Sort ascending or descending.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"asc"', '"desc"'),
      ),
      'sortByValue' => array(
        'description' => 'If set to true, sort by the value, otherwise sort by key (only used if the sources are of type Field, and the sortable key or value cannot be another key/value pair). Default is false.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', '"true"', '"false"'),
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
    $this->asc = ($this->val($this->meta->direction) == 'asc');
    $this->sortByValue = isset($this->meta->sortByValue) ? $this->val($this->meta->sortByValue) == 'true' : false;

    Core\Debug::variable($this->sortByValue);
    Core\Debug::variable($this->asc);
    
    $haveField = false;
    $haveVal = false;
    foreach ($values as $value) {
      if (is_array($value)) {
        if (sizeof($value) > 1) {
          throw new Core\ApiException('invalid value found in sort - can only use single values or key/value pairs', 1, $this->id);
        }
        $haveField = true;
      } else {
        $haveVal = true;
      }
    }

    if ($haveVal && $haveField) {
      throw new Core\ApiException('cannot sort a collection of mixed name/value pairs and values', 1, $this->id);
    }
    if ($haveField) {
      usort($values, array($this, 'sortFields'));
    } elseif ($this->asc) {
      sort($values);
    } else {
      rsort($values);
    }

    return $values;
  }

  /**
   * @param $a
   * @param $b
   * @return int
   * @throws \Datagator\Core\ApiException
   */
   public function sortFields($a, $b)
   {
     $ka = array_keys($a);
     $kb = array_keys($b);
     if ($this->sortByValue) {
       $sa = $ka[0];
       $sb = $kb[0];
     } else {
       $sa = $a[$ka[0]];
       $sb = $b[$kb[0]];
     }

     if ($sa == $sb) {
       return 0;
     }
     if (is_numeric($sa) && is_numeric($sb)) {
       if (!$this->asc) {
         return $sa > $sb ? +1 : -1;
       } else {
         return $sb > $sa ? +1 : -1;
       }
     } else {
       if (!$this->asc) {
         return strnatcmp($sa, $sb);
       } else {
         return strnatcmp($sb, $sa);
       }
     }
   }
}
