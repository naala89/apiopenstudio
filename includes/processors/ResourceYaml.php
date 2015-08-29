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
    'uri', 'application', 'method', 'ttl', 'validation', 'process'
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
      'appid' => array(
        'description' => 'The application ID the resource is associated with (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('integer')
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
    Core\Debug::variable($this->request->method, '$this->request->method');
    Core\Debug::message('Processor ResourceYaml');
    $this->validateRequired();

    // get user by token to check correct access to application and role
    $user = new Core\User($this->request->db);
    $user->findByToken($this->request->vars['token']);

    switch ($this->request->method) {
      case 'post':
        $result = $this->_save($user);
        break;
      case 'get':
        $result = $this->_fetch($user);
        break;
      case 'delete':
        $result = $this->_delete($user);
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
   * @param \Datagator\Core\User $user
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _save(Core\User $user)
  {
    // extract yaml
    $yaml = array();
    if (!empty($_FILES['yaml'])) {
      $yaml = \Spyc::YAMLLoad($_FILES['yaml']['tmp_name']);
    } else {
      $yaml = urldecode($this->getVar($this->meta->yaml));
      $yaml = \Spyc::YAMLLoadString($yaml);
    }
    if (empty($yaml)) {
      throw new Core\ApiException('invalid or no yaml supplied', -1, $this->id, 417);
    }
    Core\Debug::variable($yaml, 'YAML', 4);

    // check required elements in yaml exist
    foreach ($this->requiredElements as $requiredElement) {
      if (empty($yaml[$requiredElement])) {
        throw new Core\ApiException("missing $requiredElement in yaml", -1, $this->id, 417);
      }
    }

    // check application defined in yaml exists
    $mapper = new Db\ApplicationMapper($this->request->db);
    $application = $mapper->findByName($yaml['application']);
    if (empty($appId = $application->getAppId())) {
      throw new Core\ApiException('invalid application', -1, $this->id, 401);
    }

    // check user has correct dev permission for application in yaml
    if (!$user->hasRole($appId, 'developer')) {
      throw new Core\ApiException("permission denied", -1, $this->id, 401);
    }

    $method = $yaml['method'];
    $identifier = strtolower($yaml['uri']['noun']) . strtolower($yaml['uri']['verb']);

    $meta = json_encode(array(
      'validation' => $yaml['validation'],
      'process' => $yaml['process']
    ));
    if (!empty($yaml['output'])) {
      $meta['output'] = $yaml['output'];
    }
    $ttl = $yaml['ttl'];

    $resource = new Db\Resource(null, $appId, $method, $identifier, $meta, $ttl);
    $mapper = new Db\ResourceMapper($this->request->db);
    return $mapper->save($resource);
  }

  /**
   * Fetch a resource in YAML form.
   *
   * Uses inputs:
   *  method
   *  appid
   *  noun
   *  verb.
   *
   * @param \Datagator\Core\User $user
   * @return string
   * @throws \Datagator\Core\ApiException
   */
  private function _fetch(Core\User $user)
  {
    if (empty($appId = $this->request->vars['appid'])) {
      throw new Core\ApiException('missing appid parameter', -1, $this->id, 400);
    }
    if (!$user->hasRole($appId, 'developer')) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }
    if (empty($method = $this->request->vars['method'])) {
      throw new Core\ApiException('missing method parameter', -1, $this->id, 400);
    }
    if (empty($noun = $this->request->vars['noun'])) {
      throw new Core\ApiException('missing noun parameter', -1, $this->id, 400);
    }
    if (empty($verb = $this->request->vars['verb'])) {
      throw new Core\ApiException('missing verb parameter', -1, $this->id, 400);
    }
    $identifier = strtolower($noun) . strtolower($verb);

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    if (empty($resource->getId())) {
      throw new Core\ApiException('Resource not found', -1, $this->id, 200);
    }

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
   * GET vars:
   *  method
   *  appid
   *  noun
   *  verb
   *
   * @param \Datagator\Core\User $user
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  private function _delete(Core\User $user)
  {
    if (empty($appId = $this->request->vars['appid'])) {
      throw new Core\ApiException('missing appid parameter', -1, $this->id, 400);
    }
    if (!$user->hasRole($appId, 'developer')) {
      throw new Core\ApiException('permission denied', -1, $this->id, 401);
    }
    if (empty($method = $this->request->vars['method'])) {
      throw new Core\ApiException('missing method parameter', -1, $this->id, 400);
    }
    if (empty($noun = $this->request->vars['noun'])) {
      throw new Core\ApiException('missing noun parameter', -1, $this->id, 400);
    }
    if (empty($verb = $this->request->vars['verb'])) {
      throw new Core\ApiException('missing verb parameter', -1, $this->id, 400);
    }
    $identifier = strtolower($noun) . strtolower($verb);

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    return $mapper->delete($resource);
  }
}
