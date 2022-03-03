<?php

/**
 * Trait ConvertToTextTrait.
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
     * @return string|null
     */
    public function fromEmptyToText($data): ?string
    {
        return null;
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
     */
    public function fromArrayToText($data): string
    {
        return json_encode($data);
    }

    /**
     * Convert JSON to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromJsonToText($data): string
    {
        return $data;
    }

    /**
     * Convert XML to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromXmlToText($data): string
    {
        return $data;
    }

    /**
     * Convert HTML to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromHtmlToText($data): string
    {
        return $data;
    }

    /**
     * Convert image to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromImageToText($data): string
    {
        return $data;
    }

    /**
     * Convert file to text.
     *
     * @param $data
     *
     * @return string
     */
    public function fromFileToText($data): string
    {
        return $data;
    }
}
