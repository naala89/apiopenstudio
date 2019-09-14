<?php

/**
 * Output processor for images.
 *
 * Accepts filepath, or remote URL.
 * This will return the actual image, not the URL.
 */

namespace Gaterdata\Output;
use Gaterdata\Core;

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

  protected function fromXml(& $data) {
    return 'data is not an image';
  }

  protected function fromFloat(& $data) {
    return 'data is not an image';
  }

  protected function fromBoolean(& $data) {
    return 'data is not an image';
  }

  protected function fromInteger(& $data) {
    return 'data is not an image';
  }

  protected function fromJson(& $data) {
    return 'data is not an image';
  }

  protected function fromHtml(& $data) {
    return 'data is not an image';
  }

  protected function fromText(& $data) {
    return $data;
  }

  protected function fromArray(& $data) {
    return 'data is not an image';
  }

  protected function fromImage(& $data) {
    return $data;
  }
}
