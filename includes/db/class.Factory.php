<?php

namespace Datagator\DB;

class Factory
{
  public static function create($db, $tableName)
  {
    return new $tableName($db);
  }
}
