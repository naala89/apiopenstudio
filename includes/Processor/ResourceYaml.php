<?php

/**
 * Import, export and delete resources in YAML format.
 * 
 * If creating, this API call excpects a file to be in the POST.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Core\Debug;

class ResourceYaml extends ResourceBase
{

  /**
   * Convert YAML string to YAML array.
   *
   * @param $string
   * @return string
   * @throws \Gaterdata\Core\ApiException
   */
  protected function _importData($string)
  {
    $yaml = \Spyc::YAMLLoadString($string);
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
