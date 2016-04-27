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

  /**
   * Constructor. Store processor metadata and request data in object.
   *
   * @param $meta
   * @param $request
   */
  public function __construct($meta, $request)
  {
    $this->request['name'] = 'Resource (JSON)';
    $this->request['description'] = 'Create edit or fetch a custom API resource for the application in JSON form.';
    parent::__construct($meta, $request);
  }

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
   * @param array $data
   * @return string
   */
  protected function _exportData(array $data)
  {
    return json_encode($data);
  }
}
