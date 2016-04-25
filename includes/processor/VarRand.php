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
 *      "numeric":<boolean>,
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
        'accepts' => array('processor', 'integer')
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
      'numeric' => array(
        'description' => 'Use numeric characters.',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'bool')
      ),
      'nonalphanumic' => array(
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
    $numeric = $this->val($this->meta->numeric);
    $nonAlphanum = $this->val($this->meta->nonAlphanum);

    return Core\Utilities::random_string($length, $lower, $upper, $numeric, $nonAlphanum);
  }
}
