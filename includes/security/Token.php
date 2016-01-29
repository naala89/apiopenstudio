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
        'cardinality' => array(1),
        'accepts' => array('processor')
      )
    ),
  );

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator TokenConsumer', 4);

    $this->request->user->findByToken($this->val($this->meta->token));
    if (!$this->request->user->exists()
      || !$this->request->user->isActive()
      || ($this->role && !$this->request->user->hasRole($this->request->appId, $this->role))) {
      throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }

    return TRUE;
  }
}
