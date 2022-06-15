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
     * Convert array to image.
     *
     * @param array $array
     *
     * @throws ApiException
     */
    public function fromArrayToImage(array $array)
    {
        throw new ApiException('Cannot cast array to image', 6, -1, 400);
    }

    /**
     * Convert boolean to image.
     *
     * @param bool $boolean
     *
     * @throws ApiException
     */
    public function fromBooleanToImage(bool $boolean)
    {
        throw new ApiException('Cannot cast boolean to image', 6, -1, 400);
    }

    /**
     * Convert file to image.
     *
     * @param $file
     *
     * @return string
     */
    public function fromFileToImage($file): string
    {
        return $file;
    }

    /**
     * Convert float to image.
     *
     * @param float $float
     *
     * @throws ApiException
     */
    public function fromFloatToImage(float $float)
    {
        throw new ApiException('Cannot cast float to image', 6, -1, 400);
    }

    /**
     * Convert HTML to image.
     *
     * @param string $html
     *
     * @throws ApiException
     */
    public function fromHtmlToImage(string $html)
    {
        throw new ApiException('Cannot cast HTML to image', 6, -1, 400);
    }

    /**
     * Convert image to image.
     *
     * @param $image
     *
     * @return string
     */
    public function fromImageToImage($image): string
    {
        return $image;
    }

    /**
     * Convert integer to image.
     *
     * @param int $integer
     *
     * @throws ApiException
     */
    public function fromIntegerToImage(int $integer)
    {
        throw new ApiException('Cannot cast integer to image', 6, -1, 400);
    }

    /**
     * Convert JSON to image.
     *
     * @param string $json
     *
     * @throws ApiException
     */
    public function fromJsonToImage(string $json)
    {
        throw new ApiException('Cannot cast JSON to image', 6, -1, 400);
    }

    /**
     * Convert text to image.
     *
     * @param string $text
     *
     * @return string
     */
    public function fromTextToImage(string $text): string
    {
        return $text;
    }

    /**
     * Convert undefined to image.
     *
     * @param $data
     *
     * @return null
     */
    public function fromUndefinedToImage($data)
    {
        return null;
    }

    /**
     * Convert XML to image.
     *
     * @param string $xml
     *
     * @throws ApiException
     */
    public function fromXmlToImage(string $xml)
    {
        throw new ApiException('Cannot cast XML to image', 6, -1, 400);
    }
}
