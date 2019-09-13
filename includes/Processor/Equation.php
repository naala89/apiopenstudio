<?php

namespace Gaterdata\Processor;
use Gaterdata\Core;
use jlawrence\eos\Parser;

/**
 * Allows the calculation of equations, with variables.
 */
class Equation extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Equation',
    'machineName' => 'equation',
    'description' => 'This function allows you to define an equation with variables. These input variables are name/value pairs and substitute variables in th4e equation.',
    'menu' => 'Operation',
    'application' => 'Common',
    'input' => array(
      'equation' => array(
        'description' => 'The equation.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'variables' => array(
        'description' => 'The variables. These are an associative array',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array('varObject'),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Equation', 4);

    $eq = $this->val('equation', true);
    $vars = $this->val('variables', true);

    try {
      $result = Parser::solve($eq, $vars);
    } catch (\Exception $e) {
      throw new Core\ApiException($e->getMessage(), 0, $this->id);
    }

    return new Core\DataContainer($result, 'number');
  }
}
