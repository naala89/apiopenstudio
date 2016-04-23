<?php

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db;

abstract class ResourceBase extends ProcessorBase
{
  protected $requiredElements = array('uri', 'application', 'method', 'process');
  protected $details = array();

  /**
   * @return bool|string
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processor\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ResourceBase', 4);

    switch ($this->request->method) {
      case 'post':
        $result = $this->save();
        break;
      case 'get':
        $result = $this->fetch();
        break;
      case 'delete':
        $result = $this->delete();
        break;
      default:
        throw new Core\ApiException('unknown method', 3, $this->id);
        break;
    }

    return $result;
  }

  /**
   * @return mixed
   */
  abstract protected function _importData();

  /**
   * @param array $array
   * @return mixed
   */
  abstract protected function _exportData(array $array);

  /**
   * Create or update a resource from YAML.
   * The Yaml is either post string 'yaml', or file 'yaml'.
   * File takes precedence over the string if both present.
   *
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  protected function save()
  {
    $data = $this->_importData();
    Core\Debug::variable($data, 'data', 4);

    // check required elements in data exist
    foreach ($this->requiredElements as $requiredElement) {
      if (empty($data[$requiredElement])) {
        throw new Core\ApiException("missing $requiredElement in data", 6, $this->id, 417);
      }
    }

    // check application defined in data exists
    $mapper = new Db\ApplicationMapper($this->request->db);
    $application = $mapper->findByName($data['application']);
    if (empty($appId = $application->getAppId())) {
      throw new Core\ApiException('invalid application', 7, $this->id, 401);
    }

    // validation is not mandatory
    if (!empty($data['validation'])) {
      $meta['validation'] = $data['validation'];
    }

    // output is not mandatory
    if (!empty($data['output'])) {
      $meta['output'] = $data['output'];
    }

    $method = $data['method'];
    $identifier = strtolower($data['uri']['noun']) . strtolower($data['uri']['verb']);
    $meta = array();
    $meta['process'] =  $data['process'];
    $ttl = !empty($data['ttl']) ? $data['ttl'] : 0;

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    if (empty($resource->getId())) {
      $resource->setAppId($appId);
      $resource->setMethod($method);
      $resource->setIdentifier($identifier);
    }
    $resource->setMeta(json_encode($meta));
    $resource->setTtl($ttl);
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
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  protected function fetch()
  {
    if (empty($appId = $this->request->vars['appid'])) {
      throw new Core\ApiException('missing appid parameter', 3, $this->id, 400);
    }
    if (empty($method = $this->request->vars['method'])) {
      throw new Core\ApiException('missing method parameter', 1, $this->id, 400);
    }
    if (empty($noun = $this->request->vars['noun'])) {
      throw new Core\ApiException('missing noun parameter', 1, $this->id, 400);
    }
    if (empty($verb = $this->request->vars['verb'])) {
      throw new Core\ApiException('missing verb parameter', 1, $this->id, 400);
    }
    $identifier = strtolower($noun) . strtolower($verb);

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    if (empty($resource->getId())) {
      throw new Core\ApiException('Resource not found', 1, $this->id, 200);
    }

    $result = json_decode($resource->getMeta(), TRUE);
    $result['uri'] = array(
      'noun' => $noun,
      'verb' => $verb
    );
    $result['method'] = $resource->getMethod();
    $result['ttl'] = $resource->getTtl();

    return $this->_exportData($result);
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
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  protected function delete()
  {
    if (empty($appId = $this->request->vars['appid'])) {
      throw new Core\ApiException('missing appid parameter', 1, $this->id, 400);
    }
    if (empty($method = $this->request->vars['method'])) {
      throw new Core\ApiException('missing method parameter', 1, $this->id, 400);
    }
    if (empty($noun = $this->request->vars['noun'])) {
      throw new Core\ApiException('missing noun parameter', 1, $this->id, 400);
    }
    if (empty($verb = $this->request->vars['verb'])) {
      throw new Core\ApiException('missing verb parameter', 1, $this->id, 400);
    }
    $identifier = strtolower($noun) . strtolower($verb);

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    return $mapper->delete($resource);
  }
}
