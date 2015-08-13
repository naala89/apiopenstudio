<?php

/**
 * Container for data for an role row.
 */

namespace Datagator\Db;
use Datagator\Core;

class Role
{
  protected $rid;
  protected $name;

  /**
   * @param null $rid
   * @param null $name
   */
  public function __construct($rid=NULL, $name=NULL)
  {
    $this->rid = $rid;
    $this->name = $name;
  }

  /**
   * @return int rid
   */
  public function getRid()
  {
    return $this->rid;
  }

  /**
   * @param $val
   */
  public function setRid($val)
  {
    $this->rid = $val;
  }

  /**
   * @return int name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param $val
   */
  public function setName($val)
  {
    $this->name = $val;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    Core\Debug::variable($this->rid, 'rid');
    Core\Debug::variable($this->name, 'name');
  }
}
