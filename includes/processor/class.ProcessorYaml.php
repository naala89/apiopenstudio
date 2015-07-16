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

  protected $required = array();

  public function process()
  {
    Debug::message('ProcessorYaml');
    Debug::message(Config::$dirIncludes . 'Yaml/Yaml.php');
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
      $yaml = $_FILES['yaml'];
    } else {
      $yaml = $this->getVar($this->meta->yaml);
    }
    if (empty($yaml)) {
      throw new ApiException('no yaml supplied', -1, $this->id);
    }

    // @TODO: try/catch?
    $yaml = Spyc::YAMLLoad($yaml);
    $clientId = $this->request->client;
    $method = $yaml['method'];
    $resource = strtolower($yaml['resource']) . strtolower($yaml['action']);
    $meta = array_merge($yaml['Validation'], $yaml['process'], $yaml['output']);
    $ttl = $yaml['ttl'];

    return $this->insertResource($clientId, $method, $resource, $meta, $ttl);
  }

  private function _yamlOut()
  {
  }

  private function _yamlDelete()
  {

    $method = $this->getVar('method');
    $noun = $this->getVar('resource');
    $verb = $this->getVar('action');
    $clientId = $this->request->client;

    return $this->deleteResource(
      $clientId,
      $method,
      $noun,
      $verb
    );
  }
}
