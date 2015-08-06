<?php

namespace Datagator\Outputs;

abstract class Output
{
  public $status;
  protected $data;

  public function Output($status, $data)
  {
    $this->status = $status;
    $this->data = $data;
  }

  public function process()
  {
    $this->setStatus();
    $this->setError();
  }

  protected function setStatus()
  {
    http_response_code($this->status);
  }

  protected function setError()
  {
    if ($this->isError()) {
      $this->data = $this->data->process();
    }
  }

  protected function isError()
  {
    return (is_object($this->data) && get_class($this->data) == 'Error');
  }

  protected function isJson($string)
  {
    if (!is_string($string)) {
      return FALSE;
    }
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }
}
