<?php

namespace Datagator\Core;
use Datagator\Config;

abstract class ProcessorEntity extends Entity
{
  /**
   * Processor ID.
   * @var integer
   */
  protected $id = '';
  /**
   * Meta required for this processor.
   * @var integer
   */
  protected $meta;
  /**
   * All of the request details.
   * @var stdClass
   */
  protected $request;
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
   *    cardinality: array(int min, mixed max)
   *      e.g. array(0, 1)
   *      max can be integer or '*'. '*' = infinite
   *    type: (array): an array of input type this processor will accept.
   *      Possible values:
   *        processor - any processor
   *        processor <name> - specific processor
   *        "predefined string"
   *        file
   *        literal
   *        bool
   *        numeric
   *        integer
   *        string
   *        float
   *        bool
   *
   *    examples:
   *      input => array(
   *        'sources' => array('description' => 'desc1', 'cardinality' => array(1, '*'), type => array('function', 'literal'))
   *      )
   *          This processor has only one input, called sources.
   *          Sources must contain at least one value.
   *          The inputs can only be string or another processor.
   *
   *      input => array(
   *        'method' => array('description' => 'desc1', 'cardinality' => array(1, 1), 'accepts' => array('literal' => array('"get"', '"post"'))),
   *        'auth' => array('description' => 'desc2', 'cardinality' => array(1, 1), 'accepts' => array('function'),
   *        'vars' => array('description' => 'desc3', 'cardinality' => array(0, '*'), type => array('function', 'integer')),
   *        't' => array('description' => 'desc4', 'cardinality' => array(0, '*'), type => array('processor field', 'string'))
   *      )
   *          This Processor has 3 inputs:
   *          method, which has only one sub-input, of type string, with only 2 possible values ('get' and 'post')
   *          auth, which has only one value, of type processor
   *          vars, which can contain an infinite number of values, of type processor or integer, with no limit on value
   *          t, which can take or or many input of Processor Field or a string.
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
  public function __construct($meta, & $request)
  {
    $this->meta = $meta;
    $this->request = $request;
    $this->id = isset($meta->id) ? $meta->id : -1;
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
  abstract public function process();

  /**
   * Return details for processor.
   *
   * @return array
   */
  public function details()
  {
    return $this->details;
  }

  /**
   * Get a file.
   *
   * @param $file
   * @return bool|string
   * @throws \Datagator\Core\ApiException
   */
  protected function getFile($file)
  {
    if (empty($_FILES[$file])) {
      return false;
    }

    // Check for error
    switch ($_FILES[$file]['error']) {
      case UPLOAD_ERR_OK:
        break;
      case UPLOAD_ERR_NO_FILE:
        throw new ApiException('No file sent.', 1, $this->id);
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        throw new ApiException('Exceeded filesize limit.', 1, $this->id);
      default:
        throw new ApiException('Unknown errors.', 1, $this->id);
    }

    // Check for upload attack.
    $newFile = $_SERVER['DOCUMENT_ROOT'] . Config::$dirUploads . basename($_FILES[$file]['name']);
    if (!move_uploaded_file($_FILES[$file]['tmp_name'], $newFile)) {
      throw new ApiException('Possible file upload attack!', 1, $this->id);
    }

    $result = file_get_contents($newFile);
    if (!unlink($newFile)) {
      throw new ApiException('failed to cleanup and delete uploaded file. Please contact support.', 1, $this->id);
    }

    return $result;
  }

  /**
   * Process a variable into a final result for the processor.
   *
   * This method can be used to process a value in it's meta to return a final result that it can use.
   * If the object is a processor, then it will process that down to a final return value,
   * or if the obj is a simple value, then it will return that. Anything else will return an error object.
   *
   * Setting $realValue will force the value to be the actial valie, rather than a dataEntity.
   *
   * @param $key
   * @param bool|FALSE $realValue
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  protected function val($key, $realValue=false)
  {
    $inputDet = $this->details['input'];
    if (!isset($inputDet[$key])) {
      // undefined input key for this processor type
      throw new ApiException("invalid key: $key", 1, $this->id);
    }

    $min = $inputDet[$key]['cardinality'][0];
    $max = $inputDet[$key]['cardinality'][1];
    $limitValues = $inputDet[$key]['limitValues'];
    $limitTypes = $inputDet[$key]['limitTypes'];
    $default = $inputDet[$key]['default'];

    $count = empty($this->meta->$key) ? 0 : is_array($this->meta->$key) ? sizeof($this->meta->$key) : 1;
    if ($count < $min || ($max != '*' && $count > $max)) {
      // invalid cardinality
      throw new ApiException("invalid number of inputs ($count), requires $min - $max", 1, $this->id);
    }

    // return default if empty
    if (!isset($this->meta->$key)) {
      return $inputDet[$key]['default'];
    }
    if ($this->isDataContainer($this->meta->$key) && $this->meta->$key->getData() == '') {
      return $inputDet[$key]['default'];
    }

    $result = $this->meta->$key;

    if (is_array($result)) {
      foreach ($result as & $r) {
        $value = $this->isDataContainer($r) ? $r->getData() : $r;
        $this->_validateAllowedValues($value, $limitValues);
        $this->_validateAllowedTypes($value, $limitTypes);
      }
    } else {
      $value = $this->isDataContainer($result) ? $result->getData() : $result;
      $this->_validateAllowedValues($value, $limitValues);
      $this->_validateAllowedTypes($value, $limitTypes);
    }

    return $realValue ? $this->isDataContainer($result) ? $result->getData() : $result : $result;
  }

  protected function isDataContainer($data)
  {
    return is_object($data) && get_class($data) == 'Datagator\Core\DataContainer';
  }

  /**
   * Validate an input for allowed values
   *
   * @param $val
   * @param array $limitValues
   * @throws \Datagator\Core\ApiException
   */
  private function _validateAllowedValues($val, array $limitValues)
  {
    if (empty($limitValues)) {
      return;
    }
    if (!in_array($val, $limitValues)) {
      throw new ApiException("invalid value ($val). Only '" . implode("', '",$limitValues) . "' allowed", 5, $this->id, 417);
    }
  }

  /**
   * Validate an input for allowed variable types
   *
   * @param $val
   * @param array $limitTypes
   * @throws \Datagator\Core\ApiException
   */
  private function _validateAllowedTypes($val, array $limitTypes)
  {
    if (empty($limitTypes)) {
      return;
    }
    if (in_array('boolean', $limitTypes) && $this->_checkBool($val)) {
      return;
    }
    if (in_array('integer', $limitTypes) && $this->_checkInt($val)) {
      return;
    }
    if (in_array('float', $limitTypes) && $this->_checkFloat($val)) {
      return;
    }
    if (in_array('array', $limitTypes) && is_array($val)) {
      return;
    }
    if (!empty($val)) {
      $type = gettype($val);
      if (!in_array($type, $limitTypes)) {
        throw new ApiException("invalid value ($val), only '" . implode("', '",$limitTypes) . "' allowed", 5, $this->id, 417);
      }
    }
  }

  public function _checkBool($var) {
    return null !== filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }

  public function _checkFloat($var) {
    return null !== filter_var($var, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
  }

  public function _checkInt($var) {
    return null !== filter_var($var, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
  }

  /**
   * Get a DB object.
   *
   * @return \the
   * @throws \Datagator\Core\ApiException
   */
  protected function getDb()
  {
    $dsnOptions = '';
    if (sizeof(Config::$dboptions) > 0) {
      foreach (Config::$dboptions as $k => $v) {
        $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
    $dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
    $db = \ADONewConnection($dsn);
    if (!$db) {
      throw new ApiException('DB connection failed',2 , $this->id, 500);
    }
    $db->debug = Config::$debugDb;
    return $db;
  }
}