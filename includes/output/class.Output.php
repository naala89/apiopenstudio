<?php

abstract class Output
{
  public $status;
  public $data;

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
    if (is_object($this->data) && get_class($this->data) == 'Error') {
      $this->data = $this->data->process();
    }
  }
}