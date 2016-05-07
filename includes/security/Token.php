<?php

/**
 * Provide token authentication based on token
 *
 * Meta:
 *    {
 *      "type": "token",
 *      "meta": {
 *        "id":<integer>,
 *        "token": <processor|string>
 *      }
 *    }
 */

namespace Datagator\Security;
use Datagator\Core;
use Datagator\Db;
use Datagator\Processor;

class Token extends Processor\ProcessorBase {
  protected $role = false;
  protected $details = array(
    'machineName' => 'token',
    'name' => 'Token',
    'description' => 'Validate the request, requiring the consumer to have a valid token.',
    'menu' => 'Security',
    'client' => 'All',
    'application' => 'All',
    'input' => array(
      'token' => array(
        'description' => 'The consumers token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor')
      )
    ),
  );

  /**
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator Token', 4);

    $token = $this->val($this->meta->token);
    if (empty($token)) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    $db = $this->getDb();
    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findBytoken($token);
    if (empty($user->getUid()) || $user->getActive() == 0) {
      throw new Core\ApiException('permission denied', 4, -1, 401);
    }

    return true;
  }
}
