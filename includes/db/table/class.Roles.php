<?php

namespace Datagator\DB\Table;

class Roles extends Base
{
  protected $tableName = 'roles';
  protected $cols = array(
    'rid',
    'cid',
    'role'
  );
  protected $pk = 'rid';
}
