<?php

/**
 * Class ApiException.
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

use Exception;

/**
 * Class ApiException
 *
 * Custom Exception handler for ApiOpenStudio.
 */
class ApiException extends Exception
{
    /**
     * Processor ID.
     *
     * @var mixed
     */
    private $processor;

    /**
     * HTML response code.
     *
     * @var integer HTML response code.
     */
    private int $htmlCode;

    /**
     * Throw an API exception. This will return a standard error object in the format requested in the header.
     *
     * @param string $message The Exception message to throw.
     * @param int $code The Exception code.
     * @param mixed $processor The processor where the error occurred.
     * @param int $htmlCode The HTML return code.
     * @param ?Exception $previous The previous exception used for the exception chaining. Since 5.3.0.
     */
    public function __construct(
        string $message = 'Unknown error',
        int $code = 0,
        $processor = -1,
        int $htmlCode = 400,
        ?Exception $previous = null
    ) {
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
    public function getHtmlCode(): int
    {
        return $this->htmlCode;
    }
}
