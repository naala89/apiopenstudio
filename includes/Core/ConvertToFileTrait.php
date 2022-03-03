<?php

/**
 * Trait ConvertToFileTrait.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

/**
 * Trait ConvertToFileTrait.
 *
 * Trait to cast an input value to file.
 */
trait ConvertToFileTrait
{
    /**
     * Convert empty to file.
     *
     * @param $data
     *
     * @return null
     */
    public function fromEmptyToFile($data)
    {
        return null;
    }

    /**
     * Convert boolean to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromBooleanToFile($data): bool
    {
        return $data ? 'true' : 'false';
    }

    /**
     * Convert integer to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromIntegerToFile($data): bool
    {
        return $data;
    }

    /**
     * Convert float to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromFloatToFile($data): bool
    {
        return $data;
    }

    /**
     * Convert text to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromTextToFile($data): bool
    {
        return $data;
    }

    /**
     * Convert array to file.
     *
     * @param $data
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function fromArrayToFile($data): bool
    {
        throw new ApiException('Cannot convert an array to file, please convert to JSON or XML first');
    }

    /**
     * Convert JSON to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromJsonToFile($data): bool
    {
        return $data;
    }

    /**
     * Convert XML to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromXmlToFile($data): bool
    {
        return $data;
    }

    /**
     * Convert HTML to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromHtmlToFile($data): bool
    {
        return $data;
    }

    /**
     * Convert image to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromImageToFile($data): bool
    {
        return $data;
    }

    /**
     * Convert file to file.
     *
     * @param $data
     *
     * @return boolean
     */
    public function fromFileToFile($data): bool
    {
        return $data;
    }
}
