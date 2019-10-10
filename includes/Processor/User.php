<?php

/**
 * User CRUD.
 * 
 * @TODO: All CRUD operations should have separate processors for each operation.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class User extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'User',
    'machineName' => 'user',
    'description' => 'CRUD operations for users.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'username' => [
        'description' => 'The username of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'active' => [
        'description' => 'The status of the user account. Only used for creating user',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['boolean'],
        'limitValues' => [],
        'default' => true
      ],
      'email' => [
        'description' => 'The email of the user. Only used for creating user',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'honorific' => [
        'description' => 'The honorific of the user. Only used for creating user',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'nameFirst' => [
        'description' => 'The first name of the user. Only used for creating user',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'nameLast' => [
        'description' => 'The last name of the user. Only used for creating user',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'company' => [
        'description' => 'The company of the user Only used for creating user',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $username = $this->val('username', TRUE);
    $method = $this->request->getMethod();

    switch ($method) {
      case 'post':
        // $user->setUsername($userMapper, $username);
        // $active = $this->val('active');
        // $active = !empty($active) ? $active : 1;
        // $user->setActive($active);
        // $user->setEmail($this->val('email'));
        // $user->setHonorific($this->val('honorific'));
        // $user->setNameFirst($this->val('nameFirst'));
        // $user->setNameLast($this->val('nameLast'));
        // $user->setCompany($this->val('company'));
        // return $userMapper->save($user);
        break;
      case 'get':
        return $this->read($username);
        break;
      case 'delete':
        return $this->delete($username);
        break;
      default:
        throw new Core\ApiException('Invalid action', 3, $this->id);
        break;
    }
  }

  /**
   * Fetch a user.
   * 
   * @param string $username
   *   Username.
   *
   * @return void
   */
  protected function read($username) {
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUsername($username);
    if (empty($user->getUid())) {
      throw new Core\ApiException('User does not exist', 6, $this->id, 400);
    }
    return $user->dump();
  }
}
