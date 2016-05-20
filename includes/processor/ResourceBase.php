<?php

/**
 * Base class for processors to import, export and delete resources.
 */

namespace Datagator\Processor;
use Codeception\Util\Debug;
use Datagator\Core;
use Datagator\Db;

abstract class ResourceBase extends ProcessorBase
{
  protected $db;
  protected $details = array(
    'name' => 'Resource',
    'description' => 'Create, edit or fetch a custom API resource for the application. NOTE: in the case of DELETE, the args for the input should be as GET vars - POST vars are not guaranteed on all servers with this method.',
    'menu' => 'Resource',
    'application' => 'All',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', '"get"', '"post"', '"delete"', '"push"'),
      ),
      'appid' => array(
        'description' => 'The application ID the resource is associated with (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('integer')
      ),
      'noun' => array(
        'description' => 'The noun identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('literal')
      ),
      'verb' => array(
        'description' => 'The verb identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('literal')
      ),
      'resource' => array(
        'description' => 'The resource as a string (this input is only used if you are creating or updating a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal')
      )
    )
  );

  /**
   * @return bool|string
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processor\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ResourceBase', 4);

    $this->db = $this->getDb();

    switch ($this->request->method) {
      case 'post':
        $string = $this->val($this->meta->resource);
        $resource = $this->_importData($string);
        if (sizeof($resource) == 1 && isset($resource[0])) {
          // resource is not JSON. Fallback to assuming this is a filename.
          $resource = $this->_importData($this->getFile($resource[0]));
        }
        if (empty($resource)) {
          throw new Core\ApiException('Empty resource', 1, $this->id);
        }
        $result = $this->create($resource);
        break;
      case 'get':
        $appId = $this->request->appId;
        $method = $this->val($this->meta->method);
        $noun = $this->val($this->meta->noun);
        $verb = $this->val($this->meta->verb);
        if (empty($method)) {
          throw new Core\ApiException('Missing method', 1, $this->id);
        }
        if (empty($noun)) {
          throw new Core\ApiException('Missing noun', 1, $this->id);
        }
        if (empty($verb)) {
          throw new Core\ApiException('Missing verb', 1, $this->id);
        }
        $result = $this->read($appId, $method, $noun, $verb);
        break;
      case 'delete':
        $appId = $this->request->appId;
        $method = $this->val($this->meta->method);
        $noun = $this->val($this->meta->noun);
        $verb = $this->val($this->meta->verb);
        if (empty($method)) {
          throw new Core\ApiException('Missing method', 1, $this->id);
        }
        if (empty($noun)) {
          throw new Core\ApiException('Missing noun', 1, $this->id);
        }
        if (empty($verb)) {
          throw new Core\ApiException('Missing verb', 1, $this->id);
        }
        $result = $this->delete($appId, $method, $noun, $verb);
        break;
      default:
        throw new Core\ApiException('unknown method', 3, $this->id);
        break;
    }

    return $result;
  }

  /**
   * Abstract class used to fetch input resource into the correct array format.
   * This has to be declared in each derived class, so that we can cater for many input formats.
   *
   * @param $data
   * @return mixed
   */
  abstract protected function _importData($data);

  /**
   * Abstract class used to fetch input resource into the correct array format.
   * This has to be declared in each derived class, so that we can cater for many output formats.
   *
   * @param array $data
   * @return mixed
   */
  abstract protected function _exportData($data);

  /**
   * Create or update a resource from input data.
   *
   * @param $data
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  protected function create($data)
  {
    Core\Debug::variable($data, 'New resource', 1);
    $this->_validateData($data);

    $name = $data['name'];
    $description = $data['description'];
    $method = $data['method'];
    $identifier = strtolower($data['uri']['noun']) . strtolower($data['uri']['verb']);
    $meta = array();
    if (!empty($data['security'])) {
      $meta['security'] = $data['security'];
    }
    $meta['process'] =  $data['process'];
    if (!empty($data['fragments'])) {
      $meta['fragments'] = $data['fragments'];
    }
    $ttl = !empty($data['ttl']) ? $data['ttl'] : 0;

    $mapper = new Db\ResourceMapper($this->db);
    $resource = $mapper->findByAppIdMethodIdentifier($this->request->appId, $method, $identifier);
    if (empty($resource->getId())) {
      $resource->setAppId($this->request->appId);
      $resource->setMethod($method);
      $resource->setIdentifier($identifier);
    }
    $resource->setName($name);
    $resource->setDescription($description);
    $resource->setMeta(json_encode($meta));
    $resource->setTtl($ttl);

    return $mapper->save($resource);
  }

  /**
   * Fetch a resource.
   *
   * @param $appId
   * @param $method
   * @param $noun
   * @param $verb
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  protected function read($appId, $method, $noun, $verb)
  {
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

    $mapper = new Db\ResourceMapper($this->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    if (empty($resource->getId())) {
      throw new Core\ApiException('Resource not found', 1, $this->id, 200);
    }

    $result = json_decode($resource->getMeta(), TRUE);
    $result['uri'] = array(
      'noun' => $noun,
      'verb' => $verb
    );
    $result['name'] = $resource->getName();
    $result['description'] = $resource->getDescription();
    $result['method'] = $resource->getMethod();
    $result['ttl'] = $resource->getTtl();

    return $this->_exportData($result);
  }

  /**
   * Delete a resource.
   *
   * @param $appId
   * @param $method
   * @param $noun
   * @param $verb
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  protected function delete($appId, $method, $noun, $verb)
  {
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

    $mapper = new Db\ResourceMapper($this->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    return $mapper->delete($resource);
  }

  /**
   * Validate input data is well formed.
   *
   * @param $data
   * @throws \Datagator\Core\ApiException
   */
  protected function _validateData($data) {
    // check mandatory elements exists in data
    if (empty($data)) {
      throw new Core\ApiException("empty resource uploaded", 6, $this->id, 417);
    }
    if (is_array($data) && sizeof($data) == 1 && $data[0] == $this->meta->resource) {
      throw new Core\ApiException('Form-data element with name: "' . $this->meta->resource . '" not found.', 6, $this->id, 417);
    }
    if (empty($data['name'])) {
      throw new Core\ApiException("missing name in new resource", 6, $this->id, 417);
    }
    if (empty($data['description'])) {
      throw new Core\ApiException("missing description in new resource", 6, $this->id, 417);
    }
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
    /*
     * Do we need this?
    if (empty($data['security'])) {
      throw new Core\ApiException("missing security in new resource", 6, $this->id, 417);
    }
    */
    if (empty($data['process'])) {
      throw new Core\ApiException("missing process in new resource", 6, $this->id, 417);
    }
    if (!isset($data['ttl']) || strlen($data['ttl']) < 1) {
      throw new Core\ApiException("missing ttl in new resource", 6, $this->id, 417);
    }

    // check input types for processors
    $data['fragments'] = isset($data['fragments']) ? $data['fragments'] : array();
    $this->_validateProcessor($data['process'], $data['fragments']);
    if (isset($data['output'])) {
      foreach ($data['output'] as $output) {
        if ($output != 'response') {
          // TODO: Create this function
          //$this->_validateOutput($output, $data['fragments']);
        }
      }
    }
    if (isset($data['security'])) {
      $this->_validateProcessor($data['security'], $data['fragments']);
    }
    if (isset($data['fragments'])) {
      $this->_validateFragments($data['fragments']);
    }
  }

  /**
   * If an input is a processor, ensure it exists and has correct meta.
   *
   * @param $obj
   * @throws \Datagator\Core\ApiException
   */
  private function _validateProcessor($obj, $fragments) {
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
      if (!empty($obj['meta'][$inputName]) || strlen($obj['meta'][$inputName])) {
        if (is_array($obj['meta'][$inputName]) && (!isset($obj['meta'][$inputName]['processor']) && !isset($obj['meta'][$inputName]['meta']))) {
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
        if (is_array($obj['meta'][$inputName])) {

          if (!empty($obj['meta'][$inputName]['fragment'])) {
            // validate the fragment exists
            $fragmentName = $obj['meta'][$inputName]['fragment'];
            $validFragment = FALSE;
            foreach ($fragments as $fragment) {
              if ($fragment['fragment'] == $fragmentName) {
                $validFragment = TRUE;
              }
            }
            if (!$validFragment) {
              throw new Core\ApiException("Fragment '$fragmentName'  not defined", 6, $obj['meta']['id'], 406);
            }

          } elseif (!empty($obj['meta'][$inputName]['processor']) && !empty($obj['meta'][$inputName]['meta'])) {
            // validate the processor
            $this->_validateProcessor($obj['meta'][$inputName], $fragments);

          } else {
            // This check is for values that are array of values
            foreach ($obj['meta'][$inputName] as $element) {
              $this->_validateTypeValue($element, $inputDef['accepts'], $inputName, $fragments);
            }
          }
        }
      }
    }
  }

  /**
   * @param $fragments
   * @throws \Datagator\Core\ApiException
   */
  private function _validateFragments($fragments)
  {
    if (!is_array($fragments)) {
      throw new Core\ApiException('Invalid fragments section, this should be a list', 1);
    }
    foreach ($fragments as $fragment) {
      // check valid fragment structure
      if (empty($fragment['fragment']) || empty($fragment['meta'])) {
        throw new Core\ApiException("invalid fragment structure, missing 'fragments' or 'meta' keys in new resource", 6, -1, 406);
      }
      if (!is_string($fragment['meta'])) {
        // this must be a processor
        $this->_validateProcessor(($fragment['meta']), $fragments);
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
   * @param $fragments
   * @throws \Datagator\Core\ApiException
   */
  private function _validateTypeValue($element, $accepts, $inputName, $fragments) {
    $valid = false;

    foreach ($accepts as $accept) {
      if (isset($element['fragment'])) {
        //$this->_validateProcessor($this->request->fragments->{$element['fragment']}, $accepts, $inputName);
        $valid = true;
        break;
      }
      if ($accept == 'processor' && isset($element['processor']) && isset($element['meta'])) {
        $this->_validateProcessor($element, $fragments);
        $valid = true;
        break;
      } elseif (strpos($accept, 'processor ') !== false && isset($element['processor']) && isset($element['meta'])) {
        $parts = explode(' ', $accept);
        if (strtolower($element['processor']) == strtolower($parts[1])) {
          $valid = true;
          break;
        }
      } elseif ($accept == 'file') {
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
      throw new Core\ApiException("invalid input ($element) for $inputName in new resource. only allowed inputs are: " . implode(', ', $accepts), 6);
    }
  }
}
