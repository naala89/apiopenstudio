<?php

/**
 * Resource import and export.
 * Allowed inputs are yaml files or yaml strings.
 *
 * METADATA
 * {
 *    "type":"object",
 *    "meta":{
 *      "id": <mixed>,
 *      "yaml": <string>,
 *      "method": <"get"|"post">,
 *      "resource": <mixed>,
 *      "action": <mixed>
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class ResourceYaml extends ResourceBase
{

  /**
   * Constructor. Store processor metadata and request data in object.
   *
   * @param $meta
   * @param $request
   */
  public function __construct($meta, $request)
  {
    $this->request['name'] = 'Resource (Yaml)';
    $this->request['description'] = 'Create edit or fetch a custom API resource for the application in YAML form.';
    parent::__construct($meta, $request);
  }

  /**
   * @return array|string
   * @throws \Datagator\Core\ApiException
   */
  protected function _importData()
  {
    // extract yaml
    $yaml = '';
    if (sizeof($_FILES) > 1) {
      throw new Core\ApiException('multiple files received', 3);
    }
    if (!empty($_FILES)) {
      foreach ($_FILES as $file) {
        $yaml = \Spyc::YAMLLoad($file['tmp_name']);
      }
    } else {
      if (empty($this->request->vars['yaml'])) {
        throw new Core\ApiException('no yaml supplied', 6, $this->id, 417);
      }
      $yaml = $this->val($this->meta->yaml);
      $yaml = urldecode($yaml);
      $yaml = \Spyc::YAMLLoadString($yaml);
    }
    return $yaml;
  }

  /**
   * @param array $data
   * @return string
   */
  protected function _exportData(array $data)
  {
    return \Spyc::YAMLDump($data);
  }
}
