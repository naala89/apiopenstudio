<?php

/**
 * An If Then Else logic gate.
 */

namespace Datagator\Processor;
use Datagator\Core;

class IfThenElse extends ProcessorBase
{
  protected $details = array(
    'name' => 'If Then Else',
    'description' => 'An if then else logic gate.',
    'menu' => 'Logic',
    'application' => 'All',
    'input' => array(
      'lhs' => array(
        'description' => 'The left-land side value in the equation.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'rhs' => array(
        'description' => 'The right-land side value in the equation.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'operator' => array(
        'description' => 'The comparison operator in the equation.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', '"=="', '"!="', '">"', '">="', '"<"', '"<="'),
      ),
      'then' => array(
        'description' => 'What to do if the equation returns true.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
      'else' => array(
        'description' => 'What to do if the equation returns false.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor IfThenElse', 4);
    $lhs = $this->val($this->meta->lhs);
    $rhs = $this->val($this->meta->rhs);
    $operator = $this->val($this->meta->operator);

    switch ($operator) {
      case '==':
        $result = $lhs == $rhs;
        break;
      case '!=':
        $result = $lhs != $rhs;
        break;
      case '>':
        $result = $lhs > $rhs;
        break;
      case '>=':
        $result = $lhs >= $rhs;
        break;
      case '<':
        $result = $lhs < $rhs;
        break;
      case '<=':
        $result = $lhs <= $rhs;
        break;
      default:
        throw new Core\ApiException("invalid operator: $operator", 1, $this->id);
        break;
    }

    if ($result) {
      return $this->val($this->meta->then);
    } else {
      return $this->val($this->meta->else);
    }
  }
}
