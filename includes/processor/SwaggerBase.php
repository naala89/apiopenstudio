<?php

/**
 * Resource import and export.
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

namespace Datagator\Processor;
use Datagator\Core;

class SwaggerBase extends ProcessorBase
{
  protected $details = array(
    'name' => 'Import Swagger',
    'description' => 'Create a custom API resource using a Swagger YAML document.',
    'menu' => 'Resource',
    'application' => 'All',
    'input' => array(
      'resource' => array(
        'description' => 'The resource string or file. This can be an attached file or a urlencoded GET var.',
        'cardinality' => array(1, 1),
        'accepts' => array('string', 'file')
      )
    )
  );

  private $paramCount;

  public function process()
  {
    $this->paramCount = 0;
    $resources = array();
    $swagger = $this->_importData();

    if (empty($swagger['paths'])) {
      throw new Core\ApiException('Missing paths element in swagger YAML', 1);
    }

    foreach ($swagger['paths'] as $path => $methods) {
      $pathParts = explode('/', trim($path, '/'));

      if (sizeof($pathParts) < 2) {
        throw new Core\ApiException('invalid path (must be at least noun/verb): ' . $path, 1);
      }

      $uriParams = array();
      if (sizeof($pathParts) > 2) {
        $uriParams = $this->_extractUriParams(array_slice($pathParts, 2));
      }
      $noun = $pathParts[0];
      $verb = $pathParts[1];

      foreach ($methods as $method => $definition) {
        $requestVars = $this->_extractParameters($definition['parameters'], $method);

        $resource = array();
        $resource['name'] = !empty($definition['operationId']) ? $definition['operationId'] : 'noName';
        $resource['description'] = !empty($definition['description']) ? $definition['description'] : 'noDescription';
        $resource['uri']['noun'] = $noun;
        $resource['uri']['verb'] = $verb;
        $resource['method'] = $method;
        $resource['process'] = '';
        $resource['parameters'] = array();
        $resource['parameters'] = array_merge($resource['parameters'], $uriParams);
        $resource['parameters'] = array_merge($resource['parameters'], $requestVars);

        $resources[] = $resource;
      }
    }

    return $this->_exportData($resources);
  }

  /**
   * @return array|string
   * @throws \Datagator\Core\ApiException
   */
  protected function _importData()
  {
    // extract yaml
    $yaml = '';
    if (sizeof($_FILES) > 1) {
      throw new Core\ApiException('multiple files received', 3);
    }
    if (!empty($_FILES)) {
      foreach ($_FILES as $file) {
        $yaml = \Spyc::YAMLLoad($file['tmp_name']);
      }
    } else {
      if (empty($this->request->vars['yaml'])) {
        throw new Core\ApiException('no yaml supplied', 6, $this->id, 417);
      }
      $yaml = $this->val($this->meta->yaml);
      $yaml = urldecode($yaml);
      $yaml = \Spyc::YAMLLoadString($yaml);
    }
    return $yaml;
  }

  protected function _exportData(array $array) {}

  /**
   * @param $uriParams
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  protected function _extractUriParams($uriParams)
  {
    $result = array();
    foreach ($uriParams as $key => $val) {
      if (!preg_match("/^\{[a-z0-9_-]*\}$/i", $val)) {
        throw new Core\ApiException("invalid URI element: $val", 1);
      }
      $result[] = array(
        'processor' => 'varUri',
        'meta' => array(
          'id' => $this->paramCount++,
          'index' => $key)
      );
    }
    return $result;
  }

  /**
   * @param $parameters
   * @param $method
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  protected function _extractParameters($parameters, $method)
  {
    $result = array();
    foreach ($parameters as $parameter) {
      $p = array();
      $parameterCount = 1;
      switch ($parameter['in']) {
        case 'query':
          $p['processor'] = $method == 'get' ? 'varGet' : 'varPost';
          $p['meta']['id'] = $this->paramCount++;
          $p['meta']['name'] = $parameter['name'];
          break;
        case 'body':
          $p['processor'] = 'varBody';
          $p['meta']['id'] = $this->paramCount++;
          break;
      }
      // strongly typed
      if (!empty($parameter['items']['type'])) {
        Core\Debug::variable($parameter['items']['type'], 'strongly typed');
        $p = array(
          'meta' => array(
            'id' => $this->paramCount++,
            'value' => $p
          )
        );
        switch ($parameter['items']['type']) {
          case 'boolean':
            $p['processor'] = 'varBool';
            break;
          case 'float':
            $p['processor'] = 'varFloat';
            break;
          case 'integer':
            $p['processor'] = 'varInt';
            break;
          case 'number':
            $p['processor'] = 'varNum';
            break;
          case 'string':
            $p['processor'] = 'varStr';
            break;
          default:
            throw new Core\ApiException('unknown type: ' . $parameter['items']['type'], 1);
            break;
        }
      }
      $result[] = $p;
    }
    return $result;
  }
}
