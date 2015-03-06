<?php

class Error
{
  private $id;
  private $code;
  private $message;

  /**
   * Constructor, that populate the error object
   *
   * @param integer $code
   *  The error code ID
   * @param mixed $id
   *  The processor ID
   * @param string $message
   *  The error message
   */
  public function Error($code, $id, $message)
  {
    $this->code = $code;
    $this->message = $message;
    $this->id = $id;
  }

  /**
   * Construct and return the output error message
   *
   * @return array
   */
  public function process()
  {
    $message = !empty($this->message) ? ucfirst($this->message) . (substr($this->message, -1) !== '.' ? '.' : '') : 'Unidentified error.';
    $result = array(
      'error' => array(
        'code' => $this->code,
        'message' => $message,
      ),
    );
    if (!empty($this->id)) {
      $result['error']['id'] = $this->id;
    }
    return $result;
  }
}