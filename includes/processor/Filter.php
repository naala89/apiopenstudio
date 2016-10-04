<?php

/**
 * Perform filter
 */

namespace Datagator\Processor;
use Datagator\Core;
use Symfony\Component\Console\Helper\DebugFormatterHelper;

class Filter extends Core\ProcessorEntity
{
  private $filter;
  private $regex;
  private $keyValue;
  private $recursive;
  private $inverse;

  protected $details = array(
    'name' => 'Filter',
    'machineName' => 'filter',
    'description' => 'Filter values from a data-set.',
    'menu' => 'Operation',
    'application' => 'Common',
    'input' => array(
      'values' => array(
        'description' => 'The data-set to filter.',
        'cardinality' => array(0, '*'),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
      'filter' => array(
        'description' => 'The literal values to filter out.',
        'cardinality' => array(0, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'regex' => array(
        'description' => 'If set ot true, use the filter string as a regex. If set to false, use the filter string for exact comparison.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => 'false'
      ),
      'keyValue' => array(
        'description' => 'Filter by key or value.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('key', 'value'),
        'default' => 'value'
      ),
      'recursive' => array(
        'description' => 'Recursively filter the data set. Is set to false, the filter will only apply to the outer data-set. If set to true, the filter will apply to the entire data-set (warning: use sparingly, this could incur long processing times).',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => false
      ),
      'inverse' => array(
        'description' => 'If set to true, the filter will keep matching data. If set to false, the filter will only keep non-matching data.',
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
    Core\Debug::variable($this->meta, 'processor Filter', 4);

    $values = $this->val('values', true);
    if (empty($values)) {
      return $this->val('values');
    }

    $filter = $this->val('filter', true);
    if (empty($filter)) {
      return $this->val('values');
    }
    $regex = $this->val('regex', true);
    if ($regex && is_array($filter)) {
      throw new Core\ApiException('cannot have an array of regexes as a filter', 0, $this->id);
    }
    if (!$regex && !is_array($filter)) {
      $this->filter = array($filter);
    }
    $keyValue = $this->val('keyValue', true);
    $recursive = $this->val('recursive', true);
    $inverse = $this->val('inverse', true);

    $func = '_arrayFilter' . ucfirst($keyValue) . 'Recursive';
    $values = $this->{$func}($values, $regex ? $this->_regexComparison($filter) : $this->_strictComparison($filter));

    // TODO: better dynamic container type
    return new Core\DataContainer($values, is_array($values) ? 'array' : 'text');
  }

  /**
   * Recursively filter an array by value
   *
   * @see https://wpscholar.com/blog/filter-multidimensional-array-php/
   *
   * @param array $array
   * @param callable|NULL $callback
   * @return array
   */
  private function _arrayFilterValueRecursive(array $array, callable $callback=null) {
    $array = is_callable($callback) ? array_filter($array, $callback) : array_filter($array);
    foreach ($array as & $value) {
      if (is_array($value)) {
        $value = call_user_func(__FUNCTION__, $value, $callback);
      }
    }

    return $array;
  }

  private function _arrayFilterKeyRecursive(array $array, callable $callback=null) {
    Core\Debug::variable($array, 'array');
    $array = is_callable($callback) ? array_filter($array, $callback, ARRAY_FILTER_USE_KEY) : array_filter($array, ARRAY_FILTER_USE_KEY);
    foreach ($array as & $value) {
      if (is_array($value)) {
        $value = call_user_func(__FUNCTION__, $value, $callback);
      }
    }

    return $array;
  }

  /**
   * @see http://www.phptherightway.com/pages/Functional-Programming.html
   * @param $pattern
   * @return \Closure
   */
  private function _regexComparison($pattern)
  {
    return function($item) use ($pattern) {
      if ($this->isDataContainer($item)) {
        $item = $item->getData();
      }
      return preg_match($pattern, $item);
    };
  }

  /**
   * @see http://www.phptherightway.com/pages/Functional-Programming.html
   * @param $array
   * @return \Closure
   */
  private function _strictComparison($array)
  {
    return function($item) use ($array) {
      if ($this->isDataContainer($item)) {
        $item = $item->getData();
      }
      if (is_array($item)) {
        if (sizeof($item) == 1) {
          $keys = array_keys(($item));
          return !in_array(($item[$keys[0]]), $array);
        }
        return false;
      }
      return in_array($item, $array);
    };
  }
}
