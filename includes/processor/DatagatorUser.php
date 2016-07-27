<?php

/**
 * User table CRUD.
 */

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db;

class DatagatorUser extends ProcessorEntity
{
  protected $details = array(
    'name' => 'Datagator User',
    'description' => 'CRUD operations for Datagator users.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => array(
      'username' => array(
        'description' => 'The username of the user.',
        'cardinality' => array(1, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'active' => array(
        'description' => 'The status of the user account. Only used for creating user',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
      'email' => array(
        'description' => 'The email of the user. Only used for creating user',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'honorific' => array(
        'description' => 'The honorific of the user. Only used for creating user',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'nameFirst' => array(
        'description' => 'The first name of the user. Only used for creating user',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'nameLast' => array(
        'description' => 'The last name of the user. Only used for creating user',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'company' => array(
        'description' => 'The company of the user Only used for creating user',
        'cardinality' => array(0, 1),
        'literalAllowed' => false,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor DatagatorUser', 4);

    $username = $this->val('username');
    $method = $this->request->method;
    $db = $this->getDb();

    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findByUsername($username);

    switch ($method) {
      case 'post':
        $user->setUsername($username);
        $active = $this->val('active');
        $active = !empty($active) ? $active : 1;
        $user->setActive($active);
        $user->setEmail($this->val('email'));
        $user->setHonorific($this->val('honorific'));
        $user->setNameFirst($this->val('nameFirst'));
        $user->setNameLast($this->val('nameLast'));
        $user->setCompany($this->val('company'));
        return $userMapper->save($user);
        break;
      case 'get':
        return $user->debug();
        break;
      case 'delete':
        return $userMapper->delete($user);
        break;
      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }
}
