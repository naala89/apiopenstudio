<?php

/**
 * Output processor for images.
 *
 * Accepts filepath, or remote URL.
 * This will return the actual image, not the URL.
 */

include_once(Config::$dirIncludes . 'output/class.Output.php');
include_once(Config::$dirIncludes . 'class.Curl.php');

class OutputImage extends Output
{
  public function process()
  {
    parent::process();

    if (!is_string($this->data)) {
      include_once(Config::$dirIncludes . 'output/class.OutputJson.php');
      $xml = new OutputJson($this->status, $this->data);
      return $xml->process();
    }
    if (empty($this->data)) {
      header('Content-Type:test/json');
      return 'image empty';
    }

    if (substr($this->data, 0, 4 ) === "http") {
      $curl = new Curl();
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
