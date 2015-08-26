<?php

/**
 * Provide token authentication based on token in DB
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

  protected $required = array('token');
  protected $details = array(
    'name' => 'Token',
    'description' => 'Validate the request, based on a token.',
    'menu' => 'validator',
    'client' => '2',
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
    Core\Debug::message('Validator Token', 4);
    $this->validateRequired();

    $appId = $this->request->appId;
    $token = $this->getVar($this->meta->token);
    $userObj = new Core\User($this->request->db);

    $user = $userObj->findByToken($token);
    if (empty($user->getUid()) || !$user->getActive()) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }
    if (!$userObj->hasRole($appId, 'consumer')) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    return TRUE;
  }
}
