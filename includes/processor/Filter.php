<?php

/**
 * Perform filter
 */

namespace Datagator\Processor;
use Datagator\Core;
use phpDocumentor\Reflection\Types\Boolean;
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
    $func = '_arrayFilter' . ucfirst($keyValue);
    $callback = $regex ? $this->_regexComparison($filter, $inverse) : $this->_strictComparison($filter, $inverse);

    $values = $this->{$func}($values, $callback, $recursive, $inverse);

    // TODO: better dynamic container type
    return new Core\DataContainer($values, is_array($values) ? 'array' : 'text');
  }

  /**
   * Recursively filter an array by value
   * @see https://wpscholar.com/blog/filter-multidimensional-array-php/
   * @param $array
   * @param callable|NULL $callback
   * @return array
   */
  private function _arrayFilterValue($array, callable $callback=null, $recursive, $inverse) {
    if ($this->isDataContainer($array)) {
      $array = $array->getData();
    }
    if (!is_array($array)) {
      $array = array($array);
    }

    $array = is_callable($callback) ? array_filter($array, $callback) : array_filter($array);
    if ($recursive) {
      foreach ($array as & $value) {
        if (is_array($value)) {
          $value = call_user_func(__FUNCTION__, $value, $callback, $recursive, $inverse);
        }
      }
    }

    return $array;
  }

  /**
   * Recursively filter an array by key
   * @param $array
   * @param callable|NULL $callback
   * @return array
   */
  private function _arrayFilterKey($array, callable $callback=null, $recursive, $inverse) {
    if ($this->isDataContainer($array)) {
      $array = $array->getData();
    }
    if (!is_array($array)) {
      $array = array($array);
    }

    Core\Debug::variable($array, 'array');

    foreach ($array as $key => & $value) {
      if (is_array($value)) {
        if ($recursive) {
          Core\Debug::message('recursive and have an array');
          $value = call_user_func(__FUNCTION__, $value, $callback, $recursive, $inverse);
        }
      } else {
        if ($callback($key)) {
          Core\Debug::variable('passed callback', $key);
          if (!$inverse) {
            Core\Debug::message('not inverse');
            unset($array[$key]);
          }
        } else {
          Core\Debug::variable('failed callback', $key);
          if ($inverse) {
            Core\Debug::message('inverse');
            unset($array[$key]);
          }
        }
      }
    }

    return $array;
  }

  /**
   * @see http://www.phptherightway.com/pages/Functional-Programming.html
   * @param $pattern
   * @return \Closure
   */
  private function _regexComparison($pattern, $inverse)
  {
    return function($item) use ($pattern, $inverse) {
      //Core\Debug::variable($item, '$item');
      //Core\Debug::variable($pattern, '$pattern');
      if ($this->inverse) {
        return !preg_match($pattern, $item);
      }
      return preg_match($pattern, $item);
    };
  }

  /**
   * @see http://www.phptherightway.com/pages/Functional-Programming.html
   * @param $array
   * @return \Closure
   */
  private function _strictComparison($array, $inverse)
  {
    return function($item) use ($array, $inverse) {
      Core\Debug::variable($item, '$item');
      Core\Debug::variable($array, '$array');
      Core\Debug::variable(in_array($item, $array), 'equality');
      if ($this->inverse) {
        return !in_array($item, $array);
      }
      return in_array($item, $array);
    };
  }
}
