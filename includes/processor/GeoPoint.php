<?php

/**
 * Geo Point
 */

namespace Datagator\Processor;
use Codeception\Util\Debug;
use Datagator\Core;

class GeoPoint extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Geo-Point',
    'machineName' => 'geoPoint',
    'description' => 'GeoPoint function is a container object that holds geographic point coordinates and attributes. Each entry of a coordinate pair and associated attributes, if any, represent a discrete element in the geopoint vector.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'lat' => array(
        'description' => 'The latitude of the geo point.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('float', 'integer'),
        'limitValues' => array(),
        'default' => 0
      ),
      'lon' => array(
        'description' => 'The longitude of the geo point.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('float', 'integer'),
        'limitValues' => array(),
        'default' => 0
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor GeoPoint', 4);

    $lat = $this->val('lat', true);
    $lat = empty($lat) ? $this->details['input']['lat']['default'] : $lat;
    $lon = $this->val('lon', true);
    $lon = empty($lon) ? $this->details['input']['lon']['default'] : $lon;

    Core\Debug::variable(array(
      'lat' => $lat,
      'lon' => $lon
    ), 'geo result');

    return array(
      'lat' => $lat,
      'lon' => $lon
    );
  }
}
