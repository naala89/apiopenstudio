<?php

/**
 * Base class for processors to import, export and delete resources.
 */

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db;

abstract class ResourceBase extends Core\ProcessorEntity
{
  protected $db;
  protected $helper;
  protected $details = array(
    'name' => 'Resource',
    'machineName' => 'resourceBase',
    'description' => 'Create, edit or fetch a custom API resource for the application. NOTE: in the case of DELETE, the args for the input should be as GET vars - POST vars are not guaranteed on all servers with this method.',
    'menu' => 'Resource',
    'application' => 'Common',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('get', 'post', 'delete', 'push'),
        'default' => ''
      ),
      'appName' => array(
        'description' => 'The application name that the resource is associated with (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'uri' => array(
        'description' => 'The URI for the resource, i.e. the part after the App ID in the URL (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'resource' => array(
        'description' => 'The resource as a string (this input is only used if you are creating or updating a resource).',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      )
    )
  );
  public function __construct($meta, & $request)
  {
    $this->helper = new Core\ProcessorHelper();
    parent::__construct($meta, $request);
  }

  /**
   * @return bool|string
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processor\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ResourceBase', 4);

    $this->db = $this->getDb();

    switch ($this->request->getMethod()) {
      case 'post':
        $string = $this->val('resource', true);
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
        $method = $this->val('method', true);
        $uri = $this->val('uri', true);
        if (empty($method)) {
          throw new Core\ApiException('Missing method', 1, $this->id);
        }
        if (empty($uri)) {
          throw new Core\ApiException('Missing URI', 1, $this->id);
        }
        $result = $this->read($appId, $method, $uri);
        break;
      case 'delete':
        $appId = $this->request->getAppId();
        $method = $this->val('method', true);
        $uri = $this->val('uri', true);
        if (empty($method)) {
          throw new Core\ApiException('Missing method', 1, $this->id);
        }
        if (empty($uri)) {
          throw new Core\ApiException('Missing URI', 1, $this->id);
        }
        $result = $this->delete($appId, $method, $uri);
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
   * Fetch a resource.
   *
   * @param $appId
   * @param $method
   * @param $uri
   * @return mixed
   * @throws \Datagator\Core\ApiException
   */
  protected function read($appId, $method, $uri)
  {
    if (empty($appId)) {
      throw new Core\ApiException('missing application ID', 3, $this->id, 400);
    }
    if (empty($method)) {
      throw new Core\ApiException('missing method parameter', 1, $this->id, 400);
    }
    if (empty($uri)) {
      throw new Core\ApiException('missing $uri parameter', 1, $this->id, 400);
    }
    $identifier = strtolower($uri);

    $mapper = new Db\ResourceMapper($this->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    if (empty($resource->getId())) {
      throw new Core\ApiException('Resource not found', 1, $this->id, 200);
    }

    $result = json_decode($resource->getMeta(), TRUE);
    $result['uri'] = $resource->getIdentifier();
    $result['name'] = $resource->getName();
    $result['description'] = $resource->getDescription();
    $result['method'] = $resource->getMethod();
    $result['ttl'] = $resource->getTtl();

    return new Core\DataContainer($this->_exportData($result), 'text');
  }

  /**
   * Delete a resource.
   *
   * @param $appId
   * @param $method
   * @param $uri
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  protected function delete($appId, $method, $uri)
  {
    if (empty($appId)) {
      throw new Core\ApiException('missing application ID', 3, $this->id, 400);
    }
    if (empty($method)) {
      throw new Core\ApiException('missing method parameter', 1, $this->id, 400);
    }
    if (empty($uri)) {
      throw new Core\ApiException('missing uri parameter', 1, $this->id, 400);
    }

    $identifier = strtolower($uri);
    $mapper = new Db\ResourceMapper($this->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);

    return new Core\DataContainer($mapper->delete($resource) ? 'true' : 'false', 'text');
  }

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
    $identifier = strtolower($data['uri']);
    $meta = array();
    if (!empty($data['security'])) {
      $meta['security'] = $data['security'];
    }
    $meta['process'] =  $data['process'];
    if (!empty($data['fragments'])) {
      $meta['fragments'] = $data['fragments'];
    }
    $ttl = !empty($data['ttl']) ? $data['ttl'] : 0;

    // prevent same URLS as in common
    $mapper = new Db\ApplicationMapper($this->db);
    $application = $mapper->findByName('Common');
    $appId = $application->getAppId();
    $mapper = new Db\ResourceMapper($this->db);
    $resource = $mapper->findByAppIdMethodIdentifier($appId, $method, $identifier);
    if (!empty($resource->getId())) {
      throw new Core\ApiException("this resource is reserved ($method + $identifier)", 6, -1, 406);
    }

    $mapper = new Db\ResourceMapper($this->db);
    $resource = $mapper->findByAppIdMethodIdentifier($this->request->getAppId(), $method, $identifier);
    if (empty($resource->getId())) {
      $resource->setAppId($this->request->getAppId());
      $resource->setMethod($method);
      $resource->setIdentifier($identifier);
    }
    $resource->setName($name);
    $resource->setDescription($description);
    $resource->setMeta(json_encode($meta));
    $resource->setTtl($ttl);

    return new Core\DataContainer($mapper->save($resource) ? 'true' : 'false', 'text');
  }

  /**
   * Validate input data is well formed.
   *
   * @param $data
   * @throws \Datagator\Core\ApiException
   */
  protected function _validateData($data)
  {
    // check mandatory elements exists in data
    if (empty($data)) {
      throw new Core\ApiException("empty resource uploaded", 6, $this->id, 406);
    }
    if (is_array($data) && sizeof($data) == 1 && $data[0] == $this->meta->resource) {
      throw new Core\ApiException('Form-data element with name: "' . $this->meta->resource . '" not found.', 6, -1, 406);
    }
    if (!isset($data['name'])) {
      throw new Core\ApiException("missing name in new resource", 6, -1, 406);
    }
    if (!isset($data['description'])) {
      throw new Core\ApiException("missing description in new resource", 6, -1, 406);
    }
    if (!isset($data['uri'])) {
      throw new Core\ApiException("missing uri in new resource", 6, -1, 406);
    }
    if (!isset($data['method'])) {
      throw new Core\ApiException("missing method in new resource", 6, -1, 406);
    }
    if (!isset($data['process'])) {
      throw new Core\ApiException("missing process in new resource", 6, -1, 406);
    }
    if (!isset($data['ttl']) || strlen($data['ttl']) < 1) {
      throw new Core\ApiException("missing or negative ttl in new resource", 6, -1, 406);
    }

    // validate for identical IDs
    $this->_identicalIds($data);

    // validate dictionaries
    if (isset($data['security'])) {
      // check for identical IDs
      $this->_validateDetails($data['security']);
    }
    if (!empty($data['output'])) {
      if (!is_array($data['output']) || Core\Utilities::is_assoc($data['output'])) {
        throw new Core\ApiException('invalid output structure', 6, -1, 406);
      }
      foreach ($data['output'] as $i => $output) {
        if (is_array($output)) {
          if (!$this->helper->isProcessor($output)) {
            throw new Core\ApiException("missing function at index $i", 6, -1, 406);
          }
          $this->_validateDetails($output);
        } elseif ($output != 'response') {
          throw new Core\ApiException("invalid output structure at index: $i", 6, -1, 406);
        }
      }
    }
    if (!empty($data['fragments'])) {
      if (!Core\Utilities::is_assoc($data['fragments'])) {
        throw new Core\ApiException("invalid fragments structure", 6, -1, 406);
      }
      foreach ($data['fragments'] as $fragKey => $fragVal) {
        $this->_validateDetails($fragVal);
      }
    }
    $this->_validateDetails($data['process']);
  }

  /**
   * Search for identical IDs.
   * @param $meta
   * @throws \Datagator\Core\ApiException
   */
  private function _identicalIds($meta)
  {
    $id = array();
    $stack = array($meta);

    while ($node = array_shift($stack)) {
      if ($this->helper->isProcessor($node)) {
        if (in_array($node['id'], $id)) {
          throw new Core\ApiException('identical ID in new resource: ' . $node['id'], 6, -1, 406);
        }
        $id[] = $node['id'];
      }
      if (is_array($node)) {
        foreach ($node as $item) {
          array_unshift($stack, $item);
        }
      }
    }

    return;
  }

  /**
   * Validate a resource section
   *
   * @param $meta
   * @throws \Datagator\Core\ApiException
   */
  private function _validateDetails($meta)
  {
    if (empty($meta['id'])) {
      throw new Core\ApiException('missing ID in a function', 6, -1, 406);
    }
    $id = $meta['id'];

    $classStr = $this->helper->getProcessorString($meta['function']);
    $class = new $classStr($meta, new Core\Request());
    $details = $class->details();

    foreach ($details['input'] as $inputKey => $inputDef) {
      $min = $inputDef['cardinality'][0];
      $max = $inputDef['cardinality'][1];
      $literalAllowed = $inputDef['literalAllowed'];
      $limitFunctions = $inputDef['limitFunctions'];
      $limitTypes = $inputDef['limitTypes'];
      $limitValues = $inputDef['limitValues'];

      $count = 0;
      if (!empty($meta[$inputKey])) {
        $input = $meta[$inputKey];
        if ($this->helper->isProcessor($input)) {
          if (!empty($limitFunctions) && !in_array($input['function'], $limitFunctions)) {
            throw new Core\ApiException("invalid function in $inputKey: " . $input['function'], 6, $id, 406);
          }
          $this->_validateDetails($input);
          $count = 1;
        } elseif (is_array($input)) {
          foreach ($input as $item) {
            if ($this->helper->isProcessor($item)) {
              $this->_validateDetails($item);
            } else {
              $this->_validateTypeValue($item, $limitTypes);
            }
          }
          $count = sizeof($input);
        } elseif (!$literalAllowed) {
          throw new Core\ApiException("literals not allowed as input", 6, $id, 406);
        } else {
          if (!empty($limitValues) && !in_array($input, $limitValues)) {
            Core\Debug::variable($input);
            Core\Debug::variable($limitValues);
            throw new Core\ApiException("invalid value type in $inputKey: " . $input, 6, $id, 406);
          }
          if (!empty($limitTypes)) {
            $this->_validateTypeValue($input, $limitTypes);
          }
          $count = 1;
        }
      }

      // validate cardinality
      if ($count < $min) {
        // check for nothing to validate and if that is ok.
        throw new Core\ApiException("input '$inputKey' in function '" . $meta['id'] . "' requires min $min", 6, $id, 406);
      }
      if ($max != '*' && $count > $max) {
        throw new Core\ApiException("input '$inputKey' in function '" . $meta['id'] . "' requires max $max", 6, $id, 406);
      }
    }
  }

  /**
   * Compare an element type and possible literal value or type in the input resource with the definition in the Processor it refers to.
   * If the element type is processor, recursively iterate through, using the calling function _validateProcessor().
   *
   * @param $element
   * @param $accepts
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  private function _validateTypeValue($element, $accepts)
  {
    if (empty($accepts)) {
      return TRUE;
    }
    /*
    if (is_array($element) && isset($element['function']) && isset($element['id'])) {
      return TRUE;
    }
    */
    $valid = FALSE;

    foreach ($accepts as $accept) {
      if ($accept == 'file') {
        $valid = TRUE;
        break;
      } elseif ($accept == 'literal' && (is_string($element) || is_numeric($element))) {
        $valid = TRUE;
        break;
      } elseif ($accept == 'boolean' && filter_var($element, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
        $valid = TRUE;
        break;
      } elseif ($accept == 'integer' && filter_var($element, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) !== null) {
        $valid = TRUE;
        break;
      } elseif ($accept == 'string' && is_string($element)) {
        $valid = TRUE;
        break;
      } elseif ($accept == 'float' && filter_var($element, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) !== null) {
        $valid = TRUE;
        break;
      } elseif ($accept == 'array' && is_array($element)) {
        $valid = TRUE;
        break;
      }
    }
    if (!$valid) {
      throw new Core\ApiException("invalid literal in new resource. only '" . implode("', '", $accepts) . "' accepted", 6, -1, 406);
    }
    return $valid;
  }
}
