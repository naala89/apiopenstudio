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

class Variable extends ProcessorBase
{
  protected $required = array('var');
  protected $details = array(
    'name' => 'Var (Mixed)',
    'description' => 'A variable of any type.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'mixed')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::message('Processor Var', 4);
    return $this->getVar($this->meta->var);
  }
}