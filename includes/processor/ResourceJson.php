<?php

/**
 * Resource import and export.
 * Allowed inputs are json files or json strings.
 *
 * METADATA
 * {
 *    "type":"object",
 *    "meta":{
 *      "id": <mixed>,
 *      "json": <string>,
 *      "method": <"get"|"post">,
 *      "resource": <mixed>,
 *      "action": <mixed>
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class ResourceJson extends ResourceBase
{
  protected $details = array(
    'name' => 'Resource (Json)',
    'description' => 'Create or fetch a custom API resource for the application in JSON form.',
    'menu' => 'Resource',
    'application' => 'All',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'appid' => array(
        'description' => 'The application ID the resource is associated with (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('integer')
      ),
      'noun' => array(
        'description' => 'The noun identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'verb' => array(
        'description' => 'The verb identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      ),
      'json' => array(
        'description' => 'The json string or file. This can be a form file or a urlencoded GET var (this is only used if you are creating or updating a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('string', 'file')
      )
    )
  );

  /**
   * @return mixed|string
   * @throws \Datagator\Core\ApiException
   */
  protected function _importData()
  {
    // extract json
    $json = '';
    Core\Debug::variable($_FILES);
    if (sizeof($_FILES) > 1) {
      throw new Core\ApiException('multiple files received', 3);
    }
    if (!empty($_FILES)) {
      foreach ($_FILES as $file) {
        $json = json_decode(file_get_contents($file['tmp_name']));
      }
    } else {
      $json = urldecode($this->getVar($this->meta->json));
      $json = json_decode($json);
    }
    if (empty($json)) {
      throw new Core\ApiException('invalid or no json supplied', 6, $this->id, 417);
    }
    return json_decode(json_encode($json), true);
  }

  /**
   * @param array $array
   * @return string
   */
  protected function _exportData(array $array)
  {
    return json_encode($array);
  }
}
