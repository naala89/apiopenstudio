<?php

include_once(Config::$dirIncludes . 'processor/class.Error.php');

/**
 * Class Processor
 *
 * Base class for all Processors.
 * This is called by Api, and will start the recursive processing of thr metadata.
 */
class Processor
{
  /**
   * Status of the call.
   *
   * @var integer
   */
  public $status = 200;
  /**
   * Display the processor on the frontend.
   *
   * FALSE if you never want a processor to appear in the frontend.
   * TRUE if you always want a processor to appear in the frontend.
   * Integer of the client ID if it is a processor specific to the client.
   *
   * @var boolean|integer
   */
  public $displayFrontend = TRUE;
  /**
   * Processor ID.
   *
   * @var integer
   */
  protected $id = '';
  /**
   * Meta describing the resource (generated by frontend and stored in DB).
   *
   * @var integer
   */
  protected $meta;
  /**
   * All of the request details.
   *
   * @var stdClass
   */
  protected $request;
  /**
   * Required inputs to the processor.
   *
   * @var array
   */
  protected $required = array();
  /**
   * An array of details of the processor, used to configure the frontend GUI and metadata construction.
   *
   * Indexes:
   *  name: name of the processor
   *
   *  description: description of the processor
   *
   *  menu: lists the immediate menu parents
   *
   *    examples:
   *      'menu' => 'menu1' - belongs to menu1
   *      'menu' => array('menu1', 'menu2') - belongs to menu1, and menu2
   *
   *  input: list the input nodes for this processor
   *    This is an array with the following indexes:
   *    description (string): description of what the processor does
   *    cardinality (array): first value is the min of values that this input will accept, the second os the max. * indicates infinite
   *    type (array): an array of input type this processor will accept (i.e. str, int, processor, float, mixed, etc)
   *
   *    examples:
   *      input => array(
   *        'sources' => array('description' => 'desc1', 'cardinality' => array(1, '*'), type => array('processor', 'string'))
   *      )
   *          This processor has only one input, called sources.
   *          Sources must contain at least one value.
   *          The inputs can only be string or another processor.
   *
   *      input => array(
   *        'method' => array('description' => 'desc1', 'cardinality' => array(1, 1), 'accepts' => array('string' => array('get', 'post'))),
   *        'auth' => array('description' => 'desc2', 'cardinality' => array(0, 1), 'accepts' => array('processor'),
   *        'vars' => array('description' => 'desc3', 'cardinality' => array(1, '*'), type => array('processor', 'integer'))
   *      )
   *          This Processor has 3 inputs:
   *          method, which has only one sub-input, of type string, with only 2 possible values ('get' and 'post')
   *          auth, which has only one value, of type processor
   *          vars, which can contain an infinite number of values, of type processor or integer, with no limit on value.
   *
   * @var array
   */
  protected $details = array();

  /**
   * Constructor. Store processor metadata and request data in object.
   *
   * If this method is overridden by any derived classes, don't forget to call parent::__construct()
   *
   * @param $meta
   * @param $request
   */
  public function Processor($meta, $request)
  {
    $this->meta = $meta;
    $this->request = $request;
    if (isset($meta->id)) {
      $this->id = $meta->id;
    }
  }

  /**
   * Main processor function.
   *
   * This is where the magic happens, and should be overridden by all derived classes.
   *
   * Fetches and process the processor described in the metadata.
   * It is also the 1st stop to recursive processing of processors, so the place validate user credentials.
   *
   * @return array|Error
   */
  public function process()
  {
    // TODO: A better way to use getProcessor.
    $processor = $this->getProcessor($this->meta);
    if ($this->status != 200) {
      return $processor;
    }
    $result = $processor->process();
    if ($processor->status != 200) {
      $this->status = $processor->status;
    }

    return $result;
  }

  /**
   * Return details for processor, for frontend application.
   *
   * @return mixed
   */
  public function details()
  {
    return $this->details;
  }

  /**
   * Validate that the required fields are in the metadata
   *
   * @TODO: rename to validateRequiredFields()
   * @return bool|Error
   */
  protected function validateRequired()
  {
    $result = array();
    foreach ($this->required as $required) {
      if (!isset($this->meta->$required)) {
        $result[] = $required;
      }
    }
    if (empty($result)) {
      return TRUE;
    }
    $this->status = 417;
    return new Error(-1, $this->id, 'missing required meta: ' . implode(', ', $result));
  }

  /**
   * Evaluate an object to see if it's a processor.
   *
   * @param $obj
   * @return bool
   */
  protected function isProcessor($obj)
  {
    return (is_object($obj) && (isset($obj->type) || isset($obj->meta)));
  }

  /**
   * Process a variable into a final result for the processor.
   *
   * This method can be used to process a value in it's meta to return a final result that it can use.
   * If the object is a processor, then it will process that down to a final return value,
   * or if the obj is a simple value, then it will return that. Anything else will return an error object.
   *
   * @param $obj
   * @return array|Error
   *
   * TODO: Add validation of var type result. This can be declared in $this->required
   */
  protected function getVar($obj)
  {
    $result = $obj;

    if ($this->isProcessor($obj)) {
      // this is a processor
      $processor = $this->getProcessor($obj);
      if ($this->status != 200) {
        return $processor;
      }
      $result = $processor->process();
      if ($processor->status != 200) {
        $this->status = $processor->status;
      }
    } elseif (!is_string($obj) && !is_numeric($obj) && !is_bool($obj)) {
      // this is an invalid value
      $this->status = 417;
      $result = new Error(-1, $this->id, 'invalid var value');
    }

    return $result;
  }

  /**
   * Fetch the processor defined in the obj (from meta), or return an error.
   *
   * @param bool $obj
   * @param string $dir
   * @param string $prefix
   * @param string $suffix
   * @return Error|Object
   */
  protected function getProcessor($obj = FALSE, $dir = NULL, $prefix = 'Processor', $suffix = '.php')
  {
    $dir = (empty($dir) ? Config::$dirIncludes . 'processor/' : $dir);
    $obj = ($obj === FALSE ? $this->meta : $obj);
    if (!$this->isProcessor($obj)) {
      $this->status = 417;
      return new Error(-1, $this->id, 'invalid object');
    }

    $classname = $prefix . ucfirst(trim($obj->type));
    $filepaths = shell_exec("find $dir -name 'class.$classname.php'");
    $filepaths = preg_split('/\r\n|[\r\n]/', trim($filepaths));
    if (sizeof($filepaths) > 1) {
      $this->status = 417;
      return new Error(-1, $this->id, "multiple processors defined ($classname)");
    }
    if (empty($filepaths) || empty($filepaths[0])) {
      $this->status = 417;
      return new Error(-1, $this->id, "invalid or no processor defined ($classname)");
    }

    include_once($filepaths[0]);
    return new $classname($obj->meta, $this->request);
  }

  /**
   * Wrap a string, array or object and error code into an Error object.
   *
   * Used for instances where the return value from a processor is known to be an error,
   * but the actual data is not an Error object.
   *
   * @param $code
   * @param $error
   * @return Error
   *
   * @see Error
   * @see ProcessorInputUrl
   */
  protected function wrapError($code, $error) {
    if (is_object($error) || is_array(($error))) {
      $error = serialize($error);
    }
    return new Error($code, $this->id, $error);
  }
}