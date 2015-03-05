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

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarRand extends ProcessorVar
{
  protected $details = array(
    'name' => 'Var (Rand)',
    'description' => 'A random variable. It produces a random variable of any specified length or mix of character types.',
    'menu' => 'variables',
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
    Debug::message('ProcessorVarRand', 4);
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }
    $length = $this->getVar($this->meta->length);
    if ($this->status != 200) {
      return $length;
    }
    $lower = $this->getVar($this->meta->lower);
    if ($this->status != 200) {
      $this->status = 200;
      $lower = FALSE;
    }
    $upper = $this->getVar($this->meta->upper);
    if ($this->status != 200) {
      $this->status = 200;
      $upper = FALSE;
    }
    $number = $this->getVar($this->meta->number);
    if ($this->status != 200) {
      $this->status = 200;
      $number = FALSE;
    }
    $nonAlphanum = $this->getVar($this->meta->nonAlphanum);
    if ($this->status != 200) {
      $this->status = 200;
      $nonAlphanum = FALSE;
    }

    return Utilities::random_string($length, $lower, $upper, $number, $nonAlphanum);
  }
}
