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
use Spyc;

class ResourceYaml extends ProcessorBase
{
  protected $details = array(
    'name' => 'Resource (Yaml)',
    'description' => 'Create or fetch a custom API resource for the application in YAML form.',
    'menu' => 'resource',
    'client' => 'all',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'identifierNoun' => array(
        'description' => 'The noun identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'identifierVerb' => array(
        'description' => 'The verb identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'yaml' => array(
        'description' => 'The yaml string. This can be a form POST or a urlencoded GET var (this is only used if you want to create or update a resource).',
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

    switch ($this->request->method) {
      case 'post':
        $result = $this->_save();
        break;
      case 'get':
        $result = $this->_fetch();
        break;
      case 'delete':
        $result = $this->_delete();
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
   * @return bool
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processors\ApiException
   */
  private function _save()
  {
    $yaml = '';
    if (!empty($_FILES['yaml'])) {
      $yaml = Spyc::YAMLLoad($_FILES['yaml']['tmp_name']);
    } else {
      $yaml = urldecode($this->getVar($this->meta->yaml));
      $yaml = Spyc::YAMLLoadString($yaml);
    }
    if (empty($yaml)) {
      throw new Core\ApiException('invalid or no yaml supplied', -1, $this->id, 417);
    }

    Core\Debug::variable($yaml, '$yaml');
    $appId = $this->request->appId;
    $method = $yaml['method'];
    $identifier = strtolower($yaml['uri']['resource']) . strtolower($yaml['uri']['action']);
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
      $resource->setMethod($appId);
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
   * @return string
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processors\ApiException
   */
  private function _fetch()
  {
    $appId = $this->request->appId;
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

    return Spyc::YAMLDump($result);
  }

  /**
   * Delete a resource.
   *
   * Uses inputs:
   *  method
   *  noun
   *  verb.
   *
   * @return bool
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processors\ApiException
   */
  private function _delete()
  {
    $appId = $this->request->appId;
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
