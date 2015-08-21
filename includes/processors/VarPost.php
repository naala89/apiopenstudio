<?php

/**
 * Post variable
 *
 * METADATA
 * {
 *    "type":"postVar",
 *    "meta":{
 *      "var":<processor|mixed>,
 *    }
 *  }
 */

namespace Datagator\Processors;
use Datagator\Core;

class VarPost extends VarMixed
{
  protected $required = array('name');
  protected $details = array(
    'name' => 'Var (Get)',
    'description' => 'A "post" variable. It fetches a variable from the post request.',
    'menu' => 'variables',
    'client' => 'all',
    'input' => array(
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal')),
    ),
  );

  /**
   * @return mixed
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processors\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarPost', 4);
    $name = parent::process();

    if (empty($this->request->vars[$name])) {
      throw new Core\ApiException("post variable ($name) does not exist", 5, $this->id, 417);
    } else {
      $result = $this->request->vars[$name];
    }

    return $result;
  }
}
