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
        'accepts' => array('processor', 'literal'),
      ),
      'rhs' => array(
        'description' => 'The right-land side value in the equation.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'operator' => array(
        'description' => 'The comparison operator in the equation.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', '"=="', '"==="', '"!="', '">"', '">="', '"<"', '"<="'),
      ),
      'then' => array(
        'description' => 'What to do if the equation returns true.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'else' => array(
        'description' => 'What to do if the equation returns false.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor IfThenElse', 4);
    $lhs = $this->val($this->meta->lhs);
    $rhs = $this->val($this->meta->rhs);
    $operator = $this->val($this->meta->operator);

    $eqRes = false;
    switch ($eqRes) {
      case '==':
        $result = $lhs == $rhs;
        break;
      case '===':
        $result = $lhs === $rhs;
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
      case '<=':
        $result = $lhs <= $rhs;
        break;
      default:
        throw new Core\ApiException('invalid operator', 1, $this->id);
        break;
    }

    // equation is true
    if ($eqRes) {
      return $this->val($this->meta->then);
    } else {
      return $this->val($this->meta->else);
    }
  }
}
