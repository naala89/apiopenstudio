<?php

include_once(__DIR__ . '/class.ProcessorSystem.php');
include_once(Config::$dirIncludes . 'class.Cache.php');

class ProcessorSystemCache extends ProcessorSystem
{
  public $displayFrontend = FALSE;
  protected $required = array('operation');

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorSystemCache', 4);
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
    $cache = new Cache(Config::$cache);
    $result = $cache->clear();
    if (!$result) {
      throw new ApiException('could not clear the cache', -1, $this->id, 500);
    }
    return $result;
  }
}
