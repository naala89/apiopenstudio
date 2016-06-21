<?php

namespace Datagator\Processor;
use Datagator\Config;
use Datagator\Core;

class ProcessorEntity
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
   */
  public function __construct($meta)
  {
    $this->meta = $meta;
    $this->id = !empty($meta->id) ? $meta->id : -1;
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
    if ($this->isFragment($this->meta)) {
      return $this->request->fragments->{$this->meta->fragment};
    }
    $processor = $this->getProcessor($this->meta);
    $result =  $processor->process();
    return $result;
  }

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
   * Evaluate an object to see if it's a processor.
   *
   * @param $obj
   * @return bool
   */
  protected function isProcessor($obj)
  {
    return (is_object($obj) && isset($obj->function));
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
        throw new Core\ApiException('No file sent.', 1, $this->id);
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        throw new Core\ApiException('Exceeded filesize limit.', 1, $this->id);
      default:
        throw new Core\ApiException('Unknown errors.', 1, $this->id);
    }

    // Check for upload attack.
    $newFile = $_SERVER['DOCUMENT_ROOT'] . Config::$dirUploads . basename($_FILES[$file]['name']);
    if (!move_uploaded_file($_FILES[$file]['tmp_name'], $newFile)) {
      throw new Core\ApiException('Possible file upload attack!', 1, $this->id);
    }

    $result = file_get_contents($newFile);
    if (!unlink($newFile)) {
      throw new Core\ApiException('failed to cleanup and delete uploaded file. Please contact support.', 1, $this->id);
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
   * @param $obj
   * @return array
   * @throws \Datagator\Core\ApiException
   */
  protected function val($obj)
  {
    if (is_object($obj) && isset($obj->function)) {
      // this is a processor. Something has gone wrong - we should only have values at this point
      throw new Core\ApiException('function encountered in an input!');
    } elseif (is_array($obj) && !Core\Utilities::is_assoc($obj)) {
      // this is an array values
      $result = array();
      foreach ($obj as $o) {
        $result[] = $this->val($o);
      }
    } elseif (!empty($obj) || $obj == 0) {
      // this is a literal, so return it
      $result = $obj;
    } else {
      // invalid value, so return empty string
      $result = '';
    }

    return $result;
  }

  /**
   * Get a DB object.
   *
   * @return \the
   * @throws \Datagator\Processor\ApiException
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