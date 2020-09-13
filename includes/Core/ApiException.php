<?php
/**
 * Class ApiException.
 *
 * @package Gaterdata
 * @subpackage Core
 * @author john89 (https://gitlab.com/john89)

 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Core;

use Exception;

/**
 * Class ApiException
 *
 * Custom Exception handler for GaterData.
 */
class ApiException extends Exception
{
    /**
     * @var mixed
     */
    private $processor;

    /**
     * @var integer HTML response code.
     */
    private $htmlCode;

    /**
     * Throw an API exception. This will return a standard error object in the format requested in the header.
     *
     * @param string $message The Exception message to throw.
     * @param integer $code The Exception code.
     * @param mixed $processor The processor where the error occurred.
     * @param integer $htmlCode The HTML return code.
     * @param \Exception $previous The previous exception used for the exception chaining. Since 5.3.0.
     */
    public function __construct(
        string $message = null,
        int $code = null,
        $processor = -1,
        int $htmlCode = null,
        Exception $previous = null
    ) {
        $htmlCode = empty($htmlCode) ? 400 : $htmlCode;
        $code = empty($code) ? 0 : $code;
        $message = empty($message) ? 'Unknown error' : $message;
        $this->processor = $processor;
        $this->htmlCode = $htmlCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the ID of the processor.
     *
     * @return mixed Get the processor where the error occurred.
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Get the HTML return code.
     *
     * @return integer Get the HTML return code.
     */
    public function getHtmlCode()
    {
        return $this->htmlCode;
    }
}
