<?php

/**
 * Perform merge of multiple sources.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class Merge extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Merge',
    'machineName' => 'merge',
    'description' => 'Merge multiple data-sets.',
    'menu' => 'Operation',
    'application' => 'Common',
    'input' => array(
      'sources' => array(
        'description' => 'The data-sets to be merged.',
        'cardinality' => array(2, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'mergeType' => array(
        'description' => 'The merge operation to perform.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('union', 'intersect', 'difference'),
        'default' => 'union'
      ),
      'unique' => array(
        'description' => 'Disallow duplicate values.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => false
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'processor Merge', 4);

    $sources = $this->val('sources', true);
    $unique = $this->val('unique', true);
    $mergeType = $this->val('mergeType', true);
    $method = '_' . strtolower(trim($mergeType));

    if (!method_exists($this, $method)) {
      throw new Core\ApiException("invalid mergeType: $mergeType", 6, $this->id, 407);
    }

    if ($unique === true) {
      return array_unique($this->$method($sources));
    }
    return $this->$method($sources);
  }

  /**
   * @param $values
   * @return array|mixed
   */
  private function _union($values)
  {
    $result = array_shift($values);
    $result = is_array($result) ? $result : array($result);
    foreach ($values as $value) {
      $value = is_array($value) ? $value : array($value);
      $result = array_merge($result, $value);
    }
    return $result;
  }

  /**
   * @param $values
   * @return array|mixed
   */
  private function _intersect($values)
  {
    $result = array_shift($values);
    $result = is_array($result) ? $result : array($result);
    foreach ($values as $value) {
      $value = is_array($value) ? $value : array($value);
      $result = array_intersect($result, $value);
    }
    return $result;
  }

  private function _difference($values)
  {
    $result = array_shift($values);
    $result = is_array($result) ? $result : array($result);
    foreach ($values as $value) {
      $value = is_array($value) ? $value : array($value);
      $result = array_merge(array_diff($result, $value), array_diff($value, $result));
    }
    return $result;
  }
}
