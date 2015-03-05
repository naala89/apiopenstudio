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

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorVar extends Processor
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
    Debug::variable($this->meta, 'ProcessorVar', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    return $this->getVar($this->meta->var);
  }
}