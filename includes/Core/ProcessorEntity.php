<?php

namespace Gaterdata\Core;

use Gaterdata\Config;

abstract class ProcessorEntity extends Entity
{
    /**
     * Processor ID.
     * @var integer
     */
    protected $id = '';

    /**
     * Meta required for this processor.
     * @var array
     */
    protected $meta;

    /**
     * All of the request details.
     * @var Request
     */
    protected $request;

    /**
     * An array of details of the processor, used to configure the frontend GUI and metadata construction.
     *
     * Indexes:
     *  name: name of the processor.
     *
     *  machineName: machine name of the processor.
     *
     *  description: description of the processor.
     *
     *  account: The account that can use the processor.
     *
     *  menu: lists the immediate menu parents.
     *
     *    examples:
     *      'menu' => 'menu1' - belongs to menu1
     *
     *  input: list the input nodes for this processor
     *    This is an array with the following indexes:
     *    description (string): description of what the processor does
     *    cardinality: (int min, mixed max)
     *      e.g. [0, 1]
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
     *      input => [
     *        'sources' => [
     *            'description' => 'desc1',
     *            'cardinality' => [1, '*'],
     *            type => ['function', 'literal']
     *         ]
     *      ]
     *      This processor has only one input, called sources.
     *      Sources must contain at least one value.
     *      The inputs can only be string or another processor.
     *
     *      input => [
     *        'method' => [
     *          'description' => 'desc1',
     *          'cardinality' => [1, 1],
     *          'accepts' => [
     *            'literal' => ['"get"', '"post"']
     *          ],
     *        ],
     *        'auth' => ['description' => 'desc2', 'cardinality' => [1, 1], 'accepts' => ['function'],
     *        'vars' => ['description' => 'desc3', 'cardinality' => [0, '*'],
     *            type => ['function', 'integer']],
     *        't' => ['description' => 'desc4', 'cardinality' => [0, '*'],
     *            type => ['processor field', 'string']]
     *      ]
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
     * @param \ADOConnection $dbLayer
     */
    protected $db;

    /**
     * Constructor. Store processor metadata and request data in object.
     *
     * @param array $meta
     * @param Request $request
     * @param ADODB_mysqli $db
     */
    public function __construct($meta, &$request, $db)
    {
        $this->meta = $meta;
        $this->request = $request;
        $this->id = isset($meta->id) ? $meta->id : -1;
        $this->db = $db;
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
     * Process a variable into a final result for the processor.
     *
     * This method can be used to process a value in it's meta to return a final result that it can use.
     * If the object is a processor, then it will process that down to a final return value,
     * or if the obj is a simple value, then it will return that. Anything else will return an error object.
     *
     * Setting $realValue to true will force the value to be the actual value, rather than a potential dataContainer.
     *
     * @param string $key
     *   The key for the input variable in the meta.
     * @param bool|FALSE $realValue
     *   Return the real value or a dataContainer
     *
     * @return array|DataContainer
     *
     * @throws ApiException
     */
    public function val($key, $realValue = false)
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
            throw new ApiException("invalid number of inputs ($count) in $key, requires $min - $max", 7, $this->id);
        }

        // return default if empty.
        $result = $this->isDataContainer($this->meta->$key) ? $this->meta->$key->getData() : $this->meta->$key;
        if ($result === null || $result === '') {
            $result = $default;
        }

        if (is_array($result)) {
            foreach ($result as & $r) {
                $value = $this->isDataContainer($r) ? $r->getData() : $r;
                $this->_validateAllowedValues($value, $limitValues, $min);
                $this->_validateAllowedTypes($value, $limitTypes, $min);
            }
        } else {
            $value = $this->isDataContainer($result) ? $result->getData() : $result;
            $this->_validateAllowedValues($value, $limitValues, $min);
            $this->_validateAllowedTypes($value, $limitTypes, $min);
        }

        if (!$realValue) {
            $result = !$this->isDataContainer($result) ? new DataContainer($result, $this->detectType($result)) : $result;
        } else {
            $result = $this->isDataContainer($result) ? $result->getData() : $result;
        }

