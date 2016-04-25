<?php

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db;

abstract class ResourceBase extends ProcessorBase
{
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

    $this->_validateData($data);

    $method = $data['method'];
    $identifier = strtolower($data['uri']['noun']) . strtolower($data['uri']['verb']);
    $meta = array();
    $meta['process'] =  $data['process'];
    $ttl = !empty($data['ttl']) ? $data['ttl'] : 0;

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($this->request->appId, $method, $identifier);
    if (empty($resource->getId())) {
      $resource->setAppId($this->request->appId);
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
    $appId = $this->request->appId;
    $method = $this->val($this->meta->method);
    $noun = $this->val($this->meta->noun);
    $verb = $this->val($this->meta->verb);
    if (empty($appId)) {
      throw new Core\ApiException('missing application ID', 3, $this->id, 400);
    }
    if (empty($method)) {
      throw new Core\ApiException('missing method parameter', 1, $this->id, 400);
    }
    if (empty($noun)) {
      throw new Core\ApiException('missing noun parameter', 1, $this->id, 400);
    }
    if (empty($verb)) {
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
    $appId = $this->request->appId;
    $method = $this->val($this->meta->method);
    $noun = $this->val($this->meta->noun);
    $verb = $this->val($this->meta->verb);
    if (empty($appId)) {
      throw new Core\ApiException('missing application ID', 3, $this->id, 400);
    }
    if (empty($method)) {
      throw new Core\ApiException('missing method parameter', 1, $this->id, 400);
    }
    if (empty($noun)) {
      throw new Core\ApiException('missing noun parameter', 1, $this->id, 400);
    }
    if (empty($verb)) {
      throw new Core\ApiException('missing verb parameter', 1, $this->id, 400);
    }
    $identifier = strtolower($noun) . strtolower($verb);

    $mapper = new Db\ResourceMapper($this->request->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    return $mapper->delete($resource);
  }

  /**
   * Validate input data for well-formedness.
   * @param $data
   * @throws \Datagator\Core\ApiException
   */
  private function _validateData($data) {
    // check mandatory elements exists in data
    if (empty($data['uri'])) {
      throw new Core\ApiException("missing uri in new resource", 6, $this->id, 417);
    }
    if (empty($data['uri']['noun'])) {
      throw new Core\ApiException("missing uri/noun in new resource", 6, $this->id, 417);
    }
    if (empty($data['uri']['verb'])) {
      throw new Core\ApiException("missing uri/verb in new resource", 6, $this->id, 417);
    }
    if (empty($data['method'])) {
      throw new Core\ApiException("missing method in new resource", 6, $this->id, 417);
    }
    if (empty($data['process'])) {
      throw new Core\ApiException("missing process in new resource", 6, $this->id, 417);
    }
    if (!isset($data['ttl']) || strlen($data['ttl']) < 1) {
      throw new Core\ApiException("missing ttl in new resource", 6, $this->id, 417);
    }

    // check input types for processors
    $this->_validateProcessor($data['process']);
    if (isset($data['output'])) {
      $this->_validateProcessor($data['output']);
    }
    if (isset($data['security'])) {
      $this->_validateProcessor($data['security']);
    }
  }

  /**
   * @param $obj
   * @throws \Datagator\Core\ApiException
   */
  private function _validateProcessor($obj) {
    // check valid processor structure
    if (empty($obj['processor']) || empty($obj['meta'])) {
      throw new Core\ApiException("invalid processor structure, missing 'processor' or 'meta' keys in new resource", 6, -1, 406);
    }

    // check for ID in meta
    if (empty($obj['meta']['id'])) {
      throw new Core\ApiException("processor missing an id attribute in the meta in new resource", 6, -1, 406);
    }

    // validate all inputs
    $class = '\\Datagator\\Processor\\' . ucfirst(trim($obj['processor']));
    if (!class_exists($class)) {
      $class = '\\Datagator\\Endpoint\\' . ucfirst(trim($obj['processor']));
      if (!class_exists($class)) {
        $class = '\\Datagator\\Output\\' . ucfirst(trim($obj['processor']));
        if (!class_exists($class)) {
          $class = '\\Datagator\\Security\\' . ucfirst(trim($obj['processor']));
          if (!class_exists($class)) {
            throw new Core\ApiException('unknown processor in new resource: ' . ucfirst(trim($obj['processor'])), 1);
          }
        }
      }
    }
    $processor = new $class($obj['meta'], $this->request);
    $processorDetails = $processor->details();
    foreach($processorDetails['input'] as $inputName => $inputDef) {
      // validate cardinality
      $count = 0;
      if (isset($obj['meta'][$inputName]) && (!empty($obj['meta'][$inputName]) || strlen($obj['meta'][$inputName]))) {
        if (is_array($obj['meta'][$inputName]) && sizeof($obj['meta'][$inputName]) != 2  && !isset($obj['meta'][$inputName]['processor']) && !isset($obj['meta'][$inputName]['meta'])) {
          // This check is for values that are array of values, but we also have to filter out processors
          $count = sizeof($obj['meta'][$inputName]);
        } else {
          $count = 1;
        }
      }
      if (is_numeric($inputDef['cardinality'][0]) && $count < $inputDef['cardinality'][0]) {
        throw new Core\ApiException("$count inputs supplied (min " . $inputDef['cardinality'][0] . ') for ' . $inputName, 6, $obj['meta']['id'], 406);
      }
      if (is_numeric($inputDef['cardinality'][1]) && $count > $inputDef['cardinality'][1]) {
        throw new Core\ApiException("$count inputs supplied (max " . $inputDef['cardinality'][1] . ') for ' . $inputName, 6, $obj['meta']['id'], 406);
      }
      // validate type
      if (!empty($obj['meta'][$inputName])) {
        if (is_array($obj['meta'][$inputName]) && sizeof($obj['meta'][$inputName]) != 2  && !isset($obj['meta'][$inputName]['processor']) && !isset($obj['meta'][$inputName]['meta'])) {
          // This check is for values that are array of values, but we also have to filter out processors
          foreach ($obj['meta'][$inputName] as $element) {
            $this->_validateTypeValue($element, $inputDef['accepts'], $inputName);
          }
        } else {
          $this->_validateTypeValue($obj['meta'][$inputName], $inputDef['accepts'], $inputName);
        }
      }
    }
  }

  /**
   * Compare an element type and possible literal value or type in the input resource with the definition in the Processor it refers to.
   * If the element type is processor, recursively iterate through, using the calling function _validateProcessor().
   *
   * @param $element
   * @param $accepts
   * @param $inputName
   * @throws \Datagator\Core\ApiException
   */
  private function _validateTypeValue($element, $accepts, $inputName) {
    $valid = false;

    foreach ($accepts as $accept) {
      if ($accept == 'processor' && isset($element['processor']) && isset($element['meta'])) {
        $this->_validateProcessor($element);
        $valid = true;
        break;
      } elseif ($accept == 'literal' && (is_string($element) || is_numeric($element))) {
        $valid = true;
        break;
      } elseif ($accept == 'bool' && is_bool($element)) {
        $valid = true;
        break;
      } elseif ($accept == 'numeric' && is_numeric($element)) {
        $valid = true;
        break;
      } elseif ($accept == 'integer' && is_integer($element)) {
        $valid = true;
        break;
      } elseif ($accept == 'string' && is_string($element)) {
        $valid = true;
        break;
      } elseif ($accept == 'float' && is_float($element)) {
        $valid = true;
        break;
      } elseif ($accept == 'bool' && is_bool($element)) {
        $valid = true;
        break;
      } elseif ($accept == 'file') {
        $valid = true;
        break;
      } else {
        $firstLast = substr($accept, 0, 1) . substr($accept, -1, 1);
        if ($firstLast == '""' || $firstLast == "''") {
          if ($element == trim($accept, "\"'")) {
            $valid = true;
            break;
          }
        }
      }
    }

    if (!$valid) {
      throw new Core\ApiException("invalid input ($element) for $inputName in new resource. only allowed inputs are: " . implode(', ', $accepts), 6, $element['id'], 406);
    }
  }
}
