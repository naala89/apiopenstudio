<?php

include_once(Config::$dirIncludes . 'class.Error.php');

class Processor
{
  public $status;

  protected $id;
  protected $meta;
  protected $request;
  protected $extra;

  public function Processor($meta, $request)
  {
    $this->meta = $meta;
    $this->request = $request;
    if (isset($meta->id)) {
      $this->id = $meta->id;
    }
  }

  public function process()
  {
    $this->status = 200;

    $processor = $this->getProcessor();
    if ($this->status != 200) {
      return $processor;
    }
    $result = $processor->process();
    if ($processor->status != 200) {
      $this->status = $processor->status;
    }

    return $result;
  }

  protected function getProcessor($obj=FALSE)
  {
    $obj = (!$obj ? $this->meta : $obj);
    if (empty($obj) || empty($obj->type) || empty($obj->meta)) {
      $this->status = 417;
      return new Error(-1, 'missing meta or type');
    }

    $classname = 'Processor' . ucfirst(trim($obj->type));
    $filename = 'class.' . $classname . '.php';
    $filepath = Config::$dirIncludes . 'processor/' . $filename;

    if (!file_exists($filepath)) {
      $this->status = 417;
      return new Error(-1, 'Invalid or no processor defined (' . trim($obj->type) . ')');
    }

    include_once($filepath);
    return new $classname(
      $obj->meta,
      !empty($this->request['args']) ? $this->request['args'] : array(),
      !empty($this->request['get']) ? $this->request['get'] : array()
    );
  }
}