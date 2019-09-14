<?php

/**
 * Import, export and delete resources in YAML format.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

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
    $this->request['machineName'] = 'resourceYaml';
    $this->request['description'] = 'Create edit or fetch a custom API resource for the application in YAML form.';
    parent::__construct($meta, $request);
  }

  /**
   * Convert YAML string to YAML array.
   *
   * @param $file
   * @return array
   * @throws \Gaterdata\Core\ApiException
   */
  protected function _importData($file)
  {
    $data = $this->getFile($file);
    $yaml = \Spyc::YAMLLoadString($data);
    if (empty($yaml)) {
      throw new Core\ApiException('Invalid or no YAML supplied', 6, $this->id, 417);
    }
    return $yaml;
  }

  /**
   * Convert YAML array to YAML string
   * @param array $data
   * @return string
   */
  protected function _exportData($data)
  {
    return \Spyc::YAMLDump($data);
  }
}
