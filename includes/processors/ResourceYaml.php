<?php

/**
 * Resource import and export.@global
 * Allowed inputs are yaml files or yaml strings.
 *
 * METADATA
 * {
 *    "type":"object",
 *    "meta":{
 *      "id": <mixed>,
 *      "yaml": <string>,
 *      "method": <"get"|"post">,
 *      "resource": <mixed>,
 *      "action": <mixed>
 *    }
 *  }
 */

namespace Datagator\Processors;
use Datagator\Core;
use Datagator\Db;

class ResourceYaml extends ProcessorBase
{
  protected $requiredElements = array(
    'uri', 'application', 'appId', 'method', 'ttl', 'validation', 'process'
  );
  protected $details = array(
    'name' => 'Resource (Yaml)',
    'description' => 'Create or fetch a custom API resource for the application in YAML form.',
    'menu' => 'Resource',
    'application' => 'All',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'noun' => array(
        'description' => 'The noun identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'verb' => array(
        'description' => 'The verb identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'yaml' => array(
        'description' => 'The yaml string or file. This can be a form POST or a urlencoded GET var (this is only used if you are creating or updating a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string', 'file')
      )
    )
  );

  /**
   * @return bool|string
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processors\ApiException
   */
  public function process()
  {
    Core\Debug::message('Processor ResourceYaml');
    $this->validateRequired();

    // get uid from token to check correct access to application and role
    $token = $this->request->vars['token'];
    $mapper = new Db\UserMapper($this->request->db);
    $user = $mapper->findBytoken($token);
    $uid = $user->getUid();

    switch ($this->request->method) {
      case 'post':
        $result = $this->_save($uid);
        break;
      case 'get':
        $result = $this->_fetch($uid);
        break;
      case 'delete':
        $result = $this->_delete($uid);
        break;
      default:
        throw new Core\ApiException('unknown method', -1, $this->id);
        break;
    }

    return $result;
  }

  /**
   * Create or update a resource from YAML.
   * The Yaml is either post string 'yaml', or file 'yaml'.
   * File takes precedence over the string if both present.
   *
   * @param $uid
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _save($uid)
  {
    $yaml = '';
    if (!empty($_FILES['yaml'])) {
      $yaml = \Spyc::YAMLLoad($_FILES['yaml']['tmp_name']);
    } else {
      $yaml = urldecode($this->getVar($this->meta->yaml));
      $yaml = \Spyc::YAMLLoadString($yaml);
    }
    if (empty($yaml)) {
      throw new Core\ApiException('invalid or no yaml supplied', -1, $this->id, 417);
    }
    foreach ($this->requiredElements as $requiredElement) {
      if (empty($yaml[$requiredElement])) {
        throw new Core\ApiException("missing $requiredElement in yaml", -1, $this->id, 417);
      }
    }

    Core\Debug::variable($yaml, '$yaml');
    $appId = $this->request->appId;

    // check user has role developer for the application and
    // check the app name in the yaml matches the appid in the request
    $userRoleMapper = new Db\UserRoleMapper($this->request->db);
    $userRole = $userRoleMapper->findByUidAppNameRole($uid, $yaml['application'], 'developer');
    if (empty($userRole->getId()) || $userRole->getAppId() != $appId) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    $method = $yaml['method'];
    $identifier = strtolower($yaml['uri']['noun']) . strtolower($yaml['uri']['verb']);
    $meta = json_encode(array(
      'validation' => $yaml['validation'],
      'process' => $yaml['process'],
      'output' => $yaml['output']
    ));
    $ttl = $yaml['ttl'];

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    if ($resource->getId() == NULL) {
      $resource->setAppId($appId);
      $resource->setMethod($method);
      $resource->setIdentifier($identifier);
    }
    $resource->setMeta($meta);
    $resource->setTtl($ttl);

    return $mapper->save($resource);
  }

  /**
   * Fetch a resource in YAML form.
   *
   * Uses inputs:
   *  method
   *  noun
   *  verb.
   *
   * @param $uid
   * @return string
   * @throws \Datagator\Core\ApiException
   */
  private function _fetch($uid)
  {
    $appId = $this->request->appId;

    // check user has role developer for the application and
    $userRoleMapper = new Db\UserRoleMapper($this->request->db);
    $userRole = $userRoleMapper->findByUidAppidRole($uid, $appId, 'developer');
    if (empty($userRole->getId()) || $userRole->getAppId() != $appId) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    $method = $this->getVar($this->meta->method);
    if (empty($method)) {
      throw new Core\ApiException('missing method parameter', -1, $this->id, 400);
    }
    $noun = $this->getVar($this->meta->identifierNoun);
    if (empty($identifier)) {
      throw new Core\ApiException('missing noun identifier parameter', -1, $this->id, 400);
    }
    $verb = $this->getVar($this->meta->identifierVerb);
    if (empty($identifier)) {
      throw new Core\ApiException('missing verb identifier parameter', -1, $this->id, 400);
    }
    $identifier = strtolower($noun) . strtolower($verb);

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);

    $result = json_decode($resource->getMeta(), TRUE);
    $result['uri'] = array(
      'noun' => $noun,
      'verb' => $verb
    );
    $result['method'] = $resource->getMethod();
    $result['ttl'] = $resource->getTtl();

    return \Spyc::YAMLDump($result);
  }

  /**
   * Delete a resource.
   *
   * Uses inputs:
   *  method
   *  noun
   *  verb.
   *
   * @param $uid
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _delete($uid)
  {
    $appId = $this->request->appId;

    // check user has role developer for the application and
    $userRoleMapper = new Db\UserRoleMapper($this->request->db);
    $userRole = $userRoleMapper->findByUidAppidRole($uid, $appId, 'developer');
    if (empty($userRole->getId()) || $userRole->getAppId() != $appId) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }

    $method = $this->getVar($this->meta->method);
    if (empty($method)) {
      throw new Core\ApiException('missing method parameter', -1, $this->id, 400);
    }
    $noun = $this->getVar($this->meta->identifierNoun);
    if (empty($identifier)) {
      throw new Core\ApiException('missing noun identifier parameter', -1, $this->id, 400);
    }
    $verb = $this->getVar($this->meta->identifierVerb);
    if (empty($identifier)) {
      throw new Core\ApiException('missing verb identifier parameter', -1, $this->id, 400);
    }
    $identifier = strtolower($noun) . strtolower($verb);

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    return $mapper->delete($resource);
  }
}
