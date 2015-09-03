<?php

namespace Datagator\Output;
use Datagator\Core;
use Datagator\Config;

abstract class Output
{
  protected $data;
  protected $meta;
  protected $header = '';
  protected $required = array();
  public $details = array();
  public $status;

  /**
   * @param $data
   * @param $status
   * @param null $meta
   */
  public function __construct($data, $status, $meta=null)
  {
    $this->status = $status;
    $this->data = $data;
    $this->meta = $meta;
  }

  public function process()
  {
    $this->setStatus();
    if (Config::$debugInterface == 'LOG' || (Config::$debug < 1 && Config::$debugDb < 1)) {
      header($this->header);
    }
    return $this->getData();
  }

  abstract protected function getData();

  protected function setStatus()
  {
    http_response_code($this->status);
  }

  /**
   * Validate that the required fields are in the metadata
   *
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  protected function validateRequired()
  {
    $result = array();
    foreach ($this->required as $required) {
      if (!isset($this->meta->$required)) {
        $result[] = $required;
      }
    }
    if (empty($result)) {
      return TRUE;
    }
    throw new Core\ApiException('missing required meta: ' . implode(', ', $result), -1, $this->id, 417);
  }
}
