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

namespace Datagator\Validators;
use Datagator\Core;
use Datagator\Processors;

class Token extends Processors\ProcessorBase {
  protected $role = false;
  protected $required = array('token');
  public $details = array(
    'name' => 'Token',
    'description' => 'Validate the request, based on a token.',
    'menu' => 'validator',
    'client' => 'All',
    'input' => array(
      'token' => array(
        'description' => 'The token.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal'),
      ),
    ),
  );

  /**
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function process() {
    Core\Debug::variable($this->meta, 'Validator TokenConsumer', 4);
    $this->validateRequired();

    $this->request->user->findByToken($this->getVar($this->meta->token));
    if (!$this->request->user->exists()
      || !$this->request->user->isActive()
      || ($this->role && !$this->request->user->hasRole($this->request->appId, $this->role))) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    return TRUE;
  }
}
