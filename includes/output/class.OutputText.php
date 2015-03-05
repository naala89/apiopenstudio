<?php

include_once(Config::$dirIncludes . 'output/class.Output.php');

class OutputText extends Output
{
  public function process()
  {
    parent::process();
    header('Content:text/text');
    return $this->data;
  }

  protected function setError()
  {
    if (is_object($this->data) && get_class($this->data) == 'Error') {
      $result = $this->data->process();
      $this->data = 'Error (' . $result['error']['code'] . '): ' . $result['error']['message'];

      if (!empty($result['error']['id'])) {
        $this->data .= ' Processor ' . $result['error']['id'];
      }
    }
  }
}