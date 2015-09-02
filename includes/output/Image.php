<?php

/**
 * Output processor for images.
 *
 * Accepts filepath, or remote URL.
 * This will return the actual image, not the URL.
 */

namespace Datagator\Output;
use Datagator\Core;

class Image extends Output
{
  protected function getData()
  {

  }

  public function process()
  {
    parent::process();

    if (!is_string($this->data)) {
      $xml = new Json($this->status, $this->data);
      return $xml->process();
    }
    if (empty($this->data)) {
      header('Content-Type:application/json');
      return 'image empty';
    }

    if (substr($this->data, 0, 4 ) === "http") {
      $curl = new Core\Curl();
      $image = $curl->get($this->data, array('CURLOPT_SSL_VERIFYPEER' => 0, 'CURLOPT_FOLLOWLOCATION' => 1));
      header('Content-Type:' . $curl->type);
      return $image;
    }

    if (function_exists('finfo_open')) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $this->data);
    } elseif (function_exists('mime_content_type')) {
      $mime = mime_content_type($this->data);
    } else {
      header('Content-Type:text/plain');
      return 'Error (-1): Cannot read mime type of image. Please enable filetype extension.';
    }

    header("Content-Type:$mime");
    return file_get_contents($this->data);
  }
}
