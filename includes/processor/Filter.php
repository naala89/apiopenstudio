<?php

/**
 * Perform filter
 */

namespace Datagator\Processor;
use Datagator\Core;

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
    $this->filter = $this->val('filter', true);
    if (!empty($this->filter)) {
      return $this->val('values');
    }
    $this->regex = $this->val('regex', true);
    if ($this->regex && is_array($this->filter)) {
      throw new Core\ApiException('cannot have an array of regexes as a filter', 0, $this->id);
    }
    $this->keyValue = $this->val('keyValue', true);
    $this->recursive = $this->val('recursive', true);
    $this->inverse = $this->val('inverse', true);

    $func = '_deFilter' . ucfirst($this->keyValue);
    // TODO: better dynamic container type
    return new Core\DataContainer($this->{$func}($values), is_array($values) ? 'array' : 'text');
  }

  private function _doFilterKey(& $data)
  {
    if ($this->isDataContainer($data)) {
      $data = $this->val($data, true);
    }

    // if filter is by key, we only need to filter if value is an array
    if (is_array($data)) {
      // validate associative array
      foreach ($data as $key => & $value) {
        if (is_array($value)) {
          // array item is array
          if ($this->recursive) {
            // only continue if recursive filter
            $this->_doFilterValue($value);
          }
        } else {
          // array item is single value
          if ($this->regex) {
            if (preg_match($this->filter, $key)) {
              if (!$this->inverse) {
                // single item passes regex test, only unset if not inverse filter
                unset($data[$key]);
              }
            } elseif ($this->inverse) {
              // single item fails regex test, only unset if inverse filter
              unset($data[$key]);
            }
          } else {
            if ($this->filter == $key) {
              if (!$this->inverse) {
                // single item passes strict test, only unset if not inverse filter
                unset($data[$key]);
              }
            } elseif ($this->inverse) {
              // single item fails sgrtict test, only unset if inverse filter
              unset($data[$key]);
            }
          }
        }
      }
    }
  }

  private function _doFilterValue(& $data)
  {
    if ($this->isDataContainer($data)) {
      $data = $this->val($data, true);
    }

    if (!is_array($data)) {
      // validate single value
      if ($this->regex) {
        if (preg_match($this->filter, $data)) {
          // single value regex comparison
          if (!$this->inverse) {
            // not inverse, unset if true
            unset($data);
          }
        } elseif ($this->inverse) {
          // we failed regex test, only unset if inverse
          unset($data);
        }
      } else {
        // strict comparison
        if ($this->filter == $data) {
          if (!$this->inverse) {
            // not inverse, unset if true
            unset($data);
          }
        } elseif ($this->inverse) {
          // we failed strict comparison, only unset if inverse
          unset($data);
        }
      }
    } else {
      // validate associative array
      foreach ($data as $key => & $value) {
        if (is_array($value)) {
          // array item is array
          if ($this->recursive) {
            // only continue if recursive filter
            $this->_doFilterValue($value);
          }
        } else {
          // array item is single value
          if ($this->regex) {
            if (preg_match($this->filter, $value)) {
              if (!$this->inverse) {
                // single item passes regex test, only unset if not inverse filter
                unset($data[$key]);
              }
            } elseif ($this->inverse) {
              // single item fails regex test, only unset if inverse filter
              unset($data[$key]);
            }
          } else {
            if ($this->filter == $value) {
              if (!$this->inverse) {
                // single item passes strict test, only unset if not inverse filter
                unset($data[$key]);
              }
            } elseif ($this->inverse) {
              // single item fails sgrtict test, only unset if inverse filter
              unset($data[$key]);
            }
          }
        }
      }
    }
  }
}
