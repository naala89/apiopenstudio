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

namespace Datagator\Validator;
use Datagator\Core;
use Datagator\Processor;

class Token extends Processor\ProcessorBase {
  protected $role = false;
  protected $required = array('token');
  protected $details = array(
    'name' => 'Token',
    'description' => 'Validate the request, the user having a valid token.',
    'menu' => 'Validator',
    'client' => 'All',
    'application' => 'All',
    'input' => array(),
  );

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator TokenConsumer', 4);

    $this->request->user->findByToken($this->getVar($this->meta->token));
    if (!$this->request->user->exists()
      || !$this->request->user->isActive()
      || ($this->role && !$this->request->user->hasRole($this->request->appId, $this->role))) {
      throw new Core\ApiException('permission denied', 4, $this->id, 401);
    }

    return TRUE;
  }
}
