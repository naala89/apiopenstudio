<?php

/**
 * Trait ConvertToHtmlTrait.
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
 * Trait ConvertToHtmlTrait.
 *
 * Class to cast an input value to HTML.
 */
trait ConvertToHtmlTrait
{
    /**
     * Convert array to HTML string.
     *
     * If the array does not have a html element at the root, then the array will be converted to a data-list and will
     * be appended to the body of the new HTML doc.
     *
     * @param array $array
     *
     * @return string
     */
    public function fromArrayToHtml(array $array): string
    {
        if (!isset($array['html'])) {
            return $this->wrapDataHtmlFormat($this->fromArrayToDataList($array));
        }
        $htmlConverter = new ConvertHtml();
        return $htmlConverter->arrayToHtml($array);
    }

    /**
     * Convert boolean to HTML string.
     * The boolean will be within a div in the body of the new HTML doc.
     *
     * @param ?bool $boolean
     *
     * @return string
     */
    public function fromBooleanToHtml(?bool $boolean): string
    {
        if (is_null($boolean)) {
            $boolean = '';
        } else {
            $boolean = $boolean ? 'true' : 'false';
        }
        return $this->wrapDataHtmlFormat("<div>$boolean</div>");
    }

    /**
     * Convert empty to HTML.
     * Creates an empty HTML doc.
     *
     * @param $data
     *
     * @return string
     */
    public function fromEmptyToHtml($data): string
    {
        return $this->wrapDataHtmlFormat('<div>null</div>');
    }

    /**
     * Convert file to HTML string.
     *
     * @param $file
     *
     * @return string
     */
    public function fromFileToHtml($file): string
    {
        return $this->wrapDataHtmlFormat("<div>$file</div>");
    }

    /**
     * Convert float to HTML string.
     * The float will be within a div in the body of the new HTML doc.
     *
     * @param float|null $float
     * @return string
     */
    public function fromFloatToHtml(?float $float): string
    {
        return $this->wrapDataHtmlFormat("<div>$float</div>");
    }

    /**
     * Convert an HTML string to HTML string.
     *
     * @param string $html
     *
     * @return string
     */
    public function fromHtmlToHtml(string $html): string
    {
        return $html;
    }

    /**
     * Convert an image string to HTML string.
     *
     * @param $image
     *
     * @return string
     */
    public function fromImageToHtml($image): string
    {
        return $this->wrapDataHtmlFormat("<div>$image</div>");
    }

    /**
     * Convert integer to HTML string.
     * The integer will be within a div in the body of the new HTML doc.
     *
     * @param integer|float|null $integer
     *
     * @return string
     */
    public function fromIntegerToHtml($integer): string
    {
        return $this->wrapDataHtmlFormat("<div>$integer</div>");
    }

    /**
     * Convert JSON string to HTML string.
     *
     * @param string $json
     *
     * @return string
     */
    public function fromJsonToHtml(string $json): string
    {
        $jsonDecoded = json_decode($json, true);
        if (is_numeric($jsonDecoded)) {
            return $this->fromFloatToHtml($jsonDecoded);
        } elseif (is_string($jsonDecoded)) {
            return $this->fromTextToHtml($jsonDecoded);
        } elseif (is_bool($jsonDecoded)) {
            return $this->fromBooleanToHtml($jsonDecoded);
        }
        return $this->fromArrayToHtml($jsonDecoded);
    }

    /**
     * Convert text to HTML string.
     * The text will be within a div in the body of the new HTML doc.
     *
     * @param string $html
     *
     * @return string
     */
    public function fromTextToHtml(string $html): string
    {
        if (!$this->isHtml($html)) {
            return $this->wrapDataHtmlFormat("<div>$html</div>");
        }
        return $html;
    }

    /**
     * Convert XML string to HTML string.
     *
     * @param string $xml
     *
     * @return string
     */
    public function fromXmlToHtml(string $xml): string
    {
        $xml = preg_replace("|<\?\s*xml.*\?>|", '', $xml);
        return $this->wrapDataHtmlFormat($xml);
    }

    /**
     * Convert array to HTML Data List string.
     *
     * @param array $array
     *
     * @return string
     */
    protected function fromArrayToDataList(array $array): string
    {
        $dataList = '<dl>';
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $val = $this->fromArrayToDataList($val);
            }
            $dataList .= "<dt>$key</dt><dd>$val</dd>";
        }
        $dataList .= '</dl>';
        return $dataList;
    }

    /**
     * Wrap Data in HTML string wrapper.
     *
     * @param string $body
     *   Body content.
     * @param string $pageTitle
     *   HTML doc title.
     *
     * @return string
     */
    protected function wrapDataHtmlFormat(string $body, string $pageTitle = 'HTML generated by ApiOpenStudio'): string
    {
        $html = "<!DOCTYPE html>\n";
        $html .= '<html lang="en-us">';
        $html .= '<head><meta charset="utf-8" /><title>' . $pageTitle . '</title></head>';
        $html .= "<body>$body</body>";
        $html .= '</html>';

        return $html;
    }
}
