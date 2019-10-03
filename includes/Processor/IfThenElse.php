<?php

/**
 * An If Then Else logic gate.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class IfThenElse extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'If Then Else',
    'machineName' => 'ifThenElse',
    'description' => 'An if then else logic gate.',
    'menu' => 'Logic',
    'application' => 'Common',
    'input' => array(
      'lhs' => array(
        'description' => 'The left-land side value in the equation.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'rhs' => array(
        'description' => 'The right-land side value in the equation.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'operator' => array(
        'description' => 'The comparison operator in the equation.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('==', '!=', '>', '>=', '<', '<='),
        'default' => ''
      ),
      'then' => array(
        'description' => 'What to do if the equation returns true.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'else' => array(
        'description' => 'What to do if the equation returns false.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);
    $lhs = $this->val('lhs', true);
    $rhs = $this->val('rhs', true);
    $operator = $this->val('operator', true);

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
      return $this->val('then');
    } else {
      return $this->val('else');
    }
  }
}
