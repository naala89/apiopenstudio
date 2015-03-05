<?php

/**
 * Perform mathematical processes.
 *
 * This uses eqEOS.
 *
 * @SEE http://stackoverflow.com/questions/5057320/php-function-to-evaluate-string-like-2-1-as-arithmetic-2-1-1
 * @SEE https://github.com/jlawrence11/Classes
 * TODO: This is temporarily abandoned work, and does not work. Fix this.
 * TODO: Refine this class so that all mathematical functions are allowed, as per eqEOS
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');
include_once(Config::$dirIncludes . '/math/eos.class.php');

class ProcessorMath extends Processor
{
  protected $required = array('vars', 'operator');
  private $validOperators = array('<', '>', '==', '===', '+', '-', '/', '*', '%', '^');

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorCompare');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $operator = $this->getVar($this->meta->operator);
    if ($this->status != 200) {
      return $operator;
    }
    if (!in_array($operator, $this->validOperators)) {
      $this->status = 417;
      return new Error(3, $this->id, "Invalid operator");
    }

    $vars = array();
    $count = 1;
    foreach ($this->meta->vars as $var) {
      $v = $this->getVar($var);
      if ($this->status != 200) {
        return $v;
      }
      if (!is_string($v) && !is_numeric($v)) {
        $this->status = 417;
        Debug::variable($v);
        return new Error(3, $this->id, "variable $count did not produce a string or number. Could not compute");
      }
      $vars[] = $v;
    }

    return $this->doOperation($vars, $operator);
  }

  private function doOperation($vars, $operator)
  {
    $equation = implode($operator, $vars);
    $eq = new eqEOS();
    return $eq->solveIF($equation);
  }
}
