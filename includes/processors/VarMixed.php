<?php

/**
 * Parent class for mixed variable types
 *
 * METADATA
 * {
 *    "type":"var",
 *    "meta":{
 *      "id":<integer>,
 *      "var":<processor|mixed>,
 *    }
 *  }
 *
 * @TODO: rename class ProcessorVar to ProcessorVarMixed
 */

namespace Datagator\Processors;
use Datagator\Core;

class VarMixed extends ProcessorBase
{
  protected $required = array('value');
  protected $details = array(
    'name' => 'Var (Mixed)',
    'description' => 'A variable of any type.',
    'menu' => 'variables',
    'application' => 'All',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarMixed', 4);
    $this->validateRequired();
    return $this->getVar($this->meta->value);
  }
}