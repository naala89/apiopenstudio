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
   * @param $rid
   */
  public function setRid($rid)
  {
    $this->rid = $rid;
  }

  /**
   * @return int name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Display contents for debugging
   */
  public function debug()
  {
    return array(
      'rid' => $this->rid,
      'name' => $this->name,
    );
  }
}
