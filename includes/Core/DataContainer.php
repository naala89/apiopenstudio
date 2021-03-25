<?php

/**
 * Class DataContainer.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use DOMDocument;

/**
 * Class DataContainer
 *
 * Provide s container for data to be passed between processors in a clean manner.
 */
class DataContainer extends Entity
{
    /**
     * All data types.
     *
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
     * Data type.
     *
     * @var string Default data type
     */
    private $type = 'empty';

    /**
     * Data.
     *
     * @var mixed Data
     */
    private $data;

    /**
     * DataContainer constructor.
     *
     * @param mixed $data Data stored in the container.
     * @param string $dataType Data type.
     */
    public function __construct($data, string $dataType = null)
    {
        $dataType = empty($dataType) ? $this->detectType($data) : $dataType;
        $this->setType($dataType);
        $this->setData($data);
    }

    /**
     * Get the data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data.
     *
     * @param mixed $val Data.
     *
     * @return void
     */
    public function setData($val)
    {
        $this->data = $val;
    }

    /**
     * Get the data type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the data type.
     *
     * @param string $val Data type.
     *
     * @return void
     */
    public function setType(string $val)
    {
        $this->type = $val;
    }

    /**
     * Fetch all possible data types.
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Detect the type of data for the input string.
     *
     * @param mixed $data Data to test.
     *
     * @return string The data type.
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
     * @param mixed $var Variable to test.
     *
     * @return boolean
     */
    private function isEmpty($var)
    {
        return $var !== 0 && $var !== '0' && $var !== false && empty($var);
    }

    /**
     * Validate a variable is boolean.
     *
     * @param mixed $var Variable to test.
     *
     * @return boolean
     */
    private function isBool($var)
    {
        return $var === "true" || $var === "false" || is_bool($var);
    }

    /**
     * Validate a variable is integer.
     *
     * @param mixed $var Variable to test.
     *
     * @return boolean
     */
    private function isInt($var)
    {
        if (is_array($var)) {
            return false;
        }
        if ($var === 0 || $var === '0') {
            return true;
        }
        if ((int) ltrim($var, '0') != ltrim($var, '0')) {
            return false;
        }
        return is_int(filter_var(ltrim($var, '0'), FILTER_VALIDATE_INT, ['default' => null]));
    }

    /**
     * Validate a variable is float.
     *
     * @param mixed $var Variable to test.
     *
     * @return boolean
     */
    private function isFloat($var)
    {
        return is_float(filter_var($var, FILTER_VALIDATE_FLOAT, ['default' => null]));
    }

    /**
     * Validate a variable is an array.
     *
     * @param mixed $var Variable to test.
     *
     * @return boolean
     */
    private function isArray($var)
    {
        return is_array($var);
    }

    /**
     * Validate a variable is JSON.
     *
     * @param mixed $var Variable to test.
     *
     * @return boolean
     */
    private function isJson($var)
    {
        json_decode($var);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Validate a variable is HTML.
     *
     * @param mixed $var Variable to test.
     *
     * @return boolean
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
     * @param mixed $var Variable to test.
     *
     * @return boolean
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
