<?php

/**
 * Sort logic gate.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Sort extends ProcessorBase
{
  private $asc;
  private $fld;
  protected $details = array(
    'name' => 'Sort',
    'description' => 'Sort an input of type Processor Object. Select a field and desc or asc',
    'menu' => 'Logic',
    'application' => 'All',
    'input' => array(
      'object' => array(
        'description' => 'The obj.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor Object'),
      ),
      'field' => array(
        'description' => 'The field to be used in the sort operation.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'direction' => array(
        'description' => 'Sort ascending or descending.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"asc"', '"desc"'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Sort', 4);
    $obj = $this->val($this->meta->object);
    $this->asc = $this->val($this->meta->direction) == 'asc';
    $this->fld = $this->val($this->meta->field);

    usort($obj, array($this, 'sort'));

    return $obj;
  }

   public function sort($a, $b)
   {
     if (!isset($a[$this->fld]) || !isset($b[$this->fld])) {
       throw new Core\ApiException('missing field in object', 1, $this->id);
     }
     if ($a == $b) {
       return 0;
     }
     if (is_numeric($a) && is_numeric($b)) {
       if ($this->asc) {
         return $a > $b ? +1 : -1;
       } else {
         return $b > $a ? +1 : -1;
       }
     } else {
       if ($this->asc) {
         return strnatcmp($a, $b);
       } else {
         return strnatcmp($b, $a);
       }
     }
   }
}
