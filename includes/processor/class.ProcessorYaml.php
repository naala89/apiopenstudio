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

include_once(Config::$dirIncludes . 'processor/class.ProcessorResource.php');
include_once(Config::$dirIncludes . 'spyc/spyc.php');

class ProcessorYaml extends ProcessorResource
{
  protected $details = array(
    'name' => 'Yaml',
    'description' => 'Create a custom API resource for your organisation.',
    'menu' => 'basic',
    'input' => array(
      'yaml' => array(
        'description' => 'The yaml string.',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'method' => array(
        'description' => 'The http delivery method.',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'resource' => array(
        'description' => 'The rest query resource (aka noun).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'action' => array(
        'description' => 'The rest query action (aka verb).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      )
    )
  );

  public function process()
  {
    Debug::message('ProcessorYaml');
    $this->validateRequired();

    switch ($this->request->method) {
      case 'post':
        $result = $this->_yamlIn();
        break;
      case 'get':
        $result = $this->_yamlOut();
        break;
      case 'delete':
        $result = $this->_yamlDelete();
        break;
      default:
        throw new ApiException('unknown method', -1, $this->id);
        break;
    }

    return $result;
  }

  /**
   * Create a resource from YAML.
   * The Yaml is either post string 'yaml', or file 'yaml'.
   * File takes precedence over the string if both present.
   */
  private function _yamlIn()
  {
    $yaml = '';
    if (!empty($_FILES['yaml'])) {
      $yaml = Spyc::YAMLLoad($_FILES['yaml']['tmp_name']);
    } else {
      $yaml = urldecode($this->getVar($this->meta->yaml));
      $yaml = Spyc::YAMLLoadString($yaml);
    }
    if (empty($yaml)) {
      throw new ApiException('invalid or no yaml supplied', -1, $this->id, 417);
    }

    Debug::variable($yaml, '$yaml');
    $clientId = $this->request->client;
    $method = $yaml['method'];
    $resource = strtolower($yaml['uri']['resource']) . strtolower($yaml['uri']['action']);
    $meta = json_encode(array(
      'validation' => $yaml['validation'],
      'process' => $yaml['process'],
      'output' => $yaml['output']
    ));
    $ttl = $yaml['ttl'];

    return $this->insertResource($clientId, $method, $resource, $meta, $ttl);
  }

  private function _yamlOut()
  {
    $clientId = $this->request->client;
    $method = $this->getVar($this->meta->method);
    if (empty($method)) {
      throw new ApiException('missing get var method', -1, $this->id, 400);
    }
    $resource = $this->getVar($this->meta->resource);
    if (empty($resource)) {
      throw new ApiException('missing get var resource', -1, $this->id, 400);
    }
    $action = $this->getVar($this->meta->action);
    if (empty($action)) {
      throw new ApiException('missing get var action', -1, $this->id, 400);
    }
    $resource = strtolower($resource) . strtolower($action);

    $columns = $this->fetchResource($clientId, $method, $resource);
    $array = json_decode($columns['meta'], TRUE);
    $array['uri'] = array(
      'resource' => $resource,
      'action' => $action
    );
    $array['method'] = $method;
    $array['ttl'] = $columns['ttl'];

    return Spyc::YAMLDump($array);
  }

  private function _yamlDelete()
  {
    $clientId = $this->request->client;
    $method = $this->getVar($this->meta->method);
    if (empty($method)) {
      throw new ApiException('missing get var method', -1, $this->id, 400);
    }
    $resource = $this->getVar($this->meta->resource);
    if (empty($resource)) {
      throw new ApiException('missing get var resource', -1, $this->id, 400);
    }
    $action = $this->getVar($this->meta->action);
    if (empty($action)) {
      throw new ApiException('missing get var action', -1, $this->id, 400);
    }
    $resource = strtolower($resource) . strtolower($action);

    return $this->deleteResource($clientId, $method, $resource);
  }
}
