<?php

/**
 * Trait ConvertToTextTrait.
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

/**
 * Trait ConvertToTextTrait.
 *
 * Trait to cast an input value to text.
 */
trait ConvertToTextTrait
{
    /**
     * Convert empty to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromEmptyToText($data): string
    {
        return '';
    }

    /**
     * Convert boolean to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromBooleanToText($data): string
    {
        return var_export($data, true);
    }

    /**
     * Convert integer to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromIntegerToText($data): string
    {
        return var_export($data, true);
    }

    /**
     * Convert float to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromFloatToText($data): string
    {
        return var_export($data, true);
    }

    /**
     * Convert text to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromTextToText($data): string
    {
        return $data;
    }

    /**
     * Convert array to text.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromArrayToText($data): string
    {
        throw new ApiException('cannot convert array to text, please convert to JSON or XML');
    }

    /**
     * Convert JSON to text.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromJsonToText($data): string
    {
        throw new ApiException('cannot convert JSON to text, this will already be JSON string');
    }

    /**
     * Convert XML to text.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromXmlToText($data): string
    {
        throw new ApiException('cannot convert XML to text, this will already be XML string');
    }

    /**
     * Convert HTML to text.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromHtmlToText($data): string
    {
        throw new ApiException('cannot convert HTML to text, this will already be HTML string');
    }

    /**
     * Convert image to text.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromImageToText($data): string
    {
        throw new ApiException('cannot convert image to text');
    }

    /**
     * Convert file to text.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromFileToText($data): string
    {
        throw new ApiException('cannot convert file to text');
    }
}
