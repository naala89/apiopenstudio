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
    'description' => 'CRUD for a resource for the in JSON form.',
    'menu' => 'Resource',
    'application' => 'All',
    'input' => array(
      'method' => array(
        'description' => 'The HTTP method for the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'string'),
      ),
      'appid' => array(
        'description' => 'The application ID for the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'literal'),
      ),
      'noun' => array(
        'description' => 'The noun identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'string'),
      ),
      'verb' => array(
        'description' => 'The verb identifier of the resource (only used if fetching or deleting a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'string'),
      ),
      'json' => array(
        'description' => 'The json string or file. This can be a form file or a urlencoded GET var or in the body (this is only used if you are creating or updating a resource).',
        'cardinality' => array(0, 1),
        'accepts' => array('processor', 'file', 'string'),
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
      $json = urldecode($this->val($this->meta->json));
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
