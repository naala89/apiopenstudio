<?php

class ApiException extends Exception
{
  private $processor;
  private $htmlCode;

  /**
   * @param string $message
   * @param int $code
   * @param int $processor
   * @param int $htmlCode
   * @param \Exception $previous
   */
  public function __construct($message, $code = 0, $processor = -1, $htmlCode = 400, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
    $this->processor = $processor;
    $this->htmlCode = $htmlCode;
  }

  public function getProcessor()
  {
    return $this->processor;
  }

  public function getHtmlCode()
  {
    return $this->htmlCode;
  }
}
