<?php

class Error
{
  public $code;
  public $message;

  /**
   * Constructor, that populate the error object
   *
   * @param mixed $code
   *  The error code ID
   * @param string $message
   *  The error message
   */
  public function Error($code, $message)
  {
    $this->code = $code;
    $this->message = $message;
  }

  /**
   * Construct and return the output error message
   *
   * @return array
   */
  public function process()
  {
    return array(
      'error' => array(
        'code' => $this->code,
        'message' => $this->message,
      ),
    );
  }
}