<?php

/**
 * Perform mathematical processes.
 *
 * This uses eqEOS.
 *
 * @SEE http://stackoverflow.com/questions/5057320/php-function-to-evaluate-string-like-2-1-as-arithmetic-2-1-1
 * @SEE https://github.com/jlawrence11/Classes
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');
include_once(Config::$dirIncludes . '/math/eos.class.php');

class ProcessorMath extends Processor
{
  protected $required = array('vars', 'formula');
  protected $details = array(
    'name' => 'Math',
    'description' => 'Allows a simple or complex equation to be applied to input values. Variable names must start with a letter, and in the formula, the same variable name must be prefixed with "$"',
    'menu' => 'compute',
    'input' => array(
      'vars' => array(
        'description' => 'The inputs to the equation (variables and static values).',
        'cardinality' => array(1, '*'),
        'accepts' => array('processor', 'mixed')
      ),
      'var_names' => array(
        'description' => 'The names of the variables. These map 1:1 with vars.',
        'cardinality' => array(1, '*'),
        'accepts' => array('processor', 'mixed')
      ),
      'equation' => array(
        'description' => 'The equation.',
        'cardinality' => array(1, '*'),
        'accepts' => array('string')
      ),
    ),
  );

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorMath');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    if (sizeof($this->meta->vars) !== sizeof($this->meta->var_names)) {
      return new Error(3, $this->id, 'invalid mapping of var names to vars');
    }

    $vars = $this->meta->vars;
    $variables = array();
    $index = 0;
    foreach ($vars as $var) {
      $value = $this->getVar($var);
      if ($this->status != 200) {
        return $value;
      }
      $name = $this->getVar($this->meta->var_names[$index++]);
      if ($this->status != 200) {
        return $name;
      }
      $variables[$name] = $value;
    }

    $equation = $this->getVar($this->meta->equation);
    if ($this->status != 200) {
      return $equation;
    }

    try {
      $eq = new eqEOS();
      $result = $eq->solveIF($equation, $variables);
    } catch (Exception $e) {
      return new Error(3, $this->id, 'could not process equation. ' . $e->getMessage());
    }

    return $result;
  }
}
