<?php

/**
 * Perform filter
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Console\Helper\DebugFormatterHelper;

class Filter extends Core\ProcessorEntity
{
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
        'limitTypes' => array('string', 'array'),
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
      'keyOrValue' => array(
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
    Core\Debug::variable($this->meta, 'Processor Filter', 4);

    $filter = $this->val('filter', true);
    $keyOrValue = $this->val('keyOrValue', true);
    $recursive = $this->val('recursive', true);
    $inverse = $this->val('inverse', true);
    $regex = $this->val('regex', true);
    $values = $this->val('values', true);

    // Nothing to filter.
    if (empty($values)) {
      return $this->val('values');
    }

    // Nothing to filter.
    if (empty($filter)) {
      return $this->val('values');
    }

    // Test for multiple filters if regex (not allowed because it is inefficient).
    if ($regex === true && is_array($filter)) {
      throw new Core\ApiException('cannot have an array of regexes as a filter', 0, $this->id, 417);
    }

    // Regex filter accepted as a string, convert to array so it is always an array
    if (!$regex && !is_array($filter)) {
      $filter = array($filter);
    }

    $func = '_filterBy' . ucfirst($keyOrValue) . ($recursive ? 'Recursive' : 'Nonrecursive');
    $getCallback = '_callback' . ($inverse ? 'Inverse' : 'Noninverse') . ($regex ? 'Regex' : 'Nonregex');
    $callback = $this->{$getCallback}($filter);

    $values = $this->{$func}($values, $callback);

    // TODO: better dynamic container type
    return new Core\DataContainer($values, is_array($values) ? 'array' : 'text');
  }

  /**
   * Perform non-recursive filter on $data, based on key value.
   * @see https://wpscholar.com/blog/filter-multidimensional-array-php/
   * @see http://www.phptherightway.com/pages/Functional-Programming.html
   * @param $data
   * @param $callback
   * @return array
   */
  private function _filterByKeyNonrecursive($data, $callback)
  {
    if (!is_array($data)) {
      return $data;
    }

    return array_filter($data, $callback, ARRAY_FILTER_USE_KEY);
  }

  /**
   * Perform recursive filter on $data, based on key value.
   * @param $data
   * @param $callback
   * @return array
   */
  private function _filterByKeyRecursive($data, $callback)
  {
    if (!is_array($data)) {
      return $data;
    }

    $data = array_filter($data, $callback, ARRAY_FILTER_USE_KEY);

    foreach ($data as $key => $item) {
      if (is_array($item)) {
        $data[$key] = $this->_filterByKeyRecursive($item, $callback);
      }
    }

    return $data;
  }

  /**
   * Perform non-recursive filter on $data, based on value.
   * @param $data
   * @param $callback
   * @return array
   */
  private function _filterByValueNonrecursive($data, $callback)
  {
    if (!is_array($data)) {
      return !$callback($data) ? null : $data;
    }

    foreach ($data as $key => $item) {
      if (!is_array($item) && !$callback($item)) {
        unset($data[$key]);
      }
    }

    return $data;
  }

  /**
   * Perform non-recursive filter on $data, based on value.
   * @param $data
   * @param $callback
   * @return array
   */
  private function _filterByValueRecursive($data, $callback)
  {
    if (!is_array($data)) {
      return $callback($data) ? null : $data;
    }

    foreach ($data as $key => $item) {
      if (!is_array($item)) {
        if (!$callback($item)) {
          unset($data[$key]);
        }
      } else {
        $data[$key] = $this->_filterByValueRecursive($item, $callback);
      }
    }

    return $data;
  }

  /**
   * Filter callback for non-inverse, non-regex.
   * @param $filter
   * @return \Closure
   */
  private function _callbackNoninverseNonregex($filter)
  {
    return function($item) use ($filter) {
      return !in_array($item, $filter);
    };
  }

  /**
   * Filter callback for inverse, non-regex.
   * @param $filter
   * @return \Closure
   */
  private function _callbackInverseNonregex($filter)
  {
    return function($item) use ($filter) {
      return in_array($item, $filter);
    };
  }

  /**
   * Filter callback for non-inverse, regex.
   * @param $filter
   * @return \Closure
   */
  private function _callbackNoninverseRegex($filter)
  {
    return function($item) use ($filter) {
//      var_dump($item);
//      var_dump($filter);
//      var_dump(preg_match($filter, $item));
      return preg_match($filter, $item) == 0;
    };
  }

  /**
   * Filter callback for inverse, regex.
   * @param $filter
   * @return \Closure
   */
  private function _callbackInverseRegex($filter)
  {
    return function($item) use ($filter) {
      return preg_match($filter, $item) > 0;
    };
  }
}
