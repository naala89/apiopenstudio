<?php

/**
 * Clear system cache
 */

namespace Datagator\Processor;
use Datagator\Config;
use Datagator\Core;

class SystemCache extends ProcessorBase
{
  protected $required = array('operation');
  protected $details = array(
    'name' => 'System (Cache)',
    'description' => 'Perform cache operations on the system.',
    'menu' => 'system',
    'application' => 'System',
    'input' => array(
      'operation' => array(
        'description' => 'The cache operation to perform.',
        'cardinality' => array(1, 1),
        'accepts' => array('"clear"')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor SystemCache', 4);
    $this->validateRequired();
    parent::process();

    $op = strtolower($this->getVar($this->meta->operation));

    if (method_exists($this, $op)) {
      $result = $this->$op();
    }

    return $result;
  }

  private function clear()
  {
    $cache = new Core\Cache(Config::$cache);
    $result = $cache->clear();
    if (!$result) {
      throw new Core\ApiExceptionn('could not clear the cache', 0, $this->id, 500);
    }
    return $result;
  }
}
