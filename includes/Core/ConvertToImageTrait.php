<?php

/**
 * Trait ConvertToImageTrait.
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
 * Trait ConvertToImageTrait.
 *
 * Trait to cast an input value to image.
 */
trait ConvertToImageTrait
{
    /**
     * Convert empty to image.
     *
     * @param $data
     *
     * @return string
     */
    public function fromEmptyToImage($data): string
    {
        return '';
    }

    /**
     * Convert boolean to image.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromBooleanToImage($data): string
    {
        throw new ApiException('Cannot cast boolean to image');
    }

    /**
     * Convert integer to image.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromIntegerToImage($data): string
    {
        throw new ApiException('Cannot cast integer to image');
    }

    /**
     * Convert float to image.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromFloatToImage($data): string
    {
        throw new ApiException('Cannot cast float to image');
    }

    /**
     * Convert text to image.
     *
     * @param $data
     *
     * @return string
     */
    public function fromTextToImage($data): string
    {
        return $data;
    }

    /**
     * Convert array to image.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromArrayToImage($data): string
    {
        throw new ApiException('Cannot cast array to image');
    }

    /**
     * Convert JSON to image.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromJsonToImage($data): string
    {
        throw new ApiException('Cannot cast JSON to image');
    }

    /**
     * Convert XML to image.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromXmlToImage($data): string
    {
        throw new ApiException('Cannot cast XML to image');
    }

    /**
     * Convert HTML to image.
     *
     * @param $data
     *
     * @return string
     *
     * @throws ApiException
     */
    public function fromHtmlToImage($data): string
    {
        throw new ApiException('Cannot cast HTML to image');
    }

    /**
     * Convert image to image.
     *
     * @param $data
     *
     * @return string
     */
    public function fromImageToImage($data): string
    {
        return $data;
    }

    /**
     * Convert file to image.
     *
     * @param $data
     *
     * @return string
     */
    public function fromFileToImage($data): string
    {
        return $data;
    }
}
