<?php

/**
 * Fetch a list of all processors
 */

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db\ApplicationMapper;

class ProcessorsAll extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'processors (all)',
    'machineName' => 'processorsAll',
    'description' => 'Fetch data on all processors available to your application.',
    'menu' => 'System',
    'application' => 'Common',
    'input' => array()
  );

  /**
   * @return array
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ProcessorsAll');

    $applicationMapper = new ApplicationMapper($this->getDb());
    $application = $applicationMapper->findByAppId($this->request->getAppId());
    $appName = $application->getName();

    $classes = [];
    $classes = array_merge($classes, $this->_getClassList('endpoint'));
    $classes = array_merge($classes, $this->_getClassList('output'));
    $classes = array_merge($classes, $this->_getClassList('processor'));
    $classes = array_merge($classes, $this->_getClassList('security'));

    $result = $this->_getDetails($classes, $appName);

    return $result;
  }

  /**
   * @param $namespace
   * @return array
   */
  private function _getClassList($namespace)
  {
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../' . strtolower($namespace)));
    $objects = new \RegexIterator($iterator, '/[a-z0-9]+\.php/i', \RecursiveRegexIterator::GET_MATCH);
    $result = array();
    foreach($objects as $name => $object) {
      preg_match('/([a-zA-Z0-9]+)\.php$/i', $name, $className);
      $result[] = 'Datagator\\' . ucfirst($namespace) . '\\' . $className[1];
    }
    return $result;
  }

  /**
   * Get details of all processors in an array.
   *
   * @param array $processors
   * @param $appName
   * @return array
   */
  private function _getDetails(array $processors, $appName)
  {
    $result = array();

    foreach ($processors as $processor) {
      $abstractClass = new \ReflectionClass($processor);
      if (!$abstractClass->isAbstract()) {
        $obj = new $processor($this->meta, $this->request);
        $details = $obj->details();
        if (!empty($details['application']) && ($details['application'] == 'Common' || $details['application'] == $appName)) {
          $result[] = $details;
        }
      }
    }

    return $result;
  }
}
