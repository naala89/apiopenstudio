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
   * @return bool
   * @throws \Datagator\Security\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator TokenConsumer', 4);

    $userMapper = new UserMapper($this->request->db);
    $token = $this->val($this->meta->token);

    $user = $userMapper->findBytoken($token);
    if (empty($user->getUid()) || $user->getActive() == 0) {
      throw new ApiException('permission denied', 4, -1, 401);
    }

    return array('result' => true);
  }
}
