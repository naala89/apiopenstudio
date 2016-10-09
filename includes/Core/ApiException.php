<?php

namespace Datagator\Core;

class ApiException extends \Exception
{
  private $processor;
  private $htmlCode;

  /**
   * Throw an API exception. This will return a standard error object in the format requested in the header.
   * @param string $message
   * @param int $code
   * @param string|int $processor
   * @param int $htmlCode
   * @param \Exception|NULL $previous
   */
  public function __construct($message, $code = 0, $processor = -1, $htmlCode = 400, \Exception $previous = null)
  {
    $this->processor = $processor;
    $this->htmlCode = $htmlCode;
    parent::__construct($message, $code, $previous);
  }

  /**
   * Get the ID of the processor.
   * @return int|string
   */
  public function getProcessor()
  {
    return $this->processor;
  }

  /**
   * Get the HTML return code.
   * @return int
   */
  public function getHtmlCode()
  {
    return $this->htmlCode;
  }
}
