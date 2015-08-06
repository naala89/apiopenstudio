<?php

/**
 * Store the Drupal login token
 *
 * Takes the input of a login event and stores the token if successful
 *
 * METADATA
 * {
 *  "type":"tokenStoreDrupal",
 *  "meta": {
 *    "id":<integer>,
 *    "source":<processor>
 *    "staleTime":<string> (defaults to "+1 day")
 *  }
 * }
 */

namespace Datagator\Processors;
use Datagator\Core;

class LoginStoreDrupal extends \Processor
{
  private $user;

  /**
   * @param $meta
   * @param $request
   */
  public function ProcessorLoginStoreDrupal ($meta, $request)
  {
    $this->user = new User($request->db);
    parent::Processor($meta, $request);
  }

  /**
   * @return array|\Error|mixed|Object
   * @throws \ApiException
   */
  public function process()
  {
    Debug::variable($this->meta, 'ProcessorLoginStoreDrupal', 4);

    $source = $this->getVar($this->meta->source);
    $source = json_decode($source);
    if (empty($source->token) || empty($source->user) || empty($source->user->uid)) {
      throw new \Datagator\includes\ApiException('login failed, no token received', 3, $this->id, 419);
    }

    $token = $source->token;
    $sessionName = $source->session_name;
    $sessionId = $source->sessid;
    $externalId = $source->user->uid;
    $roles = $source->user->roles;
    $staleTime = !empty($source->staleTime) ? $source->staleTime : Config::$tokenLife;
    $client = $this->request->client;

    $this->user->deleteUser($client, $externalId);
    $this->user->insertUser($client, $externalId, $roles, $token, $sessionName, $sessionId, $staleTime);

    return $source;
  }
}
