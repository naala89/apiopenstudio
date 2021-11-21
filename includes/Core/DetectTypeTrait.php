<?php

/**
 * Trait DetectTypeTrait.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use DOMDocument;

/**
 * Trait DetectTypeTrait.
 *
 * Detect the type of data.
 */
trait DetectTypeTrait
{
    /**
     * Detect the type of data that is input .
     *
     * @param mixed $data Data to test.
     *
     * @return string The data type.
     */
    public function detectType($data): string
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
     * @return bool
     */
    public function isEmpty($var): bool
    {
        return $var === null || (is_string($var) && strlen($var) < 1);
    }

    /**
     * Validate a variable is boolean.
     *
     * @param mixed $var Variable to test.
     *
     * @return bool
     */
    public function isBool($var): bool
    {
        return is_bool($var);
    }

    /**
     * Validate a variable is integer.
     *
     * @param mixed $var Variable to test.
     *
     * @return bool
     */
    public function isInt($var): bool
    {
        return is_int($var);
    }

    /**
     * Validate a variable is float.
     *
     * @param mixed $var Variable to test.
     *
     * @return bool
     */
    public function isFloat($var): bool
    {
        return is_float($var);
    }

    /**
     * Validate a variable is an array.
     *
     * @param mixed $var Variable to test.
     *
     * @return bool
     */
    public function isArray($var): bool
    {
        return is_array($var);
    }

    /**
     * Validate a variable is JSON.
     *
     * @param mixed $var Variable to test.
     *
     * @return bool
     */
    public function isJson($var): bool
    {
        if (!is_string($var)) {
            return false;
        }
        $json = json_decode($var);
        return $json && $var != $json;
    }

    /**
     * Validate a variable is XML.
     *
     * @param mixed $var Variable to test.
     *
     * @return bool
     */
    public function isXml($var): bool
    {
        if (!is_string($var)) {
            return false;
        }
        libxml_use_internal_errors(true);
        $testXml = simplexml_load_string($var);
        if ($testXml) {
            return true;
        }
        return false;
    }

    /**
     * Validate a variable is HTML.
     *
     * @param mixed $var Variable to test.
     *
     * @return bool
     */
    public function isHtml($var): bool
    {
        if (!is_string($var)) {
            return false;
        }

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
}