        return $result;
    }

    /**
     * Validate if a set of data is wrapped in a DataContainer object.
     * @param $data
     * @return bool
     */
    protected function isDataContainer($data)
    {
        return is_object($data) && get_class($data) == 'Gaterdata\Core\DataContainer';
    }

    /**
     * Generate the params array for the sql search.
     *
     * @param string $keyword
     *   Search keyword
     * @param array $keywordCols
     *   Columns to search for the keyword.
     * @param string $orderBy
     *   Order by column.
     * @param string $direction
     *   Order direction.
     *
     * @return array
     */
    protected function generateParams($keyword, $keywordCols, $orderBy, $direction)
    {
        $params = [];
        if (!empty($keyword) && !empty($keywordCols)) {
            foreach ($keywordCols as $keywordCol) {
                $params['filter'][] = ['keyword' => "%$keyword%", 'column' => $keywordCol];
            }
        }
        if (!empty($orderBy)) {
            $params['order_by'] = $orderBy;
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }
        return $params;
    }

    /**
     * Validate an input for allowed values.
     *
     * @param mixed $val
     *   Input value.
     * @param array $limitValues
     *   List of allowed values.
     * @param integer $min
     *   Minimum number of values.
     *
     * @return bool
     *
     * @throws ApiException
     */
    private function _validateAllowedValues($val, array $limitValues, $min)
    {
        if (empty($limitValues) || ($min < 1 && empty($val))) {
            return true;
        }
        if (!in_array($val, $limitValues)) {
            throw new ApiException("invalid value ($val). Only '"
                . implode("', '", $limitValues)
                . "' allowed", 7, $this->id, 417);
        }
    }

    /**
     * Validate an input for allowed variable types
     *
     * @param mixed $val
     *   Input value.
     * @param array $limitTypes
     *   List of limit on valiable types.
     * @param integer $min
     *   Minimum number of values.
     *
     * @return bool
     *
     * @throws ApiException
     */
    private function _validateAllowedTypes($val, array $limitTypes, $min)
    {
        if (empty($limitTypes) || ($min < 1 && empty($val))) {
            return true;
        }
        if (in_array('boolean', $limitTypes) && $this->_checkBool($val)) {
            return true;
        }
        if (in_array('integer', $limitTypes) && $this->_checkInt($val)) {
            return true;
        }
        if (in_array('float', $limitTypes) && $this->_checkFloat($val)) {
            return true;
        }
        if (in_array('array', $limitTypes) && is_array($val)) {
            return true;
        }
        if (!empty($val)) {
            $type = gettype($val);
            if (!in_array($type, $limitTypes)) {
                $text = $val;
                if ($type == 'array' || $type == 'object') {
                    $text = 'compound object';
                }
                throw new ApiException("invalid value ($text), only '"
                    . implode("', '", $limitTypes)
                    . "' allowed", 7, $this->id, 417);
            }
        }
    }

    /**
     * Validate a variable is boolean.
     *
     * @param $var
     * @return bool
     */
    public function _checkBool($var)
    {
        return null !== filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Validate a variable is float.
     *
     * @param $var
     * @return bool
     */
    public function _checkFloat($var)
    {
        return null !== filter_var($var, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Validate a variable is integer.
     *
     * @param $var
     * @return bool
     */
    public function _checkInt($var)
    {
        return null !== filter_var($var, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Validate a variable is JSON.
     *
     * @param $var
     * @return bool
     */
    public function _checkJson($var)
    {
        json_decode($var);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Validate a variable is HTML.
     *
     * @param $var
     * @return bool
     */
    public function _checkHtml($var)
    {
        libxml_use_internal_errors(true);
        $testXml = simplexml_load_string($var);
        if ($testXml) {
            if (stripos($var, '<!DOCTYPE html>') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validate a variable is XML.
     *
     * @param $var
     * @return bool
     */
    public function _checkXml($var)
    {
        libxml_use_internal_errors(true);
        $testXml = simplexml_load_string($var);
        if ($testXml) {
            return true;
        }
        return false;
    }

    /**
     * Detect the type of data for the input string.
     *
     * @param string $data
     * @return string
     *   The data type.
     */
    protected function detectType($data) {
        if ($this->_checkBool($data)) {
            return 'boolean';
        }
        if ($this->_checkInt($data)) {
            return 'integer';
        }
        if ($this->_checkFloat($data)) {
            return 'float';
        }
        if ($this->_checkJson($data)) {
            return 'json';
        }
        if ($this->_checkHtml($data)) {
            return 'html';
        }
        if ($this->_checkXml($data)) {
            return 'xml';
        }
        return 'text';
    }
}
