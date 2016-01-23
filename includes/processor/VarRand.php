<?php

/**
 * Variable type random
 *
 * METADATA
 * {
 *    "type":"string",
 *    "meta":{
 *      "id":<integer>,
 *      "length":<integer>,
 *      "lower":<boolean>,
 *      "upper":<boolean>,
 *      "number":<boolean>,
 *      "non_alphanum":<boolean>,
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarRand extends ProcessorBase
{
  protected $details = array(
    'name' => 'Var (Rand)',
    'description' => 'A random variable. It produces a random variable of any specified length or mix of character types.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'length' => array(
        'description' => 'The length of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'int')
      ),
      'lower' => array(
        'description' => 'Use lower-case alpha characters.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'bool')
      ),
      'upper' => array(
        'description' => 'Use upper-case alpha characters.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'bool')
      ),
      'number' => array(
        'description' => 'Use numeric characters.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'bool')
      ),
      'non-alphanum' => array(
        'description' => 'Use non-alphanumeric characters.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'bool')
      ),
    ),
  );


  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarRand', 4);

    $length = $this->val($this->meta->length);
    $lower = $this->val($this->meta->lower);
    $upper = $this->val($this->meta->upper);
    $number = $this->val($this->meta->number);
    $nonAlphanum = $this->val($this->meta->nonAlphanum);

    return Core\Utilities::random_string($length, $lower, $upper, $number, $nonAlphanum);
  }
}
