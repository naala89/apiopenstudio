<?php

namespace Gaterdata\Core;

use DOMDocument;

class DataContainer extends Entity
{
    /**
     * @var array Data types.
     */
    private $types = [
        'boolean',
        'integer',
        'float',
        'text',
        'array',
        'json',
        'xml',
        'image',
        'file',
        'empty',
    ];

    /**
     * @var string Data type
     */
    private $type = 'empty';

    /**
     * @var mixed Data
     */
    private $data;

    /**
     * DataContainer constructor.
     *
     * @param mixed $data
     *   Data stored in the container.
     * @param string $dataType
     *   Data type.
     *
     * @throws ApiException
     */
    public function __construct($data, $dataType = null)
    {
        $dataType = empty($dataType) ? $this->detectType($data) : $dataType;
        $this->setType($dataType);
        $this->setData($data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $val
     */
    public function setData($val)
    {
        $this->data = $val;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $val
     * @throws ApiException
     */
    public function setType($val)
    {
        $this->type = $val;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Detect the type of data for the input string.
     *
     * @param string $data
     * @return string
     *   The data type.
     */
    private function detectType($data)
    {
        if ($this->isEmpty($data)) {
            return 'empty';
        }
        if ($this->isBool($data)) {
            return 'boolean';
        }
        if ($this->isInt($data)) {
            return 'integer';
        }
        if ($this->isFloat($data)) {
            return 'float';
        }
        if ($this->isArray($data)) {
            return 'array';
        }
        if ($this->isJson($data)) {
            return 'json';
        }
        if ($this->isHtml($data)) {
            return 'html';
        }
        if ($this->isXml($data)) {
            return 'xml';
        }
        return 'text';
    }

    /**
     * Validate a variable is empty.
     *
     * @param $var
     * @return bool
     */
    private function isEmpty($var)
    {
        return $var !== 0 && $var !== '0' && $var !== false && empty($var);
    }

    /**
     * Validate a variable is boolean.
     *
     * @param $var
     * @return bool
     */
    private function isBool($var)
    {
        return $var === "true" || $var === "false" || is_bool($var);
    }

    /**
     * Validate a variable is integer.
     *
     * @param $var
     * @return bool
     */
    private function isInt($var)
    {
        if (is_array($var)) {
            return false;
        }
        if ($var === 0 || $var === '0') {
            return true;
        }
        if ((integer) ltrim($var, '0') != ltrim($var, '0')) {
            return false;
        }
        return is_int(filter_var(ltrim($var, '0'), FILTER_VALIDATE_INT, ['default' => null]));
    }

    /**
     * Validate a variable is float.
     *
     * @param $var
     * @return bool
     */
    private function isFloat($var)
    {
        return is_float(filter_var($var, FILTER_VALIDATE_FLOAT, ['default' => null]));
    }

    /**
     * Validate a variable is an array.
     *
     * @param $var
     * @return bool
     */
    private function isArray($var)
    {
        return is_array($var);
    }

    /**
     * Validate a variable is JSON.
     *
     * @param $var
     * @return bool
     */
    private function isJson($var)
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
    private function isHtml($var)
    {
        $var = trim($var);

        if (empty($var)) {
            return false;
        }

        if (stripos($var, '<!DOCTYPE html>') === false) {
            return false;
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($var);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }

    /**
     * Validate a variable is XML.
     *
     * @param $var
     * @return bool
     */
    private function isXml($var)
    {
        libxml_use_internal_errors(true);
        $testXml = simplexml_load_string($var);
        if ($testXml) {
            return true;
        }
        return false;
    }
}
