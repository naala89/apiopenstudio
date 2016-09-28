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
  protected $details = array(
    'name' => 'Image',
    'machineName' => 'image',
    'description' => 'Output in image format. The data fed into the output can be a URL (must start with http) or an input filename.',
    'menu' => 'Output',
    'application' => 'Common',
    'input' => array(
      'destination' => array(
        'description' => 'Destination URLs for the output.',
        'cardinality' => array(1, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'method' => array(
        'description' => 'HTTP delivery method when sending output. Only used in the output section.',
        'cardinality' => array(0, '1'),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('get', 'post'),
        'default' => ''
      ),
      'options' => array(
        'description' => 'Extra Curl options to be applied when sent to the destination  (e.g. cursor: -1, screen_name: foobarapi, skip_status: true, etc).',
        'cardinality' => array(0, '*'),
        'literalAllowed' => true,
        'limitFunctions' => array('field'),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    parent::process();

    if (!is_string($this->data)) {
      throw new Core\ApiException('data revieved is not an image.', 1, $this->id);
    }
    if (empty($this->data)) {
      throw new Core\ApiException('image empty.', 1, $this->id);
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
      throw new Core\ApiException('Cannot read mime type of image. Please enable filetype extension.', 1, $this->id);
    }

    header("Content-Type:$mime");
    return file_get_contents($this->data);
  }

  /**
   * No need define these classes, because it is only a delivery mechanism.
   */
  protected function getData() {}

  protected function fromXml(& $data) {}

  protected function fromJson(& $data) {}

  protected function fromHtml(& $data) {}

  protected function fromText(& $data) {}

  protected function fromArray(& $data) {}
}
