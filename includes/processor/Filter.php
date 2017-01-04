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
      'strict' => array(
        'description' => 'If set to true, the comparisons are strict, i.e. boolean values and their numeric equivalents are distinct. If to false, the comparison between boolean and their numeric values are ot distinct.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => 'true'
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
    $strict = $this->val('strict', true);
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
      throw new Core\ApiException('cannot have an array of regexes as a filter', 0, $this->id);
    }

    // Regex filter accepted as a string, convert to array so it is always an array
    if (!$regex && !is_array($filter)) {
      $filter = array($filter);
    }

    $func = '_arrayFilter' . ucfirst($keyOrValue) . ($recursive ? 'Recursive' : 'Nonrecursive');
    Core\Debug::variable($func, '$func');
    $getCallback = '_callback' . ($inverse ? 'Inverse' : 'Noninverse') . ($regex ? 'Regex' : 'Nonregex') . ($strict ? 'Strict' : 'Nonstrict');
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
  private function _arrayFilterKeyNonrecursive($data, $callback)
  {
    if (!is_array($data)) {
      return $data;
    }

    return array_filter($data, $callback, ARRAY_FILTER_USE_KEY);
  }

  /**
   * Filter callback for non-inverse, non-regex, not strict comparison.
   * @param $filter
   * @return \Closure
   */
  private function _callbackNoninverseNonregexNonstrict($filter)
  {
    return function($item) use ($filter) {
      return !in_array($item, $filter);
    };
  }
}
