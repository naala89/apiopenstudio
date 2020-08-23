<?php

/**
 * Geo Point
 */

namespace Gaterdata\Processor;

use Codeception\Util\Debug;
use Gaterdata\Core;

class GeoPoint extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Geo-Point',
        'machineName' => 'geoPoint',
        // phpcs:ignore
        'description' => 'GeoPoint function is a container object that holds geographic point coordinates and attributes. Each entry of a coordinate pair and associated attributes, if any, represent a discrete element in the geopoint vector.',
        'menu' => 'Primitive',
        'input' => [
            'lat' => [
                'description' => 'The latitude of the geo point.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['float', 'integer'],
                'limitValues' => [],
                'default' => 0
            ],
            'lon' => [
                'description' => 'The longitude of the geo point.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['float', 'integer'],
                'limitValues' => [],
                'default' => 0
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $lat = $this->val('lat', true);
        $lon = $this->val('lon', true);

        return new Core\DataContainer(['lat' => $lat, 'lon' => $lon], 'array');
    }
}
