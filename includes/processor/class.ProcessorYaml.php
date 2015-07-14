<?php

include_once(Config::$dirIncludes . 'spyc/spyc.php');

/**
 * Yam; import and export
 *
 * This will usually take ana array of processorType. However, you can also have literals or processors.
 *
 * METADATA
 * {
 *    "type":"object",
 *    "meta":{
 *      "attributes":[
 *        <processor|var|literal>,
 *      ]
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/class.Processor.php');

class ProcessorYaml extends Processor
{
  protected $details = array(
    'name' => 'Yaml',
    'description' => 'Create a custom API resource for your organisation.',
    'menu' => 'basic',
    'input' => array(
      'func' => array(
        'description' => 'What to do with the Yaml.',
        'cardinality' => array(1, 1),
        'accepts' => array('input', 'output', 'delete')
      ),
      'yaml' => array(
        'description' => 'The yaml string.',
        'cardinality' => array(0, 1),
        'accepts' => array('string')
      )
    )
  );

  protected $required = array('func');

  public function process()
  {
    Debug::message('ProcessorYaml');
    $this->validateRequired();

    switch ($this->request->method) {
      case 'post':
        $this->yamlIn();
        break;
      case 'get':
        $this->yamlOut();
        break;
      case 'delete':
        $this->yamlByeBye();
        break;
      default:
        break;
    }
  }

  /**
   * Create a resource from YAML.
   * The Yaml is either post string 'yaml', or file 'yaml'.
   * File takes precedence over the string if both present.
   */
  private function yamlIn()
  {
    $yaml = FALSE;
    if (!empty($_FILES['yaml'])) {
      $yaml = $_FILES['yaml'];
    } else {
      $yaml = $this->getVar($this->meta->yaml);
    }
    if (!$yaml) {
      throw new ApiException('no yaml supplied', -1, $this->id);
    }

r
  }

  private function yamlOut()
  {

  }

  private function yamlByeBye()
  {

  }
}
