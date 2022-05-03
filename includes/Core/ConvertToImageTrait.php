<?php

/**
 * Trait ConvertToImageTrait.
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
     * @return null
     */
    public function fromEmptyToImage($data)
    {
        return null;
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
        throw new ApiException('Cannot cast boolean to image', 6, -1, 400);
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
        throw new ApiException('Cannot cast integer to image', 6, -1, 400);
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
        throw new ApiException('Cannot cast float to image', 6, -1, 400);
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
        throw new ApiException('Cannot cast array to image', 6, -1, 400);
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
        throw new ApiException('Cannot cast JSON to image', 6, -1, 400);
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
        throw new ApiException('Cannot cast XML to image', 6, -1, 400);
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
        throw new ApiException('Cannot cast HTML to image', 6, -1, 400);
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
