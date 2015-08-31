<?php

namespace Datagator\Core;

class Error
{
  private $id;
  private $code;
  private $message;

  /**
   * @param $code
   * @param $id
   * @param $message
   */
  public function __construct($code, $id, $message)
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
    $result = array(
      'error' => array(
        'code' => $this->code,
        'message' => (!empty($this->message) ? ucfirst($this->message) . '.' : 'Unidentified error.'),
      ),
    );
    if (!empty($this->id)) {
      $result['error']['id'] = $this->id;
    }
    return $result;
  }
}