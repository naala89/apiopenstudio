<?php

namespace Gaterdata\Core;

use Exception;

class ApiException extends Exception
{

    private $processor;
    private $htmlCode;

  /**
   * Throw an API exception. This will return a standard error object in the format requested in the header.
   *
   * @param string $message
   *   [optional] The Exception message to throw.
   * @param int $code
   *   [optional] The Exception code.
   * @param string|int $processor
   *   The processor where the error occurred.
   * @param int $htmlCode
   *   The HTML return code.
   * @param \Exception $previous
   *   [optional] The previous exception used for the exception chaining. Since 5.3.0
   */
    public function __construct(
        $message = 'Unknown error',
        $code = 0,
        $processor = -1,
        $htmlCode = 400,
        Exception $previous = null
    ) {
        $this->processor = $processor;
        $this->htmlCode = $htmlCode;
        parent::__construct($message, $code, $previous);
    }

  /**
   * Get the ID of the processor.
   *
   * @return int|string
   *   Get the processor where the error occurred.
   */
    public function getProcessor()
    {
        return $this->processor;
    }

  /**
   * Get the HTML return code.
   *
   * @return int
   *   Get the HTML return code.
   */
    public function getHtmlCode()
    {
        return $this->htmlCode;
    }
}
