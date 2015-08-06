<?php

namespace Datagator\DB\Table;

class Users extends Base
{
  protected $tableName = 'users';
  protected $cols = array(
    'uid',
    'cid',
    'active',
    'email',
    'password',
    'token',
    'stale_time'
  );
  protected $pk = 'uid';
}
