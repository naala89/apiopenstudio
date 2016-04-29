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

class SwaggerYaml extends SwaggerBase
{
  protected $details = array(
    'name' => 'Import Swagger (YAML)',
    'description' => 'Create resource document using a Swagger YAML document. Export result in YAML format.',
    'menu' => 'Resource',
    'application' => 'All',
    'input' => array(
      'yaml' => array(
        'description' => 'The yaml string or file. This can be an attached file or a urlencoded GET var.',
        'cardinality' => array(1, 1),
        'accepts' => array('string', 'file')
      ),
    )
  );

  /**
   * @param array $array
   * @return string
   */
  protected function _exportData(array $array)
  {
    return \Spyc::YAMLDump($array);
  }
}
